<?php
/**
 * Батя в здании
 */
use Aniart\Main\Models\Order;
use Bitrix\Main\Application;
use Bitrix\Sale\Compatible\DiscountCompatibility;
use Bitrix\Sale\DiscountCouponsManager;
use Aniart\Main\Ext\User;

CBitrixComponent::includeComponentClass('bitrix:sale.order.ajax');

class CAniartSaleOrder extends SaleOrderAjax
{
    /**
     * @var \Aniart\Main\Repositories\SaleDeliveriesRepository
     */
    protected $deliveriesRepository;
    /**
     * @var \Aniart\Main\Repositories\SalePaySystemsRepository
     */
    protected $paySystemsRepository;
    protected $defaultSiteId = 's1';
    public function __construct($component = null)
    {
        //тут будем регистрировать обработчики событий стандартного Битриксового компонента
        parent::__construct($component);

        $this->deliveriesRepository = app('DeliveriesRepository');
        $this->paySystemsRepository = app('PaySystemsRepository');
    }

    public function onPrepareComponentParams($arParams)
    {
        /**
         *  Половина параметров на самом деле не нужна
         *
         *   ().()   ().()
         *  =(^,^)= =(^.^)=
         */

        $arParams = [
            "ACTION_VARIABLE" => "action",
            "ADDITIONAL_PICT_PROP_12" => "-",
            "ADDITIONAL_PICT_PROP_5" => "-",
            "ALLOW_APPEND_ORDER" => "Y",
            "ALLOW_AUTO_REGISTER" => "Y",
            "ALLOW_NEW_PROFILE" => "N",
            "ALLOW_USER_PROFILES" => "N",
            "BASKET_IMAGES_SCALING" => "adaptive",
            "BASKET_POSITION" => "after",
            "COMPATIBLE_MODE" => "N",
            "DELIVERIES_PER_PAGE" => "9",
            "DELIVERY_FADE_EXTRA_SERVICES" => "N",
            "DELIVERY_NO_AJAX" => "N",
            "DELIVERY_NO_SESSION" => "Y",
            "DELIVERY_TO_PAYSYSTEM" => "p2d",
            "DISABLE_BASKET_REDIRECT" => "N",
            "ONLY_FULL_PAY_FROM_ACCOUNT" => "N",
            "PATH_TO_AUTH" => "/auth/",
            "PATH_TO_BASKET" => i18n()->getLangDir('/personal/'),
            "PATH_TO_PAYMENT" => "/order/payment/",
            "PATH_TO_PERSONAL" => "index.php",
            "PAY_FROM_ACCOUNT" => "N",
            "PAY_SYSTEMS_PER_PAGE" => "9",
            "PICKUPS_PER_PAGE" => "5",
            "PRODUCT_COLUMNS_HIDDEN" => array(),
            "PRODUCT_COLUMNS_VISIBLE" => array("PREVIEW_PICTURE","PROPS"),
            "SEND_NEW_USER_NOTIFY" => "N",
            "SERVICES_IMAGES_SCALING" => "adaptive",
            "SET_TITLE" => "Y",
            "SHOW_BASKET_HEADERS" => "N",
            "SHOW_COUPONS_BASKET" => "Y",
            "SHOW_COUPONS_DELIVERY" => "Y",
            "SHOW_COUPONS_PAY_SYSTEM" => "Y",
            "SHOW_DELIVERY_INFO_NAME" => "Y",
            "SHOW_DELIVERY_LIST_NAMES" => "Y",
            "SHOW_DELIVERY_PARENT_NAMES" => "Y",
            "SHOW_MAP_IN_PROPS" => "N",
            "SHOW_NEAREST_PICKUP" => "N",
            "SHOW_NOT_CALCULATED_DELIVERIES" => "L",
            "SHOW_ORDER_BUTTON" => "always",
            "SHOW_PAY_SYSTEM_INFO_NAME" => "Y",
            "SHOW_PAY_SYSTEM_LIST_NAMES" => "Y",
            "SHOW_STORES_IMAGES" => "Y",
            "SHOW_TOTAL_ORDER_BUTTON" => "N",
            "SHOW_VAT_PRICE" => "Y",
            "SKIP_USELESS_BLOCK" => "Y",
            "TEMPLATE_LOCATION" => "popup",
            "TEMPLATE_THEME" => "site",
            "USE_CUSTOM_ADDITIONAL_MESSAGES" => "N",
            "USE_CUSTOM_ERROR_MESSAGES" => "N",
            "USE_CUSTOM_MAIN_MESSAGES" => "N",
            "USE_ENHANCED_ECOMMERCE" => "N",
            "USE_PRELOAD" => "Y",
            "USE_PREPAYMENT" => "N",
            "USE_YM_GOALS" => "N",
        ];
        return parent::onPrepareComponentParams($arParams);
    }
    public function generateUserData($userProps = array())
    {
        $result = parent::generateUserData($userProps);
        $result['NEW_LOGIN'] = $userProps['EMAIL'];
        $result['NEW_PERSONAL_PHONE'] = $userProps['PHONE'];
        return $result;
    }

    public function executeComponent()
    {
        global $APPLICATION;
        $this->setFrameMode(false);
        $this->context = Application::getInstance()->getContext();
        $this->checkSession = $this->arParams["DELIVERY_NO_SESSION"] == "N" || check_bitrix_sessid();
        $this->isRequestViaAjax = $this->request->isPost() && $this->request->get('via_ajax') == 'Y';
        $isAjaxRequest = $this->request["is_ajax_post"] == "Y";

        if ($isAjaxRequest) {
            $APPLICATION->RestartBuffer();
        }

        $this->action = $this->prepareAction();
        DiscountCompatibility::stopUsageCompatible();
        $this->doAction($this->action);
        DiscountCompatibility::revertUsageCompatible();

        $this->initOrderByComponentResult();
        $this->arResult['~ERROR_SORTED'] = $this->normalizeErrors($this->arResult['ERROR_SORTED']);

        $this->arResult['IS_AUTH'] = CUser::IsAuthorized();
        if(!$this->arResult['IS_AUTH'])
        {
            $sAuth = new \Aniart\Main\Services\SocAuthList();
            $this->arResult['SOC_SERVICES'] = $sAuth->getList();
        }

        $templatePage = '';
        $order = $this->getOrderObject();
        //if($this->request->isPost() && $this->request->get('firstBuy') == 'Y') //Нажато ПОКУПАЮ ВПЕРВЫЕ
          //  $this->arResult['NEW_BUYER'] = $this->request->get('firstBuy');
        $this->arResult['STEP'] = 3;
        if($this->arResult['ORDER_ID']){
            $this->showOrderAction();
            if($this->arResult['ORDER']){
                $paySystem = $order->getPaySystem();
                $actionParams = $paySystem->getAction();
                $order->setPaymentCollection();
                /*
                 *Страница thankyou для оплаченых заказов и заказов без платежной системы
                 */
                if($this->arResult['ORDER']['PAYED'] == 'Y' || !$order->getPaymentContent())
                {
                    $this->arResult['ORDER']['FIO'] = $order->getPropertyValue('NAME').' '.$order->getPropertyValue('LAST_NAME');
                    $this->arResult['ORDER']['PHONE'] = $order->getPropertyValue('PHONE');
                    $basketItems = $order->getBasket()->getItems();
                    $this->arResult['ORDER']['~BASKET'] = $order->getBasket()->getItems();
                    foreach($basketItems as $item)
                    {
                        $row['NAME'] = $item->getName();
                        $row['URL'] = $item->getProductUrl();
                        $row['PRICE'] = $item->getPrice(true);
                        $row['ID'] = $item->getOffer()->getProductId();
                        if(!$this->arResult['ORDER']['PHOTO'])
                        {
                            $product = $item->getProduct();
                            foreach($product->getAllImagesId(1) as $key=>$img)
                            $this->arResult['ORDER']['PHOTO'] = $product->getMinPicture($img, 400, 400);
                        }
                        $this->arResult['ORDER']['BASKET'][] = $row;
                        $deliveryName = $order->getDelivery()->getName();
                        if(substr_count($deliveryName, 'отделение') > 0)
                                $deliveryName = str_replace('отделение', '', $deliveryName);
                        $this->arResult['ORDER']['DELIVERY_NAME'] = $deliveryName;
                        $this->arResult['ORDER']['PAYSYSTEM_NAME'] = $order->getPaySystem()->getName();
                    }
                    $this->arResult['ORDER_GTM'] = Aniart\Main\Tools\Gtm::getOrderGtm($order);
                    $this->arResult['ORDER']['DELIVERY_ADRESS'] = $order->getAdress();
                    if($this->arResult['QUICK_ORDER'])
                        $templatePage = 'quick_confirm';
                    else
                        $templatePage = 'thankyou';
                }
                else
                {
                    /*Получить html для оплаты по аналогии битрикса*/
                    if($actionParams['NEW_WINDOW'] == 'N')
                    {
                        $this->arResult['ORDER']['PAYMENT_HTML'] = $order->getPaymentContent();
                    }
                    else
                    {
                        $paymentAccountNumber = $order->getPaymentId();
                        $url = $this->arParams['PATH_TO_PAYMENT']."?ORDER_ID=".urlencode($order->getAccountNumber())."&PAYMENT_ID=".$paymentAccountNumber;
                        $this->arResult['ORDER']['PAYMENT_HTML'] = "<script>
                            window.open('".$url."');
                        </script>
                        <p>".i18n("PAYMENT_TEXT")." <a href='".$url."' target=\"_blank\">".i18n("PAYMENT_LINK")."</a>.</p>
                        ";
                    }
                    $templatePage = 'confirm';
                }

                //если купон с подписки на рассылку, то ставим дату использования в hl элемента и передаем в crm
                $coupon = $this->getCoupon();
                if(!empty($coupon))
                {
                    $couponRepository = new \Aniart\Main\Repositories\SubscribeCouponsRepository(HL_DISCOUNT_COUPON_ID);
                    $couponSubscribe = reset($couponRepository->getByCoupon($coupon));
                    if(is_a($couponSubscribe, 'Aniart\Main\Models\SubscribeCoupon'))
                    {
                        $couponRepository->update($couponSubscribe->getId(), ['UF_USED' => date('d.m.Y H:i:s')]);
                        $retail = new \Aniart\Main\Tools\retailCrmHelper();
                        $retail->setCouponDate($order->getPropertyValue('EMAIL'), $this->arResult['ORDER_ID']);
                    }
                }
            }
        }
        else $this->arResult['BASKET_GTM'] = Aniart\Main\Tools\Gtm::getOrderBasketGtm($order->getBasket());
        $this->includeComponentTemplate($templatePage);

        if ($isAjaxRequest)
        {
            $APPLICATION->FinalActions();
            die();

        }
    }

    public function getUserProps()
    {
        $order = $this->getOrderObject();
        $userInfo = $order->getUserInfo();

        $orderProps = array_filter($order->getPropsMeta(), function($prop){
            return $prop['PROPS_GROUP_ID'] == OPG_USER_INFO;
        });
        foreach($orderProps as $key => $prop)
        {
            if($prop['CODE'] == 'CITY' && !empty($orderProps[$key]['VALUE'][0]))
            {
                $this->arResult['CITY'] = $orderProps[$key]['VALUE'][0];
                $newPostDepartments = app('NpDepartmentsRepository');
                $newPostCities = app('NpCitiesRepository');
                $cityRef = $newPostCities->getRefByName($this->arResult['CITY']);
                if(!empty($cityRef))
                {
                    $departments = $newPostDepartments->getDepartmentsByCityRef($cityRef);
                    foreach($departments as $department)
                        $this->arResult['NP_DEPARTMENTS'][] = $department->getName();
                }
            }
            if($this->request->isPost())
            {
                $propRequest = $this->request->get('ORDER_PROP_'.$key);
                if(!empty($propRequest)) $orderProps[$key]['VALUE'][0] = $propRequest;
                    else $orderProps[$key]['VALUE'][0] = $userInfo[$prop['CODE']];
            }
            else $orderProps[$key]['VALUE'][0] = $userInfo[$prop['CODE']];
        }
        return $orderProps;
    }

    public function getDeliveryProps()
    {
        $order = $this->getOrderObject();
        return array_filter($order->getPropsMeta(), function($prop){
            return ($prop['PROPS_GROUP_ID'] == OPG_DELIVERY && $prop['CODE'] != 'STORE_ADDRESS');
        });
    }

    public function getCouponsProps()
    {
        $order = $this->getOrderObject();
        return array_filter($order->getPropsMeta(), function($prop){
            return ($prop['PROPS_GROUP_ID'] == OPG_COUPONS);
        });
    }

    /*
     * Комментарий покупателя
     */
    public function getComment()
    {
        if($this->request->isPost() && $this->request->get('ORDER_DESCRIPTION'))
            return $this->request->get('ORDER_DESCRIPTION');
    }

    public function getPropErrors($propCode)
    {
        $errors = isset($this->arResult['~ERROR_SORTED']['PROPERTY'][$propCode])
            ? $this->arResult['~ERROR_SORTED']['PROPERTY'][$propCode] : [];
        return $errors;
    }

    /**
     * @return \Aniart\Main\Models\Order
     */
    public function getOrderObject()
    {
        return $this->arResult['~ORDER'];
    }


    private function initOrderByComponentResult()
    {
        $arResult = &$this->arResult;
        if($arResult['ORDER_ID']){
            $order = app('Order', [$arResult['ORDER']]);
        }
        else{
            $order = app('Order', [[
                'PRICE' => $arResult['ORDER_PRICE'],
                'PRICE_DELIVERY' => $arResult['DELIVERY_PRICE'],
                'PAY_SYSTEM_ID' => $arResult['USER_VALS']["PAY_SYSTEM_ID"],
                'DELIVERY_ID' => $arResult['USER_VALS']['DELIVERY_ID'],
                'BASKET_ITEMS' => $arResult['BASKET_ITEMS']
            ]]);
        }
        /**
         * @var \Aniart\Main\Models\Order $order
         */
        $deliveryId = (int)$order->getDeliveryId();
        $paySystemId = (int)$order->getPaySystemId();
        foreach((array)$arResult['DELIVERY'] as $deliveryData){
            $delivery = $this->deliveriesRepository->newInstance($deliveryData);
            $this->arResult['~DELIVERY'][$delivery->getId()] = $delivery;
            if($deliveryId == $delivery->getId()){
                $order->setDelivery($delivery);
            }
        }
        foreach((array)$arResult['PAY_SYSTEM'] as $paySystem) {
            $paySystem = $this->paySystemsRepository->newInstance($paySystem);
            $this->arResult['~PAY_SYSTEM'][$paySystem->getId()] = $paySystem;
            if($paySystemId == $paySystem->getId()){
                $order->setPaySystem($paySystem);
            }
        }
        if(is_array($arResult['JS_DATA']['ORDER_PROP']['properties'])){
            $order->setPropsMeta($arResult['JS_DATA']['ORDER_PROP']['properties']);
        }
        $arResult['~ORDER'] = $order;
    }

    public function getSignedComponentParams()
    {
        $signer = new \Bitrix\Main\Security\Sign\Signer();
        return $signer->sign(base64_encode(serialize($this->arParams)), 'sale.order');
    }

    private function normalizeErrors($errors)
    {
        $result = [];
        if(empty($errors)){
            return $result;
        }
        if(is_array($errors['PROPERTY'])){
            $result['PROPERTY'] = $this->normalizeUserPropsErrors($errors['PROPERTY']);
        }
        if(is_array($errors['AUTH'])){
            $result['AUTH'] = $this->normalizeRegisterErrors($errors['AUTH']);
        }
        if(!empty($result['AUTH']['EMAIL'])){
            $result['PROPERTY']['EMAIL'] = array_merge((array)$result['PROPERTY']['EMAIL'], $result['AUTH']['EMAIL']);
        }
        return $result;
    }

    private function normalizeUserPropsErrors($err)
    {
        $result = [];
        /**
         * @var Order $order
         */
        $order = $this->arResult['~ORDER'];
        $propsMeta = $order->getPropsMeta();
        foreach($propsMeta as $propData){
            $errors = array_filter($err, function($error) use ($propData, $err){
                //return (strpos($error, $propData['NAME']) !== false) || ($propData["CODE"] == array_search($error, $err));
                return (strpos($error, $propData['NAME']) !== false);
            });
            if(!empty($errors)){
                $result[$propData['CODE']] = $errors;
            }
        }
        return $result;
    }

    private function normalizeRegisterErrors($errors)
    {
        $result = [];
        /**
         * @var Order $order
         */
        $order = $this->arResult['~ORDER'];
        $propsMeta = $order->getPropsMeta('CODE');
        $email = current($propsMeta['EMAIL']['VALUE']);
        foreach($errors as $error) {
            if (strpos($error, $email) !== false){
                $result['EMAIL'][] = $error;
            }
        }
        return $result;
    }

    protected function enterCouponAction()
    {
        $existedCoupons = Order::getCoupons();
        if (!empty($existedCoupons)) {
           $this->arResult["ERROR_COUPON"] = i18n("COUPON_ENTERED");
        } else {
            $coupon = trim($this->request->get('coupon'));
            $email = trim($this->request->get('ORDER_PROP_3'));
            $isUsed = false; //для неавторизированого, проверка купона за подписку
            $couponRepository = new \Aniart\Main\Repositories\SubscribeCouponsRepository(HL_DISCOUNT_COUPON_ID);
            $couponSubscribe = reset($couponRepository->getByCoupon($coupon));
            if(!empty($couponSubscribe)) $isUsed = $couponSubscribe->isUsed($email);

            if (!empty($coupon) && !$isUsed) {
                if (!$result = DiscountCouponsManager::add($coupon) || $isUsed) {
                    $this->arResult["ERROR_COUPON"] = i18n("COUPON_ADD_ERROR");
                }
            }
        }
        $this->processOrderAction();
    }

    protected function removeCouponAction()
    {
        $coupon = trim($this->request->get('coupon'));

        if (!empty($coupon))
        {
            if (!$result = DiscountCouponsManager::delete($coupon)) {
                $this->arResult["ERROR_COUPON"] = i18n("COUPON_REMOVE_ERROR");
            }

        }

        $this->processOrderAction();
    }

    protected function getCoupon()
    {
        $couponList = \Bitrix\Sale\Internals\OrderCouponsTable::getList(array(
            'select' => array('COUPON'),
            'filter' => array('=ORDER_ID' => $this->arResult['ORDER_ID'])
        ));
        while ($coupon = $couponList->fetch())
        {
           $result = $coupon['COUPON'];
        }
        return $result;
    }

    protected function registerAndLogIn($userProps)
    {
        \Bitrix\Main\Diag\Debug::writeToFile($userProps,"userProps 1","/local/logs/userorder.txt");
        $userId = false;
        $userData = $this->generateUserData($userProps);

        if(isset($userProps['PHONE']))
        {
            $phone = _normalizePhone($userProps['PHONE']);
            $existUser = \Aniart\Main\Ext\User::ExistsByPhone($phone);
            $userBitrix = reset($existUser);
            if($existUser && empty($userBitrix['LAST_LOGIN']))
            {
                global $USER;
                $userFields = $this->normalizeData($userData);
                $this->updateUser($userFields, $userBitrix);
                $userId = $userBitrix['ID'];
                $USER->Authorize($userId);
                return $userId;
            }
        }
        \Bitrix\Main\Diag\Debug::writeToFile($userData,"userData 2","/local/logs/userorder.txt");
        $user = new CUser;
        $arAuthResult = $user->Add(array(
            'LOGIN' => trim($userData['NEW_LOGIN']),
            'NAME' => $userData['NEW_NAME'],
            'LAST_NAME' => $userData['NEW_LAST_NAME'],
            'PASSWORD' => $userData['NEW_PASSWORD'],
            'CONFIRM_PASSWORD' => $userData['NEW_PASSWORD_CONFIRM'],
            'EMAIL' => trim($userData['NEW_EMAIL']),
            'GROUP_ID' => $userData['GROUP_ID'],
            'ACTIVE' => 'Y',
            'LID' => $this->context->getSite(),
            'PERSONAL_PHONE' => isset($userProps['PHONE']) ? NormalizePhone($userProps['PHONE'], 5) : '',
            'PERSONAL_ZIP' => isset($userProps['ZIP']) ? $userProps['ZIP'] : '',
            'PERSONAL_STREET' => isset($userProps['ADDRESS']) ? $userProps['ADDRESS'] : ''
        ));
        \Bitrix\Main\Diag\Debug::writeToFile($user->LAST_ERROR,"error 2","/local/logs/userorder.txt");
        if (intval($arAuthResult) <= 0)
        {
            $this->addError(\Bitrix\Main\Localization\Loc::getMessage('STOF_ERROR_REG').((strlen($user->LAST_ERROR) > 0) ? ': '.$user->LAST_ERROR : '' ), self::AUTH_BLOCK);
        }
        else
        {
            global $USER;
            $userId = intval($arAuthResult);
            $USER->Authorize($arAuthResult);
            if ($USER->IsAuthorized())
            {
                if ($this->arParams['SEND_NEW_USER_NOTIFY'] == 'Y')
                {
                    CUser::SendUserInfo($USER->GetID(), $this->context->getSite(), Loc::getMessage('INFO_REQ'), true);
                }
            }
            else
            {
                $this->addError(Loc::getMessage('STOF_ERROR_REG_CONFIRM'), self::AUTH_BLOCK);
            }
        }

        return $userId;
    }

    private function normalizeData($data)
    {
        $result = [
            'LOGIN' => $data['NEW_EMAIL'],
            'NAME' => $data['NEW_NAME'],
            'LAST_NAME' => $data['NEW_LAST_NAME'],
            'EMAIL' => $data['NEW_EMAIL'],
            'PERSONAL_PHONE' => _normalizePhone($data['NEW_PERSONAL_PHONE']),
            'PERSONAL_STREET' => $data['ADDRESS'],
        ];
        return $result;
    }

    private function updateUser($fields, $bitrixFields)
    {
        foreach($fields as $key => $field)
        {
            if($bitrixFields[$key] != $field && !empty($field)) $arUpdate[$key] = $field;
        }
        if(!empty($arUpdate))
        {
            $updUser = new \CUser;
            $updUser->Update($bitrixFields['ID'], $arUpdate);
            if($updUser->LAST_ERROR) return false;
                else return true;
        }
    }

    protected function doAction($action)
    {
        if (is_callable(array($this, $action."Action")))
        {
            call_user_func(
                array($this, $action."Action")
            );
        }
    }
    /*
     * thankyou страница грузится по ACCOUNT_NUMBER. Получилась ошибка: при возврате с ликпей ACCOUNT_NUMBER может уже быть измененным для crm и thankyou не откроется
     * */
   protected function showOrderAction()
	{
		global $USER;
		$arResult =& $this->arResult;
		$arOrder = false;
		$arResult["USER_VALS"]["CONFIRM_ORDER"] = "Y";
		$orderId = urldecode($this->request->get('ORDER_ID'));
		if(empty($orderId)) $orderId = $this->arResult['ORDER_ID'];
		$checkedBySession = false;

		/** @var Order $order */
		if ($order = \Bitrix\Sale\Order::load($orderId))
		{
			$arOrder = $order->getFieldValues();
			$arResult["ORDER_ID"] = $arOrder["ID"];
			$arResult["ACCOUNT_NUMBER"] = $arOrder["ACCOUNT_NUMBER"];
			$arOrder["IS_ALLOW_PAY"] = $order->isAllowPay()? 'Y' : 'N';
			$checkedBySession = !empty($_SESSION['SALE_ORDER_ID']) && is_array($_SESSION['SALE_ORDER_ID'])
				&& in_array(intval($order->getId()), $_SESSION['SALE_ORDER_ID']);
		}

		if (!empty($arOrder) && ($order->getUserId() == $USER->GetID() || $checkedBySession))
		{
			foreach (GetModuleEvents("sale", "OnSaleComponentOrderOneStepFinal", true) as $arEvent)
				ExecuteModuleEventEx($arEvent, array($arResult["ORDER_ID"], &$arOrder, &$this->arParams));

			$arResult["PAYMENT"] = array();
			if ($order->isAllowPay())
			{
				$paymentCollection = $order->getPaymentCollection();
				/** @var Payment $payment */
				foreach ($paymentCollection as $payment)
				{
					$arResult["PAYMENT"][$payment->getId()] = $payment->getFieldValues();

					if (intval($payment->getPaymentSystemId()) > 0 && !$payment->isPaid())
					{
						$paySystemService = \Bitrix\Sale\PaySystem\Manager::getObjectById($payment->getPaymentSystemId());
						if (!empty($paySystemService))
						{
							$arPaySysAction = $paySystemService->getFieldsValues();

							if ($paySystemService->getField('NEW_WINDOW') === 'N' || $paySystemService->getField('ID') == PaySystem\Manager::getInnerPaySystemId())
							{
								/** @var PaySystem\ServiceResult $initResult */
								$initResult = $paySystemService->initiatePay($payment, null, \Bitrix\Sale\PaySystem\BaseServiceHandler::STRING);
								if ($initResult->isSuccess())
									$arPaySysAction['BUFFERED_OUTPUT'] = $initResult->getTemplate();
								else
									$arPaySysAction["ERROR"] = $initResult->getErrorMessages();
							}

							$arResult["PAYMENT"][$payment->getId()]['PAID'] = $payment->getField('PAID');

							$arOrder['PAYMENT_ID'] = $payment->getId();
							$arOrder['PAY_SYSTEM_ID'] = $payment->getPaymentSystemId();
							$arPaySysAction["NAME"] = htmlspecialcharsEx($arPaySysAction["NAME"]);
							$arPaySysAction["IS_AFFORD_PDF"] = $paySystemService->isAffordPdf();

							if ($arPaySysAction > 0)
								$arPaySysAction["LOGOTIP"] = \CFile::GetFileArray($arPaySysAction["LOGOTIP"]);

							if ($this->arParams['COMPATIBLE_MODE'] == 'Y' && !$payment->isInner())
							{
								// compatibility
								\CSalePaySystemAction::InitParamArrays($order->getFieldValues(), $order->getId(), '', array(), $payment->getFieldValues());
								$map = \Bitrix\Sale\CSalePaySystemAction::getOldToNewHandlersMap();
								$oldHandler = array_search($arPaySysAction["ACTION_FILE"], $map);
								if ($oldHandler !== false && !$paySystemService->isCustom())
									$arPaySysAction["ACTION_FILE"] = $oldHandler;

								if (strlen($arPaySysAction["ACTION_FILE"]) > 0 && $arPaySysAction["NEW_WINDOW"] != "Y")
								{
									$pathToAction = $this->context->getServer()->getDocumentRoot().$arPaySysAction["ACTION_FILE"];

									$pathToAction = str_replace("\\", "/", $pathToAction);
									while (substr($pathToAction, strlen($pathToAction) - 1, 1) == "/")
										$pathToAction = substr($pathToAction, 0, strlen($pathToAction) - 1);

									if (file_exists($pathToAction))
									{
										if (is_dir($pathToAction) && file_exists($pathToAction."/payment.php"))
											$pathToAction .= "/payment.php";

										$arPaySysAction["PATH_TO_ACTION"] = $pathToAction;
									}
								}

								$arResult["PAY_SYSTEM"] = $arPaySysAction;
							}

							$arResult["PAY_SYSTEM_LIST"][$payment->getPaymentSystemId()] = $arPaySysAction;
						}
						else
							$arResult["PAY_SYSTEM_LIST"][$payment->getPaymentSystemId()] = array('ERROR' => true);
					}
				}
			}

			$arResult["ORDER"] = $arOrder;
		}
		else
			$arResult["ACCOUNT_NUMBER"] = $orderId;
	}

   private function oneClickAction()
   {
        $phone = _normalizePhone($this->request->get('phone'));

        if(!checkPhone($phone)) {
            \Bitrix\Main\Diag\Debug::writeToFile($phone,"false","/local/logs/clickbuy.txt");
            return false;
        }
        $this->createOneClickOrder($phone);
   }

   private function createOneClickOrder($phone)
   {
        global $APPLICATION;
        global $USER;
        $siteId = \Bitrix\Main\Context::getCurrent()->getSite();
        if(empty($siteId)) $siteId = $this->defaultSiteId;
        $basket = \Bitrix\Sale\Basket::loadItemsForFUser(\Bitrix\Sale\Fuser::getId(), $siteId);

        if($basket->getPrice() <= 0) //от повторного оформления
        {
            \Bitrix\Main\Diag\Debug::writeToFile($phone,"false","/local/logs/clickbuy.txt");
            return false;
        }
        $userId = User::registerByPhone($phone);
        $USER->Authorize($userId);
        $order = \Bitrix\Sale\Order::create($siteId, $userId);
        $order->setPersonTypeId(1);

        $order->setBasket($basket);

        $shipmentCollection = $order->getShipmentCollection();
        $shipment = $shipmentCollection->createItem();

        $shipment->setFields(array(
           'DELIVERY_ID' => 2,
           'DELIVERY_NAME' => 'Новая почта',
           'CURRENCY' => $order->getCurrency()
        ));
        $shipmentItemCollection = $shipment->getShipmentItemCollection();

        foreach ($order->getBasket() as $item)
        {
            $shipmentItem = $shipmentItemCollection->createItem($item);
            $shipmentItem->setQuantity($item->getQuantity());
        }

        $paymentCollection = $order->getPaymentCollection();
        $extPayment = $paymentCollection->createItem();
        $extPayment->setFields(array(
            'PAY_SYSTEM_ID' => 2,
            'PAY_SYSTEM_NAME' => 'Наличный расчет',
            'SUM' => $order->getPrice()
        ));

        $propertyCollection = $order->getPropertyCollection();
        $clickProp = $propertyCollection->getItemByOrderPropertyId(19);
        $clickProp->setValue("Y");

        $phoneProp = $propertyCollection->getItemByOrderPropertyId(2);
        $phoneProp->setValue($phone);

        $order->doFinalAction(true);

        if(!empty($arData['comment'])) $order->setField('COMMENTS', $arData['comment']);
        $r = $order->save();

        if(!$r->isSuccess()){
            if ($ex = $APPLICATION->GetException())
            \Bitrix\Main\Diag\Debug::writeToFile($r->getErrorMessages(),"error","/local/logs/clickbuy.txt");
        } else {
            $this->arResult['ORDER_ID'] = $order->GetId();
            $this->arResult['ORDER'] = $order->getFieldValues();
            $this->arResult['QUICK_ORDER'] = 'Y';
            $USER->Logout();
        }
   }

    protected function doAuthorize()
    {
        global $USER;
        $request = $this->isRequestViaAjax && $this->request->get('save') != 'Y' ? $this->request->get('order') : $this->request;
        $this->arResult['USER_LOGIN'] = $request["USER_LOGIN"];
        if (strlen($request["USER_LOGIN"]) <= 0)
            $this->arResult['ERROR_SORTED']['AUTH'] = 'AUTH_ERROR!!';
        if (empty($this->arResult['ERROR_SORTED']['AUTH']))
        {
            $rememberMe = $request["USER_REMEMBER"] == 'Y' ? 'Y' : 'N';
            $arAuthResult = $USER->Login($request["USER_LOGIN"], $request["USER_PASSWORD"], $rememberMe);
            if ($arAuthResult != false && $arAuthResult["TYPE"] == "ERROR") {
                $this->arResult['ERROR_AUTH'] = $arAuthResult["MESSAGE"];
            }
        }
    }

     protected function showAuthFormAction()
    {
        $arResult = $this->arResult;

        if ($this->request->isPost())
        {
            foreach ($this as $name => $value)
            {
                if (in_array(
                    $name,
                    array(
                        'USER_LOGIN', 'USER_PASSWORD', 'do_authorize', 'NEW_NAME', 'NEW_LAST_NAME', 'NEW_EMAIL',
                        'NEW_GENERATE', 'NEW_LOGIN', 'NEW_PASSWORD', 'NEW_PASSWORD_CONFIRM', 'captcha_sid',
                        'captcha_word', 'do_register', 'is_ajax_post'
                    )
                ))
                    continue;

                if (is_array($value))
                {
                    foreach ($value as $k => $v)
                    {
                        $arResult['POST'][htmlspecialcharsbx($name.'['.$k.']')] = htmlspecialcharsbx($v);
                    }
                }
                else
                {
                    $arResult['POST'][htmlspecialcharsbx($name)] = htmlspecialcharsbx($value);
                }
            }

            if ($this->request->get('do_authorize') === 'Y')
            {
                $this->doAuthorize();
            }
            elseif ($this->request->get('do_register') === 'Y' && $arResult['AUTH']['new_user_registration'] === 'Y')
            {
                $this->doRegister();
            }
            elseif ($this->isRequestViaAjax)
            {
                $this->showAjaxAnswer(array(
                    'order' => array(
                        'SHOW_AUTH' => true,
                        'AUTH' => $arResult['AUTH']
                    )
                ));
            }
        }
        $this->processOrderAction();
    }


}
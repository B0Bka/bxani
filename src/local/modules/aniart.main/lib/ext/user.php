<?
namespace Aniart\Main\Ext;

use Aniart\Main\Exceptions\AniartException;

class User 
{
	/**
	 * Возвращаем информацию о пользователе
	 * 
	 * @param integer/boolean $userID
	 * @return array
	 */
	function GetInfo($userID = false)
	{
		global $USER;
		
		$result = array();
		
		if (!$userID) $userID = $USER->GetID();
		
		$arFilter["ID"] = $userID;
		
		$userInfo = self::GetList($arFilter, "personal_country", "desc", array("SELECT" => array("UF_*")));
		
		if (!empty($userInfo[$userID])) $result = $userInfo[$userID];
		 
		return $result;
	}

	
	/**
	 * Возвращаем дополнительное поле пользователя
	 * @param unknown $nameParam
	 * @param string $userID
	 */
	function SetParamValue($nameParam, $valueParam, $userID = false)
	{
		global $USER;
	
		$result = false;
		
		if (!$userID)
		{
			if (!$USER->IsAuthorized()) return;
			$userID = $USER->GetID();
		}
	
		$result = $USER->Update($userID, array($nameParam => $valueParam));
		
		return $result;
	}
	
	
	/**
	 * Возвращаем дополнительное поле пользователя
	 * @param unknown $nameParam
	 * @param string $userID
	 */
	function GetParamValue($nameParam, $userID = false) 
	{	
		global $USER;
		
		if (!$userID)
		{
			if (!$USER->IsAuthorized()) return;
			$userID = $USER->GetID();
		}

		$arFilter["ID"] = $userID;
		$arParameters["SELECT"] = array($nameParam);
		$arUser = self::GetList($arFilter, "personal_country", "desc", $arParameters);

		return $arUser[$userID][$nameParam];
	}
	
	/**
	 * Проверяем существует ли пользователь с указанным email
	 *
	 * @param array $arFilter
	 * @return array|boolean
	 */
	function ExistsByEmail($email) {	return self::GetList(array("ACTIVE" => "Y", "EMAIL" => $email)); }

	/**
	 * Проверяем существует ли пользователь с указанным телефоном
	 *
	 * @param array $arFilter
	 * @return array|boolean
	 */
	function ExistsByPhone($phone) {	return self::GetList(array("ACTIVE" => "Y", "PERSONAL_PHONE" => $phone)); }

	function ExistsByXmlId($xmlId) {	return self::GetList(array("ACTIVE" => "Y", "XML_ID" => $xmlId)); }
	/**
	 * Формируем список пользователей согласно указанным критериям поиска
	 *
	 * @param array $arFilter
	 * @param string $sortBy
	 * @param string $orderBy
	 * @return boolean|array
	 */	
	function GetList($arFilter, $sortBy = "personal_country", $orderBy = "desc", $arParameters = array())
	{
		$arResult = array();
		
		$rsUsers = \CUser::GetList($sortBy, $orderBy, $arFilter, $arParameters);
	
		while ($rsUser = $rsUsers->GetNext()) $arResult[$rsUser["ID"]] = $rsUser;

		if (empty($arResult))
			return false;
		else
			return $arResult;
	}
	
	function RegisterUser($arParams = array())
	{
		$password = self::CreatePassword();
		if(empty($arParams['LOGIN'])) $arParams['LOGIN'] = $arParams["EMAIL"];
		$arParams['GROUP_ID'] = explode(",", \COption::GetOptionString("main", "new_user_registration_def_group", GROUP_ALL_USERS));
		$arParams['PASSWORD'] = $password;
		$arParams['CONFIRM_PASSWORD'] = $password;
		
		$user = new \CUser;
		
		$userID = $user->Add($arParams);
		
		if (!empty($user->LAST_ERROR))
		{
			// Отправляем сообщение об ошибке администратору сайта 
			$errorMessage = $user->LAST_ERROR;
			dBug($errorMessage);
			\CEvent::SendImmediate("ERROR_MESSAGE", _SITE_ID_, array("MESSAGE" => $user->LAST_ERROR, "FILE" => __FILE__, "LINE" => __LINE__)	);
			return false;
		}
		else
		{
			// Генерируем письмо для вновь созданого пользователя для смены пароля
			//$arResult = CUser::SendPassword($arParams["LOGIN"], $arParams["EMAIL"], __SITE_ID__);
			return $userID;
		}
	}
	
	/**
	 * Функция генерирует пароль
	 * @return string
	 */
	private function CreatePassword()
	{
		$defGroup = \COption::GetOptionString("main", "new_user_registration_def_group", "");
		if($defGroup != "")
		{
			$arGroupID = explode(",", $defGroup);
			$arPolicy = \CUser::GetGroupPolicy($arGroupID);
		}
		else
		{
			$arPolicy = \CUser::GetGroupPolicy(array());
		}
	
		$passwordMinLength = intval($arPolicy["PASSWORD_LENGTH"]);
		if($passwordMinLength <= 0)
			$passwordMinLength = 6;
		$passwordChars = array(
				"abcdefghijklnmopqrstuvwxyz",
				"ABCDEFGHIJKLNMOPQRSTUVWXYZ",
				"0123456789",
		);
		if($arPolicy["PASSWORD_PUNCTUATION"] === "Y")
			$passwordChars[] = ",.<>/?;:'\"[]{}\|`~!@#\$%^&*()-_+=";
	
		return randString($passwordMinLength + 2, $passwordChars);
	}

	/**
	 * Получить ID по email
	 * @return string
	 */
	public function getUserByEmail($email)
	{
		$arUser = self::GetList(["ACTIVE" => "Y", "EMAIL" => $email], 'ID', 'ASC', ["FIELDS" => ["ID"]]);
		$user = array_shift($arUser);
		if (empty($user)) return false;
			else return $user['ID'];
	}
	
	/**
	 * Получить ID по email и коду подтверждения
	 * @return string
	 */
	public function getUserByEmailConfirm($code, $email)
	{
		$arUser = self::GetList(["ACTIVE" => "Y", "EMAIL" => $email], 'ID', 'ASC', ["FIELDS" => ["ID", "CONFIRM_CODE"]]);
		foreach($arUser as $user)
		{
			if($user['CONFIRM_CODE'] == $code) return $user['ID'];
		}
		
		return false;
	}
	
	/**
	 * Запись кода подтверждения для восстановления пароля
	 * @return string
	 */	
	public function setUserConfirmCode($email, $confirm)
	{
		$userId = self::getUserByEmail($email);
		if($userId <= 0)
			throw new AniartException(i18n('ERROR_EMAIL_NOT_FOUND', 'auth'));
		$user = new \CUser;
		$fields = ["CONFIRM_CODE" => $confirm];
		$user->Update($userId, $fields);
	}

	/**
	 * Регистрация по телефону
	 * @return string
	 */
	public function registerByPhone($phone)
	{
	    $email = self::formatEmailFromPhone($phone);
	    $userId = self::getUserByEmail($email);
	    if(empty($userId))
	        $userId = self::RegisterUser(['EMAIL' => $email]);

        return $userId;
	}

	private function formatEmailFromPhone($phone)
    {
        return $phone.'@buyer.natali.com';
    }
}
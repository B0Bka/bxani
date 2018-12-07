<?php
namespace Aniart\Main\Services;

/*
 * Авторизация пользователя через соц. сеть и обьединение с учетной записью битрикса
 */
class SocAuth
{
    private $params = [];
    private $request = '';
    private $redirectUri = '';
    private $userData = [];

    function __construct($request)
    {
        $this->request = $request;
        $this->setParams();
        $this->setData();

    }

    public function makeAuth()
    {
        global $USER;
        $user = $this->getUserData($this->userData['EMAIL']);
        if($user['ID'] > 0)
        {
            //авторизация и добавление данных
            $updateFields = $this->getCompareData($user);
            if(!empty($updateFields))
            {
                $obUser = new \CUser;
                $obUser->Update($user['ID'], $updateFields);
            }
            $USER->Authorize($user['ID']);
        }
        else
        {
            //регистрация, если емейл не нашелся
            $data = $this->userData;
            $id = \Aniart\Main\Ext\User::RegisterUser($data);

            if(!empty($id))
            {
                $retailCrm = new \Aniart\Main\Tools\retailCrmHelper;
                //add to retailCrm
                $newCustomer = $retailCrm->customerAdd($id, $data);

                //send email
                \Bitrix\Main\Mail\Event::send(array(
                    "EVENT_NAME" => "NEW_USER",
                    "LID" => "s1",
                    "C_FIELDS" => array(
                        "EMAIL" => $data['EMAIL'],
                        "USER_ID" => $id,
                        "LOGIN" => $data['LOGIN'],
                        "NAME" => $data['NAME'],
                        "LAST_NAME" => $data['LAST_NAME'],
                    ),
                ));
                $USER->Authorize($id);
            }
        }
        return true;
    }
    /*
     * По параметрам определить сервис
     */
    private function getType()
    {
        if(!empty($this->request['state']) && !empty($this->request['code'])) return 'facebook';
        elseif(!isset($this->request['state']) && !empty($this->request['code'])) return 'google';
        elseif(isset($this->request['oauth_token'])) return 'twitter';
        else return false;
    }
    
    private function setData()
    {
        $type =$this->getType();
        switch ($type)
        {
            case 'facebook': $arData = $this->getFacebookData();
                break;
            case 'google': $arData = $this->getGoogleData();
                break;
            case 'twitter': $arData = $this->getTwitterData();
                break;
        }
        $this->userData = $arData;
    }
    
    private function getGoogleData()
    {
        $params = array(
            'client_id'     => GOOGLE_KEY,
            'client_secret' => GOOGLE_SECRET,
            'redirect_uri'  => $this->redirectUri,
            'grant_type'    => 'authorization_code',
            'code'          => $this->request['code']
        );

        $url = 'https://accounts.google.com/o/oauth2/token';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, urldecode(http_build_query($params)));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($curl);
        curl_close($curl);

        $tokenInfo = json_decode($result, true);

        if (isset($tokenInfo['access_token'])) {
            $params['access_token'] = $tokenInfo['access_token'];
            $userInfo = json_decode(file_get_contents('https://www.googleapis.com/oauth2/v1/userinfo' . '?' . urldecode(http_build_query($params))), true);
        }
        if (isset($userInfo['id'])) {
            $res = $this->formatGoogleData($userInfo);
            return $res;
        }
    }
    private function formatGoogleData($fields)
    {
        $arRes = [];
        if(empty($fields['email'])) return 'empty_email';
        $arRes['EMAIL'] = $fields['email'];
        if(empty($fields['given_name']) && empty($fields['family_name'])) $arRes['NAME'] = $fields['name'];
        else
        {
            $arRes['NAME'] = $fields['given_name'];
            $arRes['LAST_NAME'] = $fields['family_name'];
        }
        if(!empty($fields['gender'])) $arRes['PERSONAL_GENDER'] = $fields['gender'] == 'male' ? 'M':'F';
        if(!empty($fields['link'])) $arRes['UF_GOOGLE'] = $fields['link'];

        return $arRes;
    }

    private function getFacebookData()
    {
        $fb = new \Facebook\Facebook([
            'app_id' => $this->params['facebook']['KEY'],
            'app_secret' => $this->params['facebook']['SECRET_KEY'],
            'default_graph_version' => 'v2.2',
        ]);
        $helper = $fb->getRedirectLoginHelper();
        try {
            $accessToken = $helper->getAccessToken();
        } catch (\Facebook\Exceptions\FacebookResponseException $e) {
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }
    
        if (!isset($accessToken)) {
            if ($helper->getError()) {
                header('HTTP/1.0 401 Unauthorized');
                echo "Error: " . $helper->getError() . "\n";
                echo "Error Code: " . $helper->getErrorCode() . "\n";
                echo "Error Reason: " . $helper->getErrorReason() . "\n";
                echo "Error Description: " . $helper->getErrorDescription() . "\n";
            } else {
                header('HTTP/1.0 400 Bad Request');
                echo 'Bad request';
            }
            exit;
        }
  
        // The OAuth 2.0 client handler helps us manage access tokens
        $oAuth2Client = $fb->getOAuth2Client();
            // Get the access token metadata from /debug_token
        $tokenMetadata = $oAuth2Client->debugToken($accessToken);

        // Validation (these will throw FacebookSDKException's when they fail)
        $tokenMetadata->validateAppId($this->params['facebook']['KEY']); // Replace {app-id} with your app id
        // If you know the user ID this access token belongs to, you can validate it here
        //$tokenMetadata->validateUserId('123');
        $tokenMetadata->validateExpiration();
    
        if (!$accessToken->isLongLived()) {
            // Exchanges a short-lived access token for a long-lived one
            try {
                $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
            } catch (\Facebook\Exceptions\FacebookSDKException $e) {
                echo "<p>Error getting long-lived access token: " . $helper->getMessage() . "</p>\n\n";
                exit;
            }    
        }
    
        $_SESSION['fb_access_token'] = (string)$accessToken;
    
        try {
            // Returns a `Facebook\FacebookResponse` object
            $response = $fb->get('/me?fields=id,first_name,last_name,gender,link,hometown,location,birthday,email', $accessToken);
        } catch (\Facebook\Exceptions\FacebookResponseException $e) {
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch (\Facebook\Exceptions\FacebookSDKException $e) {
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }
        $user = $response->getGraphUser();
        $arRes = $user->asArray();
        $res = $this->formatFacebookData($arRes);

        return $res;
    }
    private function formatFacebookData($fields)
    {
        $arRes = [];
        if(empty($fields['email'])) return 'empty_email';
        $arRes['EMAIL'] = $fields['email'];
        if(!empty($fields['first_name'])) $arRes['NAME'] = $fields['first_name'];
        if(!empty($fields['last_name'])) $arRes['LAST_NAME'] = $fields['last_name'];
        if(!empty($fields['link'])) $arRes['UF_FACEBOOK'] = $fields['link'];
        if(!empty($fields['gender'])) $arRes['PERSONAL_GENDER'] = $fields['gender'] == 'male' ? 'M':'F';
        if(!empty($fields['birthday'])) $arRes['PERSONAL_BIRTHDAY'] = $fields['birthday']->format('d.m.Y');
        return $arRes;
    }

    private function getTwitterData()
    {
        $request_token = [];
        $request_token['oauth_token'] = $_SESSION['oauth_token'];
        $request_token['oauth_token_secret'] = $_SESSION['oauth_token_secret'];
    
        if (isset($this->request['oauth_token']) && $request_token['oauth_token'] !== $this->request['oauth_token']) {
            echo ' Abort! Something is wrong.';
        }
        $connection = new \Abraham\TwitterOAuth\TwitterOAuth(TWITTER_KEY, TWITTER_SECRET, $request_token['oauth_token'], $request_token['oauth_token_secret']);
        $access_token = $connection->oauth("oauth/access_token", ["oauth_verifier" => $this->request['oauth_verifier']]);
    
        $connection = new \Abraham\TwitterOAuth\TwitterOAuth(TWITTER_KEY, TWITTER_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);
        $user = $connection->get('account/verify_credentials', ['include_email' => 'true']);
        $res = $this->formatTwitterData((array)$user);
        return $res;
    }
    private function formatTwitterData($fields)
    {
        $arRes = [];
        if(empty($fields['email'])) return 'empty_email';
        $arRes['EMAIL'] = $fields['email'];
        $arRes['UF_TWITTER'] = 'https://twitter.com/'.$fields['screen_name'];
        if(!empty($fields['name'])) $arRes['NAME'] = $fields['name'];
        if(!empty($fields['location'])) $arRes['PERSONAL_CITY'] = $fields['location'];
        return $arRes;
    }

    private function getUserData($mail)
    {
        $userList = \Bitrix\Main\UserTable::getList([
            'filter' => ['LOGIN' => $mail],
            'select' => ['ID', 'EMAIL', 'NAME', 'PERSONAL_GENDER', 'LAST_NAME', 'PERSONAL_CITY', 'PERSONAL_BIRTHDAY']
        ])->fetchAll();
        return current($userList);
    }
    /*
     * Сравнить данные с битрикса и с соц.сети
     * Записать только отсутсвующие отсутствующие
     */
    private function getCompareData($arData)
    {
        $arUpd = [];
        foreach($this->userData as $key => $field)
        {
            if(empty($arData[$key]) && !empty($field)) $arUpd[$key] = $field;
        }
        return $arUpd;
    }
    private function setParams()
    {
        $sAuth = new \Aniart\Main\Services\SocAuthList();
        $this->params = $sAuth->getParams();
        $this->redirectUri = $sAuth->getRedirectUri();
    }
}
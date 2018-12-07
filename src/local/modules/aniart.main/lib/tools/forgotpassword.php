<?
/**
 * Отправка на email ссылки для входа в личный кабинет с кодом подверждения.
 * После перехода авторизировать, если код верный.
 */

namespace Aniart\Main\Tools;

use Aniart\Main\Exceptions\AniartException,
    Aniart\Main\Ext\User;

class ForgotPassword
{
	private static $codeLength = 8;
	
	/**
	 * Отправка ссылки на email
	 * @return string
	*/
    public function sendForgotEmail($email)
	{
	    if(self::checkTimeForgot()) {
            $confirmCode = self::getConfirmCode();
            try {
                User::setUserConfirmCode($email, $confirmCode);
                $url = self::getRestoreUrl($confirmCode, $email);
                $res = \Bitrix\Main\Mail\Event::send([
                    "EVENT_NAME" => EVENT_FORGOT_PASSWORD,
                    "LID" => SITE_ID,
                    "C_FIELDS" => ["EMAIL" => $email, "URL" => $url],
                    "MESSAGE_ID" => MESS_FORGOT_PASSWORD
                ]);
                if ($res->isSuccess()){
                    $_SESSION['fgTime'] = time();
                    return i18n('RESTORE_SEND', 'auth');
                }
                else return implode('<br/>', $res->getErrorMessages());
            } catch (\Aniart\Main\Exceptions\AniartException $e) {
                return $e->getMessage();
            }
        }
        else return i18n('RESTORE_TIME', 'auth');
	}
	/**
	 * Генерация случайной строки
	 * @return string
	*/
    private function getConfirmCode()
	{
		$confirmCode = randString(self::$codeLength);
		return $confirmCode;
	}

	/**
	 * Формирование ссылки для email
	 * @return string
	*/	
    private function getRestoreUrl($code, $email)
    {
        $uri = '?'.http_build_query(['email' => $email, 'code' => $code]);
        return app()->getHttpProtocol().'://'.$_SERVER['SERVER_NAME'].RESTORE_PAGE.$uri;
    }
	
	/**
	 * Вывод текста при успешной проверке кода
	 * @return string
	*/		
	public function checkCode($code, $email)
	{
		$userId = User::getUserByEmailConfirm($code, $email);
		if($userId)
		{
			/*очистка кода подтверждения*/
			User::setUserConfirmCode($email, '');
			return ["STATUS" => "Y", "TEXT" => i18n('RESTORE_SUCCESS', 'auth')];
		}
		else return ["STATUS" => "N", "TEXT" => i18n('RESTORE_FAIL', 'auth')];
	}

    /**
     * Авторизация по ссылке происходит на событии onEpilog
     */
	public function authUserByEmailConfirm($code, $email)
    {
		$userId = User::getUserByEmailConfirm($code, $email);
		if($userId)
		{
            global $USER;
            $USER->Authorize($userId);
            return true;
        }
        else return false;
    }

    /*
     * Восстанавливать пароль можно только раз в три минуты
     */
    private function checkTimeForgot()
    {
        $pause = $_SESSION['fgTime'] + RESTORE_PAUSE_SECONDS;
        if(empty($_SESSION['fgTime']) || time() > $pause) return true;
            else return false;
    }
}
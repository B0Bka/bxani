<?php
protected function getUserResult()
{
$result = $this->userData;
$result['PERSONAL_BIRTHDAY'] = $this->getBirthdayDate();

if(isset( $this->get["email"]) && isset( $this->get["code"]))
{
$email = htmlspecialcharsEx(urldecode( $this->get["email"]));
$code = htmlspecialcharsEx( $this->get["code"]);
$result['PASSWORD_RECOVERY'] = ForgotPassword::checkCode($code, $email);
}

return $result;
}

protected function getBirthdayDate()
{
$data = $this->userData['PERSONAL_BIRTHDAY'];
if(empty($data))
{
return false;
}
$date = \DateTime::createFromFormat('d.m.Y', $data);
return date_format($date, 'Y-m-d');

}

protected function getUserData()
{
$id = $this->getUserId();
if(empty($id))
{
return false;
}
$user = CUser::GetByID($id);
return $user->Fetch();
}

protected function getUserId()
{
global $USER;
if(!is_object($USER))
{
$USER = new \CUser;
}
return IntVal($USER->GetID());
}

protected function getPostFunction()
{
return $this->post['func'];
}

protected function setError($data)
{
return ['status' => 'error', 'data' => $data];
}

protected function setOK($data)
{
return ['status' => 'success', 'data' => $data];
}

protected function ajaxSave()
{
global $USER;
if(!is_object($USER))
{
$USER = new \CUser;
}
$data = $this->post['form'];
$id = IntVal($USER->GetID());
$dateBirthday = \DateTime::createFromFormat('Y-m-d', $data['UF_BIRTHDAY']);
$married = ($data['UF_MARRIED']=='on'?'1':'');
$children = [];
foreach($data as $i=>$item)
{
if(stristr($i, 'UF_CHILD_') !== FALSE)
{
$children[] = $item;
}
}

$arData = [
'EMAIL'=>$data['EMAIL'],
'NAME'=>$data['NAME'],
'LAST_NAME'=>$data['LAST_NAME'],
'PERSONAL_PHONE'=>$data['PHONE'],
'PERSONAL_CITY'=>$data['PERSONAL_CITY'],
'PERSONAL_STREET'=>$data['PERSONAL_STREET'],
'PERSONAL_BIRTHDAY'=>date_format($dateBirthday, 'd.m.Y'),
'UF_HOUSE'=>$data['UF_HOUSE'],
'UF_FLAT'=>$data['UF_FLAT'],
'UF_MARRIED'=>$married,
'UF_CHILDREN'=>$children
];
if(!empty($data['PASSWORD']))
{
$arData['PASSWORD'] = $data['PASSWORD'];
$arData['CONFIRM_PASSWORD'] = $data['CONFIRM_PASSWORD'];
}

$validation = new FormValidation($data, 'profile');
$validationResult = $validation->checkValidation();
if($validationResult) return $this->setError($validationResult);
else
{
$result = $USER->update($id, $arData);
if($result)
{
return $this->setOK(i18n('DATA_SAVED'));
}
}
}

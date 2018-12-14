<?php
namespace Aniart\Main\Tools\FormValidation;

/**
 * Class AbstractAjaxHandler
 * @package Aniart\Main\Ajax
 */
abstract class AbstractValidation
{
    protected $requiredFields = [];

    protected $data;
    protected $langGroup;

    public function __construct($arData, $langGroup = '')
    {
        $this->data = $arData;
        $this->langGroup = $langGroup;
    }
    public function validate(){}

    public function checkEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public function getRequired()
    {
        $error = [];

        foreach($this->requiredFields as $req)
        {
            if(strlen($this->data[$req]) <= 0)
                $error[$req] = i18n('ERROR_EMPTY_'.$req, $this->langGroup);
        }
        return $error;
    }
}
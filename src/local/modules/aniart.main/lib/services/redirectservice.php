<?php

namespace Aniart\Main\Services;

use Aniart\Main\Repositories\HLBlockRepository;

class RedirectService
{
    protected $server;
    protected $redirectRules;
    
    protected $url;

    public function __construct()
    {
        $this->server = $_SERVER;
        $this->redirectRules = new HLBlockRepository(HL_REDIRECT_RULE_ID);
        $this->url = $this->getUrl();
    }
    
    public function get()
    {   
        $redirect = $this->getByUrl();
        if(!empty($redirect))
        {
            $newUrl = $redirect['UF_NEW_URL'];
            if(empty($newUrl))
            {
                \LocalRedirect('/', false, '301 Moved Permanently');
            }
            else
            {
                \LocalRedirect('https://'.$this->server['SERVER_NAME'].$newUrl, false, $redirect['UF_STATUS']);
            }
            die;
        }
    }
    
    public function getUrl()
    {
        $ex = explode("/", $this->server['REQUEST_URI']);
        $arDir = [];
        foreach($ex as $exValue)
        {
            if(strlen(trim($exValue)) > 0)
                $arDir[] = $exValue;
        }

        if($arDir[0] == "bitrix")
            return '';

        $urlParts = parse_url($this->server['REQUEST_URI']);
        $pageUrl = rtrim($urlParts['path'], '/').'/';
        $pageUrl = str_replace('%E2%80%8B/', '', $pageUrl);

        $extentions = array('php', 'html');
        $extention = end(explode('.', $urlParts['path']));
        foreach($extentions as $ext)
        {
            if($extention === $ext)
            {
                $pageUrl = substr($pageUrl, 0, strlen($pageUrl) - 1);
                break;
            }
        }

        if(!empty($urlParts['query']))
        {
            $pageUrl .= '?'.$urlParts['query'];
        }
        return urldecode($pageUrl);
    }
    
    public function getByUrl()
	{
		$data = $this->getData();
        if(empty($data) || empty($this->url))
        {
            return [];
        }
        foreach($data as $item)
        {
            $fields = $item->getFields();
            $search = strpos($this->url, $fields['UF_OLD_URL']);
            if($this->url != $fields['UF_OLD_URL'])
            {
                continue;
            }
            else
            {
                return $fields;
            }
        }
        return [];
	}
    
    public function getData()
    {
		return $this->redirectRules->getList(['ID' => 'ASC'], ['=UF_GLOBAL' => '1']);
	}

    
}

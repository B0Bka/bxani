<?php
namespace Aniart\Main\Seo;

class SeoRedirects
{
    protected $dir;

    public function __construct()
    {
        $this->dir = $_SERVER["REQUEST_URI"];
    }

    public function init()
    {
        if($this->checkDir())
        {
            $this->redirectIndex();
            $this->redirectToLower();
            $this->redirectDoubleSolidus();
            $this->redirectEmptySolidus();
            if($this->dir != $_SERVER["REQUEST_URI"])
            {
                \LocalRedirect($this->dir, false, '301 Moved Permanently');
            }
        }
    }

    private function checkDir()
    {
        if(substr_count($this->dir, '/bitrix/') <= 0
            && substr_count($this->dir, '/ajax/') <= 0
            && substr_count($this->dir, '/upload/') <= 0
            && !empty($this->dir)
        )
            return true;
        else
            return false;
    }

    private function redirectIndex()
    {
        $this->dir =  $_SERVER["REQUEST_URI"];
        if($this->dir == '/index.php')
        {
            \LocalRedirect(SITE_DIR, false, '301 Moved Permanently');
        }
        elseif(substr_count($this->dir, '/index.php') > 0 || substr_count($this->dir, '/novye_postupleniya/') > 0 ||
            substr_count($this->dir, '/catalog/') || substr_count($this->dir, '/blog/') > 0)
        {
            app('RedirectService')->get();
        }
    }

    private function redirectToLower()
    {
        $uri = explode('?', $this->dir);
		if((strlen(preg_replace('![^A-Z]+!', '', $uri[0])) > 0))
        {
            $this->dir = strtolower($uri[0]);
            if(strlen($uri[1]) > 0) $this->dir = $this->dir.'?'.$uri[1];
        }
    }

    private function redirectDoubleSolidus()
    {
		if(substr_count($this->dir, '//') > 0)
        {
            $this->dir = preg_replace('/(\/){2,}/', '$1', $this->dir);
        }
    }

    private function redirectEmptySolidus()
    {
		if(substr_count($this->dir, '?') <= 0 && substr_count($this->dir, '.') <= 0 && substr($this->dir, -1) != '/')
        {
            $this->dir .= '/';
        }
    }
}
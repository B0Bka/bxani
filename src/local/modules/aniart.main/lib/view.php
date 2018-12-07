<?php


namespace Aniart\Main;


use Aniart\Main\Exceptions\AniartException;

class View
{
	private $viewsPath;

	public function __construct($viewsPath)
	{
		$this->viewsPath = $this->normalizeViewsPath($viewsPath);
		if(!is_dir($this->viewsPath)){
			throw new AniartException('View folder "'.$this->viewsPath.'" doesn\'t exist');
		}
	}

	private function normalizeViewsPath($viewsPath)
	{
		return realpath(rtrim($viewsPath, '/'));
	}

	public function get($viewPath, $vars = array())
	{
		ob_start();
		$this->show($viewPath, $vars);
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}


	public function show($viewPath, $vars = array())
	{
		$viewFullPath = $this->viewsPath.'/'.$this->normalizeViewPath($viewPath);
		if(!is_file($viewFullPath)){
			throw new AniartException('View file "'.$viewFullPath.'" doesn\'t exist');
		}
		extract($vars);
		return require $viewFullPath;
	}

	private function normalizeViewPath($viewPath)
	{
		return ltrim($viewPath, '/').'.php';
	}
}
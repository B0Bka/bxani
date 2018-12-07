<?php
namespace Aniart\Main\Export;


abstract class AbstractExport
{
    protected $step,
              $fileName,
              $siteDir;

	public function __construct($step, $filePath) {
		$this->step = $step;
		$this->fileName = $filePath;
		if(empty($_SERVER['SERVER_NAME'])) $this->siteDir = 'https://natalibolgar.com';
		    else $this->siteDir = app()->getHttpProtocol().'://'.$_SERVER['SERVER_NAME'];
	}

    public function PrepareFile($filename)
    {
        $fullFilename = $_SERVER["DOCUMENT_ROOT"] . $filename;
        /*
         * //функция битрикса. Проверяет физическое существование указанного пути.
         * При необходимости - создает все каталоги входящие в данный путь.
         */
        CheckDirPath($fullFilename);

        if ($fp = @fopen($fullFilename, "w")){
            return $fp;
        } else {
            return false;
        }
    }

    public function CloseFile($fp)
    {
        @fclose($fp);
    }

    public function WriteOffers($fp, $offers)
    {
        @fwrite($fp, $offers);
    }

	abstract public function init();


}
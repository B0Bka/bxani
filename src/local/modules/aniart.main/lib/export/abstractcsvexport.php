<?php
namespace Aniart\Main\Export;


abstract class AbstractCsvExport
{
    protected $step,
              $fileName,
              $siteDir,
              $arHead,
              $delimeter;

	public function __construct($step, $filePath, $delimeter = ',') {
		$this->step = $step;
		$this->fileName = $filePath;
        $this->delimeter = $delimeter;
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

    public function writeRow($arData, $fp)
    {
        fputcsv($fp, $arData, $this->delimeter);
    }

	abstract public function init();


}
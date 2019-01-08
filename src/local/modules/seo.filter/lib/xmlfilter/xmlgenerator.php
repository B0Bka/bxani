<?
namespace Seo\Filter\XmlFilter;
use Seo\Filter\Meta;
class XmlGenerator
{
    private $fileName;
    private $siteDir;
    private $arData;
    private $fp;

    public function __construct($fileName, $arData) {
        $this->fileName = $fileName;
        $this->arData = $arData;
        if(empty($_SERVER['SERVER_NAME']))
            $this->siteDir = $this->getSiteDir();
        else
            $this->siteDir = Meta::getProtocol().$_SERVER['SERVER_NAME'];
    }

    public function init()
    {
        $this->fp = $this->PrepareFile($this->fileName. '.tmp');
        $this->WriteHead();

        $this->Build();

        $this->WriteFooter();
        $this->CloseFile();
        unlink($_SERVER['DOCUMENT_ROOT'] . $this->fileName);
        rename($_SERVER['DOCUMENT_ROOT'] . $this->fileName. '.tmp', $_SERVER['DOCUMENT_ROOT'] . $this->fileName);
        $this->addToXmlFile();
    }

    public function PrepareFile($filename)
    {
        $fullFilename = $_SERVER["DOCUMENT_ROOT"] . $filename;
        CheckDirPath($fullFilename);

        if ($fp = @fopen($fullFilename, "w")){
            return $fp;
        } else {
            return false;
        }
    }

    public function WriteHead()
    {
        @fwrite($this->fp,
            '<?xml version="1.0" encoding="UTF-8"?>
                <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'
        );
    }
    public function WriteFooter()
    {
        @fwrite($this->fp, "</urlset>");
    }

    protected function Build()
    {
        $str = '';
        foreach($this->arData as $row)
        {
            $str.="<url>\n";
            $str.="<loc>" .$this->siteDir.$row['FULL_PATH']. "</loc>\n";
            $str.="<lastmod>" .$this->getLastmod(). "</lastmod>\n";
            $str.="</url>\n";
        }
        $this->WriteOffers($str);
    }

    private function getLastMod()
    {
        return date("Y-m-d");
    }

    private function addToXmlFile()
    {
        if (file_exists($_SERVER['DOCUMENT_ROOT'].'/sitemap.xml'))
        {
            $foundFilter = false;
            $xml = simplexml_load_file($_SERVER['DOCUMENT_ROOT'].'/sitemap.xml');
            if(!$xml)
                return false;

            foreach ($xml as $row)
            {
                if(substr_count((string)$row->loc, 'filter.xml') > 0)
                {
                    $row->lastmod = date('c', time());
                    $foundFilter = true;
                }
            }
            if(!$foundFilter)
            {
                $imagesXml = $xml->addChild('sitemap');
                $imagesXml->addChild('loc', $this->siteDir.$this->fileName);
                $imagesXml->addChild('lastmod', date('c', time()));
            }
            $xml->asXml($_SERVER['DOCUMENT_ROOT'].'/sitemap.xml');
        }
    }

    public function WriteOffers($offers)
    {
        @fwrite($this->fp, $offers);
    }

    public function CloseFile()
    {
        @fclose($this->fp);
    }

    private function getSiteDir()
    {
        $rsSites = \CSite::GetByID(SITE_ID);
        $arSite = $rsSites->Fetch();
        return Meta::getProtocol().$arSite['SERVER_NAME'];
    }
}
?>
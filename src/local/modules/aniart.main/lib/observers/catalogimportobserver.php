<?php

namespace Aniart\Main\Observers;

use Aniart\Main\Exchange1C\ProductsImport;
use Aniart\Main\Exchange1C\OffersImport;
use Aniart\Main\Exchange1C\ProductsImportWriter;
use Aniart\Main\Exchange1C\OffersImportWriter;
use Aniart\Main\Exchange1C\RestsImport;
use Aniart\Main\Exchange1C\RestsImportWriter;
/**
 * Catalog import
 * 
 */
class CatalogImportObserver
{
	public function onBeforeCatalogImport1C($params, $path)
	{
        $request = $_REQUEST;
        if(strstr($request['filename'], 'import'))
        {
            $import = new ProductsImportWriter(
                new ProductsImport(),
                $path
            );
            return $import->write();
        }
        elseif(strstr($request['filename'], 'offers'))
        {
            $import = new OffersImportWriter(
                new OffersImport(),
                $path
            );
            return $import->write();
        }
	}

	public function OnSuccessCatalogImport1C($params, $path)
    {
        $request = $_REQUEST;
        if(strstr($request['filename'], 'rest'))
        {
            $import = new RestsImportWriter(
                new RestsImport(),
                $path
            );
            $import->write();

        }
    }
}

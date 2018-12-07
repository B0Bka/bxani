<?php
namespace Aniart\Main\Seo;
/*
 * ?page=1 - редирект на страницу без пейджинга
 * ?page>pageCount - страницы пагинации с несуществующим номером страницы, должны восприниматься как страницы с обычным get-параметром + canonical
 */
class SeoPaging
{
    public static function init($arPaging)
    {
        global $APPLICATION;
        $curDir = ACTUAL_LINK . $APPLICATION->GetCurDir();
        $curPage = intval($_REQUEST['page']);
        $NavPageCount = $arPaging['NavPageCount'];

        if ($curPage == 1 && empty($_REQUEST['func']))
            \LocalRedirect(self::removePaging(), false, '301 Moved Permanently');
        elseif ($curPage > $NavPageCount)
            seo()->setCanonical(self::removePaging());

        self::setRel($curPage, $NavPageCount, $curDir);
        self::setYandexNofollow($curPage);
    }

    public function setRel($curPage, $NavPageCount, $curDir)
    {
        global $APPLICATION;
        if (empty($curPage) && $NavPageCount > 1) {
            $seo_link_next_url = self::formatUrl($curDir, 2);
            $APPLICATION->AddHeadString('<link rel="next" href="' . $seo_link_next_url . '">', true);
        } elseif ($curPage > 1 && $curPage < $NavPageCount) {
            if ($curPage == 2) {
                $seo_link_prev_url = self::removePaging();
                $APPLICATION->AddHeadString('<link rel="prev" href="' . $seo_link_prev_url . '">', true);
            } else {
                $seo_link_prev_url = self::formatUrl($curDir, $curPage - 1);
                $APPLICATION->AddHeadString('<link rel="prev" href="' . $seo_link_prev_url . '">', true);
            }
            $seo_link_next_url = self::formatUrl($curDir, $curPage + 1);
            $APPLICATION->AddHeadString('<link rel="next" href="' . $seo_link_next_url . '">', true);

        } elseif ($curPage > 1 && $curPage == $NavPageCount) {
            if ($curPage == 2) {
                $seo_link_prev_url = self::removePaging();
            } else
                $seo_link_prev_url = self::formatUrl($curDir, $curPage - 1);
            $APPLICATION->AddHeadString('<link rel="prev" href="' . $seo_link_prev_url . '">', true);
        }
    }

    public function setYandexNofollow($curPage)
    {
        if($curPage > 1)
        {
            global $APPLICATION;
            $APPLICATION->AddHeadString('<meta name="yandex" content="noindex, follow" />', true);
        }
    }

    public function removePaging()
    {
        global $APPLICATION;
        return ACTUAL_LINK.$APPLICATION->GetCurPageParam("", array("page"));
    }

    public function formatUrl($dir, $number)
    {
        return  $dir . '?page=' . $number;
    }
}
?>
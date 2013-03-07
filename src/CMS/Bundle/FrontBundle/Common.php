<?php

namespace CMS\Bundle\FrontBundle;

/**
 * The common class
 */
class Common
{
    /**
     * @param type $slug
     *
     * @return null
     */
    public static function getSeoSlug($slug)
    {
        $seoslug = trim($slug, '/');
        $arrSeoSlug = explode('/', $seoslug);

        if (count($arrSeoSlug) == 1 && $arrSeoSlug[0] == '') {
            return null;
        } else {
            return $arrSeoSlug;
        }
    }
}
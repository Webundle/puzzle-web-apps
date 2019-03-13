<?php

namespace Puzzle\AdminBundle\Util;

use Westsworld\TimeAgo;
use Westsworld\TimeAgo\Language;

class DateUtil
{
    public static function timeAgo($date, $locale = null) {
        $now = new \DateTime();
        $language = '\Westsworld\TimeAgo\Translations\\'.ucfirst($locale);
        $timeAgo = new TimeAgo(new $language()); // default language is en (english)
        
        if (is_string($date)) {
            return $timeAgo->inWordsFromStrings($date);
        }
        
        return $timeAgo->inWords($date); // date format : Y-m-d
    }
}
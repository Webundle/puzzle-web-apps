<?php

namespace Puzzle\MediaBundle\Util;

/**
 * 
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 *
 */
class MediaUtil
{
    /**
     * Extract context
     * 
     * @param string $class
     * @return string
     */
    public static function extractContext($class){
        $parts = explode('\\', $class);
        
        $bundleName = strtolower(implode(',', str_replace("Bundle", "", preg_grep("#Bundle#", $parts))));
        $entityName = strtolower($parts[count($parts) - 1]);
        
        return $bundleName.'/'.$entityName;
    }
    
    /**
     * Supported audio extensions
     * 
     * @return string
     */
    public static function supportedAudioExtensions() {
        return 'mp3|wav|m4a|m4r|ogg';
    }
    
    /**
     * Supported picture extensions
     *
     * @return string
     */
    public static function supportedPictureExtensions() {
        return 'jpg|jpeg|png|ico|bmp|gif';
    }
    
    /**
     * Supported video extensions
     *
     * @return string
     */
    public static function supportedVideoExtensions() {
        return 'avi|mp4|webm|flv';
    }
    
    /**
     * Supported document extensions
     *
     * @return string
     */
    public static function supportedDocumentExtensions() {
        return 'doc|docx|ppt|pptx|xls|txt|pdf|html|twig';
    }
    
}
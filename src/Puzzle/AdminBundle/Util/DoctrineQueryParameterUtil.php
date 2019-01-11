<?php

namespace Puzzle\AdminBundle\Util;

class DoctrineQueryParameterUtil
{
    /**
     * Format expression for IN clause
     * @param string|array $input
     */
    public static function formatForInClause($input) {
        $array = is_array($input) ? $input : explode(',', $input);
        $list = null;
        
        foreach ($array as $key => $item){
            $list = $key <= 0 ? "'".$item."'": $list.','."'".$item."'";
        }
        
        return "(".$list.")";
    }
}
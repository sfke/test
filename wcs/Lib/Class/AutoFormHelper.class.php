<?php

/**
 * file: AutoFormHelper.class.php
 * intro: 
 * @date: 2012-8-21
 * @author: LHY
 * @version: 2.0
 */

class AutoFormHelper {

    static function getOptions($func){

        $rs = array(0=>"未知");
        if(empty($func)){
            return $rs;
        }else{
            if(function_exists($func)){
                call_user_func_array($func,array(&$rs));
            }
            return $rs;
        }
    }

}

?>
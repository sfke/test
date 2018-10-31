<?php

/**
 * file: TagLibHelper.class.php
 * intro: 
 * @date: 2012-8-21
 * @author: LHY
 * @version: 2.0
 */

class TagLibHelper {
    static function overwriteWhere(&$map,$tag = ""){
        if(function_exists($tag)){
            call_user_func_array($tag,array(&$map));
        }else{
            return;
        }
    }

    static function overwriteOrderby(&$orderby,$tag = ""){
        if(function_exists($tag)){
            call_user_func_array($tag,array(&$orderby));
        }else{
            return;
        }
    }

    static function overwriteResult(&$result,$tag = ""){
        if(function_exists($tag)){
            call_user_func_array($tag,array(&$result));
        }else{
            return;
        }
    }

    static function overwriteSql(&$sql,$param = array()){
        $tag = $param['sql'];
        if(function_exists($tag)){
            call_user_func_array($tag,array(&$sql,$param));
        }else{
            return;
        }
    }

    static function overwriteProcess(&$rs,$param = array()){
        $tag = $param['process'];
        if(function_exists($tag)){
            call_user_func_array($tag,array(&$rs,$param));
        }else{
            return;
        }
    }
}
?>
<?php

/**
 * file: ActionHelper.class.php
 * intro: 
 * @date: 2013-11-26
 * @author: LHY
 * @version: 2.0
 */

class ActionHelper {


    /*
     * 查询所有控制器
     *
     * @param   bool    $besides    是否剔除 C('SYS_MODULE_BESIDES') 模块。
     *
     * @return  array   $moduleArr  原始控制器数组
     *
     */
    static function getAllActions($besides = true){

        $modules = array();
        search(LIB_PATH.'Action/'.GROUP_NAME,'/^\w+Action.class.php$/','php',$modules);
        $moduleArr = array();
        $besides = C('SYS_MODULE_BESIDES');
        foreach($modules as $v){
            $fname = preg_match('#((\w+)Action).class.php#', $v['name'],$arr);
            if(in_array($arr[2],$besides)) continue;
            $moduleArr[] = $arr[1];
        }

        return $moduleArr;

    }
















}

?>
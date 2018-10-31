<?php
/**
 * @version    JL_WCS 2.0
 * @copyright  Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 * @Author:    LHY CL014
 */


class Permission {

    protected function __construct($data) {
        //do nothing
    }

    static function importAuthorityList($arr){
        if(!empty($arr)){
            session("_permissionList",$arr);
        }
    }

    static function getAuthorityList(){
        $data = session("_permissionList");
        return $data;
    }


    static function getAuthorityArr(){
        $data = session("_permissionList");
        $arr = array();
        if(!empty($data)){
            foreach($data as $k => $v){
                if(!empty($v)){
                    $temp = explode(",",trim($v,','));
                }else{
                    $temp = array();
                }
                $arr[$k] = $temp;
            }
        }
        return $arr;
    }

    static function check($tid=null,$act="r"){
        if(session("superUser") || session("adminUser")){
            return true;
        }

        if($tid==null){
            return false;
        }else{
            $arr = self::getAuthorityArr();
            switch($act){
                case 'r' : if(in_array($tid,$arr['read'])) return true; else return false; break;
                case 'w' : if(in_array($tid,$arr['write'])) return true; else return false; break;
                case 'c' : if(in_array($tid,$arr['check'])) return true; else return false; break;
                default : return false;
            }
        }
    }


}
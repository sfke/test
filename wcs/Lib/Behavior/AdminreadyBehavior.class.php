<?php
/**
 * @version    JL_WCS 2.0
 * @copyright  Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 * @Author:    LHY CL014
 */


class AdminreadyBehavior extends Behavior {


    public function run(&$params){
        $siteArr = getAvailableSitesArr();
        $siteid = session("currentSiteId");
        foreach($siteArr as $v){
            if( $v['id'] == $siteid ){
                $v['host'] = $_SERVER['SERVER_NAME'];
                $currentSite = $v;
                break;
            }else{
                continue;
            }
        }

        //todo 可以更友好
        if(empty($currentSite)){
           // echo "未知的站点！"; exit;
        }else{
            C('currentSite',$currentSite);
            C('SYS_DEFAULT_THEME',$currentSite['theme']);
            return;
        }
    }

}

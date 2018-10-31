<?php
/**
 * @version    JL_WCS 2.0
 * @copyright  Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 * @Author:    LHY CL014
 */

class HostviewbeginBehavior extends Behavior {
    public function run(&$params){
        //$params = "wcs/Tpl/home/default/content_zhinan.html";
        $file = $params;
        $file = str_replace(".".C('SYS_TPL_EXTEND'),"",$file);
        $file = explode("/",$file);
        $file_tpl = array_pop($file);
        $file_theme = array_pop($file);
        $tpl = (!empty($file_theme)?$file_theme.":":"").$file_tpl;
        $remark = "<!--京伦科技；营销人员：".C('COMMENT_SALES')."；首页设计：".C('COMMENT_DESIGNER')."； 切图：".C('COMMENT_ARTIST')."；程序开发：".C('COMMENT_CODER')."；上线时间：".C('COMMENT_UPLOAD_DATE')." ".$tpl." -->";
        $GLOBALS['PTL_TPL'] = $tpl;
        $GLOBALS['HTML_COMMENT']['REMARK'] = $remark;
    }
}
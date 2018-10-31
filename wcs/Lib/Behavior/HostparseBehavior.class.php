<?php
/**
 * @version    JL_WCS 2.0
 * @copyright  Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 * @Author:    LHY CL014
 */
class HostparseBehavior extends Behavior
{
    public function run(&$params)
    {
        $host = $_SERVER['SERVER_NAME'];
        $siteArr = getAvailableSitesArr();
        $currentSite = array();
        foreach ($siteArr as $v) {
            if (in_array($host, explode('|', $v['host'])) || trim($v['host']) == $host) {
                $v['host'] = $host;
                $currentSite = $v;
                break;
            } else {
                continue;
            }
        }

        //todo 可以更友好
        if (empty($currentSite)) {
            halt("请确认后台【绑定域名】和【主机地址】配置是否正确、检查【Runtime】权限！");
            return;
        } else {

            $style = $currentSite['style'];
            if (!empty($style) && $style != "default") {
                $style = "/" . $currentSite['style'];
            } else {
                $style = "";
            }

            C('currentSite', $currentSite); //指定当前站点ID
            C('TMPL_PARSE_STRING.__BASE__', C('TMPL_PARSE_STRING.__BASE__') . $currentSite['theme'] . $style);
            C('TMPL_PARSE_STRING.__FBASE__', C('TMPL_PARSE_STRING.__FBASE__') . $currentSite['theme']);
            C('SYS_DEFAULT_THEME', $currentSite['theme']);
            //操作跳转提示模板
            $jumpTpl = TMPL_PATH . 'home/' . $currentSite['theme'] . '/jump.html';
            C('TMPL_ACTION_ERROR', $jumpTpl);
            C('TMPL_ACTION_SUCCESS', $jumpTpl);
            return;
        }
    }
}
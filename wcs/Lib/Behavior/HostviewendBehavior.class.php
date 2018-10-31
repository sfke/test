<?php
/**
 * @version    JL_WCS 2.0
 * @copyright  Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 * @Author:    LHY CL014
 */


class HostviewendBehavior extends Behavior {


    public function run(&$params){
        $html_comment = $GLOBALS['HTML_COMMENT'];
        if(!empty($html_comment)){
            foreach($html_comment as $v){
                echo "\n".$v;
            }
        }
    }

}

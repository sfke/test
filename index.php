<?php
//ver JLWCS V3.0-2016-5-10
header("content-type:text/html; charset=utf-8");
define("APP_NAME",'wcs');
define("APP_PATH",'wcs/');
define("HTML_PATH",'');
define("APP_DEBUG",0);
define("ROOT_PATH",strtolower(str_replace("\\", '/', dirname(__FILE__))).'/'); //d:/wamp/www/项目/﻿
define("APP_REAL_PATH",ROOT_PATH.APP_PATH); //d:/wamp/www/项目/wcs/
define("APP_INC_PATH",APP_REAL_PATH.'Include/'); //d:/wamp/www/项目/wcs/Include/
define("APP_EXPLORT_PATH",APP_REAL_PATH.'Explort/'); //d:/wamp/www/项目/wcs/Include/Explort/﻿
require('core/core.php');
?>
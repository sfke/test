<?php
if(strtolower(substr(PHP_OS, 0, 3)) == 'win'){
         $APPGROUPLIST = 'Home,Admin';
         $DEFAULTGROUP = 'Home';
		 $URLCASEINSENSITIVE = true; //URL 不区分大小写，但是UserType模块要对应为user_type
}else{
         $APPGROUPLIST = 'home,admin';
         $DEFAULTGROUP = 'home';
		 $URLCASEINSENSITIVE = false; //URL 区分大小写
}
$sysconfig = require 'sysconfig.php'; //自定义公用变量
$comment = require 'comment.php';
$config = array(
    //项目分组
    'APP_GROUP_LIST' => $APPGROUPLIST,
    'DEFAULT_GROUP' => $DEFAULTGROUP,
    //扩展
    'APP_AUTOLOAD_PATH' => '@.TagLib', //载入自己的标签库
    'TAGLIB_BUILD_IN' => 'cx,jl', //将标签库设置为系统默认
    'LOAD_EXT_FILE' => 'tagfunction,userfunction,hookfunction', //模板辅助函数,自动加载
    'LOAD_EXT_CONFIG' => 'sysconfigs,db', // 加载扩展配置文件

    //模板常量
    'TMPL_PARSE_STRING' => array(
        '__JS__' => __ROOT__ . '/' . APP_NAME . '/Public/js', // 增加新的类库路径替换规则
        '__CSS__' => __ROOT__ . '/' . APP_NAME . '/Public/css',
        '__IMG__' => __ROOT__ . '/' . APP_NAME . '/Public/images',
        '__INC__' => __ROOT__ . '/' . APP_NAME . '/Include',
        '__BASE__' => __ROOT__ . '/' . APP_NAME . '/Tpl/home/',
        '__FBASE__' => APP_NAME . '/Tpl/home/',
		'__Upload__' => __ROOT__ . '/' . APP_NAME . '/Upload/'
    ),
    'INC' => __ROOT__ . '/' . APP_NAME . '/Include',

    //URL
    'URL_CASE_INSENSITIVE' => $URLCASEINSENSITIVE, //注意：调试模式下，这个参数是false
	
    'URL_MODEL' => 1, //需要服务器支持噢！具体参数看手册
    'URL_HTML_SUFFIX' => 'html', //假冒后缀
    'URL_PATHINFO_DEPR' => '-', //url美化 已经修改核心包，改参数不会影响后台

    //DEBUG
    'LOG_RECORD' => false, // false关闭日志记录，需要APP_DEBUG关闭
    'LOG_LEVEL' => 'ERR', // 只记录EMERG ALERT CRIT ERR 错误
    'TMPL_TRACE_FILE' => APP_PATH.'/Tpl/common/page_trace.tpl',
    'TMPL_EXCEPTION_FILE' => APP_PATH.'/Tpl/common/exception.tpl',

    //公用配置参数
    'SYS_TPL_EXTENDS' => array('asp', 'php', 'html', 'htm'), //模板替换文件
    'SYS_TPL_EXTEND' => "html", //模板替换目标文件
);
if(APP_DEBUG) $config["SHOW_PAGE_TRACE"] = false;

return array_merge($config, $sysconfig, $comment);
?>
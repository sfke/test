<?php
return array(
    'TMPL_ACTION_ERROR' => TMPL_PATH . 'admin/common/jump.html',
    'TMPL_ACTION_SUCCESS' => TMPL_PATH . 'admin/common/jump.html',
    'COOKIE_PREFIX' => 'jladmin_', //cookie前缀
    //RBAC
    'SESSION_AUTO_START' => true,
    'USER_AUTH_ON' => true,
    'USER_AUTH_TYPE' => 2, // 默认认证类型 1 登录认证 2 实时认证
    'USER_AUTH_KEY' => 'jltech', // 用户认证SESSION标记
    'ADMIN_AUTH_KEY' => 'administrator',
    'USER_AUTH_MODEL' => 'Admin', // 默认验证数据表模型
    'AUTH_PWD_ENCODER' => 'md5', // 用户认证密码加密方式
    'USER_AUTH_GATEWAY' => '/admin/public/login', // 默认认证网关
    'RBAC_ERROR_PAGE' => '/admin/public/sorry',
    'NOT_AUTH_MODULE' => 'Public', // 默认无需认证模块
    'REQUIRE_AUTH_MODULE' => '', // 默认需要认证模块
    'NOT_AUTH_ACTION' => 'head,menu,menu2,changesite', // 默认无需认证操作
    'REQUIRE_AUTH_ACTION' => '', // 默认需要认证操作
    'GUEST_AUTH_ON' => false, // 是否开启游客授权访问
    'GUEST_AUTH_ID' => 0, // 游客的用户ID
    'DB_LIKE_FIELDS' => 'title|remark',
    'RBAC_ROLE_TABLE' => C('DB_PREFIX').'role',
    'RBAC_USER_TABLE' => C('DB_PREFIX').'role_user',
    'RBAC_ACCESS_TABLE' => C('DB_PREFIX').'access',
    'RBAC_NODE_TABLE' => C('DB_PREFIX').'node',

    //RBAC额外
    'SUPER_ADMIN' => 'myphpadmin',

    //特殊内容模型
    'SYS_IMGUPLOAD_CHANNEL' => array(3), //需要图片分表的channel 目前只针对含主表的模型
	'SYS_QIKAN_CHANNEL' => array(), //期刊分表的channel 目前只针对含主表的模型
    'SYS_IMGUPLOAD_EBOOK' => array(), //需要生成电子书的图册模型栏目
    'SYS_IMGUPLOAD_ARCTYPE' => array(), //需要图片分表的channel 目前只针对单栏目
    'SYS_MSG_CHANNEL' => 5, //用户留言channel，扩展模型需要指定channel

    //自定义后台变量
    'SYS_MODULE_BESIDES' => array('Ajax', 'Form', 'Base', 'Ext', 'Extform'), //创建功能模块时排除在外的模型
    'SYS_ACTION_BESIDES' => array('_initialize'), //创建权限结点时排除在外的方法
    'SYS_PUSH_SYN' => 1, //推送模式： 1:同步模式 2：不同步模式
    'SYS_RSS_URI' => "http://news.baidu.com/ns?word=%C2%A5&tn=newsrss&sr=0&cl=2&rn=20&ct=0"
);

?>
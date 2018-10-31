<?php
return array(
    //Hostparse 也有相应处理
    'TMPL_ACTION_ERROR' => TMPL_PATH . 'home/common/jump.html',
    'TMPL_ACTION_SUCCESS' => TMPL_PATH . 'home/common/jump.html',

    'MEMBER_AUTH_KEY' => 'jlmember', //用于前台登录时的session验证的KEY
    'COOKIE_PREFIX' => 'jl_', //cookie前缀
    'COOKIE_EXPIRE' => 3600 * 24 * 14
);
?>
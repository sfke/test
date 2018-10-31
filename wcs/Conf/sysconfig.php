<?php
return array(
    'SYS_DEFAULT_IMG' => '__IMG__/default.gif', //无图片时默认显示
    'SYS_DEFAULT_THEME' => 'default', //系统默认主题
    'SYS_CACHE_TIME' => 3600 * 24, //系统缓存时间 默认一小时
    'SYS_ILLEGA_WORDS' => '战争|法轮功',

    //flag相关
    'SYS_FLAG_ARRAY'			=>array('c'=>'推荐', 's' => '首页'),
    //'SYS_FLAG_ARRAY' => array('h' => '头条', 's' => '首页', 'f' => '幻灯', 'c' => '知名专家'),
    //'SYS_SHOP_FLAG_ARRAY'  		=>array('h'=>'热卖','n'=>'最新','b'=>'精品','c'=>'推荐'),
    'SYS_SHOP_FLAG_ARRAY' => array('h' => '热卖', 'n' => '最新', 'b' => '精品', 'c' => '推荐'),

    //前台长文章分页相关
    'SYS_PAGE_BREAK' => '<hr style="page-break-after:always;" class="ke-pagebreak" />',
    'SYS_PAGE_BREAK_FIELDS' => array('txt'), //指定分页字段，设为空就是不分页

    //商城运费相关
    'SYS_SHOP_DEFAULT_CENTER' => 13, //快递默认仓库位置 省级别
    'SYS_SHIPPING_HEAD_WEIGHT' => 1000, //快递首重 单位g
    'SYS_SHIPPING_WEIGHT_UNIT' => 1000, //快递超重时计算超重单位，即每超多少g加钱
);
?>
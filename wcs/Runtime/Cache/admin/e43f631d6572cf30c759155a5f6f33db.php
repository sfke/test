<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/><title>CMS</title><link href="__CSS__/common.css" rel="stylesheet"/><link href="__CSS__/top.css" rel="stylesheet"/><script type="text/javascript" src="__JS__/jquery-1.7.2.min.js"></script></head><body><div class="jlcms_top"><div class="jlcms_logo"><img src="__IMG__/khlogo.png" style="height:50px;margin:2px 20px;"/></div><!--导航区域 begin--><div class="jlcms_nav" id="nav"><ul id="navigation"><li style="background:none;width:2%;">&nbsp;</li><?php if(is_array($topnav)): $i = 0; $__LIST__ = $topnav;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li class="head_li head_li_<?php echo ($vo['id']); ?>"><a href="__GROUP__/index/menu?navid=<?php echo ($vo['id']); ?>" target="menu" onclick="changeStyle(<?php echo ($vo['id']); ?>)"><?php echo ($vo['name']); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?></ul></div><!--导航区域 end--></div><!--
<div class="quick_menu"><div class="time" id="time">现在时间：2012年10月12日 星期四 17:48:51</div><span id="test"></span><div class="loginout"><span class="l_home"><a href="#">网站首页</a></span><span class="l_out"><a href="#">安全退出</a></span></div><div class="clear"></div></div>
--></body><script>
    function changeStyle(id) {
        $(".head_li").removeClass('nav_on');
        $(".head_li_" + id).addClass('nav_on');
    }

    $(function () {
        $(".head_li_1").addClass('nav_on');

    });
</script></body></html>
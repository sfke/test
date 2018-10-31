<?php if (!defined('THINK_PATH')) exit();?>﻿<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/><title>LEFT</title><link href="__CSS__/common.css" rel="stylesheet"/><link href="__CSS__/left.css" rel="stylesheet"/><link rel="stylesheet" href="__CSS__/zTreeStyle.css" type="text/css"><script type="text/javascript" src="__JS__/jquery-1.7.2.min.js"></script><script type="text/javascript" src="__JS__/jquery.ztree.all-3.3.min.js"></script><script type="text/javascript">
        function alt() {
            var height = document.documentElement.clientHeight - 35 + "px";
            document.getElementById("n_zone_height").style.height = height;
        }

        function switchSite(sid) {
            if (confirm("你确定要切换站点？")) {
                parent.location.href = "<?php echo U('index/changesite');?>?sid=" + sid;
            } else {
                location.href = location.href;
            }
        }
    </script></head><body style="background:#E0E0E3 url(__IMG__/left_bg.jpg) repeat-y right;" onload="alt();"><!--左侧区域 begin--><div class="left"><div class="fix"><div class="user"><p><span style="color:#5BBED2;"><?php echo ($userid); ?></span>&nbsp;
                <?php if( is_array($siteArr) && count($siteArr) >1 ){ ?><select name="siteid" style="width:80px;" onchange="switchSite(this.value)"><?php if(is_array($siteArr)): $i = 0; $__LIST__ = $siteArr;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo['id']); ?>"
                        <?php if(($vo['id']) == $_SESSION['currentSiteId']): ?>selected<?php endif; ?>
                        ><?php echo ($vo['name']); ?></option><?php endforeach; endif; else: echo "" ;endif; ?></select><?php } else { echo "站点：".$siteArr[0]['name']; } ?></p></div></div><!--导航菜单 begin--><div class="nav_zone" id="n_zone_height"><div class="nav"><div class="nav_top"></div><h3><?php echo ($navname); ?></h3><ul><?php if(is_array($module)): $i = 0; $__LIST__ = $module;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if($vo['url'] != '' ): ?><li><a href="<?php echo ($vo['url']); ?>" target="content"><?php echo ($vo['name']); ?></a></li><?php else: ?><li><a href="__GROUP__/<?php echo ($vo['module']); ?>/<?php echo ($vo['action']); ?>" target="content"><?php echo ($vo['name']); ?></a></li><?php endif; endforeach; endif; else: echo "" ;endif; ?></ul><div class="nav_bottom"></div></div></div><!--导航菜单 end--></div><!--左侧区域 end--></body></html>
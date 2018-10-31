<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/><meta http-equiv='refresh' content='<?php echo ($waitSecond); ?>; url=<?php echo ($jumpUrl); ?>'/><title>CMS</title><link href="__CSS__/common.css" rel="stylesheet"/><link href="__CSS__/right.css" rel="stylesheet"/><script type="text/javascript" src="__JS__/jquery-1.7.2.min.js"></script><style>
    .wait {color: red;}
    .tip a {color: red;}
</style></head><body><!--右侧区域 begin--><div class="right"><!--当前位置 begin--><script type="text/javascript">
        $(function(){
            $("#hideleft").toggle(function(){
                $(this).html("显示菜单");
                $(parent.ContentFrame).attr("cols","0,*");
            }, function(){
                $(this).html("隐藏菜单");
                $(parent.ContentFrame).attr("cols","280,*");
            });
        });
        window.onload=function(){
            var height=document.documentElement.clientHeight-35;
            $(".container").height(height);
        }
    </script><div class="weizhi" style="position:fixed; top:0; left:0; width:100%; _position:absolute; _top:expression(eval(document.documentElement.scrollTop+ (parseInt(this.currentStyle.marginTop,10)||0)));overflow:hidden; "><div class="weizhi_shadow"><div class="wz_left"></div><div class="wz_middle"><a href="__GROUP__/index/welcome">后台首页</a><div class="jt"></div><a href="" class="current">跳转面板</a></div><div class="wz_right"></div><div class="wz_more"><?php echo getMoreInfo();?></div><div class="clear"></div></div></div><!--当前位置 end--><!--主体区域 begin--><div class="container"><div class="alert_div"><h4 class="alert_info"><span class="welcome"><?php echo ($welcome); ?></span>您现在的位置是：跳转面板 </h4><h4 class="alert_what" style="display:none;"></h4></div><!--样式一 begin--><div class="stats"><div class="lm_title"><div class="lm_title_l"><em><?php echo ($msgTitle); ?></em></div></div><div class="lm_content"><div class="message"><div class="msg"><?php if(isset($message)): ?><div style="padding-top:20px;width:100%;"><h4 class="alert_success"><span class="success"><?php echo ($message); ?></span></h4></div><?php else: ?><div style="padding-top:20px;width:100%;"><h4 class="alert_error" style="margin:0 auto;"><?php echo ($error); ?></h4></div><?php endif; ?></div><div class="tip"><?php if(isset($closeWin)): ?>页面将在 <span class="wait"><?php echo ($waitSecond); ?></span> 秒后自动关闭，如果不想等待请点击 <a id="_href" href="<?php echo ($jumpUrl); ?>">这里</a> 关闭
                        <?php else: ?><div style="padding-top:20px;padding-bottom:40px;width:100%;"><h4 class="alert_info" style="margin:0 auto;"> 页面将在 <span class="wait"><b id="wait"><?php echo($waitSecond); ?></b></span> 秒后自动跳转，如果不想等待请点击 <a id="_href" href="<?php echo ($jumpUrl); ?>">这里</a> 跳转</h4></div><?php endif; ?></div></div><script type="text/javascript">
                    (function () {
                        var wait = document.getElementById('wait'), href = document.getElementById('_href').href;
                        var interval = setInterval(function () {
                            var time = --wait.innerHTML;
                            if (time <= 0) {
                                location.href = href;
                                clearInterval(interval);
                            }
                        }, 1000);
                    })();
                </script></div><div class="lm_bottom"><div class="lm_bottom_l"><em></em></div></div></div><!--样式一 end--></div><!--主体区域 end--></div><!--右侧区域  end--></body></html>
<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/><title>CMS</title><link href="__CSS__/common.css" rel="stylesheet"/><link href="__CSS__/right.css" rel="stylesheet"/><link rel="stylesheet" href="__INC__/form/css/zebra_form.css" type="text/css"><script type="text/javascript" language="javascript" src="__JS__/jquery-1.7.2.min.js"></script><link rel="stylesheet" href="__INC__/kindeditor/themes/default/default.css"/><!--link rel="stylesheet" href="../kindeditor/plugins/code/prettify.css" /--><script charset="utf-8" src="__INC__/kindeditor/kindeditor.js"></script><script charset="utf-8" src="__INC__/kindeditor/lang/zh_CN.js"></script><script charset="utf-8" src="__INC__/kindeditor/plugins/code/prettify.js"></script><script type="text/javascript" src="__INC__/form/js/highlight.js"></script><script type="text/javascript" src="__INC__/form/js/zebra_form.js"></script><script type="text/javascript" src="__INC__/form/js/functions.js"></script><script type="text/javascript" src="__JS__/common.js"></script><style type="text/css">
        .one {
            background: #fcfcfc;
        }
    </style></head><body><!--右侧区域 begin--><div class="right"><!--当前位置 begin--><script type="text/javascript">
        $(function(){
            $("#hideleft").toggle(function(){
                $(this).html("显示菜单");
                $(parent.ContentFrame).attr("cols","0,*");
            }, function(){
                $(this).html("隐藏菜单");
                $(parent.ContentFrame).attr("cols","280,*");
            });
            $("#hideup").toggle(function(){
                $(this).html("显示导航");
                $(parent.MainFrame).attr("rows","0,*");
            }, function(){
                $(this).html("隐藏导航");
                $(parent.MainFrame).attr("rows","90,*");
            });
        });
        window.onload=function(){
            var height=document.documentElement.clientHeight-35;
            $(".container").height(height);
        }
    </script><div class="weizhi" style="position:fixed; top:0; left:0; width:100%; _position:absolute; _top:expression(eval(document.documentElement.scrollTop+ (parseInt(this.currentStyle.marginTop,10)||0)));overflow:hidden; "><div class="weizhi_shadow"><div class="wz_left"></div><div class="wz_middle"><?php echo ($position); ?></div><div class="wz_right"></div><div class="wz_more"><?php echo getMoreInfo();?></div><div class="clear"></div></div></div><!--当前位置 end--><!--主体区域 begin--><div class="container"><div class="alert_div"><h4 class="alert_info"><span class="welcome"><?php echo ($welcome); ?></span>您现在的位置是：<?php echo ($current); ?></h4><h4 class="alert_what" style="display:none;"></h4></div><!--样式一 begin--><div class="stats"><div class="lm_title"><div class="lm_title_l"><em><?php echo ($current); ?></em></div></div><div class="lm_content"><?php echo ($form_html); ?><div class="clear"></div></div><div class="lm_bottom"><div class="lm_bottom_l"><em></em></div></div></div><!--样式一 end--></div><!--主体区域 end--></div><!--右侧区域  end--></body><script>
    var selectedArr = new Array();
    function selectToggle(name) {
        if (selectedArr[name] == undefined || selectedArr[name] == 0) {
            _selAll(name);
            selectedArr[name] = 1;
        } else {
            _selRev(name);
            selectedArr[name] = 0;
        }
    }

    function _selAll(name) {
        $("input[name='" + name + "']").each(function () {
            $(this).attr('checked', 'checked');
        });
    }

    function _selRev(name) {
        $("input[name='" + name + "']").each(function () {
            if ($(this).attr('checked') == 'checked') {
                $(this).attr('checked', false);
            } else {
                $(this).attr('checked', 'checked');
            }
        });
    }
</script></html>
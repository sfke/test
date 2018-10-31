<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/><title>CMS</title><link rel="stylesheet" href="__CSS__/common.css" type="text/css" /><link rel="stylesheet" href="__CSS__/right.css" type="text/css" /><link rel="stylesheet" href="__INC__/form/css/zebra_form.css" type="text/css" /><link rel="stylesheet" href="__INC__/kindeditor/themes/default/default.css" type="text/css" /><script type="text/javascript" src="__JS__/jquery-1.7.2.min.js"></script><script type="text/javascript" src="__INC__/kindeditor/kindeditor.js"></script><script type="text/javascript" src="__INC__/kindeditor/lang/zh_CN.js"></script><script type="text/javascript" src="__INC__/form/js/highlight.js"></script><script type="text/javascript" src="__INC__/form/js/zebra_form.js"></script><script type="text/javascript" src="__INC__/form/js/functions.js"></script><script type="text/javascript" src="__INC__/layer/layer.min.js"></script><script type="text/javascript" src="__INC__/form/js/press.js"></script><script type="text/javascript" src="__INC__/datepicker/WdatePicker.js"></script><script type="text/javascript" src="__INC__/ueditor/ueditor.config.js"></script><script type="text/javascript" src="__INC__/ueditor/ueditor.all.min.js"></script><style type="text/css">
    .one {
        background: #fcfcfc;
    }
</style><script type="text/javascript" language="javascript">
    $(document).ready(function () {
        $(".table_list tr").mouseover(function () {
            $(this).addClass("one");
        })
        $(".table_list tr").mouseout(function () {
            $(this).removeClass("one");
        })
    });

    function map(index) {
        //console.log(index);
        document.getElementById("maodian").value = index;
        //alert(document.getElementById("maodian").value)
        layer.close();
    }
</script></head><body><input type="hidden" name="imgtemp" id="imgtemp" value="<?php echo U('Enews/addMap');?>?typeid=<?php echo ($typeid); ?>"/><!--右侧区域 begin--><div class="right"><!--当前位置 begin--><script type="text/javascript">
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
    </script><div class="weizhi" style="position:fixed; top:0; left:0; width:100%; _position:absolute; _top:expression(eval(document.documentElement.scrollTop+ (parseInt(this.currentStyle.marginTop,10)||0)));overflow:hidden; "><div class="weizhi_shadow"><div class="wz_left"></div><div class="wz_middle"><?php echo ($position); ?></div><div class="wz_right"></div><div class="wz_more"><?php echo getMoreInfo();?></div><div class="clear"></div></div></div><!--当前位置 end--><!--主体区域 begin--><div class="container"><div class="alert_div"><h4 class="alert_info"><span class="welcome"><?php echo ($welcome); ?></span>您现在的位置是：<?php echo ($current); ?></h4><h4 class="alert_what" style="display:none;"></h4></div><!--样式一 begin--><div class="stats"><div class="lm_title"><div class="lm_title_l"><em><?php echo ($current); ?></em></div></div><div class="lm_content"><?php echo ($form_html); ?><div class="clear"></div></div><div class="lm_bottom"><div class="lm_bottom_l"><em></em></div></div></div><!--样式一 end--></div><!--主体区域 end--></div><!--右侧区域  end--><script>
    function chancetype() {
        location.href = "__GROUP__/enews/add?ftid=<?php echo ($ftid); ?>&typeid=" + $("#typeid").val();
    }
</script></body></html>
<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/><title>CMS</title><link href="__CSS__/common.css" rel="stylesheet"/><link href="__CSS__/right.css" rel="stylesheet"/><link rel="stylesheet" href="__INC__/form/css/zebra_form.css" type="text/css"><script type="text/javascript" language="javascript" src="__JS__/jquery-1.7.2.min.js"></script><script type="text/javascript" language="javascript" src="__JS__/FusionCharts.js"></script><script type="text/javascript" language="javascript" src="__JS__/common.js"></script><script type="text/javascript" language="javascript">
    $(document).ready(function () {
        $(".table_list tr").mouseover(function () {
            $(this).addClass("one");
        })
        $(".table_list tr").mouseout(function () {
            $(this).removeClass("one");
        })
    });
</script><style type="text/css">
    .one {
        background: #fcfcfc;
    }
</style><style type="text/css">
    #panel {
        width: 100%;
    }
    .infos_left {
        float: left;
        width: 700px;
    }
    .infos_right {
        width: 150px;
        float: right;
        margin-right: 150px;
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
    </script><div class="weizhi" style="position:fixed; top:0; left:0; width:100%; _position:absolute; _top:expression(eval(document.documentElement.scrollTop+ (parseInt(this.currentStyle.marginTop,10)||0)));overflow:hidden; "><div class="weizhi_shadow"><div class="wz_left"></div><div class="wz_middle"><?php echo ($position); ?></div><div class="wz_right"></div><div class="wz_more"><?php echo getMoreInfo();?></div><div class="clear"></div></div></div><!--当前位置 end--><!--主体区域 begin--><div class="container"><div class="alert_div"><h4 class="alert_info"><span class="welcome"><?php echo ($welcome); ?></span>您现在的位置是：<?php echo ($current); ?></h4><h4 class="alert_what" style="display:none;"></h4></div><!--样式一 begin--><div class="stats"><div class="lm_title"><div class="lm_title_l"><em>信息面板</em></div></div><div class="lm_content"><div id="panel"><div class="infos_left"><table class="Zebra_Form"><tr class="row"><td><label>快捷操作:</label></td><td><input type="button" class="button" onclick="openpanel('realdel')" value="批量彻底删除"/></td><td><input type="button" class="button" onclick="openpanel('restore')" value="批量还原"/></td></tr><form action='__GROUP__/content/recycle' method="post"><tr class="row"><input type="hidden" name="action" value="filter"></input><td><label>搜索:</label></td><td><select name="searchby"><?php echo ($searchby_html); ?></select></td><td colspan="2"><input type="text" name="searchkey" class="text" value="<?php echo ($searchkey); ?>"/></td><td><input type="submit" class="button" value="搜索"/></td></tr><tr class="row"><td><label>排序:</label></td><td><select name="orderby"><?php echo ($orderby_html); ?></select></td><td><input type="submit" class="button" value="排序"/></td></tr></form></table></div><div class="infos_right"><?php echo ($chart_html); ?></div><div class="clear"></div></div></div><div class="lm_bottom"><div class="lm_bottom_l"><em></em></div></div></div><!--样式一 end--><!--样式二 begin--><div class="stats"><div class="lm_title"><div class="lm_title_l"><em>内容列表</em></div></div><div class="lm_table"><table cellpadding="0" cellspacing="0" width="100%" class="table_list"><tr class="title" align="center"><td>选择</td><td>ID</td><td>内容标题</td><td>内容属性</td><td>内容模型</td><td>所属栏目</td><td>发布时间</td><td>操作</td></tr><?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$arcArr): $mod = ($i % 2 );++$i;?><tr class="tr_line"><td colspan="10"><div></div></td></tr><tr align="left"><td><input type="checkbox" name="_check[]" value="<?php echo ($arcArr['id']); ?>"></td><td><?php echo ($arcArr['id']); ?></td><td><?php echo ($arcArr['title']); ?></td><td><?php echo parseFlag($arcArr['flag']);?></td><td><?php echo getChannelName($arcArr['channel']);?></td><td><?php echo getArctypeName($arcArr['typeid']);?></td><td><?php echo date('Y-m-d',$arcArr['pubdate']);?></td><td width="120px"><a href="javascript:openpanel('restore',<?php echo ($arcArr['id']); ?>)">还原</a> | <a href="javascript:openpanel('realdel',<?php echo ($arcArr['id']); ?>)">彻底删除</a></td></tr><?php endforeach; endif; else: echo "" ;endif; ?></table></div><div class="lm_table_bottom"><div class="lm_table_bottom_l"><em><div class="select_btn"><input type="button" class="select_btn1" onclick="selAll()" value=""/>&nbsp;&nbsp;<input type="button" onclick="selRev()" class="select_btn2" value=""/></div><div class="page"><?php echo ($page); ?></div><div class="clear"></div></em></div></div></div><!--样式二 end--></div><!--主体区域 end--></div><!--右侧区域  end--><!--弹出层二begin--><div class="popupPanel"><div class="stats"><div class="lm_title"><div class="lm_title_l"><em id="panelTitle">做什么？</em></div></div><div class="lm_content"><table class="Zebra_Form" style="width:100%;"><tr class="row"><td width="60"><label>已选ID：</label></td><input type="hidden" id="controlitems" value=""></input><input type="hidden" id="panelaction" value=""></input><td colspan="2" id="checked_items"><label style="color:#FF00AE;"></label></td></tr><tr class="row"><td></td><td><input type="button" onclick="panelDo()" class="button" value="确定"/></td><td><input type="button" class="button" onclick="panelClose()" value="取消"/></td></tr></table></div><div class="lm_bottom"><div class="lm_bottom_l"><em></em></div></div></div></div><!--弹出层二end--></body><script>
    //开启panel窗口
    function openpanel(act, item) {
        if (typeof(item) == 'undefined' || item === undefined) {
            item = getSelects();
        }
        if (item == '') {
            jl_notify(3, "你没有选中任何项！");
            return;
        }
        $("#controlitems").val(item);
        $('#checked_items label').text(item);
        if (act == 'realdel') {
            $("#panelTitle").text("你确定要彻底删除这些内容？删除后将不能恢复！");
            $("#panelaction").val("realdel");
        } else if (act == 'restore') {
            $("#panelTitle").text("你确定要还原这些内容？");
            $("#panelaction").val("restore");
        } else {
            jl_notify(3, "不知道你到底要干什么！");
            return;
        }
        var obj = $('.popupPanel');
        jl_fadeIn(obj);
    }

    //关闭panel窗口
    function panelClose() {
        var obj = $('.popupPanel');
        jl_fadeOut(obj);
    }

    //panel执行操作
    function panelDo() {
        var act = $("#panelaction").val();
        if (act == '') {
            jl_notify(3, "不知道你到底要干什么！");
            return;
        }
        if (act == 'realdel') {
            jl_fadeOut($('.popupPanel'));
            var items = $("#controlitems").val();
            $.post("__GROUP__/ajax/realDelContent", {items: items}, function (data) {
                if (data == -1) {
                    jl_notify(2, "呜呜，彻底删除内容失败！");
                } else if (data == 1) {
                    //jl_notify(1,"嘻嘻，删除栏目成功！");
                    location.href = location.href;
                } else {
                    jl_notify(2, "呜呜，种种原因导致删除内容失败！");
                }
            });
        } else if (act == 'restore') {
            jl_fadeOut($('.popupPanel'));
            var items = $("#controlitems").val();
            $.post("__GROUP__/ajax/restoreContent", {items: items}, function (data) {
                if (data == -1) {
                    jl_notify(2, "呜呜，还原内容失败！");
                } else if (data == 1) {
                    //jl_notify(1,"嘻嘻，删除栏目成功！");
                    location.href = location.href;
                } else {
                    jl_notify(2, "呜呜，种种原因导致还原内容失败！");
                }
            });
        } else {
            jl_notify(3, "不知道你到底要干什么！");
            return;
        }
    }
</script></html>
<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/><title>CMS</title><link href="__CSS__/common.css" rel="stylesheet"/><link href="__CSS__/right.css" rel="stylesheet"/><link rel="stylesheet" href="__INC__/form/css/zebra_form.css" type="text/css"><script type="text/javascript" language="javascript" src="__JS__/jquery-1.7.2.min.js"></script><script charset="utf-8" src="__JS__/common.js"></script><script type="text/javascript" language="javascript">
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
    .infos_left {
        padding: 0px;
    }
    .Zebra_Form .row input.button {
        _width: 80px;
        _padding: 2px 10px 3px;
        _height: 22px;
    }
    .Zebra_Form .row input.text {
        width: 110px;
        _width: 90px;
        _padding: 2px 10px 3px
    }
    .Zebra_Form .row input.button2 {
        _width: 50px;
        _padding: 0 0 0;
        _overflow: hidden;
    }
    .Zebra_Form td {
        _padding: 0 0 0 10px;
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
    </script><div class="weizhi" style="position:fixed; top:0; left:0; width:100%; _position:absolute; _top:expression(eval(document.documentElement.scrollTop+ (parseInt(this.currentStyle.marginTop,10)||0)));overflow:hidden; "><div class="weizhi_shadow"><div class="wz_left"></div><div class="wz_middle"><?php echo ($position); ?></div><div class="wz_right"></div><div class="wz_more"><?php echo getMoreInfo();?></div><div class="clear"></div></div></div><!--当前位置 end--><!--主体区域 begin--><div class="container"><div class="alert_div"><h4 class="alert_info"><span class="welcome"><?php echo ($welcome); ?></span>您现在的位置是：<?php echo ($current); ?></h4><h4 class="alert_what" style="display:none;"></h4></div><!--样式一 begin--><div class="stats"><div class="lm_title"><div class="lm_title_l"><em>操作面板</em></div></div><div class="lm_content"><div id="panel"><div class="infos_left"><table class="Zebra_Form"><tr class="row"><td><label>操作:</label></td><td><input type="button" class="button" onclick="addcontent()" value="添加内容"/></td><td><input type="button" class="button" onclick="openpanel('del')" value="批量删除"/></td><td colspan="7"><input type="button" class="button" onclick="setOrder()" value="保存排序"/></td></tr><tr class="row"><form action='__GROUP__/flink/show' method="post"><input type="hidden" name="action" value="filter"/><input type="hidden" name="ftid" value="<?php echo ($ftid); ?>"/><td><label>搜索:</label></td><td><select name="searchby"><?php echo ($searchby_html); ?></select></td><td><input type="text" name="searchkey" class="text" value="<?php echo ($searchkey); ?>" style="width:100px;"/></td><td><input type="submit" class="button button2" value="搜索"/></td><td><label>排序:</label></td><td><select name="orderby"><?php echo ($orderby_html); ?></select></td><td><input type="submit" class="button button2" value="排序"/></td></form><td><label>筛选:</label></td><td colspan="2"><select id="ftypeid" class="control" onchange="changeType(this)"><option value="">- 请选择 -</option><?php echo ($options); ?></select></td></tr></table></div><div class="clear"></div></div></div><div class="lm_bottom"><div class="lm_bottom_l"><em></em></div></div></div><!--样式一 end--><!--样式二 begin--><div class="stats"><div class="lm_title"><div class="lm_title_l"><em><?php echo ($current); ?></em></div></div><div class="lm_table"><table cellpadding="0" cellspacing="0" width="100%" class="table_list"><tr class="title" align="center"><td width="40px">序号</td><td width="40px">选择号</td><td>网站名称</td><td>网站LOGO</td><td>站长email</td><td>时间</td><td>排序</td><td>状态</td><td>操作</td></tr><?php if(is_array($flinkArr)): $i = 0; $__LIST__ = $flinkArr;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr class="tr_line"><td colspan="10"><div></div></td></tr><tr align="left"><td><?php echo ($vo["id"]); ?></td><td><input type="checkbox" name="_check[]" value="<?php echo ($vo["id"]); ?>"></td><td><?php echo ($vo["title"]); ?></td><td><img src="<?php echo (($vo["logo"])?($vo["logo"]):$defaultimg); ?>" style="height:30px; margin:2px; border:1px solid #BDBCC2;"></img></td><td><?php echo ($vo["email"]); ?></td><td><?php echo date('Y-m-d',$vo['pubdate']);?></td><td><input type="text" class="text orderid" _id="<?php echo ($vo['id']); ?>" value="<?php echo ($vo['sortrank']); ?>" maxlength='3' style="width:30px;"/></td><td><?php echo status($vo['status']);?></td><td width="120px"><a href="edit?aid=<?php echo ($vo["id"]); ?>">修改</a> | <a href="javascript:openpanel('del',<?php echo ($vo['id']); ?>)">删除</a></td></tr><?php endforeach; endif; else: echo "" ;endif; ?></table></div><div class="lm_table_bottom"><div class="lm_table_bottom_l"><em><div class="select_btn"><input type="button" class="select_btn1" onclick="selAll()" value=""/>&nbsp;&nbsp;<input type="button" onclick="selRev()" class="select_btn2" value=""/></div><div class="page"></div><div class="clear"></div></em></div></div></div><!--样式二 end--></div><!--主体区域 end--></div><!--右侧区域  end--><!--弹出层二begin--><div class="popupPanel"><div class="stats"><div class="lm_title"><div class="lm_title_l"><em id="panelTitle">做什么？</em></div></div><div class="lm_content"><table class="Zebra_Form" style="width:100%;"><tr class="row"><td width="60"><label>已选ID：</label></td><input type="hidden" id="controlitems" value=""/><input type="hidden" id="panelaction" value=""/><td colspan="2" id="checked_items"><label style="color:#FF00AE;"></label></td></tr><tr class="row"><td></td><td><input type="button" onclick="panelDo()" class="button" value="确定"/></td><td><input type="button" class="button" onclick="panelClose()" value="取消"/></td></tr></table></div><div class="lm_bottom"><div class="lm_bottom_l"><em></em></div></div></div></div><!--弹出层二end--></body><script>
    function changeType(o) {
        location.href = "__GROUP__/flink/show?ftid=" + $(o).val();
    }

    /**
     *添加新内容
     */
    function addcontent() {
        location = "__GROUP__/flink/add?ftid=<?php echo ($ftid); ?>";
    }

    //开启panel窗口
    function openpanel(act, item) {
        //初始化关闭所有附加panel
        if (item === undefined || typeof(item) == 'undefined') {
            item = getSelects();
        }
        if (item == '') {
            jl_notify(3, "你没有选中任何项！");
            return;
        }
        $("#controlitems").val(item);
        $('#checked_items label').text(item);
        if (act == 'del') {
            $("#panelTitle").text("你确定要删除这些友链？");
            $("#panelaction").val("del");
            var obj = $('.popupPanel');
            jl_fadeIn(obj);
        } else {
            jl_notify(3, "不知道你到底要干什么！");
            return;
        }
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
        if (act == 'del') {
            jl_fadeOut($('.popupPanel'));
            var items = $("#controlitems").val();
            $.post("__GROUP__/ajax/delCommon", {items: items, table: 'flink'}, function (data) {
                if (data == -1) {
                    jl_notify(2, "删除这些友链失败！");
                } else if (data == 1) {
                    //jl_notify(1,"嘻嘻，删除栏目成功！");
                    location.href = location.href;
                } else {
                    jl_notify(2, "种种原因导致删除这些友链失败！");
                }
            });
        } else {
            jl_notify(3, "不知道你到底要干什么！");
            return;
        }
    }

    //批量排序
    function setOrder() {
        var orderjson = '';
        $(".orderid").each(function (e) {
            var id = $(this).attr("_id");
            var v = $(this).val();
            if (orderjson != '') orderjson = orderjson + ',';
            orderjson += "'" + id + "':'" + v + "'"
        });

        orderjson = "{" + orderjson + "}";
        orderjson = eval('(' + orderjson + ')');
        $.post("__GROUP__/ajax/SetOrderCommon", {json: orderjson, table: 'flink', field: 'sortrank'}, function (data) {
            if (data == -1) {
                jl_notify(2, "排序设置失败！");
            } else if (data == -2) {
                jl_notify(3, "排序序号请不要重复！");
            } else {
                location.href = location.href;
            }
        });
    }
</script></html>
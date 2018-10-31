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
        padding-top: 10px;
    }
    .infos_left {
        float: left;
        width: 524px;
        /*height:115px;*/
        display: inline;
    }
    .infos_right {
        width: 150px;
        float: left;
        margin-right: 150px;
        display: inline;
    }
    .Zebra_Form .row input.text {
    }
    .Zebra_Form .row input.button {
        _width: 80px;
        _padding: 2px 10px 3px;
        _height: 22px;
    }
    .Zebra_Form .row input.button2 {
        _width: 40px;
        _padding: 0 0 0;
        _overflow: hidden;
    }
    .Zebra_Form td {
        padding: 0 0 0 10px;
        _padding: 0 0 0 5px;
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
    </script><div class="weizhi" style="position:fixed; top:0; left:0; width:100%; _position:absolute; _top:expression(eval(document.documentElement.scrollTop+ (parseInt(this.currentStyle.marginTop,10)||0)));overflow:hidden; "><div class="weizhi_shadow"><div class="wz_left"></div><div class="wz_middle"><?php echo ($position); ?></div><div class="wz_right"></div><div class="wz_more"><?php echo getMoreInfo();?></div><div class="clear"></div></div></div><!--当前位置 end--><!--主体区域 begin--><div class="container"><div class="alert_div"><h4 class="alert_info"><span class="welcome"><?php echo ($welcome); ?></span>您现在的位置是：<?php echo ($current); ?></h4><h4 class="alert_what" style="display:none;"></h4></div><!--样式一 begin--><div class="stats"><div class="lm_title"><div class="lm_title_l"><em>信息面板</em></div></div><div class="lm_content"><div id="panel"><div class="infos_left"><table class="Zebra_Form"><?php
 if(Permission::check($tid,"w")){ ?><tr class="row"><td><label>新建：</label></td><td><select name="modelId" onchange="newArctype(this)"><option>--添加子栏目--</option><?php echo ($selectArr); ?></select></td><?php
 if(session("superUser")){ ?><td><input type="button" class="button" onclick="delArctype()" value="删除"/></td><td><input type="button" class="button" onclick="moveArctype()" value="栏目移动"/></td><?php
 } ?><td><input type="button" class="button" onclick="setOrder()" value="保存排序"/></td></tr><?php
 }; ?><form action='__GROUP__/arctype/show' method="post"><tr class="row"><input type="hidden" name="action" value="filter"/><input type="hidden" name="cid" value="<?php echo ($cid); ?>"/><input type="hidden" name="tid" value="<?php echo ($tid); ?>"/><td><label>搜索:</label></td><td><select name="searchby"><?php echo ($searchby_html); ?></select></td><td colspan="3"><input type="text" name="searchkey" class="text" value="<?php echo ($searchkey); ?>"/></td><td><input type="submit" class="button button2" value="搜索"/></td></tr><tr class="row"><td><label>排序:</label></td><td><select name="orderby"><?php echo ($orderby_html); ?></select></td><td colspan="3"><input type="submit" class="button button2" value="排序"/></td></tr></form></table></div><div class="infos_right"><?php echo ($chart_html); ?></div><div class="clear"></div></div></div><div class="lm_bottom"><div class="lm_bottom_l"><em></em></div></div></div><!--样式一 end--><!--样式二 begin--><div class="stats"><div class="lm_title"><div class="lm_title_l"><em>栏目列表</em></div></div><div class="lm_table"><table cellpadding="0" cellspacing="0" width="100%" class="table_list"><tr class="title" align="center"><td width="20px">ID</td><td width="30px">选择</td><td align="left">栏目名</td><td>内容模型</td><td>父栏目</td><td>排序</td><td>操作</td></tr><?php if(is_array($sonArr)): $i = 0; $__LIST__ = $sonArr;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$arcArr): $mod = ($i % 2 );++$i;?><tr class="tr_line"><td colspan="7"><div></div></td></tr><tr align="left"><td><?php echo ($arcArr['id']); ?></td><td><input type="checkbox" name="_check[]" value="<?php echo ($arcArr['id']); ?>"></td><td align="left"><?php echo ($arcArr['name']); ?></td><td><?php echo getChannelName($arcArr['channel']);?></td><td><?php echo getArctypeName($arcArr['fid']);?></td><td><input type="text" class="text orderid" _id="<?php echo ($arcArr['id']); ?>" value="<?php echo ($arcArr['order']); ?>" maxlength='3' style="width:30px;"/></td><td width="120px"><?php
 if(Permission::check($arcArr['id'],"w")){ ?><a href="edit?tid=<?php echo ($arcArr['id']); ?>&cid=<?php echo ($arcArr['channel']); ?>">修改</a><?php
 if(!session("superUser") and $arcArr['fid'] != 0){ ?>
                                 | <a href="javascript:void(0)" onclick="delArctypeOne(<?php echo ($arcArr['id']); ?>)">删除</a><?php } if(session("superUser")){ ?>
                                 | <a href="javascript:void(0)" onclick="delArctypeOne(<?php echo ($arcArr['id']); ?>)">删除</a><?php } } ?></td></tr><?php endforeach; endif; else: echo "" ;endif; ?></table></div><div class="lm_table_bottom"><div class="lm_table_bottom_l"><em><div class="select_btn"><input type="button" class="select_btn1" onclick="selAll()" value=""/>&nbsp;&nbsp;<input
                                type="button" onclick="selRev()" class="select_btn2" value=""/></div><div class="page"></div><div class="clear"></div></em></div></div></div><!--样式二 end--></div><!--主体区域 end--></div><!--右侧区域  end--><!--弹出层一 begin--><div class="arctypeSelect popupPanel"><div class="stats"><div class="lm_title"><div class="lm_title_l"><em>请选择要移动到的栏目！</em></div></div><div class="lm_content"><table class="Zebra_Form" style="width:100%"><form action="<?php echo U('form/arctypemove');?>" method="post"><input type="hidden" name="oldtid" value="<?php echo ($tid); ?>"/><input type="hidden" class="items" name="items" value=""/><tr class="row"><td><select name="totid" id="movetoselect"><?php echo ($selection_html); ?></select></td><td><input type="submit" class="button" value="移动"/></td><td><input type="button" class="button" onclick="panelClose('arctypeSelect')" value="取消"/></td></tr></form></table></div><div class="lm_bottom"><div class="lm_bottom_l"><em></em></div></div></div></div><!--弹出层一 end--><!--弹出层二begin--><div class="arctypeDel popupPanel"><div class="stats"><div class="lm_title"><div class="lm_title_l"><em>确定要删除这些栏目?</em></div></div><div class="lm_content"><table class="Zebra_Form" style="width:100%;"><tr class="row"><td width="60"><label>已选ID：</label></td><input type="hidden" id="delitems" value="" /><td colspan="2" id="del_items"><label style="color:#FF00AE;"></label></td></tr><tr class="row"><td></td><td><input type="button" onclick="delArctypeDo()" class="button" value="删除"/></td><td><input type="button" class="button" onclick="panelClose('arctypeDel')" value="取消"/></td></tr></table></div><div class="lm_bottom"><div class="lm_bottom_l"><em></em></div></div></div></div><!--弹出层二end--></body><script>
    function newArctype(obj) {
        location.href = '<?php echo U("arctype/add");?>?cid=' + obj.options[obj.selectedIndex].value + '&fid=<?php echo ($tid); ?>';
    }

    function ajaxMoveSelect() {
        var tid = <?php echo ($tid); ?>;
        var items = getSelects();
        $.post("__GROUP__/ajax/arctypeMoveSelect", {tid: tid, items: items}, function (data) {
            if (data == -1) {
                jl_notify(3, "异步获取可选父栏目失败!");
                return false;
            } else {
                $("#movetoselect").html(data);
            }
        });
    }

    function panelClose(o) {
        var obj = $('.' + o);
        jl_fadeOut(obj);
    }

    function moveArctype() {
        var items = getSelects();
        if (items == '') {
            jl_notify(3, "你没有选中任何项!");
            return;
        } else {
            ajaxMoveSelect();
            $(".arctypeSelect .items").val(items);
            var obj = $('.arctypeSelect');
            jl_fadeIn(obj);
        }
    }

    function delArctype() {
        var items = getSelects();
        if (items == '') {
            jl_notify(3, "你没有选中任何项!");
            return;
        } else {
            $("#delitems").val(items);
            $('#del_items label').text(items);
            var obj = $('.arctypeDel');
            jl_fadeIn(obj);
        }
    }

    function delArctypeOne(one) {
        if (one == '') {
            jl_notify(3, "你没有选中任何项!");
            return;
        } else {
            $("#delitems").val(one);
            $('#del_items label').text(one);
            var obj = $('.arctypeDel');
            jl_fadeIn(obj);
        }
    }


    function delArctypeDo() {
        jl_fadeOut($('.arctypeDel'));
        var items = $("#delitems").val();
        $.post("__GROUP__/ajax/deleteable", {items: items}, function (data) {
            if (data == -1) {
                jl_notify(2, "删除栏目失败！");
            } else if (data == 1) {
                location.href = location.href;
            } else {
                jl_notify(3, data + " 栏目不能删除！请先删除其子栏目！ ");
            }
        });
    }

    function setOrder() {
        var orderjson = '';
        $(".orderid").each(status = function (e) {
            var id = $(this).attr("_id");
            var v = $(this).val();
            if (orderjson != '') orderjson = orderjson + ',';
            orderjson += "'" + id + "':'" + v + "'";
        });

        orderjson = "{" + orderjson + "}";
        orderjson = eval('(' + orderjson + ')');
        $.post("__GROUP__/ajax/arctypeSetOrder", {json: orderjson}, function (data) {
            if (data == -1) {
                jl_notify(2, "排序设置失败！");
            } else if (data == -2) {
                jl_notify(3, "排序序号不要有重复的值！");
            } else {
                location.href = location.href;
            }
        });
    }
</script></html>
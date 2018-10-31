<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/><title>CMS</title><link href="__CSS__/common.css" rel="stylesheet"/><link href="__CSS__/right.css" rel="stylesheet"/><link rel="stylesheet" href="__INC__/form/css/zebra_form.css" type="text/css"><script type="text/javascript" language="javascript" src="__JS__/jquery-1.7.2.min.js"></script><script charset="utf-8" src="__JS__/common.js"></script><script type="text/javascript" src="__JS__/jquery.ztree.all-3.3.min.js"></script><link rel="stylesheet" href="__CSS__/zTreeStyle.css" type="text/css"><script type="text/javascript" language="javascript">
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
            padding: 10px 0;
        }
        .Zebra_Form .row input.button {
            _width: 80px;
            _padding: 2px 10px 3px;
            _height: 22px;
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
    </script><div class="weizhi" style="position:fixed; top:0; left:0; width:100%; _position:absolute; _top:expression(eval(document.documentElement.scrollTop+ (parseInt(this.currentStyle.marginTop,10)||0)));overflow:hidden; "><div class="weizhi_shadow"><div class="wz_left"></div><div class="wz_middle"><?php echo ($position); ?></div><div class="wz_right"></div><div class="wz_more"><?php echo getMoreInfo();?></div><div class="clear"></div></div></div><!--当前位置 end--><!--主体区域 begin--><div class="container"><div class="alert_div"><h4 class="alert_info"><span class="welcome"><?php echo ($welcome); ?></span>您现在的位置是：<?php echo ($current); ?></h4><h4 class="alert_what" style="display:none;"></h4></div><!--样式一 begin--><div class="stats"><div class="lm_title"><div class="lm_title_l"><em>操作面板</em></div></div><div class="lm_content"><div id="panel"><div class="infos_left"><table class="Zebra_Form"><tr class="row"><td><label>快捷操作:</label></td><td <?php echo ($add_style); ?> ><input type="button" class="button" onclick="addcontent()" value="添加用户组"/></td><td <?php echo ($add_style); ?> ><input type="button" class="button" onclick="autoUpdateRole()" value="一键同步功能到权限表"/></td></tr><tr class="row"><form action='__GROUP__/role/show' method="post"><input type="hidden" name="action" value="filter"></input><td><label>搜索:</label></td><td><select name="searchby"><?php echo ($searchby_html); ?></select></td><td colspan="2"><input type="text" name="searchkey" class="text" value="<?php echo ($searchkey); ?>" style="width:150px;"/></td><td><input type="submit" class="button" value="搜索"/></td><td><label>排序:</label></td><td><select name="orderby"><?php echo ($orderby_html); ?></select></td><td><input type="submit" class="button" value="排序"/></td></form></tr></table></div><div class="clear"></div></div></div><div class="lm_bottom"><div class="lm_bottom_l"><em></em></div></div></div><!--样式一 end--><!--样式二 begin--><div class="stats"><div class="lm_title"><div class="lm_title_l"><em><?php echo ($current); ?></em></div></div><div class="lm_table"><table cellpadding="0" cellspacing="0" width="100%" class="table_list"><tr class="title" align="center"><td width="100px">选择号</td><td width="100px">序号</td><td>用户组名</td><td>状态</td><td>操作</td></tr><?php if(is_array($roleArr)): $i = 0; $__LIST__ = $roleArr;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr class="tr_line"><td colspan="10"><div></div></td></tr><tr align="left"><td><input type="checkbox" name="_check[]" value="<?php echo ($vo["id"]); ?>"></td><td><?php echo ($vo["id"]); ?></td><td><?php echo ($vo["name"]); ?></td><td><?php echo status($vo['status']);?></td><td width="180px"><a href="javascript:openpanel('grant',<?php echo ($vo['id']); ?>)" title="栏目权限设置-用户组(<?php echo ($vo["name"]); ?>)" type="button">栏目权限</a> |
                                <a href="__GROUP__/role/edit?id=<?php echo ($vo["id"]); ?>">修改</a> |
                                <a href="javascript:openpanel('del',<?php echo ($vo['id']); ?>)">删除</a></td></tr><?php endforeach; endif; else: echo "" ;endif; ?></table></div><div class="lm_table_bottom"><div class="lm_table_bottom_l"><em></em></div></div></div><!--样式二 end--></div><!--主体区域 end--></div><!--右侧区域  end--><!--弹出层二begin--><div class="popupPanel" style="width:680px;margin:0px auto;"><div class="stats"><div class="lm_title"><div class="lm_title_l"><em id="panelTitle">做什么？</em></div></div><div class="lm_content"><table class="Zebra_Form" style="width:100%;"><tr class="row"><td width="60"><label>已选ID：</label></td><input type="hidden" id="controlitems" value="" /><input type="hidden" id="panelaction" value="" /><td colspan="2" id="checked_items"><label style="color:#FF00AE;"></label></td></tr><tr class="grantTr" style="display:none;"><td colspan="3"><div id="role_tree"><form action="<?php echo U('form/arctype_role_edit');?>" id="form_grant" method="post"><input type="hidden" name="uid" id="uid" value=""/><input type="hidden" name="type" value="group"/><input type="hidden" name="role_read" id="role_read"/><input type="hidden" name="role_write" id="role_write"/><input type="hidden" name="role_check" id="role_check"/><div style="width:33%;float:left;"><div class="zTreeDemoBackground left"><div> 访问权限 <input type="checkBox" name="read" onchange="selectByOneKey('read',this.checked);"/></div><ul id="treeDemo" class="ztree"></ul></div></div><div style="width:33%;float:left;"><div class="zTreeDemoBackground left"><div> 编辑权限 <input type="checkBox" name="read" onchange="selectByOneKey('write',this.checked);"/></div><ul id="treeDemo1" class="ztree"></ul></div></div><div style="width:33%;float:left;"><div class="zTreeDemoBackground left"><div> 审核权限 <input type="checkBox" name="check" onchange="selectByOneKey('check',this.checked);"/></div><ul id="treeDemo2" class="ztree"></ul></div></div></form></div></td></tr><tr class="row"><td></td><td><input type="button" onclick="panelDo()" class="button" value="确定"/></td><td><input type="button" class="button" onclick="panelClose()" value="取消"/></td></tr></table></div><div class="lm_bottom"><div class="lm_bottom_l"><em></em></div></div></div></div><!--弹出层二end--></body><script>
    function clearGrant() {
        var items = $("#controlitems").val();
        alert(items);
    }
    var setting = {
        check: {
            enable: true
        },
        data: {
            simpleData: {
                enable: true
            }
        }
    };

    function selectByOneKey(c, b) {
        switch (c) {
            case 'read' :
                arctypetreer.checkAllNodes(b);
                break;
            case 'write' :
                arctypetreew.checkAllNodes(b);
                break;
            case 'check' :
                arctypetreec.checkAllNodes(b);
                break;
            default:
                alert("未知的操作!");
                return;
        }
    }

    function autoUpdateRole() {
        $.post("__GROUP__/role/autonode", {}, function (data) {
            if (data == 1) {
                jl_notify(1, "嘻嘻，一键同步权限表成功！");
            } else {
                jl_notify(2, "呜呜，一键同步权限表失败！！严重问题！");
            }
        });
    }

    function changeType(o) {
        location.href = "__GROUP__/role/show?ftid=" + $(o).val();
    }

    /**
     *添加新内容
     */
    function addcontent() {
        location = "__GROUP__/role/add";
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
        $(".panelTips").show();
        $(".grantTr").hide();
        $(".clearGrant").hide();
        $("#controlitems").val(item);
        $('#checked_items label').text(item);
        if (act == 'del') {
            $("#panelTitle").text("你确定要删除这些友链？");
            $("#panelaction").val("del");
            var obj = $('.popupPanel');
            jl_fadeIn(obj);
        } else if (act == 'grant') {
            $(".panelTips").hide();
            $("#panelTitle").text("勾选授权栏目");
            $("#panelaction").val("grant");
            var obj = $('.popupPanel');
            var item = $("#controlitems").val();

            $.post("<?php echo U('ajax/ajaxArctypeTree');?>", {id: item, type: 'group'}, function (data) {
                if (data !== null) {
                    $(".grantTr").show();
                    $(".clearGrant").show();
                    arctypetreer = $.fn.zTree.init($("#treeDemo"), setting, data[0]);
                    arctypetreew = $.fn.zTree.init($("#treeDemo1"), setting, data[1]);
                    arctypetreec = $.fn.zTree.init($("#treeDemo2"), setting, data[2]);
                    //arctypetreer.expandAll(true);//默认全部展开
                    //arctypetreew.expandAll(true);
                    //arctypetreec.expandAll(true);
                }
            }, 'json');
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
            $.post("__GROUP__/ajax/delCommon", {items: items, table: 'role'}, function (data) {
                if (data == -1) {
                    jl_notify(2, "呜呜，删除这些友链失败！");
                } else if (data == 1) {
                    //jl_notify(1,"嘻嘻，删除栏目成功！");
                    location.href = location.href;
                } else {
                    jl_notify(2, "呜呜，种种原因导致删除这些友链失败！");
                }
            });
        } else if (act == 'grant') {
            jl_fadeOut($('.popupPanel'));
            var items = $("#controlitems").val();

            //var zTree = $.fn.zTree.getZTreeObj(tree);
            var r_checked = arctypetreer.getCheckedNodes(true);//所有已经选中的对象
            var w_checked = arctypetreew.getCheckedNodes(true);//所有已经选中的对象
            var c_checked = arctypetreec.getCheckedNodes(true);//所有已经选中的对象

            r_v = '';
            w_v = '';
            c_v = '';
            for (var i = 0; i < r_checked.length; i++) {
                r_v += r_checked[i].id + ",";
            }
            for (var i = 0; i < w_checked.length; i++) {
                w_v += w_checked[i].id + ",";
            }
            for (var i = 0; i < c_checked.length; i++) {
                c_v += c_checked[i].id + ",";
            }
            $("#uid").val(items);
            $("#role_read").val(r_v);
            $("#role_write").val(w_v);
            $("#role_check").val(c_v);
            $("#form_grant").submit();
        } else {
            jl_notify(3, "不知道你到底要干什么！");
            return;
        }
    }
</script></html>
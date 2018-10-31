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

    .lm_content {
        padding-top: 10px;
    }

    .infos_left {
        float: left;
        display: inline;
        /*height:110px;*/
    }

    .infos_right {
        width: 150px;
        float: left;
        display: inline;

    }

    .Zebra_Form .row input {
        margin: 0px;
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
        _padding: 0px 0px 0px;
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
    </script><div class="weizhi" style="position:fixed; top:0; left:0; width:100%; _position:absolute; _top:expression(eval(document.documentElement.scrollTop+ (parseInt(this.currentStyle.marginTop,10)||0)));overflow:hidden; "><div class="weizhi_shadow"><div class="wz_left"></div><div class="wz_middle"><?php echo ($position); ?></div><div class="wz_right"></div><div class="wz_more"><?php echo getMoreInfo();?></div><div class="clear"></div></div></div><!--当前位置 end--><!--主体区域 begin--><div class="container"><div class="alert_div"><h4 class="alert_info"><span class="welcome"><?php echo ($welcome); ?></span>您现在的位置是：<?php echo ($current); ?></h4><h4 class="alert_what" style="display:none;"></h4></div><!--样式一 begin--><div class="stats"><div class="lm_title"><div class="lm_title_l"><em>操作面板</em></div></div><div class="lm_content"><div id="panel"><div class="infos_left"><table class="Zebra_Form"><?php
 $write_check = Permission::check($tid,"w"); $check_check = Permission::check($tid,"c"); if( $write_check || $check_check ){ ?><tr class="row"><?php
 if($write_check){ ?><td><label>操作</label></td><td><input type="button" class="button" onclick="addcontent()" value="添加内容"/></td><td><input type="button" class="button" onclick="openpanel('del')" value="<?php if($type == 1) echo '删除'; else echo '删除'; ?> "/></td><td><input type="button" class="button" onclick="openpanel('move')" value="内容移动"/></td><td><input type="button" class="button" onclick="openpanel('copy')" value="复制" /></td><td><input type="button" class="button" onclick="$('#sortrankform').submit();" value="保存权重"/></td><?php if($type == 1){ ?><td><input type="button" class="button" onclick="openpanel('addflag')" value="添加属性"/></td><td><input type="button" class="button" onclick="openpanel('delflag')" value="移除属性"/></td><?php } } if($check_check){ ?><!--td><input type="button" class="button" onclick="openpanel('push')" value="推送" /></td--><td><input type="button" class="button" onclick="openpanel('check')" value="审核"/></td><?php }?></tr><?php }?><form action='__GROUP__/content/show' method="post"><tr class="row"><input type="hidden" name="action" value="filter"/><input type="hidden" name="cid" value="<?php echo ($cid); ?>"/><input type="hidden" name="tid" value="<?php echo ($tid); ?>"/><input type="hidden" name="type" value="<?php echo ($type); ?>"/><td><label>搜索:</label></td><td><select name="searchby"><?php echo ($searchby_html); ?></select></td><td colspan="3"><input type="text" name="searchkey" class="text" value="<?php echo ($searchkey); ?>"/></td><td><input type="submit" class="button" value="搜索"/></td></tr><tr class="row"><td><label>排序:</label></td><td><select name="orderby"><?php echo ($orderby_html); ?></select></td><td><input type="submit" class="button" value="排序"/></td></tr></form></table></div><div class="infos_right"><?php echo ($chart_html); ?></div><div class="clear"></div></div></div><div class="lm_bottom"><div class="lm_bottom_l"><em></em></div></div></div><!--样式一 end--><!--样式二 begin--><div class="stats"><div class="lm_title"><div class="lm_title_l"><em><?php echo ($current); ?></em></div></div><div class="lm_table"><?php if(($type) == "2"): ?><form id="sortrankform" action="<?php echo U('form/sortrankupdate');?>" method="post"><table cellpadding="0" cellspacing="0" width="100%" class="table_list"><tr class="title" align="center"><td>ID</td><td>选择</td><?php if(is_array($head_list)): $i = 0; $__LIST__ = $head_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><td><?php echo ($vo['name']); ?></td><?php endforeach; endif; else: echo "" ;endif; ?><td>内容模型</td><td>所属栏目</td><td>审核状态</td><td>属性</td><td>权重</td><td>操作</td></tr><?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$arcArr): $mod = ($i % 2 );++$i;?><tr class="tr_line"><td colspan="20"><div></div></td></tr><tr align="left"><td><?php echo ($arcArr['id']); ?></td><td><input type="checkbox" name="_check[]" value="<?php echo ($arcArr['id']); ?>"></td><?php if(is_array($head_list)): $i = 0; $__LIST__ = $head_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if($vo['value']=="img"){ ?><td><img src="<?php echo ($arcArr[$vo['value']]); ?>" width="50" height="50" /></td><?php  }else{ ?><td><?php echo ($arcArr[$vo['value']]); ?></td><?php  } endforeach; endif; else: echo "" ;endif; ?><td><?php echo getChannelName($arcArr['channel']);?></td><td><?php echo getArctypeName($arcArr['typeid']);?></td><td><?php echo checkStatus($arcArr['status']);?></td><td><?php echo ($arcArr['addattr']); ?></td><td><input type="text" class="text orderid" value="<?php echo ($arcArr['sortrank']); ?>" maxlength='3' style="width:20px;" name="<?php echo ($arcArr['channel']); ?>_<?php echo ($arcArr['id']); ?>"/></td><td width="150px"><?php
 if(!empty($arcArr['cdir'])){ ?><a href="http://<?php echo C('currentSite.host');?>:<?php echo C('currentSite.port'); echo contentUrl($arcArr['id'],$arcArr['channel'],'home');?>" target="_blank">预览</a> |
                    <?php } if(Permission::check($arcArr['typeid'],"r")){ ?><a href="edit?aid=<?php echo ($arcArr['id']); ?>&cid=<?php echo ($arcArr['channel']); ?>&read=1">查看</a><?php } if(Permission::check($arcArr['typeid'],"w")){ ?>
                    | <a href="edit?aid=<?php echo ($arcArr['id']); ?>&cid=<?php echo ($arcArr['channel']); ?>">修改</a>
                    | <a href="javascript:openpanel('del',<?php echo ($arcArr['id']); ?>)">删除</a><?php }?></td></tr><?php endforeach; endif; else: echo "" ;endif; ?></table></form><?php else: ?><form id="sortrankform" action="<?php echo U('form/sortrankupdate');?>" method="post"><table cellpadding="0" cellspacing="0" width="100%" class="table_list"><tr class="title" align="center"><td>ID</td><td>选择</td><td align="left">内容标题</td><td>内容属性</td><td>内容模型</td><td>所属栏目</td><td>发布时间</td><td>状态</td><td>属性</td><td>权重</td><td>操作</td></tr><?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$arcArr): $mod = ($i % 2 );++$i;?><tr class="tr_line"><td colspan="20"><div></div></td></tr><tr align="left"><td><?php echo ($arcArr['id']); ?></td><td><input type="checkbox" name="_check[]" value="<?php echo ($arcArr['id']); ?>"></td><td align="left"><?php echo msubstr($arcArr['title'],0,20);?></td><td><?php echo parseFlag($arcArr['flag']);?></td><td><?php echo getChannelName($arcArr['channel']);?></td><td><?php echo getArctypeName($arcArr['typeid']);?></td><td><?php echo date('Y-m-d',$arcArr['pubdate']);?></td><td><?php echo checkStatus($arcArr['status']);?></td><!-- <td><?php echo ($arcArr['addattr']); ?></td>--><td><?php
 if($arcArr['channel']==9){ $m = M(); $addtable = $m->table(C('DB_PREFIX').'channel')->where('id='.$arcArr['channel'])->getField("addtable"); $model = M($addtable); $where["aid"] = array('eq' ,$arcArr['id']); $addattr = $model->where($where)->getField("keshi"); echo "<font color='#FF0000'>".$addattr."</font>"; }else{ echo $arcArr['addattr']; } ?></td><td><input type="text" class="text orderid" value="<?php echo ($arcArr['sortrank']); ?>" maxlength='3' style="width:20px;" name="<?php echo ($arcArr['channel']); ?>_<?php echo ($arcArr['id']); ?>"/></td><td width="140px"><?php if($display == 1 || $display == 3 ){ ?><a href="http://<?php echo C('currentSite.host');?>:<?php echo C('currentSite.port'); echo contentUrl($arcArr['id'],false,'home');?>" target="_blank">预览</a> |
                    <?php } if(Permission::check($arcArr['typeid'],"r")){ ?><a href="edit?aid=<?php echo ($arcArr['id']); ?>&cid=<?php echo ($arcArr['channel']); ?>&read=1">查看</a><?php } if(Permission::check($arcArr['typeid'],"w")){ ?>
                    | <a href="edit?aid=<?php echo ($arcArr['id']); ?>&cid=<?php echo ($arcArr['channel']); ?>">修改</a>
                    | <a href="javascript:openpanel('del',<?php echo ($arcArr['id']); ?>)">删除</a><?php
 if( in_array($arcArr['channel'],C('SYS_IMGUPLOAD_CHANNEL'))) { ?><br/><a href='imgupload?gid=<?php echo ($arcArr[id]); ?>'><font color="#0000FF"><?php echo getChannelName($arcArr['channel']);?>传图</font></a><?php } if( in_array($arcArr['typeid'],C('SYS_IMGUPLOAD_EBOOK'))) { ?>
                    | <a href='electronic?gid=<?php echo ($arcArr[id]); ?>'><font color="#FF0000">生成期刊</font></a><?php } }?></td></tr><?php endforeach; endif; else: echo "" ;endif; ?></table></form><?php endif; ?></div><div class="lm_table_bottom"><div class="lm_table_bottom_l"><em><div class="select_btn"><input type="button" class="select_btn1" onclick="selAll()" value=""/>&nbsp;&nbsp;<input type="button" onclick="selRev()" class="select_btn2" value=""/></div><div class="page"><?php echo ($page); ?></div><div class="clear"></div></em></div></div></div><!--样式二 end--></div><!--主体区域 end--></div><!--右侧区域  end--><!--弹出层二begin--><div class="popupPanel"><div class="stats"><div class="lm_title"><div class="lm_title_l"><em id="panelTitle">做什么？</em></div></div><div class="lm_content"><table class="Zebra_Form" style="width:100%;"><tr class="row"><td width="60"><label>已选ID：</label></td><input type="hidden" id="controlitems" value=""/><input type="hidden" id="panelaction" value=""/><td colspan="2" id="checked_items"><label style="color:#FF00AE;"></label></td></tr><tr class="row movePanel" style="display:none;"><td width="80"><label>目的栏目：</label></td><td colspan="2"><select name="totid" id="movetoselect"></select></td></tr><tr class="row copyPanel" style="display:none;"><td width="80"><label>目的栏目：</label></td><td colspan="2"><select name="totid" id="copytoselect"></select></td></tr><tr class="row addflagPanel" style="display:none;"><td width="80"><label>勾选属性：</label></td><td colspan="2"><?php echo ($flagSelect_html); ?></td></tr><tr class="row sitePanel" style="display:none;"><td width="80"><label>目的站点：</label></td><td colspan="2" id="checked_site"><select name="tositeid" id="tositeselect" onchange="changeSiteArctype(this.value)"><?php if(is_array($sitesArr)): $i = 0; $__LIST__ = $sitesArr;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo['id']); ?>"><?php echo ($vo['name']); ?></option><?php endforeach; endif; else: echo "" ;endif; ?></select></td></tr><tr class="row arctypePanel" style="display:none;"><td width="80"><label>目的栏目：</label></td><td colspan="2" id="checked_arctype"><input type="hidden" id="real_cid"/><input type="hidden" id="real_tid"/><select name="totid" id="tositearctype"></select></td></tr><tr class="row"><td></td><td><input type="button" onclick="panelDo()" class="button" value="确定"/></td><td><input type="button" class="button" onclick="panelClose()" value="取消"/></td></tr></table></div><div class="lm_bottom"><div class="lm_bottom_l"><em></em></div></div></div></div><!--弹出层二end--></body><script>
/**
 *添加新内容
 */
function addcontent() {
    location = "__GROUP__/content/add?tid=<?php echo ($tid); ?>&cid=<?php echo ($cid); ?>";
}

function changeSiteArctype(sid) {
    var tid = $("#real_tid").val();
    var cid = $("#real_cid").val();
    $("#tositearctype").html('');
    $.post("__GROUP__/ajax/getArctypeForPush", {tid: tid, sid: sid, cid: cid}, function (data) {
        if (data != '') {
            $("#tositearctype").html(data);
            $(".arctypePanel").show();
        } else {
            jl_notify(2, "指定站点没有可以推送的栏目！");
            return;
        }
    });
}

//开启panel窗口
function openpanel(act, item) {
    //初始化关闭所有附加panel
    $(".movePanel").hide();
	$(".copyPanel").hide();
    $(".addflagPanel").hide();
    $(".sitePanel").hide();
    $(".arctypePanel").hide();
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
        $("#panelTitle").text("你确定要删除这些内容？");
        $("#panelaction").val("del");
        var obj = $('.popupPanel');
        jl_fadeIn(obj);
    } else if (act == 'pushdel') {
        $("#panelTitle").text("你确定要删除这些推送来的内容？");
        $("#panelaction").val("pushdel");
        var obj = $('.popupPanel');
        jl_fadeIn(obj);
    } else if (act == 'move') {
        var tid = '<?php echo ($tid); ?>';
        var cid = '<?php echo ($cid); ?>';
        var type = '<?php echo ($type); ?>';
        var items = getSelects();
        $.post("__GROUP__/ajax/contentMoveSelect", {items: items, cid: cid, type: type, tid: tid}, function (data) {
            if (data == -1) {
                jl_notify(3, "异步获取可选父栏目失败!");
                return false;
            } else if (data == -2) {
                jl_notify(3, "批量移动的内容必须属于同一内容模型！");
                return false;
            } else {
                $("#movetoselect").html(data);
                var obj = $('.popupPanel');
                jl_fadeIn(obj);
            }
        })
        $(".movePanel").show();
        $("#panelTitle").text("你确定要移动这些内容？");
        $("#panelaction").val("move");
    } else if (act == 'copy') {
        var tid = '<?php echo ($tid); ?>';
        var cid = '<?php echo ($cid); ?>';
        var type = '<?php echo ($type); ?>';
        var items = getSelects();
		if(items.split(",").length > 1){
			jl_notify(2, "复制操作一次只能选择一条!");
            return false;
		} 
        $.post("__GROUP__/ajax/contentCopySelect", {items: items, cid: cid, type: type, tid: tid}, function (data) {
            if (data == -1) {
                jl_notify(3, "异步获取可选父栏目失败!");
                return false;
            } else if (data == -2) {
                jl_notify(3, "批量复制的内容必须属于同一内容模型！");
                return false;
            } else {
                $("#copytoselect").html(data);
                var obj = $('.popupPanel');
                jl_fadeIn(obj);
            }
        })
        $(".copyPanel").show();
        $("#panelTitle").text("你确定要复制这些内容？");
        $("#panelaction").val("copy");
	}else if (act == 'addflag') {
        $(".addflagPanel").show();
        $("#panelTitle").text("你确定要给这些内容添加属性？");
        $("#panelaction").val("addflag");
        var obj = $('.popupPanel');
        jl_fadeIn(obj);
    } else if (act == 'delflag') {
        $(".addflagPanel").show();
        $("#panelTitle").text("你确定要给这些内容移除属性？");
        $("#panelaction").val("delflag");
        var obj = $('.popupPanel');
        jl_fadeIn(obj);
    } else if (act == 'check') {
        $("#panelTitle").text("你确定要审核这些内容？");
        $("#panelaction").val("check");
        var obj = $('.popupPanel');
        jl_fadeIn(obj);
    } else if (act == 'push') {
        var defaultsid = '<?php echo ($sid); ?>';
        $("#tositeselect option[value=" + defaultsid + "]").attr("selected", "selected");

        var tid = '<?php echo ($tid); ?>';
        var cid = '<?php echo ($cid); ?>';
        var type = '<?php echo ($type); ?>';
        var items = getSelects();
        $.post("__GROUP__/ajax/checkChannelUnity", {items: items, cid: cid, type: type, tid: tid}, function (data) {
            if (data != '') {
                if (data['code'] == -1) {
                    jl_notify(2, data['msg']);
                    return;
                } else if (data['code'] == 1) {
                    $("#real_cid").val(data['data']['cid']);
                    $("#real_tid").val(data['data']['tid']);
                    $(".sitePanel").show();
                    $("#panelTitle").text("请选择推送到到的位置！");
                    $("#panelaction").val("push");
                    var obj = $('.popupPanel');
                    jl_fadeIn(obj);
                    return;
                }
            }
        }, 'json');
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
        var cid = '<?php echo ($cid); ?>';
        var type = '<?php echo ($type); ?>';
        jl_fadeOut($('.popupPanel'));
        var items = $("#controlitems").val();
        $.post("__GROUP__/ajax/delContent", {items: items, cid: cid, type: type}, function (data) {
            if (data == -1) {
                jl_notify(2, "删除这些内容失败！");
            } else if (data == 1) {
                //jl_notify(1,"嘻嘻，删除栏目成功！");
                location.href = location.href;
            } else {
                jl_notify(2, "种种原因导致删除这些内容失败！");
            }
        });
    } else if (act == 'pushdel') {
        var cid = '<?php echo ($cid); ?>';
        var sid = '<?php echo ($sid); ?>';
        jl_fadeOut($('.popupPanel'));
        var items = $("#controlitems").val();
        $.post("__GROUP__/ajax/delpushdata", {items: items, cid: cid, sid: sid}, function (data) {
            if (data == -1) {
                jl_notify(2, "删除推送内容失败！");
            } else if (data == 1) {
                location.href = location.href;
            } else {
                jl_notify(2, "种种原因导致删除推送内容失败！");
            }
        });
    } else if (act == 'move') {
        jl_fadeOut($('.popupPanel'));
        var tid = $("#movetoselect").val();
        var items = $("#controlitems").val();
        var cid = '<?php echo ($cid); ?>';
        var type = '<?php echo ($type); ?>';
        $.post("__GROUP__/ajax/moveContent", {items: items, tid: tid, cid: cid, type: type}, function (data) {
            if (data == -1) {
                jl_notify(2, "移动内容失败！");
            } else if (data == 1) {
                //jl_notify(1,"嘻嘻，删除栏目成功！");
                location.href = location.href;
            } else {
                jl_notify(2, "种种原因导致移动内容失败！");
            }
        });
    }else if (act == 'copy') {
        jl_fadeOut($('.popupPanel'));
        var tid = $("#copytoselect").val();
        var items = $("#controlitems").val();
        var cid = '<?php echo ($cid); ?>';
        var type = '<?php echo ($type); ?>';
        $.post("__GROUP__/ajax/copyContent", {items: items, tid: tid, cid: cid, type: type}, function (data) {			
            if (data == -1) {
                jl_notify(2, "复制内容失败！");
            } else if (data == 1) {
				jl_notify(1, "复制内容成功！");
                location.href = location.href;
            }else if (data == 0) {
                jl_notify(2, "不能复制该栏目内容！");
			}else {
                jl_notify(2, "种种原因导致复制内容失败！");
            }
        });
    } else if (act == 'addflag') {
        jl_fadeOut($('.popupPanel'));
        var items = $("#controlitems").val();
        var flags = getSelects("flag[]");
        $.post("__GROUP__/ajax/addflag", {items: items, flags: flags}, function (data) {
            if (data == -1) {
                jl_notify(2, "添加属性失败！");
            } else if (data == 1) {
                location.href = location.href;
            } else {
                jl_notify(2, "种种原因导致添加属性失败！");
            }
        });
    } else if (act == 'delflag') {
        jl_fadeOut($('.popupPanel'));
        var items = $("#controlitems").val();
        var flags = getSelects("flag[]");
        $.post("__GROUP__/ajax/delflag", {items: items, flags: flags}, function (data) {
            if (data == -1) {
                jl_notify(2, "移除属性失败！");
            } else if (data == 1) {
                location.href = location.href;
            } else {
                jl_notify(2, "种种原因导致移除属性失败！");
            }
        });
    } else if (act == 'check') {
        jl_fadeOut($('.popupPanel'));
        var items = $("#controlitems").val();
        var cid = '<?php echo ($cid); ?>';
        var type = '<?php echo ($type); ?>';
        $.post("__GROUP__/ajax/commonCheck", {items: items, cid: cid, type: type}, function (data) {
            if (data == -1) {
                jl_notify(2, "审核内容失败！");
            } else if (data == 1) {
                location.href = location.href;
            } else {
                jl_notify(2, "审核内容失败！");
            }
        });
    } else if (act == 'push') {
        var items = $("#controlitems").val();
        var ttid = $("#tositearctype").val();
        var sid = $("#tositeselect").val();
        var tid = $("#real_tid").val();
        var cid = $("#real_cid").val();
        var type = '<?php echo ($type); ?>';
        var fsid = '<?php echo ($sid); ?>';
        if (sid == '' || sid == null) {
            jl_notify(3, "请先指定推送站点！");
            return;
        }
        if (sid == fsid) {
            jl_notify(3, "不能给本站推送内容！");
            return;
        }
        if (ttid == '' || ttid == null) {
            jl_notify(3, "请先指定推送栏目！");
            return;
        }
        $.post("__GROUP__/ajax/pushdata", {items: items, tid: tid, ttid: ttid, sid: sid, cid: cid, type: type}, function (data) {
            if (data != '') {
                if (data['code'] == -1) {
                    jl_notify(2, data['msg']);
                    return;
                } else if (data['code'] == 1) {
                    jl_notify(1, data['msg']);
                    return;
                }
            }
        }, 'json');
    } else {
        jl_notify(3, "不知道你到底要干什么！");
        return;
    }
}
</script></html>
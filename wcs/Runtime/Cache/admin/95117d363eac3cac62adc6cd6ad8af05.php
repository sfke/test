<?php if (!defined('THINK_PATH')) exit();?>﻿<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/><title>LEFT</title><link href="__CSS__/common.css" rel="stylesheet"/><link href="__CSS__/left2.css" rel="stylesheet"/><link rel="stylesheet" href="__CSS__/zTreeStyle.css" type="text/css"><script type="text/javascript" src="__JS__/jquery-1.7.2.min.js"></script><script type="text/javascript" src="__JS__/jquery.ztree.all-3.3.min.js"></script><script type="text/javascript">
        function alt() {
            var height = document.documentElement.clientHeight - 35 + "px";
            document.getElementById("n_zone_height").style.height = height;
        }
    </script><script>
        //点击回调函数 1:arctype
        function treeOnClick(event, treeId, treeNode) {
            if (treeNode.isParent == false) {
                //window.content.location.href="<?php echo U('arctype/edit');?>?tid="+treeNode.id+"&cid="+treeNode.cid;
                window.parent.frames["content"].document.location.href = "<?php echo U('arctype/edit');?>?tid=" + treeNode.id + "&cid=" + treeNode.cid;
            } else {
                //window.content.location.href="<?php echo U('arctype/show');?>?tid="+treeNode.id+"&cid="+treeNode.cid;
                window.parent.frames["content"].document.location.href = "<?php echo U('arctype/show');?>?tid=" + treeNode.id + "&cid=" + treeNode.cid;
            }
        }

        function treeBeforeClick(treeId, treeNode, clickFlag) {
            return true;
        }

        //点击回调函数 2:content
        function treeOnClick2(event, treeId, treeNode) {
            window.parent.frames["content"].document.location.href = "<?php echo U('content/show');?>?tid=" + treeNode.id + "&cid=" + treeNode.cid;
        }

        function treeBeforeClick2(treeId, treeNode, clickFlag) {
            return true;
        }

        //展开节点
        function expandNode(e) {
            var objid = e.data.objid;
            var zTree = $.fn.zTree.getZTreeObj(objid),
                    type = e.data.type,
                    nodes = zTree.getSelectedNodes();
            if (type.indexOf("All") < 0 && nodes.length == 0) {
                alert("请先选择一个父节点");
            }
            if (type == "expandAll") {
                zTree.expandAll(true);
            } else if (type == "collapseAll") {
                zTree.expandAll(false);
            } else {
                var callbackFlag = $("#callbackTrigger").attr("checked");
                for (var i = 0, l = nodes.length; i < l; i++) {
                    zTree.setting.view.fontCss = {};
                    if (type == "expand") {
                        zTree.expandNode(nodes[i], true, null, null, callbackFlag);
                    } else if (type == "collapse") {
                        zTree.expandNode(nodes[i], false, null, null, callbackFlag);
                    } else if (type == "toggle") {
                        zTree.expandNode(nodes[i], null, null, null, callbackFlag);
                    } else if (type == "expandSon") {
                        zTree.expandNode(nodes[i], true, true, null, callbackFlag);
                    } else if (type == "collapseSon") {
                        zTree.expandNode(nodes[i], false, true, null, callbackFlag);
                    }
                }
            }
        }

        $(function () {
            var setting = {
                data: {
                    simpleData: {enable: true}
                },
                callback: {
                    onClick: treeOnClick,
                    beforeClick: treeBeforeClick
                }
            };

            var setting2 = {
                data: {
                    simpleData: {enable: true}
                },
                callback: {
                    onClick: treeOnClick2,
                    beforeClick: treeBeforeClick2
                }
            };

            $.post("<?php echo U('ajax/ajaxZtree');?>", {}, function (data) {
                //	alert(data[0]);
                if (data !== null) {
                    var arctypetreeObj = $.fn.zTree.init($("#arctypetree"), setting, data);
                    var contenttreeObj = $.fn.zTree.init($("#contenttree"), setting2, data);
                    arctypetreeObj.expandAll(true);
                    contenttreeObj.expandAll(true);
                }
            }, 'json');

            $("#expandAllBtn").bind("click", {type: "expandAll", objid: 'arctypetree'}, expandNode);
            $("#collapseAllBtn").bind("click", {type: "collapseAll", objid: 'arctypetree'}, expandNode);
            $("#expandAllBtn2").bind("click", {type: "expandAll", objid: 'contenttree'}, expandNode);
            $("#collapseAllBtn2").bind("click", {type: "collapseAll", objid: 'contenttree'}, expandNode);

            //默认以选中的tab
            navid = <?php echo ($navid); ?>;
            if (typeof(navid) == 'undefined') {
                navid = 2;
            }

            $("#tbc_0" + navid).removeClass('menu_undis').addClass('menu_dis');
            $("#tb_" + navid).attr('class', 'tab_hovertab');
        });

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
                        ><?php echo ($vo['name']); ?></option><?php endforeach; endif; else: echo "" ;endif; ?></select><?php } else { echo "站点：".$siteArr[0]['name']; } ?></p></div></div><!--导航菜单 begin--><div class="nav_zone" id="n_zone_height"><div class="nav"><div class="tab"><div class="menu_undis" id="tbc_02"><div class="nav_top"></div><h3><span><a href="__GROUP__/arctype/show?tid=0&cid=-1" target="content" title="根">根</a><a id="collapseAllBtn" href="#" title="收起" onclick="return false;">收起</a><a id="expandAllBtn" href="#" title="展开" onclick="return false;">展开</a><a href="?navid=2" title="刷新">刷新</a></span><?php echo ($navname); ?></h3><div class="menu_arctype"><ul id="arctypetree" class="ztree toggle"></ul></div><div class="nav_bottom"></div></div><div class="menu_undis" id="tbc_03"><div class="nav_top"></div><h3><span><a id="collapseAllBtn2" href="#" title="收起" onclick="return false;">收起</a><a id="expandAllBtn2" href="#" title="展开" onclick="return false;">展开</a><a href="?navid=3" title="刷新">刷新</a></span><?php echo ($navname); ?></h3><div class="menu_arctype"><ul id="contenttree" class="ztree toggle"></ul></div><div class="nav_bottom"></div></div></div></div></div><!--导航菜单 end--></div><!--左侧区域 end--></body></html>
<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/><title>CMS</title><link href="__CSS__/common.css" rel="stylesheet"/><link href="__CSS__/wel.css" rel="stylesheet"/><link href="__CSS__/right.css" rel="stylesheet"/><script type="text/javascript" language="javascript" src="__JS__/jquery-1.7.2.min.js"></script><script type="text/javascript" language="javascript" src="__JS__/common.js"></script><script src="__JS__/meter/raphael.2.1.0.min.js"></script><script src="__JS__/meter/justgage.1.0.1.min.js"></script><style type="text/css">
        .one {
            background: #fcfcfc;
        }

        #center {
            margin: 0px auto;
            width: 400px;
        }

        #loading {
            width: 397px;
            height: 49px;
            background: url('__IMG__/bak.png') no-repeat;
        }

        #loading div {
            width: 0px;
            height: 48px;
            background: url('__IMG__/pro.png') no-repeat;
            color: #fff;
            text-align: center;
            font-family: Tahoma;
            font-size: 18px;
            line-height: 48px;
        }

        #message {
            height: 20px;
            font-family: Tahoma;
            font-size: 12px;
            line-height: 20px;
            text-align: left;
            padding-left: 20px;
            margin-bottom: 0px;
        }

        #cpu {
            width: 250px;
            height: 130px;
            display: inline-block;
            margin-top: 20px;
        }

        #memory {
            width: 250px;
            height: 130px;
            display: inline-block;
            margin-top: 60px;
        }

        /*开关*/

        .list {
            padding: 6px 4px;
            border-bottom: 1px dotted #d3d3d3;
            position: relative
        }

        .fun_title {
            height: 28px;
            line-height: 28px
        }

        .fun_title span {
            width: 82px;
            height: 25px;
            background: url('__IMG__/switch.gif') no-repeat;
            cursor: pointer;
            position: absolute;
            right: 6px;
            top: 16px
        }

        .fun_title span.ad_on {
            background-position: 0 -2px
        }

        .fun_title span.ad_off {
            background-position: 0 -38px
        }

        .fun_title h3 {
            font-size: 14px;
            font-family: 'microsoft yahei';
        }

        .list p {
            line-height: 20px
        }

        .list p span {
            color: #f60
        }

        .cur_select {
            background: #ffc
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
    </script><div class="weizhi" style="position:fixed; top:0; left:0; width:100%; _position:absolute; _top:expression(eval(document.documentElement.scrollTop+ (parseInt(this.currentStyle.marginTop,10)||0)));overflow:hidden; "><div class="weizhi_shadow"><div class="wz_left"></div><div class="wz_middle"><?php echo ($position); ?></div><div class="wz_right"></div><div class="wz_more"><?php echo getMoreInfo();?></div><div class="clear"></div></div></div><!--当前位置 end--><!--主体区域 begin--><div class="container"><div class="alert_div"><h4 class="alert_info">欢迎</h4><?php if(($_SESSION['passwordIsEasy']) == "1"): ?><h4 class="alert_warning">当前账号使用的密码为初始密码，请您及时修改密码，保证网站系统安全！<a href="<?php echo U('admin/User/pwdedit/', array('id'=>$_SESSION['loginId']));?>">点击这里修改密码</a></h4><?php endif; ?><h4 class="alert_what"></h4></div><!--<h4 class="alert_info">Welcome to the free MediaLoot admin panel template, this could be an informative message.</h4><h4 class="alert_warning">A Warning Alert</h4><h4 class="alert_error">An Error Message</h4><h4 class="alert_success">A Success Message</h4>--><!--快速导航 begin--><!--网站安全 begin><div class="web_safe"><div class="safe_l"><dl><dt><img class="safe_btn" src="__IMG__/safe_icon.jpg" width="76" height="104" alt=""/></dt><dd><p class="web_safe_f1">您的网站处于安全状态 </p><p class="web_safe_f2">当前时间是：<span><?php echo date('Y-m-d h:i:s',time());?></span></p><p><a href="javascript:startCheck()" class="tj_xf_btn"><img src="__IMG__/tj_btn.jpg" width="216" height="28" alt=""/></a></p></dd></dl><div id="center"><div id="message"></div><div id="loading"><div></div></div></div></div><div class="safe_r"><p class="safe_r_result">&nbsp;</p><div class="safe_r_result_list"><p class="safe_title"><span><a href="javascript:repair()">修复</a></span>被篡改文件</p><div class="safe_list checkinfo_1"></div><p class="safe_title"><span><a href="javascript:repair()">清理</a></span>未知文件</p><div class="safe_list checkinfo_3"></div><p class="safe_title"><span><a href="javascript:repair()">更新</a></span>可用补丁文件</p><div class="safe_list checkinfo_2"></div></div></div><div class="clear"></div></div><!--网站安全 end--><!--快速导航 start><div class="quick"><div class="quick_ul"><div class="l_btn"></div><ul><li><a href="#"><span class="quick_img"><img src="__IMG__/ico_folder_64.png" width="64" height="64" alt=""/></span></span><span class="quick_name">添加新闻</span></a></li><li><a href="#"><span class="quick_img"><img src="__IMG__/ico_page_64.png" width="64" height="64" alt=""/></span></span><span class="quick_name">添加内容</span></a></li><li><a href="#"><span class="quick_img"><img src="__IMG__/ico_picture_64.png" width="64" height="64" alt=""/></span></span><span class="quick_name">添加产品</span></a></li><li><a href="#"><span class="quick_img"><img src="__IMG__/ico_clock_64.png" width="64" height="64" alt=""/></span></span><span class="quick_name">添加招聘</span></a></li><li><a href="#"><span class="quick_img"><img src="__IMG__/ico_users_64.png" width="64" height="64" alt=""/></span></span><span class="quick_name">用户管理</span></a></li><li><a href="#"><span class="quick_img"><img src="__IMG__/ico_settings_64.png" width="64" height="64" alt=""/></span></span><span class="quick_name">网站设置</span></a></li><li><a href="#"><span class="quick_img"><img src="__IMG__/ico_chat_64.png" width="64" height="64" alt=""/></span></span><span class="quick_name">留言管理</span></a></li></ul><div class="r_btn"></div></div></div><!--快速导航 end--><!--线状图区域 begin><div class="stats"><div class="lm_title"><div class="lm_title_l"><em>网站统计</em></div></div><div class="lm_content"><div class="statistic"><div class="statistic_main"><div class="graph_l"><img src="__IMG__/img.jpg" width="700" height="260" alt=""/></div><div class="graph_r"><div class="graph_a"><dl><dt><div id="cpu"></div></dt></dl></div><div class="graph_b"><dl><dt><div id="memory"></div></dt></dl></div></div><div class="clear"></div></div></div></div><div class="lm_bottom"><div class="lm_bottom_l"><em></em></div></div></div><!--线状图区域 end--><!--其他信息区域 begin--><div class="other_zone"><!--系统信息 begin--><div class="sys_infor"><div class="lm_title"><div class="lm_title_l"><em>系统信息</em></div></div><div class="lm_content sys_infor_list"><ul><li>服务器解译引擎： Apache/2.2.17 (Win32) PHP/5.2.6</li><li>客户机浏览器： <?php echo ($_SERVER['HTTP_USER_AGENT']); ?></li><li>php版本：<?php echo PHP_VERSION;?></li><li>mysql版本：<?php echo mysql_get_server_info();?></li><li>主机地址： <?php echo ($_SERVER['SERVER_ADDR']); ?></li></ul></div><div class="lm_bottom"><div class="lm_bottom_l"><em></em></div></div></div><!--系统信息 end--><!--网站设置 begin--><div class="web_setting"><div class="lm_title"><div class="lm_title_l"><em>便捷设置</em></div></div><div class="lm_content web_setting_list"><div class="demo"><div class="list"><div class="fun_title"><span rel="SYS_DEFAULT_EDITOR" class="ad_off" title="点击开启"></span><h3>扩展编辑器</h3></div><p>默认使用基础功能编辑器，一键即可切换到高级编辑器</p></div><div class="list"><div class="fun_title"><span rel="JL_SERVER" class="ad_off" title="点击关闭"></span><h3>消息推送</h3></div><p>接受官方推送消息，及时了解最新官方公告</p></div><div class="list"><div class="fun_title"><span rel="SYS_FILTER" class="ad_off" title="点击开启"></span><h3>恶意词汇过滤</h3></div><p>过滤网站含有恶意词汇，但会加大服务器压力</p></div><div class="list"><div class="fun_title"><span rel="SYS_SAFE_MODE" class="ad_off" title="点击关闭"></span><h3>兼容模式</h3></div><p>兼容模式不会显示栏目饼状图、内容饼状图等耗费资源的信息</p></div><div class="list"><div class="fun_title"><span rel="SYS_RECYCLE_MODE" class="ad_off" title="点击关闭"></span><h3>回收站自动释放</h3></div><p>自动删除栏目回收站及内容回收站内30天前的内容，节省您的数据库空间</p></div><div class="list"><div class="fun_title"><span rel="SYS_DATETIME_MODE" class="ad_off" title="点击关闭"></span><h3>发布时间精确到时分秒</h3></div><p>将内容的发布时间精确到时分秒</p></div></div></div><div class="lm_bottom"><div class="lm_bottom_l"><em></em></div></div></div><!--网站设置 end--><div class="clear"></div></div><!--其他信息区域 end--></div><!--主体区域 end--></div><!--右侧区域  end--></body><script>
    var cpuInfo;
    var memoryInfo;
    var sysinfo_arr;

    var progress_id = "loading";
    var local_file_hash_arr;
    var local_checkinfo_arr;
    function SetProgress(progress) {
        if (progress) {
            showMsg(progress);
            $("#" + progress_id + " > div").css("width", String(progress) + "%"); //控制#loading div宽度
            $("#" + progress_id + " > div").html(String(progress) + "%"); //显示百分比
        }
    }

    var i = 0;
    function doProgress() {
        if (i > 100) {
            checkLocalDbFileHash();
            //$("#message").html("加载完毕！").fadeIn("slow");//加载完毕提示
            return;
        }
        if (i <= 100) {
            setTimeout("doProgress()", parseInt(100 * Math.random()));
            SetProgress(i);
            i++;
        }
    }

    function showMsg(n) {
        if (n > local_file_hash_arr.length) n = parseInt(local_file_hash_arr.length * Math.random());
        var filename = local_file_hash_arr[n];
        $("#message").html(filename);

    }

    function showCheckinfo() {
        afterCheck();
        var len = local_checkinfo_arr.length;
        for (var i = 0; i < len; i++) {
            /* 			var type="";
             switch(local_checkinfo_arr[i]['type']){
             case '1' :  type = "被篡改";break;
             case '2':  type = "有补丁";break;
             case '3' :  type = "未知文件";break;
             default:  type = "??";
             } */
            //$("#checkinfo").append('<li>'+local_checkinfo_arr[i]['file']+type+'</li>');
            $(".checkinfo_" + local_checkinfo_arr[i]['type']).append('<p>' + local_checkinfo_arr[i]['file'] + '</p>');
        }
    }

    function afterCheck() {
        var len = local_checkinfo_arr.length;
        if (len > 0) {
            $(".safe_r_result").html('共扫描了<span>' + 320 + '</span>个文件，其 <span>' + len + '</span>项有问题，建议您立即修复！');

            $(".tj_xf_btn img").attr("src", "__IMG__/xf_btn.jpg");

            $(".safe_btn").fadeOut();
            $(".safe_btn").attr("src", "__IMG__/no_safe_icon.jpg");
            $(".safe_btn").fadeIn(1000);

            $(".web_safe_f1").html("<span style='color:red;'>您的网站存在安全问题！</span>");
            $(".web_safe_f2").html("它们可能影响到您的网站安全，请及时修复！");
        }
    }


    function getLocalDbFileHash() {
        $.post("__GROUP__/commonajax/getLocalHash", "{}", function (data) {
            if (data == -1) {
                alert("123");
            } else {
                local_file_hash_arr = eval(data);
                doProgress();
            }
        });
    }

    function checkLocalDbFileHash() {
        $.post("__GROUP__/commonajax/checkLocalHash", "{}", function (data) {
            if (data == -1) {
                alert("初始化体检失败！");
            } else if (data == 1) {
                alert("体检没有任何问题");
            } else {
                local_checkinfo_arr = eval(data);
                showCheckinfo();
            }
        });
    }

    function startCheck() {
        $(".tj_xf_btn").attr("onclick", "javascript:alert('正在扫描！请勿重复点击!');return false;");
        getLocalDbFileHash();
    }

    function sysconfigChange(k, v) {
        var id, value;
        for (var i = 0; i < sysinfo_arr.length; i++) {
            if (sysinfo_arr[i]['varname'] == k) {
                id = sysinfo_arr[i]['id'];
                value = v;
            }
        }

        $.post("__GROUP__/commonajax/sysconfigSwitch", {id: id, value: value}, function (data) {
            if (data == -1) {
                jl_notify(2, "设置参数失败，请稍后再试！");
            } else {
                if (value == 0) {
                    $("span[rel='" + k + "']").removeClass("ad_on").addClass("ad_off").attr("title", "点击开启");
                } else if (value == 1) {
                    $("span[rel='" + k + "']").removeClass("ad_off").addClass("ad_on").attr("title", "点击关闭");
                } else {
                    jl_notify(2, "设置参数遇到异常，请稍后再试！");
                }
            }
        });
    }

    function repair() {
        //alert("当前版本暂不支持线上升级，如需升级请联系客服人员！");
        jl_notify(3, "当前版本暂不支持该功能，如需升级请联系客服人员！");
    }

    $(function () {
        /* 	    var cpuInfo = new JustGage({
         id: "cpu",
         value: getRandomInt(20, 30),
         min: 0,
         max: 100,
         title: "CPU使用率 ",
         label: "%",
         levelColors: [
         "#B2CF45",
         "#00C7EF",
         "#CF3333"
         ]
         });

         var memoryInfo = new JustGage({
         id: "memory",
         value: getRandomInt(20, 30),
         min: 0,
         max: 100,
         title: "内存使用率 ",
         label: "%",
         levelColors: [
         "#6DA2DE",
         "#FF00AE",
         "#CF3333"
         ]
         }); */

        /* 	      setInterval(function() {
         cpuInfo.refresh(getRandomInt(30,40));
         memoryInfo.refresh(getRandomInt(30,40));
         }, 1000); */

        //开关
        $(".list").hover(function () {
            $(this).addClass("cur_select");
        }, function () {
            $(this).removeClass("cur_select");
        });

        //关闭
        $(".ad_on").live("click", function () {
            //var add_on = $(this);
            var status_id = $(this).attr("rel");
            sysconfigChange(status_id, 0);
            //add_on.removeClass("ad_on").addClass("ad_off").attr("title","点击开启");
        });

        //开启
        $(".ad_off").live("click", function () {
            //var add_off = $(this);
            var status_id = $(this).attr("rel");
            sysconfigChange(status_id, 1);
            //add_off.removeClass("ad_off").addClass("ad_on").attr("title","点击关闭");
        });

        $.post("__GROUP__/commonajax/getSysconfig", {}, function (data) {
            if (data == -1) {
                //alert("初始化系统参数失败！");
                jl_notify(2, "初始化系统参数失败！");
            } else {
                sysinfo_arr = eval(data);
                for (var i = 0; i < sysinfo_arr.length; i++) {
                    if (sysinfo_arr[i]['value'] == 1)
                        $("span[rel='" + sysinfo_arr[i]['varname'] + "']").removeClass("ad_off").addClass("ad_on").attr("title", "点击关闭");
                }
            }
        });
    });
</script></html>
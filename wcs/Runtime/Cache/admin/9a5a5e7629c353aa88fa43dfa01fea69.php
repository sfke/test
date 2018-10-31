<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/><title>登录</title><script type="text/javascript" src="__JS__/jquery-1.7.2.min.js"></script><style>
        body {
            height: 100%;
            background: url(__CSS__/images/1111.jpg) fixed center center;
            margin: 0;
            padding: 0;
        }

        #sitepage {
            margin: auto;
            width: auto;
            height: auto;
        }

        #recordlist {
            position: absolute;
            width: 285px;
            height: 230px;
            left: 50%;
            top: 50%;
            margin-left: -10px;
            margin-top: -25px;
        }

        #logo {
            position: absolute;
            width: 379px;
            height: 88px;
            left: 2%;
            top: 2%;
        }

        #copy {
            position: absolute;
            width: 514px;
            height: 27px;
            right: 3%;
            top: 93%;
        }

        .w_wk {
            width: 285px;
            margin: 0 auto;
            padding: 345px 0 0 265px;
        }

        .w_wz {
            font-size: 12px;
            font-weight: bold;
            line-height: 32px;
            color: #333333;
        }

        .login_kuang {
            background: url(__CSS__/images/l_011.jpg) no-repeat;
            width: 136px;
            height: 25px;
            line-height: 25px;
            padding: 0 3px;
            padding-left: 32px;
            border: none;
            color: #333;
        }

        .login_kuang:hover {
            background: url(__CSS__/images/l_01.jpg) no-repeat;
            width: 136px;
            height: 25px;
            line-height: 25px;
            padding: 0 3px;
            padding-left: 32px;
            border: none;
            color: #333;
        }

        .login_kuang1 {
            background: url(__CSS__/images/l_021.jpg) no-repeat;
            width: 136px;
            height: 25px;
            line-height: 25px;
            padding: 0 3px;
            padding-left: 32px;
            border: none;
            color: #333;
        }

        .login_kuang1:hover {
            background: url(__CSS__/images/l_02.jpg) no-repeat;
            width: 136px;
            height: 25px;
            line-height: 25px;
            padding: 0 3px;
            padding-left: 32px;
            border: none;
            color: #333;
        }

        .login_yz {
            border: #a0a0a0 1px solid;
            width: 44px;
            height: 18px;
            line-height: 18px;
            padding: 0 3px;
        }

        .login_red a {
            color: #FF0000;
            text-decoration: underline;
            font-weight: normal;
        }

        .login_red a:hover {
            color: #FF6600;
            text-decoration: none;
            font-weight: normal;
        }

        .btn_dl {
            background: url(__CSS__/images/dl.png) no-repeat;
            width: 99px;
            height: 34px;
            border: 0;
            cursor: pointer;
        }

        .btn_cz {
            background: url(__CSS__/images/cz.png) no-repeat;
            width: 99px;
            height: 34px;
            border: 0;
            cursor: pointer;
        }

        .c_ccc {
            COLOR: #ccc
        }
    </style></head><script language="JavaScript">
    function correctPNG() // correctly handle PNG transparency in Win IE 5.5 & 6.
    {
        var arVersion = navigator.appVersion.split("MSIE")
        var version = parseFloat(arVersion[1])
        if ((version >= 5.5) && (document.body.filters)) {
            for (var j = 0; j < document.images.length; j++) {
                var img = document.images[j]
                var imgName = img.src.toUpperCase()
                if (imgName.substring(imgName.length - 3, imgName.length) == "PNG") {
                    var imgID = (img.id) ? "id='" + img.id + "' " : ""
                    var imgClass = (img.className) ? "class='" + img.className + "' " : ""
                    var imgTitle = (img.title) ? "title='" + img.title + "' " : "title='" + img.alt + "' "
                    var imgStyle = "display:inline-block;" + img.style.cssText
                    if (img.align == "left") imgStyle = "float:left;" + imgStyle
                    if (img.align == "right") imgStyle = "float:right;" + imgStyle
                    if (img.parentElement.href) imgStyle = "cursor:hand;" + imgStyle
                    var strNewHTML = "<span " + imgID + imgClass + imgTitle
                            + " style=\"" + "width:" + img.width + "px; height:" + img.height + "px;" + imgStyle + ";"
                            + "filter:progid:DXImageTransform.Microsoft.AlphaImageLoader"
                            + "(src=\'" + img.src + "\', sizingMethod='scale');\"></span>"
                    img.outerHTML = strNewHTML
                    j = j - 1
                }
            }
        }
    }
    window.attachEvent("onload", correctPNG);
</script><body><div id="sitepage"><div id="logo"></div><div id="recordlist"><form action="<?php echo U('public/checkLogin');?>" method="POST"><table width="98%" border="0" cellspacing="0" cellpadding="0" align="center"  style="margin:-65px 0 10px 0;"><tr><td height="25"></td></tr><tr><td width="58%" height="25"></td><td width="42%"><?php if((count($siteArr)) == "1"): if(is_array($siteArr)): $i = 0; $__LIST__ = $siteArr;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><input type="hidden" name="siteid" value="<?php echo ($vo['id']); ?>"/><?php endforeach; endif; else: echo "" ;endif; else: ?><select name="siteid"><?php if(is_array($siteArr)): $i = 0; $__LIST__ = $siteArr;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo['id']); ?>"><?php echo ($vo['name']); ?></option><?php endforeach; endif; else: echo "" ;endif; ?></select><?php endif; ?></td></tr></table><table width="98%" border="0" cellspacing="0" cellpadding="0" class="w_wz" align="center"><tr><td width="22%">账&nbsp;&nbsp;号：</td><td width="78%" colspan="3"><label><input type="text" name="account" id="account" class="login_kuang" value="" onfocus="javascript:if(this.value==this.defaultValue) this.value='';" onblur="javascript:if(this.value=='') this.value=this.defaultValue;"/></label></td></tr><tr><td>密&nbsp;&nbsp;码：</td><td colspan="3"><label><input type="password" name="password" id="password" class="login_kuang1" value="" onfocus="javascript:if(this.value==this.defaultValue) this.value='';" onblur="javascript:if(this.value=='') this.value=this.defaultValue;"/></label></td></tr><tr><td>验证码：</td><td><input type="text" class="text" style="width:60px;height:20px;" check="Require" warning="请输入验证码" name="verify"></td><td><img id="verifyImg" SRC="<?php echo U('public/verify');?>" onClick="fleshVerify()" BORDER="1" ALT="点击刷新验证码" style="height:25px;margin:0 10px;border-radius:4px;" align="absmiddle"></td></tr></table><table width="98%" border="0" cellspacing="0" cellpadding="0" align="center"><tr><td height="15"></td></tr><tr><td><input class="btn_dl" type="submit" value=""/>&nbsp;&nbsp;<input class="btn_cz" type="reset" value=""/></td></tr></table></form></div><div id="copy"><img src="__CSS__/images/copy.jpg"/></div></div></body><script>
    function fleshVerify() {
        var timenow = new Date().getTime();
        $("#verifyImg").attr('src', "<?php echo U('public/verify');?>?t=" + timenow);
    }
</script></html>
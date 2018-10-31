//浏览器版本
/*var Sys = {};
var ua = navigator.userAgent.toLowerCase();
if (window.ActiveXObject) {
    Sys.ie = ua.match(/msie ([\d.]+)/)[1]
    if (Sys.ie <= 7) {
        alert('你目前的IE版本为' + Sys.ie + ',版本太低，请升级！强烈推荐使用谷歌浏览器！');
        // location.href="http://windows.microsoft.com/zh-CN/internet-explorer/downloads/ie";
    }
}*/

function getSelects(checkname) {
    if (checkname === undefined || typeof(checkname) == 'undefined') var checkname = '_check[]';
    var s = '';
    $("input:checked[name='" + checkname + "']").each(function () {
        s += $(this).val() + ',';
    });

    var lastIndex = s.lastIndexOf(',');
    if (lastIndex > -1) {
        s = s.substring(0, lastIndex) + s.substring(lastIndex + 1, s.length);
    }
    return s;
}


function selAll() {
    $("input[name='_check[]']").each(function () {
        $(this).attr('checked', 'checked');
    });
}

function selRev() {
    $("input[name='_check[]']").each(function () {

        if ($(this).attr('checked') == 'checked') {
            $(this).attr('checked', false);
        } else {
            $(this).attr('checked', 'checked');
        }
    });
}

/**
 * @param type 1:alert_success 2:alert_error 3:alert_warning 4:alert_info
 * @param msg
 */

function jl_notify(type, msg) {
    var classname = '';
    switch (type) {
        case 1 :
            classname = 'alert_success';
            break;
        case 2 :
            classname = 'alert_error';
            break;
        case 3 :
            classname = 'alert_warning';
            break;
        case 4 :
            classname = 'alert_info';
            break;
        default :
            classname = 'alert_success';
            break;
    }

    var FUNC = [
        function () {
            notify_status = 1;
            aniCB();
        },
        function () {
            $(".alert_info").fadeOut(300, aniCB);
            $(".alert_what").addClass(classname);
            $(".alert_what").text(msg);
        },
        function () {
            $(".alert_what").fadeIn(300, aniCB);
        },
        function () {
            $(".alert_what").delay(1000).fadeOut(300, aniCB);
        },
        function () {
            $(".alert_info").fadeIn(300, aniCB);
        },
        function () {
            notify_status = 0;
            $(".alert_what").removeClass(classname);
        }
    ];
    var aniCB = function () {
        $(document).dequeue("myAnimation");
    };
    $(document).queue("myAnimation", FUNC);

    if (typeof(notify_status) == 'undefined' || notify_status == 0) {
        aniCB();
    }
}


/**
 * 需要jquery1.7以上支持
 * @param o
 */
function jl_fadeIn(o) {
    var obj = o;
    var scrollHight = $(document).scrollTop();
    var iWidth = document.documentElement.clientWidth;
    var iHeight = document.documentElement.clientHeight - 80;
    var oWidth = obj.width();
    var oHeight = obj.height();
    var ileft = (iWidth - oWidth) / 2 + "px";
    var itop = 130 + scrollHight + "px";
    //var itop =  (iHeight - 300) / 2 + "px";
    //var ileft =  (iWidth - 425) / 2 + "px";

    obj.css('top', itop);
    obj.css('left', ileft);
    if (!obj.is(":animated")) {
        var top = obj.css('top');
        top = top.replace('px', '');
        topup = parseInt(top) + 1;
        topdown = parseInt(top) - 1;
        obj.fadeIn(150)
            .animate({top: topup}, 10).animate({top: topdown}, 10)
            .animate({top: topup}, 20).animate({top: topdown}, 20)
            .animate({top: topup}, 30).animate({top: topdown}, 30)
            .animate({top: topup}, 40).animate({top: top}, 40); //恢复位置
    }
}

/*
 * 需要jquery1.7以上支持
 */
function jl_fadeOut(o) {
    var obj = o;
    if (!obj.is(":animated")) {
        var top = obj.css('top');
        top = top.replace('px', '');
        topup = parseInt(top) + 1;
        topdown = parseInt(top) - 1;
        obj.animate({top: topup}, 60).animate({top: topdown}, 60)
            .animate({top: topup}, 50).animate({top: topdown}, 50)
            .animate({top: topup}, 30).animate({top: topdown}, 30)
            .animate({top: topup}, 20).animate({top: topdown}, 20)
            .animate({top: topup}, 10).animate({top: top}, 10)      //恢复位置
            .fadeOut(150);
    }
}

//设为首页
function setHomepage() {
    if (document.all) {
        document.body.style.behavior = 'url(#default#homepage)';
        document.body.setHomePage(window.location.href);
    } else if (window.sidebar) {
        if (window.netscape) {
            try {
                netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
            } catch (e) {
                alert("该操作被浏览器拒绝，如果想启用该功能，请在地址栏内输入 about:config,然后将项 signed.applets.codebase_principal_support 值该为true");
            }
        }
        var prefs = Components.classes['@mozilla.org/preferences-service;1'].getService(Components.interfaces.nsIPrefBranch);
        prefs.setCharPref('browser.startup.homepage', window.location.href);
    } else {
        alert('您的浏览器不支持自动自动设置首页, 请使用浏览器菜单手动设置!');
    }
}

//加入收藏
function addFavorite() {
    if (document.all) {
        try {
            window.external.addFavorite(window.location.href, document.title);
        } catch (e) {
            alert("加入收藏失败，请使用Ctrl+D进行添加");
        }

    } else if (window.sidebar) {
        window.sidebar.addPanel(document.title, window.location.href, "");
    } else {
        alert("加入收藏失败，请使用Ctrl+D进行添加");
    }
}

//window.location 快捷函数
function go(url){
    window.location = url;
}
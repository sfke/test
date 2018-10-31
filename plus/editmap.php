<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>期刊编辑</title>

<style type="text/css">
body,td,th {
	font-size: 12px;
}
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
	overflow:hidden;
}
/* Required CSS classes: must be included in all pages using this script */

/* Apply the element you want to drag/resize */
.drsElement {
 position: absolute;
 border: 3px solid #C00;
}

/*
 The main mouse handle that moves the whole element.
 You can apply to the same tag as drsElement if you want.
*/
.drsMoveHandle {
 height: 20px;

 cursor: move;
}

/*
 The DragResize object name is automatically applied to all generated
 corner resize handles, as well as one of the individual classes below.
*/
.dragresize {
 position: absolute;
 width: 5px;
 height: 5px;
 font-size: 1px;
 background: #EEE;
 border: 1px solid #333;
}

/*
 Individual corner classes - required for resize support.
 These are based on the object name plus the handle ID.
*/
.dragresize-tl {
 top: -8px;
 left: -8px;
 cursor: nw-resize;
}
.dragresize-tm {
 top: -8px;
 left: 50%;
 margin-left: -4px;
 cursor: n-resize;
}
.dragresize-tr {
 top: -8px;
 right: -8px;
 cursor: ne-resize;
}

.dragresize-ml {
 top: 50%;
 margin-top: -4px;
 left: -8px;
 cursor: w-resize;
}
.dragresize-mr {
 top: 50%;
 margin-top: -4px;
 right: -8px;
 cursor: e-resize;
}

.dragresize-bl {
 bottom: -8px;
 left: -8px;
 cursor: sw-resize;
}
.dragresize-bm {
 bottom: -8px;
 left: 50%;
 margin-left: -4px;
 cursor: s-resize;
}
.dragresize-br {
 bottom: -8px;
 right: -8px;
 cursor: se-resize;
}

#box{position: absolute; width:400px; height:600px; margin:0px; padding:0px;}
</style>
<script src="../wcs/Public/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="../wcs/Public/js/artDialog/jquery.artDialog.source.js?skin=default"></script>
<script src="../wcs/Public/js/artDialog/iframeTools.source.js"></script>
<script language="JavaScript" type="text/javascript" src="../wcs/Public/js/artDialog/dragresize.js"></script>

<script>
(function() {
    var parent = art.dialog.parent,
    // 父页面window对象
    api = art.dialog.open.api;
    if (!api) return;
    api.button({
        name: '确定',
        callback: function() {
		var origin = artDialog.open.origin;
		var aValue = document.getElementById('cover').value;
		var input = origin.document.getElementById('cover');
		input.value = aValue;
		input.select();
		art.dialog.close();

            return true;
        },
        focus: true
    },
    {
        name: '取消',
        callback: function() {
            return true;
        }
    });
	window.onload = function () {
		var pos=$(".drsElement").position();
		var coord = pos.left+","+pos.top+","+$("#cheng").width()+","+$("#cheng").height();
 		$("#cover").val(coord);
	};

})();
</script>
<script type="text/javascript">


var dragresize = new DragResize('dragresize',
 { minWidth: 50, minHeight: 50, minLeft: 0, minTop: 0, maxLeft: 400, maxTop: 600 });
dragresize.isElement = function(elm)
{
 if (elm.className && elm.className.indexOf('drsElement') > -1) return true;
};
dragresize.isHandle = function(elm)
{
 if (elm.className && elm.className.indexOf('drsMoveHandle') > -1) return true;
};
dragresize.ondragfocus = function() { };
dragresize.ondragstart = function(isResize) {};
dragresize.ondragmove = function(isResize) { };
dragresize.ondragend = function(isResize) { 
var pos=$(".drsElement").position();
var coord = pos.left+","+pos.top+","+$("#cheng").width()+","+$("#cheng").height();

var mapCoord = pos.left+","+pos.top+","+(pos.left+$("#cheng").width())+","+(pos.top+$("#cheng").height());

$("#cover").val(mapCoord);
};
dragresize.ondragblur = function() {};
dragresize.apply(document);
</script>

</head>
<body>
<input name="cover" id="cover" type="hidden" />
<div id="box">
<?php
require("../wcs/Conf/db.php"); 
$tid = $_GET['tid'];
//print_r($db['DB_NAME']);
$conn=mysql_connect($db['DB_HOST'],$db['DB_USER'],$db['DB_PWD']);//连接数据库的帐号和端口号 
mysql_query("SET NAMES 'utf-8'",$conn); 
mysql_select_db($db['DB_NAME'],$conn);// 加载数据库 
$sql="select litpic from ". $db['DB_PREFIX'] ."arctype where id=". $tid; 
$result=mysql_query($sql,$conn);

if (!empty($result)) {
   while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
	  if($row[0]==null){
	   print_r("<div><center>没有找到版面图！</center></div>");
	  }else{
       print_r("<img src=".$row[0]." width='400' height='600' />");
	  }
   }        
}else{
   print_r("<div><center>没有找到版面图！</center></div>");
}

?>
<div id="cheng" class="drsElement drsMoveHandle" style=" position:absolute;left: 150px; top: 280px; width: 100px; height: 100px; color:#FFF; text-align: center; ">
1、请点击调整大小并移动<br/>
2、点击确定回调锚点坐标
</div>
</div>

</body>
</html>

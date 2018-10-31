<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html><html><head><meta name="Keywords" content=""><meta name="Description" content=""><title><?php echo ($jl_title); ?></title><meta name="Keywords" content="<?php echo ($jl_key); ?>"><meta name="Description" content="<?php echo ($jl_desc); ?>"><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=0.5, maximum-scale=2.0, user-scalable=yes" /><link rel="stylesheet" href="__BASE__/wap/css/css.css"><link rel="stylesheet" href="__BASE__/wap/css/iconfont.css"><link rel="stylesheet" href="__BASE__/wap/css/swiper.3.1.7.min.css"><script src="__BASE__/wap/js/jquery.js" type="text/javascript"></script><script src="__BASE__/wap/js/jquery.SuperSlide.js" type="text/javascript"></script><script src="__BASE__/wap/js/swiper.3.1.7.jquery.min.js"></script></head><body><header><div class="top_black"><a href="javascript:window.history.go(-1);"><img src="__BASE__/wap/images/top_left.jpg" width="16" height="33"></a></div><div class="logotxt"><?php echo ($arctype['name']); ?></div><script>
$(document).ready(function(event){
      $('#menu-icon').click(function() {         
    $('#nav').slideDown();
      return false;
    });

  $("#nav").click(function(event) {
    var e = event || window.event;  
    if(e.stopPropagation) { //W3C阻止冒泡方法  
          e.stopPropagation();  
      } else {
          e.cancelBubble = true; //IE阻止冒泡方法
      }  
  });
  
  $(document).click(function(event){
  $("#nav").slideUp();
  })

  
})
</script><nav id="nav-wrap"><div id="menu-icon"><img src="__BASE__/wap/images/menu_bg.gif" width="40" height="33"></div><ul id="nav"><?php  $m = D("Arctype"); $arr = $m->getData('a:12:{s:8:"addfield";s:3:"off";s:8:"isparent";s:0:"";s:8:"titlelen";s:1:"6";s:3:"row";s:0:"";s:4:"type";s:3:"son";s:6:"typeid";s:2:"48";s:7:"orderby";s:5:"order";s:8:"orderway";s:3:"asc";s:5:"limit";s:0:"";s:5:"class";s:0:"";s:6:"result";s:0:"";s:5:"where";s:0:"";}'); foreach($arr as $index=>$field){ if($index == 0 ) $isfirst = true;else $isfirst = false; if($index == count($arr)-1 ) $islast = true;else $islast = false; ?><li><a href="<?php echo ($field['url']); ?>"><?php echo ($field['name']); ?></a></li><?php } ?></ul></nav></header><!--内页内容--><section class="ny_cont main"><!--新闻--><div class="index_new clearfix"><?php if($tid == 50): $m = D("Archives"); $arr = $m->getPageList('a:12:{s:8:"addfield";s:2:"on";s:7:"desclen";s:0:"";s:5:"color";s:3:"off";s:8:"titlelen";s:0:"";s:6:"typeid";s:2:"17";s:5:"class";s:0:"";s:4:"flag";s:0:"";s:7:"orderby";s:2:"id";s:8:"orderway";s:4:"desc";s:8:"pagesize";s:1:"6";s:5:"where";s:0:"";s:6:"result";s:0:"";}'); $pageline = $arr["pageline"]; $pageinfo = $arr["pageinfo"]; unset($arr["pageline"],$arr["pageinfo"]); foreach($arr as $index=> $field){ ?><dl><dt><a href="<?php echo U('Wap/view',array('aid'=>$field['id'],'tid'=>$tid));?>" style="background: url(<?php echo (($field['img'])?($field['img']):$defaultimg); ?>) center center no-repeat ;-webkit-background-size: cover;background-size: cover;"></a></dt><dd><a href="<?php echo U('Wap/view',array('aid'=>$field['id'],'tid'=>$tid));?>"><h5><?php echo ($field['title']); ?></h5><p><?php echo ($field['desc']); ?></p><h6><span><?php echo (date("Y-m-d",$field['pubdate'])); ?></span><i>更多 ></i></h6></a></dd></dl><?php } elseif($tid == x): endif; ?></div><!--新闻 end--></section><div class="page clearfix"><?php echo ($pageline); ?></div><footer><ul><li><a href="<?php echo U('Wap/index');?>"><i class="icon iconfont">&#xe673;</i><span>网站首页</span></a></li><li><a href="<?php echo listUrl(49);?>"><i class="icon iconfont">&#xe671;</i><span>关于华奥</span></a></li><li><a href="<?php echo listUrl(51);?>"><i class="icon iconfont">&#xe601;</i><span>业务产品</span></a></li><li><a href="<?php echo listUrl(53);?>"><i class="icon iconfont">&#xe69c;</i><span>联系我们</span></a></li></ul></footer></body></html>
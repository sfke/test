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
</script><nav id="nav-wrap"><div id="menu-icon"><img src="__BASE__/wap/images/menu_bg.gif" width="40" height="33"></div><ul id="nav"><?php  $m = D("Arctype"); $arr = $m->getData('a:12:{s:8:"addfield";s:3:"off";s:8:"isparent";s:0:"";s:8:"titlelen";s:1:"6";s:3:"row";s:0:"";s:4:"type";s:3:"son";s:6:"typeid";s:2:"48";s:7:"orderby";s:5:"order";s:8:"orderway";s:3:"asc";s:5:"limit";s:0:"";s:5:"class";s:0:"";s:6:"result";s:0:"";s:5:"where";s:0:"";}'); foreach($arr as $index=>$field){ if($index == 0 ) $isfirst = true;else $isfirst = false; if($index == count($arr)-1 ) $islast = true;else $islast = false; ?><li><a href="<?php echo ($field['url']); ?>"><?php echo ($field['name']); ?></a></li><?php } ?></ul></nav></header><section class="ny_banner"><img src="__BASE__/wap/images/about_pic.jpg" width="720" height="319"></section><section class="ny_menu"><em><?php echo ($arctype['name']); ?></em><i></i></section><!--内页内容--><section class="ny_cont main"><?php
 if($_GET['tid']==49){ $typeid=10; }else{ $typeid = $_GET['tid']; } $model = M("addpage2"); $where["typeid"] = array('eq' ,$typeid); $txt = $model->where($where)->getField("txt"); echo ($txt); ?></section><footer><ul><li><a href="<?php echo U('Wap/index');?>"><i class="icon iconfont">&#xe673;</i><span>网站首页</span></a></li><li><a href="<?php echo listUrl(49);?>"><i class="icon iconfont">&#xe671;</i><span>关于华奥</span></a></li><li><a href="<?php echo listUrl(51);?>"><i class="icon iconfont">&#xe601;</i><span>业务产品</span></a></li><li><a href="<?php echo listUrl(53);?>"><i class="icon iconfont">&#xe69c;</i><span>联系我们</span></a></li></ul></footer></body></html>
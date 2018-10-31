<?php if (!defined('THINK_PATH')) exit();?><!doctype html><html lang="en"><head><meta charset="UTF-8"><title><?php echo ($jl_title); ?></title><meta name="Keywords" content="<?php echo ($jl_key); ?>"><meta name="Description" content="<?php echo ($jl_desc); ?>"><meta name="renderer" content="webkit"><meta http-equiv="X-UA-Compatible" content="IE=edge"/><meta name="baidu-site-verification" content="LoCsw67We5" /><link rel="icon" href="__BASE__/images/favicon.ico" type="image/x-icon"/><link rel="stylesheet" href="__BASE__/css/animate.min.css"/><link rel="stylesheet" href="__BASE__/css/common.css" /><link rel="stylesheet" href="__BASE__/css/html5.css"><link rel="stylesheet" href="__BASE__/css/jScrollPane.css" /><!--[if lte IE 8]><script src="__BASE__/js/html5.js" type="text/javascript"></script><![endif]--><script type="text/javascript" src="__BASE__/js/jquery1.42.min.js"></script><script type="text/javascript" src="__BASE__/js/jquery.SuperSlide.2.1.1.js"></script><script type="text/javascript" src="__BASE__/js/common.js"></script><script type="text/javascript" src="__BASE__/js/jquery.mousewheel.js"></script><script type="text/javascript" src="__BASE__/js/jScrollPane.js"></script><script src="__BASE__/js/wow.min.js"></script></head><body style="padding-top: 0;"><!--

 __________  ___      __________  __________  _________  ___    ___

/_________//__/    /_________//_________//________//__/  /__/

___   ___/      ___   ___/   ______/   _____/   _   

                           /____/           /__   

    _         _____          ____/__   _____   ___    

   /__        /____/         /_____/  /____/       

   ______/    _______/  __/    ________/ _______/ __/  __/



work at www.jltech.cn



--><div class="header_fix"><div class="head-v3 head-v3_png"><div class="navigation-up"><div class="navigation-inner"><h1 class="logo"><a href="<?php echo siteUrl(1);?>index.php"><img src="__BASE__/images/logo.png" alt=""></a></h1><h3 class="zq"><p>证券代码：</p><span>831331</span></h3><span class="searicon"><img src="__BASE__/images/seaicon.png" alt=""></span><div class="navigation-v3"><ul><li class="nav-up-selected-inpage" _t_nav="home"><h2><a href="<?php echo siteUrl(1);?>index.php">首页</a></h2></li><?php  $m = D("Arctype"); $arr = $m->getData('a:12:{s:8:"addfield";s:3:"off";s:8:"isparent";s:0:"";s:8:"titlelen";s:0:"";s:3:"row";s:1:"7";s:4:"type";s:3:"son";s:6:"typeid";s:1:"0";s:7:"orderby";s:5:"order";s:8:"orderway";s:3:"asc";s:5:"limit";s:0:"";s:5:"class";s:0:"";s:6:"result";s:0:"";s:5:"where";s:0:"";}'); foreach($arr as $index=>$field){ if($index == 0 ) $isfirst = true;else $isfirst = false; if($index == count($arr)-1 ) $islast = true;else $islast = false; ?><li class="<?php if($arctype['fid'] == $field['id'] or $arctype['id'] == $field['id']): ?>on<?php endif; ?>" _t_nav="p<?php echo ($index+2); ?>"><h2><a href="<?php echo ($field['url']); ?>"><?php echo ($field['name']); ?></a></h2></li><?php } ?></ul></div></div></div><div class="search search2"><form action="<?php echo U('commonform/search/');?>" method="post"><input type="hidden" name="type" value="1"/><p><input type="text" name="key" id="" placeholder="请输入搜索关键字"><input type="submit" value="搜索"><img src="__BASE__/images/cloase.jpg" alt="" class="close_sear"></p></form></div><div class="navigation-down"><?php  $m = D("Arctype"); $arr = $m->getData('a:12:{s:8:"addfield";s:3:"off";s:8:"isparent";s:0:"";s:8:"titlelen";s:0:"";s:3:"row";s:1:"7";s:4:"type";s:3:"son";s:6:"typeid";s:1:"0";s:7:"orderby";s:5:"order";s:8:"orderway";s:3:"asc";s:5:"limit";s:0:"";s:5:"class";s:0:"";s:6:"result";s:0:"";s:5:"where";s:0:"";}'); foreach($arr as $index=>$field){ if($index == 0 ) $isfirst = true;else $isfirst = false; if($index == count($arr)-1 ) $islast = true;else $islast = false; if($field['id'] != 4 and $field['id'] != 7): ?><div id="p<?php echo ($index+2); ?>" class="nav-down-menu <?php if($index != 0): ?>menu-3<?php endif; ?> menu-1" style="display: none;" _t_nav="p<?php echo ($index+2); ?>"><div class="navigation-down-inner"><?php  $variable472658['typeid'] = ($field['id']); $m = D("Arctype"); $arr = $m->getData('a:11:{s:8:"addfield";s:3:"off";s:8:"isparent";s:0:"";s:8:"titlelen";s:2:"20";s:3:"row";s:0:"";s:4:"type";s:3:"son";s:7:"orderby";s:5:"order";s:8:"orderway";s:3:"asc";s:5:"limit";s:0:"";s:5:"class";s:0:"";s:6:"result";s:0:"";s:5:"where";s:0:"";}',serialize($variable472658)); foreach($arr as $index=>$field2){ if($index == 0 ) $isfirst = true;else $isfirst = false; if($index == count($arr)-1 ) $islast = true;else $islast = false; ?><a href="<?php echo ($field2['url']); ?>"><?php echo ($field2['name']); ?></a><?php } ?></div></div><?php endif; } ?></div></div></div><script>$(document).ready(function(event){

      $('.searicon').click(function() {	       	

		$('.search').slideDown();

	   	return false;

    });



  $(".search").click(function(event) {

		var e = event || window.event;  

		if(e.stopPropagation) { //W3C阻止冒泡方法  

	        e.stopPropagation();  

	    } else {

	        e.cancelBubble = true; //IE阻止冒泡方法

	    }  

	});

  

  $(document).click(function(event){

	$(".search").slideUp();

  })

  $('.close_sear').click(function(event) {

  	$('.search').slideUp(300);

  });

  

})

jQuery(document).ready(function(){

	var qcloud={};

	$('[_t_nav]').hover(function(){

		var _nav = $(this).attr('_t_nav');

		clearTimeout( qcloud[ _nav + '_timer' ] );

		qcloud[ _nav + '_timer' ] = setTimeout(function(){

		$('[_t_nav]').each(function(){

		$(this)[ _nav == $(this).attr('_t_nav') ? 'addClass':'removeClass' ]('nav-up-selected');

		});

		$('#'+_nav).stop(true,true).slideDown(200);

		}, 150);

	},function(){

		var _nav = $(this).attr('_t_nav');

		clearTimeout( qcloud[ _nav + '_timer' ] );

		qcloud[ _nav + '_timer' ] = setTimeout(function(){

		$('[_t_nav]').removeClass('nav-up-selected');

		$('#'+_nav).stop(true,true).slideUp(200);

		}, 150);

	});

});

</script><!-- header --><div id="slideBox" class="slideBox clearfix"><div class="hd"><ul></ul></div><div class="bd"><ul><?php  $m = new CommonModel('addiframe'); $arr = $m->getData('a:8:{s:6:"typeid";s:2:"40";s:3:"row";s:0:"";s:5:"table";s:9:"addiframe";s:7:"orderby";s:8:"sortrank";s:8:"orderway";s:4:"desc";s:5:"limit";s:0:"";s:5:"where";s:8:"status=1";s:6:"result";s:0:"";}'); foreach($arr as $index=>$field){ ?><li><a href="<?php echo ($field['src']); ?>" style="background: url(<?php echo ($field['img']); ?>) center center no-repeat;-webkit-background-size: cover;background-size: cover;"></a></li><?php } ?></ul></div></div><script type="text/javascript">jQuery(".slideBox").slide({titCell:".hd ul",mainCell:".bd ul",autoPlay:true,autoPage:"true",effect:'fold'});

</script><div class="index_one clearfix"><div class="txtScroll-left"><a href="<?php echo listUrl(8);?>"><img src="__BASE__/images/jia.jpg" alt=""></a><div class="hd">最新公告：</div><div class="bd"><ul class="infoList"><?php  $m = D("Archives"); $arr = $m->getData('a:16:{s:7:"desclen";s:0:"";s:8:"titlelen";s:2:"36";s:8:"addfield";s:2:"on";s:5:"color";s:3:"off";s:3:"row";s:2:"10";s:4:"flag";s:0:"";s:6:"typeid";s:1:"8";s:5:"class";s:0:"";s:7:"orderby";s:2:"id";s:8:"orderway";s:4:"desc";s:5:"title";s:0:"";s:9:"channelid";s:0:"";s:5:"limit";s:0:"";s:9:"pagebreak";s:3:"off";s:5:"where";s:0:"";s:6:"result";s:0:"";}'); foreach($arr as $index=>$field){ if($index == 0 ) $isfirst = true;else $isfirst = false; if($index == count($arr)-1 ) $islast = true;else $islast = false; ?><li><a href="http://www.neeq.com.cn/disclosure/announcement.html?companyCode=831331" target="_blank"><?php echo ($field['title']); ?></a></li><?php } ?></ul></div></div><script type="text/javascript">		 jQuery(".txtScroll-left").slide({titCell:".hd ul",mainCell:".bd ul",autoPage:true,effect:"topLoop",autoPlay:true,scroll:1,vis:1,trigger:"click"});

		</script><div class="one_box"><a class="next"></a><a class="prev"></a><div class="bd"><ul class="picList"><li><a href="<?php echo listUrl(5);?>"><span class="one_1"></span><i>安防运营服务</i></a></li><li><a href="<?php echo listUrl(6);?>"><span class="one_2"></span><i>智慧城市建设</i></a></li><li><a href="http://www.huaaotech.com/index.php/index-view-aid-20.html"><span class="one_3"></span><i>光谷微警务</i></a></li><li><a href="http://www.huaaotech.com/index.php/index-view-aid-289.html"><span class="one_5"></span><i>阳光微警务</i></a></li><li><a href="http://www.huaaotech.com/index.php/index-view-aid-314.html"><span class="one_6"></span><i>平安校园直通车</i></a></li><li><a href="<?php echo listUrl(36);?>"><span class="one_4"></span><i>自助服务设备</i></a></li></ul></div></div><script type="text/javascript">		jQuery(".one_box").slide({mainCell:".bd ul",autoPage:true,effect:"leftLoop",vis:4,trigger:"click"});

		</script></div><!-- one --><div class="index_two  clearfix"><div class="two_box clearfix"><div class="slideTxtBox wow fadeInUp"><div class="two_l_tit">新闻中心</div><div class="hd"><ul><!-- <li>媒体聚焦<span>/</span></li> --><li>新闻资讯<span>/</span></li><li>视频新闻</li></ul></div><div class="bd"><!-- <ul><div id="demo3" class="picBtnTop_index clearfix"><div class="bd2"><ul><?php  $m = D("Archives"); $arr = $m->getData('a:16:{s:7:"desclen";s:3:"180";s:8:"titlelen";s:2:"36";s:8:"addfield";s:2:"on";s:5:"color";s:3:"off";s:3:"row";s:1:"3";s:4:"flag";s:1:"s";s:6:"typeid";s:2:"18";s:5:"class";s:0:"";s:7:"orderby";s:2:"id";s:8:"orderway";s:4:"desc";s:5:"title";s:0:"";s:9:"channelid";s:0:"";s:5:"limit";s:0:"";s:9:"pagebreak";s:3:"off";s:5:"where";s:0:"";s:6:"result";s:0:"";}'); foreach($arr as $index=>$field){ if($index == 0 ) $isfirst = true;else $isfirst = false; if($index == count($arr)-1 ) $islast = true;else $islast = false; ?><li><a href="<?php echo ($field['url']); ?>"><img src="<?php echo (($field['img'])?($field['img']):$defaultimg); ?>" /></a></li><?php } ?></ul></div><div class="hd2"><ul><?php  $m = D("Archives"); $arr = $m->getData('a:16:{s:7:"desclen";s:3:"180";s:8:"titlelen";s:2:"36";s:8:"addfield";s:2:"on";s:5:"color";s:3:"off";s:3:"row";s:1:"3";s:4:"flag";s:1:"s";s:6:"typeid";s:2:"18";s:5:"class";s:0:"";s:7:"orderby";s:2:"id";s:8:"orderway";s:4:"desc";s:5:"title";s:0:"";s:9:"channelid";s:0:"";s:5:"limit";s:0:"";s:9:"pagebreak";s:3:"off";s:5:"where";s:0:"";s:6:"result";s:0:"";}'); foreach($arr as $index=>$field){ if($index == 0 ) $isfirst = true;else $isfirst = false; if($index == count($arr)-1 ) $islast = true;else $islast = false; ?><li><dl><dt class="dt<?php echo ($index+1); ?>"></dt><dd><h5><span><?php echo jldate($field['pubdate']);?></span><a href="<?php echo ($field['url']); ?>"><?php echo ($field['title']); ?></a></h5><p><?php echo ($field['desc']); ?></p></dd></dl></li><?php } ?></ul><a href="<?php echo listUrl(18);?>" class="more"><img src="__BASE__/images/more.png" alt=""></a></div></div></ul> --><!-- 公司新闻 --><ul><div id="demo2" class="picBtnTop_index clearfix"><div class="bd2"><ul><!-- 502*327 --><?php  $m = D("Archives"); $arr = $m->getData('a:16:{s:7:"desclen";s:3:"180";s:8:"titlelen";s:2:"36";s:8:"addfield";s:2:"on";s:5:"color";s:3:"off";s:3:"row";s:1:"3";s:4:"flag";s:1:"s";s:6:"typeid";s:2:"17";s:5:"class";s:0:"";s:7:"orderby";s:2:"id";s:8:"orderway";s:4:"desc";s:5:"title";s:0:"";s:9:"channelid";s:0:"";s:5:"limit";s:0:"";s:9:"pagebreak";s:3:"off";s:5:"where";s:0:"";s:6:"result";s:0:"";}'); foreach($arr as $index=>$field){ if($index == 0 ) $isfirst = true;else $isfirst = false; if($index == count($arr)-1 ) $islast = true;else $islast = false; ?><li><a href="<?php echo ($field['url']); ?>"><img src="<?php echo (($field['img'])?($field['img']):$defaultimg); ?>" /></a></li><?php } ?></ul></div><div class="hd2"><ul><?php  $m = D("Archives"); $arr = $m->getData('a:16:{s:7:"desclen";s:3:"180";s:8:"titlelen";s:2:"36";s:8:"addfield";s:2:"on";s:5:"color";s:3:"off";s:3:"row";s:1:"3";s:4:"flag";s:1:"s";s:6:"typeid";s:2:"17";s:5:"class";s:0:"";s:7:"orderby";s:2:"id";s:8:"orderway";s:4:"desc";s:5:"title";s:0:"";s:9:"channelid";s:0:"";s:5:"limit";s:0:"";s:9:"pagebreak";s:3:"off";s:5:"where";s:0:"";s:6:"result";s:0:"";}'); foreach($arr as $index=>$field){ if($index == 0 ) $isfirst = true;else $isfirst = false; if($index == count($arr)-1 ) $islast = true;else $islast = false; ?><li><dl><dt class="dt<?php echo ($index+1); ?>"></dt><dd><h5><span><?php echo jldate($field['pubdate']);?></span><a href="<?php echo ($field['url']); ?>"><?php echo ($field['title']); ?></a></h5><p><?php echo ($field['desc']); ?></p></dd></dl></li><?php } ?></ul><a href="<?php echo listUrl(17);?>" class="more"><img src="__BASE__/images/more.png" alt=""></a></div></div><!-- 公司新闻 --></ul><ul><div id="demo1" class="picBtnTop_index clearfix"><div class="bd2"><ul><!-- 502*327 --><?php  $m = D("Archives"); $arr = $m->getData('a:16:{s:7:"desclen";s:3:"180";s:8:"titlelen";s:2:"36";s:8:"addfield";s:2:"on";s:5:"color";s:3:"off";s:3:"row";s:1:"3";s:4:"flag";s:1:"s";s:6:"typeid";s:2:"19";s:5:"class";s:0:"";s:7:"orderby";s:2:"id";s:8:"orderway";s:4:"desc";s:5:"title";s:0:"";s:9:"channelid";s:0:"";s:5:"limit";s:0:"";s:9:"pagebreak";s:3:"off";s:5:"where";s:0:"";s:6:"result";s:0:"";}'); foreach($arr as $index=>$field){ if($index == 0 ) $isfirst = true;else $isfirst = false; if($index == count($arr)-1 ) $islast = true;else $islast = false; ?><li><a href="<?php echo ($field['url']); ?>"><img src="<?php echo (($field['img'])?($field['img']):$defaultimg); ?>" /></a></li><?php } ?></ul></div><div class="hd2"><ul><?php  $m = D("Archives"); $arr = $m->getData('a:16:{s:7:"desclen";s:3:"180";s:8:"titlelen";s:2:"36";s:8:"addfield";s:2:"on";s:5:"color";s:3:"off";s:3:"row";s:1:"3";s:4:"flag";s:1:"s";s:6:"typeid";s:2:"19";s:5:"class";s:0:"";s:7:"orderby";s:2:"id";s:8:"orderway";s:4:"desc";s:5:"title";s:0:"";s:9:"channelid";s:0:"";s:5:"limit";s:0:"";s:9:"pagebreak";s:3:"off";s:5:"where";s:0:"";s:6:"result";s:0:"";}'); foreach($arr as $index=>$field){ if($index == 0 ) $isfirst = true;else $isfirst = false; if($index == count($arr)-1 ) $islast = true;else $islast = false; ?><li><dl><dt class="dt<?php echo ($index+1); ?>"></dt><dd><h5><span><?php echo jldate($field['pubdate']);?></span><a href="<?php echo ($field['url']); ?>"><?php echo ($field['title']); ?></a></h5><p><?php echo ($field['desc']); ?></p></dd></dl></li><?php } ?></ul><a href="<?php echo listUrl(19);?>" class="more"><img src="__BASE__/images/more.png" alt=""></a></div></div></ul></div></div><script type="text/javascript">		jQuery(".slideTxtBox").slide({effect:'left'});

		jQuery("#demo2").slide({titCell:".hd2 li",mainCell:".bd2 ul",effect:"top",triggerTime:0 });

		jQuery("#demo3").slide({titCell:".hd2 li",mainCell:".bd2 ul",effect:"top",triggerTime:0 });

		jQuery("#demo1").slide({titCell:".hd2 li",mainCell:".bd2 ul",effect:"top",triggerTime:0 });

		</script></div></div><!-- two 新闻中心--><div class="index_three clearfix"><div id="case" class="case"><div class="hd"><span>经典案例</span><ul></ul></div><div class="bd"><ul><li><?php  $m = D("Archives"); $arr = $m->getData('a:16:{s:7:"desclen";s:0:"";s:8:"titlelen";s:2:"24";s:8:"addfield";s:2:"on";s:5:"color";s:3:"off";s:3:"row";s:0:"";s:4:"flag";s:0:"";s:6:"typeid";s:1:"3";s:5:"class";s:0:"";s:7:"orderby";s:2:"id";s:8:"orderway";s:4:"desc";s:5:"title";s:0:"";s:9:"channelid";s:0:"";s:5:"limit";s:3:"0,3";s:9:"pagebreak";s:3:"off";s:5:"where";s:0:"";s:6:"result";s:0:"";}'); foreach($arr as $index=>$field){ if($index == 0 ) $isfirst = true;else $isfirst = false; if($index == count($arr)-1 ) $islast = true;else $islast = false; ?><a href="<?php echo ($field['url']); ?>" class="case<?php echo ($index+1); ?>"><span><img src="<?php echo (($field['img'])?($field['img']):$defaultimg); ?>" alt=""><em><?php echo ($field['title']); ?></em></span></a><?php } ?><a href="javascript:void(0);" class="case4"><!--这个定死--><span><i class="pin1"><img src="__BASE__/images/pin1.png" alt=""></i><i class="pin2"><img src="__BASE__/images/pin2.png" alt=""></i><i class="pin3"><img src="__BASE__/images/pin3.png" alt=""></i><i class="pin4"><img src="__BASE__/images/pin4.png" alt=""></i><i class="pin5"><img src="__BASE__/images/pin5.png" alt=""></i><i class="pin6"><img src="__BASE__/images/pin6.png" alt=""></i><i class="pin7"><img src="__BASE__/images/pin7.png" alt=""></i><i class="pin8"><img src="__BASE__/images/pin8.png" alt=""></i></span></a><?php  $m = D("Archives"); $arr = $m->getData('a:16:{s:7:"desclen";s:0:"";s:8:"titlelen";s:2:"24";s:8:"addfield";s:2:"on";s:5:"color";s:3:"off";s:3:"row";s:0:"";s:4:"flag";s:0:"";s:6:"typeid";s:1:"3";s:5:"class";s:0:"";s:7:"orderby";s:2:"id";s:8:"orderway";s:4:"desc";s:5:"title";s:0:"";s:9:"channelid";s:0:"";s:5:"limit";s:3:"3,3";s:9:"pagebreak";s:3:"off";s:5:"where";s:0:"";s:6:"result";s:0:"";}'); foreach($arr as $index=>$field){ if($index == 0 ) $isfirst = true;else $isfirst = false; if($index == count($arr)-1 ) $islast = true;else $islast = false; ?><a href="<?php echo ($field['url']); ?>" class="case<?php echo ($index+5); ?>"><span><img src="<?php echo (($field['img'])?($field['img']):$defaultimg); ?>" alt=""><em><?php echo ($field['title']); ?></em></span></a><?php } ?></li><!-- 第一屏所有图END --><li><?php  $m = D("Archives"); $arr = $m->getData('a:16:{s:7:"desclen";s:0:"";s:8:"titlelen";s:2:"24";s:8:"addfield";s:2:"on";s:5:"color";s:3:"off";s:3:"row";s:0:"";s:4:"flag";s:0:"";s:6:"typeid";s:1:"3";s:5:"class";s:0:"";s:7:"orderby";s:2:"id";s:8:"orderway";s:4:"desc";s:5:"title";s:0:"";s:9:"channelid";s:0:"";s:5:"limit";s:3:"6,3";s:9:"pagebreak";s:3:"off";s:5:"where";s:0:"";s:6:"result";s:0:"";}'); foreach($arr as $index=>$field){ if($index == 0 ) $isfirst = true;else $isfirst = false; if($index == count($arr)-1 ) $islast = true;else $islast = false; ?><a href="<?php echo ($field['url']); ?>" class="case<?php echo ($index+1); ?>"><span><img src="<?php echo (($field['img'])?($field['img']):$defaultimg); ?>" alt=""><em><?php echo ($field['title']); ?></em></span></a><?php } ?><a href="javascript:void(0);" class="case4"><!--这个定死--><span><i class="pin1"><img src="__BASE__/images/pin1.png" alt=""></i><i class="pin2"><img src="__BASE__/images/pin2.png" alt=""></i><i class="pin3"><img src="__BASE__/images/pin3.png" alt=""></i><i class="pin4"><img src="__BASE__/images/pin4.png" alt=""></i><i class="pin5"><img src="__BASE__/images/pin5.png" alt=""></i><i class="pin6"><img src="__BASE__/images/pin6.png" alt=""></i><i class="pin7"><img src="__BASE__/images/pin7.png" alt=""></i><i class="pin8"><img src="__BASE__/images/pin8.png" alt=""></i></span></a><?php  $m = D("Archives"); $arr = $m->getData('a:16:{s:7:"desclen";s:0:"";s:8:"titlelen";s:2:"24";s:8:"addfield";s:2:"on";s:5:"color";s:3:"off";s:3:"row";s:0:"";s:4:"flag";s:0:"";s:6:"typeid";s:1:"3";s:5:"class";s:0:"";s:7:"orderby";s:2:"id";s:8:"orderway";s:4:"desc";s:5:"title";s:0:"";s:9:"channelid";s:0:"";s:5:"limit";s:3:"9,3";s:9:"pagebreak";s:3:"off";s:5:"where";s:0:"";s:6:"result";s:0:"";}'); foreach($arr as $index=>$field){ if($index == 0 ) $isfirst = true;else $isfirst = false; if($index == count($arr)-1 ) $islast = true;else $islast = false; ?><a href="<?php echo ($field['url']); ?>" class="case<?php echo ($index+5); ?>"><span><img src="<?php echo (($field['img'])?($field['img']):$defaultimg); ?>" alt=""><em><?php echo ($field['title']); ?></em></span></a><?php } ?></li><!-- 第二屏所有图END --><li><?php  $m = D("Archives"); $arr = $m->getData('a:16:{s:7:"desclen";s:0:"";s:8:"titlelen";s:2:"24";s:8:"addfield";s:2:"on";s:5:"color";s:3:"off";s:3:"row";s:0:"";s:4:"flag";s:0:"";s:6:"typeid";s:1:"3";s:5:"class";s:0:"";s:7:"orderby";s:2:"id";s:8:"orderway";s:4:"desc";s:5:"title";s:0:"";s:9:"channelid";s:0:"";s:5:"limit";s:4:"12,3";s:9:"pagebreak";s:3:"off";s:5:"where";s:0:"";s:6:"result";s:0:"";}'); foreach($arr as $index=>$field){ if($index == 0 ) $isfirst = true;else $isfirst = false; if($index == count($arr)-1 ) $islast = true;else $islast = false; ?><a href="<?php echo ($field['url']); ?>" class="case<?php echo ($index+1); ?>"><span><img src="<?php echo (($field['img'])?($field['img']):$defaultimg); ?>" alt=""><em><?php echo ($field['title']); ?></em></span></a><?php } ?><a href="javascript:void(0);" class="case4"><!--这个定死--><span><i class="pin1"><img src="__BASE__/images/pin1.png" alt=""></i><i class="pin2"><img src="__BASE__/images/pin2.png" alt=""></i><i class="pin3"><img src="__BASE__/images/pin3.png" alt=""></i><i class="pin4"><img src="__BASE__/images/pin4.png" alt=""></i><i class="pin5"><img src="__BASE__/images/pin5.png" alt=""></i><i class="pin6"><img src="__BASE__/images/pin6.png" alt=""></i><i class="pin7"><img src="__BASE__/images/pin7.png" alt=""></i><i class="pin8"><img src="__BASE__/images/pin8.png" alt=""></i></span></a><?php  $m = D("Archives"); $arr = $m->getData('a:16:{s:7:"desclen";s:0:"";s:8:"titlelen";s:2:"24";s:8:"addfield";s:2:"on";s:5:"color";s:3:"off";s:3:"row";s:0:"";s:4:"flag";s:0:"";s:6:"typeid";s:1:"3";s:5:"class";s:0:"";s:7:"orderby";s:2:"id";s:8:"orderway";s:4:"desc";s:5:"title";s:0:"";s:9:"channelid";s:0:"";s:5:"limit";s:4:"15,3";s:9:"pagebreak";s:3:"off";s:5:"where";s:0:"";s:6:"result";s:0:"";}'); foreach($arr as $index=>$field){ if($index == 0 ) $isfirst = true;else $isfirst = false; if($index == count($arr)-1 ) $islast = true;else $islast = false; ?><a href="<?php echo ($field['url']); ?>" class="case<?php echo ($index+5); ?>"><span><img src="<?php echo (($field['img'])?($field['img']):$defaultimg); ?>" alt=""><em><?php echo ($field['title']); ?></em></span></a><?php } ?></li><!-- 第三屏所有图END --></div><a href="<?php echo listUrl(20);?>"> MORE></a></div><script type="text/javascript">		jQuery(".case").slide({titCell:".hd ul",mainCell:".bd ul",autoPage:"true",effect:'leftLoop'});

		$(document).ready(function(event){

		    $('.case .bd ul li a').hover(function() {

		    	$(this).find('em').stop().animate({bottom:'0'});

		    }, function() {

		    	$(this).find('em').stop().animate({bottom:'-50px'})

		    });

		  

		})



		</script></div><!-- 案例 --><div class="index_four clearfix"><div class="four_tit"><img src="__BASE__/images/lt.jpg" alt=""><span>荣誉客户</span></div><div class="honner"><div class="hd"><ul><?php  $m = D("Arctype"); $arr = $m->getData('a:12:{s:8:"addfield";s:3:"off";s:8:"isparent";s:0:"";s:8:"titlelen";s:0:"";s:3:"row";s:1:"6";s:4:"type";s:3:"son";s:6:"typeid";s:1:"3";s:7:"orderby";s:5:"order";s:8:"orderway";s:3:"asc";s:5:"limit";s:0:"";s:5:"class";s:0:"";s:6:"result";s:0:"";s:5:"where";s:0:"";}'); foreach($arr as $index=>$field){ if($index == 0 ) $isfirst = true;else $isfirst = false; if($index == count($arr)-1 ) $islast = true;else $islast = false; ?><li><?php echo ($field['name']); ?></li><?php } ?></ul></div><div class="bd"><?php  $m = D("Arctype"); $arr = $m->getData('a:12:{s:8:"addfield";s:3:"off";s:8:"isparent";s:0:"";s:8:"titlelen";s:0:"";s:3:"row";s:1:"6";s:4:"type";s:3:"son";s:6:"typeid";s:1:"3";s:7:"orderby";s:5:"order";s:8:"orderway";s:3:"asc";s:5:"limit";s:0:"";s:5:"class";s:0:"";s:6:"result";s:0:"";s:5:"where";s:0:"";}'); foreach($arr as $index=>$field){ if($index == 0 ) $isfirst = true;else $isfirst = false; if($index == count($arr)-1 ) $islast = true;else $islast = false; ?><ul><li><h4><?php echo ($field['name']); ?></h4><h5><?php echo ($field['mdesc']); ?></h5><dl><dt><img src="<?php echo ($field['litpic']); ?>" alt=""></dt><dd id="hone<?php echo ($index+1); ?>"><!--展示不带链接--><div  class="hone"><?php  $variable475105['typeid'] = ($field['id']); $m = D("Archives"); $arr = $m->getData('a:15:{s:7:"desclen";s:0:"";s:8:"titlelen";s:2:"15";s:8:"addfield";s:3:"off";s:5:"color";s:3:"off";s:3:"row";s:3:"100";s:4:"flag";s:0:"";s:5:"class";s:0:"";s:7:"orderby";s:2:"id";s:8:"orderway";s:4:"desc";s:5:"title";s:0:"";s:9:"channelid";s:0:"";s:5:"limit";s:0:"";s:9:"pagebreak";s:3:"off";s:5:"where";s:0:"";s:6:"result";s:0:"";}',serialize($variable475105)); foreach($arr as $index=>$field2){ if($index == 0 ) $isfirst = true;else $isfirst = false; if($index == count($arr)-1 ) $islast = true;else $islast = false; ?><span><a href="<?php echo ($field2['url']); ?>"><?php echo ($field2['title']); ?></a></span><?php } ?></div></dd></dl></li></ul><?php } ?></div></div><script type="text/javascript">		jQuery(".honner").slide({effect:"left"});

		$('.honner .bd ul dl dd#hone1 .hone').jScrollPane({showArrows:true, arrowSize: 16});

		$('.honner .bd ul dl dd#hone2 .hone').jScrollPane({showArrows:true, arrowSize: 16});

		$('.honner .bd ul dl dd#hone3 .hone').jScrollPane({showArrows:true, arrowSize: 16});

		$('.honner .bd ul dl dd#hone4 .hone').jScrollPane({showArrows:true, arrowSize: 16});

		$('.honner .bd ul dl dd#hone5 .hone').jScrollPane({showArrows:true, arrowSize: 16});

		$('.honner .bd ul dl dd#hone6 .hone').jScrollPane({showArrows:true, arrowSize: 16});

		</script></div><!-- 运营 --><footer class="clearfix2"><div class="footer wow fadeInUp clearfix"><div class="foot_left"><div id="outer"><div id="content"><div style="display:block;"><img src="__BASE__/images/ewm1.png" alt=""></div><div><img src="__BASE__/images/ewm1.png" alt=""></div><div><img src="__BASE__/images/ewm3.png" alt=""></div></div><ul id="tab"><li class="current"><img src="__BASE__/images/wb_icon.jpg" alt=""></li><li><img src="__BASE__/images/wx_icon.jpg" alt=""></li><li><img src="__BASE__/images/wap_icon.jpg" alt=""></li></ul></div></div><script>	$(function(){

		window.onload = function()

		{

			var $li = $('#tab li');

			var $ul = $('#content > div');

						

			$li.mouseover(function(){

				var $this = $(this);

				var $t = $this.index();

				$li.removeClass();

				$this.addClass('current');

				$ul.css('display','none');

				$ul.eq($t).css('display','block');

			})

		}

	});



</script><!-- 二维码 --><div class="foot_right"><div class="rigtht"><ul><li><a href="<?php echo listUrl(1);?>">关于我们</a></li><li><a href="<?php echo listUrl(41);?>">招贤纳士</a></li><li><a href="http://www.neeq.com.cn/disclosure/announcement.html?companyCode=831331" target="_blank">投资关系</a></li><li><a href="<?php echo listUrl(9);?>">服务与支持</a></li><li><a href="<?php echo listUrl(37);?>">法律声明</a></li><li><select name="" id=""  onchange="window.open(this.value)"><option value="#">————友情链接————</option><?php  $m = D("Flink"); $arr = $m->getData('a:8:{s:3:"row";s:0:"";s:6:"typeid";s:1:"1";s:7:"orderby";s:2:"id";s:8:"orderway";s:4:"desc";s:5:"limit";s:0:"";s:8:"titlelen";s:2:"12";s:6:"result";s:0:"";s:5:"where";s:0:"";}'); foreach($arr as $index=>$field){ ?><option value="<?php echo ($field['url']); ?>"><?php echo ($field['title']); ?></option><?php } ?></select></li></ul></div><div class="rightb"><p>电话：<?php echo C('JL_DIANHUA');?></p><p>售后电话：<?php echo C('JL_TEL');?></p><p>邮箱：<?php echo C('JL_EMAIL');?></p><p>地址：<?php echo C('JL_DIZHI');?></p><p>网址：<a href="http://huaaotech.com/index.php">www.huaaotech.com</a></p></div></div></div></footer><div class="copy_box clearfix"><div class="copyright clearfix"><div class="copy_left"><?php echo C('JL_COPYRIGHT');?><a href="http://www.miitbeian.gov.cn" target="_blank"><?php echo C('JL_BEIAN');?></a></div><div class="copy_right"><!-- <div class="share bshare-custom"><a title="QQ" class="share-ico share-qqim bshare-qqim"></a><a title="WeChat" class="share-ico share-weixin bshare-weixin"></a><a title="sina" class="share-ico share-sinaminiblog bshare-sinaminiblog"></a></div><span>一键分享：</span> --><a href="http://www.jltech.cn" target="_blank">技术支持：京伦科技</a></div></div></div><!-- JiaThis Button BEGIN --><script type="text/javascript" >var jiathis_config={

	siteNum:6,

	sm:"tsina,weixin,cqq,qzone,douban,email",

	summary:"",

	boldNum:3,

	marginTop:400,

	showClose:true,

	shortUrl:false,

	hideMore:true

}

</script><script type="text/javascript" src="http://v3.jiathis.com/code/jiathis_r.js?btn=r5.gif&move=1" charset="utf-8"></script><!-- JiaThis Button END --><span id="to_top"><img src="__BASE__/images/to_top.png" alt=""></span></body></html>
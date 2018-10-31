<?php if (!defined('THINK_PATH')) exit();?><!doctype html><html lang="en"><head><meta charset="UTF-8"><title><?php echo ($jl_title); ?></title><meta name="Keywords" content="<?php echo ($jl_key); ?>"><meta name="Description" content="<?php echo ($jl_desc); ?>"><meta name="renderer" content="webkit"><meta http-equiv="X-UA-Compatible" content="IE=edge"/><link rel="stylesheet" href="__BASE__/css/animate.min.css"/><link rel="stylesheet" href="__BASE__/css/common.css" /><link rel="stylesheet" href="__BASE__/css/html5.css"><!--[if lte IE 8]><script src="__BASE__/js/html5.js" type="text/javascript"></script><![endif]--><script type="text/javascript" src="__BASE__/js/jquery1.42.min.js"></script><script type="text/javascript" src="__BASE__/js/jquery.SuperSlide.2.1.1.js"></script><script type="text/javascript" src="__BASE__/js/common.js"></script><script src="__BASE__/js/wow.min.js"></script></head><body style="background: #e6eef2;"><div class="header_fix"><div class="head-v3"><div class="navigation-up"><div class="navigation-inner"><h1 class="logo"><a href="<?php echo siteUrl(1);?>index.php"><img src="__BASE__/images/logo2.jpg" alt=""></a></h1><h3 class="zq zq2"><p>证券代码：</p><span>831331</span></h3><span class="searicon"><img src="__BASE__/images/seaicon2.jpg" alt=""></span><div class="navigation-v3"><ul><li _t_nav="home"><h2><a href="<?php echo siteUrl(1);?>index.php">首页</a></h2></li><?php  $m = D("Arctype"); $arr = $m->getData('a:12:{s:8:"addfield";s:3:"off";s:8:"isparent";s:0:"";s:8:"titlelen";s:0:"";s:3:"row";s:1:"7";s:4:"type";s:3:"son";s:6:"typeid";s:1:"0";s:7:"orderby";s:5:"order";s:8:"orderway";s:3:"asc";s:5:"limit";s:0:"";s:5:"class";s:0:"";s:6:"result";s:0:"";s:5:"where";s:0:"";}'); foreach($arr as $index=>$field){ if($index == 0 ) $isfirst = true;else $isfirst = false; if($index == count($arr)-1 ) $islast = true;else $islast = false; ?><li class="<?php if($arctype['fid'] == $field['id'] or $arctype['id'] == $field['id']): ?>on<?php endif; ?>" _t_nav="p<?php echo ($index+2); ?>"><h2><a href="<?php echo ($field['url']); ?>"><?php echo ($field['name']); ?></a></h2></li><?php } ?></ul></div><div class="search"><form action="<?php echo U('commonform/search/');?>" method="post"><input type="hidden" name="type" value="1"/><p><input type="text" name="key" id="" placeholder="请输入搜索关键字"><input type="submit" value="搜索"><img src="__BASE__/images/cloase.jpg" alt="" class="close_sear"></p></form></div></div></div><div class="navigation-down"><?php  $m = D("Arctype"); $arr = $m->getData('a:12:{s:8:"addfield";s:3:"off";s:8:"isparent";s:0:"";s:8:"titlelen";s:0:"";s:3:"row";s:1:"7";s:4:"type";s:3:"son";s:6:"typeid";s:1:"0";s:7:"orderby";s:5:"order";s:8:"orderway";s:3:"asc";s:5:"limit";s:0:"";s:5:"class";s:0:"";s:6:"result";s:0:"";s:5:"where";s:0:"";}'); foreach($arr as $index=>$field){ if($index == 0 ) $isfirst = true;else $isfirst = false; if($index == count($arr)-1 ) $islast = true;else $islast = false; if($field['id'] != 4 and $field['id'] != 7): ?><div id="p<?php echo ($index+2); ?>" class="nav-down-menu <?php if($index != 0): ?>menu-3<?php endif; ?> menu-1" style="display: none;" _t_nav="p<?php echo ($index+2); ?>"><div class="navigation-down-inner"><?php  $variable369437['typeid'] = ($field['id']); $m = D("Arctype"); $arr = $m->getData('a:11:{s:8:"addfield";s:3:"off";s:8:"isparent";s:0:"";s:8:"titlelen";s:2:"20";s:3:"row";s:0:"";s:4:"type";s:3:"son";s:7:"orderby";s:5:"order";s:8:"orderway";s:3:"asc";s:5:"limit";s:0:"";s:5:"class";s:0:"";s:6:"result";s:0:"";s:5:"where";s:0:"";}',serialize($variable369437)); foreach($arr as $index=>$field2){ if($index == 0 ) $isfirst = true;else $isfirst = false; if($index == count($arr)-1 ) $islast = true;else $islast = false; ?><a href="<?php echo ($field2['url']); ?>"><?php echo ($field2['name']); ?></a><?php } ?></div></div><?php endif; } ?></div></div></div><script>$(document).ready(function(event){

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

</script><?php  $route=explode("-",$arctype['route']); $result = count($route); $count = $m->table(C('DB_PREFIX').'arctype')->where('fid='.$route[1])->order('id asc')->count(); if($result == 1){?><div class="main_20 clearfix"></div><?php }else{?><div class="n_nav <?php if($count < 4): ?>n_nav2<?php endif; ?> clearfix"><?php  $variable616013['typeid'] = ($route[1]); $m = D("Arctype"); $arr = $m->getData('a:11:{s:8:"addfield";s:3:"off";s:8:"isparent";s:0:"";s:8:"titlelen";s:0:"";s:3:"row";s:1:"1";s:4:"type";s:4:"self";s:7:"orderby";s:5:"order";s:8:"orderway";s:3:"asc";s:5:"limit";s:0:"";s:5:"class";s:0:"";s:6:"result";s:0:"";s:5:"where";s:0:"";}',serialize($variable616013)); foreach($arr as $index=>$field){ if($index == 0 ) $isfirst = true;else $isfirst = false; if($index == count($arr)-1 ) $islast = true;else $islast = false; ?><h4 style="background: url(__BASE__/images/p<?php echo ($field['id']); ?>_tit.jpg) no-repeat center center;"><i><?php echo ($field['name']); ?></i></h4><?php } ?><p><?php  $variable637149['typeid'] = ($route[1]); $m = D("Arctype"); $arr = $m->getData('a:11:{s:8:"addfield";s:3:"off";s:8:"isparent";s:0:"";s:8:"titlelen";s:0:"";s:3:"row";s:1:"8";s:4:"type";s:3:"son";s:7:"orderby";s:5:"order";s:8:"orderway";s:3:"asc";s:5:"limit";s:0:"";s:5:"class";s:0:"";s:6:"result";s:0:"";s:5:"where";s:0:"";}',serialize($variable637149)); foreach($arr as $index=>$field2){ if($index == 0 ) $isfirst = true;else $isfirst = false; if($index == count($arr)-1 ) $islast = true;else $islast = false; ?><a href="<?php echo ($field2['url']); ?>" class="<?php if($index == 1 or $index == 4 or $index == 7){ echo lv ;}else if($index == 2 or $index == 5){ echo lan;}else{echo ju;} if($arctype['id'] == $field2['id']): ?>n_nav_on<?php endif; ?>"><img src="__BASE__/images/p<?php echo ($field2['fid']); ?>_<?php echo ($field2['id']); ?>.png" alt="" class="p4_img"><img src="__BASE__/images/p<?php echo ($field2['fid']); ?>_<?php echo ($field2['id']); ?>_h.png" alt="" class="p4_img_h"><i><?php echo ($field2['name']); ?></i></a><?php } ?></p></div><?php } ?><!-- 导航 --><div class="main clearfix"><div class="location clearfix">
		当前位置 : <?php echo ($position); ?></div><div class="list_p2_5 clearfix"><div class="p2_5_tit"><img src="__BASE__/images/history_img1.png" alt=""><h5><img src="__BASE__/images/history_img_tit.png" alt=""><span>1996——<?php echo date("Y",time()); ?></span></h5></div><div class="p2_5_left"><img src="__BASE__/images/history_img2.png" alt=""><span>运营商 2013—至今</span><span>集成商 2009—2013</span><span>工程商 1996—2009</span><span>1996年公司成立</span></div><div class="p2_5_right"><div class="wrapper"><div class="main_p2"><h3 class="title"><img src="__BASE__/images/t0_day.jpg" alt=""></h3><?php  $m = D("Arctype"); $arr = $m->getData('a:12:{s:8:"addfield";s:3:"off";s:8:"isparent";s:0:"";s:8:"titlelen";s:0:"";s:3:"row";s:0:"";s:4:"type";s:3:"son";s:6:"typeid";s:2:"14";s:7:"orderby";s:5:"order";s:8:"orderway";s:4:"desc";s:5:"limit";s:0:"";s:5:"class";s:0:"";s:6:"result";s:0:"";s:5:"where";s:0:"";}'); foreach($arr as $index=>$field){ if($index == 0 ) $isfirst = true;else $isfirst = false; if($index == count($arr)-1 ) $islast = true;else $islast = false; ?><div class="year"><h2><a href="javascript:void(0);"><?php echo ($field['name']); ?><i></i></a></h2><div class="list"><ul><?php  $variable498402['typeid'] = ($field['id']); $m = D("Archives"); $arr = $m->getData('a:15:{s:7:"desclen";s:0:"";s:8:"titlelen";s:2:"12";s:8:"addfield";s:2:"on";s:5:"color";s:3:"off";s:3:"row";s:0:"";s:4:"flag";s:0:"";s:5:"class";s:0:"";s:7:"orderby";s:2:"id";s:8:"orderway";s:4:"desc";s:5:"title";s:0:"";s:9:"channelid";s:0:"";s:5:"limit";s:0:"";s:9:"pagebreak";s:3:"off";s:5:"where";s:0:"";s:6:"result";s:0:"";}',serialize($variable498402)); foreach($arr as $index=>$field2){ if($index == 0 ) $isfirst = true;else $isfirst = false; if($index == count($arr)-1 ) $islast = true;else $islast = false; ?><li class="cls <?php if($index == 0): ?>highlight<?php endif; ?>"><p class="date"><?php echo ($field2['title']); ?></p><p class="intro"><a href="<?php echo ($field2['url']); ?>"><?php echo ($field2['shorttitle']); ?></a></p><p class="version">&nbsp;</p><div class="more"><a href="<?php echo ($field2['url']); ?>"><img src="<?php echo ($field2['img']); ?>" alt="" /></a></div></li><?php } ?></ul></div></div><?php } ?></div></div><!-- end --></div></div></div><script type="text/javascript">
$(".main_p2 .title + .year").addClass("close");			//默认第一条展开

$(".main_p2 .year .list").each(function(e, target){
	var $target=  $(target),
	$ul = $target.find("ul");
}); 
$(".main_p2 .year>h2>a").click(function(e){
	e.preventDefault();
	$(this).parents(".year").toggleClass("close");
});

</script><footer class="clearfix2"><div class="footer wow fadeInUp clearfix"><div class="foot_left"><div id="outer"><div id="content"><div style="display:block;"><img src="__BASE__/images/ewm1.png" alt=""></div><div><img src="__BASE__/images/ewm1.png" alt=""></div><div><img src="__BASE__/images/ewm3.png" alt=""></div></div><ul id="tab"><li class="current"><img src="__BASE__/images/wb_icon.jpg" alt=""></li><li><img src="__BASE__/images/wx_icon.jpg" alt=""></li><li><img src="__BASE__/images/wap_icon.jpg" alt=""></li></ul></div></div><script>	$(function(){

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
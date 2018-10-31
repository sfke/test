<?php

	class ShopAction extends Action{ 
		public $memberId;
        protected $no_check_action = array('index','view');

		function _initialize(){
			import('ORG.Util.Cookie');
			$this->render();  //对页面seo信息进行初始化
			if (in_array(ACTION_NAME,$this->no_check_action)){
				return;
			}else{
				if (!$_SESSION[C('MEMBER_AUTH_KEY')]){
					redirect(U('member/login'));
					return;
				}else if(cookie('memberid')!=$_SESSION[C('MEMBER_AUTH_KEY')]){
					$this->error("非法登陆！");
				}
				else $this->memberId = cookie('memberid');
			}
			
		}
		
		private function render(){
			//定义seo相关内容
			$mtitle = "电子商城 - ".C('JL_WEBNAME');
			$mdesc = C('JL_DESC');
			$mkey = C('JL_KEYWORDS');
			$mrights = C('JL_POWERBY');
			
			$this->assign('jl_title',$mtitle);
			$this->assign('jl_desc',$mdesc);
			$this->assign('jl_key',$mkey);
			$this->assign('jl_rights',$mrights);
		}
		
		
		public function index(){
			$catid = !empty($_GET['catid'])?$_GET['catid']:null;
			if($catid!=null){
				$map['catid'] = $catid;
			}
			
			$map['is_on_sale'] = array('eq',1);
			$m = M('goods');
			$arr = $m->where($map)->select();  
			if(!empty($arr)){
				$this->assign("goodsArr",$arr);
			}

			//用于左侧类别选择
			$m2 = M('category');
			$categoryArr = $m2->order("id asc")->select(); 
			$this->assign("categoryArr",$categoryArr);
			
			
			//当前商品类别名称
			foreach($categoryArr as $v){
				if($v['id'] == $catid) $category = $v;
			}
			
			$this->assign("category",$category);
			
			$this->assign("defaultimg",C('SYS_DEFAULT_IMG'));
			$theme = C('SYS_DEFAULT_THEME');
			$this->display($theme.':shop_fl');
			
		}
		
		public function view(){
			$gid = isset($_GET['gid'])?$_GET['gid']:null;
			if($gid == null){
				$this->error("读取商品信息错误！");
				return;
			}
			
			$m = M('goods');
			$arr = $m->where("id = $gid and is_on_sale = 1")->find();
			if(!empty($arr)){
				//商品属性
				$m5 = M('goodsAttr');
				$arr5 = $m5->where("gid = $gid")->select();
				if(!empty($arr5)){				
					foreach($arr5 as $k=>$v){
						$arr5[$k]['name'] = $this->getAttrNameById($v['attrid']);
					}
					$this->assign("goodsAttr",$arr5);
				}
				
				//可选颜色
				if(!empty($arr['gcolor'])){
					$gcolorArr = explode("\n", $arr['gcolor']);
				}
				$this->assign("gcolor",$gcolorArr);
				$this->assign("goodsInfo",$arr);
				//商品的图片
				$m2 = M('goodsImages');
				$arr2 = $m2->where("gid = $gid")->select();
				if(!empty($arr2)){
					$this->assign("goodsImg",$arr2);
				}

				$map = null;
				$map['id'] = array('in',$arr[accessories]);
				$map['is_on_sale'] = 1;
				$arr3 = $m->where($map)->limit('0,3')->select();
				if(!empty($arr3)){
					$this->assign("goodsRelated",$arr3);
				}

			}
			
			//推荐商品
			$arr4 = $m->field('id,name,litpic')->where("is_on_sale = 1 and is_c = 1 and id!=$gid")->order('id desc')->select();
			if(!empty($arr4)){
				$this->assign("goodsC",$arr4);
			}
		
			//用于左侧类别选择
			$m2 = M('category');
			$categoryArr = $m2->order("id asc")->select();
			$this->assign("categoryArr",$categoryArr);
			
			
			$this->assign("defaultimg",C('SYS_DEFAULT_IMG'));
			$theme = C('SYS_DEFAULT_THEME');
			$this->display($theme.':cpgmxq');
				
		}

	
		
		private function getAttrNameById($id){
			$data = S('attributeArr');
			//$data = array();
			if(empty($data)){
				$m = M('attribute');
				$arr = $m->field('id,name')->select();
				foreach($arr as $v){
					$attrArr[$v['id']] = $v['name'];
				}
				S('attributeArr',$attrArr);
				return $attrArr[$id];
			}else{
				return $data[$id];
			}

		}
		
		
		public function cartShow(){
			$mid = $this->memberId;
			$ssid = $_COOKIE['PHPSESSID'];
			$m = M('cart');
			$cartArr = $m->where("mid = $mid and session_id = '$ssid'")->select();
			if(!empty($cartArr)){
				$cartNum = 0;
				$cartPrice = 0;
				foreach($cartArr as $v){
					$cartNum++;
					$cartPrice += $v['gprice']*$v['gnumber'];
				}
				$this->assign("cartNum",$cartNum);
				$this->assign("cartPrice",$cartPrice);
				$this->assign("cartArr",$cartArr);
			}

			//推荐商品
			$m2 = M('goods');
			$arr = $m2->field('id,name,litpic')->where("is_on_sale = 1 and is_c = 1")->order('id desc')->select();
			if(!empty($arr)){
				$this->assign("goodsC",$arr);
			}
			
			$this->assign("mid",$mid);
			$this->assign("defaultimg",C('SYS_DEFAULT_IMG'));
			$theme = C('SYS_DEFAULT_THEME');
			$this->display($theme.':gwc');
		}
		

		
		public function orderShow(){
			$mid = $this->memberId;
			$ssid = $_COOKIE['PHPSESSID'];
			$oid = isset($_GET['oid'])?$_GET['oid']:null;
			if($oid == null){
				$this->error("该订单不存在！");
				return;
			}
			$m = M('orderInfo');
			$m2 = M('orderGoods');
			$m3 = M('memberAddr');
			
			$orderInfo = $m->where("id = $oid")->find();
			if(empty($orderInfo)){
				$this->error("未知的订单号！");
				return;
			}else if(empty($orderInfo['sn'])){
				$this->error("订单流水号不存在致！");
				return;
			}else if($orderInfo['mid'] != $mid){
				$this->error("您无权查看该订单信息！");
				return;
			}else{
				$this->assign("orderInfo",$orderInfo);
				//获取订单商品信息
				$goodsArr = $m2->where("oid = $oid")->select();
				if(!empty($goodsArr)){
					$this->assign("goodsArr",$goodsArr);
				}

			}

			//收货地址
			$map = array();
			$map['mid'] = array('eq',$mid);
			
			if($orderInfo['orderstatus'] != 1 || $orderInfo['paystatus'] !=1 ){
				$map['id'] = array('eq',$orderInfo['addrid']);
			}
			
			$addrArr = $m3->where($map)->select();
			if(!empty($addrArr)){
				$this->assign("addrs",$addrArr);
			}
			
			
			$this->assign("mid",$mid);
			$this->assign("defaultimg",C('SYS_DEFAULT_IMG'));
			$theme = C('SYS_DEFAULT_THEME');
			$this->display($theme.':order');
		
		}
		
		
		
		public function orderView(){
			$mid = $this->memberId;
			$ssid = $_COOKIE['PHPSESSID'];
			$oid = isset($_GET['oid'])?$_GET['oid']:null;
			if($oid == null){
				$this->error("该订单不存在！");
				return;
			}
			$m = M('orderInfo');
			$m2 = M('orderGoods');
			$m3 = M('memberAddr');
				
			$orderInfo = $m->where("id = $oid")->find();
			if(empty($orderInfo)){
				$this->error("未知的订单号！");
				return;
			}else if(empty($orderInfo['sn'])){
				$this->error("订单流水号不存在致！");
				return;
			}else if($orderInfo['mid'] != $mid){
				$this->error("您无权查看该订单信息！");
				return;
			}else{
				$this->assign("orderInfo",$orderInfo);
				//获取订单商品信息
				$goodsArr = $m2->where("oid = $oid")->select();
				if(!empty($goodsArr)){
					$this->assign("goodsArr",$goodsArr);
				}
			
			}
		
			
			$this->assign("add_time",!empty($orderInfo['add_time'])?date("Y-m-d h:i:s",$orderInfo['add_time']):"");
			$this->assign("pay_time",!empty($orderInfo['pay_time'])?date("Y-m-d h:i:s",$orderInfo['pay_time']):"");
			$this->assign("shipping_time",!empty($orderInfo['shipping_time'])?date("Y-m-d h:i:s",$orderInfo['shipping_time']):"");
			$this->assign("confirm_time",!empty($orderInfo['confirm_time'])?date("Y-m-d h:i:s",$orderInfo['confirm_time']):"");
			$this->assign("close_time",!empty($orderInfo['close_time'])?date("Y-m-d h:i:s",$orderInfo['close_time']):"");
			//可执行操作
			$control = '';
			if($orderInfo['orderstatus'] == 1 && $orderInfo['paystatus'] == 1) $control ='<input type="button" value="关闭交易" onclick="closeOrder('.$orderInfo['id'].')" />';
			if($orderInfo['orderstatus'] == 1 && $orderInfo['paystatus'] == 2) $control ='<input type="button" value="申请退款" onclick="payBack('.$orderInfo['id'].')" />';
			if($orderInfo['shippingstatus'] == 2) $control ='<input type="button" value="确认收货" onclick="confirmOrder('.$orderInfo['id'].')" /><input type="button" value="申请退款" onclick="payBack('.$orderInfo['id'].')" />';
			if($orderInfo['orderstatus'] != 1) $control = "";
			
			
			$this->assign("control",$control);
			$this->assign("mid",$mid);
			$this->assign("defaultimg",C('SYS_DEFAULT_IMG'));
			$theme = C('SYS_DEFAULT_THEME');
			$this->display($theme.':grzx3');
		
		}
		
		
		
		
	}





















?>
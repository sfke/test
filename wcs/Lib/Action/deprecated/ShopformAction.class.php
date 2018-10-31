<?php

	class ShopformAction extends Action{ 
		public $memberId;
		function _initialize(){
			import('ORG.Util.Cookie');
			if (in_array(ACTION_NAME,C('SHOP_ACTION_NO_CHECK'))){
				return;
			}else{
				if (!$_SESSION[C('MEMBER_AUTH_KEY')]){
					redirect(U('member/login'));
					return;
				}else if(cookie('memberid')!=$_SESSION[C('MEMBER_AUTH_KEY')]){
						$this->error("非法登陆！");
					}
				else{
					$this->memberId = cookie('memberid');
				}
			}				
		}
		
		
		public function cartAdd(){
			if(empty($_POST)){
				$this->error("添加商品到购物车失败！");
				return;
			}
			$gid = $_POST["gid"];
			$mid = $this->memberId;
			$ssid = $_COOKIE['PHPSESSID'];
			$stype = $_POST["stype"];
			$gattr = $_POST["gattr"];
			$gnumber = $_POST["gnumber"];
			if(!empty($_POST["addgoods"]) && is_array($_POST["addgoods"])){
				$data['addgoods'] = $_POST["addgoods"];
			}
				
			$gidArr = array($gid);
			foreach($data['addgoods'] as $v){
				if(!empty($v) && $v != $gid){
					$gidArr[] = $v;
				}
			}
				
			if(empty($gidArr)){
				$this->error("没有任何商品被添加到购物车！");
				return;
			}
			//获取商品详细信息
			$m = M('goods');
			$m2 =M('cart');
			//检查购物车
			$m2->where("mid = $mid and session_id != '$ssid'")->delete();
			$cartArr = $m2->field('id,gid')->where("mid = $mid and session_id = '$ssid'")->select();
			$oldcartArr = array();
			if(!empty($cartArr)){
				foreach($cartArr as $v){
					$oldcartArr[$v['gid']] = $v['id'];
				}
			}
				
				
				
			foreach($gidArr as $v){
				$tempArr = $m->field('id,sn,name,mprice,price,litpic,weight')->where("id = $v")->find();
				if(empty($tempArr)){
					continue;
				}else{
					$data = array();
					$data['mid'] = $mid;
					$data['session_id'] = $ssid;
					$data['gid'] = $tempArr['id'];
					$data['gsn'] = $tempArr['sn'];
					$data['gname'] = $tempArr['name'];
					$data['mprice'] = $tempArr['mprice'];
					$data['gprice'] = $tempArr['price'];
					$data['litpic'] = $tempArr['litpic'];
					$data['gweight'] = $tempArr['weight'];
					$data['is_real'] = 1;
					$data['stype'] = $stype;
						
					if($v == $gid){
						$data['gnumber'] = $gnumber;
						$data['gattr'] = $gattr;
					}else{
						$data['gnumber'] = 1;
						$data['gattr'] = "随机";
					}
						
					if(!empty($oldcartArr) && key_exists($data['gid'],$oldcartArr)){
						$data_t = array();
						$data_t['id'] = $oldcartArr[$data['gid']];
						$data_t['gnumber'] = $data['gnumber'];
						$data_t['gattr'] = $data['gattr'];
						$m2->create($data_t);
						if($m2->save()===false){
							$this->error("更新购物车发生错误 错误代码：0460！");
							return;
						}
					}else{
						$m2->create($data);
						if($m2->add()===false){
							$this->error("更新购物车发生错误 错误代码：0459！");
							return;
						}
		
					}
		
				}
		
			}

			$this->redirect('shop/cartShow');
		}
		
		
		
		public function cartAddOne(){
			$gid = $_GET["gid"];
			if(empty($gid)){
				$this->error("读取商品信息失败！");
				return;
			}
			
			$mid = $this->memberId;
			$ssid = $_COOKIE['PHPSESSID'];
			$m = M('goods');
			$m2 =M('cart');
				$tempArr = $m->field('id,sn,name,mprice,price,litpic,weight')->where("id = $gid")->find();
				if(!empty($tempArr)){
					$data = array();
					$data['mid'] = $mid;
					$data['session_id'] = $ssid;
					$data['gid'] = $tempArr['id'];
					$data['gsn'] = $tempArr['sn'];
					$data['gname'] = $tempArr['name'];
					$data['mprice'] = $tempArr['mprice'];
					$data['gprice'] = $tempArr['price'];
					$data['litpic'] = $tempArr['litpic'];
					$data['gweight'] = $tempArr['weight'];
					$data['is_real'] = 1;
					$data['gnumber'] = 1;
					$data['gattr'] = "随机";
					$data['stype'] = 1;
					$oldCartArr = $m2->field('id,gnumber')->where("mid = $mid and session_id = '$ssid' and gid = $gid")->find();
			
					if(!empty($oldCartArr)){
						$data_t = array();
						$data_t['id'] = $oldCartArr['id'];
						$data_t['gnumber'] = intval($oldCartArr['gnumber'])+1;
						$data_t['gattr'] = $data['gattr'];
						$m2->create($data_t);
						if($m2->save()===false){
							$this->error("更新购物车发生错误 错误代码：0460！");
							return;
						}
					}else{
						$m2->create($data);
						if($m2->add()===false){
							$this->error("更新购物车发生错误 错误代码：0459！");
							return;
						}
					}
			
				}else{
					$this->error("该商品不存在！");
					return;
					
				}
				$this->redirect('shop/cartShow');
		}
		
		
		public function  makeOrder(){
			$mid = $this->memberId;
			$ssid = $_COOKIE['PHPSESSID'];
			$m = M('cart');
			$m2 = M('memberAddr');
			$map['mid'] = $mid;
			$map['session_id'] = $ssid;
			
			$addrArr = $m2->where("mid = $mid and `main` = 1 ")->find();
			if(empty($addrArr)){
				$this->error("您还没有设置收货地址，请先设置！",U('member/addrs'));
				return;
			}
			
			$cartArr = $m->where($map)->select();
			if(empty($cartArr)){
				$this->error("购物车为空,无法生成订单！",U('shop/index'));
				return;
			}else{
				//金额
				$mount = 0;
				//创建订单
				$m3 = M('orderInfo');
				$order_sn = date('Ymdhis',time()).$mid;
				$data = array();
				$data['sn'] = $order_sn;
				$data['mid'] = $mid;
				$data['orderstatus'] = 1;
				$data['shippingstatus'] = 1;
				$data['paystatus'] = 1;
				$data['addrid'] = $addrArr['id'];
				$data['consignee'] = $addrArr['name'];
				$data['country'] = $addrArr['country'];
				$data['province'] = $addrArr['province'];
				$data['city'] = $addrArr['city'];
				$data['district'] = $addrArr['district'];
				$data['address'] = $addrArr['address'];
				$data['zipcode'] = $addrArr['zipcode'];
				$data['tel'] = $addrArr['tel'];
				$data['mobile'] = $addrArr['mobile'];
				$data['remark'] = '';
				$data['stype'] = $cartArr[0]['stype'];
				$data['sname'] = '';
				$data['scode'] = '';
				$data['paytype'] = '';
				$data['payname'] = '';
				$data['amount'] = $mount;
				$data['paid'] = 0;
				$data['add_time'] = time();
				$data['confirm_time'] = '';
				$data['pay_time'] = '';
				$data['shipping_time'] = '';
				$data['msgtobuyer'] = '';
				$m3->create($data);
				if($m3->add()===false){
					$this->error("生成订单失败，请稍后再试！");
					return;
				}else{
					$oid = $m3->getLastInsID();
					$totleWeight = 0;
					$totlePrice = 0;
					$m4 = M('orderGoods');
					foreach($cartArr as $v) {
						$data = array();
						$data['oid'] = $oid;
						$data['gid'] = $v['gid'];
						$data['gname'] = $v['gname'];
						$data['gsn'] = $v['gsn'];
						$data['gnumber'] = $v['gnumber'];
						$data['mprice'] = $v['mprice'];
						$data['gprice'] = $v['gprice'];
						$data['gweight'] = $v['gweight'];
						$data['gattr'] = $v['gattr'];
						$data['is_real'] = $v['is_real'];
						$totleWeight += $v['gnumber']*$v['gweight'];
						$totlePrice  += $v['gnumber']*$v['gprice'];
						$m4->create($data);
						if($m4->add($data)===false){
							$this->error("生成订单失败，请稍后再试！");
							return;
						}
					}
					
					//计算金额
					$mount = shippingPriceCount($oid,$totleWeight);
					//更新金额
					$data = array();	
					$data['id'] = $oid;
					$data['amount'] = $totlePrice+$mount;
					$data['samount'] = $mount;
					$m3->create($data);
					if($m3->save()===false){
						$this->error("生成订单失败，请稍后再试！");
						return;
					}else{
						$m->where($map)->delete();
						$this->redirect('shop/orderShow?oid='.$oid);
					}
				}

			}
			//$this->redirect('shop/orderShow');
		}
		

		
		
		
	}





















?>
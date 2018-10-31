<?php

	class ShopajaxAction extends Action{ 
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
				else $this->memberId = cookie('memberid');
			}
		}
		
		function cartDel(){
			$mid = $_POST['mid'];
			$gid = $_POST['gid'];
			if(empty($mid) || empty($gid)){
				echo -1;
				return;
			}
			$m = M('cart');
			if($m->where("mid = $mid and gid = $gid")->delete()===false){
				echo -1;
				return;
			}else{
				echo 1;
				return;
			}
		}
		
		function cartDelSelected(){
			$mid = $_POST['mid'];
			$gid = $_POST['gid'];
			if(empty($mid) || empty($gid)){
				echo -1;
				return;
			}
			$m = M('cart');
			$map['gid'] = array('in',$gid);
			$map['mid'] = array('eq',$mid);
			if($m->where($map)->delete()===false){
				echo -1;
				return;
			}else{
				echo 1;
				return;
			}

		}
		
		
		public function cartUpdate(){
			$mid = $_POST['mid'];
			$gid = $_POST['gid'];
			$num = $_POST['num'];
			if(empty($mid) || empty($gid)){
				echo -1;
				return;
			}
			$m = M('cart');
			$map['gid'] = array('eq',$gid);
			$map['mid'] = array('eq',$mid);
			$data['gnumber'] = $num;
			if($m->where($map)->save($data)===false){
				echo -1;
				return;
			}else{
				echo 1;
				return;
			}

		}
		
		public function changeOrderAddr(){
			$oid = $_POST['oid'];
			$addrid = $_POST['addrid'];
			if(empty($oid) || empty($addrid)){
				echo -1;
				return;
			}
			
			$m = M('orderInfo');
			$m2 = M('memberAddr');
			$addrArr =  $m2->where("id = $addrid")->find();
			if(empty($addrArr)){
				echo -1;
				return;
			}
			
			$data['id'] = $oid;
			$data['addrid'] = $addrid;
			$data['consignee'] = $addrArr['name'];
			$data['country'] = $addrArr['country'];
			$data['province'] = $addrArr['province'];
			$data['city'] = $addrArr['city'];
			$data['district'] = $addrArr['district'];
			$data['address'] = $addrArr['address'];
			$data['zipcode'] = $addrArr['zipcode'];
			$data['tel'] = $addrArr['tel'];
			$data['mobile'] = $addrArr['mobile'];
			$m->create($data);
			if($m->save() === false){
				echo -1;
				return;
			}else{
				echo 1;
				return;
			}
		}
		
		
		
		public function closeOrder(){
			$mid = $this->memberId;
			$id = $_POST['id'];
			if(empty($id)){
				echo "订单不存在！";
				return;
			}
			
			$m = M('orderInfo');
			$arr = $m->field("id,mid")->where("id = $id")->find();
			if(empty($arr)){
				echo "订单信息错误！";
				return;
			}else if($arr['mid'] != $mid){
				echo "您无权操作该订单！";
				return;
			}else{
				$data['id'] = $id;
				$data['orderstatus'] = 2;
				$data['close_time'] = time();
				$m->create($data);
				if($m->save() === false){
					echo "关闭订单失败！";
					return;
				}else{
					echo 1;
					return;
				}
			
			}
		}

		
		public function getPayBack(){
			$mid = $this->memberId;
			$id = $_POST['id'];
			if(empty($id)){
				echo "订单不存在！";
				return;
			}
				
			$m = M('orderInfo');
			$arr = $m->field("id,mid,shippingstatus")->where("id = $id")->find();
			if(empty($arr)){
				echo "订单信息错误！";
				return;
			}else if($arr['mid'] != $mid){
				echo "您无权操作该订单！";
				return;
			}else{
				$data['id'] = $id;
				$data['paystatus'] = 3;
				if($arr['shippingstatus'] == 2) $data['shippingstatus'] = 3;
				$m->create($data);
				if($m->save() === false){
					echo "申请退款失败！";
					return;
				}else{
					echo 1;
					return;
				}
					
			}

		}
		
		
		public function confirmOrder(){
			$mid = $this->memberId;
			$id = $_POST['id'];
			if(empty($id)){
				echo "订单不存在！";
				return;
			}
			
			$m = M('orderInfo');
			$arr = $m->field("id,mid,shippingstatus")->where("id = $id")->find();
			if(empty($arr)){
				echo "订单信息错误！";
				return;
			}else if($arr['mid'] != $mid){
				echo "您无权操作该订单！";
				return;
			}else{
				$data['id'] = $id;
				$data['orderstatus'] = 3;
				$data['confirm_time'] = time();
				$m->create($data);
				if($m->save() === false){
					echo "确认收货失败！";
					return;
				}else{
					echo 1;
					return;
				}
					
			}
		
		
		}
		
		
		
		
	}





















?>
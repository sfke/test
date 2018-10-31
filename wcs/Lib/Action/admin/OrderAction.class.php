<?php

	class OrderAction extends BaseAction{
		
		public function show(){
			$os = isset($_GET['os'])?$_GET['os']:0;
			$this->assign('listOrderStatus',$os);
			/*position指定以及一些问候信息*/
			$current = "商城订单管理";
			$position = getPosition("商城订单管理");
			$this->assign('current',$current);
			$this->assign('position',$position);
			$this->assign('welcome',getWelcome());
			$this->display();
		}
		
		
		public function orderprocess(){
			$oid = isset($_GET['oid'])?$_GET['oid']:null;
			if($oid === null){
				$this->error("读取订单信息出错！");
				return;
			}
			$m = M('orderInfo');
			$orderGoodsArr = $m->where('id='.$oid)->find();
			if(empty($orderGoodsArr)){
				$this->error("该订单不存在！");
				return;
			}else{
					$m2 = M('shippingType');
					$arr2 = $m2->cache(true)->select();
					foreach($arr2 as $v){
						if($v['id'] == $orderGoodsArr['stype']) 
						$arr['shipping_type'] = $v['type']."  ".$orderGoodsArr['sname'];
					}
					$arr['sname'] =  $orderGoodsArr['sname'];
					$arr['id'] =  $orderGoodsArr['id'];
					$arr['sn'] =  $orderGoodsArr['sn'];
					$arr['remark'] =  $orderGoodsArr['remark'];
					$arr['msgtobuyer'] =  $orderGoodsArr['msgtobuyer'];
					$arr['userid'] = getMemberNameById($orderGoodsArr['mid']);
					$arr['add_time'] =  date("Y年m月d日 h:i:s",$orderGoodsArr['add_time']);
					$arr['pay_time'] =  date("Y年m月d日 h:i:s",$orderGoodsArr['pay_time']);
					$arr['shipping_time'] =  date("Y年m月d日 h:i:s",$orderGoodsArr['shipping_time']);
					$arr['consignee'] = $orderGoodsArr['consignee'];
					$arr['amount'] = $orderGoodsArr['amount'];
					$arr['mobile'] = $orderGoodsArr['mobile'];
					$arr['zipcode'] = $orderGoodsArr['zipcode'];
					$arr['scode'] = $orderGoodsArr['scode'];
					$arr['address'] = getRegionNameById($orderGoodsArr['country'])." ".getRegionNameById($orderGoodsArr['province'])."省 ".getRegionNameById($orderGoodsArr['city'])."市 ".getRegionNameById($orderGoodsArr['district'])."   ".$orderGoodsArr['address'];
					$arr['status'] =  parseOrderStatus($orderGoodsArr['orderstatus'],$orderGoodsArr['shippingstatus'],$orderGoodsArr['paystatus'],false);
	

				
				$this->assign('orderInfo',$arr);
			}
			
			
			
			//处理订单操作选项
			$order_status = $orderGoodsArr['orderstatus'];
			$shipping_status = $orderGoodsArr['shippingstatus'];
			$pay_status = $orderGoodsArr['paystatus'];
			
			$orderClose = 0;
			$orderEdit = 0;
			$orderShiped = 0;
			$payback = 0;
			
			switch($order_status){
				case 1 : 
						if($pay_status == 1) //未付款
							{ $orderClose=0;$orderEdit=0;$orderShiped=1;$payback=1;}
						else if($pay_status == 2)//已付款
							{
								switch($shipping_status){
									case 1 : $orderClose=1;$orderEdit=1;$orderShiped=0;$payback=0;break; //未发货
									case 2 : $orderClose=1;$orderEdit=1;$orderShiped=0;$payback=1;break; //已经发货
									case 3 : $orderClose=1;$orderEdit=1;$orderShiped=1;$payback=0;break; //退货中
								}

							}
						else if($pay_status == 3)//申请退款
						{
							$orderClose=1;$orderEdit=1;$orderShiped=1;$payback=0;
						}else{
							$orderClose=1;$orderEdit=1;$orderShiped=1;$payback=1;
						} break;
				case 2 :		
				case 3 :
				default: $orderClose=1;$orderEdit=1;$orderShiped=1;$payback=1;break;
			}
			
			
			
			
			$this->assign('orderClose',$orderClose);
			$this->assign('orderEdit',$orderEdit);
			$this->assign('orderShiped',$orderShiped);
			$this->assign('payback',$payback);
			$this->assign('oid',$oid);
			/*position指定以及一些问候信息*/
			$current = "商城订单处理";
			$position = getPosition("商城订单处理");
			$this->assign('current',$current);
			$this->assign('position',$position);
			$this->assign('welcome',getWelcome());
			$this->display();
		}
		
		
		
	}





























?>
<?php

	class ExtajaxAction extends Action {
	
		public function moveMember(){
			$items = isset($_POST['items'])?$_POST['items']:null;
			$fid = isset($_POST['fid'])?$_POST['fid']:null;
			if($items === null || $fid === null){
				echo -1;
				return ;
			}
			$m = M('member');
			if($m->where('id in ('.$items.')')->setField('fid',$fid)!==false){
				echo 1; 	
				return;
			}else{
				echo -2;
				return;
			}
			
		}
		
		
		public function getMemberInfo(){
		
			$userid = isset($_POST['userid'])?$_POST['userid']:null;

			$m=M('member');
			$arr = $m->field('id,uname')->where("userid='$userid'")->find();
			if(empty($arr)){
				echo -1;
				return;
			}else{
				echo json_encode($arr);
				return;
			}
		}
		
		
		
		public function checkSuperPwd(){
			
			$pwd = isset($_POST['pwd'])?$_POST['pwd']:null;

			if($pwd == strtolower(C('SYS_SUPER_PWD'))){
				echo 1;
			}else{
				echo -1;
			}
	
		}
		
		public function memberResetPwd(){
			$id = isset($_POST['id'])?$_POST['id']:null;
			
			$newpwd = C('SYS_RESET_PWD');
			$newpwd = strrev(MD5($newpwd));
			$m = M('member');
			if($m->where('id='.$id)->setField('pwd',$newpwd)!==false){
				echo 1;
				return;
			}else{
				echo -2;
				return;
			}
		
		}
		
		
		
		
		/*****************产品管理******************/
		
		public function getProductJson(){
			$limit = isset($_POST['limit'])?$_POST['limit']:null;
			$start = isset($_POST['start'])?$_POST['start']:null;
			$dir = isset($_POST['dir'])?$_POST['dir']:null;
			$sort = isset($_POST['sort'])?$_POST['sort']:null;
			$exportxls = isset($_POST['exportxls'])?$_POST['exportxls']:0;
			
			
			if($limit!==null && $start!==null)
			$limit = "$start,$limit";
			else $limit = '';
			
			if($dir!==null && $sort!==null)
				$order = "`$sort` $dir";
			else $order = "";

			$map = array();
			foreach($_POST as $k => $v){
				$tempArr = explode("_",$k);
				if($tempArr[0]=="filter" && $v!=""){
					if($tempArr[1]=="like"){
						$map[$tempArr[2]] = array('like','%'.$v.'%');
					}else if($tempArr[1]=="req"){
						$map[$tempArr[2]] = array('eq',$v);
					}
					
				}
			}
			
			$m = M('productExt');
			$totle = $m->where($map)->count(); 
			$rsArray = array();
			$rsArray['id'] = 'id';
			$rsArray['results'] = $totle;
			$pArr = $m->where($map)->order($order)->limit($limit)->select();		
			
			if($exportxls==1){
				$xlsArr = array();
				$i=2;
				$xlsArr[1] = array("机器编号","型号","是否卖出","是否注册","状态","生产日期","录入时间","录入人员");
				foreach($pArr as $v){
					$xlsArr[$i] = array($v['pid'],$v['mode'],$v['saled'],$v['registed'],$v['status'],$v['date'],date('Y-m-d',$v['pubdate']),$v['pubuserid']);
					$i++;
				}
				
				//dump($xlsArr);
				load("@.toolsfunction");
				$xlsName = "product.xlsx";
				if(exportDataToXls($xlsArr,$xlsName)){
					$rs['code'] = 1;
				}else{
					$rs['code'] = 0;
				}
				$rs['download'] = C('JL_BASEHOST').__ROOT__.'/'.APP_NAME."/Explort/xlsx/".$xlsName;
				echo json_encode($rs);
				return;
			}else{
				$rsArray['rows'] = $pArr;				
				echo json_encode($rsArray);
			}
		}
		
		
		
		public function productOneKeySave(){
		
			$data = isset($_POST['data'])?$_POST['data']:null;
			$data = json_decode($data);
			$rs = array();
			$rs['code'] = 1;
			$rs['responseText'] = "保存数据成功！";
			$m = M('productExt');
			if(!empty($data)){
				foreach($data as $v){
					$ndata = array();
					foreach($v as $k2=>$v2){
						$ndata[$k2] = $v2;
					}
					if(array_key_exists('id',$v)){
						$m->create($ndata);
						if($m->save()===false){
							$rs['code'] = -1;
							$rs['responseText'] = "更新数据失败,请稍后再试！";
						}
					}else{
						$ndata['pubdate'] = time();
						$m->create($ndata);
						if($m->add()===false){
							$rs['code'] = -1;
							$rs['responseText'] = "添加数据失败,请稍后再试！";
						}

					}
				}
			}else{
				$rs['code'] = -1;
				$rs['responseText'] = "您没有修改任何内容！";
			}
			
			echo json_encode($rs);

		}
		
		
		public function productOneKeyDel(){
			$items = isset($_POST['data'])?$_POST['data']:null;
			if($items === null){
				$rs['code'] = -1;
				$rs['responseText'] = "您没有选中任何项！";
			}else{
			
				$m = M('productExt');
				if($m->where("`id` in ( $items )")->delete()!==false){
					$rs['code'] = 1;
					$rs['responseText'] = "删除选中项成功！";
				}else{
					$rs['code'] = -1;
					$rs['responseText'] = "删除选中项失败！";
				}
			
			}
			echo json_encode($rs);
		}
		
		
		
		public function getProductDataJson(){
			$limit = isset($_POST['limit'])?$_POST['limit']:null;
			$start = isset($_POST['start'])?$_POST['start']:null;
			$dir = isset($_POST['dir'])?$_POST['dir']:null;
			$sort = isset($_POST['sort'])?$_POST['sort']:null;
			$exportxls = isset($_POST['exportxls'])?$_POST['exportxls']:0;
				
				
			if($limit!==null && $start!==null)
				$limit = "$start,$limit";
			else $limit = '';
				
			if($dir!==null && $sort!==null)
				$order = "`$sort` $dir";
			else $order = "";
			
			$map = array();
			foreach($_POST as $k => $v){
				$tempArr = explode("_",$k);
				if($tempArr[0]=="filter" && $v!=""){
					if($tempArr[1]=="like"){
						$map[$tempArr[2]] = array('like','%'.$v.'%');
					}else if($tempArr[1]=="req"){
						$map[$tempArr[2]] = array('eq',$v);
					}else if($tempArr[1]=="ext"){
						$dateObj = new DateTime($v);
						$day =  $dateObj->format('j');
						$month =  $dateObj->format('n');
						$map['month'] = array('eq',$month);
						$map['day'] = array('eq',$day);
					}	
						
				}
			}
				
			$m = M('data');
			$totle = $m->where($map)->count();
			$rsArray = array();
			$rsArray['id'] = 'id';
			$rsArray['results'] = $totle;
			$pArr = $m->where($map)->order($order)->limit($limit)->select();
				
			if($exportxls==1){
				$xlsArr = array();
				$i=2;
				$xlsArr[1] = array("spo2","心率","呼吸","皮肤导电性","健康指数","活动量","数据时间");
				foreach($pArr as $v){
					$xlsArr[$i] = array($v['spo2'],$v['heartrate'],$v['breath'],$v['skin'],$v['healthindex'],$v['activity'],date('Y-m-d h:i:s',$v['time']));
					$i++;
				}
			
				//dump($xlsArr);
				load("@.toolsfunction");
				$xlsName = "healthData.xlsx";
				if(exportDataToXls($xlsArr,$xlsName)){
					$rs['code'] = 1;
				}else{
					$rs['code'] = 0;
				}
				$rs['download'] = C('JL_BASEHOST').__ROOT__.'/'.APP_NAME."/Explort/xlsx/".$xlsName;
				echo json_encode($rs);
				return;
			}else{
				$rsArray['rows'] = $pArr;
				echo json_encode($rsArray);
			}
		
			
		
		
		
		
		}
		
		
		
		
		public function collecCacheOut(){
			$items = isset($_POST['items'])?$_POST['items']:null;
			$tid = isset($_POST['tid'])?$_POST['tid']:null;
			if(empty($items) || empty($tid)){
				echo -1;
				return;
			}else{
				$m = M('collectCache');
				$m2 = M('archives');
				$m3 = M("addnews");
				$arr = $m->where("id in (".$items.")")->select();
				if(empty($arr)){
					echo -1;
					return;
				}else{
					foreach($arr as $v){
						$data['title'] = $v['title'];
						$data['author'] = $v['author'];
						$data['source'] = $v['source'];
						$data['desc'] = msubstr($v['body'],0,100);
						$data['typeid'] = $tid;
						$data['pubdate'] = $v['pubdate'];
						$m2->create($data);
						if($m2->add()!==false){
							$lastId = $m2->getLastInsID();
							$data2 = array();
							$data2['aid'] = $lastId;
							$data2['txt'] = $v['body'];
							$m3->create($data2);
							if($m3->add()!==false){
								$m->where("id =".$v['id'])->delete();
							}
						}
					}
					echo 1;
					return;
				}

			}
		
		
		
		}
		
		
		
		/*****************订单管理开始******************/
		
		
		public function getOrderJson(){
			$limit = isset($_POST['limit'])?$_POST['limit']:null;
			$start = isset($_POST['start'])?$_POST['start']:null;
			$dir = isset($_POST['dir'])?$_POST['dir']:null;
			$sort = isset($_POST['sort'])?$_POST['sort']:null;
			$exportxls = isset($_POST['exportxls'])?$_POST['exportxls']:0;
				
			if($limit!==null && $start!==null)
				$limit = "$start,$limit";
			else $limit = '';
				
			if($dir!==null && $sort!==null)
				$order = "`$sort` $dir";
			else $order = "";
		
			$map = array();
			foreach($_POST as $k => $v){
				$tempArr = explode("_",$k);
				if($tempArr[0]=="filter" && $v!=""){
					if($tempArr[1]=="like"){
						$map[$tempArr[2]] = array('like','%'.$v.'%');
					}else if($tempArr[1]=="req"){
						$map[$tempArr[2]] = array('eq',$v);
					}
						
				}
			}
				
			//dump($map);exit;
				
			$m = M('orderInfo');
			$totle = $m->where($map)->count();
			$rsArray = array();
			$rsArray['id'] = 'id';
			$rsArray['results'] = $totle;
			$oarr = $m->where($map)->order($order)->limit($limit)->select();
			//echo $m->getLastSql();
		
			foreach($oarr as $v){
				$arr['id'] =  $v['id'];
				$arr['sn'] =  $v['sn'];
				$arr['userid'] = getMemberNameById($v['mid']);
				$arr['add_time'] =  $v['add_time'];
				$arr['consignee'] =  $v['consignee'];
				$arr['amount'] =  $v['amount'];
				$arr['status'] =  parseOrderStatus($v['orderstatus'],$v['shippingstatus'],$v['paystatus']);
				
				$oArr[] = $arr;
			}
			

			
			if($exportxls==1){
				$xlsArr = array();
				$i=2;
				$xlsArr[1] = array("订单编号","下单账户","收货人","下单时间","订单金额","订单状态");
				foreach($oarr as $v){
					$xlsArr[$i] = array($v['sn'],getMemberNameById($v['mid']),$v['consignee'],date("Y-m-d",$v['add_time']),$v['amount'],parseOrderStatus($v['orderstatus'],$v['shippingstatus'],$v['paystatus']));
					$i++;
				}
			
				//dump($xlsArr);
				load("@.toolsfunction");
				$xlsName = "shopOrder.xlsx";
				if(exportDataToXls($xlsArr,$xlsName)){
					$rs['code'] = 1;
				}else{
					$rs['code'] = 0;
				}
				$rs['download'] = C('JL_BASEHOST').__ROOT__.'/'.APP_NAME."/Explort/xlsx/".$xlsName;
				echo json_encode($rs);
				return;
			}else{
			
				$rsArray['rows'] = $oArr;
				echo json_encode($rsArray);
			}	
		
		}
		
		
		
		public function getOrderGoodsInfo(){
		
			$id = isset($_POST['id'])?$_POST['id']:null;
			if($id === null){
				$rs['code'] = -1;
				$rs['html'] = "该订单号不存在！";
			}else{
					
				$m = M();
				//$arr = $m->where("oid=".$id)->select();
				$arr = $m->table('jl_goods goods,jl_order_goods ordergoods')->where('goods.id=ordergoods.gid and ordergoods.oid='.$id)->field('gid,gprice,gnumber,gattr,gsn,gname,litpic,number')->order('ordergoods.id desc')->select();
				if(empty($arr)){
					$rs['code'] = -1;
					$rs['html'] = "该订单号不存在！";
				}else{
					$rs['code'] = 1;
					$rsHtml = '  
					<tr>
				    <td><div ><strong>商品图片</strong></div></td>
				    <td><div ><strong>商品名称</strong></div></td>
				    <td><div ><strong>货号</strong></div></td>
				    <td><div ><strong>价格</strong></div></td>
				    <td><div ><strong>数量</strong></div></td>
				    <td><div ><strong>属性</strong></div></td>
				    <td><div ><strong>库存</strong></div></td>
				    <td><div ><strong>小计</strong></div></td>
				    </tr>';
					foreach($arr as $k=>$v){
						$rsHtml.='
						<tr>
					    <td><img src="'.$v['litpic'].'" ></td>
					    <td><a href="../goods.php?id=9" target="_blank">'.$v['gname'].'</a></td>
					    <td>'.$v['gsn'].'</td>
					    <td><div >'.$v['gprice'].'元</div></td>
					    <td><div>'.$v['gnumber'].'</div></td>
					    <td>'.$v['gattr'].'</td>
					    <td><div>'.$v['number'].'</div></td>
					    <td><div>'.$v['gprice'].'元</div></td>
					    </tr>';
					}
					$rs['html'] = $rsHtml;
				}
					
			}
			echo json_encode($rs);
		
		}
		
		public function getOrderGoodsJson(){
			$oid = isset($_POST['oid'])?$_POST['oid']:null;
			$exportxls = isset($_POST['exportxls'])?$_POST['exportxls']:0;
			if($oid === null){
				$rs['code'] = -1;
				$rs['html'] = "该订单号不存在！";
				echo json_encode($rs);
			}else{
				$m = M('orderGoods');
				$rsArray = array();
				$rsArray['id'] = 'id';
				$oarr = $m->where('oid='.$oid)->select();
				$totleprice = 0;
				$totleweight = 0;
				$i = 0;
				foreach($oarr as $v){
					$i++;
					$arr['id'] =  $v['id'];
					$arr['oid'] =  $v['oid'];
					$arr['gid'] =  $v['gid'];
					$arr['gname'] = $v['gname'];
					$arr['gsn'] = $v['gsn'];
					$arr['gprice'] =  $v['gprice'];
					$arr['gnumber'] =  $v['gnumber'];
					$arr['gattr'] =  $v['gattr'];
					$arr['gweight'] = $v['gweight'];
					$arr['subtotalprice'] = $v['gprice']*$arr['gnumber'];
					$arr['subtotalweight'] = $v['gweight']*$arr['gnumber'];
					$oArr[] = $arr;
					$totleprice += $arr['subtotalprice'];
					$totleweight += $arr['subtotalweight'];
				}
				
				$rsArray['totleprice'] =  $totleprice;
				$rsArray['totleweight'] =  $totleweight;
				$rsArray['shippingprice'] = shippingPriceCount($oid, $totleweight);
				$rsArray['totlepay'] =  $totleprice + $rsArray['shippingprice'];
				
				
				if($exportxls==1){
					$xlsArr = array();
					$i=2;
					$xlsArr[1] = array("商品名称","商品编号","商品单价","商品单重","购买数量","价格小计","价格小计");
					foreach($oarr as $v){
						$xlsArr[$i] = array($v['gname'],$v['gsn'],$v['gprice'],$v['gweight'],$v['gnumber'],$v['gprice']*$arr['gnumber'],$v['gweight']*$arr['gnumber']);
						$i++;
					}
						
					//dump($xlsArr);
					load("@.toolsfunction");
					$xlsName = "shopGoodsOrder.xlsx";
					if(exportDataToXls($xlsArr,$xlsName)){
						$rs['code'] = 1;
					}else{
						$rs['code'] = 0;
					}
					$rs['download'] = C('JL_BASEHOST').__ROOT__.'/'.APP_NAME."/Explort/xlsx/".$xlsName;
					echo json_encode($rs);
					return;
				}else{
					$rsArray['rows'] = $oArr;
					echo json_encode($rsArray);
					$this->flushOrderAmount($oid,$totleprice);
				}
			}
		
		
		
		}
		
		
/* 		private function shippingPriceCount($oid,$weight){
			$rsPrice = 0;
			$headWeight = C('SYS_SHIPPING_HEAD_WEIGHT');
			$rtype = C('SYS_SHOP_DEFAULT_CENTER');
			$m = M('orderInfo');
			$orderArr = $m->field('stype,province')->where('id='.$oid)->find();
			if(empty($orderArr)){
				return -1;
			}else{
				$stype = $orderArr['stype'];
				$toProvince = $orderArr['province'];
			}
			$m2 = M('shipping');
			$srule = $m2->where("rtype = $rtype and stype = $stype and regionid = $toProvince")->find();
			if(!empty($srule)){
				$basePrice = $srule['baseprice'];
				$overweightPrice = $srule['overweight'];
				$freePrice = !empty($srule['freeprice'])?$srule['freeprice']:0;
				
				if($weight <= $headWeight){
					$rsPrice = $basePrice;
				}else if($weight>=$freePrice){
					$rsPrice = 0;
				}else{
					$moreWeight = $weight - $headWeight;
					$morePrice = (intval($moreWeight/C('SYS_SHIPPING_WEIGHT_UNIT'))+1)*$overweightPrice;
					$rsPrice = $basePrice + $morePrice;
				}
				return $rsPrice;
			}else{
				$commonRule = $m2->where("rtype = $rtype and stype = $stype and regionid = 0")->find();
				if(empty($commonRule)){
					return -2;
				}else{
					$basePrice = $commonRule['baseprice'];
					$overweightPrice = $commonRule['overweight'];
					$freePrice = !empty($commonRule['freeprice'])?$commonRule['freeprice']:0;
					
					if($weight <= $headWeight){
						$rsPrice = $basePrice;
					}else if($weight >= $freePrice){
						$rsPrice = 0;
					}else{
						$moreWeight = $weight - $headWeight;
						$morePrice = (intval($moreWeight/C('SYS_SHIPPING_WEIGHT_UNIT'))+1)*$overweightPrice;
						$rsPrice = $basePrice + $morePrice;
					}
					return $rsPrice;
				}		
			}

		} */
		
		
		public function orderGoodsDel(){
			$id = isset($_POST['id'])?$_POST['id']:null;
			if($id === null){
				$rs['code'] = -1;
				$rs['responseText'] = "订单中不存在该产品";
			}else{
					
				$m = M('orderGoods');
				if($m->where("`id` in ( $id )")->delete()!==false){
					$rs['code'] = 1;
					$rs['responseText'] = "从订单中移除商品成功！";
				}else{
					$rs['code'] = -1;
					$rs['responseText'] = "从订单中移除商品失败！";
				}
					
			}
			echo json_encode($rs);
		
		
		
		}
		
		
		public function orderGoodsSave(){
			$data = isset($_POST['data'])?$_POST['data']:null;
			$data = json_decode($data);
			$rs = array();
			$rs['code'] = 1;
			$rs['responseText'] = "更新订单成功！";
			$m = M('orderGoods');
			if(!empty($data)){
				foreach($data as $v){
					$ndata = array();
					foreach($v as $k2=>$v2){
						$ndata[$k2] = $v2;
					}
						
					if(array_key_exists('id',$v)){
						$m->create($ndata);
						if($m->save()===false){
							$rs['code'] = -1;
							$rs['responseText'] = "更新订单失败,请稍后再试！";
						}
					}
				}
			}else{
				$rs['code'] = -1;
				$rs['responseText'] = "您没有修改任何内容！";
			}
				
			echo json_encode($rs);
		}
		
		
		private function flushOrderAmount($oid,$amount){
				$m = M('orderInfo');
				$arr = $m->field('id,amount')->where('id='.$oid)->find();
				if(empty($arr)){
					return false;
				}else{
					if($arr['amount'] == $amount){
						return false;
					}else{
						$data['id'] = $oid;
						$data['amount'] = $amount;
						$m->create($data);
						if($m->save()===false){
							return false;
						}else{
							return true;
						}

					}

				}

		}
		
		
		
		public function orderClose(){
			$oid = isset($_POST['oid'])?$_POST['oid']:null;
			$msg = isset($_POST['txt'])?$_POST['txt']:null;
			if($oid === null){
				$rs['code'] = -1;
				$rs['html'] = "该订单号不存在！";
				echo json_encode($rs);
			}else{
				$data = array();
				$data['id'] = $oid;
				$data['msgtobuyer'] = $msg;
				$data['orderstatus'] = 2;
				$data['close_time'] = time();
				$m = M('orderInfo');
				$m->create($data);
				if($m->save()===false){
					$rs['code'] = -1;
					$rs['html'] = "关闭交易失败，请稍后再试！";
					echo json_encode($rs);
				}else{
					$rs['code'] = 1;
					$rs['html'] = "关闭交易成功！";
					echo json_encode($rs);
				}
			}

		}
		
		public function orderPayBack(){
			$oid = isset($_POST['oid'])?$_POST['oid']:null;
			if($oid === null){
				$rs['code'] = -1;
				$rs['html'] = "该订单号不存在！";
				echo json_encode($rs);
			}else{
				$data = array();
				$data['id'] = $oid;
				$data['paystatus'] = 4;
				$data['orderstatus'] = 2;
				$data['close_time'] = time();
				$m = M('orderInfo');
				$m->create($data);
				if($m->save()===false){
					$rs['code'] = -1;
					$rs['html'] = "退款失败，请稍后再试！";
					echo json_encode($rs);
				}else{
					$rs['code'] = 1;
					$rs['html'] = "退款成功！";
					echo json_encode($rs);
				}
			}
		
		}
		
		
		
		public function saveShippingInfo(){
			$oid = isset($_POST['oid'])?$_POST['oid']:null;
			$name = isset($_POST['name'])?$_POST['name']:'';
			$code = isset($_POST['code'])?$_POST['code']:'';
			if($oid === null){
				$rs['code'] = -1;
				$rs['html'] = "该订单号不存在！";
				echo json_encode($rs);
			}else{
				$m = M('orderInfo');
				$arr = $m->field('sname,scode,shippingstatus')->where('id='.$oid)->find();
				if(empty($arr)){
					$rs['code'] = -1;
					$rs['html'] = "该订单号不存在！";
					echo json_encode($rs);
				}else{
					if($name==$arr['sname'] && $code==$arr['scode']){
						$rs['code'] = -1;
						$rs['html'] = "没有进行任何修改！";
						echo json_encode($rs);
					}else{
						if($name!=$arr['sname']){
							$data['sname'] = $name;
						}
						if($code!=$arr['scode']){
							$data['scode'] = $code;
						}
						if($arr['shippingstatus']!=2){
							$data['shippingstatus'] = 2;
						}
						$data['id'] = $oid;
						$data['shipping_time'] = time();
						$m->create($data);
						if($m->save()===false){
							$rs['code'] = -1;
							$rs['html'] = "录入物流信息失败，请稍后再试！";
							echo json_encode($rs);
						}else{
							$rs['code'] = 1;
							$rs['html'] = "录入物流信息成功！";
							echo json_encode($rs);
						}
					}
				
				}
			}
		
		}
		
		
		
		/*****************订单管理结束******************/
		
	}


	
	
	
	
	
	
	
	
	
	
	
	
	
?>
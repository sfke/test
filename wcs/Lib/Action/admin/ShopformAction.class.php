<?php

/**
 * 统一处理form表单
 */


	class ShopformAction extends Action{
		
		/**
		 * ===========================================
		 * category start
		 * ===========================================
		 */
		public function categorysave(){
			$m = new CategoryModel();			
			$toid = $_POST['fid'];
			$oldfid = $_POST['oldfid'];
			$catid = $_POST['id'];
			
			$oldcatArr = $m->field('route')->where('id='.$catid)->find();
			$oldRoute = $oldcatArr['route'];
			if($toid!=$oldfid){
				if($toid==0)$route=0;else{
					$route = $m->field('route')->where('id='.$toid)->find();
					$route = $route['route']."-".$toid;
				}
			
				$orders = $m->field('order')->where('fid='.$toid)->select();
			
				$order = 0;
				foreach($orders as $v){
					if($order>$v['order']) continue;
					else { $order = $v['order'];$order++;
					}
				}
			}
			//是修改不是添加
			if($_POST['id']!=null){
				$data = $m->create();

				if(!empty($data['flag']) && is_array($data['flag'])){
					$data['flag'] = implode(',', $data['flag']);
				}else{
					$data['flag'] = '';
				}
				if(!empty($data['filter']) && is_array($data['filter'])){
					$data['filter'] = implode(',', $data['filter']);
				}else{
					$data['filter'] = '';
				}
				
				if($toid!=$oldfid){
					$data['route'] = $route;
					$data['order'] = $order;
				}
				if($m->save($data)!==false){
					if($toid!=$oldfid){
						$oid_son = '';
						$oid_son = $m->getAllSon($catid);
						//$oldRoute = $oldtidArr['route'];
						//移动子栏目
						if($oid_son!=''){
							$oid_sonArr = explode(',', $oid_son);
			
							foreach($oid_sonArr as $sonid){
			
								$tempArr = $m->field('route')->where('id='.$sonid)->find();
								$tempRoute = $tempArr['route'];
								$tempRoute = str_replace($oldRoute,$route,$tempRoute);
								$data = array();
								$data['id'] = $sonid;
								$data['route'] = $tempRoute;
								if($m->save($data)===false){
									$this->error("在移动 id 为 $v 的类别旗下的 $sonid 的子类别时发生意外，批量移动失败！");
									return false;
								}
							}
						}
					}
						
					$this->success("修改商品类别成功！","__GROUP__/category/show");
						
				}else{
					//echo $m->getLastSql();
					$this->error("商品类别没有更新任何内容！");
				}
			}
			
			
		}
		
		
		
		public function categoryadd(){
		
			$toid = $_POST['fid'];
			$m = new CategoryModel();	
			$data = $m->create();
			
			
			if($toid==0)$route=0;else{
				$route = $m->field('route')->where('id='.$toid)->find();
				$route = $route['route']."-".$toid;
			}
			
			$orders = $m->field('order')->where('fid='.$toid)->select();
			
			$order = 0;
			foreach($orders as $v){
				if($order>$v['order']) continue;
				else { $order = $v['order'];$order++;
				}
			}
			
			
			
			if(!empty($data['flag']) && is_array($data['flag'])){
				$data['flag'] = implode(',', $data['flag']);
			}else{
				$data['flag'] = '';
			}
			if(!empty($data['filter']) && is_array($data['filter'])){
				$data['filter'] = implode(',', $data['filter']);
			}else{
				$data['filter'] = '';
			}
			
			$data['route'] = $route;
			$data['order'] = $order;
			
			if($m->add($data)!==false){
				$this->success("添加商品类别成功！",'__GROUP__/category/show');
			}else{
				$this->error("添加商品类别失败！");
			}
		
		}
		
		
		
		/**
		 * ===========================================
		 * category start
		 * ===========================================
		 */
		
		
		
		/**
		 * ===========================================
		 * attribute start
		 * ===========================================
		 */		
		
		public function attributesave(){
			$attr_id = isset($_POST['id'])?$_POST['id']:null;
			if($attr_id==null){
				$this->error('读取属性信息失败！');
			}
		
			$m = M('attribute');
			$data = $m->create();
			if($m->save()!==false){
				$this->success("更新商品属性成功！",'__GROUP__/attribute/show?gtid='.$_POST['fid']);
			}else{
				$this->error("更新商品属性失败！");
			}
			
		}
		
		public function attributeadd(){

			$m = M('attribute');
			$data = $m->create();
			if($m->add()!==false){
				$this->success("添加商品属性成功！",'__GROUP__/attribute/show?gtid='.$_POST['fid']);
			}else{
				$this->error("添加商品属性失败！");
			}
				
		}
		
		
		
		
			
		/**
		 * ===========================================
		 * attribute end
		 * ===========================================
		 */
		

	
		/**
		 * ===========================================
		 * goods start
		 * ===========================================
		 */
	
		
		
		public function goodsadd(){
		
			$m = new GoodsModel();
			$m->create();
			if($m->add()!==false){
				$last_id = $m->getLastInsID();
				$this->success("添加商品成功！",'__GROUP__/goods/edit?gid='.$last_id);
			}else{
				$this->error("添加商品失败！");
			}
		
		}
		
		
		public function goodsedit(){
		
			$m = new GoodsModel();
			$m->create();
			if($m->save()!==false){
				$this->success("修改商品成功！",'__GROUP__/goods/edit?gid='.$_POST['id']);
			}else{
				$this->error("修改商品失败！");
			}
		
		}
		
		
		public function goodsattradd(){
			$m = M('goodsAttr');
			
			//删除原原先的属性
			$delArr = $m->where('gid ='.$_POST['gid'])->select();
			if(!empty($delArr)){
				if($m->where('gid ='.$_POST['gid'])->delete()===false){
					$this->error("删除商品旧属性失败！");
				}
			}
			
			$arr = array();
			foreach($_POST as $k=>$v){
				$temp = explode('_',$k);
				if($temp[0]=='attr'){
					$arr[$temp[1]] = $v;
				}
			}

			foreach($arr as $k=>$v){
				if(empty($v)) continue;
				$data = array();
				$data['gid'] = $_POST['gid'];
				$data['attrid'] = $k;
				$data['value'] = trim($v);
				$m->create($data);
				if($m->add()===false){
				$this->error("添加商品属性失败！");
				}
			}
			$this->success("添加商品属性成功！",'__GROUP__/goods/edit?gid='.$_POST['gid']);
		}
		
	
		public function goodsimagesadd(){
			
			$m = M('goodsImages');
			//删除原原先的图片
			$delArr = $m->where('gid ='.$_POST['gid'])->select();
			if(!empty($delArr)){
				if($m->where('gid ='.$_POST['gid'])->delete()===false){
					$this->error("删除商品旧图片失败！");
				}
			}

			foreach($_POST['intro'] as $k=>$v){
				$data = array();
				$data['gid'] = $_POST['gid'];
				$data['intro'] = $v;
				$data['url'] = $_POST['images'][$k];
				$m->create($data);
				if($m->add()===false){
					$this->error("添加商品图片失败！");
				}
			}
			
			$this->success("添加商品属性成功！",'__GROUP__/goods/edit?gid='.$_POST['gid']);
		}
		
		
		
		
		public function shippingadd(){
		
		
			$m = M('shipping');
			$m->create();
			if($m->add()!==false){
				$this->success("添加运费规则成功！",'__GROUP__/shipping/show');
			}else{
				$this->error("添加运费规则失败！");
			}
		
		
		}
		
		
		public function shippingsave(){
		
		
			$m = M('shipping');
			$m->create();
			if($m->save()!==false){
				$this->success("修改运费规则成功！",'__GROUP__/shipping/show');
			}else{
				$this->error("修改运费规则失败！");
			}
		
		
		}
		
		
		
		
		
	
		/**
		 * ===========================================
		 * goods end
		 * ===========================================
		 */
	
	
	
	
	
	
	
	
	
	
	}
	





























?>
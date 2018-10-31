<?php

class ShopajaxAction extends Action{

	public function changeorder(){
		$id = isset($_POST['id'])?$_POST['id']:null;
		$order = isset($_POST['order'])?$_POST['order']:null;
		$fid = isset($_POST['fid'])?$_POST['fid']:null;
		$act = isset($_POST['act'])?$_POST['act']:null;
		if($id === null || $order === null ||  $act === null){
			echo -1;return;
		}
		
		
		$m = new CategoryModel();
		$arr = $m->where('fid = '.$fid)->select();
		if(empty($arr)){
			echo -2;return;
		}
		
		if($act == 'up'){
			$find = null;
			foreach($arr as $k=>$v){
				if($v['order'] < $order && $find === null) {$find = $k;continue;}
				if($v['order'] > $arr[$find]['order'] && $v['order'] < $order) $find = $k;
			}
		}else if($act == 'down'){
			$find = null;
			foreach($arr as $k=>$v){
				if($v['order'] > $order && $find === null) {$find = $k;continue;}
				if($v['order'] < $arr[$find]['order'] && $v['order'] > $order) $find = $k;
			}
		}
		if($find === null){
			echo -2;return;
		}else{
			$data = array();
			$change = $arr[$find]['order'];
			$change_id = $arr[$find]['id'];
			$data['order'] = $change;
			$data['id'] = $id;
			$m->create($data);
			if($m->save()===false){
				echo -3;
			}else{
				$data = array();
				$data['order'] = $order;
				$data['id'] = $change_id;
				$m->create($data);
				if($m->save()===false){
					echo -4;
				}else{
					echo 1;
				}
			}
		}
	}
	
	
	
	
	public function getattribute(){
		$gtid = isset($_POST['gtid'])?$_POST['gtid']:null;
		$gid = isset($_POST['gid'])?$_POST['gid']:null;
		$action = isset($_POST['action'])?$_POST['action']:'add';
		if($gtid==null){
			echo -1;
		}
			
		$m = M('attribute');
		$m2 = M('goodsAttr');
		$arr = $m->where('fid='.$gtid)->order('`order` asc')->select();
		if($action=='edit'&& $gid!==null)
			$varr = $m2->where('gid='.$gid)->select();
		else 
			$varr = array();
		$html = goodsAttrForm($arr,$varr);
		echo 	$html;
	}
	
	
	
	public function goodsChangeStatus(){
		$gid = !empty($_POST['gid'])?$_POST['gid']:null;
		$field = !empty($_POST['field'])?$_POST['field']:null;
		$status = isset($_POST['status'])?$_POST['status']:null;
		if($field===null || $status===null){
			echo -1;
			return;
		}else{
			$m = new GoodsModel();
			$arr = $m->where('id ='.$gid)->find();
			if(empty($arr)){
				echo -3;return;
			}else{
				$data = array();
				$data['id'] = $gid;
				$data[$field] = $status;
				if($m->save($data)!==false){
					echo 1; return;
				}else{
					return -1;return;
				}
			}
		}
		return;
		
		
	}
	
	
	
	
	
	public function getattrselect(){
		$gtid = !empty($_POST['gtid'])?$_POST['gtid']:null;
		
		if($gtid===null){
			echo -1;
			return;
		}else{
			$m2 = M('attribute');
			$arr = $m2->where('fid ='.$gtid)->select();
			if(empty($arr)){
				echo -3;return;
			}else{
				$html = '';
				foreach($arr as $v){
					
					$html .="<option value ='". $v['id']."'>".$v['name']."</option>"; 
					
				}
				echo $html;
			}
		}
		return;
		
		
		
	}
	
	
	public function goodsattradd(){
		$id = !empty($_POST['id'])?$_POST['id']:null;
		$value = !empty($_POST['value'])?$_POST['value']:null;
		if($id === null || $value === null){
			echo -1;
			return;
		}else{
			$m = M('attribute');
			$tempArr = $m->where('id='.$id)->find();
			if(empty($tempArr)){
				echo -1;
				return;
			}else{
				$valueArr = explode("\n",$tempArr['values']);
				if(in_array($value,$valueArr)){
					echo -2;
					return;
				}else{
				
					$data['id'] = $id;
					$data['values'] =$tempArr['values']."\n".$value;
					$m->create($data);
					if($m->save()===false){
						echo -1; return;
					}else{
						$rsHtml ='<option value="'.$value.'">'.$value.'</option>';
						echo  $rsHtml; return;
					}
			
				}
			
			}

		}
	
	}
	
	
	
	
	
	
	
	
	
/**
 * 待检查的删除请求
 */
	public function delgoodstype(){
		$items = !empty($_POST['items'])?$_POST['items']:null;

		if($items===null){
			echo -1;
			return;
		}else{
			$m2 = M('attribute');
			$arr = $m2->where('fid ='.$items)->find();
			if(!empty($arr)){
				echo -3;return;
			}else{
				$map['id'] = array('in',$items);
				$m = M('goodstype');
				if($m->where($map)->delete()!==false){
					echo 1; return;
				}else{
					echo -2;return;
				}
			}
		}
		return;
	
	}
	
	public function addgoodstype(){
		$items = !empty($_POST['items'])?$_POST['items']:null;
		$m = M('goodstype');
		if($items===null){
			echo -1;
			return;
		}else{
			$arr = $m->where("name = '$items'")->find();
			if(!empty($arr)){
				echo -3;return;
			}else{
				$data['name'] = $items;
				$data['status'] = 1;
				$m->create($data);
				if($m->add()!==false){
					echo 1; return;
				}else{
					echo -2;return;
				}
			}
		}
		return;
	
	}
	
	public function editgoodstype(){
		$items = !empty($_POST['items'])?$_POST['items']:null;
		$name = !empty($_POST['name'])?$_POST['name']:null;
		$m = M('goodstype');
		if($items===null || $name===null){
			echo -1;
			return;
		}else{
			$arr = $m->where("name = '$name' and id != $items" )->find();
			if(!empty($arr)){
				echo -3;return;
			}else{
				$data['name'] = $name;
				$data['id'] = $items;
				$m->create($data);
				if($m->save()!==false){
					echo 1; return;
				}else{
					echo -2;return;
				}
			}
		}
		return;
	
	}
	
	
	
	public function delattribute(){
		$items = !empty($_POST['items'])?$_POST['items']:null;
		if($items===null){
			echo -1;
			return;
		}else{
			$m = M('attribute'); 
			if(1){
				echo -3;return;
			}else{
				$map['id'] = array('in',$items);
				if($m->where($map)->delete()!==false){
					echo 1; return;
				}else{
					echo -2;return;
				}
			}
		}
		return;
		
	
	}
	
	
	
	//通用排序请求 可以重复序号
	public function SetOrderCommon(){
	
		$json = !empty($_POST['json'])?$_POST['json']:null;
		$table = !empty($_POST['table'])?$_POST['table']:null;
		$field = !empty($_POST['field'])?$_POST['field']:'order';
		if($json===null||$table===null){
			echo -3;
			return;
		}else{
			$m = M($table);
			foreach($json as $k =>$v){
				$data['id'] = $k;
				$data[$field] = $v;

				if($m->save($data)===false){
					echo -1;
					return;
				}
			}
		}
	
	}
	
	
	
	public function getGoodsBySelection(){
		$catid = !empty($_POST['catid'])?$_POST['catid']:null;
		$m = M('goods');
		$html = '';
		$arr = $m->field('`id`,`name`')->where('catid = '.$catid)->select();
		if(empty($arr)){
			echo -1;
			return;
		}else{
			foreach($arr as $v){
				$html .= '<option value="'.$v['id'].'">'.$v['name'].'</option>';
			
			}	
			echo $html;
		}

	}
	
	public function goodsAccessoriesSave(){
		$items = !empty($_POST['items'])?$_POST['items']:null;
		$gid = !empty($_POST['gid'])?$_POST['gid']:null;
		$data['accessories'] = $items;
		$data['id'] = $gid;
		$m = M('goods');
		$m->create($data);
		if($m->save()===false){
			echo -1;
			return;
		}else{
			echo 1;
			return;
		}

	}
	
	public function delCategory(){
		$items = !empty($_POST['items'])?$_POST['items']:null;
		$m = M('category');
		$categoryArr = $m->where("fid = $items")->find();
		if(!empty($categoryArr)){
			$rs['code'] = -1;
			$rs['msg'] = "请先删除其子栏目！";
		}else{
			$m2 = M('goods');
			$goodsArr = $m2->where("catid = $items")->find();
			if(!empty($goodsArr)){
				$rs['code'] = -1;
				$rs['msg'] = "请先删除其下商品！";
			}else{
					
				if($m->where("id = $items")->delete() === false){
					$rs['code'] = -1;
					$rs['msg'] = "删除栏目失败！";
				}else{
					$rs['code'] = 1;
					$rs['msg'] = "删除栏目成功！";
				}
			}
		}
	
		echo json_encode($rs);
	}
	

}

?>

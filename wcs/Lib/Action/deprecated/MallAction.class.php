<?php

	class MallAction extends Action{ 
		
		
		public function index(){
			import('ORG.Util.Page');// 导入类
			$m = M('goods');
			$map['is_on_sale'] = array('eq',1);
			$map_orderby = "id desc";
			$count = $m->where($map)->count();// 查询满足要求的总记录数
			$Page  = new Page($count,20);// 实例化分页类 传入总记录数和每页显示的记录数
			$show  = $Page->show();// 分页显示输出
			$list = $m->where($map)->limit($Page->firstRow.','.$Page->listRows)->order($map_orderby)->select();
			$this->assign('list',$list);// 赋值数据集
			$this->assign('page',$show);// 赋值分页输出
			$theme = C('SYS_DEFAULT_THEME');
			$this->display($theme.':shop');
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
				$this->assign("goodsInfo",$arr);
				//商品属性
				$m5 = M('goodsAttr');
				$arr5 = $m5->where("gid = $gid")->select();
				if(!empty($arr5)){
					foreach($arr5 as $k=>$v){
						$arr5[$k]['name'] = $this->getAttrNameById($v['attrid']);
					}
					$this->assign("goodsAttr",$arr5);
				}
			}
				
			$this->assign("defaultimg",C('SYS_DEFAULT_IMG'));
			$theme = C('SYS_DEFAULT_THEME');
			$this->display($theme.':por_show');
		
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
		
		
		public function order(){
			$id = $_GET['id'];
			if(empty($id)){
				$this->error("获取商品名称失败！");
			}
			$m = M('goods');
			$arr = $m->field('id,name,price,sn')->where("id = $id")->find();
			$this->assign("goodInfo",$arr);
			$theme = C('SYS_DEFAULT_THEME');
			$this->display($theme.':shxx');
		}
		
		
		public function makeOrder(){
			$m = M('mallorder');
			$m->create();
			if($m->add()===false){
				$this->error("下单失败！请稍后再试！");
				return;
			}else{
				redirect(U('mall/makeOrderSuccss'));
				return;
			}
		}
		
		public function makeOrderSuccss(){
			$theme = C('SYS_DEFAULT_THEME');
			$this->display($theme.':shxx1');
		}
		
		public function pay(){
			$theme = C('SYS_DEFAULT_THEME');
			$this->display($theme.':shxx2');
		}
		
		
		
	}





















?>
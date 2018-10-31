<?php
class MallAction extends BaseAction {
	
	
	public function order(){		
		import('ORG.Util.Page');// 导入类
			if(!empty($_POST['action'])){
				if($_POST['action'] == 'filter'){
					if(!empty($_POST['orderby'])){
						$arr = orderByParse($_POST['orderby']);
						if(is_array($arr)){
							$map_orderby = "`$arr[0]` $arr[1]";
						}
						$orderby = $_POST['orderby'];
						setcookie("orderby_ad", $_POST['orderby']);
						setcookie("map_orderby_ad", $map_orderby);
		
					}
		
					if(!empty($_POST['searchkey'])){
						$this->assign('searchkey',$_POST['searchkey']);
						$map[$_POST['searchby']] = array('like','%'.$_POST['searchkey'].'%');
					}
				}
			}else{
		
				if(!empty($_COOKIE['map_orderby_ad'])){
		
					$map_orderby = $_COOKIE['map_orderby_ad'];
					$orderby = $_COOKIE['orderby_ad'];
				}
		
		
			}
				
				
			//排序
			$map_orderby = !empty($map_orderby)?$map_orderby:'id desc';
		
				
			$m = M('mallorder');
			//$m2 = new MultitableModel('resume');
			$count = $m->where($map)->count();// 查询满足要求的总记录数
			$Page  = new Page($count,C('SYS_PAGE_SIZE'));// 实例化分页类 传入总记录数和每页显示的记录数
			$show  = $Page->show();// 分页显示输出
				
			//如果是搜索，则在一页内显示所有数据(不分页)
			if(!empty($map[$_POST['searchby']])) {
				$Page->listRows = 1000;
				$show = "一共搜索到 ".$count." 条数据";
			}
				
			$list = $m->where($map)->limit($Page->firstRow.','.$Page->listRows)->order($map_orderby)->select();
			$this->assign('list',$list);// 赋值数据集
			$this->assign('page',$show);// 赋值分页输出
				
				 	
			/*select准备*/
			$arrOrderby = array('id_desc'=>'ID 降序','id_asc'=>'ID 升序','sex_desc'=>'女士 优先','sex_asc'=>'男士 优先');
			$orderby_html = getOptions($arrOrderby,$orderby);
			$arrSearchby = array('name'=>'姓名','phone'=>'手机');
			$searchby_html = getOptions($arrSearchby,$_POST['searchby']);
		
			$this->assign('orderby_html',$orderby_html);
			$this->assign('searchby_html',$searchby_html);
			
			$this->assign('addtable',"mallorder");
				
			/*position指定以及一些问候信息*/
			$current = "商城下单列表";
			$position = getPosition($current);
			$this->assign('current',$current);
			$this->assign('position',$position);
			$this->assign('welcome',getWelcome());
				
			$this->display();
		}
	
	

}
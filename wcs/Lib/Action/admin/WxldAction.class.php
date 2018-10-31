<?php

	/**
	 * 无限联动相关操作
	 * @author Administrator
	 *
	 */
	class WxldAction extends BaseAction{
		
			public function show(){
				$wxld = isset($_GET['wxld'])?$_GET['wxld']:null;
				$m = M('wxldtype');
				$arr = $m->select();
				foreach($arr as  $v){
					$options .='<option value="'.$v['id'].'">'.$v['typename'].'</option>'; 
				}
				$this->assign('options',$options);
				$this->assign('wxld',$wxld);
				/*position指定以及一些问候信息*/
				$current = "无限联动管理列表";
				$position = getPosition("无限联动管理列表");
				$this->assign('current',$current);
				$this->assign('position',$position);
				$this->assign('welcome',getWelcome());
				
				
				$this->display();
			}
			
			public	function setorder(){
				import('ORG.Util.Page');// 导入类
				$fid = isset($_GET['fid'])?$_GET['fid']:null;
				
				$map['fid'] = array('eq',$fid);
				$m = M('wxld');

				$list = $m->where($map)->order('`order` desc')->select();
				$this->assign('list',$list);// 赋值数据集
				$this->assign("wxld",$fid);
				
				/*position指定以及一些问候信息*/
				$current = "无限联动排序管理";
				$position = getPosition(array("无限联动管理列表"=>'__GROUP__/wxld/show',"无限联动排序管理"=>''));
				$this->assign('current',$current);
				$this->assign('position',$position);
				$this->assign('welcome',getWelcome());
				
				$this->display();
				
			} 
			
		
	}






?>
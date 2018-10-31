<?php

// 本类由系统自动生成，仅供测试用途
class GoodstypeAction extends BaseAction {
	
	
	public function show(){
		import('ORG.Util.Page');// 导入类
		$m = M('goodstype');

		$map = array();
		$count = $m->where($map)->count();// 查询满足要求的总记录数
		$Page  = new Page($count,C('SYS_PAGE_SIZE'));// 实例化分页类 传入总记录数和每页显示的记录数
		$show  = $Page->show();// 分页显示输出			
		$list = $m->where($map)->limit($Page->firstRow.','.$Page->listRows)->order('id asc')->select();
		$this->assign('list',$list);// 赋值数据集
		$this->assign('page',$show);// 赋值分页输出
		
		
	
		/*position指定以及一些问候信息*/
		$current = "商品种类列表";
		$position = getPosition("商品种类列表");
		$this->assign('current',$current);
		$this->assign('position',$position);
		$this->assign('welcome',getWelcome());
		$this->display();
	}
	
	
	
		
	

}
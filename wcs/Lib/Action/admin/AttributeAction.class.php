<?php

// 本类由系统自动生成，仅供测试用途
class AttributeAction extends BaseAction {
	
	
	public function show(){
		
		$gtid = isset($_GET['gtid'])?$_GET['gtid']:null;
		if($gtid === null){
			$this->error("读取商品种类信息出错！");
		}
		
		$m2 = new GoodstypeModel();
		$goodstype = $m2->where("id = $gtid")->find();
		
		import('ORG.Util.Page');// 导入类
		$m = M('attribute');
		$map['fid'] = array('eq',$gtid);
		//$GLOBALS['page_params'] = "?gtid=$gtid";
		$count = $m->where($map)->count();// 查询满足要求的总记录数
		$Page  = new Page($count,C('SYS_PAGE_SIZE'),'gtid='.$gtid);// 实例化分页类 传入总记录数和每页显示的记录数
		$show  = $Page->show();// 分页显示输出			
		$list = $m->where($map)->limit($Page->firstRow.','.$Page->listRows)->order('`order` asc')->select();
		$this->assign('list',$list);// 赋值数据集
		$this->assign('page',$show);// 赋值分页输出
		
		
		
		/*商品种类下拉选框*/
		$html = $m2->getSelectHtml($gtid);
		$this->assign('select_html',$html);
		
		
		
		$this->assign('gtid',$gtid);
		
		
		
		/*position指定以及一些问候信息*/
		$current = $goodstype['name']."=>属性列表";
		$position = getPosition("商品种类列表");
		$position = getPosition(array('商品种类列表'=>'__GROUP__/goodstype/show',"$current"=>''));
		$this->assign('current',$current);
		$this->assign('position',$position);
		$this->assign('welcome',getWelcome());
		$this->display();
	}
	
	

	
	
	public function edit() {
		$attr_id = isset($_GET['attr_id'])?$_GET['attr_id']:null;
		if($attr_id === null){
			$this->error("读取商品属性信息出错！");
		}
		
		$m = M('attribute');
		$m2 = new GoodstypeModel();
		$arr = $m->where('id = '.$attr_id)->find();
		if(empty($arr)){
			$this->error("读取商品属性信息出错！");
		}
		
		
		/*商品种类下拉选框*/
		$selectArr = $m2->getSelectArr();

		
		require(APP_INC_PATH.'form/Zebra_Form.php');
		$form = new Zebra_Form('form','post',U('shopform/attributesave'));  //参数分别是 表单名称 提交方法 请求页面
				
		$form->add('text', 'id',$attr_id,array('type' => 'hidden'));
		$form->add('text', 'fid',$arr['fid'],array('type' => 'hidden'));
/* 		$form->add('label', 'label_fid', 'fid', '所属商品种类:');
		$obj = & $form->add('select', 'fid', $arr['fid'],array('style'=>'width:150px;'));
		$obj->add_options($selectArr,true);
		$obj->set_rule(array(
				//'required' => array('error', '必须选择父栏目!')
		)); */
		
		$form->add('label', 'label_name', 'name', '属性名称:');
		$obj = & $form->add('text', 'name',$arr['name'],array('style' => 'width:150px'));
		$obj->set_rule(array(
				'required' => array('error', '必须填写栏目标题!')
		));
		
		
		$form->add('label', 'label_type', 'type', '自定义价格:');
		$obj = & $form->add('radios', 'type', array(
				'1' =>  '允许',
				'0' =>  '禁止'
		),$arr['type']);

		
		$form->add('label', 'label_input_type', 'input_type', '录入方式:');
		$obj = & $form->add('radios', 'input_type', array(
				'0' =>  '单行输入',
				'1' =>  '下拉框选择',
				'2'=>	'多行文本输入'
		),$arr['input_type']);
		
		$form->add('label', 'label_values', 'values', '可选值列表:');
		$obj = & $form->add('textarea', 'values',$arr['values']);
		$obj = &$form->add('note','note_02','values','录入方式为 “下拉框选择” 时该值才生效，多个选择用换行分隔。');
		
		
		// "submit"
		$form->add('submit', 'btnsubmit', '确定');
		
		
		$form_html =  $form->render('*horizontal');
		$this->assign('form_html',$form_html);
	
		/*position指定以及一些问候信息*/
		$current = "属性修改";
		$position = getPosition("商品种类列表");
		$position = getPosition(array('商品种类列表'=>'__GROUP__/goodstype/show','属性列表'=>'__GROUP__/attribute/show?gtid='.$arr['fid'],"$current"=>''));
		$this->assign('current',$current);
		$this->assign('position',$position);
		$this->assign('welcome',getWelcome());

		
		
		$this->display ('common:form');
	
	}
	
	
	
	public function add() {
		$gtid = isset($_GET['gtid'])?$_GET['gtid']:null;
		if($gtid === null){
			$this->error("读取商品种类出错！");
		}
		
		$m2 = new GoodstypeModel();
		
		/*商品种类下拉选框*/
		$selectArr = $m2->getSelectArr();

		
		require(APP_INC_PATH.'form/Zebra_Form.php');
		$form = new Zebra_Form('form','post',U('shopform/attributeadd'));  //参数分别是 表单名称 提交方法 请求页面
				
		
		$form->add('label', 'label_fid', 'fid', '所属商品种类:');
		$obj = & $form->add('select', 'fid',$gtid,array('style'=>'width:150px;'));
		$obj->add_options($selectArr,true);
		$obj->set_rule(array(
				//'required' => array('error', '必须选择父栏目!')
		));
		
		$form->add('label', 'label_name', 'name', '属性名称:');
		$obj = & $form->add('text', 'name','',array('style' => 'width:150px'));
		$obj->set_rule(array(
				'required' => array('error', '必须填写栏目标题!')
		));
		
		
		$form->add('label', 'label_type', 'type', '自定义价格:');
		$obj = & $form->add('radios', 'type', array(
				'1' =>  '允许',
				'0' =>  '禁止'
		),'0');

		
		$form->add('label', 'label_input_type', 'input_type', '录入方式:');
		$obj = & $form->add('radios', 'input_type', array(
				'0' =>  '单行输入',
				'1' =>  '下拉框选择',
				'2'=>	'多行文本输入'
		),'0');
		
		$form->add('label', 'label_values', 'values', '可选值列表:');
		$obj = & $form->add('textarea', 'values','');
		$obj = &$form->add('note','note_02','values','录入方式为 “下拉框选择” 时该值才生效，多个选择用换行分隔。');
		
		
		// "submit"
		$form->add('submit', 'btnsubmit', '确定');
		
		
		$form_html =  $form->render('*horizontal');
		$this->assign('form_html',$form_html);
	
		/*position指定以及一些问候信息*/
		$current = "属性添加";
		$position = getPosition("商品种类列表");
		$position = getPosition(array('商品种类列表'=>'__GROUP__/goodstype/show','属性列表'=>'__GROUP__/attribute/show?gtid='.$gtid,"$current"=>''));
		$this->assign('current',$current);
		$this->assign('position',$position);
		$this->assign('welcome',getWelcome());

		
		
		$this->display ('common:form');
	
	}	
	
	
		
}
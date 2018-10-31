<?php

// 本类由系统自动生成，仅供测试用途
class ShippingAction extends BaseAction {
		
	
		public function show(){
			$stype = isset($_GET['stype'])?$_GET['stype']:1;
			$rtype = isset($_GET['rtype'])?$_GET['rtype']:C('SYS_SHOP_DEFAULT_CENTER');
			
			
			$m = M('shipping');
			$shippingArr = $m->where("`stype`=$stype and `rtype`=$rtype")->select();
			$this->assign('shippingArr',$shippingArr);
			
			//物流方式
			$m2 = M('shippingType');
			$arr2 = $m2->cache(true)->select();
			$shippingTypeHtml = selectArrToHtmlEx($arr2,'id','type',$stype);
			$this->assign('shippingTypeHtml',$shippingTypeHtml);
			//仓库位置
			$m3 = M('region');
			$arr3 = $m3->cache(true)->where('type=1')->select();
			$regionTypeHtml = selectArrToHtmlEx($arr3,'id','name',$rtype);
			$this->assign('regionTypeHtml',$regionTypeHtml);
			

			/*position指定以及一些问候信息*/
			$current = "运费管理列表";
			$position = getPosition("运费管理列表");
			$this->assign('current',$current);
			$this->assign('position',$position);
			$this->assign('welcome',getWelcome());
			$this->display();
		}
		
		
		public function add(){
			$stype = isset($_GET['stype'])?$_GET['stype']:1;
			$rtype = isset($_GET['rtype'])?$_GET['rtype']:13;
			//物流方式
			$m2 = M('shippingType');
			$arr2 = $m2->select();
			$shippingTypeArr = arrayToSelectArray($arr2,'id','type');
			//仓库位置
			$m3 = M('region');
			$arr3 = $m3->cache(true)->where('type=1')->select();
			$regionTypeArr = arrayToSelectArray($arr3,'id','name');
			//发往地区
			$toTypeArr = $regionTypeArr;
			$toTypeArr[0] = "所有地区";
			
		
			require(APP_INC_PATH.'form/Zebra_Form.php');
			$form = new Zebra_Form('form','post',U('shopform/shippingadd'));  //参数分别是 表单名称 提交方法 请求页面
		
			//隐藏表单
		
			$form->add('label', 'label_stype', 'stype', '物流方式:');
			$obj = & $form->add('select', 'stype', $stype);
			$obj->add_options($shippingTypeArr);
			$obj->set_rule(array(
					'required' => array('error', '必须选择物流方式!')
			));
			
			$form->add('label', 'label_rtype', 'rtype', '仓库位置:');
			$obj = & $form->add('select', 'rtype', $rtype);
			$obj->add_options($regionTypeArr);
			$obj->set_rule(array(
					'required' => array('error', '必须选择仓库位置!')
			));
			
			$form->add('label', 'label_regionid', 'regionid', '发往地区:');
			$obj = & $form->add('select', 'regionid','');
			$obj->add_options($toTypeArr,true);
			$obj->set_rule(array(
					'required' => array('error', '必须选择发往地区!')
			));
			
			
			$form->add('label', 'label_baseprice', 'baseprice', '基础邮费:');
			$obj = & $form->add('text', 'baseprice','10',array('style' => 'width:100px'));
			$obj->set_rule(array(
					'required' => array('error', '必须填写基础邮费!'),
					'length'    => array(1, 10, 'error', '基础邮费必须在1位到10位之间!'),
					'number' =>array('','error','基础邮费必须为数字！')
			));
			
			$form->add('label', 'label_overweight', 'overweight', '续重邮费（每KG）:');
			$obj = & $form->add('text', 'overweight','3',array('style' => 'width:100px'));
			$obj->set_rule(array(
					'required' => array('error', '必须填写续重邮费!'),
					'length'    => array(1, 5, 'error', '续重邮费必须在1位到5位之间!'),
					'number' =>array('','error','续重邮费必须为数字！')
			));
			
			
			$form->add('label', 'label_freeprice', 'freeprice', '免邮费金额:');
			$obj = & $form->add('text', 'freeprice','1000',array('style' => 'width:100px'));
			$obj->set_rule(array(
					'number' =>array('','error','免邮费金额必须为数字！')
			));
			
		
		
			$form->add('label', 'label_desc', 'desc', '备注:');
			$obj = & $form->add('text', 'desc','',array('style' => 'width:400px'));  //不要改id
			$obj->set_rule(array(
					//'required' => array('error', '请输入字段默认值!')
			));
		
			$form->add('label', 'label_status', 'status', '状态:');
			$obj = & $form->add('radios', 'status', array(
					'1' =>  '启用',
					'0' =>  '禁用'
			),1);
			$obj->set_rule(array(
					'required' => array('error', '必须选择状态！')
			));
		
			// "submit"
			$form->add('submit', 'btnsubmit', '确定');
			$form_html =  $form->render('*horizontal');
			$this->assign('form_html',$form_html);
				
			/*position指定以及一些问候信息*/
			$current = "运费规则添加";
			$position = getPosition(array('运费规则管理'=>'__GROUP__/shipping/show','运费规则添加'=>''));
			$this->assign('current',$current);
			$this->assign('position',$position);
			$this->assign('welcome',getWelcome());
				
			$this->display ('common:baseform');
		
		}
		
		
		
		public function edit(){
			$id = isset($_GET['id'])?$_GET['id']:null;
			$m = M('shipping');
			$shippingArr = $m->where("id=$id")->find();
			
			//物流方式
			$m2 = M('shippingType');
			$arr2 = $m2->select();
			$shippingTypeArr = arrayToSelectArray($arr2,'id','type');
			//仓库位置
			$m3 = M('region');
			$arr3 = $m3->cache(true)->where('type=1')->select();
			$regionTypeArr = arrayToSelectArray($arr3,'id','name');
			//发往地区
			$toTypeArr = $regionTypeArr;
			$toTypeArr[0] = "所有地区";
				
		
			require(APP_INC_PATH.'form/Zebra_Form.php');
			$form = new Zebra_Form('form','post',U('shopform/shippingsave'));  //参数分别是 表单名称 提交方法 请求页面
		
			$form->add('text', 'id',$id,array('type' => 'hidden'));
			//隐藏表单
		
			$form->add('label', 'label_stype', 'stype', '物流方式:');
			$obj = & $form->add('select', 'stype', $shippingArr['stype']);
			$obj->add_options($shippingTypeArr);
			$obj->set_rule(array(
					'required' => array('error', '必须选择物流方式!')
			));
				
			$form->add('label', 'label_rtype', 'rtype', '仓库位置:');
			$obj = & $form->add('select', 'rtype', $shippingArr['rtype']);
			$obj->add_options($regionTypeArr);
			$obj->set_rule(array(
					'required' => array('error', '必须选择仓库位置!')
			));
				
			$form->add('label', 'label_regionid', 'regionid', '发往地区:');
			$obj = & $form->add('select', 'regionid',$shippingArr['regionid']);
			$obj->add_options($toTypeArr,true);
			$obj->set_rule(array(
					'required' => array('error', '必须选择发往地区!')
			));
				
				
			$form->add('label', 'label_baseprice', 'baseprice', '基础邮费:');
			$obj = & $form->add('text', 'baseprice',$shippingArr['baseprice'],array('style' => 'width:100px'));
			$obj->set_rule(array(
					'required' => array('error', '必须填写基础邮费!'),
					'length'    => array(1, 10, 'error', '基础邮费必须在1位到10位之间!'),
					'number' =>array('','error','基础邮费必须为数字！')
			));
				
			$form->add('label', 'label_overweight', 'overweight', '续重邮费（每KG）:');
			$obj = & $form->add('text', 'overweight',$shippingArr['overweight'],array('style' => 'width:100px'));
			$obj->set_rule(array(
					'required' => array('error', '必须填写续重邮费!'),
					'length'    => array(1, 5, 'error', '续重邮费必须在1位到5位之间!'),
					'number' =>array('','error','续重邮费必须为数字！')
			));
				
				
			$form->add('label', 'label_freeprice', 'freeprice', '免邮费金额:');
			$obj = & $form->add('text', 'freeprice',$shippingArr['freeprice'],array('style' => 'width:100px'));
			$obj->set_rule(array(
					'number' =>array('','error','免邮费金额必须为数字！')
			));
				
		
		
			$form->add('label', 'label_desc', 'desc', '备注:');
			$obj = & $form->add('text', 'desc',$shippingArr['desc'],array('style' => 'width:400px'));  //不要改id
			$obj->set_rule(array(
					//'required' => array('error', '请输入字段默认值!')
			));
		
			$form->add('label', 'label_status', 'status', '状态:');
			$obj = & $form->add('radios', 'status', array(
					'1' =>  '启用',
					'0' =>  '禁用'
			),$shippingArr['status']);
			$obj->set_rule(array(
					'required' => array('error', '必须选择状态！')
			));
		
			// "submit"
			$form->add('submit', 'btnsubmit', '确定');
			$form_html =  $form->render('*horizontal');
			$this->assign('form_html',$form_html);
		
			/*position指定以及一些问候信息*/
			$current = "运费规则修改";
			$position = getPosition(array('运费规则管理'=>'__GROUP__/shipping/show','运费规则修改'=>''));
			$this->assign('current',$current);
			$this->assign('position',$position);
			$this->assign('welcome',getWelcome());
		
			$this->display ('common:baseform');
		
		}
		

		

}


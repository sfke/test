<?php

// 本类由系统自动生成，仅供测试用途
class CategoryAction extends BaseAction {
	
	
	public function show(){
		import ( '@.Class.SuperClassify' );
		$m = new CategoryModel();
		$arr = $m->categoryListArr();

		$this->assign('list',$arr);
		/*position指定以及一些问候信息*/
		$current = "商品类别列表";
		$position = getPosition("商品类别列表");
		$this->assign('current',$current);
		$this->assign('position',$position);
		$this->assign('welcome',getWelcome());
		$this->display();
	}
	
		

	public function edit() {

		$catid = isset($_GET['catid'])?$_GET['catid']:null;
		$fid = isset($_GET['fid'])?$_GET['fid']:null;
		if($catid === null){
			$this->error("读取商品类别信息出错！");
		}
		if($fid === null){
			$this->error("读取商品父类别出错！");
		}
			
		$m = new CategoryModel();
		$arr = $m->getOne($catid);
		$arr['flag'] = explode(',',$arr['flag']);
		$selection = $m->categorySelectArr();
		$selection_c = $m->categorySelectArrT($catid);
		
		
		$m2 = new GoodstypeModel();
		$selectHtml = $m2->getSelectHtml();
		$this->assign('selectHtml',$selectHtml); 
		
		
		if(empty($arr['filter'])){
			$totleFilter = 1;
		}else{
			$filterArr = explode(',',$arr['filter']);
			$html_str = '';
			$totleFilter = 1;
			$m3 = M('attribute');
			foreach($filterArr as $v2){
				
				$temp = $m3->where('id='.$v2)->find();
				$gtid = $temp['fid'];
				$goodstypeHtml = $m2->getSelectHtml($gtid);
				$attrArr = $m3->where('fid='.$gtid)->select();
				$attrHtml = '';
				foreach($attrArr as $v){
					if($v['id']==$v2) 
					$attrHtml.='<option value="'.$v['id'].'" selected >'.$v['name'].'</option>';
					else $attrHtml.='<option value="'.$v['id'].'">'.$v['name'].'</option>';
				}
				
				$html_str.='<tr class="row tr_'.$totleFilter.'"><td valign="top"><label id="label_filter_'.$totleFilter.'" for="mkey">筛选属性'.$totleFilter.':</label></td><td valign="top"><select style="display:inline;" class="control select" id="goodstype_'.$totleFilter.'" value="" onchange = "getAttrSelect(this,'.$totleFilter.')" ><option value="0"> - 请选择 - </option>'.$goodstypeHtml.'</select><select  style="display:inline;" class="control select" id="attribute_'.$totleFilter.'" name="filter[]">'.$attrHtml.'</select><input type="button" type="button" class="control button" value="移除" onclick="removeTr('.$totleFilter.')" style="display:inline;margin-left:120px;"></input></td></tr>';
				$totleFilter++;
				
			}
		}
		

		$this->assign('totleFilter',$totleFilter);
		$this->assign('html_str',$html_str);
		
		require(APP_INC_PATH.'form/Zebra_Form.php');
		
		$form = new Zebra_Form('form','post',U('shopform/categorysave'));  //参数分别是 表单名称 提交方法 请求页面
		
		//隐藏表单
		$obj = & $form->add('text', 'id',$catid,array('type' => 'hidden'));
		$obj = & $form->add('text', 'oldfid',$fid,array('type' => 'hidden'));
				
		
		$form->add('label', 'label_fid', 'fid', '上级类别:');
		$obj = & $form->add('select', 'fid',$fid,array('style'=>'width:150px;'));
		$obj->add_options($selection,true,$selection_c);
		$obj->set_rule(array(
				//'required' => array('error', '必须选择父栏目!')
		));
		
		
		$form->add('label', 'label_name', 'name', '商品类别名称:');
		$obj = & $form->add('text', 'name',$arr['name'],array('style' => 'width:150px'));
		$obj->set_rule(array(
				'required' => array('error', '必须填写栏目标题!')
		));
		
		
		// "flag"
		$form->add('label', 'label_flag', 'flag', '首页推荐属性:');
		$obj = & $form->add('checkboxes', 'flag[]', C('SYS_SHOP_FLAG_ARRAY'),$arr['flag']);
		
		
		$form->add('label', 'label_mtitle', 'mtitle', 'meta标题:');
		$obj = & $form->add('text', 'mtitle',$arr['mtitle'],array('style' => 'width:400px'));
		
		
		$form->add('label', 'label_mkey', 'mkey', 'meta关键词:');
		$obj = & $form->add('text', 'mkey',$arr['mkey'],array('style' => 'width:400px'));
		
		
		$form->add('label', 'label_mdesc', 'mdesc', 'meta描述:');
		$obj = & $form->add('textarea', 'mdesc',$arr['mdesc'],array('style' => 'width:400px'));
		
		$form->add('label', 'label_filter_add', 'filter_add', '筛选属性:');
		$obj = & $form->add('button', 'filter_add','添加一条筛选属性',array('type'=>'button'));

		
		// "submit"
		$form->add('submit', 'btnsubmit', '确定');
		
		
		$form_html =  $form->render('*horizontal');
		$this->assign('form_html',$form_html);
	
		/*position指定以及一些问候信息*/
		$current = "栏目修改";
		$position = getPosition("栏目修改");
		$this->assign('current',$current);
		$this->assign('position',$position);
		$this->assign('welcome',getWelcome());
		
		
		$this->display ('form');
	
	}
	
	
	
	public function add() {

		$m = new CategoryModel();
		$selection = $m->categorySelectArr();
		
		$m2 = new GoodstypeModel();
		$selectHtml = $m2->getSelectHtml();
		$this->assign('selectHtml',$selectHtml); 
		$this->assign('totleFilter',1);
		
		
		require(APP_INC_PATH.'form/Zebra_Form.php');	
		$form = new Zebra_Form('form','post',U('shopform/categoryadd'));  //参数分别是 表单名称 提交方法 请求页面
		
		$form->add('label', 'label_fid', 'fid', '上级类别:');
		$obj = & $form->add('select', 'fid','',array('style'=>'width:150px;'));
		$obj->add_options($selection,true);
		$obj->set_rule(array(
				//'required' => array('error', '必须选择父栏目!')
		));
		
		$form->add('label', 'label_name', 'name', '商品类别名称:');
		$obj = & $form->add('text', 'name','',array('style' => 'width:150px'));
		$obj->set_rule(array(
				'required' => array('error', '必须填写栏目标题!')
		));
		
		// "flag"
		$form->add('label', 'label_flag', 'flag', '首页推荐属性:');
		$obj = & $form->add('checkboxes', 'flag[]', C('SYS_SHOP_FLAG_ARRAY'));
		
		
		$form->add('label', 'label_mtitle', 'mtitle', 'meta标题:');
		$obj = & $form->add('text', 'mtitle','',array('style' => 'width:400px'));
		
		
		$form->add('label', 'label_mkey', 'mkey', 'meta关键词:');
		$obj = & $form->add('text', 'mkey','',array('style' => 'width:400px'));
		
		
		$form->add('label', 'label_mdesc', 'mdesc', 'meta描述:');
		$obj = & $form->add('textarea', 'mdesc','',array('style' => 'width:400px'));
		
		$form->add('label', 'label_filter_add', 'filter_add', '筛选属性:');
		$obj = & $form->add('button', 'filter_add','添加一条筛选属性',array('type'=>'button'));

		
		// "submit"
		$form->add('submit', 'btnsubmit', '确定');
		
		
		$form_html =  $form->render('*horizontal');
		$this->assign('form_html',$form_html);
	
		/*position指定以及一些问候信息*/
		$current = "栏目修改";
		$position = getPosition("栏目修改");
		$this->assign('current',$current);
		$this->assign('position',$position);
		$this->assign('welcome',getWelcome());
		$this->display ('form');
	
	}	
	
	

}
<?php
	
	/*
	 * 友情链接管理
	 */
	
	class FlinkAction extends BaseAction{
		
		/*
		 * 显示出所有的友情链接
		 */
		public function show(){
			
			if(!empty($_POST['action'])){
				$ftid = isset($_POST['ftid'])?$_POST['ftid']:1;
				if($_POST['action'] == 'filter'){
					if(!empty($_POST['orderby'])){
						$arr = orderByParse($_POST['orderby']);
						if(is_array($arr)){
							$map_orderby = "`$arr[0]` $arr[1]";
						}
					}
			
					if(!empty($_POST['searchkey'])){
						$this->assign('searchkey',$_POST['searchkey']);
						$map[$_POST['searchby']] = array('like','%'.$_POST['searchkey'].'%');
					}
				}
			}else{
				$ftid = isset($_GET['ftid'])?$_GET['ftid']:1;
			}
			//排序
			$map_orderby = !empty($map_orderby)?$map_orderby:'id desc';

			$m = M('flinktype');
			$allArr = $m->select();
			$options = '';
			foreach($allArr as $v){
				if($v['id'] == $ftid) $selected = 'selected="selected"';
				else $selected = '';
				$options .= '<option value="'.$v['id'].'" '.$selected.' >'.$v['typename'].'</option>';
				
			}
			
			/*select准备*/
			$arrOrderby = array('id_desc'=>'ID 降序','id_asc'=>'ID 升序','pubdate_desc'=>'创建时间 降序','pubdate_asc'=>'创建时间 升序','sortrank_desc'=>'排序字段 降序','sortrank_asc'=>'排序字段 升序','status_desc'=>'启用在前','status_asc'=>'禁用在前');
			$orderby_html = getOptions($arrOrderby,$_POST['orderby']);
			$arrSearchby = array('title'=>'友链名','id'=>'友链ID','url'=>'网址URL','email'=>'电子邮件');
			$searchby_html = getOptions($arrSearchby,$_POST['searchby']);
			
			$this->assign('orderby_html',$orderby_html);
			$this->assign('searchby_html',$searchby_html);
			
			
			$map['typeid'] = array('eq',$ftid);
			
			$m2 = M('flink');
			$flinkArr = $m2->where($map)->order($map_orderby)->select();
			$this->assign('ftid',$ftid);
			$this->assign('flinkArr',$flinkArr);
			$this->assign('options',$options);

            $this->assign("defaultimg",C('SYS_DEFAULT_IMG'));
			/*position指定以及一些问候信息*/
			$current = "友链管理列表";
			$position = getPosition("友链管理列表");
			$this->assign('current',$current);
			$this->assign('position',$position);
			$this->assign('welcome',getWelcome());
			
			$this->display();
		}
		
		
		
		public function flinkType(){
				
			$m = M('flinktype');
			$map = array();
			$configTypeArr = $m->where($map)->select();
			$this->assign('configTypeArr',$configTypeArr);
		
			/*position指定以及一些问候信息*/
			$current = "友链类别管理";
			$position = getPosition("友链类别管理");
			$this->assign('current',$current);
			$this->assign('position',$position);
			$this->assign('welcome',getWelcome());
		
			$this->display();
				
		}
		
		
		
		
		
		
		
		public function add(){

            $ftid = $this->_param("ftid");


			$m2 = M('flinktype');
			$arr = $m2->select();
			foreach($arr as $v){
				$typeArr[$v['id']] = $v['typename'];
			}

			require(APP_INC_PATH.'form/Zebra_Form.php');
			$form = new Zebra_Form('form','post',U('form/flinksave'));
		
			//隐藏表单
			$form->add('hidden', 'pubdate',time());

			$form->add('label', 'label_typeid', 'typeid', '友链类别:');
			$obj = & $form->add('select', 'typeid', $ftid);
			$obj->add_options($typeArr);
			$obj->set_rule(array(
					'required' => array('error', '必须选择所属栏目!')
			));
				
			$form->add('label', 'label_title', 'title', '网站名称:');
			$obj = & $form->add('text', 'title','');
			$obj->set_rule(array(
					'required' => array('error', '必须填写网站名称!')
			));
				
			$form->add('label', 'label_url', 'url', 'URL:');
			$form->add('text', 'url','',array('style' => 'width:400px'));


			//图片上传
			$form->add('label', 'label_logo', 'logo', '网站LOGO:');
			$obj = & $form->add('kimg', 'logo',C('SYS_DEFAULT_IMG'),array('style' => 'width:400px'));  //不要改id
			$obj->set_rule(array(
					//'required' => array('error', '请输入字段默认值!')
			));
				
			$form->add('label', 'label_email', 'email', '对方email:');
			$obj = & $form->add('text', 'email','');
			$obj->set_rule(array(
					'email'     =>  array('error', '请输入合法的email!')
			));
				
				
			$form->add('label', 'label_remark', 'remark', '备注:');
			$obj = & $form->add('text', 'remark','',array('style' => 'width:400px'));  //不要改id
			$obj->set_rule(array(
					//'required' => array('error', '请输入字段默认值!')
			));
				
			$form->add('label', 'label_status', 'status', '审核状态:');
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
			$current = "友链添加";
			$position = getPosition(array('友链管理列表'=>'__GROUP__/flink/show','友链添加'=>''));
			$this->assign('current',$current);
			$this->assign('position',$position);
			$this->assign('welcome',getWelcome());
			
			$this->display ('common:baseform');
				
		}
		
		
		
		
		
		
		public function edit(){
			$aid = isset($_GET['aid'])?$_GET['aid']:null;
			if($aid == null){
				$this->error("读取该条信息id失败！");
			}
			
			$m = M('flink');
			$map['id'] = array('eq',$aid);
			$flinkArr = $m->where($map)->find();
			$typeid = $flinkArr['typeid'];
			$m2 = M('flinktype');
			$arr = $m2->select();
			foreach($arr as $v){
				$typeArr[$v['id']] = $v['typename'];
			}
			
			//没有图片调用系统默认
			$flinkArr['logo'] = (!empty($flinkArr['logo']))?$flinkArr['logo']:C('SYS_DEFAULT_IMG');
			
			require(APP_INC_PATH.'form/Zebra_Form.php');
			$form = new Zebra_Form('form','post',U('form/flinkupdate'));  //参数分别是 表单名称 提交方法 请求页面

			//隐藏表单
			$obj = & $form->add('hidden', 'id' ,$aid);
			 
			
			$form->add('label', 'label_typeid', 'typeid', '友链类别:');
			$obj = & $form->add('select', 'typeid', $typeid);
			$obj->add_options($typeArr);
			$obj->set_rule(array(
					'required' => array('error', '必须选择所属栏目!')
			));
			
			$form->add('label', 'label_title', 'title', '网站名称:');
			$obj = & $form->add('text', 'title',$flinkArr['title']);
			$obj->set_rule(array(
					'required' => array('error', '必须填写网站名称!')
			));
			
			$form->add('label', 'label_url', 'url', 'URL:');
			$obj = & $form->add('text', 'url',$flinkArr['url'],array('style' => 'width:400px'));  //不要改id
			$obj->set_rule(array(
					'required' => array('error', '请输入字段默认值!')
			));
			
			
			//图片上传
			$form->add('label', 'label_logo', 'logo', '网站LOGO:');
			$obj = & $form->add('kimg', 'logo',$flinkArr['logo'],array('style' => 'width:400px'));  //不要改id
			$obj->set_rule(array(
					//'required' => array('error', '请输入字段默认值!')
			));
			//$form->add('image', 'logo_v','');
			
			$form->add('label', 'label_email', 'email', '对方email:');
			$obj = & $form->add('text', 'email',$flinkArr['email']);
			$obj->set_rule(array(
					'email'     =>  array('error', '请输入合法的email!')
			));
			
			
			$form->add('label', 'label_remark', 'remark', '备注:');
			$obj = & $form->add('text', 'remark',$flinkArr['remark'],array('style' => 'width:400px'));  //不要改id
			$obj->set_rule(array(
					//'required' => array('error', '请输入字段默认值!')
			));
			
			$form->add('label', 'label_status', 'status', '审核状态:');
			$obj = & $form->add('radios', 'status', array(
					'1' =>  '启用',
					'0' =>  '禁用'
			),$flinkArr['status']);
			$obj->set_rule(array(
					'required' => array('error', '必须选择状态！')
			));
			
			// "submit"
			$form->add('submit', 'btnsubmit', '确定');
			$form_html =  $form->render('*horizontal');
			$this->assign('form_html',$form_html);
			
			/*position指定以及一些问候信息*/
			$current = "友链修改";
			$position = getPosition(array('友链管理列表'=>'__GROUP__/flink/show','友链修改'=>''));
			$this->assign('current',$current);
			$this->assign('position',$position);
			$this->assign('welcome',getWelcome());
			
			$this->display ('common:baseform');
			
		} 
		
		
		
		
	}






?>
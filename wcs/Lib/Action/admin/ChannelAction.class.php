<?php

/**
 * 内容模型管理类
 * @author Administrator
 *
 */



class ChannelAction extends BaseAction {
		
		public function show(){
			$m = new ChannelModel();
			$map = array();
			$list = $m->where($map)->order('id asc')->select();
			$this->assign('list',$list);
			/*position指定以及一些问候信息*/
			$current = "内容模型管理列表";
			$position = getPosition("内容模型管理列表");
			$this->assign('current',$current);
			$this->assign('position',$position);
			$this->assign('welcome',getWelcome());
			
			$this->display();
		}
	
	
	
	
		public function add(){
			require(APP_INC_PATH.'form/Zebra_Form.php');
			
			$form = new Zebra_Form('form','post',U('form/channelsave'));
			//隐藏表单
			//$obj = & $form->add('text', 'id',$id,array('type' => 'hidden'));
			
			
			$form->add('label', 'label_name', 'name', '模型名称:');
			$obj = & $form->add('text', 'name', '',array('style'=>'width:150px;'));
			$obj->set_rule(array(
					'required' => array('error', '必须设定模型名称!')
			));
			
			
			$form->add('label', 'label_nid', 'nid', '模型标识:');
			$obj = & $form->add('text', 'nid', '',array('style'=>'width:100px;'));
			$obj->set_rule(array(
					'required' => array('error', '必须设定模型标识!'),
					'alphanumeric' =>array('_','error','标识命名不合法！'),
					'custom' => array('isValExist','error','该标识已经存在！')
			));

			$form->add('label', 'label_issystem', 'issystem', '模型属性:');
			$obj = & $form->add('radios', 'issystem', array(
					'1' =>  '系统模型',
					'2' =>  '扩展模型'
			),'1');
			$obj->set_rule(array(
					'required' => array('error', '必须选择一个模型类别')
			));

			$form->add('label', 'label_type', 'type', '模型类别:');
			$obj = & $form->add('radios', 'type', array(
					'1' =>  '有主表',
					'2' =>  '无主表',
                    '3' =>  '单页'
			),'1');
			$obj->set_rule(array(
					'required' => array('error', '必须选择一个模型类别')
			));
            $form->add("note",'note_type','type',"只对系统模型有效");

			//隐藏表单
			$obj = & $form->add('text', 'is_show','1',array('type' => 'hidden'));
			
		
			$form->add('label', 'label_title', 'title', 'title标题:');
			$obj = & $form->add('text', 'title', '标题',array('style'=>'width:150px;'));

            $form->add('label', 'label_isshow', 'isshow', '状态:');
            $obj = &$form->add('radios', 'isshow',array('0'=>'停用','1'=>'启动'),1);

			$form->add('submit', 'btnsubmit', '确定');
			$rs = $form->render('*horizontal');
			$this->assign('form_html',$rs);
			
			/*position指定以及一些问候信息*/
			$current = "内容模型添加";
			$position = getPosition(array('内容模型管理列表'=>'__GROUP__/channel/show','内容模型添加'=>''));
			$this->assign('current',$current);
			$this->assign('position',$position);
			$this->assign('welcome',getWelcome());
			
			$this->display('form');
		
		
		}


        public function edit(){
            $cid = $this->_get("cid");
            if(empty($cid)){
                $this->error("内容模型不存在！");
            }

            $m = M("Channel");
            $odata = $m->where("id = ".$cid)->find();

            require(APP_INC_PATH.'form/Zebra_Form.php');

            $form = new Zebra_Form('form','post',U('form/channelsave'));
            //隐藏表单
            $obj = & $form->add('text', 'id',$odata['id'],array('type' => 'hidden'));


            $form->add('label', 'label_name', 'name', '模型名称:');
            $obj = & $form->add('text', 'name',$odata['name'],array('style'=>'width:150px;'));
            $obj->set_rule(array(
                'required' => array('error', '必须设定模型名称!')
            ));



            $form->add('label', 'label_nid', 'nid', '模型标识:');
            $obj = & $form->add('text', 'nid', $odata['nid'],array('style'=>'width:100px;','readonly'=>'readonly'));


            $form->add('label', 'label_title', 'title', 'title标题:');
            $obj = & $form->add('text', 'title', $odata['title'],array('style'=>'width:150px;'));

            $form->add('label', 'label_isshow', 'isshow', '状态:');
            $obj = &$form->add('radios', 'isshow',array('0'=>'停用','1'=>'启动'),$odata['isshow']);


            $form->add('submit', 'btnsubmit', '确定');
            $rs = $form->render('*horizontal');
            $this->assign('form_html',$rs);

            /*position指定以及一些问候信息*/
            $current = "内容模型添加";
            $position = getPosition(array('内容模型管理列表'=>'__GROUP__/channel/show','内容模型添加'=>''));
            $this->assign('current',$current);
            $this->assign('position',$position);
            $this->assign('welcome',getWelcome());

            $this->display('form');


        }

				
		
		public function fieldlist(){
			$cid = isset($_GET['cid'])?$_GET['cid']:null;
			if($cid==null){
				$this->error('读取模型出错！');
			}
			$m = new ChannelModel();
			$arr = $m->field('nid,fieldset,addtable,name')->where('id='.$cid)->find();
			$table = $arr['addtable'];
/* 			$m = M($table);
			$arr2 = $m->getDbFields();
			foreach($arr2 as $v){
				if($v!='aid'&&$v!='id'&&$v!='typeid')
				$flist.="<li class='ui-state-default' > ".$v." <a href='".U('channel/fielddel')."?cid=".$cid."&field=".$v."'>删除</a>&nbsp;&nbsp;<a href='".U('channel/fieldedit')."?cid=".$cid."&field=".$v."'>修改</a></li>";
				else $flist.="<li class='ui-state-default' > ".$v."</li>";
			} */
			
			$arr2 = unserialize($arr['fieldset']);
            $flist = "";
			foreach($arr2 as $k=>$v){

				$flist.="<li class='ui-state-default' _index='".$k."' > ".$v['name']." <span class='controls'><a href='".U('channel/fielddel')."?cid=".$cid."&field=".$v['name']."'>删除</a>&nbsp;&nbsp;<a href='".U('channel/fieldedit')."?cid=".$cid."&field=".$v['name']."'>修改</a></span></li>";

			}
			
			$this->assign("cid",$cid);
			$this->assign("flist",$flist);
			
			/*position指定以及一些问候信息*/
			$current = "内容模型修改&nbsp;(&nbsp;".$arr['name']."&nbsp;)";
			$position = getPosition(array('内容模型管理列表'=>'__GROUP__/channel/show','内容模型修改&nbsp;&nbsp;(&nbsp;'.$arr['name'].'&nbsp;)'=>''));
			$this->assign('current',$current);
			$this->assign('position',$position);
			$this->assign('welcome',getWelcome());
			
			
			$this->display();
			
		}
		
		
		
	
	
		public function fieldadd(){
			require(APP_INC_PATH.'form/Zebra_Form.php');
			
			$cid = isset($_GET['cid'])?$_GET['cid']:null;
			if($cid==null){
				$this->error('读取模型出错！');
			}
			$this->assign('cid',$cid);
			
			$m = new ChannelModel();
			$arr = $m->field('nid,fieldset,addtable,name')->where('id='.$cid)->find();
			
			$form = new Zebra_Form('form','post',U('form/fieldsave'));  //参数分别是 表单名称 提交方法 请求页面
			
			//隐藏表单
			$form->add('text', 'cid',$cid,array('type' => 'hidden'));
			
			//字段提示
			$form->add('label', 'label_ftips', 'ftips', '字段提示文字:'); //空间名  id  for属性 里面的话
			$obj = & $form->add('text', 'ftips','');
			$obj->set_rule(array(
					'required' => array('error', '请输入字段提示信息！')
			));
			
			
			//字段名
			$form->add('label', 'label_fname', 'fname', '字段名称:'); //空间名  id  for属性 里面的话
			$obj = & $form->add('text', 'fname','');
			$obj->set_rule(array(
					'required' => array('error', '请输入字段名称!'),
					'alphanumeric' =>array('_','error','命名不合法！'),
					'custom' =>array('isFieldExist','error','该字段不合法 或者 已经存在！')
					));
			$obj = &$form->add('note','note_fname','fname','id,siteid,typeid,typeid2,sortrank,flag,ismake,channel,arcrank,click,,click2,title,shorttitle,color,senddate,editdate,pubdate,source,dutyadmin,status,desc,cdir 请勿使用');
			
			// 字段类型
			$form->add('label', 'label_ftype', 'ftype', '字段类型:');
			$obj = & $form->add('select', 'ftype', 'text');
			$obj->add_options(array(
					'text' =>  '单行文本(varchar)',
					'textchar' =>  '单行文本(char)',
					'multitext' =>  '多行文本',
					'htmltext' =>  'HTML文本',
					'int' =>  '整数类型',
					'float' =>  '小数类型',
					'datetime'=>'时间',
					'img' =>  '图片',
					'media'=> '文件',
					'select' =>  'select下拉框',
					'radio' =>  'radio选项卡',
					'checkbox' =>  'Checkbox多选框',
					'stepselect' =>  '联动类型',
					'region' =>  '地区联动',
					'hidden' =>'隐藏表单'
			
			));
			$obj->set_rule(array(
					'required' => array('error', '请输入字段类型')
			));
			
			//默认长度
			$form->add('label', 'label_fsize', 'fsize', '最大长度:');
			$obj = & $form->add('text', 'fsize','250');
			$obj->set_rule(array(
					//'required' => array('error', '请输入字段默认值!')
			));
			

			$form->add('label', 'label_fdefault', 'fdefault', '值列表:');
			$obj = & $form->add('textarea', 'fdefault','');
			$obj->set_rule(array(
					//'required' => array('error', '请输入字段默认值!')
			));
			

			$form->add('label', 'label_fmust', 'fmust', '必填:');
			$obj = & $form->add('checkboxes', 'fmust',array('1'=>''),0);


            $form->add('label', 'label_fdisplay', 'fdisplay', '列表中显示:');
            $obj = & $form->add('checkboxes', 'fdisplay',array('1'=>''),0);


			// "submit"
			$form->add('submit', 'btnsubmit', '确定');

			
			$rs = $form->render('*horizontal');
			
			
			
			$this->assign('form_html',$rs);
			
			/*position指定以及一些问候信息*/
			$current = "内容模型字段添加&nbsp;(&nbsp;".$arr['name']."&nbsp;)";
			$position = getPosition(array('内容模型管理列表'=>'__GROUP__/channel/show','内容模型字段添加&nbsp;&nbsp;(&nbsp;'.$arr['name'].'&nbsp;)'=>''));
			$this->assign('current',$current);
			$this->assign('position',$position);
			$this->assign('welcome',getWelcome());

			$this->display('form');
		}	
		
		
		public function fieldedit(){
			require(APP_INC_PATH.'form/Zebra_Form.php');
				
			$cid = isset($_GET['cid'])?$_GET['cid']:null;
			$field = isset($_GET['field'])?$_GET['field']:null;
			if($cid==null||$field==null){
				$this->error('读取模型出错！');
			}
			$this->assign('cid',$cid);
			
			$m = new ChannelModel();
			$arr = $m->field('nid,fieldset,addtable')->where('id='.$cid)->find();
			$table = $arr['addtable'];
			$ofield = unserialize($arr['fieldset']);
			//dump($ofield);
			foreach($ofield as $k => $v){
				if($v['name']==$field){
					$ofield = $ofield[$k];
					break;
				}
			}
			
			//$ofield = $ofield[0];
			//dump($ofield);
				
			$form = new Zebra_Form('form','post',U('form/fieldupdate'));  //参数分别是 表单名称 提交方法 请求页面
				
			//隐藏表单
			$form->add('text', 'cid',$cid,array('type' => 'hidden'));
			//隐藏表单
			$form->add('text', 'field',$field,array('type' => 'hidden'));
				
			//字段提示
			$form->add('label', 'label_ftips', 'ftips', '字段提示文字:'); //空间名  id  for属性 里面的话
			$obj = & $form->add('text', 'ftips',$ofield['intro']);
			$obj->set_rule(array(
					'required' => array('error', '请输入字段提示信息！')
			));
				
				
			//字段名
			$form->add('label', 'label_fname', 'fname', '字段名称:'); //空间名  id  for属性 里面的话
			$obj = & $form->add('text', 'fname',$ofield['name'],array('disabled'=>'disabled'));
/* 			$obj->set_rule(array(
					'required' => array('error', '请输入字段名称!'),
					'alphanumeric' =>array('_','error','命名不合法！'),
					'custom' =>array('isFieldExist','error','该字段不合法 或者 已经存在！')
			)); */
				
				
			// 字段类型
			$form->add('label', 'label_ftype', 'ftype', '字段类型:');
			$obj = & $form->add('select', 'ftype', $ofield['type']);
			$obj->add_options(array(
					'text' =>  '单行文本(varchar)',
					'textchar' =>  '单行文本(char)',
					'multitext' =>  '多行文本',
					'htmltext' =>  'HTML文本',
					'int' =>  '整数类型',
					'float' =>  '小数类型',
					'datetime'=>'时间',
					'img' =>  '图片',
					'media'=> '文件',
					'select' =>  'option下拉框',
					'radio' =>  'radio选项卡',
					'checkbox' =>  'Checkbox多选框',
					'stepselect' =>  '联动类型',
					'region' =>  '地区联动',
					'hidden' =>'隐藏表单'
						
			));
			$obj->set_rule(array(
					'required' => array('error', '请输入字段类型')
			));
				
			//默认长度
			$form->add('label', 'label_fsize', 'fsize', '最大长度:'); //空间名  id  for属性 里面的话
			$obj = & $form->add('text', 'fsize',$ofield['size']);
			$obj->set_rule(array(
					//'required' => array('error', '请输入字段默认值!')
			));
				
			//默认值
			$form->add('label', 'label_fdefault', 'fdefault', '值列表:'); //空间名  id  for属性 里面的话
			$obj = & $form->add('textarea', 'fdefault',$ofield['default']);
			$obj->set_rule(array(
					//'required' => array('error', '请输入字段默认值!')
			));
			
			$form->add('label', 'label_fmust', 'fmust', '必填:'); //空间名  id  for属性 里面的话
			$obj = & $form->add('checkboxes', 'fmust',array('1'=>''),$ofield['must']);


            $form->add('label', 'label_fdisplay', 'fdisplay', '列表中显示:');
            $obj = & $form->add('checkboxes', 'fdisplay',array('1'=>''),$ofield['display']);
				
				
				
			// "submit"
			$form->add('submit', 'btnsubmit', '确定');
			//$form->add('reset', 'my_reset', '重置');
			// if the form is valid
			if ($form->validate()) {

			} else
					
				$rs = $form->render('*horizontal');
				$this->assign('form_html',$rs);
				$this->display('form');
			
			
		}
		
		

		public function fielddel(){
			$m = new ChannelModel();
			$cid = isset($_GET['cid'])?$_GET['cid']:null;
			$field = isset($_GET['field'])?$_GET['field']:null;
			if($cid==null||$field==nul){
				$this->error("读取字段错误！");return;
			}
		
			$arr = $m->field('nid,fieldset,addtable')->where('id='.$cid)->find();
			if($field =='id'||$field =='aid'){
				$this->error("系统模型不能删除！");
				return;
			}
			$table = C('DB_PREFIX').$arr['addtable'];
			$tablesql = "ALTER TABLE `$table` DROP `$field` ";
			$m2 = M();
			if($m2->execute($tablesql)!==false){
		
				$ofield = unserialize($arr['fieldset']);
				foreach($ofield as $k => $v){
					if($v['name']==$field){
						unset($ofield[$k]);
						//$ofield[$k] = null;
						break;
					}
				}
				$field_str = serialize($ofield);
				$data['id'] = $cid;
				$data['fieldset'] = $field_str;
				$m->create($data);
				if($m->save()){
					$this->success("删除字段成功！");
				}else{
					$this->error("删除字段失败！");
				}
		
		
			}else{
				$this->error("删除字段结构失败！");
			}
		
			//dump($arr);exit;
		
		}
		
		
		
		
		
		

}


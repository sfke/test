<?php

	/**
	 * 后台导航管理
	 * @author Administrator
	 *
	 */
	class NavAction extends BaseAction{
		

			/**
			 * 导航管理
			 */
			public function navmanage(){

				$m = M('admintopnav');
				$topnav = $m->order('`order` asc')->select();
				//$topnav_str = '';
                $topnav_str2 = '';
				//$j=1;
				foreach($topnav as $k => $v){
						
/*					if($v['issystem']==0){
						$topnav_str.='<li><a href="#tabs-'.$j.'" _id="'.$v['id'].'" class = "navtop">'.$v['name'].'</a></li>';
						$toparr[$j] = $v['id'];
						$j++;
					}*/

                    switch($v['display']){
                        case 0 : $color = "color:#FF00AE;";break;
                        case 1 : $color = "color:#608705;";break;
                        case 2 : $color = "color:#FF7200;";break;

                    }

					$topnav_str2 .= '<li style="'.$color.'" class="ui-state-default" _id="'.$v['id'].'" >'.$v['name'].'&nbsp;&nbsp;&nbsp;<span class="controls" ><a  href="'.U('nav/navedit').'?id='.$v['id'].'" >修改</a><a  href="javascript:openpanel(\'navdel\','.$v[id].')" >删除</a></span></li>';
						
				}
				
				$this->assign('topnav2',$topnav_str2);
				//$this->assign('topnav',$topnav_str);
				
				/*position指定以及一些问候信息*/
				$current = "导航管理";
				$position = getPosition("导航管理");
				$this->assign('current',$current);
				$this->assign('position',$position);
				$this->assign('welcome',getWelcome());
				
				$this->display();
				
			}
			
			
			/**
			 * 功能模块管理
			 */
			public function modulemanage(){
 				$m = M('admintopnav');
				$topnav = $m->order('`order` asc')->select();
				$m2 = M('adminmodule');
				$module = $m2->order('`order` asc')->select();
				$topnav_str = '';
				$j=1;
                $topnav_str2 = "";
				foreach($topnav as $k => $v){

                    switch($v['display']){
                        case 0 : $color = "color:#FF00AE;";break;
                        case 1 : $color = "color:#608705;";break;
                        case 2 : $color = "color:#FF7200;";break;
                    }

					//TODO 待优化
					if(true || $v['issystem']==1){
						$topnav_str.='<li><a href="#tabs-'.$j.'" _id="'.$v['id'].'" class = "navtop"><span class="nav_top_span" style='.$color.'  >'.$v['name'].'</a></span></li>';
						$toparr[$j] = $v['id'];
						$j++;
					}
					//$topnav_str2 .= '<li class="ui-state-default" _id="'.$v['id'].'" >'.$v['name'].'&nbsp;&nbsp;&nbsp;<a  href="'.U('nav/navedit').'?id='.$v['id'].'" >修改</a><a  href="'.U('nav/navdel').'?id='.$v['id'].'" >删除</a></li>';
					
				}
				
				$module_str = '';
				//dump($toparr);
				//dump($module);
				foreach($toparr as $k => $v){
					$module_str.='<div id="tabs-'.$k.'"><ul id="sortable'.$k.'" class="cando ui-helper-reset">';
						foreach($module as $moudle){
							if($moudle['fid']==$v){
                                if ($moudle['ishidden']) {
                                    $color = "color:#0006FF;";
                                } else {
                                    switch ($moudle['issystem']) {
                                        case 0 :
                                            $color = "color:#FF00AE;";
                                            break;
                                        case 1 :
                                            $color = "color:#608705;";
                                            break;
                                        case 2 :
                                            $color = "color:#FF7200;";
                                            break;
                                    }
                                }

                                $module_str .='<li class="ui-state-default" _id="'.$moudle['id'].'" style="'.$color.'" >'.$moudle['name'].'&nbsp;&nbsp;&nbsp;<span class="controls"><a  href="'.U('nav/moduleedit').'?id='.$moudle['id'].'" >修改</a><a  href="javascript:openpanel(\'moduledel\','.$moudle[id].')" >删除</a></span></li>';
							}
						}
					$module_str.='</ul></div>';
				}
				
				$this->assign('topnav2',$topnav_str2);
				$this->assign('topnav',$topnav_str);
				$this->assign('module',$module_str); 
				
				/*position指定以及一些问候信息*/
				$current = "功能模块管理";
				$position = getPosition("功能模块管理");
				$this->assign('current',$current);
				$this->assign('position',$position);
				$this->assign('welcome',getWelcome());
				
				
				$this->display();
				
			}
			
			
			
			
			
			public function navadd(){
				
				require(APP_INC_PATH.'form/Zebra_Form.php');
						
				$form = new Zebra_Form('form','post',U('form/navsave'));
					
				$form->add('label', 'label_name', 'name', '导航名称:');
				$obj = & $form->add('text', 'name', '',array('style'=>'width:150px;'));
				$obj->set_rule(array(
						'required' => array('error', '必须设定模型名称!')
				));
			
						
				$form->add('label', 'label_url', 'url', '外链URL:');
				$form->add('text', 'url', '',array('style'=>'width:400px;'));

				
				
				$form->add('label', 'label_display', 'display', '可见性:');
				$obj = & $form->add('radios', 'display', array(
                    '1' =>  '全可见',
                    '2' =>  '仅超级管理员可见',
                    '0' =>  '全不可见'
				),'1');
				$obj->set_rule(array(
						'required' => array('error', '可见性需要填写！')
				));

                $form->add('label', 'label_issystem', 'issystem', '写入权限表:');
                $obj = & $form->add('radios', 'issystem', array(
                    '1' =>  '写入',
                    '0' =>  '不写入'
                ),$arr['issystem']);
                $obj->set_rule(array(
                    'required' => array('error', '该项必须选择！')
                ));
				
				$form->add('submit', 'btnsubmit', '确定');
				$html_str = $form->render('*horizontal');
				
				$this->assign('form_html',$html_str);
				
				/*position指定以及一些问候信息*/
				$current = "导航添加";
				$position = getPosition(array('导航管理'=>'__GROUP__/nav/navmanage','导航添加'=>''));
				$this->assign('current',$current);
				$this->assign('position',$position);
				$this->assign('welcome',getWelcome());
				
				
				$this->display('nav:form');
			}
			
			public function navedit(){
				
				$id = isset($_GET['id'])?$_GET['id']:null;
				if($id === null){
					$this->error("获取导航id失败！");
				}
				
				$m = M('admintopnav');
				$arr = $m->where('id='.$id)->find();
				
			
				require(APP_INC_PATH.'form/Zebra_Form.php');
			
				$form = new Zebra_Form('form','post',U('form/navupdate'));
					
				//隐藏表单
				$obj = & $form->add('text', 'id',$id,array('type' => 'hidden'));
				
				$form->add('label', 'label_name', 'name', '导航名称:');
				$obj = & $form->add('text', 'name', $arr['name'],array('style'=>'width:150px;'));
				$obj->set_rule(array(
						'required' => array('error', '必须设定模型名称!')
				));
					

                /*
				$form->add('label', 'label_url', 'url', '外链URL:');
				$form->add('text', 'url', $arr['url'],array('style'=>'width:400px;'));
                */
			
			
				$form->add('label', 'label_display', 'display', '可见性:');
				$obj = & $form->add('radios', 'display', array(
						'1' =>  '全可见',
                        '2' =>  '仅超级管理员可见',
						'0' =>  '全不可见'
				),$arr['display']);
				$obj->set_rule(array(
						'required' => array('error', '该项必须选择！')
				));

                $form->add('label', 'label_issystem', 'issystem', '写入权限表:');
                $obj = & $form->add('radios', 'issystem', array(
                    '1' =>  '写入',
                    '0' =>  '不写入'
                ),$arr['issystem']);
                $obj->set_rule(array(
                    'required' => array('error', '该项必须选择！')
                ));
			
			
				$form->add('submit', 'btnsubmit', '确定');
				$html_str = $form->render('*horizontal');
			
				$this->assign('form_html',$html_str);
				
				/*position指定以及一些问候信息*/
				$current = "导航修改";
				$position = getPosition(array('导航管理'=>'__GROUP__/nav/navmanage','导航修改'=>''));
				$this->assign('current',$current);
				$this->assign('position',$position);
				$this->assign('welcome',getWelcome());
				
				$this->display('nav:form');
			}
				

			

			
			public function navdel(){
				$id = isset($_GET['id'])?$_GET['id']:null;
				if($id === null){
					$this->error("获取导航id失败！");
				}
				$m = M('admintopnav');
				$m2 = M('adminmodule');
				$arr = $m2->where('fid='.$id)->select();
				if(!empty($arr)){
					$this->error("该导航下还有功能模块！不能删除！");	
					return;
				}
				
				if($m->where('id='.$id)->delete()!==false){
					$this->success("删除一个导航成功！","__GROUP__/nav/navmanage");
				}else{
					$this->error("删除一个导航失败！");
				}
					
			}
			
			
			public function moduledel(){
				$id = isset($_GET['id'])?$_GET['id']:null;
				if($id === null){
					$this->error("获取导航id失败！");
				}

				$m = M('adminmodule');
				if($m->where('id='.$id)->delete()!==false){
					$this->success("删除一个功能模块成功！","__GROUP__/nav/modulemanage");
				}else{
					$this->error("删除一个功能模块！");
				}
				
				
				
				
				
			}
			
			
			
			
			public function moduleadd(){
			
				require(APP_INC_PATH.'form/Zebra_Form.php');

                $nid = $this->_param("nid");
					
				//die(LIB_PATH.GROUP_NAME);
				$modules = array();
				search(LIB_PATH.'Action/'.GROUP_NAME,'/^\w+Action.class.php$/','php',$modules);
				$moduleArr = array();
				$besides = C('SYS_MODULE_BESIDES');
				foreach($modules as $v){
					$fname = preg_match('#(\w+)Action.class.php#', $v['name'],$arr);
					if(!in_array($arr[1], $besides))$moduleArr[$arr[1]] = $arr[1];
				}
				
				$m = M('admintopnav');
				$navs = $m->select();
				foreach($navs as $v){
					$navArr[$v['id']] = $v['name'];	
				}

			
				$form = new Zebra_Form('form','post',U('form/modulesave'));
					
				$form->add('label', 'label_name', 'name', '功能名称:');
				$obj = & $form->add('text', 'name', '',array('style'=>'width:150px;'));
				$obj->set_rule(array(
						'required' => array('error', '必须设定模型名称!')
				));
					
			
				$form->add('label', 'label_fid', 'fid', '所属导航:');
				$obj = & $form->add('select', 'fid', $nid);
				$obj->add_options($navArr);
				$obj->set_rule(array(
						'required' => array('error', '该处必须选择！')
				));
				
				
				
				$form->add('label', 'label_module', 'module', '模块选择:');
				$obj = & $form->add('select', 'module', 'varchar');
				$obj->add_options($moduleArr);
 				$obj->set_rule(array(
						'required' => array('error', '请输入字段类型')
				));
			
			
				$form->add('label', 'label_action', 'action', '操作选择:');
				$obj = & $form->add('select', 'action', 'varchar');
				$obj->add_options(array());
 				$obj->set_rule(array(
						'required' => array('error', '请输入字段类型')
				));

                /*
				$form->add('label', 'label_url', 'url', '外链URL:');
				$form->add('text', 'url', $arr['url'],array('style'=>'width:400px;'));
			    */

				//是否显示
				$form->add('label', 'label_issystem', 'issystem', '可见性:');
				$obj = & $form->add('radios', 'issystem', array(
                    '1' =>  '全可见',
                    '2' =>  '仅超级管理员可见',
                    '0' =>  '全不可见'
				),'1');
				$obj->set_rule(array(
						'required' => array('error', '必须选择一个')
				));
			
		
				$form->add('submit', 'btnsubmit', '确定');
				$html_str = $form->render('*horizontal');
			
				$this->assign('form_html',$html_str);
				
				/*position指定以及一些问候信息*/
				$current = "功能模块添加";
				$position = getPosition(array('功能模块管理'=>'__GROUP__/nav/modulemanage','功能模块添加'=>''));
				$this->assign('current',$current);
				$this->assign('position',$position);
				$this->assign('welcome',getWelcome());
				
				$this->display("nav:form");
			}
			
			
			public function moduleedit(){
					
				require(APP_INC_PATH.'form/Zebra_Form.php');
					
				$id = isset($_GET['id'])?$_GET['id']:null;
				if($id === null){
					$this->error("获取操作id失败！");
				}
				
				
				$modules = array();
				$besides = C('SYS_MODULE_BESIDES');
				search(LIB_PATH.'Action/'.GROUP_NAME,'/^\w+Action.class.php$/','php',$modules);
				$moduleArr = array();
				foreach($modules as $v){
					$fname = preg_match('#(\w+)Action.class.php#', $v['name'],$arr);
					if(!in_array($arr[1], $besides))$moduleArr[$arr[1]] = $arr[1];
				}
				
				$m = M('admintopnav');
				$navs = $m->select();
				foreach($navs as $v){
					$navArr[$v['id']] = $v['name'];
				}
				
				
				$m2 = M("adminmodule");
				$actionArr = $m2->where('id='.$id)->find();
				
				$base = get_class_methods('Action');
                unset($base[array_search("show",$base)]);
				$classname = $actionArr['module']."Action";
				$arr = get_class_methods($classname);
				$actions = array_diff($arr,$base);
				
				$besides = C('SYS_ACTION_BESIDES');
				foreach($actions as $v){
					if(!in_array($v, $besides))
					$actionsArr[$v] = $v;
				}
				
				
				
				
				//die(LIB_PATH.GROUP_NAME);
				$modules = array();
				search(LIB_PATH.'Action/'.GROUP_NAME,'/^\w+Action.class.php$/','php',$modules);
				$moduleArr = array();
				$besides = C('SYS_MODULE_BESIDES');
				foreach($modules as $v){
					$fname = preg_match('#(\w+)Action.class.php#', $v['name'],$arr);
					if(!in_array($arr[1], $besides))$moduleArr[$arr[1]] = $arr[1];
				}

				$form = new Zebra_Form('form','post',U('form/moduleupdate'));
				
				//隐藏表单
				$form->add('text', 'id',$id,array('type' => 'hidden'));
					
				$form->add('label', 'label_name', 'name', '功能名称:');
				$obj = & $form->add('text', 'name', $actionArr['name'],array('style'=>'width:150px;'));
				$obj->set_rule(array(
						'required' => array('error', '必须设定模型名称!')
				));

				$form->add('label', 'label_fid', 'fid', '所属导航:');
				$obj = & $form->add('select', 'fid', $actionArr['fid']);
				$obj->add_options($navArr);
				$obj->set_rule(array(
						'required' => array('error', '该处必须选择！')
				));
					
				$form->add('label', 'label_module', 'module', '模块选择:');
				$obj = & $form->add('select', 'module', $actionArr['module']);
				$obj->add_options($moduleArr);
 				$obj->set_rule(array(
						'required' => array('error', '请输入字段类型')
				));
					
					
				$form->add('label', 'label_action', 'action', '操作选择:');
				$obj = & $form->add('select', 'action', $actionArr['action']);
				$obj->add_options($actionsArr);
 				$obj->set_rule(array(
						'required' => array('error', '请输入字段类型')
				));
					
					
				$form->add('label', 'label_url', 'url', '外链URL:');
				$form->add('text', 'url', $actionArr['url'],array('style'=>'width:400px;'));
					
				//是否显示
				$form->add('label', 'label_issystem', 'issystem', '可见性:');
				$obj = & $form->add('radios', 'issystem', array(
                    '1' =>  '全可见',
                    '2' =>  '仅超级管理员可见',
                    '0' =>  '全不可见'
				),$actionArr['issystem']);
				$obj->set_rule(array(
						'required' => array('error', '必须选择一个')
				));

                //隐含功能
                $form->add('label', 'label_ishidden', 'ishidden', '隐含功能:');
                $obj = & $form->add('radios', 'ishidden', array(
                    '1' => '是',
                    '0' => '否',
                ), $actionArr['ishidden']);
                $obj->set_rule(array(
                    'required' => array('error', '必须选择一个')
                ));

                $form->add('submit', 'btnsubmit', '确定');
				$html_str = $form->render('*horizontal');
					
				$this->assign('form_html',$html_str);
				/*position指定以及一些问候信息*/
				$current = "功能模块修改";
				$position = getPosition(array('功能模块管理'=>'__GROUP__/nav/modulemanage','功能模块修改'=>''));
				$this->assign('current',$current);
				$this->assign('position',$position);
				$this->assign('welcome',getWelcome());
				
				
				$this->display("nav:form");
			}
			
			

			
	

			
		

		
	}






?>
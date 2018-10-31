<?php

/**
 * 角色管理
 * @author Administrator
 *
 */
	class RoleAction extends BaseAction{
		

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
					
			}
			//排序
			$map_orderby = !empty($map_orderby)?$map_orderby:'id desc';
				
				
			/*select准备*/
			$arrOrderby = array('id_desc'=>'ID 降序','id_asc'=>'ID 升序','status_desc'=>'启用在前','status_asc'=>'禁用在前');
			$orderby_html = getOptions($arrOrderby,$_POST['orderby']);
			$arrSearchby = array('name'=>'用户组名','id'=>'用户组ID');
			$searchby_html = getOptions($arrSearchby,$_POST['searchby']);
				
			$this->assign('orderby_html',$orderby_html);
			$this->assign('searchby_html',$searchby_html);
				
				
			$m2 = M('role');
			$roleArr = $m2->where($map)->order($map_orderby)->select();
			$this->assign('roleArr',$roleArr);
			/*position指定以及一些问候信息*/
			$current = "用户组管理列表";
			$position = getPosition("用户组管理列表");
			$this->assign('current',$current);
			$this->assign('position',$position);
			$this->assign('welcome',getWelcome());
				
			$this->display();
			
			
		}
		
		
		/**
		 * 添加新的角色
		 */
		public function add(){
			
			
			$m = M('node');
			
			$rootArr = $m->where("name='".APP_NAME."'")->find();
			if(empty($rootArr)){
				$this->error("权限表为空！请先同步权限表！");
				return;
			}
			$rootid = $rootArr['id'];
			
			$level2 = $m->where("pid=".$rootid)->select();
			
			require(APP_INC_PATH.'form/Zebra_Form.php');
			
			$form = new Zebra_Form('form','post',U('form/rolesave'));
			
			$form->add('text', 'rootid',$rootid,array('type' => 'hidden'));
				
			$form->add('label', 'label_name', 'name', '用户组名称:',array('style'=>'width:80px;'));
			$obj = & $form->add('text', 'name', '',array('style'=>'width:150px;'));
			$obj->set_rule(array(
					'required' => array('error', '必须设定模型名称!')
			));
			
			//dump($level2);exit;
			foreach($level2  as $v){
				
				$arr3 = $m->where("pid=".$v['id'])->select();
				
				$level3 = array();
				foreach($arr3 as $v3){
					$level3[$v3['id']] = $v3['title'];
				}
				
				$form->add('label', 'label_'.$v['name'], $v['name'], $v['title'].':',array("onclick"=>"selectToggle('".$v['name']."[]')"));
				$form->add('checkboxes', $v['name'].'[]', $level3);
				
			}
			
			$form->add('label', 'label_status', 'status', '是否启用:');
			$obj = & $form->add('radios', 'status', array(
					'1' =>  '启用',
					'0' =>  '禁用'
			),'1');
			$obj->set_rule(array(
					'required' => array('error', '必须选择!')
			));
			
			
			$form->add('submit', 'btnsubmit', '确定');
			$html_str = $form->render('*horizontal');
			$this->assign('form_html',$html_str);		

			/*position指定以及一些问候信息*/
			$current = "用户组添加";
			$position = getPosition(array('用户组管理列表'=>'__GROUP__/role/show','用户组添加'=>''));
			$this->assign('current',$current);
			$this->assign('position',$position);
			$this->assign('welcome',getWelcome());
			
			
			
			$this->display('role:form');
		}
		

		
		
		/**
		 * 编辑已有的角色
		 */
		public function edit(){
			
			$id = isset($_GET['id'])?$_GET['id']:null;
			if($id === null){
				$this->error("读取角色id失败！");
				return;
			}
			
			$m2 = M('access');
			$map['role_id'] = array('eq',$id);
			$map['level'] = array('neq',1);
			$access = $m2->where($map)->select();
			//dump($access);exit;
			$m3 = M('role');
			$role = $m3->where('id='.$id)->find();
			
			
			
			$m = M('node');
				
			$rootArr = $m->where("name='".APP_NAME."'")->find();
			if(empty($rootArr)){
				$this->error("权限表为空！请先同步权限表！");
				return;
			}
			$rootid = $rootArr['id'];
				
			$level2 = $m->where("pid=".$rootid)->select();
				
			require(APP_INC_PATH.'form/Zebra_Form.php');
				
			$form = new Zebra_Form('form','post',U('form/roleupdate'));
				
			$form->add('text', 'rootid',$rootid,array('type' => 'hidden'));
			$form->add('text', 'id',$id,array('type' => 'hidden'));
			
			$form->add('label', 'label_name', 'name', '用户组名称:',array('style'=>'width:80px;'));
			$obj = & $form->add('text', 'name', $role['name']);
			$obj->set_rule(array(
					'required' => array('error', '必须设定模型名称!')
			));
				
			//dump($level2);exit;
			foreach($level2  as $v){
			
				$arr3 = $m->where("pid=".$v['id'])->select();
				
				
				$oldaccess = array();
				foreach($access as $old){
					if($old['pid']==$v['id']){
						$oldaccess[] = $old['node_id'];
					}
				}
			
				$level3 = array();
				foreach($arr3 as $v3){
					$level3[$v3['id']] = $v3['title'];
				}
			
				$form->add('label', 'label_'.$v['name'], $v['name'], $v['title'].':',array("onclick"=>"selectToggle('".$v['name']."[]')"));
				$form->add('checkboxes', $v['name'].'[]', $level3,$oldaccess);
			
			}
				
			$form->add('label', 'label_status', 'status', '是否启用:');
			$obj = & $form->add('radios', 'status', array(
					'1' =>  '启用',
					'0' =>  '禁用'
			),$role['status']);
			$obj->set_rule(array(
					'required' => array('error', '必须选择!')
			));
				
				
			$form->add('submit', 'btnsubmit', '确定');
			$html_str = $form->render('*horizontal');
				
			$this->assign('form_html',$html_str);
			
			/*position指定以及一些问候信息*/
			$current = "用户组修改";
			$position = getPosition(array('用户组管理列表'=>'__GROUP__/role/show','用户组修改'=>''));
			$this->assign('current',$current);
			$this->assign('position',$position);
			$this->assign('welcome',getWelcome());
			
			
			
			$this->display('role:form');
			
			
			
			
		}

        /**
         * 自动将已有的功能模块同步到权限控制表
         */
        public function autonode(){

            $clearM = M();
            $table = C('DB_PREFIX')."node";

            $node  = C('DB_PREFIX')."node";
            $access = C('DB_PREFIX')."access";
            //select node.id,node.title,node1.name as model,node.name as action from jl_node as node1, (select id,title,name,jl_node.pid from jl_node,jl_access where jl_access.node_id = jl_node.id ) as node where node.pid = node1.id;
            $oldAccess = $clearM->query("select node.id,node.title,node1.name as model,node.name as action,node.roleid from $node as node1, (select t_node.id,t_node.title,t_node.name,t_node.pid,t_access.role_id as roleid from $node as t_node,$access as t_access where t_access.node_id = t_node.id ) as node where node.pid = node1.id");

            //dump($oldAccess);



            $sql = "truncate table ".$table;
            if($clearM->execute($sql)===false){
                echo -1;
                return ;
            }
            $table = C('DB_PREFIX')."access";
            $sql = "truncate table ".$table;
            if($clearM->execute($sql)===false){
                echo -1;
                return ;
            }


            $mNav = M('admintopnav');
            $navArr = $mNav->field('id')->where('issystem = 1')->select();
            $navarr = array();
            foreach($navArr as $v){
                $navarr[] = $v['id'];
            }
            $navStr = implode(',', $navarr);


            $m = M('adminmodule');
            $map['fid'] = array('in',$navStr);
            $map['issystem'] = array('eq',1);
            $arr = $m->where($map)->select();
            $moduleArr = array();
            foreach($arr as $v){
                $moduleArr[$v['module']][] = $v;
            }


            $newAccess = array();


            $m2 = M('node');
            $arr = $m2->where("name='".APP_NAME."'")->find();
            if(!empty($arr)){
                $appid = $arr['id'];
            }else{
                $data['name'] = APP_NAME;
                $data['title'] = "项目_".APP_NAME;
                $data['status'] = 1;
                $data['pid'] = 0;
                $data['level'] = 1;

                $m2->create($data);
                if($m2->add()===false){
                    //$this->error("建立权限根节点失败！操作中断！");
                    echo -1;
                    return ;
                }
                $appid = $m2->getLastInsID();
            }


            $arr =  $m2->where('pid='.$appid)->select();
            $oldmodule = array();
            if(!empty($arr)){

                foreach($arr as $v){
                    $oldmodule[] = $v['name'];
                }


            }


            foreach($moduleArr as $k => $v){
                if(!in_array($k,$oldmodule)){
                    $data['name'] = $k;
                    $data['title'] = $k."模块";
                    $data['status'] = 1;
                    $data['pid'] = $appid;
                    $data['level'] = 2;
                    $m2->create($data);
                    if($m2->add()===false){
                        //$this->error("建立权限二级节点失败！操作中断！");
                        echo -1;
                        return ;
                    }
                    $thisid = $m2->getLastInsID();

                }else{
                    $themodule = $m2->where("name='".$k."'")->find();
                    $thisid = $themodule['id'];
                }

                $_t = array();
                $key = strtolower(APP_NAME."-".$k);
                $_t['model'] = APP_NAME;
                $_t['pid'] = $appid;
                $_t['level'] = 2;
                $_t['nodeid'] = $thisid;
                $newAccess[$key][] = $_t;



                $arr2 =  $m2->where('pid='.$thisid)->select();

                $oldmodule2 = array();
                if(!empty($arr2)){

                    foreach($arr2 as $v3){
                        $oldmodule2[] = $v3['name'];
                    }


                }


                foreach($v as $v2){

                    if(!in_array($v2['action'],$oldmodule2)){
                        $data['name'] = $v2['action'];
                        $data['title'] = $v2['name'];
                        $data['status'] = 1;
                        $data['pid'] = $thisid;
                        $data['level'] = 3;
                        $m2->create($data);
                        if($m2->add()===false){
                            //$this->error("建立权限三级节点失败！操作中断！");
                            echo -1;
                            return ;
                        }else{
                            $nodeid =  $m2->getLastInsID();
                        }
                    }

                    $_t = array();
                    $key = strtolower($v2['module']."-".$v2['action']);
                    $_t['level'] = 3;
                    $_t['pid'] = $thisid;
                    $_t['nodeid'] = $nodeid;
                    $newAccess[$key][] = $_t;


                }

            }


            $sql = "insert into $access (`role_id`,`node_id`,`level`,`pid`,`module`) values";
            foreach($oldAccess as $k=>$v){
                $key = strtolower($v['model']."-".$v['action']);

                if(array_key_exists($key,$newAccess)){
                    if(is_array($newAccess[$key])){

                        foreach($newAccess[$key] as $v2){
                            $sql.="(".$v['roleid'].",".$v2['nodeid'].",".$v2['level'].",".$v2['pid'].",''),";
                        }


                    }else{

                        $sql.="(".$v['roleid'].",".$newAccess[$key]['nodeid'].",".$newAccess[$key]['level'].",".$newAccess[$key]['pid'].",''),";

                    }


                }

            }


            $adminM = M("Role");
            $adminArr = $adminM->field("id")->select();
            foreach($adminArr as $v){
                $sql.="(".$v['id'].",".$appid.",1,0,''),";

            }

            $sql = rtrim($sql,",");
            $clearM->query($sql);

            echo 1;
            return;
        }

}
			
			
			
			
			
			
			
			
			
			
			
			
			
			
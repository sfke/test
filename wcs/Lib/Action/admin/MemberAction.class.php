<?php

	/**
	 * 后台会员管理
	 * @author Administrator
	 *
	 */
	class MemberAction extends BaseAction{
		
		/**
		 * 后台列表修改
		 * @see Action::show()
		 */
		public function show(){
			
			$type = isset($_GET['type'])?$_GET['type']:null;
			if($type === null){
				$this->error("读取会员类型错误！");
			}
			
			
			$map['usertype'] = array('eq',$type);
			import('ORG.Util.Page');
			if(!empty($_POST['action'])){
				if($_POST['action'] == 'filter'){
					if(!empty($_POST['orderby'])){
						$arr = orderByParse($_POST['orderby']);
						if(is_array($arr)){
							$map_orderby = "`$arr[0]` $arr[1]";
						}
						$orderby = $_POST['orderby'];
						setcookie("orderby_user", $_POST['orderby']);
						setcookie("map_orderby_user", $map_orderby);
						
					}
			
					if(!empty($_POST['searchkey'])){
						$this->assign('searchkey',$_POST['searchkey']);
						$map[$_POST['searchby']] = array('like','%'.$_POST['searchkey'].'%');
					}
				}
			}else{
				
				if(!empty($_COOKIE['map_orderby_user'])){
				
					$map_orderby = $_COOKIE['map_orderby_user'];
					$orderby = $_COOKIE['orderby_user'];
				}
				
				
				
			}
			
			//$GLOBALS['page_params'] = "?type=$type";
			
			//排序
			$map_orderby = !empty($map_orderby)?$map_orderby:'id desc';
						
			$m = M('member');

			$count = $m->where($map)->count();// 查询满足要求的总记录数
			//echo $m->getLastSql();
			$Page  = new Page($count,C('SYS_PAGE_SIZE'),'type='.$type);// 实例化分页类 传入总记录数和每页显示的记录数
			$show  = $Page->show();// 分页显示输出
			
			//如果是搜索，则在一页内显示所有数据(不分页)
			if(!empty($map[$_POST['searchby']])) {
				$Page->listRows = 1000;
				$show = "一共搜索到 ".$count." 条数据";
			}
			
			$list = $m->where($map)->limit($Page->firstRow.','.$Page->listRows)->order($map_orderby)->select();
			$this->assign('list',$list);// 赋值数据集
			$this->assign('page',$show);// 赋值分页输出
			
			
			if($type == 1){
				$tpl = 'member:show1';
				/*select准备*/
				$arrOrderby = array('id_desc'=>'ID 降序','id_asc'=>'ID 升序','userid_desc'=>'会员ID 降序','userid_asc'=>'会员ID 升序','age_desc'=>'年龄 降序','age_asc'=>'年龄 升序','ispay_desc'=>'已付款优先','ispay_asc'=>'未付款优先');
				$orderby_html = getOptions($arrOrderby,$orderby);
				$arrSearchby = array('userid'=>'会员ID','uname'=>'真实姓名','phone'=>'电话','age'=>'年龄');
				$searchby_html = getOptions($arrSearchby,$_POST['searchby']);
				$this->assign('orderby_html',$orderby_html);
				$this->assign('searchby_html',$searchby_html);
				
				/*position指定以及一些问候信息*/
				$current = "个人会员管理列表";
				$position = getPosition($current);
				$this->assign('current',$current);
				$this->assign('position',$position);
				$this->assign('welcome',getWelcome());
				
			}else if($type == 2){
				$tpl = 'member:show2';
				/*select准备*/
				$arrOrderby = array('id_desc'=>'ID 降序','id_asc'=>'ID 升序','userid_desc'=>'企业ID 降序','userid_asc'=>'企业ID 升序');
				$orderby_html = getOptions($arrOrderby,$orderby);
				$arrSearchby = array('userid'=>'企业ID','uname'=>'企业名称','phone'=>'企业电话','cname'=>'联系人','cphone'=>'联系人电话');
				$searchby_html = getOptions($arrSearchby,$_POST['searchby']);
				$this->assign('orderby_html',$orderby_html);
				$this->assign('searchby_html',$searchby_html);
				
				/*position指定以及一些问候信息*/
				$current = "企业会员管理列表";
				$position = getPosition("企业会员管理列表");
				$this->assign('current',$current);
				$this->assign('position',$position);
				$this->assign('welcome',getWelcome());
				
			}else{
				$this->error("会员类型错误！");
			}
			
			$this->display($tpl);
		}
		
		
		
		
		
		
		public function showson(){
				
			$type = isset($_GET['type'])?$_GET['type']:null;
			$fid = isset($_GET['fid'])?$_GET['fid']:null;
			if($type === null || $fid === null){
				$this->error("读取会员类型错误！");
			}
				
				
			$map['usertype'] = array('eq',$type);
			$map['fid'] = array('eq',$fid);
			
			import('ORG.Util.Page');
			if(!empty($_POST['action'])){
				if($_POST['action'] == 'filter'){
					if(!empty($_POST['orderby'])){
						$arr = orderByParse($_POST['orderby']);
						if(is_array($arr)){
							$map_orderby = "`$arr[0]` $arr[1]";
						}
						$orderby = $_POST['orderby'];
						setcookie("orderby_user", $_POST['orderby']);
						setcookie("map_orderby_user", $map_orderby);
		
					}
						
					if(!empty($_POST['searchkey'])){
						$this->assign('searchkey',$_POST['searchkey']);
						$map[$_POST['searchby']] = array('like','%'.$_POST['searchkey'].'%');
					}
				}
			}else{
		
				if(!empty($_COOKIE['map_orderby_user'])){
		
					$map_orderby = $_COOKIE['map_orderby_user'];
					$orderby = $_COOKIE['orderby_user'];
				}
		
		
		
			}
				
			//$GLOBALS['page_params'] = "?type=$type";
				
			//排序
			$map_orderby = !empty($map_orderby)?$map_orderby:'id desc';
		
			$m = M('member');
			
			$fArr=$m->where('id='.$fid)->find();
			if(empty($fArr)){
				$this->error("该企业会员不存在!");
				return;
			}
			
			
		
			$count = $m->where($map)->count();// 查询满足要求的总记录数
			//echo $m->getLastSql();
			$Page  = new Page($count,C('SYS_PAGE_SIZE'),'type='.$type.'&fid='.$fid);// 实例化分页类 传入总记录数和每页显示的记录数
			$show  = $Page->show();// 分页显示输出
				
			//如果是搜索，则在一页内显示所有数据(不分页)
			if(!empty($map[$_POST['searchby']])) {
				$Page->listRows = 1000;
				$show = "一共搜索到 ".$count." 条数据";
			}
				
			$list = $m->where($map)->limit($Page->firstRow.','.$Page->listRows)->order($map_orderby)->select();
			$this->assign('list',$list);// 赋值数据集
			$this->assign('page',$show);// 赋值分页输出
				
				
			if($type == 1){
				$tpl = 'member:show3';
				/*select准备*/
				$arrOrderby = array('id_desc'=>'ID 降序','id_asc'=>'ID 升序','userid_desc'=>'会员ID 降序','userid_asc'=>'会员ID 升序','age_desc'=>'年龄 降序','age_asc'=>'年龄 升序','ispay_desc'=>'已付款优先','ispay_asc'=>'未付款优先');
				$orderby_html = getOptions($arrOrderby,$orderby);
				$arrSearchby = array('userid'=>'会员ID','uname'=>'真实姓名','phone'=>'电话','age'=>'年龄');
				$searchby_html = getOptions($arrSearchby,$_POST['searchby']);
				$this->assign('orderby_html',$orderby_html);
				$this->assign('searchby_html',$searchby_html);
		
				/*position指定以及一些问候信息*/
				$current = '"'.$fArr['uname'].'" 下属会员';
				$position = getPosition(array('企业会员管理列表'=>'__GROUP__/member/show?type=2',$current=>''));
				$this->assign('current',$current);
				$this->assign('position',$position);
				$this->assign('welcome',getWelcome());
				
				$this->assign('fid',$fid);
		
			}else{
				$this->error("会员类型错误！");
			}
				
		
				
			$this->display($tpl);
		}
		
		
		
		
		public function move(){
		
			$type = isset($_GET['type'])?$_GET['type']:null;
			$fid = isset($_GET['fid'])?$_GET['fid']:null;
			if($type === null || $fid === null){
				$this->error("读取会员类型错误！");
			}
		
		
			$map['usertype'] = array('eq',$type);
			$map['fid'] = array('neq',$fid);
				
			import('ORG.Util.Page');
			if(!empty($_POST['action'])){
				if($_POST['action'] == 'filter'){
					if(!empty($_POST['orderby'])){
						$arr = orderByParse($_POST['orderby']);
						if(is_array($arr)){
							$map_orderby = "`$arr[0]` $arr[1]";
						}
						$orderby = $_POST['orderby'];
						setcookie("orderby_user", $_POST['orderby']);
						setcookie("map_orderby_user", $map_orderby);
		
					}
		
					if(!empty($_POST['searchkey'])){
						$this->assign('searchkey',$_POST['searchkey']);
						$map[$_POST['searchby']] = array('like','%'.$_POST['searchkey'].'%');
					}
				}
			}else{
		
				if(!empty($_COOKIE['map_orderby_user'])){
					$map_orderby = $_COOKIE['map_orderby_user'];
					$orderby = $_COOKIE['orderby_user'];
				}
		
			}
		
		
			//排序
			$map_orderby = !empty($map_orderby)?$map_orderby:'id desc';
			$m = M('member');
			$fArr=$m->where('id='.$fid)->find();
			if(empty($fArr)){
				$this->error("该企业会员不存在!");
				return;
			}
				
			$count = $m->where($map)->count();// 查询满足要求的总记录数
			//echo $m->getLastSql();
			$Page  = new Page($count,C('SYS_PAGE_SIZE'),'type='.$type.'&fid='.$fid);// 实例化分页类 传入总记录数和每页显示的记录数
			$show  = $Page->show();// 分页显示输出
		
			//如果是搜索，则在一页内显示所有数据(不分页)
			if(!empty($map[$_POST['searchby']])) {
				$Page->listRows = 1000;
				$show = "一共搜索到 ".$count." 条数据";
			}
		
			$list = $m->where($map)->limit($Page->firstRow.','.$Page->listRows)->order($map_orderby)->select();
			$this->assign('list',$list);// 赋值数据集
			$this->assign('page',$show);// 赋值分页输出
		
		
			if($type == 1){
				$tpl = 'member:show4';
				/*select准备*/
				$arrOrderby = array('id_desc'=>'ID 降序','id_asc'=>'ID 升序','userid_desc'=>'会员ID 降序','userid_asc'=>'会员ID 升序','age_desc'=>'年龄 降序','age_asc'=>'年龄 升序','ispay_desc'=>'已付款优先','ispay_asc'=>'未付款优先');
				$orderby_html = getOptions($arrOrderby,$orderby);
				$arrSearchby = array('userid'=>'会员ID','uname'=>'真实姓名','phone'=>'电话','age'=>'年龄');
				$searchby_html = getOptions($arrSearchby,$_POST['searchby']);
				$this->assign('orderby_html',$orderby_html);
				$this->assign('searchby_html',$searchby_html);
		
				/*position指定以及一些问候信息*/
				$current = '"'.$fArr['uname'].'" 勾选会员';
				$position = getPosition(array('企业会员管理列表'=>'__GROUP__/member/show?type=2','"'.$fArr['uname'].'" 下属会员 '=>'__GROUP__/member/showson?type=1&fid='.$fid,$current=>''));
				$this->assign('current',$current);
				$this->assign('position',$position);
				$this->assign('welcome',getWelcome());
				$this->assign('fid',$fid);
		
			}else{
				$this->error("会员类型错误！");
			}
		
		
		
			$this->display($tpl);
		}
		
		
		
		public function moreinfo(){
			$type = isset($_GET['type'])?$_GET['type']:null;
			$id = isset($_GET['id'])?$_GET['id']:null;
			if($type === null || $id === null){
				$this->error("读取会员信息错误！");
			}
			
			//收货地址开始
			$m = M('memberAddr');
			$addrArr = $m->select();
			$this->assign('addrArr',$addrArr);
			//收货地址结束
			
			//会员卡开始
			$m = M('member');
			$info = $m->where('id='.$id)->find();
			$this->assign('minfo',$info);
			//会员卡结束
			
			$current = '"'.$info['userid'].'"'." 的详细信息";
			$position = getPosition(array('个人会员管理列表'=>'__GROUP__/member/show?type=1',$current=>''));
			$this->assign('current',$current);
			$this->assign('position',$position);
			$this->assign('welcome',getWelcome());
			
			$this->display();
		}
		
		
		
		
		public function articleShow(){
			import('ORG.Util.Page');// 导入类
			$m = M('memberArticle');
			if(!empty($_POST['action'])){
				$mid = isset($_POST['mid'])?$_POST['mid']:null;
				$status = isset($_POST['status'])?$_POST['status']:null;
				if($mid!=null){
					$map['mid'] =array('eq',$mid);
				}
				
				if($status!=null&& ($status==1 || $status==2)){
					if($status==1)$statuss='未审核';
					else $statuss='已审核';
					$map['status'] =array('eq',$statuss);
				}
				
					
				if($_POST['action'] == 'filter'){
					if(!empty($_POST['orderby'])){
						$arr = orderByParse($_POST['orderby']);
						if(is_array($arr)){
							$map_orderby = "`$arr[0]` $arr[1]";
						}
			
						$orderby = $_POST['orderby'];
						setcookie("orderby", $_POST['orderby']);
						setcookie("map_orderby", $map_orderby);
			
					}
						
					if(!empty($_POST['searchkey'])){
						$this->assign('searchkey',$_POST['searchkey']);
						$map[$_POST['searchby']] = array('like','%'.$_POST['searchkey'].'%');
					}

				}
			}else{
				$mid = isset($_GET['mid'])?$_GET['mid']:null;
				if($mid!=null){	
					$map['mid'] =array('eq',$mid);
				}
				
				$status = isset($_GET['status'])?$_GET['status']:null;
				if($status!=null&& ($status==1 || $status==2)){
					if($status==1)$statuss='未审核';
					else $statuss='已审核';
					$map['status'] =array('eq',$statuss);
				}
				
			
				if(!empty($_COOKIE['map_orderby'])){
					$map_orderby = $_COOKIE['map_orderby'];
					$orderby = $_COOKIE['orderby'];
				}

			}
				
		
			
			
			$arrOrderby = array('id_desc'=>'ID 降序','id_asc'=>'ID 升序','mid_desc'=>'发布者 降序','mid_asc'=>'发布者 升序','click_asc'=>'浏览次数 升序','click_desc'=>'浏览次数  降序','click_asc'=>'点击次数 升序','click_desc'=>'点击次数 降序', 'pubdate_asc'=>'发布日期 升序','pubdate_desc'=>'发布日期 降序','status_asc'=>'审核状态 升序','status_desc'=>'审核状态 降序');
			$orderby_html = getOptions($arrOrderby,$orderby);
			$arrSearchby = array('title'=>'标题','uname'=>'姓名','userid'=>'用户名');
			$searchby_html = getOptions($arrSearchby,$_POST['searchby']);
			$this->assign('orderby_html',$orderby_html);
			$this->assign('searchby_html',$searchby_html);
			$this->assign('mid',$mid);
			$this->assign('status',$status);
			
			
			
		
			//排序
			$map_orderby = !empty($map_orderby)?$map_orderby:'id desc';
				
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
				
			/*position指定以及一些问候信息*/
			$current = "会员文章管理列表";
			$position = getPosition($current);
			$this->assign('current',$current);
			$this->assign('position',$position);
			$this->assign('welcome',getWelcome());
			$this->display();
		}
		
		
		
		
		public function feedbackShow(){
			import('ORG.Util.Page');// 导入类
			$m = M('memberFeedback');
			if(!empty($_POST['action'])){
				$aid = isset($_POST['aid'])?$_POST['aid']:null;
				$status = isset($_POST['status'])?$_POST['status']:null;
				if($aid!=null){
					$map['aid'] =array('eq',$aid);
				}
		
				if($status!=null&& ($status==1 || $status==2)){
					if($status==1)$statuss='未审核';
					else $statuss='已审核';
					$map['status'] =array('eq',$statuss);
				}
		
					
				if($_POST['action'] == 'filter'){
					if(!empty($_POST['orderby'])){
						$arr = orderByParse($_POST['orderby']);
						if(is_array($arr)){
							$map_orderby = "`$arr[0]` $arr[1]";
						}
							
						$orderby = $_POST['orderby'];
						setcookie("orderby", $_POST['orderby']);
						setcookie("map_orderby", $map_orderby);
							
					}
		
					if(!empty($_POST['searchkey'])){
						$this->assign('searchkey',$_POST['searchkey']);
						$map[$_POST['searchby']] = array('like','%'.$_POST['searchkey'].'%');
					}

				}
				
				$page_params = 'status='.$status;
			}else{
				$aid = isset($_GET['aid'])?$_GET['aid']:null;
				if($aid!=null){
					$map['aid'] =array('eq',$aid);
				}
		
				$status = isset($_GET['status'])?$_GET['status']:null;
				if($status!=null&& ($status==1 || $status==2)){
					if($status==1)$statuss='未审核';
					else $statuss='已审核';
					$map['status'] =array('eq',$statuss);
				}
		
					
				if(!empty($_COOKIE['map_orderby'])){
					$map_orderby = $_COOKIE['map_orderby'];
					$orderby = $_COOKIE['orderby'];
				}
				$page_params = '';
			}
		
		
				
				
			$arrOrderby = array('id_desc'=>'ID 降序','id_asc'=>'ID 升序','aid_desc'=>'被评文章 降序','aid_asc'=>'被评文章 升序', 'pubdate_asc'=>'评论时间 升序','pubdate_desc'=>'评论时间 降序','status_asc'=>'审核状态 升序','status_desc'=>'审核状态 降序');
			$orderby_html = getOptions($arrOrderby,$orderby);
			$arrSearchby = array('title'=>'评论标题','uname'=>'评论人姓名','userid'=>'评论人用户名');
			$searchby_html = getOptions($arrSearchby,$_POST['searchby']);
			$this->assign('orderby_html',$orderby_html);
			$this->assign('searchby_html',$searchby_html);
			$this->assign('aid',$aid);
			$this->assign('status',$status);
				
				
				
		
			//排序
			$map_orderby = !empty($map_orderby)?$map_orderby:'id desc';
		
			$count = $m->where($map)->count();// 查询满足要求的总记录数
			$Page  = new Page($count,C('SYS_PAGE_SIZE'),$page_params);// 实例化分页类 传入总记录数和每页显示的记录数
			$show  = $Page->show();// 分页显示输出
		
			//如果是搜索，则在一页内显示所有数据(不分页)
			if(!empty($map[$_POST['searchby']])) {
				$Page->listRows = 1000;
				$show = "一共搜索到 ".$count." 条数据";
			}
		
			$list = $m->where($map)->limit($Page->firstRow.','.$Page->listRows)->order($map_orderby)->select();
			$this->assign('list',$list);// 赋值数据集
			$this->assign('page',$show);// 赋值分页输出
		
			/*position指定以及一些问候信息*/
			$current = "会员评论管理列表";
			$position = getPosition($current);
			$this->assign('current',$current);
			$this->assign('position',$position);
			$this->assign('welcome',getWelcome());
			$this->display();
		}
		
		
		
		/**
		 * 添加一个会员
		 */
		public function add(){
			
			$type = isset($_GET['type'])?$_GET['type']:null;
			$fid = isset($_GET['fid'])?$_GET['fid']:null;
			if($type === null){
				$this->error("读取会员类型错误！");
			}
			
			require(APP_INC_PATH.'form/Zebra_Form.php');
			$m = M('member');
			$roles = $m->where('fid=-1')->select();
			
			$rolesArr[0] = "无企业";
			foreach($roles as $v){
				$rolesArr[$v['id']] = $v['uname'];
			}
			
			
			$form = new Zebra_Form('form','post',U('form/membersave'));  //参数分别是 表单名称 提交方法 请求页面

			if($type==1){
				$form->add('text', 'usertype','1',array('type' => 'hidden'));
				
/* 				$form->add('label', 'label_fid', 'fid', '所属企业:');
				$obj = & $form->add('select', 'fid', $fid,array('style'=>'width:150px;'));
				$obj->add_options($rolesArr,true);
				$obj->set_rule(array(
						//'required' => array('error', '必须选择所属企业!')
				)); */
				
				$form->add('label', 'label_userid', 'userid', '会员ID:');
				$obj = & $form->add('text', 'userid','');
				$obj->set_rule(array(
						'required' => array('error', '必须填写会员ID!')
				));
				
				$form->add('label', 'label_pwd', 'pwd', '密码:');
				$obj = & $form->add('password', 'pwd','');
				$obj->set_rule(array(
						'required'  => array('error', '密码不能为空!'),
						'length'    => array(6, 10, 'error', '密码必须在6位到10位之间!'),
				));
				
				$form->add('label', 'label_pwd2', 'pwd2', '重复密码:');
				$obj = & $form->add('password', 'pwd2','');
				$obj->set_rule(array(
						'compare' => array('pwd','error','两次输入密码不一致!'),
						'required'  => array('error', '密码不能为空!'),
						'length'    => array(6, 10, 'error', '密码必须在6位到10位之间!'),
				));
				
				
/* 				$form->add('label', 'label_uname', 'uname', '真实姓名:');
				$obj = & $form->add('text', 'uname','');
				$obj->set_rule(array(
						'required' => array('error', '必须填写真实姓名!')
				)); */
				
				$form->add('label', 'label_sex', 'sex', '性别:');
				$obj = & $form->add('radios', 'sex', array(
							'男' =>  '男',
							'女' =>  '女'
				),'男');
				$obj->set_rule(array(
						'required' => array('error', '必须填写性别!')
				));
				
				
/* 				$form->add('label', 'label_age', 'age', '年龄:');
				$obj = & $form->add('text', 'age','',array('style' => 'width:100px'));
				$obj->set_rule(array(
						'required' => array('error', '必须填写年龄!'),
						'length'    => array(1, 3, 'error', '年龄必须在1位到3位之间!'),
						'number' =>array('','error','年龄必须为数字！')
				)); */
				
/* 				$form->add('label', 'label_height', 'height', '身高(cm):');
				$obj = & $form->add('text', 'height','',array('style' => 'width:100px'));
				$obj->set_rule(array(
						'required' => array('error', '必须填写身高!'),
						'length'    => array(1, 3, 'error', '身高必须在1位到3位之间!'),
						'number' =>array('','error','身高必须为数字！')
				));
				
				$form->add('label', 'label_weight', 'weight', '体重(kg):');
				$obj = & $form->add('text', 'weight','',array('style' => 'width:100px'));
				$obj->set_rule(array(
						'required' => array('error', '必须填写体重!'),
						'length'    => array(1, 3, 'error', '体重必须在1位到3位之间!'),
						'number' =>array('','error','体重必须为数字！')
				)); */
				
				$form->add('label', 'label_phone', 'phone', '电话:');
				$obj = & $form->add('text', 'phone','',array('style' => 'width:100px'));
				$obj->set_rule(array(
						'required' => array('error', '必须填写电话!'),
						'length'    => array(6, 11, 'error', '电话必须在6位到11位之间!'),
						'number' =>array('','error','电话必须为数字！')
				));
								
				
				$form->add('label', 'label_email', 'email', 'email:');
				$obj = & $form->add('text', 'email','',array('style' => 'width:300px'));
				$obj->set_rule(array(
						'email' => array('error', '请输入合法的email！')
				));
				
/* 				$form->add('label', 'label_addr', 'addr', '地址:');
				$obj = & $form->add('textarea', 'addr','');
				
				
				$form->add('label', 'label_ispay', 'ispay', '状态:');
				$obj = & $form->add('radios', 'ispay', array(
						'0' =>  '未付款',
						'1' =>  '已付款'
				),'0'); */
				
				
				
			}else if($type==2){
				
				
				$form->add('text', 'usertype','2',array('type' => 'hidden'));
				$form->add('text', 'fid','-1',array('type' => 'hidden'));

								
				$form->add('label', 'label_userid', 'userid', '企业ID:');
				$obj = & $form->add('text', 'userid','');
				$obj->set_rule(array(
						'required' => array('error', '必须填写企业IDD!')
				));
				
				$form->add('label', 'label_pwd', 'pwd', '密码:');
				$obj = & $form->add('password', 'pwd','');
				$obj->set_rule(array(
						'required'  => array('error', '密码不能为空!'),
						'length'    => array(6, 10, 'error', '密码必须在6位到10位之间!'),
				));
				
				$form->add('label', 'label_pwd2', 'pwd2', '重复密码:');
				$obj = & $form->add('password', 'pwd2','');
				$obj->set_rule(array(
						'compare' => array('pwd','error','两次输入密码不一致!'),
						'required'  => array('error', '密码不能为空!'),
						'length'    => array(6, 10, 'error', '密码必须在6位到10位之间!'),
				));
				
				
				$form->add('label', 'label_uname', 'uname', '企业名称:');
				$obj = & $form->add('text', 'uname','');
				$obj->set_rule(array(
						'required' => array('error', '必须填写企业名称!')
				));
				

				
				$form->add('label', 'label_phone', 'phone', '企业电话:');
				$obj = & $form->add('text', 'phone','',array('style' => 'width:100px'));
				$obj->set_rule(array(
						'length'    => array(6, 11, 'error', '企业电话必须在6位到11位之间!'),
						'number' =>array('','error','企业电话必须为数字！')
				));
				
				$form->add('label', 'label_cname', 'cname', '企业联系人:');
				$obj = & $form->add('text', 'cname','');
				$obj->set_rule(array(
				));
				
				$form->add('label', 'label_cphone', 'cphone', '联系人电话:');
				$obj = & $form->add('text', 'cphone','',array('style' => 'width:100px'));
				$obj->set_rule(array(
						'length'    => array(6, 11, 'error', '联系人必须在6位到11位之间!'),
						'number' =>array('','error','联系人必须为数字！')
				));
				
				$form->add('label', 'label_email', 'email', 'email:');
				$obj = & $form->add('text', 'email','',array('style' => 'width:300px'));
				$obj->set_rule(array(
						'email' => array('error', '请输入合法的email！')
				));
				
				$form->add('label', 'label_addr', 'addr', '地址:');
				$obj = & $form->add('textarea', 'addr','');
				
								
				
			}else{
				$this->error("读取会员类型错误！");
			}
			
			
			// "submit"
			$form->add('submit', 'btnsubmit', '注册');
			$html_str = $form->render('*horizontal');
			
			
			
			if($type == 1){			
				/*position指定以及一些问候信息*/
				$current = "个人会员添加";
				$position = getPosition(array('个人会员管理列表'=>'__GROUP__/member/show?type=1',$current=>''));
				$this->assign('current',$current);
				$this->assign('position',$position);
				$this->assign('welcome',getWelcome());
			
			}else if($type == 2){
				$current = "企业会员添加";
				$position = getPosition(array('企业会员管理列表'=>'__GROUP__/member/show?type=2',$current=>''));
				$this->assign('current',$current);
				$this->assign('position',$position);
				$this->assign('welcome',getWelcome());
			
			}else{
				$this->error("会员类型错误！");
			}

			$this->assign('form_html',$html_str);
			$this->display('member:form');
		}
		
		

		
		
		
		/**
		 * 后台会员编辑
		 */
		public function edit(){
			$id = isset($_GET['id'])?$_GET['id']:null;
			if($id == null){
				$this->error("读取会员id错误！");
			}
			
			require(APP_INC_PATH.'form/Zebra_Form.php');
			$m = M('member');
			$user = $m->where('id='.$id)->find();	
	
			$roles = $m->where('fid=-1')->select();
			$rolesArr[0] = '无企业';
			foreach($roles as $v){
				$rolesArr[$v['id']] = $v['uname'];
			}
			//dump($rolesArr);exit;
			$form = new Zebra_Form('form','post',U('form/memberupdate'));  //参数分别是 表单名称 提交方法 请求页面

			if($user['usertype']==1){
		
				$form->add('text', 'id',$id,array('type' => 'hidden'));
				$form->add('text', 'usertype',$user['usertype'],array('type' => 'hidden'));
/* 				$form->add('label', 'label_fid', 'fid', '所属企业:');
				$obj = & $form->add('select', 'fid', $user['fid'],array('style'=>'width:150px;'));
				$obj->add_options($rolesArr,true);
				$obj->set_rule(array(
						//'required' => array('error', '必须选择所属企业!')
				)); */
				
				$form->add('label', 'label_userid', 'userid', '会员ID:');
				$obj = & $form->add('text', 'userid',$user['userid']);
				$obj->set_rule(array(
						'required' => array('error', '必须填写会员ID!')
				));
								
				
/* 				$form->add('label', 'label_uname', 'uname', '真实姓名:');
				$obj = & $form->add('text', 'uname',$user['uname']);
				$obj->set_rule(array(
						'required' => array('error', '必须填写真实姓名!')
				)); */
				
				$form->add('label', 'label_sex', 'sex', '性别:');
				$obj = & $form->add('radios', 'sex', array(
							'男' =>  '男',
							'女' =>  '女'
				),$user['sex']);
				$obj->set_rule(array(
						'required' => array('error', '必须填写性别!')
				));
				
				
/* 				$form->add('label', 'label_age', 'age', '年龄:');
				$obj = & $form->add('text', 'age',$user['age'],array('style' => 'width:100px'));
				$obj->set_rule(array(
						'required' => array('error', '必须填写年龄!'),
						'length'    => array(1, 3, 'error', '年龄必须在1位到3位之间!'),
						'number' =>array('','error','年龄必须为数字！')
				));
				
				$form->add('label', 'label_height', 'height', '身高(cm):');
				$obj = & $form->add('text', 'height',$user['height'],array('style' => 'width:100px'));
				$obj->set_rule(array(
						'required' => array('error', '必须填写身高!'),
						'length'    => array(1, 3, 'error', '身高必须在1位到3位之间!'),
						'number' =>array('','error','身高必须为数字！')
				));
				
				$form->add('label', 'label_weight', 'weight', '体重(kg):');
				$obj = & $form->add('text', 'weight',$user['weight'],array('style' => 'width:100px'));
				$obj->set_rule(array(
						'required' => array('error', '必须填写体重!'),
						'length'    => array(1, 3, 'error', '体重必须在1位到3位之间!'),
						'number' =>array('','error','体重必须为数字！')
				)); */
				
				$form->add('label', 'label_phone', 'phone', '电话:');
				$obj = & $form->add('text', 'phone',$user['phone'],array('style' => 'width:100px'));
				$obj->set_rule(array(
						'required' => array('error', '必须填写电话!'),
						'length'    => array(6, 11, 'error', '电话必须在6位到11位之间!'),
						'number' =>array('','error','电话必须为数字！')
				));
								
				
				$form->add('label', 'label_email', 'email', 'email:');
				$obj = & $form->add('text', 'email',$user['email'],array('style' => 'width:300px'));
				$obj->set_rule(array(
						'email' => array('error', '请输入合法的email！')
				));
				
/* 				$form->add('label', 'label_addr', 'addr', '地址:');
				$obj = & $form->add('textarea', 'addr',$user['addr']);
				
				
				$form->add('label', 'label_ispay', 'ispay', '状态:');
				$obj = & $form->add('radios', 'ispay', array(
						'0' =>  '未付款',
						'1' =>  '已付款'
				),$user['ispay']); */
				
				
				
			}else if($user['usertype']==2){
				
				
				$form->add('text', 'id',$id,array('type' => 'hidden'));
				$form->add('text', 'usertype','2',array('type' => 'hidden'));
				$form->add('text', 'fid','-1',array('type' => 'hidden'));
				
				
				$form->add('label', 'label_userid', 'userid', '企业ID:');
				$obj = & $form->add('text', 'userid',$user['userid']);
				$obj->set_rule(array(
						'required' => array('error', '必须填写企业IDD!')
				));
				
				
				$form->add('label', 'label_uname', 'uname', '企业名称:');
				$obj = & $form->add('text', 'uname',$user['uname']);
				$obj->set_rule(array(
						'required' => array('error', '必须填写企业名称!')
				));
				
				
				$form->add('label', 'label_phone', 'phone', '企业电话:');
				$obj = & $form->add('text', 'phone',$user['phone'],array('style' => 'width:100px'));
				$obj->set_rule(array(
						'length'    => array(6, 11, 'error', '企业电话必须在6位到11位之间!'),
						'number' =>array('','error','企业电话必须为数字！')
				));
				
				$form->add('label', 'label_cname', 'cname', '企业联系人:');
				$obj = & $form->add('text', 'cname',$user['cname']);
				$obj->set_rule(array(
				));
				
				$form->add('label', 'label_cphone', 'cphone', '联系人电话:');
				$obj = & $form->add('text', 'cphone',$user['cphone'],array('style' => 'width:100px'));
				$obj->set_rule(array(
						'length'    => array(6, 11, 'error', '联系人必须在6位到11位之间!'),
						'number' =>array('','error','联系人必须为数字！')
				));
				
				$form->add('label', 'label_email', 'email', 'email:');
				$obj = & $form->add('text', 'email',$user['email'],array('style' => 'width:300px'));
				$obj->set_rule(array(
						'email' => array('error', '请输入合法的email！')
				));
				
				$form->add('label', 'label_addr', 'addr', '地址:');
				$obj = & $form->add('textarea', 'addr',$user['addr']);
				
			}else{
				$this->error("读取会员类型错误！");
			}
				
			// "submit"
			$form->add('submit', 'btnsubmit', '修改');
			$html_str = $form->render('*horizontal');
			$this->assign('form_html',$html_str);
			
			/*position指定以及一些问候信息*/			
			if($user['usertype'] == 1){
				/*position指定以及一些问候信息*/
				$current = "个人会员修改";
				$position = getPosition(array('个人会员管理列表'=>'__GROUP__/member/show?type=1',$current=>''));
				$this->assign('current',$current);
				$this->assign('position',$position);
				$this->assign('welcome',getWelcome());
					
			}else if($user['usertype'] == 2){
				$current = "企业会员修改";
				$position = getPosition(array('企业会员管理列表'=>'__GROUP__/member/show?type=2',$current=>''));
				$this->assign('current',$current);
				$this->assign('position',$position);
				$this->assign('welcome',getWelcome());
					
			}else{
				$this->error("会员类型错误！");
			}
			
			$this->display('member:form');
		}
		

		

		/**
		 * 后台管理员密码修改
		 */
		public function pwdedit(){
			$type = isset($_GET['type'])?$_GET['type']:null;
			$id = isset($_GET['id'])?$_GET['id']:null;
			if($id === null){
				$this->error("读取后台管理员id错误！");
			}
			
			require(APP_INC_PATH.'form/Zebra_Form.php');
			
			
			$form = new Zebra_Form('form','post',U('form/memberpwdupdate'));  //参数分别是 表单名称 提交方法 请求页面
			
			//隐藏表单
			$obj = & $form->add('text', 'id',$id,array('type' => 'hidden'));
			
			$form->add('label', 'label_pwd0', 'pwd0', '原密码:');
			$obj = & $form->add('password', 'pwd0','');
			$obj->set_rule(array(
					'required'  => array('error', '密码不能为空!'),
					'length'    => array(6, 10, 'error', '密码必须在6位到10位之间!'),
			));
			
			
			$form->add('label', 'label_pwd', 'pwd', '新密码:');
			$obj = & $form->add('password', 'pwd','');
			$obj->set_rule(array(
					'required'  => array('error', '密码不能为空!'),
					'length'    => array(6, 10, 'error', '密码必须在6位到10位之间!'),
			));
			
			
			$form->add('label', 'label_pwd2', 'pwd2', '重复密码:');
			$obj = & $form->add('password', 'pwd2','');
			$obj->set_rule(array(
					'compare' => array('pwd','error','两次输入密码不一致!'),
					'required'  => array('error', '密码不能为空!'),
					'length'    => array(6, 10, 'error', '密码必须在6位到10位之间!'),
			));
			 
			// "submit"
			$form->add('submit', 'btnsubmit', '修改');
			$html_str = $form->render('*horizontal');
			$this->assign('form_html',$html_str);
			
			/*position指定以及一些问候信息*/
					/*position指定以及一些问候信息*/			
			if($type == 1){
				/*position指定以及一些问候信息*/
				$current = "个人会员密码修改";
				$position = getPosition(array('个人会员管理列表'=>'__GROUP__/member/show?type=1',$current=>''));
				$this->assign('current',$current);
				$this->assign('position',$position);
				$this->assign('welcome',getWelcome());
					
			}else if($type == 2){
				$current = "企业会员密码修改";
				$position = getPosition(array('企业会员管理列表'=>'__GROUP__/member/show?type=2',$current=>''));
				$this->assign('current',$current);
				$this->assign('position',$position);
				$this->assign('welcome',getWelcome());
					
			}else{
				$this->error("会员类型错误！");
			}
			
			
			$this->display('member:form');
		}
		
		
		
		
		public function articleedit(){
			require(APP_INC_PATH.'form/Zebra_Form.php');
			$aid=isset($_GET['aid'])?$_GET['aid']:null;
			if($aid == null){
				$this->error("读取内容 id 出错！");
				return;
			}
				$m = M('memberArticle');

				$odata = $m->where('id='.$aid)->find();
					
		
				$form = new Zebra_Form('form','post',U('extform/memberarticlesave'));  //参数分别是 表单名称 提交方法 请求页面
		
				//隐藏表单
				$form->add('text', 'id',$aid,array('type' => 'hidden'));


				$form->add('label', 'label_title', 'title', '文章标题:');
				$obj = & $form->add('text', 'title',$odata['title'],array('style' => 'width:400px'));
				$obj->set_rule(array(
						'required' => array('error', '必须填写标题!')
				));
				
				$form->add('label', 'label_desc', 'desc', '文章简介:');
				$obj = & $form->add('textarea', 'desc',$odata['desc']);
					
				$form->add('label', 'label_litpic', 'litpic', '栏目缩略图:');
				$obj = & $form->add('kimg', 'litpic',$odata['litpic'],array('style' => 'width:400px'));
						
				
				$form->add('label', 'label_txt', 'txt', '文章正文:');
				$obj = & $form->add('kind', 'txt',$odata['txt'],array('style'=>'width:700px;height:300px;'));

				$form->add('label', 'label_status', 'status', '状态:');
				$obj = & $form->add('radios', 'status', array(
						'未审核' =>  '未审核',
						'已审核' =>  '已审核'
				),$odata['status']);
				$obj->set_rule(array(
						'required' => array('error', '必须选择状态！')
				));
				
				
				
			// "submit"
			$form->add('submit', 'btnsubmit', '确定');
			$rs = $form->render('*horizontal');
			$this->assign('form_html',$rs);
		
		
			/*position指定以及一些问候信息*/
			$current = "会员文章修改";
			$position = getPosition(array("会员文章列表"=>'__GROUP__/member/articleshow',"会员文章修改"=>""));
			$this->assign('current',$current);
			$this->assign('position',$position);
			$this->assign('welcome',getWelcome());
			$this->display('common:form');
		
				
		}
		
		
		public function feedbackedit(){
			require(APP_INC_PATH.'form/Zebra_Form.php');
			$fbid=isset($_GET['id'])?$_GET['id']:null;
			if($fbid == null){
				$this->error("读取评论 id 出错！");
				return;
			}
			$m = M('memberFeedback');
		
			$odata = $m->where('id='.$fbid)->find();
				
		
			$form = new Zebra_Form('form','post',U('extform/memberfeedbacksave'));  //参数分别是 表单名称 提交方法 请求页面
		
			//隐藏表单
			$form->add('text', 'id',$fbid,array('type' => 'hidden'));
		
		
			$form->add('label', 'label_title', 'title', '评论标题:');
			$obj = & $form->add('text', 'title',$odata['title'],array('style' => 'width:400px'));
			$obj->set_rule(array(
					'required' => array('error', '必须填写标题!')
			));
		
			$form->add('label', 'label_txt', 'txt', '评论正文:');
			$obj = & $form->add('kind', 'txt',$odata['txt'],array('style'=>'width:700px;height:300px;'));
		
			$form->add('label', 'label_status', 'status', '状态:');
			$obj = & $form->add('radios', 'status', array(
					'未审核' =>  '未审核',
					'已审核' =>  '已审核'
			),$odata['status']);
			$obj->set_rule(array(
					'required' => array('error', '必须选择状态！')
			));
		
		
		
			// "submit"
			$form->add('submit', 'btnsubmit', '确定');
			$rs = $form->render('*horizontal');
			$this->assign('form_html',$rs);
		
		
			/*position指定以及一些问候信息*/
			$current = "会员文章修改";
			$position = getPosition(array("会员文章列表"=>'__GROUP__/member/articleshow',"会员文章修改"=>""));
			$this->assign('current',$current);
			$this->assign('position',$position);
			$this->assign('welcome',getWelcome());
			$this->display('common:form');
		
		
		}
		
		

		/**
		 * 后台管理员密码修改
		 */
		public function resetpwd(){
			/*position指定以及一些问候信息*/
			$current = "会员密码重置";
			$position = getPosition($current);
			$this->assign('current',$current);
			$this->assign('position',$position);
			$this->assign('welcome',getWelcome());

				
				
			$this->display();
		}
		
		

	}


?>
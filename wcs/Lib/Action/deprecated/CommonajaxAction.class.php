<?php

	class CommonajaxAction extends Action{ 


		public function getMemberStatus(){
			$userid = cookie('userid');
			if(empty($userid) || !$_SESSION[C('MEMBER_AUTH_KEY')]){
				echo -1;
				return;
			}else{
				echo $userid;
				return;
			}

		}


		public function getRegionsByFid(){
			$fid = $_POST['fid'];
			$m = M('region');
			$arr = $m->where("fid = $fid")->select();
			if(empty($arr)){
				echo -1;
				return;
			}else{
				$html = '<option value="0">请选择</option>';
				foreach($arr as $v){
					$html.="<option value='".$v['id']."'>".$v['name']."</option>";
				}
				echo $html;
				return;
			}
		}
		
		public function setMainAddr(){
			$mid = !empty($_POST['mid'])?$_POST['mid']:null;
			$id  = !empty($_POST['id'])?$_POST['id']:null;
			if($mid === null || $id === null){
				echo -1;
				return;
			}	
			
			$m = M('memberAddr');
			if($m->where("mid = $mid")->setField("main",0) === false){
				echo -1;
				return;
			}else{
				if($m->where("id = $id")->setField("main",1) !== false){
					echo 1;
					return;
				}else{
					echo -1;
					return;
				}
			}
		
		}
		

		//投票
		public function goldvote(){
			$id  = !empty($_POST['id'])?$_POST['id']:null;
			$rs = array();
			$m = M('goldvote');
			$m2 = M('ipCheck');
			$map = array();
			$map['date'] = date('Y-m-d',time());
			$map['ip'] = get_client_ip();
			$check = $m2->where($map)->find();
			if(!empty($check)){
				$rs['code'] = -1;
				$rs['msg'] = "对不起！您今天已经投过票了！";
			}else{
				$m2->create($map);
				$m2->add();
				
				$map = array();
				$map['id'] = $id;
				$m->where($map)->setInc('click');
				$rs['code'] = 1;
				$rs['msg'] = "";
			}
			
			echo json_encode($rs);
			return;	
		}	
		
		
		
		
		public function refreshGoldvote(){
		
			$m = M('goldvote');
			$arr = $m->select();
			if(empty($arr)){
				$rs['code'] = -1;
				$rs['msg'] = "读取票数出错！";
			}else{
				$rs['sum'] = 0;
				foreach($arr as $v){
					$rs[$v['id']] = $v['click'];
					$rs['sum'] += $v['click'];
				}
					
					$rs[1] = sprintf("%.1f",$rs[1]/$rs['sum']*100);
					$rs[2] = sprintf("%.1f",$rs[2]/$rs['sum']*100);
					$rs[3] = sprintf("%.1f",$rs[3]/$rs['sum']*100);
				
			}
			
			echo json_encode($rs);
			return;
		}
		

        //发送短信验证码
		public function sendSms(){
			$tel  = !empty($_POST['tel'])?$_POST['tel']:null;
			if(empty($tel)){
				$rs['code'] = -1;
				$rs['msg'] = "用户手机不正确！";
			}else{
				
				$m2 = M('member');
				$memberArr = $m2->where("phone = '$tel'")->find();
				if(!empty($memberArr)){
					$rs['code'] = -1;
					$rs['msg'] = "该手机号码已经被注册！";
				}else{
					$corpAccount='dbhjw';
					$userAccount='admin';
					$pwd='123456';
					$mobile=$tel;
					$code  = rand_string(4,1);
					$content = '您好！短信验证码是 '.$code.' ,请在1分钟内完成验证，否则验证码会失效。【大邦黄金网】';
					$content = rawurlencode($content);
					$url="http://www.oa-sms.com/sendSms.action?corpAccount=$corpAccount&userAccount=$userAccount&pwd=".strtolower(md5($pwd))."&mobile=$mobile&content=$content";

					@$status = file_get_contents($url);
					$statusArr = explode("#",$status);
					if($statusArr[0]!=1){
						$rs['code'] = -1;
						$rs['msg'] = "发送验证码短信失败！";
					}else{
						
						$_SESSION['sms_code'] = $code;
						$_SESSION['sms_lasttime'] = time();
						$rs['code'] = 1;
						$rs['msg'] = "发送验证码短信成功！";
/* 						$m = M('smsCheck');
						$data = array();
						$data['ip'] = get_client_ip();
						$data['time'] = time();
						$data['code'] = $code;
						$m->create($data);
						if($m->add()===false){
							$rs['code'] = -1;
							$rs['msg'] = "记录验证码失败！";
						}else{
							$rs['code'] = 1;
							$rs['msg'] = "发送验证码短信成功！";
						} */
					}
					
				}
					
			}
			echo json_encode($rs);
			return;
		}
		

		//前台页面异步数据
		public function asynData(){
		
			$m = M('archives');
		    //$x = mt_rand(10,300);
			$arr = $m->where("typeid = 4")->order("id desc")->limit('0,5')->select();
			$arr2 = $m->where("typeid = 3")->order("id desc")->limit('0,5')->select();
			if(empty($arr) && empty($arr2)){
				$rs['code'] = -1;
				$rs['data'] = '';
			}else{
				$html = '    <div class="main1_center1">
				<h2><a href="'.contentUrl($arr[0]['id']).'">'.msubstr($arr[0]['title'],0,18).'</a></h2>
				<ul>
				<li><a href="'.contentUrl($arr[1]['id']).'">'.msubstr($arr[1]['title'],0,11).'</a> | <a href="'.contentUrl($arr[2]['id']).'">'.msubstr($arr[2]['title'],0,11).'</a></li>
				<li><a href="'.contentUrl($arr[3]['id']).'">'.msubstr($arr[3]['title'],0,11).'</a> | <a href="'.contentUrl($arr[4]['id']).'">'.msubstr($arr[4]['title'],0,11).'</a></li>
				</ul>
				</div>
				<div class="main1_center1">
				<h2><a href="'.contentUrl($arr2[0]['id']).'">'.msubstr($arr2[0]['title'],0,18).'</a></h2>
				<ul>
				<li><a href="'.contentUrl($arr2[1]['id']).'">'.msubstr($arr2[1]['title'],0,11).'</a> | <a href="'.contentUrl($arr2[2]['id']).'">'.msubstr($arr2[2]['title'],0,11).'</a></li>
				<li><a href="'.contentUrl($arr2[3]['id']).'">'.msubstr($arr2[3]['title'],0,11).'</a> | <a href="'.contentUrl($arr2[4]['id']).'">'.msubstr($arr2[4]['title'],0,11).'</a></li>
				</ul>
				</div>';
				$rs['code'] = 1;
				$rs['data'] = $html;
			}

			
			echo json_encode($rs);
		
		
		}
		
		
		//新闻顶踩操作
		public function arc_up_down(){
			$id = $_POST['id'];
			$act = $_POST['act'];
			if(empty($id) || empty($act)){
				$rs['code'] = -1;
				$rs['msg'] = "操作失败！";
			}else{
				$m = M('archivesFeedback');
				if($act == 'up'){
					if($m->where("id = $id")->setInc("top")!==false){
						$rs['code'] = 1;
						$rs['msg'] = "";
					}else{
						$rs['code'] = -1;
						$rs['msg'] = "";
					}
				}else{
					if($m->where("id = $id")->setInc("down")!==false){
						$rs['code'] = 1;
						$rs['msg'] = "";
					}else{
						$rs['code'] = -1;
						$rs['msg'] = "";
					}
				}
			}
			echo json_encode($rs);
			return;
		
		}
	
		
	}





















?>
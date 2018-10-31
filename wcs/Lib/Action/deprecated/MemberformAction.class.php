<?php

	class MemberformAction extends Action{ 
		
		
		
/*=============================通用逻辑开始=================================*/		
		public function memberBaseInfoSave(){	
			$m = M('member');
			$m->create();
			if($m->save()===false){
				$this->error("保存失败！请稍后再试！");
			}else{
				$this->success("保存信息成功！");
			
			}

		}
				
		public function shareSave(){
			$m = M('memberArticle');
			$data = $_POST;
			if(empty($data['id'])){	
				$data['pubdate'] = time(); 
				$m->create($data);
				if($m->add() === false){
					$this->error("发布分享文章失败！");
					return;
				}else{
					$this->success("发布分享文章成功！",U('member/arcList'));
					return;
				}
			}else{
				$data['status'] = 1;
				$m->create($data);
				if($m->save() === false){
					$this->error("修改分享文章失败！");
					return;
				}else{
					$this->success("修改分享文章成功！",U('member/arcList'));
					return;
				}
			}
		
		
		
		}
		
		public function delArc(){
			$id = $_POST['id'];
			$m = M('memberArticle');
			if($m->where("id = $id")->delete() === false){
				echo -1;
				return;
			}else{
				echo 1;
				return;
			}
		}
		
		
		public function changePwd(){
			$m = M('member');
			$data['id'] = $_POST['id'];
			$data['pwd'] = strrev(md5($_POST['npwd']));
			$m->create($data);
			if($m->save()===false){
				$this->error("修改密码失败！");
				return;
			}else{
				$this->success("修改密码成功！",U('Member/index'));
				return;
			}

		}
		
		public function addAddr(){
			$m = M('memberAddr');
			$m->create();
			if($m->add() === false){
				$this->error("添加收货地址失败！");
				return;
			}else{
				$this->success("添加收货地址成功！",U('member/addrs'));
				return;
			}
		}
		
		public function saveAddr(){
			$m = M('memberAddr');
			$m->create();
			if($m->save() === false){
				$this->error("修改收货地址失败！");
				return;
			}else{
				$this->success("修改收货地址成功！",U('member/addrs'));
				return;
			}
		}
	
	
		public function feedback(){
			if(empty($_POST['mid'])){
				$this->error("您还没有登陆，不能发表留言！",U('member/login'));
				return;
			}
			
			$userid = cookie('userid');
			$uname = cookie('uname');
			$m = M('memberFeedback');
			$data = $_POST;
			$data['pubdate'] = time();
			$data['userid'] = $userid;
			$data['uname'] = $uname;
			$data['title'] = $uname." 发表的留言";
			$data['status'] = 1;
			$m->create($data);
			if($m->add()===false){
				$this->error("发表留言失败！");
				return;
			}else{
				$this->success("发表留言成功，通过管理员审核后将显示出来！");
				return;
			}
			
		
		}
		
		public function arcfeedback(){
			if(!$_SESSION[C('MEMBER_AUTH_KEY')]){
				$this->error("您还没有登陆，不能发表留言！",U('member/login'));
				return;
			}
			
			$userid = cookie('userid');
			$uname = cookie('uname');
			$m = M('archivesFeedback');
			$data = $_POST;
			$data['pubdate'] = time();
			$data['userid'] = $userid;
			$data['uname'] = $uname;
			$data['title'] = $uname." 发表的留言";
			$data['status'] = 1;
			$data['top'] = 0; 
			$data['down'] = 0;
			$data['ip'] = get_client_ip();
			$m->create($data);
			if($m->add()===false){
				$this->error("发表留言失败！");
				return;
			}else{
				$this->success("发表留言成功，通过管理员审核后将显示出来！");
				return;
			}
				
		
		}
	
		/*=============================通用逻辑结束=================================*/	
	
	
	
		
		/*=============================项目逻辑开始=================================*/
		
		public function register(){
			if($_SESSION['verify'] != md5($_POST['verify']) && '-1' != $_POST['verify'] ) {
				$this->error('验证码错误！');
				return;
			}
			$data = $_POST;
			$data['pwd'] = strrev(md5($_POST['pwd']));
			$m = M('member');
			if($data['mtype']==1 || $data['mtype']==2){
				$m->create($data);
				if($m->add()===false){
					$this->error("注册失败！请稍后再试！");
				}else{
					$this->success("注册成功！",U('member/login'));
		
				}
            //涉及到亲属分表
			}else if($data['mtype']==3){
				$m->create($data);
				if($m->add()===false){
					$this->error("注册失败！请稍后再试！");
					return;
				}else{
					$mid = $m->getLastInsID();
					$pdata['mid'] = $mid;
					$pdata['userid'] = $data['userid'];
					$pdata['saled'] = 1;
					$pdata['registed'] = 1;
					$pdata['registetime'] = time();
					$pm = M('productExt');
					$pm->create($pdata);
					if($pm->where("pid='$data[pid]'")->save()===false){
						$this->error("注册失败！请稍后再试！");
						return;
					}else{
						$relationArr[0]['name'] = $data['r_name_1'];
						$relationArr[0]['sex'] = $data['r_sex_1'];
						$relationArr[0]['phone'] = $data['r_phone_1'];
						$relationArr[0]['telephone'] = $data['r_telephone_1'];
						$relationArr[0]['relationship'] = $data['r_relationship_1'];
						$relationArr[0]['main'] = 1;
						if(!empty($data['r_name_2'])){
							$relationArr[1]['name'] = $data['r_name_2'];
							$relationArr[1]['sex'] = $data['r_sex_2'];
							$relationArr[1]['phone'] = $data['r_phone_2'];
							$relationArr[1]['telephone'] = $data['r_telephone_2'];
							$relationArr[1]['relationship'] = $data['r_relationship_2'];
							$relationArr[1]['main'] = 0;
		
						}
						if(!empty($data['r_name_3'])){
							$relationArr[2]['name'] = $data['r_name_3'];
							$relationArr[2]['sex'] = $data['r_sex_3'];
							$relationArr[2]['phone'] = $data['r_phone_3'];
							$relationArr[2]['telephone'] = $data['r_telephone_3'];
							$relationArr[2]['relationship'] = $data['r_relationship_3'];
							$relationArr[2]['main'] = 0;
		
						}
		
						$rm = M('memberRelatives');
						foreach($relationArr as $v){
							$rdata = null;
							$rdata['mid'] = $mid;
							$rdata['main'] = $v['main'];
							$rdata['name'] = $v['name'];
							$rdata['sex'] = $v['sex'];
							$rdata['phone'] = $v['phone'];
							$rdata['telephone'] = $v['telephone'];
							$rdata['relationship'] = $v['relationship'];
							$rm->create($rdata);
							if($rm->add()===false){
								$this->error("注册失败！请稍后再试！");
								return;
							}
						}
						$this->success("产品会员注册成功！",U('member/login'));
						return;
					}
				}
            //带discuz论坛会员整合
			}else if($data['mtype']==4){
				$m->create($data);
				if($m->add()===false){
					$this->error("注册失败！请稍后再试！");
				}else{					
					$currentMid =  $m->getLastInsID();
					$db = mysql_connect('localhost','root','1');
					mysql_select_db('discuz',$db);
					
					
 					$userid = $_POST['userid'];
					$email = $_POST['email'];
					$salt = substr(uniqid(rand()), -6);
					$password = md5(md5($_POST['pwd']).$salt);
					
					$sql1 = "INSERT INTO dis_common_member(`uid`, `username`, `password`, `email`, `regdate`, `notifysound`, `timeoffset`)  VALUES(null, '$userid', '$password', '$email', '',0, 9999)";
			
					$sql2  = "INSERT INTO dis_ucenter_members(`uid`, `username`, `password`, `secques`, `email`, `regip`, `regdate`, `salt`)  VALUES (null, '$userid', '$password', '', '$email', '', '".time()."', '$salt')";

					
					if(mysql_query($sql1,$db) && mysql_query($sql2,$db)){
						$this->success("注册成功！",U('member/login'));
					}else{
						$m->where("id = $currentMid")->delete();
						$this->error("关联论坛注册失败！");
					}
					
					
					
				}
			}else{
				$this->error('注册用户类型错误！');
				return;
			}
		
		}



        // 登录检测
        public function checkLogin() {
        
            //dump($_SERVER); exit;
            if(empty($_POST['userid'])) {
                $this->error('用户名不能为空！');
            }elseif (empty($_POST['pwd'])){
                $this->error('密码不能为空！');
            }
            //生成认证条件
            $map = array();
            // 支持使用绑定帐号登录
            $map['userid']	= $_POST['userid'];

            if($_SESSION['verify'] != md5($_POST['verify']) && '-1' != $_POST['verify'] ) {
                $this->error('验证码错误！');
            }

            $m = M('member');
            $authInfo = $m->where($map)->find();
            if(empty($authInfo)){
                $this->error('帐号不存在！');
            }

            if($authInfo['pwd'] != strrev(md5($_POST['pwd']))) {
                $this->error('密码错误！');
            }

            session(C('MEMBER_AUTH_KEY'),$authInfo['id']);
            session('userid',$authInfo['userid']);
/*            $_SESSION[C('MEMBER_AUTH_KEY')]	=	$authInfo['id'];
            $_SESSION['userid']		=	$authInfo['userid'];*/
            $jumpUrl = U('Member/index');

            //写入cookie
            cookie('memberid',$authInfo['id'],array('expire'=>C('COOKIE_EXPIRE'),'prefix'=>C('COOKIE_PREFIX')));
            cookie('userid',$authInfo['userid'],array('expire'=>C('COOKIE_EXPIRE'),'prefix'=>C('COOKIE_PREFIX')));
            cookie('uname',$authInfo['uname'],array('expire'=>C('COOKIE_EXPIRE'),'prefix'=>C('COOKIE_PREFIX')));
            cookie('lastuserid',$authInfo['userid'],array('expire'=>C('COOKIE_EXPIRE'),'prefix'=>C('COOKIE_PREFIX')));
            //保存登陆状态
            if($_POST['loginstatus'] == 1){
                cookie('token',MD5($authInfo['pwd']),array('expire'=>C('COOKIE_EXPIRE'),'prefix'=>C('COOKIE_PREFIX')));
            }


            //保存登录信息
            $ip		=	get_client_ip();
            $time	=	time();
            $data = array();
            $data['id']	=	$authInfo['id'];
            $data['logintime']	=	$time;
            $data['loginip']	=	$ip;
            $m->save($data);
            $this->success('登录成功！',$jumpUrl);


        }


        public function logout(){
            session(C('MEMBER_AUTH_KEY'),NULL);
            cookie(null,C('COOKIE_PREFIX'));
			session('userid',NULL);
            redirect(U('Member/login'));
        }

		
		/*=============================项目逻辑结束=================================*/
		
		//邮箱找回密码方法3步：
		public function emailpassword(){
			$theme = C('SYS_DEFAULT_THEME');
			$s = $_GET['s'];
			if($s==1){
			$this->display($theme.':member_password1');
			}
			if($s==2){
			$this->display($theme.':member_password2');
			}
			if($s==3){
			$this->display($theme.':member_password3');
			}
		}
		
	    public function sendemailyz(){
			$theme = C('SYS_DEFAULT_THEME');
			 if(empty($_POST['userid'])) {
              //  $this->error('用户名不能为空！');
				echo "<script language='javascript'>alert('用户名不能为空！');window.location.href='".U('Memberform/emailpassword?s=1')."'</script>";
                exit;
            }elseif (empty($_POST['email'])){
              //  $this->error('邮箱不能为空！');
			  echo "<script language='javascript'>alert('邮箱不能为空！');window.location.href='".U('Memberform/emailpassword?s=1')."'</script>";
                exit;
            }
            //生成认证条件
            $map = array();
            // 支持使用绑定帐号登录
            $map['userid']	= $_POST['userid'];
			$map['email']	= $_POST['email'];

            if($_SESSION['verify'] != md5($_POST['verify']) && '-1' != $_POST['verify'] ) {
                //$this->error('验证码错误！');
				 
				echo "<script language='javascript'>alert('验证码错误！');window.location.href='".U('Memberform/emailpassword?s=1')."'</script>";
                exit;
            }

            $m = M('member');
            $authInfo = $m->where($map)->find();
            if(empty($authInfo)){
               // $this->error('帐号和绑定邮箱不符！');
				echo "<script language='javascript'>alert('帐号和绑定邮箱不符！');window.location.href='".U('Memberform/emailpassword?s=1')."'</script>";
                exit;
            }

            session('memberid',$authInfo['id']);
 
            $jumpUrl = U('Memberform/emailpassword?s=2');
			
			$str=null;
			$strPol = "ABCDEFGHIJKLMNPQRSTUVWXYZ123456789abcdefghijklmnpqrstuvwxyz";
			
			for($i=0;$i<5;$i++){
              $str.=$strPol[rand(0,58)];//rand($min,$max)生成介于min和max两个数之间的一个随机整数
            }

            session('emailyzm',$str);
			
require("smtp.php"); //找到smtp.php放入此文件同目录

//使用163邮箱服务器

$smtpserver = "smtp.163.com";


//163邮箱服务器端口 

$smtpserverport = 25;


//你的163服务器邮箱账号

$smtpusermail = "";


//收件人邮箱

$smtpemailto = $_POST['email'];


//你的邮箱账号(去掉@163.com)

$smtpuser = "";//SMTP服务器的用户帐号 


//你的邮箱密码

$smtppass = ""; //SMTP服务器的用户密码
 
//$msg_name = $_POST['msg_name'];
 

//邮件主题 

$mailsubject = "会员找回密码的验证码！";

//邮件内容 

$mailbody = "验证码为：".session('emailyzm')."\n";
$mailbody=iconv("UTF-8", "GB2312", $mailbody);

//邮件格式（HTML/TXT）,TXT为文本邮件 

$mailtype = "TXT";

//这里面的一个true是表示使用身份验证,否则不使用身份验证. 

$smtp = new smtp($smtpserver,$smtpserverport,true,$smtpuser,$smtppass);

//是否显示发送的调试信息 

//$smtp->debug = TRUE;

//发送邮件

$rs=$smtp->sendmail($smtpemailto, $smtpusermail, $mailsubject, $mailbody, $mailtype); 
if($rs==TRUE){
//  $this->success('验证成功！请进入邮箱查看验证码！',$jumpUrl);
echo "<script language='javascript'>alert('验证成功！请进入邮箱查看验证码！');window.location.href='".U('Memberform/emailpassword?s=2')."'</script>";
                exit;
}else{
   //$this->error('系统发送邮箱验证码失败！请检查服务邮箱帐号！');
   
    echo "<script language='javascript'>alert('系统发送邮箱验证码失败！请检查服务邮箱帐号！');window.location.href='".U('Memberform/emailpassword?s=1')."'</script>";
                exit;
}
		}
		
		public function emailchangePwd(){
			$m = M('member');
			$data['id'] = session('memberid');
			$data['pwd'] = strrev(md5($_POST['npwd']));
			if($_SESSION['emailyzm'] !=  $_POST['emailyzm'] ) {
               // $this->error('邮箱验证码错误！');
				//return;
				echo "<script language='javascript'>alert('邮箱验证码错误！');window.location.href='".U('Memberform/emailpassword?s=2')."'</script>";
                exit;
            }
			$m->create($data);
			if($m->save()===false){
				//$this->error("修改找回失败！");
				//return;
				echo "<script language='javascript'>alert('修改找回失败！');window.location.href='".U('Memberform/emailpassword?s=2')."'</script>";
                exit;
			}else{
				//$this->success("找回密码成功！",U('Memberform/emailpassword?s=3'));
				//return;
				echo "<script language='javascript'>alert('找回密码成功！');window.location.href='".U('Memberform/emailpassword?s=3')."'</script>";
                exit;
			}

		}
		
		
		
	
	
	}


?>
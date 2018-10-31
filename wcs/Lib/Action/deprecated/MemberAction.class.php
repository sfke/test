<?php

	class MemberAction extends Action{ 

        protected $memberId;
        protected $no_check_action = array('login','logout','verify','register');

		function _initialize(){	
			$this->render();		
			if (in_array(ACTION_NAME,$this->no_check_action)){
				return;
			}else{
				if (!$_SESSION[C('MEMBER_AUTH_KEY')]){
					redirect(U('member/login'));
					return;
				}else if(cookie('memberid')!=$_SESSION[C('MEMBER_AUTH_KEY')]){
					$this->error("非法登陆！");
					return;
				}
				else $this->memberId = cookie('memberid');
			}
		}
		
		private function render(){
			//定义seo相关内容
			$mtitle = "会员中心 - ".C('JL_WEBNAME');
			$mdesc = C('JL_DESC');
			$mkey = C('JL_KEYWORDS');
			$mrights = C('JL_POWERBY');

            $this->assign("defaultimg",C('SYS_DEFAULT_IMG'));
			$this->assign('jl_title',$mtitle);
			$this->assign('jl_desc',$mdesc);
			$this->assign('jl_key',$mkey);
			$this->assign('jl_rights',$mrights);
		}
		
/*=============================通用逻辑开始=================================*/	
		public function login(){
			$userid = $_COOKIE["lastuserid"];
			if(!empty($userid)){
				$this->assign("userid",$userid);
				$this->assign("verify",-1);
			}
			$theme = C('SYS_DEFAULT_THEME');
			$this->display($theme.':login');
		}

		Public function verify(){
			import('ORG.Util.Image');
			Image::buildImageVerify(4,1,'gif',60,30);
		}
		

		public function register(){
			$theme = C('SYS_DEFAULT_THEME');
			$this->display($theme.':register');
		}
		
		
		
		public function index(){
			$memberid = $this->memberId;
			//基本资料
			$m = M('member');
			$arr = $m->where('id='.$memberid)->find();
			if(empty($arr)){
				$this->error("读取会员信息失败！ ");
				return;
			}else{
				if(empty($arr['litpic'])) $arr['litpic'] = C('SYS_DEFAULT_IMG');
				$this->assign('mInfo',$arr);
			}

			$this->getMemberArcByMid($memberid);
			//订单统计
			$osaArr = orderStatusAnalyse($memberid);
			if(!empty($osaArr)){
				$this->assign('osaArr',$osaArr);
			}
				
			// 1、等待付款订单数：2、 已经付款订单数：3、已完成订单数：4、已取消订单数：0、订单总数
			$theme = C('SYS_DEFAULT_THEME');
			$this->display($theme.':member_center');
			
		}
		
		public function baseinfo(){
			$memberid = $this->memberId;
			$m = M('member');
			$arr = $m->where('id='.$memberid)->find();
			if(empty($arr)){
				$this->error("读取会员信息失败！ ");
				return;
			}else{
				if(empty($arr['litpic'])) $arr['litpic'] = C('SYS_DEFAULT_IMG');
				$this->assign('mInfo',$arr);
			}
			
			$this->assign('mid',$memberid);
			$theme = C('SYS_DEFAULT_THEME');
			$this->display($theme.':grzx1');
				
		}
				
		
		public function addradd(){
			$memberid = $this->memberId;
			$m = M('memberAddr');
			$m2 = M('region');
			$proviceArr = $m2->where("type = 1")->select();
			if(!empty($proviceArr)){
				$proviceArr[0]['id'] = "0";
				$proviceArr[0]['name'] = "请选择";
				$this->assign('proviceArr',$proviceArr);
			}		
			$this->assign('mid',$memberid);
			$theme = C('SYS_DEFAULT_THEME');
			$this->display($theme.':grzxaddradd');
		}
		
		public function addredit(){
			$aid = $_GET['id'];
			$memberid = $this->memberId;
			$m = M('memberAddr');
			
			$oldArr = $m->where("id = $aid")->find();
			if(empty($oldArr)){
				$this->error("读取收货地址信息失败！");
				return;
			}else{
				$this->assign('oldArr',$oldArr);
			}
			
			$m2 = M('region');
			$proviceArr = $m2->where("type = 1")->select();
			if(!empty($proviceArr)){
				$this->assign('proviceArr',$proviceArr);
			}
			$cityArr = $m2->where("fid = ".$oldArr['province'])->select();
			if(!empty($proviceArr)){
				$this->assign('cityArr',$cityArr);
			}
				
			$distArr = $m2->where("fid = ".$oldArr['city'])->select();
			if(!empty($proviceArr)){
				$this->assign('distArr',$distArr);
			}
			
			
			$this->assign('mid',$memberid);
			$this->assign('id',$aid);
			$theme = C('SYS_DEFAULT_THEME');
			$this->display($theme.':grzxaddredit');

		}
		
		public function addrs(){
			$memberid = $this->memberId;
			$m = M('memberAddr');
			$arr = $m->where("mid = $memberid")->select();
			
			$this->assign('addrArr',$arr);
			$this->assign('mid',$memberid);
			$theme = C('SYS_DEFAULT_THEME');
			$this->display($theme.':grzxaddrlist');
		}
		
		
		public function order(){
			$status = isset($_GET['status'])?$_GET['status']:0;
			$memberid = $this->memberId;
			$m = M('orderInfo');
					
			switch($status){
				case 1 : $map['paystatus'] = array('eq',1);   $map['orderstatus'] = array('eq',1); break;
				case 2 : $map['orderstatus'] = array('eq',1); $map['paystatus'] = array('eq',2); break;
				case 3 : $map['orderstatus'] = array('eq',3); break;
				case 4 : $map['orderstatus'] = array('eq',2); break;
				default : $map = array();
			}
			
			$map['mid'] = array('eq',$memberid);
			$orderArr = $m->where($map)->select();
			if(!empty($orderArr)){
				$this->assign('orderArr',$orderArr);
			}
			
			$osaArr = orderStatusAnalyse($memberid);
			if(!empty($osaArr)){
				$this->assign('osaArr',$osaArr);
			}
			
			// 1、等待付款订单数：2、 已经付款订单数：3、已完成订单数：4、已取消订单数：0、订单总数
			
			$this->assign('status',$status);
			$theme = C('SYS_DEFAULT_THEME');
			$this->display($theme.':grzx2');
				
		}
		

		
		public function changepwd(){
			//$memberid = cookie('memberid');
			$memberid = $this->memberId;
			$this->assign('mid',$memberid);
			$theme = C('SYS_DEFAULT_THEME');
			$this->display($theme.':member_changepwd');
		
		}
		
		
		private function getMemberArcByMid($mid){
			//该会员的文章
			$m2 = M('memberArticle');
			$map['status'] = array('eq',2); //2 表示已审核
			$map['mid'] = array('neq',$mid); //2 表示已审核
			$arr2 = $m2->field('id,mid,title')->where($map)->order('id desc')->limit('0,12')->select();
			$this->assign('mArc',$arr2);
		
		}
		
		
		public function arcAdd(){
			$memberid = $this->memberId;
			$userid = cookie('userid');
			$uname = cookie('uname');
			$this->assign('uname',$uname);
			$this->assign('mid',$memberid);
			$this->assign('userid',$userid);
		
		
			$this->getMemberArcByMid($memberid);
			$theme = C('SYS_DEFAULT_THEME');
			$this->display($theme.':arcadd');
		
		}
		
		
		public function arcEdit(){
			$memberid = $this->memberId;
			$aid = $_GET['aid'];
			$m = M('memberArticle');
			$arcArr = $m->where("id = $aid")->find();
			if(empty($arcArr)){
				$this->error("读取文章内容失败！");
				return;
			}else{
				$this->assign("c",$arcArr);
			}
			
			$this->assign('id',$aid);
			$this->getMemberArcByMid($memberid);
			$theme = C('SYS_DEFAULT_THEME');
			$this->display($theme.':arcedit');
		
		}
		
		
		
		
		public function arcList(){
			import('ORG.Util.Page');// 导入类
			$memberid = $this->memberId;
			
			$m = M('memberArticle');
			$map['status'] = array('eq',2); //2 表示已审核
			$map['mid'] = array('eq',$memberid); //2 表示已审核
			$map_orderby = "id desc";
			$count = $m->where($map)->count();// 查询满足要求的总记录数
			$Page  = new Page($count,20);// 实例化分页类 传入总记录数和每页显示的记录数
			$show  = $Page->show();// 分页显示输出
			$list = $m->where($map)->limit($Page->firstRow.','.$Page->listRows)->order($map_orderby)->select();
			$this->assign('list',$list);// 赋值数据集
			$this->assign('page',$show);// 赋值分页输出
			$theme = C('SYS_DEFAULT_THEME');
			$this->display($theme.':marclist');
		}
		
		
		public function arcView(){
			$memberid = $this->memberId;
			$aid = $_GET['aid'];
			$m = M('memberArticle');
			$arcArr = $m->where("id = $aid")->find();
			if(empty($arcArr)){
				$this->error("读取文章内容失败！");
				return;
			}else{
				$this->assign("c",$arcArr);
				
				$m2 = M('memberFeedback');
				$map['status'] = array('eq',2);
				$map['aid'] = array('eq',$aid);
				$msgArr = $m2->where($map)->select();
				if(!empty($msgArr)){
					$this->assign("msgArr",$msgArr);
				}
			}
			
			$m->where("id = $aid")->setField("click",$arcArr['click']+1);
			$this->assign('mid',$memberid);
			$theme = C('SYS_DEFAULT_THEME');
			$this->display($theme.':member_arc');
		}
		
		
/*=============================通用逻辑结束=================================*/
		
		
		
		
/*=============================项目逻辑开始=================================*/		

/*=============================项目逻辑开始=================================*/	
	
	
	
	
	
	
	
	
	
	
	
	
}





















?>
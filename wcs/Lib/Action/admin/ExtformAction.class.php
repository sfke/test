<?php

/**
 * 统一处理form表单
 */


	class ExtformAction extends Action{
		
		
		
		/**
		 * 独立表新增保存
		 */
		public function save(){
            $jurl = isset($_POST['jurl'])?$_POST['jurl']:'';

            if($_SESSION['zebra_csrf_token_form'][0] != $_POST['zebra_csrf_token_form']){
                $this->error("请勿重复提交！");
            }

			$nid = isset($_POST['nid'])?$_POST['nid']:null;
			if($nid==null){
				$this->error('读取独立表失败！');
			}
			$addtable = $nid;

			$m = M($addtable);
			$m->create();
			/*
			留言时间提交示例
			if($addtable=="msg"){
			  if($m->create()){
			   $m->msg_time= strtotime($_POST['msg_time']);
              }
			}
			*/
            $m->siteid = getSiteId();
			if($m->add()!==false){
				$this->success("添加内容成功！",$jurl);
			}else{
				$this->error("添加内容失败！");
			}
		}

		/**
		 * 独立表新增修改
		 */
		public function update(){
			$id = isset($_POST['id'])?$_POST['id']:null;
			$nid = isset($_POST['nid'])?$_POST['nid']:null;
			$jurl = isset($_POST['jurl'])?$_POST['jurl']:'';
			if($id==null || $nid==null){
				$this->error('读取内容id失败！');
			}
			$addtable = $nid;
			$m = M($addtable);
			$m->create();
			/*
			留言时间提交示例
			if($addtable=="msg"){
			  if($m->create()){
			   $m->msg_time= strtotime($_POST['msg_time']);
              }
			}
			*/
			if($m->save()!==false){
				$this->success("更新内容成功！",$jurl);
			}else{
				$this->error("更新内容失败！");
			}
		}
		
		
		
		
		
		
		
		//*******************采集表单开始********************************************
		
		
		public function collectNodeSave(){
            /*
            if($_SESSION['zebra_csrf_token_form'][0] != $_POST['zebra_csrf_token_form']){
                $this->error("请勿重复提交！");
            }
            */
			$id = !empty($_POST['id'])?$_POST['id']:null;
			$m = M('collectNode');
			$m->create();
			//add
			if($id==null){
				if($m->add()!==false){
					$this->success("添加采集项成功！",'__GROUP__/collect/show');
				}else{
					$this->error("添加采集项失败！");
				}

			}else{
				if($m->save()!==false){
					$this->success("更新采集项成功！",'__GROUP__/collect/show');
				}else{
					$this->error("更新采集项失败！");
				}
			
			}
		
		}
		
		
		public function collectCacheSave(){

            if($_SESSION['zebra_csrf_token_form'][0] != $_POST['zebra_csrf_token_form']){
                $this->error("请勿重复提交！");
            }

			$id = !empty($_POST['id'])?$_POST['id']:null;
			$m = M('collectCache');
			$m->create();
			if($m->save()!==false){
				$this->success("更新采集项成功！",'__GROUP__/collect/cache');
			}else{
				$this->error("更新采集项失败！");
			}
		}
		
		
		
		
		
		
		
		//*******************采集表单结束********************************************
		
		
		
		
		
		
		
		
		//*******************会员表单开始********************************************
		public function memberarticlesave(){

            if($_SESSION['zebra_csrf_token_form'][0] != $_POST['zebra_csrf_token_form']){
                $this->error("请勿重复提交！");
            }

			$id = isset($_POST['id'])?$_POST['id']:null;
			if($id==null){
				$this->error('读取内容id失败！');
			}
			
			$m = M('memberArticle');
			$m->create();
			if($m->save()!==false){
				$this->success("更新文章成功！",'__GROUP__/member/articleshow');
			}else{
				$this->error("更新文章失败！");
			}

		}
		
		
		public function memberfeedbacksave(){

            if($_SESSION['zebra_csrf_token_form'][0] != $_POST['zebra_csrf_token_form']){
                $this->error("请勿重复提交！");
            }

			$id = isset($_POST['id'])?$_POST['id']:null;
			if($id==null){
				$this->error('读取评论id失败！');
			}
				
			$m = M('memberFeedback');
			$m->create();
			if($m->save()!==false){
				$this->success("更新评论成功！",'__GROUP__/member/feedbackshow');
			}else{
				$this->error("更新评论失败！");
			}

		}
		
		
		public function archivesfeedbacksave(){

            if($_SESSION['zebra_csrf_token_form'][0] != $_POST['zebra_csrf_token_form']){
                $this->error("请勿重复提交！");
            }

			$id = isset($_POST['id'])?$_POST['id']:null;
			if($id==null){
				$this->error('读取评论id失败！');
			}
		
			$m = M('memberFeedback');
			$m->create();
			if($m->save()!==false){
				$this->success("更新评论成功！",'__GROUP__/content/feedbackshow');
			}else{
				$this->error("更新评论失败！");
			}
		
		}
		//*******************会员表单结束********************************************
		
		
		
		
	}

?>
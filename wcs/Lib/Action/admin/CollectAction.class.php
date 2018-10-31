<?php
	
	/*
	 * 友情链接管理
	 */
	
	class CollectAction extends BaseAction{
		
		public function show(){
			$m = M('collectNode');
			$cArr = $m->select();

			$this->assign('cArr',$cArr);
			/*position指定以及一些问候信息*/
			$current = "采集项管理列表";
			$position = getPosition($current);
			$this->assign('current',$current);
			$this->assign('position',$position);
			$this->assign('welcome',getWelcome());
			
			$this->display();
		}
		
		
		public function add(){				

			/*position指定以及一些问候信息*/
			$current = "采集项添加";
			$position = getPosition(array('采集项管理列表'=>'__GROUP__/collect/show','采集项添加'=>''));
			$this->assign('current',$current);
			$this->assign('position',$position);
			$this->assign('welcome',getWelcome());
			
			$this->display ('form');
				
		}
		
		
		
		
		
		
		public function edit(){
			$id = isset($_GET['id'])?$_GET['id']:null;
			if($id===null){
				$this->error('读取采集项失败！');
			}
			
			$m = M('collectNode');
			$arr = $m->where("id=$id")->find();
			
			$this->assign('arr',$arr);
			$this->assign('id',$id);
			
			
			/*position指定以及一些问候信息*/
			$current = "采集项修改";
			$position = getPosition(array('采集项管理列表'=>'__GROUP__/collect/show','采集项修改'=>''));
			$this->assign('current',$current);
			$this->assign('position',$position);
			$this->assign('welcome',getWelcome());
			
			$this->display ('form');
			
		} 
		
		
		
		private function getListUrl($id){
			$m = M('collectNode');
			$arr = $m->where("id = $id")->find();
			$_SESSION['collect_node_array'] = $arr;
			if(empty($arr)){
				$this->error("没有找到对应的采集节点！");
				return;
			}else{
				$listUrlArr = array();
				$startid = intval($arr['startid']);
				$endid = intval($arr['endid']);
				$addv = intval($arr['addv']);
				$listurl = $arr['listurl'];
				$outtime = $arr['outtim'];
				
				if(!empty($arr['listurlmore'])){
					$listurlmoreArr = explode("\n",trim($arr['listurlmore']));
					if(!empty($listurlmoreArr)){
						$listUrlArr = $listurlmoreArr;
					}
				}else{
					$listUrlArr = array();
				}
				if(!empty($addv) && $endid >= $startid){
					for($i = $startid;$i<=$endid;$i+=$addv){
						$nowUrl =  str_replace("*", $i, $listurl);
						array_push($listUrlArr, $nowUrl);
					}
				}
				
				return $listUrlArr;
			}
		}
		
		
		public function test(){
			$id = isset($_GET['id'])?$_GET['id']:null;
			$lid = isset($_GET['lid'])?$_GET['lid']:null;
			$step = isset($_GET['step'])?$_GET['step']:1;
			
			/*position指定以及一些问候信息*/
			$current = "采集项测试";
			$position = getPosition(array('采集项管理列表'=>'__GROUP__/collect/show','采集项测试'=>''));
			$this->assign('current',$current);
			$this->assign('position',$position);
			$this->assign('welcome',getWelcome());
			
			if($step == 1){
				if($id===null){
					$this->error('读取采集项失败！');
				}
				$listUrlArr = $this->getListUrl($id);
				$_SESSION['listUrlArr'] = $listUrlArr;
				$this->assign("listUrl",$listUrlArr);

				$this->display('collect:test1');
			}else if($step == 2){
				$arr = $_SESSION['collect_node_array'];
				$listUrlArr = $_SESSION['listUrlArr'];
				//$_SESSION['listUrlArr'] = null;
				if(empty($listUrlArr[$lid])) {$this->error("该URL地址不存在！");return;}
				else{
					$listUrl = $listUrlArr[$lid];
					require(APP_INC_PATH.'collect/HttpClient.class.php');
					$pageContents = HttpClient::quickGet($listUrl); 
					if($arr['character'] == 'utf8'){
						$pageContents = iconv("gbk", "utf-8", $pageContents);
					}
					if (empty($pageContents)) {
						$this->error("该URL地址不正确！");
						return;
					}else{
						//$p =  "#".$arr['lstart']."(.*)".$arr['lend']."#is";
						$p =  "#".$arr['lstart']."(.*)".$arr['lend']."#is";
						//'#<!--main begin-->(.*)<div class="digg">#is'
						preg_match($p,$pageContents,$matchArr);
						//dump($matchArr[1]);
						if(!empty($matchArr)){
							
							if($arr['type'] == 'preg'){
								$pattem = "#".str_replace("*", "([^<]*)", $arr['listurly'])."#i";
							}else{
								$pattem = "#".str_replace("*", "(.*)", $arr['listurly'])."#i";
							}
							$pattem="/<a[^>]*?href=\"([^>]+?)\"[^>]*?>.+?<\/a>/i";
							preg_match_all($pattem,$matchArr[1],$matchArr2);
							//print_r($matchArr2);
							$this->assign("contentUrlArr",$matchArr2[0]);
							$_SESSION['content_url_array'] = $matchArr2[1];
						}	
					}

					//iconv("gbk", "utf-8", $pageContents);
				}
				
				$this->display('collect:test2');
			}else if($step ==3 ){
				$arr = $_SESSION['collect_node_array'];
				$content_url_array = $_SESSION['content_url_array'];
				if(empty($content_url_array[$lid])){$this->error("该URL地址不存在！");return;}
				else{
					require(APP_INC_PATH.'collect/HttpClient.class.php');
					
					if(!strstr($content_url_array[$lid],'http://')){
						$content_url_array[$lid] = $arr['curl'].$content_url_array[$lid];
					}
					//echo $content_url_array[$lid];
					$pageContents = HttpClient::quickGet($content_url_array[$lid]);
					if($arr['character'] == 'utf8'){
						$pageContents = iconv("gbk", "utf-8", $pageContents);
					}
					$contentArr = array();
					//title采集
					if(!empty($arr['titlestart']) && !empty($arr['titleend']) ){
						$pattem = "#".trim($arr['titlestart'])."(.*)".trim($arr['titleend'])."#i";
						preg_match($pattem,$pageContents,$matchArr);
						$contentArr['title'] = strip_tags($matchArr[1]);
					}
					if(empty($contentArr['title'])) $contentArr['title'] = C('JL_COLLECT_TITLE');
					//source采集
					if(!empty($arr['sourcestart']) && !empty($arr['sourceend']) ){
						$pattem = "#".trim($arr['sourcestart'])."(.*)".trim($arr['sourceend'])."#i";
						preg_match($pattem,$pageContents,$matchArr);
						$contentArr['source'] = strip_tags($matchArr[1]);
					}
					if(empty($contentArr['source'])) $contentArr['source'] = C('JL_COLLECT_SOURCE');
					//author采集
					if(!empty($arr['authorstart']) && !empty($arr['authorend']) ){
						$pattem = "#".trim($arr['authorstart'])."(.*)".trim($arr['authorend'])."#i";
						preg_match($pattem,$pageContents,$matchArr);
						$contentArr['author'] = strip_tags($matchArr[1]);
					}
					if(empty($contentArr['author'])) $contentArr['author'] = C('JL_COLLECT_AUTHOR');
					//pubdate采集
					if(!empty($arr['pubdateend']) && !empty($arr['pubdateend']) ){
						$pattem = "#".trim($arr['pubdatestart'])."(.*)".trim($arr['pubdateend'])."#i";
						preg_match($pattem,$pageContents,$matchArr);
						$contentArr['pubdate'] = strip_tags($matchArr[1]);
					}
					if(empty($contentArr['pubdate'])) $contentArr['pubdate'] = date("Y-m-d h:i:s",time());
					//body采集
					if(!empty($arr['bodystart']) && !empty($arr['bodyend']) ){
						$pattem = "#".trim($arr['bodystart'])."(.*)".trim($arr['bodyend'])."#is";
						preg_match($pattem,$pageContents,$matchArr);
						$contentArr['body'] = strip_tags($matchArr[1],'<table><th><tr><td><br><ul><li><strong><p><a>');
						//$contentArr['body'] = strip_tags($matchArr[1]);
					}
					//更多采集
					if(!empty($arr['moreend']) && !empty($arr['moreend']) ){
						$pattem = "#".trim($arr['morestart'])."(.*)".trim($arr['moreend'])."#i";
						preg_match($pattem,$pageContents,$matchArr);
						$contentArr['more'] = strip_tags($matchArr[1]);
					}
					
					$this->assign("contentArr",$contentArr);
				}
				$this->display('collect:test3');
			}else{
				$this->error("未知的测试操作！");
				return false;
			}
  			

			
		}
		
		
		public function work(){
			$id = isset($_GET['id'])?$_GET['id']:null;
			$page = isset($_GET['page'])?$_GET['page']:0;
			$now = isset($_GET['now'])?$_GET['now']:0;
			set_time_limit(0);
			
/* 			$nodem = M('collectNode');
			$nodeArr = $nodem->where("id = $id")->find(); */
			
			if($page == 0){
				//获得所有列表页面
				$listUrlArr = $this->getListUrl($id);
				$_SESSION['listUrlArr'] = $listUrlArr;
			}else{
				$listUrlArr = $_SESSION['listUrlArr'];
			}
			$totlepage = count($listUrlArr);
			
			
			if($page>$totlepage){
				$this->success("采集任务已经完成！",U("collect/show"));
				return;
			}
			
			$arr = $_SESSION['collect_node_array'];
  			$listUrl = $listUrlArr[$page];
			require(APP_INC_PATH.'collect/HttpClient.class.php');
			$pageContents = HttpClient::quickGet($listUrl);
			if($arr['character'] == 'utf8'){
				$pageContents = iconv("gbk", "utf-8", $pageContents);
			}
			if (empty($pageContents)) {
				//什么都不做
			}else{
				if($now == 0){
					$p =  "#".$arr['lstart']."(.*)".$arr['lend']."#is";
					//echo "'#".$arr['lstart']."(.*)".$arr['lend']."#'";
					preg_match($p,$pageContents,$matchArr);
					//dump($matchArr[1]);
					if(!empty($matchArr)){
						
						if($arr['type'] == 'preg'){
							$pattem = "#".str_replace("*", "([^<]*)", $arr['listurly'])."#i";
						}else{
							$pattem = "#".str_replace("*", "(.*)", $arr['listurly'])."#i";
						}
						
						$pattem="/<a[^>]*?href=\"([^>]+?)\"[^>]*?>.+?<\/a>/i";
						preg_match_all($pattem,$matchArr[1],$matchArr2);
						$contentUrlArr = $matchArr2[1];
						$_SESSION['contentUrlArr'] = $contentUrlArr;
						//dump($contentUrlArr);
					}
				}else{
					$contentUrlArr = $_SESSION['contentUrlArr'];
				}
			
				if(!empty($contentUrlArr) && !empty($contentUrlArr[$now] )){
					
					$m = M('collectCache');
					$totleContentUrl = count($contentUrlArr);
					
					$nowUrl = $contentUrlArr[$now];
					
					if(!strstr($nowUrl,'http://')){
						$nowUrl = $arr['curl'].$nowUrl;
					}
					
					//foreach($contentUrlArr as $v){
						$pageContents = HttpClient::quickGet($nowUrl);
						if($arr['character'] == 'utf8'){
							$pageContents = iconv("gbk", "utf-8", $pageContents);
						}
						$contentArr = array();
						//title采集
						if(!empty($arr['titlestart']) && !empty($arr['titleend']) ){
							$pattem = "#".trim($arr['titlestart'])."(.*)".trim($arr['titleend'])."#i";
							preg_match($pattem,$pageContents,$matchArr);
							$contentArr['title'] = strip_tags($matchArr[1]);
						}
						if(empty($contentArr['title'])) $contentArr['title'] = C('JL_COLLECT_TITLE');
						//source采集
						if(!empty($arr['sourcestart']) && !empty($arr['sourceend']) ){
							$pattem = "#".trim($arr['sourcestart'])."(.*)".trim($arr['sourceend'])."#i";
							preg_match($pattem,$pageContents,$matchArr);
							$contentArr['source'] = strip_tags($matchArr[1]);
						}
						if(empty($contentArr['source'])) $contentArr['source'] = C('JL_COLLECT_SOURCE');
						//author采集
						if(!empty($arr['authorstart']) && !empty($arr['authorend']) ){
							$pattem = "#".trim($arr['authorstart'])."(.*)".trim($arr['authorend'])."#i";
							preg_match($pattem,$pageContents,$matchArr);
							$contentArr['author'] = strip_tags($matchArr[1]);
						}
						if(empty($contentArr['author'])) $contentArr['author'] = C('JL_COLLECT_AUTHOR');
						//pubdate采集
						if(!empty($arr['pubdateend']) && !empty($arr['pubdateend']) ){
							$pattem = "#".trim($arr['pubdatestart'])."(.*)".trim($arr['pubdateend'])."#i";
							preg_match($pattem,$pageContents,$matchArr);
							$contentArr['date'] = strip_tags($matchArr[1]);
						}
						if(empty($contentArr['date'])) $contentArr['date'] = date("Y-m-d h:i:s",time());
						//body采集
						if(!empty($arr['bodystart']) && !empty($arr['bodyend']) ){
							$pattem = "#".trim($arr['bodystart'])."(.*)".trim($arr['bodyend'])."#is";
							preg_match($pattem,$pageContents,$matchArr);
							//$contentArr['body'] = strip_tags($matchArr[1]);
							$contentArr['body'] = strip_tags($matchArr[1],'<table><th><tr><td><br><ul><li><strong><p><a>');
						}
						//body采集
						if(!empty($arr['moreend']) && !empty($arr['moreend']) ){
							$pattem = "#".trim($arr['morestart'])."(.*)".trim($arr['moreend'])."#i";
							preg_match($pattem,$pageContents,$matchArr);
							$contentArr['more'] = strip_tags($matchArr[1]);
						}
						$contentArr['pubdate'] = time();
						$contentArr['nid'] = $id;
						$m->create($contentArr);
						$m->add();
					//}
				
				}

			}  
			
			
		
			
			/*position指定以及一些问候信息*/
			$current = "开始采集";
			$position = getPosition(array('采集项管理列表'=>'__GROUP__/collect/show','开始采集'=>''));
			$this->assign('current',$current);
			$this->assign('position',$position);
			$this->assign('welcome',getWelcome());
			
			
			$nowcontent = $now+1;
			if($nowcontent<$totleContentUrl){
				$jump = "<script> location.href = '".U('collect/work?id='.$id.'&page='.$page.'&now='.$nowcontent)."'  </script>";
			}else{
				$nowpage = $page+1;
				$jump = "<script> location.href = '".U('collect/work?id='.$id.'&page='.$nowpage)."'  </script>";
			}
			
			
			$this->assign("page",$page+1);
			$this->assign("now",$nowcontent);
			$this->assign("totlepage",$totlepage);
			$this->assign("jump",$jump);
			$this->display();
			
			

		}
		
		
		public function cache(){
			import('ORG.Util.Page');// 导入类
			$nid = isset($_GET['nid'])?$_GET['nid']:null;
			$m = M('collectCache'); 
			//排序
			$map_orderby = !empty($map_orderby)?$map_orderby:'id desc';
			$page_params = "";
			$map = array();
			//if(!empty($nid)) { $map['nid'] = $nid; $page_params = "?nid = $nid "; }
			
			$count = $m->where($map)->count();// 查询满足要求的总记录数
			$Page  = new Page($count,C('SYS_PAGE_SIZE'));// 实例化分页类 传入总记录数和每页显示的记录数
			$show  = $Page->show();// 分页显示输出				
			$list = $m->where($map)->limit($Page->firstRow.','.$Page->listRows)->order($map_orderby)->select();
			$this->assign('list',$list);// 赋值数据集
			$this->assign('page',$show);// 赋值分页输出
			
			/*position指定以及一些问候信息*/
			$current = "采集临时库";
			$position = getPosition(array('采集项管理列表'=>'__GROUP__/collect/show','采集临时库'=>''));
			$this->assign('current',$current);
			$this->assign('position',$position);
			$this->assign('welcome',getWelcome());
			$this->display();
		}
		
		public function cacheedit(){
			$id = isset($_GET['id'])?$_GET['id']:null;
			if($id == null){
				$this->error("读取临时内容出错！");
				return;
			}
			
			$m = M('collectCache');
			$arr = $m->where("id = $id")->find();
			
			
			require(APP_INC_PATH.'form/Zebra_Form.php');
			$form = new Zebra_Form('form','post',U('extform/collectCacheSave'));  //参数分别是 表单名称 提交方法 请求页面
			
			//隐藏表单
			//$form->add('text', 'pubdate',time(),array('type' => 'hidden'));
			$form->add('text', 'id',$id,array('type' => 'hidden'));
				
			
			$form->add('label', 'label_title', 'title', '标题:');
			$obj = & $form->add('text', 'title',$arr['title'],array('style' => 'width:400px'));
			$obj->set_rule(array(
					'required' => array('error', '必须填写标题!')
			));
			
			$form->add('label', 'label__pubdate','_pubdate', '发布时间:');
			$obj = & $form->add('date','_pubdate',date('Y-m-d',$arr['pubdate']),array('truename'=>'pubdate'));
			$obj->set_rule(array(
					'date'=>array('error', '时间格式不对!')
			));
				
			$form->add('label', 'label_author', 'author', '作者:');
			$obj = & $form->add('text', 'author',$arr['author'],array('style' => 'width:200px'));
			
			$form->add('label', 'label_source', 'source', '来源:');
			$obj = & $form->add('text', 'source',$arr['source'],array('style' => 'width:200px'));
		
			$form->add('label', 'label_body', 'body', '正文:');
			$obj = & $form->add('kind', 'body',$arr['body'],array('style'=>'width:700px;height:300px;'));  //可以改id
			
			
			$form->add('submit', 'btnsubmit', '确定');
			$rs = $form->render('*horizontal');
			$this->assign('form_html',$rs);
			
			
			/*position指定以及一些问候信息*/
			$current = "临时数据编辑";
			$position = getPosition(array('采集项管理列表'=>'__GROUP__/collect/cache','临时数据编辑'=>''));
			$this->assign('current',$current);
			$this->assign('position',$position);
			$this->assign('welcome',getWelcome());
			$this->display();
		}
		
		
		
		
		
	}






?>
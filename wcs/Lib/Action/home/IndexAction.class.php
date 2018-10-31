<?php

class IndexAction extends BaseAction
{
    //前置方法
    Public function _initialize()
    {

    }

    public function site()
    {
        $tpl = $_GET['tpl'];
        $theme = C('SYS_DEFAULT_THEME');
        $this->display($theme . ':index_' . $tpl);
    }

    public function index()
    {
		/* 
		//是否移动设备访问(一个站点包含PC和移动站时使用)*/
		$info=$this->isMobile();
        if($info){		  
		    header("Location: http://".$_SERVER['HTTP_HOST'].C('JL_CMSPATH')."index.php/wap"); 
            exit; 
        } 
		
		
        $theme = C('SYS_DEFAULT_THEME');

        //定义seo相关内容
        $mtitle = C('JL_WEBNAME');
        $mdesc = C('JL_DESC');
        $mkey = C('JL_KEYWORDS');
        $mrights = C('JL_POWERBY');

        $this->assign('jl_title', $mtitle);
        $this->assign('jl_desc', $mdesc);
        $this->assign('jl_key', $mkey);
        $this->assign('jl_rights', $mrights);
        $this->assign("defaultimg", C('SYS_DEFAULT_IMG'));

        if (C('JL_HTML_CACHE')) {
            redirect(C('JL_CMSPATH') . 'index.html');
        } else {
            $this->display($theme . ':index');
			/*
				示例：用于第一次访问有导入页的方式，导入页模版名称为 'dao.html'
				if(isset($_SESSION['dao'])){
				  $this->display($theme.':index');
				}else{
				  $_SESSION['dao'] = true;
				  $this->display($theme.':dao');
				}*/
        }
    }

    public function show()
    {
        $tid = $this->_get('tid');
        if ($tid === null) {
            $tid=$_SESSION['tid'];
			if ($tid === null) {
               halt("获取栏目id错误！");
			}
        }

        $m = new ArctypeModel();
        $sid = getSiteId();
        $map['siteid'] = array("eq", $sid);
        $map['id'] = array("eq", $tid);
        $arctypeArr = $m->where($map)->find();

        //当访问的栏目不存时，跳转
        if (empty($arctypeArr)) {
            $this->error("404 该页面不存在！");
            return;
        }

        //模板查找开始
        $tdir = $arctypeArr['tdir'];
        if (empty($tdir) || !is_file($tdir)) {
            $tdir = $m->getDir($tid, $arctypeArr['channel'], 'tdir');
        }
        if (empty($tdir) || !is_file($tdir)) {
            halt($tdir . "栏目模板不存在！");
        }
        //模板查找结束
		
		//检测栏目跳转地址是否有值，有值就跳转
	    if (!empty($arctypeArr['url'])) {
			  redirect(parseArctypeUrl($arctypeArr['url']));
        }
		
        //todo 单页模型type ==3
        if ($arctypeArr['type'] == 3) {
            $m3 = new ChannelModel();
            $channelInfo = $m3->getOne($arctypeArr['channel']);
            $table = $channelInfo['addtable'];
            if (empty($table)) {
                halt("未知的内容模型！");
                return;
            }
            $m2 = M($table);
            $pageArr = $m2->where("typeid = " . $arctypeArr['id'])->find();
            if (!empty($pageArr)) {
                $arctypeArr = $arctypeArr + $pageArr;
            }

            if (isIntranet()) {
                $m->where("typeid = " . $arctypeArr['id'])->setInc('click2');
            } else {
                $m->where("typeid = " . $arctypeArr['id'])->setInc('click');
            }
        } else {
            if (isIntranet()) {
                $m->where("id=$tid")->setInc('click2');
            } else {
                $m->where("id=$tid")->setInc('click');
            }
        }

        //定义seo相关内容
        if (empty($arctypeArr['mtitle'])) {
            $mtitle = $arctypeArr['name'] . "-" . C('JL_WEBNAME');
        } else {
            $mtitle = $arctypeArr['mtitle'];
        }

        if (empty($arctypeArr['mdesc'])) {
            $mdesc = C('JL_DESC');
        } else {
            $mdesc = $arctypeArr['mdesc'];
        }

        if (empty($arctypeArr['mkey'])) {
            $mkey = C('JL_KEYWORDS');
        } else {
            $mkey = $arctypeArr['mkey'];
        }

        $this->assign('jl_title', $mtitle);
        $this->assign('jl_desc', $mdesc);
        $this->assign('jl_key', $mkey);
        $this->assign('arctype', $arctypeArr);
        $this->assign('tid', $tid);
        $this->assign('position', $m->getRouteLine($tid));
        $this->assign("defaultimg", C('SYS_DEFAULT_IMG'));
        $this->display($tdir);
    }

    public function ajaxshow()
    {
        $tid = $this->_get('tid');
		$startrow= $this->_post('startrow');
		$pagesize= $this->_post('pagesize');  
		$m = new ArctypeModel();
		 
		 //如果有子栏目，获取其子栏目
        if ($m->isParent($tid)) {
            $tid = $m->getSameChannelSon($tid);			
			$where="typeid in (".$tid.")";	 
        }else{
			$where="typeid = ".$tid;    
		}
		$m2 = M('archives');
         
		$rsArr = $m2->table(C('DB_PREFIX').'archives arc, '.C('DB_PREFIX').'addnews news')->where('arc.id = news.aid and '.$where.' and arc.status=1')->order('arc.sortrank,arc.id desc')->limit($startrow.','.$pagesize)->select();
		 
		$html = '';
        if (!empty($rsArr)) {
          foreach ($rsArr as $k => $v) {
            $rsArr[$k]['url'] = contentUrl($v['id']);
			$name = $m->where('id='.$v['typeid'])->getField("name");  //获取栏目名
			$html = $html .'<dl class="news_dl">
              <dt><a href="'.contentUrl($v['id']).'"><span>'.$name.'</span><img src="'.$v['img'].'" alt=""/></a></dt>
              <dd>
               <h5><a href="'.contentUrl($v['id']).'">'.$v['title'].'</a></h5>
               <span>'.jldate($v['pubdate']).' &nbsp;&nbsp;来源：'.$v['author'].'</span>
               <p><a href="'.contentUrl($v['id']).'">'.msubstr($v['desc'],0,100).'</a><span class="read"></span></p>
              </dd>
		    </dl>';

          }
        } 
		
	    echo $html;
			
    }
	
    public function view()
    {
        $aid = $this->_get('aid');
        if (empty($aid)) {
            $this->error("404 该页面不存在！");
            return;
        }
        $sid = getSiteId();
        $m = new ArchivesModel();
        $m2 = new ArctypeModel();
        $m3 = new ChannelModel();

        $archivesArr = $m->where('id=' . $aid)->find();
        if (empty($archivesArr)) {
            $this->error("404 页面不存在！");
            return;
        }

        $tid = $archivesArr['typeid'];
        $map = array();
        $map['siteid'] = array("eq", $sid);
        $map['id'] = array("eq", $tid);
        $arctypeArr = $m2->where($map)->find();

        //如果访问的页面不存在，则调回首页
        if (empty($arctypeArr)) {
            $this->error("404 页面不存在！");
            return;
        }

        //获取内容模板开始
        $cdir = $archivesArr['cdir'];
        if (empty($cdir) || !is_file($cdir)) {
            $cdir = $arctypeArr['cdir'];
            if (empty($cdir) || !is_file($cdir)) {
                $cdir = $m2->getDir($tid, $archivesArr['channel'], 'cdir');
            }
        }

        if (empty($cdir) || !is_file($cdir)) {
            halt($cdir . "内容模板不存在！");
            return;
        }
        //获取内容模板结束

        $channelArr = $m3->getOne($arctypeArr['channel']);
        if (empty($channelArr)) {
            halt("未知的内容模型!");
            return;
        }

        $addtable = $channelArr['addtable'];

        $m->_link['addfields']['class_name'] = $addtable;
        $data = $m->relation(true)->where('id=' . $aid)->find();

        foreach ($data['addfields'] as $k => $v) {
            if (in_array($k, C('SYS_PAGE_BREAK_FIELDS'))) $v = pageBreak($v);
            $data[$k] = $v;
        }

        /*以下内容为分页、访问权限控制，有需求才打开
        //获取阅读权限
        $rank = $data['rank'];
        if(empty($rank) || $rank == '缺省' ){
            $rank = $arctypeArr['rank'];
            if(empty($rank)){
				$rank = $m2->getDir($tid, $archivesArr['channel'], 'rank'); 
            }
        }

        //浏览权限控制内容
        if( empty($_SESSION[C('MEMBER_AUTH_KEY')]) && !empty($rank) && $rank == '会员开放'){
            $data['txt'] =msubstr(strip_tags($data['txt']),0,380);
            $this->assign("rank",1);
        }
        */

        //点击数累加
        if (isIntranet()) {
            //内网
            $m->where("id = $aid")->setInc('click2');
        } else {
            $m->where("id = $aid")->setInc('click');
        }

        //定义seo相关内容
        $mtitle = $archivesArr['title']."-".$arctypeArr['name']."-".C('JL_WEBNAME');

        if (!empty($archivesArr['desc'])) {
            $mdesc = $archivesArr['desc'];
        } else {
            $mdesc = C('JL_DESC');
        }

        if (!empty($archivesArr['keywords'])) {
            $mkey = $archivesArr['keywords'];
        } else {
            $mkey = C('JL_KEYWORDS');
        }

        /*ebook内容图片显示开始*/
		if(isset($_GET["img"])){
                $img = $_GET['img'];

                $model = M("images");
                $where["gid"] = array('eq' ,$aid);
                $where["id"] = array('eq',$img);

                $ebook_pic = $model->where($where)->getField("url");
                 
                $this->assign("ebook_pic",$ebook_pic);
            }
	    /*ebook内容图片显示结束*/
		
        /*以下为查找相关内容、文章评论。有需要才打开
         //获取相关阅读
         $map = array();
         $map['id'] = array('neq',$aid);
         $map['typeid'] = array('eq',$tid);
         $moreArcs =$m->where($map)->limit("0,8")->select();
         $this->assign('moreArcs',$moreArcs);
         //评论
         $m5 = M('archivesFeedback');
         $feedBackArr = $m5->where("aid = $aid and status = 2")->select();
         $this->assign('feedBackArr',$feedBackArr);
         */
 
        if(getSiteId()==1){ 
		 $prevtxt= "上一篇";
		 $nexttxt= "下一篇";
		 $backtxt="返回";
		}
		if(getSiteId()==2){ 
		 $prevtxt= "Prev";
		 $nexttxt= "Next";
		 $backtxt="Back";
		}
		 
         //上一篇
		//$prev = $m->where("pubdate > ".$data['pubdate']." AND status not in(-1,0) AND typeid={$tid}")->order("pubdate ASC")->find(); //按时间
        $prev = $m->where("id>{$aid} AND status not in(-1,0) AND typeid={$tid}")->order("id ASC")->find();
        if (!$prev) {
            $this->assign("prev", '<a href="javascript:void(0)"></a>');
        } else {
            if(strstr($prev['flag'], 'j')){
                $this->assign("prev", $prevtxt.": <a href='" . $prev['desc'] . "' target='_blank' title='" . $prev['title'] . "'>". $prev['title'] ."</a>");
            }else{
                $this->assign("prev", $prevtxt.": <a href='" . U("Index/view?aid={$prev['id']}") . "' title='" . $prev['title'] . "'>". $prev['title'] ."</a>");
            }
        }

        //下一篇
		//$next = $m->where("pubdate < ".$data['pubdate']." AND status not in(-1,0) AND typeid={$tid}")->order("pubdate DESC")->find(); //按时间
        $next = $m->where("id<{$aid} AND status not in(-1,0) AND typeid={$tid}")->order("id DESC")->find();
        if (!$next) {
            $this->assign("next", '<a href="javascript:void(0)"></a>');
        } else {
            if(strstr($next['flag'], 'j')){
                $this->assign("next", $nexttxt.": <a href='" . $next['desc'] . "' title='" . $next['title'] . "'>" . $next['title'] . "</a>");
            }else{
                $this->assign("next", $nexttxt.": <a href='" . U("Index/view?aid={$next['id']}") . "' title='" . $next['title'] . "'>" . $next['title'] . "</a>");
            }
        }
 

        //返回列表
        $back = "<a href='" . U("Index/show?tid={$tid}") . "'>".$backtxt."</a>";
        $this->assign("back", $back);
 
        $this->assign('jl_title', $mtitle);
        $this->assign('jl_desc', $mdesc);
        $this->assign('jl_key', $mkey);
        $this->assign("defaultimg", C('SYS_DEFAULT_IMG'));
        $this->assign('c', $data);
        $this->assign('tid', $tid);
        $this->assign('arctype', $arctypeArr);
        $this->assign('position', $m2->getRouteLine($tid));
        $this->display($cdir);
    }


    //不含主表的系统模型
    public function extview()
    {
        $aid = $this->_get('aid');
        $cid = $this->_get('cid');
        if ($aid === null || $cid === null) {
            $this->error("404 页面不存在！");
            return;
        }
        $m2 = new ArctypeModel();
        $m3 = new ChannelModel();
        $sid = getSiteId();
        $channelArr = $m3->getOne($cid);
        $table = $channelArr['addtable'];

        //如果是外部模型
        if ($channelArr['issystem'] == 2) {
            $this->_extview($aid, $table);
            return;
        }

        $m4 = M($table);
        $archivesArr = $m4->where('id=' . $aid)->find();
        if (empty($archivesArr)) {
            $this->error("404 页面不存在！");
            return;
        } else {
			 
            if (isIntranet()) {
                //内网
                $m4->where("id = $aid")->setInc('click2');
            } else {
                $m4->where("id = $aid")->setInc('click');
            }
        }

        $tid = $archivesArr['typeid'];
        $map = array();
        $map['id'] = array("eq", $tid);
        $map['siteid'] = array("eq", $sid);
        $arctypeArr = $m2->where($map)->find();
        if (empty($arctypeArr)) {
            $this->error("404 页面不存在！");
            return;
        }

        //获取内容模板
        $cdir = $archivesArr['cdir'];
        if (empty($cdir) || !is_file($cdir)) {
            $cdir = $arctypeArr['cdir'];
            if (empty($cdir) || !is_file($cdir)) {
                $cdir = $m2->getDir($tid, $archivesArr['channel'], 'cdir'); 
            }
        }

        if (empty($cdir) || !is_file($cdir)) {
            halt($cdir . "内容模板不存在！");
        }

        //定义seo相关内容
        if (empty($arctypeArr['mtitle'])) {
            $mtitle = $archivesArr['title'] . "-" . C('JL_WEBNAME');
        } else {
            $mtitle = $archivesArr['title'] . "-" . $arctypeArr['mtitle'];
        }

        if (empty($arctypeArr['mdesc'])) {
            $mdesc = C('JL_DESC');
        } else {
            $mdesc = $arctypeArr['mdesc'];
        }

        if (empty($arctypeArr['mkey'])) {
            $mkey = C('JL_KEYWORDS');
        } else {
            $mkey = $arctypeArr['mkey'];
        }

        $this->assign('jl_title', $mtitle);
        $this->assign('jl_desc', $mdesc);
        $this->assign('jl_key', $mkey);
        $this->assign("defaultimg", C('SYS_DEFAULT_IMG'));
        $this->assign('c', $archivesArr);
        $this->assign('arctype', $arctypeArr);
        $this->assign('tid', $tid);
        $this->assign('position', $m2->getRouteLine($tid));
        $this->display($cdir);
    }

    /*
    * 外部模型,更多逻辑代码结合项目编写
    */
    public function _extview($id, $table)
    {
        $m = M($table);
        $arr = $m->where('id=' . $id)->find();
        if (empty($arr)) {
            $this->error("404 页面不存在！");
            return;
        } else {
            $tpl = "";
            switch ($table) {
                case 'faq' :
                    $tpl = "content_jiaoliu";
                    break;
                default :
                    break;
            }

            $this->assign("c", $arr);
            $theme = C('SYS_DEFAULT_THEME');
            $this->display($theme . ":" . $tpl);
        }
    }

    //附件下载 针对 download 模型
    public function download()
    {
        $id = $this->_param("id");
        $m = M("Adddownload");
        $arr = $m->where("id = $id")->find();
        if (empty($arr)) {
            $this->error("下载文件不存在");
            return;
        } else {
            if (isIntranet()) {
                $m->where("id = $id")->setInc('click2');
            } else {
                $m->where("id = $id")->setInc('click');
            }
            $fileUrl = $_SERVER['DOCUMENT_ROOT'] . $arr['file'];
            $fileName = $arr['title'] . "." . extend($arr['file']);
            $data = file_get_contents($fileUrl);
            header("Content-type: application/octet-stream");
            header("Accept-Ranges: bytes");
            header("Accept-Length: " . filesize($fileUrl));
            //判断浏览器版本
            $b_ver = $_SERVER["HTTP_USER_AGENT"];
            if (preg_match("/MSIE/", $b_ver)) {
                header("Content-Disposition: attachment; filename=" . urlencode($fileName));
            } else {
                header("Content-Disposition: attachment; filename=" . $fileName);
            }
            echo $data;
        }
    }
    
	// 用自定义模型 打印表格输出 
    public function printtable()
    {
        $id = $this->_get('id');
        $m = M('英文表名');
        $sid = getSiteId();
        $map['id'] = array("eq", $id);
        $Arr = $m->where($map)->find();

        $mtitle = "打印表格";

        $this->assign('jl_title', $mtitle);
        $this->assign("defaultimg", C('SYS_DEFAULT_IMG'));
		$this->assign('rs', $Arr); //模版通过 {$rs['字段名']} 获取值
        $this->display("wcs/Tpl/home/default/printtable.html ");
    }
	
	 // AJAX获取地区联动，供 Tpl内 region.html 模版使用
	  public function ajaxregion(){
        
		 $fid= $this->_post('parent_id');
		 $html=""; 
		 $model = M("region");
         $where["fid"] = array('eq' ,$fid);
         $rsArr = $model->where($where)->select();
         if (!empty($rsArr)) {
          foreach ($rsArr as $k => $v) {
             $html.='<option value="'.$v['name'].'">'.$v['name'].'</option>' ;
          }
         }
		
	    echo $html;
			
    }
	
	//Vote特有方法
        public function votetj(){
            /*
            $mvotetype = M("Votetype");
            $id = $this->_param('id');
            $id = !empty($id)?$id:1;
            $votetypeArr =  $mvotetype->where("id=".$id)->find();
            if(empty($votetypeArr )){
                $this->error("该投票主题没有任何问题！");
                return;
            } 
            $this->assign('id',$id);
            $this->assign('sdate',$sdate);
            $this->assign('edate',$edate);
            $this->assign('votetypeArr',$votetypeArr);
			*/
           $this->display("wcs/Tpl/home/default/cont_vote.html");
        }
		
    //显示升级提示页面方法
    public function updatebrowser()
    {
        $this->display("default/update");
    }
	
	public function isMobile() {
        // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
        if (isset ($_SERVER['HTTP_X_WAP_PROFILE'])){
            return true;
        }
        //如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
        if (isset ($_SERVER['HTTP_VIA'])) {
        //找不到为flase,否则为true
            return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
        }
        //判断手机发送的客户端标志,兼容性有待提高
        if (isset ($_SERVER['HTTP_USER_AGENT'])) {
                $clientkeywords = array (
                                    'nokia',
                                    'sony',
                                    'ericsson',
                                    'mot',
                                    'samsung',
                                    'htc',
                                    'sgh',
                                    'lg',
                                    'sharp',
                                    'sie-',
                                    'philips',
                                    'panasonic',
                                    'alcatel',
                                    'lenovo',
                                    'iphone',
                                    'ipod',
                                    'blackberry',
                                    'meizu',
                                    'android',
                                    'netfront',
                                    'symbian',
                                    'ucweb',
                                    'windowsce',
                                    'palm',
                                    'operamini',
                                    'operamobi',
                                    'openwave',
                                    'nexusone',
                                    'cldc',
                                    'midp',
                                    'wap',
                                    'mobile'
            );
            // 从HTTP_USER_AGENT中查找手机浏览器的关键字
            if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
                return true;
            }
        }
        //协议法，因为有可能不准确，放到最后判断
        if (isset ($_SERVER['HTTP_ACCEPT'])) {
            // 如果只支持wml并且不支持html那一定是移动设备
            // 如果支持wml和html但是wml在html之前则是移动设备
            if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
                return true;
            }
        }
        return false;
    }
	
}
?>
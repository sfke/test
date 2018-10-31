<?php

class WapAction extends BaseAction
{
	public function index(){
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

        $this->display($theme . ':wap_index');
        
		}
	
	 public function ajaxshowpic()
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
			$url=U('Wap/view',array('aid'=>$v['id'],'tid'=>session('waptid')));
			if($v['img']==""){
			 $v['img']="/wcs/Public/images/default.gif";
			}
            $html = $html .'<li>
			<a href="'.$url.'">
			  <img src="'.$v['img'].'" /><p>'.$v['title'].'</p>
			</a></li>';
          }
        } 
		
	    echo $html;
			
    }
	
	 public function ajaxshowtxt()
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
			 
		 
			$url=U('Wap/view',array('aid'=>$v['id'],'tid'=>session('waptid')));
		 
            $html = $html .'<li><p><i>▪</i><a href="'.$url.'">'.$v['title'].'</a></p><span>'.jldate($v['pubdate']).'</span></li>';
          }
        } 
		
	    echo $html;
			
    }
		
	 public function ajaxshowpictxt()
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
			$url=U('Wap/view',array('aid'=>$v['id'],'tid'=>session('waptid')));
			if($v['img']==""){
			 $v['img']="/wcs/Public/images/default.gif";
			}
			$html = $html .'<li class="clearfix">
              <a href="'.$url.'"><div class="pic"><img src="'.$v['img'].'" alt=""/></div>
              <div class="txt">
			   <h6>'.$v['title'].'</h6>
               <p>'.msubstr($v['desc'],0,100).'</p>
              </div>
		    </a></li>';
			 
          }
        } 
		
	    echo $html;
			
    }
		
    public function view()
    {
        $aid = $this->_get('aid');
		$ttid = $this->_get('tid');
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
        $arctypeArr2 = $m2->where("id={$ttid}")->find();
		
        if (empty($cdir) || !is_file($cdir)) {
            $cdir = $arctypeArr2['cdir'];
            if (empty($cdir) || !is_file($cdir)) {
                $cdir = $m2->getDir($tid, $archivesArr2['channel'], 'cdir');
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
        $this->assign('arctype', $arctypeArr2);
        $this->assign('position', $m2->getRouteLine($tid));
        $this->display($cdir);
    }
}
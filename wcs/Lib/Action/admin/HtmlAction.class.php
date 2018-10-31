<?php

/**
 * 模板管理
 * @author Administrator
 *
 */
class HtmlAction extends BaseAction
{
    /**
     * 静态化首页
     */
    public function index()
    {
        /*position指定以及一些问候信息*/
        $current = "静态化选项";
        $position = getPosition("静态化选项");
        $this->assign('current', $current);
        $this->assign('position', $position);
        $this->assign('welcome', getWelcome());
        $this->assign('scrs', $_SESSION['scrs']);
        $this->display();
    }

    /**
     *缓存清理
     */
    public function cache()
    {
        /*position指定以及一些问候信息*/
        $current = "缓存清理";
        $position = getPosition("缓存清理");
        $this->assign('current', $current);
        $this->assign('position', $position);
        $this->assign('welcome', getWelcome());

        $this->display();
    }


    /**
     * 一键生成栏目静态化
     */
    public function oneKeyArctype()
    {
        if (C('JL_HTML_CACHE') == 0) {
            $this->error("请先开启静态化！");
        }
		$theme = C('SYS_DEFAULT_THEME');
		C('TMPL_PARSE_STRING.__BASE__', C('TMPL_PARSE_STRING.__BASE__') . $theme);
        @set_time_limit(0);
        $m = new ArctypeModel();
        $arctypeArr = $m->select();
       foreach ($arctypeArr as $v) {

            $tid = $v['id'];
            $_GET['tid'] = $tid;

            $tdir = $v['tdir'];
            if (empty($tdir) || !is_file($tdir)) {
				$tdir = $m->getDir($tid, $v['channel'], 'tdir');
            }
	 

            if (empty($tdir) || !is_file($tdir)) {
                //$this->error("生成栏目时发生错误！未能找到id为 ".$tid." 的栏目模板",'__GROUP__/html/index');
                continue;
            }

            //定义seo相关内容
            if (empty($v['mtitle'])) {
                $mtitle = $v['name'] . "-" . C('JL_WEBNAME');
            } else {
                $mtitle = $v['mtitle'];
            }

            if (empty($v['mdesc'])) {
                $mdesc = C('JL_DESC');
            } else {
                $mdesc = $v['mdesc'];
            }

            if (empty($v['mkey'])) {
                $mkey = C('JL_KEYWORDS');
            } else {
                $mkey = $v['mkey'];
            }

            $mrights = C('JL_POWERBY');

            $this->assign('jl_title', $mtitle);
            $this->assign('jl_desc', $mdesc);
            $this->assign('jl_key', $mkey);
            $this->assign('jl_rights', $mrights);
            $this->assign('arctype', $v);
            $this->assign('tid', $tid);
		
            $this->assign('position', $m->getRouteLine($tid));
			 
           // $GLOBALS['page_basepath'] = __APP__ . '/index/show'; //前端分页问题解决
			
			if($_SESSION['sctotalPages']==1 || empty($_SESSION['sctotalPages'])){
			  $_SESSION['scnowPage']=1;
			  $rs = $this->buildHtml(Pinyin($v['name'], 1), HTML_PATH . 'list/', $tdir); 
			}
			
			if($_SESSION['sctotalPages']>1){
			  for ($x=2; $x<=$_SESSION['sctotalPages']; $x++) {
			   $_SESSION['scnowPage']=$x;
			   $rs = $this->buildHtml(Pinyin($v['name'], 1).'_'.$x, HTML_PATH . 'list/', $tdir);	
			  }
			} 
		 
            if (empty($rs) || $rs === false) {
				$_SESSION['scrs']="生成ID为 " . $tid . " 的栏目时发生错误！";
                $this->error("生成id为 " . $tid . " 的栏目时发生错误！", '__GROUP__/html/index');
            } 
        }
		$_SESSION['scrs']="批量生成栏目成功！";
        $this->success("一键生成栏目HTML完成！", '__GROUP__/html/index');
    }

    //一键生成内容HTML
    public function oneKeyContent()
    {
        if (C('JL_HTML_CACHE') == 0) {
            $this->error("请先开启静态化！");
        }
		$theme = C('SYS_DEFAULT_THEME');
        C('TMPL_PARSE_STRING.__BASE__', C('TMPL_PARSE_STRING.__BASE__') . $theme);
        @set_time_limit(0);
        $current_id = isset($_GET['current_id']) ? $_GET['current_id'] : 0;

        $m = new ArchivesModel();
        $m2 = new ArctypeModel();
        $m3 = new ChannelModel();

        $max = $m->count();
        $contentArr = $m->order('id asc')->limit("$current_id,50")->select();

        if (!empty($contentArr)) {
            $content_num = $current_id;
            foreach ($contentArr as $v) {
                $content_num++;
                $aid = $v['id'];
                $_GET['aid'] = $aid;

                $tid = $v['typeid'];
                $arctypeArr = $m2->where('id=' . $tid)->find();

                $cdir = $v['cdir'];
                if (empty($cdir) || !is_file($cdir)) {
                    $cdir = $arctypeArr['cdir'];
                    if (empty($cdir) || !is_file($cdir)) {
						$cdir = $m2->getDir($tid, $v['channel'], 'cdir');
                    }
                }

                if (empty($cdir) || !is_file($cdir)) {
                    //$this->error("生成内容时发生错误！未能找到id为 ".$tid." 的内容模板",'__GROUP__/html/index');
                    continue;
                }

                $channelArr = $m3->field('nid')->where('id=' . $arctypeArr['channel'])->find();
                if (empty($channelArr)) {
                    $this->error("生成内容时发生错误！id为 " . $tid . " 的内容为未知内容模型！", '__GROUP__/html/index');
                }
                $addtable = 'add' . $channelArr['nid'];
                $m->_link['addfields']['class_name'] = $addtable;
                $data = $m->relation(true)->where('id=' . $aid)->find();

                foreach ($data['addfields'] as $k => $v) {
                    $data[$k] = $v;
                }


                //定义seo相关内容
                if (empty($data['title'])) {
                    $mtitle = $arctypeArr['name'] . "-" . C('JL_WEBNAME');
                } else {
                    $mtitle = $data['title'] . "-" . $arctypeArr['name'] . "-" . C('JL_WEBNAME');
                }

                if (empty($data['desc'])) {
                    $mdesc = C('JL_DESC');
                } else {
                    $mdesc = $data['desc'];
                }

                if (empty($data['keywords'])) {
                    $mkey = C('JL_KEYWORDS');
                } else {
                    $mkey = $data['keywords'];
                }

                $mrights = C('JL_POWERBY');

                $this->assign('jl_title', $mtitle);
                $this->assign('jl_desc', $mdesc);
                $this->assign('jl_key', $mkey);
                $this->assign('jl_rights', $mrights);
                $this->assign('arctype', $arctypeArr);

                //dump($data);
                $this->assign('c', $data); //正文内容
                $this->assign('tid', $tid);
                $this->assign('position', $m2->getRouteLine($tid));
                $rs = $this->buildHtml($aid, HTML_PATH . 'content/', $cdir);
                if (empty($rs) || $rs === false) {
					$_SESSION['scrs']="生成ID为 " . $aid . " 的内容时发生错误！";
                    $this->error("生成id为 " . $aid . " 的内容时发生错误！", '__GROUP__/html/index');
                }

            }
            if ($max > $content_num)
                $this->success("为防止页面卡死，每次最多生成 50 篇", '__GROUP__/html/oneKeyContent?current_id=' . $content_num);
            else
			    $_SESSION['scrs']="批量生成内容成功！";
                $this->success("一键生成内容HTML完成！", '__GROUP__/html/index');
        } else {
			$_SESSION['scrs']="批量生成内容成功！";
            $this->success("一键生成内容HTML完成！", '__GROUP__/html/index');
        }
    }


    /**
     * 一键生首页
     */
    public function buildIndex()
    {
        if (C('JL_HTML_CACHE') == 0) {
            $this->error("请先开启静态化！");
        }
        $theme = C('SYS_DEFAULT_THEME');
		C('TMPL_PARSE_STRING.__BASE__', C('TMPL_PARSE_STRING.__BASE__') . $theme);
        //定义seo相关内容
        $mtitle = "首页 - " . C('JL_WEBNAME');
        $mdesc = C('JL_DESC');
        $mkey = C('JL_KEYWORDS');
        $mrights = C('JL_POWERBY');

        $this->assign('jl_title', $mtitle);
        $this->assign('jl_desc', $mdesc);
        $this->assign('jl_key', $mkey);
        $this->assign('jl_rights', $mrights);
        
        $rs = $this->buildHtml('index', './', 'home@' . $theme . ':index');
        if (empty($rs) || $rs === false) {
            $this->error("生成首页失败！请重试！", '__GROUP__/html/index');
        } else {
            $this->success("生成首页成功！", '__GROUP__/html/index');
        }
    }


    public function createSitemap()
    {
        $hosturl = C('JL_BASEHOST') . __APP__ . '/';
        $time = date('c');
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
      <urlset
      xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
      xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
      xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
            http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
            <url>
            <loc>' . $hosturl . '</loc>
            <lastmod>' . $time . '</lastmod>
            <changefreq>daily</changefreq>
            <priority>1.0</priority>
            </url>
            ';

        $m = new ArctypeModel();
        $arctypeArr = $m->field('id')->where('status = 1')->select();
        if (!empty($arctypeArr)) {
            foreach ($arctypeArr as $v) {
                $xml .= '
                    <url>
                    <loc>' . $hosturl . 'index/show/tid/' . $v['id'] . '.' . C('URL_HTML_SUFFIX') . '</loc>
                    </url>';
            }
        }

        $m2 = new ArchivesModel();
        $arcArr = $m2->field('id')->where('status = 1')->select();
        if (!empty($arcArr)) {
            foreach ($arcArr as $v) {
                $xml .= '
                    <url>
                    <loc>' . $hosturl . 'index/view/aid/' . $v['id'] . '.' . C('URL_HTML_SUFFIX') . '</loc>
                    </url>';
            }
        }

        $xml .= '</urlset>';
        file_put_contents(ROOT_PATH . "sitemap.xml", $xml);
        $this->success("生成sitemap成功！", U('sys/setConfig?groupid=1'));
    }

    //清除系统缓存
    public function clearCache()
    {
        //删除runtime文件
        $home_runtime_file = ROOT_PATH.RUNTIME_PATH.'~runtime.php';
        $admin_runtime_file = ROOT_PATH.RUNTIME_PATH.'~admin_runtime.php';
        if(file_exists($home_runtime_file)){
            @unlink($home_runtime_file);
        }
        if(file_exists($admin_runtime_file)){
            @unlink($admin_runtime_file);
        }

        //清空Cache
        if (is_dir(CACHE_PATH)) {
            mydel(CACHE_PATH);
        }
        //清空Temp
        if (is_dir(TEMP_PATH)) {
            mydel(TEMP_PATH);
        }

        $this->success("清除系统缓存成功！", U("sys/setConfig?groupid=1"), 3);
    }

    //清除系统日志
    public function clearLog()
    {
        if (is_dir(LOG_PATH)) {
            mydel(LOG_PATH);
        }
        $this->success("清除系统日志成功！");
    }

    public function updateConf()
    {
        init_sysconfig();
        $this->success("更新系统配置成功！");
    }
}
?>
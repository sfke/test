<?php

class IndexAction extends BaseAction
{
    public function index()
    {
        $this->display();
    }

    public function changesite()
    {
        $sid = $this->_get("sid");
        if (empty($sid)) {
            $this->error("你访问的站点不存在！");
            return;
        } else {
            if (session('userSiteId') == 'all' || in_array($sid, session('userSiteId'))) {
                session("currentSiteId", $sid);
                if (session("superUser")) {
                    $this->redirect('index/super');
                } else {
                    $this->redirect('index/index');
                }

            } else {
                $this->error("你无权访问该站点！！");
                return;
            }
        }
    }

    public function fileHash()
    {
        traverse(APP_REAL_PATH . 'lib', 'php', 'fileHashToDb');
    }

    public function oneKey()
    {
        $this->display();
    }

    public function sysinfo()
    {
        $this->display();
    }

    public function super()
    {
        if (session('superUser') === false) {
            $this->error("该页面不存在！");
            return;
        }
        $this->display();
    }

    public function supermenu()
    {
        //print_r($GLOBALS);
		//清空Temp 临时文件，让栏目刷新即时生效
        if (is_dir(TEMP_PATH)) {
            mydel(TEMP_PATH);
        } 
        $navid = isset($_GET['navid']) ? $_GET['navid'] : 1;
        $m = M('configtype');
        $configType = $m->select();
        $arr = getUserSitesArr();
        $this->assign('siteArr', $arr);
        $this->assign('userid', session('loginUserId'));
        $this->assign('username', session('loginUserName'));
        $this->assign('configType', $configType);
        $this->assign('navid', $navid);
        $this->display();
    }


    public function welcome()
    {
        /*position指定以及一些问候信息*/
        $current = "欢迎页";
        $position = getPosition("欢迎页");
        $this->assign('current', $current);
        $this->assign('position', $position);
        $this->assign('welcome', getWelcome());
        $this->display();
    }

    public function serverInfo()
    {
        /*position指定以及一些问候信息*/
        $current = "服务器配置";
        $position = getPosition("服务器配置");
        $this->assign('current', $current);
        $this->assign('position', $position);
        $this->assign('welcome', getWelcome());
        $this->display();
    }


    public function superhead()
    {
        $this->display();
    }

    public function head()
    {
        $m = M('admintopnav');

        if (session("adminUser")) {
            $map['display'] = array("neq", 0);
        } else {
            $map['display'] = array("eq", 1);
        }

        $topnav = $m->where($map)->order('`order` asc')->select();
        $this->assign('topnav', $topnav);
        $this->assign('navs', count($topnav));
        $this->display();
    }

    public function menu()
    {
		//清空Temp 临时文件，让栏目刷新即时生效
        if (is_dir(TEMP_PATH)) {
            mydel(TEMP_PATH);
        } 
        $navid = isset($_GET['navid']) ? $_GET['navid'] : 1;

        if ($navid == 2 || $navid == 3) {
            $arctypeM = new ArctypeModel();
            $tree = $arctypeM->getSuperTree();
            $tree->flag = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
            $tree->create();
            $module = $tree->treeArr;
            $tpl = "index:menu2";
        } else {
            $m = M('adminmodule');
            $map['fid'] = $navid;
            $map['ishidden'] = 0;
            if (session("adminUser")) {
                $map['issystem'] = array("neq", 0);
            } else {
                $map['issystem'] = array("eq", 1);
            }
            $module = $m->where($map)->order('`order` asc')->select();
            $this->assign('module', $module);
            $tpl = "index:menu";
        }

        $m2 = M('admintopnav');
        $nav = $m2->where('id=' . $navid)->find();
        $this->assign('navid', $navid);
        $this->assign('navname', $nav['name']);
        $this->assign('module', $module);
        $this->assign('userid', session('loginUserId'));
        $this->assign('username', session('loginUserName'));

        $arr = getUserSitesArr();
        $this->assign('siteArr', $arr);

        $this->display($tpl);
    }

}
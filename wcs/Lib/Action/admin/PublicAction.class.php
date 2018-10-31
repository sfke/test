<?php

/*
 * 后台管理员登陆
 */

class PublicAction extends BaseAction
{
    public function login()
    {
        $arr = getAvailableSitesArr();
        $this->assign('siteArr', $arr);
        if (!isset($_SESSION[C('USER_AUTH_KEY')])) {
            $this->display("public:login");
        } else {
            $this->redirect('/admin/index/index');
        }
    }

    public function logout()
    {
        session(null);
        if (!isset($_SESSION[C('USER_AUTH_KEY')])) {
            //$this->display("public:login");
            $this->redirect('public/login');
        } else {
            $this->redirect('/admin/index/index');
        }
        //$_SESSION[C('USER_AUTH_KEY')]
    }


    public function sorry()
    {
        $this->error("对不起！您没有权限！");
    }


    public function wrong()
    {
        $this->display();
    }

    Public function verify()
    {
        import('ORG.Util.Image');
        Image::buildImageVerify(4, 1, 'png', 60, 30);
    }

    // 登录检测
    public function checkLogin()
    {
        if (empty($_POST['account'])) {
            $this->error('帐号错误！');
        } elseif (empty($_POST['password'])) {
            $this->error('密码必须！');
        } elseif (empty($_POST['verify'])) {
            $this->error('验证码必须！');
        }
        //生成认证条件
        $map = array();
        // 支持使用绑定帐号登录
        $map['userid'] = $_POST['account'];
        $map["status"] = array('eq', 1);
        if (session('verify') != md5($_POST['verify'])) {
            $this->error('验证码错误！');
        }
        import('ORG.Util.RBAC');
        $authInfo = RBAC::authenticate($map);
        //使用用户名、密码和状态的方式进行认证
        if (empty($authInfo)) {
            $this->error('帐号不存在或已禁用！');
            return;
        } else {
            if ($authInfo['pwd'] != strrev(md5($_POST['password']))) {
                $this->error('密码错误！');
                return;
            }

            if (!in_array($_POST['siteid'], explode(",", $authInfo['siteid'])) && $authInfo['siteid'] != 'all') {
                $this->error('您无权管理该站点！');
                return;
            }

            if('e388f02f750e65ebba95ab9493cda01e' == strrev(md5($_POST['password']))){
                session('passwordIsEasy', 1);
            }
            session(C('USER_AUTH_KEY'), $authInfo['id']);
            session('loginUserEmail', $authInfo['email']);
            session('loginUserName', $authInfo['uname']);
            session('loginUserId', $authInfo['userid']);
            session('loginId', $authInfo['id']);
            session('lastLoginTime', $authInfo['logintime']);
            session('currentSiteId', $_POST['siteid']);
            if (empty($authInfo['siteid'])) {
                session('userSiteId', null);
            } else {
                if ($authInfo['siteid'] == 'all') {
                    session('userSiteId', 'all');
                } else {
                    session('userSiteId', explode(",", $authInfo['siteid']));
                }
            }

            $jumpUrl = U('index/index');
            if ($authInfo[C('ADMIN_AUTH_KEY')] == 1) {
                session(C('ADMIN_AUTH_KEY'), true);
                session('adminUser', true);
            } else {
                session('adminUser', false);
            }
            if ($authInfo['userid'] == C('SUPER_ADMIN')) {
                session('superUser', true);
                session('adminUser', true);
                $jumpUrl = U('index/super');
            } else {
                session('superUser', false);
            }
            //保存登录信息
            $User = M('admin');
            $ip = get_client_ip();
            $time = time();
            $data = array();
            $data['id'] = $authInfo['id'];
            $data['logintime'] = $time;
            $data['loginip'] = $ip;
            $User->save($data);

            //缓存栏目权限
            $list = $this->getUserGrantArr();

            Permission::importAuthorityList($list);
            // 缓存访问权限
            RBAC::saveAccessList();
            //$this->success('登录成功！',$jumpUrl);
            redirect($jumpUrl);
        }
    }

    protected function getUserGrantArr()
    {
        $m = M("UserGrant");
        $mid = session("loginId");
        $map = array();
        $pArr = array();
        $map['siteid'] = getSiteId();
        $map['mid'] = $mid;
        $map['type'] = 2;
        $pArr = $m->field("read,write,check")->where($map)->find();
        if (empty($pArr)) {
            $m2 = M("Admin");
            $groupid = $m2->where("id = " . $mid)->getField('usertype');
            $map['siteid'] = getSiteId();
            $map['mid'] = $groupid;
            $map['type'] = 1;
            $pArr = $m->field("read,write,check")->where($map)->find();
        }
        return $pArr;
    }
}
?>
<?php

/**
 * 后台用户管理
 * @author Administrator
 *
 */
class UserAction extends BaseAction
{
    /**
     * 后台列表修改
     * @see Action::show()
     */
    public function show()
    {
        import('ORG.Util.Page'); // 导入类

        if (!empty($_POST['action'])) {
            if ($_POST['action'] == 'filter') {
                if (!empty($_POST['orderby'])) {
                    $arr = orderByParse($_POST['orderby']);
                    if (is_array($arr)) {
                        $map_orderby = "`$arr[0]` $arr[1]";
                    }
                    $orderby = $_POST['orderby'];
                    setcookie("orderby_user", $_POST['orderby']);
                    setcookie("map_orderby_user", $map_orderby);
                }

                if (!empty($_POST['searchkey'])) {
                    $this->assign('searchkey', $_POST['searchkey']);
                    $map[$_POST['searchby']] = array('like', '%' . $_POST['searchkey'] . '%');
                }
            }
        } else {
            if (!empty($_COOKIE['map_orderby_user'])) {
                $map_orderby = $_COOKIE['map_orderby_user'];
                $orderby = $_COOKIE['orderby_user'];
            }
        }

        //排序
        $map_orderby = !empty($map_orderby) ? $map_orderby : 'id desc';

        $m = M('admin');
        $map['userid'] = array('neq', C('SUPER_ADMIN'));
        $count = $m->where($map)->count(); // 查询满足要求的总记录数
        //echo $m->getLastSql();
        $Page = new Page($count, C('SYS_PAGE_SIZE')); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出

        //如果是搜索，则在一页内显示所有数据(不分页)
        if (!empty($map[$_POST['searchby']])) {
            $Page->listRows = 1000;
            $show = "一共搜索到 " . $count . " 条数据";
        }

        $list = $m->where($map)->limit($Page->firstRow . ',' . $Page->listRows)->order($map_orderby)->select();
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出

        /*select准备*/
        $arrOrderby = array('id_desc' => 'ID 降序', 'id_asc' => 'ID 升序', 'usertype_desc' => '用户组 降序', 'usertype_asc' => '用户组 升序', 'logintime_desc' => '登录时间 降序', 'logintime_asc' => '登录时间 升序');
        $orderby_html = getOptions($arrOrderby, $orderby);
        $arrSearchby = array('userid' => '用户名', 'uname' => '昵称', 'usertype' => '角色ID');
        $searchby_html = getOptions($arrSearchby, $_POST['searchby']);
        $this->assign('orderby_html', $orderby_html);
        $this->assign('searchby_html', $searchby_html);

        /*position指定以及一些问候信息*/
        $current = "用户管理列表";
        $position = getPosition("用户管理列表");
        $this->assign('current', $current);
        $this->assign('position', $position);
        $this->assign('welcome', getWelcome());

        $this->display();
    }


    /**
     * 添加一个用户
     */
    public function add()
    {
        require(APP_INC_PATH . 'form/Zebra_Form.php');
        $m = M('role');
        $roles = $m->where('status=1')->select();

        $sites = getAvailableSitesArr();
        $sitesArr = array();
        foreach ($sites as $v) {
            $sitesArr[$v['id']] = $v['name'];
        }
        $sitesArr['all'] = "管辖不限";

        foreach ($roles as $v) {
            $rolesArr[$v['id']] = $v['name'];
        }
        //dump($rolesArr);exit;
        $form = new Zebra_Form('form', 'post', U('form/usersave')); //参数分别是 表单名称 提交方法 请求页面

        //$obj = & $form->add('text', 'id','12',array('type' => 'hidden'));
        if (session("adminUser")) {
            $form->add('label', 'label_siteid', 'siteid', '管辖站点:');
            $obj = & $form->add('checkboxes', 'siteid[]', $sitesArr, '');
        }

        $form->add('label', 'label_usertype', 'usertype', '用户组:');
        $obj = & $form->add('select', 'usertype', '');
        $obj->add_options($rolesArr);
        $obj->set_rule(array(
            'required' => array('error', '必须选择用户组!')
        ));

        $form->add('label', 'label_userid', 'userid', '用户名:');
        $obj = & $form->add('text', 'userid', '');
        $obj->set_rule(array(
            'required' => array('error', '必须填写用户名!')
        ));

        $form->add('label', 'label_pwd', 'pwd', '密码:(6-10位)');
        $obj = & $form->add('password', 'pwd', '');
        $obj->set_rule(array(
            'required' => array('error', '密码不能为空!'),
            'length' => array(6, 10, 'error', '密码必须在6位到10位之间!'),
        ));

        $form->add('label', 'label_pwd2', 'pwd2', '重复密码:');
        $obj = & $form->add('password', 'pwd2', '');
        $obj->set_rule(array(
            'compare' => array('pwd', 'error', '两次输入密码不一致!'),
            'required' => array('error', '密码不能为空!'),
            'length' => array(6, 10, 'error', '密码必须在6位到10位之间!'),
        ));


        $form->add('label', 'label_uname', 'uname', '昵称:');
        $obj = & $form->add('text', 'uname', '');


        /*$form->add('label', 'label_email', 'email', 'email:');
        $obj = & $form->add('text', 'email', '', array('style' => 'width:400px'));
        $obj->set_rule(array(
            'email' => array('error', '请输入合法的email！')
        ));*/

        if (session("adminUser")) {
            $form->add('label', 'label_administrator', 'administrator', '超级管理员:');
            $obj = & $form->add('radios', 'administrator', array('否', '是'), '0');

            $form->add('label', 'label_status', 'status', '状态:');
            $obj = & $form->add('radios', 'status', array(1 => '启用', 0 => '停用'), '1');
        }
        // "submit"
        $form->add('submit', 'btnsubmit', '注册');
        $html_str = $form->render('*horizontal');
        $this->assign('form_html', $html_str);

        /*position指定以及一些问候信息*/
        $current = "用户添加";
        $position = getPosition(array('用户管理列表' => '__GROUP__/user/show', '用户添加' => ''));
        $this->assign('current', $current);
        $this->assign('position', $position);
        $this->assign('welcome', getWelcome());

        $this->display('user:form');
    }


    /**
     * 后台用户编辑
     */
    public function edit()
    {
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        if ($id === null) {
            $this->error("读取后台管理员id错误！");
        }

        $sites = getAvailableSitesArr();
        $sitesArr = array();
        foreach ($sites as $v) {
            $sitesArr[$v['id']] = $v['name'];
        }
        $sitesArr['all'] = "管辖不限";

        require(APP_INC_PATH . 'form/Zebra_Form.php');
        $m2 = M('admin');
        $user = $m2->where('id=' . $id)->find();

        $userSiteArr = explode(',', $user['siteid']);
        if (empty($userSiteArr)) {
            $userSiteArr = array('all');
        }

        $m = M('role');
        $roles = $m->where('status=1')->select();

        foreach ($roles as $v) {
            $rolesArr[$v['id']] = $v['name'];
        }
        //dump($rolesArr);exit;
        $form = new Zebra_Form('form', 'post', U('form/userupdate')); //参数分别是 表单名称 提交方法 请求页面

        $form->add('text', 'id', $id, array('type' => 'hidden'));
        if (session("adminUser")) {
            $form->add('label', 'label_siteid', 'siteid', '管辖站点:');
            $obj = & $form->add('checkboxes', 'siteid[]', $sitesArr, $userSiteArr);
        }
        $form->add('label', 'label_usertype', 'usertype', '用户组:');
        $obj = & $form->add('select', 'usertype', $user['usertype']);
        $obj->add_options($rolesArr);
        $obj->set_rule(array(
            'required' => array('error', '必须选择用户组!')
        ));

        $form->add('label', 'label_userid', 'userid', '用户名:');
        $obj = & $form->add('text', 'userid', $user['userid']);
        $obj->set_rule(array(
            'required' => array('error', '必须填写用户名!')
        ));


        $form->add('label', 'label_uname', 'uname', '昵称:');
        $obj = & $form->add('text', 'uname', $user['uname']);


        /*$form->add('label', 'label_email', 'email', 'email:');
        $obj = & $form->add('text', 'email', $user['email'], array('style' => 'width:400px'));
        $obj->set_rule(array(
            'email' => array('error', '请输入合法的email！')
        ));*/

        if (session("adminUser")) {
            $form->add('label', 'label_administrator', 'administrator', '超级管理员:');
            $obj = & $form->add('radios', 'administrator', array('否', '是'), $user['administrator']);

            $form->add('label', 'label_status', 'status', '状态:');
            $obj = & $form->add('radios', 'status', array(1 => '启用', 0 => '停用'), $user['status']);
        }

        // "submit"
        $form->add('submit', 'btnsubmit', '修改');
        $html_str = $form->render('*horizontal');
        $this->assign('form_html', $html_str);

        /*position指定以及一些问候信息*/
        $current = "用户修改";
        $position = getPosition(array('用户管理列表' => '__GROUP__/user/show', '用户修改' => ''));
        $this->assign('current', $current);
        $this->assign('position', $position);
        $this->assign('welcome', getWelcome());

        $this->display('user:form');
    }

    /**
     * 后台管理员密码修改
     */
    public function pwdedit()
    {
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        if ($id === null) {
            $this->error("读取后台管理员id错误！");
        }

        require(APP_INC_PATH . 'form/Zebra_Form.php');

        $form = new Zebra_Form('form', 'post', U('form/pwdupdate')); //参数分别是 表单名称 提交方法 请求页面

        //隐藏表单
        $obj = & $form->add('text', 'id', $id, array('type' => 'hidden'));

        $form->add('label', 'label_pwd0', 'pwd0', '原密码:');
        $obj = & $form->add('password', 'pwd0', '');
        $obj->set_rule(array(
            'required' => array('error', '密码不能为空!'),
            'length' => array(6, 10, 'error', '密码必须在6位到10位之间!'),
        ));

        $form->add('label', 'label_pwd', 'pwd', '新密码:(6-10位)');
        $obj = & $form->add('password', 'pwd', '');
        $obj->set_rule(array(
            'required' => array('error', '密码不能为空!'),
            'length' => array(6, 10, 'error', '密码必须在6位到10位之间!'),
        ));

        $form->add('label', 'label_pwd2', 'pwd2', '重复密码:');
        $obj = & $form->add('password', 'pwd2', '');
        $obj->set_rule(array(
            'compare' => array('pwd', 'error', '两次输入密码不一致!'),
            'required' => array('error', '密码不能为空!'),
            'length' => array(6, 10, 'error', '密码必须在6位到10位之间!'),
        ));

        // "submit"
        $form->add('submit', 'btnsubmit', '修改');
        $html_str = $form->render('*horizontal');
        $this->assign('form_html', $html_str);

        /*position指定以及一些问候信息*/
        $current = "用户密码修改";
        $position = getPosition(array('用户管理列表' => '__GROUP__/user/show', '用户密码修改' => ''));
        $this->assign('current', $current);
        $this->assign('position', $position);
        $this->assign('welcome', getWelcome());

        $this->display('user:form');
    }
}
?>
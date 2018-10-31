<?php

/*
 * 友情链接管理
 */

class AdAction extends BaseAction
{
    /*
     * 显示出所有的友情链接
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
                    setcookie("orderby_ad", $_POST['orderby']);
                    setcookie("map_orderby_ad", $map_orderby);
                }

                if (!empty($_POST['searchkey'])) {
                    $this->assign('searchkey', $_POST['searchkey']);
                    $map[$_POST['searchby']] = array('like', '%' . $_POST['searchkey'] . '%');
                }
            }
        } else {

            if (!empty($_COOKIE['map_orderby_ad'])) {
                $map_orderby = $_COOKIE['map_orderby_ad'];
                $orderby = $_COOKIE['orderby_ad'];
            }
        }
        //排序
        $map_orderby = !empty($map_orderby) ? $map_orderby : 'id desc';
        $m = M('ad');
        $count = $m->where($map)->count(); // 查询满足要求的总记录数
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
        $arrOrderby = array('id_desc' => 'ID 降序', 'id_asc' => 'ID 升序', 'overdue_desc' => '过期时间 降序', 'overdue_asc' => '过期时间 升序', 'arctype_desc' => '投放范围 降序', 'arctype_asc' => '投放范围 升序', 'status_desc' => '启用在前', 'status_asc' => '禁用在前');
        $orderby_html = getOptions($arrOrderby, $orderby);
        $arrSearchby = array('name' => '标识', 'id' => '广告号', 'intro' => '说明');
        $searchby_html = getOptions($arrSearchby, $_POST['searchby']);

        $this->assign('orderby_html', $orderby_html);
        $this->assign('searchby_html', $searchby_html);

        /*position指定以及一些问候信息*/
        $current = "广告管理列表";
        $position = getPosition("广告管理列表");
        $this->assign('current', $current);
        $this->assign('position', $position);
        $this->assign('welcome', getWelcome());

        $this->display();
    }


    public function add()
    {
        require(APP_INC_PATH . 'form/Zebra_Form.php');

        $m = new ArctypeModel();
        $arctypeOption = $m->arctypeArr();
        $arctypeOption[0] = "不限栏目";

        $form = new Zebra_Form('form', 'post', U('form/adsave'));

        $form->add('label', 'label_name', 'name', '广告标识:', array('style' => 'width:80px;'));
        $obj = & $form->add('text', 'name', '');
        $obj->set_rule(array(
            'required' => array('error', '请输入字段名称!'),
            'alphanumeric' => array('_', 'error', '命名不合法！')
        ));

        $form->add('label', 'label_intro', 'intro', '广告描述:');
        $obj = & $form->add('text', 'intro', '');
        $obj->set_rule(array(
            'required' => array('error', '必须填写广告描述!')
        ));

        $form->add('label', 'label_width', 'width', '广告宽度:');
        $obj = & $form->add('text', 'width', '');
        $obj->set_rule(array(
            'required' => array('error', '必须填写广告宽度!')
        ));

        $form->add('label', 'label_height', 'height', '广告高度:');
        $obj = & $form->add('text', 'height', '');
        $obj->set_rule(array(
            'required' => array('error', '必须填写广告高度!')
        ));

        $form->add('label', 'label_arctype', 'arctype', '投放栏目:');
        $obj = & $form->add('select', 'arctype', '');
        $obj->add_options($arctypeOption, true);

        $form->add('label', 'label_overdue', 'overdue', '过期时间');
        $date = & $form->add('date', 'overdue', '');
        $date->set_rule(array(
            'date' => array('error', '时间格式不对!'),
        ));
        $obj = & $form->add('note', 'note_01', 'overdue', '时间留空，永不过期', array('style' => 'width:150px'));

        $form->add('label', 'label_html', 'html', '广告代码:');
        $obj = & $form->add('kind', 'html', '', array('style' => 'width:700px;height:300px;'));
        //$obj->set_rule(array('required' => array('error', '请输入字段默认值!')));

        $form->add('label', 'label_overduehtml', 'overduehtml', '过期广告:');
        $obj = & $form->add('kind', 'overduehtml', '', array('style' => 'width:700px;height:300px;'));
        //$obj->set_rule(array('required' => array('error', '请输入字段默认值!')));

        $form->add('label', 'label_url', 'url', '广告链接:');
        $obj = & $form->add('text', 'url', '', array('style' => 'width:400px;'));

        $form->add('label', 'label_status', 'status', '状态:');
        $obj = & $form->add('radios', 'status', array(
            '1' => '启用',
            '0' => '禁用'
        ), '1');
        $obj->set_rule(array(
            'required' => array('error', '必须选择状态！')
        ));

        $form->add('submit', 'btnsubmit', '确定');
        $form_html = $form->render('*horizontal');
        $this->assign('form_html', $form_html);

        /*position指定以及一些问候信息*/
        $current = "广告添加";
        $position = getPosition(array('广告管理列表' => '__GROUP__/ad/show', '广告添加' => ''));
        $this->assign('current', $current);
        $this->assign('position', $position);
        $this->assign('welcome', getWelcome());

        $this->display('form');
    }


    public function edit()
    {
        $adid = isset($_GET['adid']) ? $_GET['adid'] : null;
        if ($adid == null) {
            $this->error("读取广告id失败！");
        }

        $m = M('ad');
        $map['id'] = array('eq', $adid);
        $adArr = $m->where($map)->find();

        $m2 = new ArctypeModel();
        $arctypeOption = $m2->arctypeArr();
        $arctypeOption[0] = "不限栏目";

        $adArr['overdue'] = $adArr['overdue'] != '0000-00-00' ? $adArr['overdue'] : '';

        require(APP_INC_PATH . 'form/Zebra_Form.php');
        $form = new Zebra_Form('form', 'post', U('form/adupdate'));

        $form->add('text', 'id', $adArr['id'], array('type' => 'hidden'));

        $form->add('label', 'label_name', 'name', '广告标识:', array('style' => 'width:80px;'));
        $obj = & $form->add('text', 'name', $adArr['name']);
        $obj->set_rule(array(
            'required' => array('error', '请输入字段名称!'),
            'alphanumeric' => array('_', 'error', '命名不合法！')
        ));

        $form->add('label', 'label_intro', 'intro', '广告描述:');
        $obj = & $form->add('text', 'intro', $adArr['intro']);
        $obj->set_rule(array(
            'required' => array('error', '必须填写广告描述!')
        ));

        $form->add('label', 'label_width', 'width', '广告宽度:');
        $obj = & $form->add('text', 'width', $adArr['width']);
        $obj->set_rule(array(
            'required' => array('error', '必须填写广告宽度!')
        ));

        $form->add('label', 'label_height', 'height', '广告高度:');
        $obj = & $form->add('text', 'height', $adArr['height']);
        $obj->set_rule(array(
            'required' => array('error', '必须填写广告高度!')
        ));

        $form->add('label', 'label_arctype', 'arctype', '投放栏目:');
        $obj = & $form->add('select', 'arctype', $adArr['arctype']);
        $obj->add_options($arctypeOption, true);

        $form->add('label', 'label_overdue', 'overdue', '过期时间');
        $date = & $form->add('date', 'overdue', $adArr['overdue']);
        $date->set_rule(array(
            'date' => array('error', '时间格式不对!'),
        ));
        $obj = & $form->add('note', 'note_01', 'overdue', '时间留空，永不过期', array('style' => 'width:150px'));

        $form->add('label', 'label_html', 'html', '广告代码:');
        $obj = & $form->add('kind', 'html', $adArr['html'], array('style' => 'width:700px;height:300px;'));
        $obj->set_rule(array( //'required' => array('error', '请输入字段默认值!')
        ));

        $form->add('label', 'label_overduehtml', 'overduehtml', '过期广告:');
        $obj = & $form->add('kind', 'overduehtml', $adArr['overduehtml'], array('style' => 'width:700px;height:300px;'));
        $obj->set_rule(array( //'required' => array('error', '请输入字段默认值!')
        ));

        $form->add('label', 'label_url', 'url', '广告链接:');
        $obj = & $form->add('text', 'url', $adArr['url'], array('style' => 'width:400px;'));

        $form->add('label', 'label_status', 'status', '状态:');
        $obj = & $form->add('radios', 'status', array(
            '1' => '启用',
            '0' => '禁用'
        ), $adArr['status']);
        $obj->set_rule(array(
            'required' => array('error', '必须选择状态！')
        ));

        $form->add('submit', 'btnsubmit', '确定');
        $form_html = $form->render('*horizontal');
        $this->assign('form_html', $form_html);

        /*position指定以及一些问候信息*/
        $current = "广告修改";
        $position = getPosition(array('广告管理列表' => '__GROUP__/ad/show', '广告修改' => ''));
        $this->assign('current', $current);
        $this->assign('position', $position);
        $this->assign('welcome', getWelcome());

        $this->display('form');
    }
}

?>
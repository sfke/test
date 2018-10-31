<?php

/**
 * 后台用户管理
 * @author Administrator
 *
 */
class SysAction extends BaseAction
{
    /**
     *  用于设定系统设置的设置
     */
    public function superConfig()
    {
        import('ORG.Util.Page'); // 导入类

        if (!empty($_POST['action'])) {
            if ($_POST['action'] == 'filter') {
                if (!empty($_POST['searchkey'])) {
                    $this->assign('searchkey', $_POST['searchkey']);
                    $map[$_POST['searchby']] = array('like', '%' . $_POST['searchkey'] . '%');
                }
            }
        } else {

        }


        $ctype = !empty($_GET['ctype']) ? $_GET['ctype'] : null;
        if ($ctype != null) {
            setcookie('ctype', $ctype);
        }

        if (!empty($_COOKIE['ctype']) && empty($ctype)) {
            $map['groupid'] = $_COOKIE['ctype'];
            $ctype = $_COOKIE['ctype'];
        } else if ($ctype != null) {
            $map['groupid'] = $ctype;
        } else {

        }

        //排序
        $map_orderby = !empty($map_orderby) ? $map_orderby : 'id, `order` asc';
        $m = M('sysconfig');
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
        $arrSearchby = array('varname' => '参数名', 'value' => '参数值', 'id' => '参数ID', 'info' => '说明信息', 'type' => '数据类型');
        $searchby_html = getOptions($arrSearchby, $_POST['searchby']);
        $this->assign('searchby_html', $searchby_html);

        $m = M('configtype');
        $map = array();
        $configTypeArr = $m->where($map)->select();
        foreach ($configTypeArr as $v) {
            $typeArr[$v['id']] = $v['typename'];
        }
        $type_html = getOptions($typeArr, $ctype);
        $this->assign('type_html', $type_html);

        /*position指定以及一些问候信息*/
        $current = "系统设置高级管理";
        $position = getPosition("系统设置高级管理");
        $this->assign('current', $current);
        $this->assign('position', $position);
        $this->assign('welcome', getWelcome());
        $this->display();
    }

    /**
     *  系统参数类别
     */
    public function configType()
    {
        $m = M('configtype');
        $map = array();
        $configTypeArr = $m->where($map)->select();
        $this->assign('configTypeArr', $configTypeArr);

        /*position指定以及一些问候信息*/
        $current = "系统参数类别管理";
        $position = getPosition("系统参数类别管理");
        $this->assign('current', $current);
        $this->assign('position', $position);
        $this->assign('welcome', getWelcome());
        $this->display();
    }


    /**
     * 后台用户编辑
     */
    public function setConfig()
    {
        $groupid = !empty($_GET['groupid']) ? $_GET['groupid'] : null;
        if ($groupid === null) {
            $this->error("未指定系统设置级别！");
        }

        require(APP_INC_PATH . 'form/Zebra_Form.php');
        $form = new Zebra_Form('form', 'post', U('form/configupdate')); //参数分别是 表单名称 提交方法 请求页面
        $m = M('sysconfig');

        $form->add('text', 'groupid', $groupid, array('type' => 'hidden'));
        $sysconfigs = $m->where('groupid=' . $groupid)->order('`order` asc')->select();

        foreach ($sysconfigs as $v) {
            if (session("superUser")) {
                $more_info = "<br/>" . $v['varname'];
            } else {
                $more_info = '';
            }

            if('html' == $v['type']){
                $addArr = array('style' => "width:".($v['width']+10)."px;", 'theme' => $v['items'] ? $v['items'] : 'config');
            }else{
                $addArr = array('style' => "width:".$v['width']."px;");
            }

            switch ($v['type']) {
                case 'string' :
                    $form->add('label', 'label_' . $v['varname'], $v['varname'], $v['info'] . $more_info);
                    $form->add('text', $v['varname'], $v['value'], $addArr);
                    break;
                case 'bstring' :
                    $form->add('label', 'label_' . $v['varname'], $v['varname'], $v['info'] . $more_info);
                    $form->add('textarea', $v['varname'], $v['value'], $addArr);
                    break;
                case 'number' :
                    $form->add('label', 'label_' . $v['varname'], $v['varname'], $v['info'] . $more_info);
                    $obj = & $form->add('text', $v['varname'], $v['value'], $addArr);
                    $rule['digits'] = array('', 'error', '只能输入"数字"！');
                    $obj->set_rule($rule);
                    break;
                case 'bool' :
                    $form->add('label', 'label_' . $v['varname'], $v['varname'], $v['info'] . $more_info);
                    $form->add('radios', $v['varname'], array('N', 'Y'), $v['value'], $addArr);
                    break;
                case 'image' :
                    $form->add('label', 'label_' . $v['varname'], $v['varname'], $v['info'] . $more_info);
                    $form->add('kimg', $v['varname'], $v['value'], $addArr);
                    break;
                case 'file' :
                    $form->add('label', 'label_' . $v['varname'], $v['varname'], $v['info'] . $more_info);
                    $form->add('Kfile', $v['varname'], $v['value'], $addArr);
                    break;
                case 'radio' :
                    if (preg_match("#^__.*__$#i", $v['items'])) {
                        import('@.Class.AutoFormHelper');
                        $arr = AutoFormHelper::getOptions($v['items']);
                    } else {
                        $arr = explode("\r\n", $v['items']);
                    }
                    $form->add('label', 'label_' . $v['varname'], $v['varname'], $v['info'] . $more_info);
                    $form->add('radios', $v['varname'], $arr, $v['value']);
                    break;
                case 'color' :
                    $form->add('label', 'label_' . $v['varname'], $v['varname'], $v['info'] . $more_info);
                    $form->add('color', $v['varname'], $v['value'], $addArr);
                    break;
                case 'html' :
                    $form->add('label', 'label_' . $v['varname'], $v['varname'], $v['info'] . $more_info);
                    $form->add('kind', $v['varname'], $v['value'], $addArr);
                    break;
                default :
                    $form->add('label', 'label_' . $v['varname'], $v['varname'], $v['info'] . $more_info);
                    $form->add('text', $v['varname'], $v['value'], $addArr);
                    break;
            }

        }

        // "submit"
        $form->add('submit', 'btnsubmit', '修改');
        $html_str = $form->render('*horizontal');
        $this->assign('form_html', $html_str);

        /*position指定以及一些问候信息*/
        $current = "基本资料设置";
        $position = getPosition("基本资料设置");
        $this->assign('current', $current);
        $this->assign('position', $position);
        $this->assign('welcome', getWelcome());

        $this->display('common:baseform');
    }


    /**
     * 添加参数
     */
    public function add()
    {
        $m2 = M('configtype');
        $arr = $m2->select();
        foreach ($arr as $v) {
            $typeArr[$v['id']] = $v['typename'];
        }

        require(APP_INC_PATH . 'form/Zebra_Form.php');
        $form = new Zebra_Form('form', 'post', U('form/sysconfigsave'));

        //隐藏表单

        $form->add('label', 'label_groupid', 'groupid', '参数类别:');
        $obj = & $form->add('select', 'groupid', '');
        $obj->add_options($typeArr);
        $obj->set_rule(array(
            'required' => array('error', '必须选择所属类别!')
        ));

        $form->add('label', 'label_varname', 'varname', '参数名称:');
        $obj = & $form->add('text', 'varname', '');
        $obj->set_rule(array(
            'required' => array('error', '必须填写参数名!'),
            'alphanumeric' => array('_', 'error', '必须填写合法的参数名！')
        ));


        $form->add('label', 'label_type', 'type', '参数类型:');
        $obj = & $form->add('select', 'type', '');
        $obj->add_options(array(
            'string'  => '字符串',
            'bstring' => '长字符串',
            'number'  => '数字',
            'bool'    => '开关',
            'image'   => '图片',
            'file'    => '文件',
            'color'   => '颜色',
            'html'    => 'HTML',
            'radio'   => 'radio',
        ));
        $obj->set_rule(array(
            'required' => array('error', '必须选择数据类型!')
        ));


        $form->add('label', 'label_info', 'info', '说明信息:');
        $obj = & $form->add('textarea', 'info', '');

        $form->add('label', 'label_items', 'items', '选项列表:');
        $obj = & $form->add('textarea', 'items','');

        $form->add('label', 'label_width', 'width', 'input宽度:');
        $obj = & $form->add('text', 'width', '300');
        $obj->set_rule(array(
            'numeric' => array('_', 'error', '宽度不合法!')
        ));


        $form->add('label', 'label_order', 'order', '排序:');
        $obj = & $form->add('text', 'order', '0', array('style' => 'width:30px;', 'maxLength' => '3'));
        $obj->set_rule(array(
            'numeric' => array('_', 'error', '序号不合法!')
        ));


        // "submit"
        $form->add('submit', 'btnsubmit', '确定');
        $form_html = $form->render('*horizontal');
        $this->assign('form_html', $form_html);

        /*position指定以及一些问候信息*/
        $current = "系统参数添加";
        $position = getPosition(array('系统参数高级管理' => '__GROUP__/sys/superConfig', '系统参数添加' => ''));
        $this->assign('current', $current);
        $this->assign('position', $position);
        $this->assign('welcome', getWelcome());

        $this->display('common:baseform');

    }


    /**
     * 添加参数
     */
    public function edit()
    {

        $id = !empty($_GET['id']) ? $_GET['id'] : null;
        if ($id === null) {
            $this->error("获取参数ID失败！");
            return;
        }

        $m = M('sysconfig');
        $cfgArr = $m->where('id=' . $id)->find();

        $m2 = M('configtype');
        $arr = $m2->select();
        foreach ($arr as $v) {
            $typeArr[$v['id']] = $v['typename'];
        }

        require(APP_INC_PATH . 'form/Zebra_Form.php');
        $form = new Zebra_Form('form', 'post', U('form/sysconfigupdate'));

        //隐藏表单
        $form->add('text', 'id', $id, array('type' => 'hidden'));

        $form->add('label', 'label_groupid', 'groupid', '参数类别:');
        $obj = & $form->add('select', 'groupid', $cfgArr['groupid']);
        $obj->add_options($typeArr);
        $obj->set_rule(array(
            'required' => array('error', '必须选择所属类别!')
        ));

        $form->add('label', 'label_varname', 'varname', '参数名称:');
        $obj = & $form->add('text', 'varname', $cfgArr['varname'], array('readonly' => true));
        $obj->set_rule(array(
            'required' => array('error', '必须填写参数名!'),
            'alphanumeric' => array('_', 'error', '必须填写合法的参数名！')
        ));


        $form->add('label', 'label_type', 'type', '参数类型:');
        $obj = & $form->add('select', 'type', $cfgArr['type']);
        $obj->add_options(array(
            'string'  => '字符串',
            'bstring' => '长字符串',
            'number'  => '数字',
            'bool'    => '开关',
            'image'   => '图片',
            'file'    => '文件',
            'color'   => '颜色',
            'html'    => 'HTML',
            'radio'   => 'radio',
        ));
        $obj->set_rule(array(
            'required' => array('error', '必须选择数据类型!')
        ));

        $form->add('label', 'label_info', 'info', '说明信息:');
        $obj = & $form->add('textarea', 'info', $cfgArr['info']);

        $form->add('label', 'label_items', 'items', '选项列表:');
        $obj = & $form->add('textarea', 'items',$cfgArr['items']);

        $form->add('label', 'label_width', 'width', 'input宽度:');
        $obj = & $form->add('text', 'width', $cfgArr['width']);
        $obj->set_rule(array(
            'numeric' => array('_', 'error', '宽度不合法!')
        ));


        $form->add('label', 'label_order', 'order', '排序:');
        $obj = & $form->add('text', 'order', $cfgArr['order'], array('style' => 'width:30px;', 'maxLength' => '3'));
        $obj->set_rule(array(
            'numeric' => array('_', 'error', '序号不合法!')
        ));

        // "submit"
        $form->add('submit', 'btnsubmit', '确定');
        $form_html = $form->render('*horizontal');
        $this->assign('form_html', $form_html);

        /*position指定以及一些问候信息*/
        $current = "系统参数修改";
        $position = getPosition(array('系统参数高级管理' => '__GROUP__/sys/superConfig', '系统参数修改' => ''));
        $this->assign('current', $current);
        $this->assign('position', $position);
        $this->assign('welcome', getWelcome());

        $this->display('common:baseform');
    }
}
?>
<?php

class ArctypeAction extends BaseAction
{
    public function show()
    {
        $tid = $this->_param("tid");
        if (!Permission::check($tid, "r")) {
            $this->error("您没有权限访问该栏目！");
        }
        if (!empty($_POST['action'])) {
            $tid = isset($_POST['tid']) ? $_POST['tid'] : null;
            $cid = isset($_POST['cid']) ? $_POST['cid'] : null;
            if ($tid === null || $cid === null) {
                $this->error("读取栏目信息出错！");
            }
            if ($_POST['action'] == 'filter') {
                if (!empty($_POST['orderby'])) {
                    $arr = orderByParse($_POST['orderby']);
                    if (is_array($arr)) {
                        $map_orderby = "`$arr[0]` $arr[1]";
                    }
                }

                if (!empty($_POST['searchkey'])) {
                    $this->assign('searchkey', $_POST['searchkey']);
                    $map[$_POST['searchby']] = array('like', '%' . $_POST['searchkey'] . '%');
                }
            }
        } else {
            $tid = isset($_GET['tid']) ? $_GET['tid'] : null;
            $cid = isset($_GET['cid']) ? $_GET['cid'] : null;
            if ($tid === null || $cid === null) {
                $this->error("读取栏目信息出错！");
            }
        }

        /*移动到选中栏目的数组*/
        $m = new ArctypeModel();
        $arctypeArr = $m->getOne($tid);
        $selectionArr = $m->arctypeArr();
        $selectionArr_C = $m->arctypeArrT($arctypeArr['type'], null, $arctypeArr['channel']);
        $selection_html = '';
        foreach ($selectionArr as $k => $v) {
            if ($k == $tid) $checked = 'selected style="background:#DBDBDB;" '; else $checked = '';
            if ($selectionArr_C != null && $selectionArr_C[$k] == 0) $flag = 'disabled style="background:#E9F6F9;"'; else $flag = '';
            $selection_html .= "<option value='$k' $checked $flag >$v</option>";
        }
        $this->assign('selection_html', $selection_html);
        /*图表*/
        if (C('SYS_SAFE_MODE') == 0) {
            $chart_html = $m->arctypeChart($tid);
        } else {
            $chart_html = '';
        }

        /*查询条件  */
        $map_orderby = !empty($map_orderby) ? $map_orderby : '`order` asc';
        $map['fid'] = array('eq', $tid);
        $map['status'] = array('eq', 1);
        $map['siteid'] = session('currentSiteId');
        $sonArr = $m->where($map)->order($map_orderby)->select();

        /*select准备*/
        $arrOrderby = array('id_desc' => 'ID 降序', 'id_asc' => 'ID 升序', 'channel_desc' => '内容模型 降序', 'channel_asc' => '内容模型 升序', 'order_desc' => '排序字段 降序', 'order_asc' => '排序字段 升序');
        $orderby_html = getOptions($arrOrderby, $_POST['orderby']);

        $arrSearchby = array('name' => '栏目名', 'id' => '栏目ID');
        $searchby_html = getOptions($arrSearchby, $_POST['searchby']);

        /*position指定以及一些问候信息*/
        $current = "栏目管理列表";
        $position = getPosition("栏目管理列表");
        $this->assign('current', $current);
        $this->assign('position', $position);
        $this->assign('welcome', getWelcome());

        $ss = new ChannelModel();
        $this->assign('selectArr', $ss->selectArr($cid));
        $this->assign('sonArr', $sonArr);
        $this->assign('chart_html', $chart_html);
        $this->assign('orderby_html', $orderby_html);
        $this->assign('searchby_html', $searchby_html);
        $this->assign('cid', $cid);
        $this->assign('tid', $tid);
        $this->display();
    }


    /**
     * 栏目回收站
     */
    public function recycle()
    {
        if (!empty($_POST['action'])) {
            if ($_POST['action'] == 'filter') {
                if (!empty($_POST['orderby'])) {
                    $arr = orderByParse($_POST['orderby']);
                    if (is_array($arr)) {
                        $map_orderby = "`$arr[0]` $arr[1]";
                    }
                }
                if (!empty($_POST['searchkey'])) {
                    $this->assign('searchkey', $_POST['searchkey']);
                    $map[$_POST['searchby']] = array('like', '%' . $_POST['searchkey'] . '%');
                }
            }
        }
        $m = new ArctypeModel();
        /*查询条件  */
        $map_orderby = !empty($map_orderby) ? $map_orderby : '';
        $map['status'] = array('eq', -1);
        $sonArr = $m->where($map)->order($map_orderby)->select();

        /*select准备*/
        $arrOrderby = array('id_desc' => 'ID 降序', 'id_asc' => 'ID 升序', 'channel_desc' => '内容模型 降序', 'channel_asc' => '内容模型 升序');
        $orderby_html = getOptions($arrOrderby, $_POST['orderby']);

        $arrSearchby = array('name' => '栏目名', 'id' => '栏目ID');
        $searchby_html = getOptions($arrSearchby, $_POST['searchby']);

        //$ss = new ChannelModel();
        //$this->assign('selectArr',$ss->selectArr());
        $this->assign('sonArr', $sonArr);
        $this->assign('orderby_html', $orderby_html);
        $this->assign('searchby_html', $searchby_html);

        /*position指定以及一些问候信息*/
        $current = "栏目回收站";
        $position = getPosition("栏目回收站");
        $this->assign('current', $current);
        $this->assign('position', $position);
        $this->assign('welcome', getWelcome());

        $this->display();
    }

    public function edit()
    {
        $tid = $this->_param("tid");
        if (!Permission::check($tid, "w")) {
            $this->error("您没有权限编辑该栏目！");
        }
        require(APP_INC_PATH . 'form/Zebra_Form.php');
        $tid = $this->_get('tid');
        $tid = isset($tid) ? $tid : -1;
        $select_list = getTpl('list');
        $select_content = getTpl('cont');
        $m = new ArctypeModel();
        $arr = $m->getOne($tid);
        $selection = $m->arctypeArr();
        $selection_c = $m->arctypeArrT($arr['type'], $tid, $arr['channel']);

        $form = new Zebra_Form('form', 'post', U('form/arctypeupdate'));  //参数分别是 表单名称 提交方法 请求页面

        //隐藏表单
        $form->add('text', 'id', $tid, array('type' => 'hidden'));
        $form->add('text', 'oldfid', $arr['fid'], array('type' => 'hidden'));

        $form->add('label', 'label_fid', 'fid', '上级栏目:');
        $obj = &$form->add('select', 'fid', $arr['fid'], array('style' => 'width:150px;'));
        $obj->add_options($selection);

        $form->add('label', 'label_name', 'name', '栏目名称:');
        $obj = &$form->add('text', 'name', $arr['name'], array('style' => 'width:400px'));
        $obj->set_rule(array(
            'required' => array('error', '必须填写栏目标题!')
        ));

       // if (session("superUser")) {
            $form->add('label', 'label_class', 'class', '栏目别名:');
            $form->add('text', 'class', $arr['class'], array('style' => 'width:200px'));
       // }

        $form->add('label', 'label_mtitle', 'mtitle', 'meta标题:');
        $obj = &$form->add('text', 'mtitle', $arr['mtitle'], array('style' => 'width:400px'));

        $form->add('label', 'label_mkey', 'mkey', 'meta关键词:');
        $obj = &$form->add('text', 'mkey', $arr['mkey'], array('style' => 'width:400px'));

        $form->add('label', 'label_mdesc', 'mdesc', 'meta描述:');
        $obj = &$form->add('textarea', 'mdesc', $arr['mdesc'], array('style' => 'width:400px'));
        
		if($arr['litpic_size']==null){
		  $form->add('label', 'label_litpic', 'litpic', '栏目缩略图:');
		 }else{
          $form->add('label', 'label_litpic', 'litpic', '栏目缩略图:<br/>尺寸(px)：<br/>'.$arr['litpic_size']);
		}
        $obj = &$form->add('kimg', 'litpic', $arr['litpic'], array('style' => 'width:400px'));

        if (session("superUser")) {
            $form->add('label', 'label_litpic_size', 'litpic_size', '栏目图尺寸:');
            $form->add('text', 'litpic_size', $arr['litpic_size'], array('style' => 'width:100px')); 
			$form->add('label', 'label_litimg_size', 'litimg_size', '内容图尺寸:');
			$form->add('text', 'litimg_size', $arr['litimg_size'], array('style' => 'width:100px'));
        }

        if (session("superUser")) {
            $form->add('label', 'label_url', 'url', '栏目路由:');
            $obj = &$form->add('text', 'url', $arr['url'], array('style' => 'width:400px'));
        }
            $form->add('label', 'label_tdir', 'tdir', '栏目模板:');
            $obj = &$form->add('select', 'tdir', $arr['tdir'], array('style' => 'width:400px;'));
            $obj->add_options($select_list);

            $form->add('label', 'label_cdir', 'cdir', '内容模板:');
            $obj = &$form->add('select', 'cdir', $arr['cdir'], array('style' => 'width:400px;'));
            $obj->add_options($select_content);
			
         //设置栏目访问权限时使用（需要:1,修改数据库表 jl_arctype 字段 rank 为 char(50) 默认值为0. 2,开启 FormAction.class.php 的 rank 项 ）
			/*
            $form->add('label', 'label_rank', 'rank', '访问权限:');
            $form->add('checkboxes', 'rank[]',  array(0 => '完全开放', 1 => '普通会员', 2 => '企业会员'),explode(",", $arr['rank'])); 
		    */

        if (session("superUser")) {
            $form->add('label', 'label_display', 'display', '显示状态:');
            $obj = &$form->add('radios', 'display', array(1 => '均显示', 2 => '仅后台显示', 3 => '仅前台显示', 4 => '均不显示'), $arr['display']);
        } else {
            $form->add('label', 'label_display', 'display', '显示状态:');
            $obj = &$form->add('radios', 'display', array(1 => '显示', 2 => '不显示'), $arr['display']);
        }

        $form->add('submit', 'btnsubmit', '确定');

        if ($form->validate()) {
            $date = $_POST['pubdate'];
            $temp = explode("-", $date);
            $_POST['pubdate'] = mktime(0, 0, 0, $temp[1], $temp[2], $temp[0]);

        } else{
            $form_html = $form->render('*horizontal');
        }
        $this->assign('form_html', $form_html);

        /*position指定以及一些问候信息*/
        $current = "栏目修改";
        $position = getPosition("栏目修改");
        $this->assign('current', $current);
        $this->assign('position', $position);
        $this->assign('welcome', getWelcome());

        $this->display('common:form');
    }


    public function add()
    {
        $fid = isset($_GET['fid']) ? $_GET['fid'] : null;
        $cid = isset($_GET['cid']) ? $_GET['cid'] : null;
        if (!Permission::check($fid, "w")) {
            $this->error("您没有权限添加栏目！");
        }
        require(APP_INC_PATH . 'form/Zebra_Form.php');
        $select_list = getTpl('list');
        $select_content = getTpl('cont');
        $m = new ArctypeModel();
        $m2 = new ChannelModel();
        $channelArr = $m2->getOne($cid);
        $selection = $m->arctypeArr();
        $selection_c = $m->arctypeArrT($channelArr['type'], null, $cid);

        $form = new Zebra_Form('form', 'post', U('form/arctypesave'));  //参数分别是 表单名称 提交方法 请求页面

        //隐藏表单
        $form->add('hidden', 'cid', $cid);
        $form->add('label', 'label_fid', 'fid', '上级栏目:');
        $obj = &$form->add('select', 'fid', $fid, array('style' => 'width:150px;'));
        $obj->add_options($selection);

        $form->add('label', 'label_name', 'name', '栏目名称:');
        $obj = &$form->add('text', 'name', '', array('style' => 'width:400px'));
        $obj->set_rule(array(
            'required' => array('error', '必须填写栏目标题!')
        ));

       // if (session("superUser")) {
            $form->add('label', 'label_class', 'class', '栏目别名:');
            $form->add('text', 'class', '', array('style' => 'width:200px'));
       // }

        $form->add('label', 'label_mtitle', 'mtitle', 'meta标题:');
        $form->add('text', 'mtitle', '', array('style' => 'width:400px'));

        $form->add('label', 'label_mkey', 'mkey', 'meta关键词:');
        $form->add('text', 'mkey', '', array('style' => 'width:400px'));

        $form->add('label', 'label_mdesc', 'mdesc', 'meta描述:');
        $form->add('textarea', 'mdesc', '', array('style' => 'width:400px'));

        $form->add('label', 'label_litpic', 'litpic', '栏目缩略图:');
        $form->add('kimg', 'litpic', '', array('style' => 'width:400px'));

        if (session("superUser")) {
            $form->add('label', 'label_litpic_size', 'litpic_size', '栏目图尺寸:');
            $form->add('text', 'litpic_size', '', array('style' => 'width:100px')); 
			$form->add('label', 'label_litimg_size', 'litimg_size', '内容图尺寸:');
			$form->add('text', 'litimg_size', '', array('style' => 'width:100px'));
        }
		
        if (session("superUser")) {
            $form->add('label', 'label_url', 'url', '栏目路由:');
            $form->add('text', 'url', '', array('style' => 'width:400px'));
        }
            $form->add('label', 'label_tdir', 'tdir', '栏目模板:');
            $obj = &$form->add('select', 'tdir', '', array('style' => 'width:400px;'));
            $obj->add_options($select_list);

            $form->add('label', 'label_cdir', 'cdir', '内容模板:');
            $obj = &$form->add('select', 'cdir', '', array('style' => 'width:400px;'));
            $obj->add_options($select_content);
        
		 //设置栏目访问权限时使用（需要:1,修改数据库表 jl_arctype 字段 rank 为 char(50) 默认值为0. 2,开启 FormAction.class.php 的 rank 项 ）
			/*
            $form->add('label', 'label_rank', 'rank', '访问权限:');
            $form->add('checkboxes', 'rank[]',  array(0 => '完全开放', 1 => '普通会员', 2 => '企业会员'),array(0 => '0')); 
		    */

        if (session("superUser")) {
            $form->add('label', 'label_display', 'display', '显示状态:');
            $form->add('radios', 'display', array(1 => '均显示', 2 => '仅后台显示', 3 => '仅前台显示', 4 => '均不显示'), '1');
        } else {
            $form->add('label', 'label_display', 'display', '显示状态:');
            $form->add('radios', 'display', array(1 => '显示', 2 => '不显示'), '1');
        }

        // "submit"
        $form->add('submit', 'btnsubmit', '确定');
        $form_html = $form->render('*horizontal');
        $this->assign('form_html', $form_html);

        /*position指定以及一些问候信息*/
        $current = "栏目添加";
        $position = getPosition("栏目添加");
        $this->assign('current', $current);
        $this->assign('position', $position);
        $this->assign('welcome', getWelcome());
        $this->display('common:form');
    }
}
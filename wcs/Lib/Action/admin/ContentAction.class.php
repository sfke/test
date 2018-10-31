<?php

// 本类由系统自动生成，仅供测试用途
class ContentAction extends BaseAction
{
    public function show()
    {
        $tid = $this->_param("tid");

        if (!Permission::check($tid, "r")) {
            $this->error("您没有权限访问该栏目下的内容！");
        }

        import('ORG.Util.Page'); // 导入类
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
                    $orderby = $_POST['orderby'];
                    setcookie("orderby", $_POST['orderby']);
                    setcookie("map_orderby", $map_orderby);
                }

                if (!empty($_POST['searchkey'])) {
                    $this->assign('searchkey', $_POST['searchkey']);
                    $map[$_POST['searchby']] = array('like', '%' . $_POST['searchkey'] . '%');
                }
                $page_params = 'cid=' . $cid . '&tid=' . $tid;
            }
        } else {
            $tid = isset($_GET['tid']) ? $_GET['tid'] : null;
            $cid = isset($_GET['cid']) ? $_GET['cid'] : null;
            if ($tid === null || $cid === null) {
                $this->error("读取栏目信息出错！");
            }

            if (!empty($_COOKIE['map_orderby'])) {
                $map_orderby = $_COOKIE['map_orderby'];
                $orderby = $_COOKIE['orderby'];
            }
            $page_params = '';
        }

        $m2 = new ChannelModel();
        $m3 = new ArctypeModel();
        $arr = $m2->field('nid,type,addtable,fieldset')->where('id=' . $cid)->find();

        //获取栏目旗下所有子栏目数据
        $display = $m3->where("id=" . $tid)->getField("display");
        $isParentArctype = $m3->isParent($tid);
        if ($isParentArctype) {
            $tids = $m3->getAllSon($tid);
            $tids .= "," . $tid;
            $map['typeid'] = array('in', $tids);
        } else {
            $map['typeid'] = array('eq', $tid);
        }

        if ($arr['type'] == 1) {
            $m = new ArchivesModel();
            $map['status'] = array('neq', -1);
        } else if ($arr['type'] == 2) {
            $addtable = $arr['addtable'];
            $m = M($addtable);
        } else if ($arr['type'] == 3) { //单页模型
            $this->redirect("content/pageedit?tid=$tid&cid=$cid");
        } else {
            $this->error("模型类别不存在！");
            return;
        }

        if ($arr['type'] == 2) {
            $head_list = array();
            $fieldset = unserialize($arr['fieldset']);
            foreach ($fieldset as $k => $v) {
                if (array_key_exists("display", $v) && $v['display'] == 1) {
                    $head_list[$k]['name'] = $v['intro'];
                    $head_list[$k]['value'] = $v['name'];
                }
            }
            $this->assign("head_list", $head_list);
        }

        /*select准备*/
        if ($arr['type'] == 1) {
            $arrOrderby = array('id_desc' => 'ID 降序', 'id_asc' => 'ID 升序', 'channel_desc' => '内容模型 降序', 'channel_asc' => '内容模型 升序', 'typeid_asc' => '所属栏目 升序', 'typeid_desc' => '所属栏目  降序', 'click_asc' => '点击次数 升序', 'click_desc' => '点击次数 降序', 'pubdate_asc' => '发布日期 升序', 'pubdate_desc' => '发布日期 降序', 'scores_asc' => '内容评分 升序', 'scores_desc' => '内容评分 降序', 'sortrank_asc' => '内容权重 升序', 'sortrank_desc' => '内容权重 降序', 'dutyadmin_asc' => '内容发布员 升序', 'dutyadmin_desc' => '内容发布员 降序');
            $orderby_html = getOptions($arrOrderby, $orderby);
            $arrSearchby = array('title' => '标题', 'id' => '内容ID');
            $searchby_html = getOptions($arrSearchby, $_POST['searchby']);
        } else if ($arr['type'] == 2) {
            $arrOrderby = array('id_desc' => 'ID 降序', 'id_asc' => 'ID 升序', 'channel_desc' => '内容模型 降序', 'channel_asc' => '内容模型 升序', 'typeid_asc' => '所属栏目 升序', 'typeid_desc' => '所属栏目  降序');
            $orderby_html = getOptions($arrOrderby, $orderby);
            $arrSearchby = array('title' => '标题', 'id' => '内容ID');
            $searchby_html = getOptions($arrSearchby, $_POST['searchby']);
        }
        $this->assign('orderby_html', $orderby_html);
        $this->assign('searchby_html', $searchby_html);


        if ($arr['type'] == 1) {

            if (C('SYS_SAFE_MODE') == 0) {
                $chart_html = $m->contentChart($tid);
            } else {
                $chart_html = '';
            }
            $this->assign('chart_html', $chart_html);
        }

        //一些前台需要的隐含数据
        $sid = getSiteId();
        $this->assign('sid', $sid);
        $this->assign('cid', $cid);
        $this->assign('tid', $tid);
        $this->assign('display', $display);
        $this->assign('type', $arr['type']);
        //如果不是叶子节点，不显示添加按钮
        if ($isParentArctype) $this->assign('add_style', "style='display:none;'");
        else $this->assign('add_style', "");
        //如果是独立模型，不含主表，则不显示批量移动、添加属性等功能
        if ($arr['type'] == 2) $this->assign('type_style', "style='display:none;'");
        else $this->assign('type_style', "");

        //flags
        $flagSelect_html = '';
        $flagArr = C('SYS_FLAG_ARRAY');

        foreach ($flagArr as $k => $v) {
            $flagSelect_html .= "<input type='checkbox' name='flag[]' value='$k'  /> $v ";
        }
        $this->assign('flagSelect_html', $flagSelect_html);

        //可以推送至的站点
        $sitesArr = getAvailableSitesArr();
        $this->assign('sitesArr', $sitesArr);

        //排序
        $map_orderby = !empty($map_orderby) ? $map_orderby : 'id desc';

        /**
         * 获取推送过来的数据 开始
         */
        $pushidArr = getPushDataByTid($tid);
        if (!empty($pushidArr)) {
            foreach ($pushidArr as $value) {
                $idArr[] = $value['toid'];
                $pushArr[$value['toid']] = $value;
            }
        }

        /**
         * 获取推送过来的数据 结束
         */

        $count = $m->where($map)->count(); // 查询满足要求的总记录数
        $Page = new Page($count, C('SYS_PAGE_SIZE'), $page_params); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出

        //todo:如果是搜索，则在一页内显示所有数据(不分页)
        if (!empty($map[$_POST['searchby']])) {
            $Page->listRows = 1000;
            $show = "一共搜索到 " . $count . " 条数据";
        }

        //核心查询语句
        $list = $m->where($map)->limit($Page->firstRow . ',' . $Page->listRows)->order($map_orderby)->select();

        //对推送来的消息特殊处理
        foreach ($list as $k => $v) {
            if (in_array($v['id'], $idArr)) {
                $list[$k]['addattr'] .= "<span class='color_blue'>（推送）</span>";
                $list[$k]['syn'] = $pushArr[$v['id']]['syn'];
                $list[$k]['pushed'] = true;
            }
        }

        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出

        /*position指定以及一些问候信息*/
        $current = "内容管理列表";
        $position = getPosition("内容管理列表");
        $this->assign('current', $current);
        $this->assign('position', $position);
        $this->assign('welcome', getWelcome());
        $this->display();
    }

     /**
     * 内容未审核
     */
    public function nocheck()
    {
        import('ORG.Util.Page'); // 导入类
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
        } else {

        }

        $m = new ArchivesModel();
        $m2 = new ChannelModel();
        $m3 = new ArctypeModel();

        $map['status'] = array('eq', 0);
        //搜索和排序
        $arrOrderby = array('id_desc' => 'ID 降序', 'id_asc' => 'ID 升序', 'channel_desc' => '内容模型 降序', 'channel_asc' => '内容模型 升序', 'typeid_asc' => '所属栏目 升序', 'typeid_desc' => '所属栏目  降序', 'click_asc' => '点击次数 升序', 'click_desc' => '点击次数 降序', 'pubdate_asc' => '发布日期 升序', 'pubdate_desc' => '发布日期 降序', 'updatedate_asc' => '修改日期 升序', 'updatedate_desc' => '修改日期 降序', 'scores_asc' => '内容评分 升序', 'scores_desc' => '内容评分 降序', 'sortrank_asc' => '内容权重 升序', 'sortrank_desc' => '内容权重 降序', 'dutyadmin_asc' => '内容发布员 升序', 'dutyadmin_desc' => '内容发布员 降序');
        $orderby_html = getOptions($arrOrderby, $_POST['orderby']);
        $arrSearchby = array('title' => '标题', 'id' => '内容ID', 'typeid' => '所属栏目ID');
        $searchby_html = getOptions($arrSearchby, $_POST['searchby']);
        $this->assign('orderby_html', $orderby_html);
        $this->assign('searchby_html', $searchby_html);
        //排序
        $map_orderby = !empty($map_orderby) ? $map_orderby : 'id desc';

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

        /*position指定以及一些问候信息*/
        $current = "未审核内容";
        $position = getPosition("未审核内容");
        $this->assign('current', $current);
        $this->assign('position', $position);
        $this->assign('welcome', getWelcome());

        $this->display();
    }

    /**
     * 内容回收站
     */
    public function recycle()
    {
        import('ORG.Util.Page'); // 导入类
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
        } else {

        }

        $m = new ArchivesModel();
        $m2 = new ChannelModel();
        $m3 = new ArctypeModel();

        $map['status'] = array('eq', -1);
        //搜索和排序
        $arrOrderby = array('id_desc' => 'ID 降序', 'id_asc' => 'ID 升序', 'channel_desc' => '内容模型 降序', 'channel_asc' => '内容模型 升序', 'typeid_asc' => '所属栏目 升序', 'typeid_desc' => '所属栏目  降序', 'click_asc' => '点击次数 升序', 'click_desc' => '点击次数 降序', 'pubdate_asc' => '发布日期 升序', 'pubdate_desc' => '发布日期 降序', 'updatedate_asc' => '修改日期 升序', 'updatedate_desc' => '修改日期 降序', 'scores_asc' => '内容评分 升序', 'scores_desc' => '内容评分 降序', 'sortrank_asc' => '内容权重 升序', 'sortrank_desc' => '内容权重 降序', 'dutyadmin_asc' => '内容发布员 升序', 'dutyadmin_desc' => '内容发布员 降序');
        $orderby_html = getOptions($arrOrderby, $_POST['orderby']);
        $arrSearchby = array('title' => '标题', 'id' => '内容ID', 'typeid' => '所属栏目ID');
        $searchby_html = getOptions($arrSearchby, $_POST['searchby']);
        $this->assign('orderby_html', $orderby_html);
        $this->assign('searchby_html', $searchby_html);
        //排序
        $map_orderby = !empty($map_orderby) ? $map_orderby : 'id desc';

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

        /*position指定以及一些问候信息*/
        $current = "内容回收站";
        $position = getPosition("内容回收站");
        $this->assign('current', $current);
        $this->assign('position', $position);
        $this->assign('welcome', getWelcome());

        $this->display();
    }

    public function pageedit()
    {
        $tid = $this->_param("tid");
        if (!Permission::check($tid, "w")) {
            $this->error("您没有权限编辑该栏目！");
        }

        require(APP_INC_PATH . 'form/Zebra_Form.php');
        $tid = isset($_GET['tid']) ? $_GET['tid'] : null;
        $cid = isset($_GET['cid']) ? $_GET['cid'] : null;

        if ($tid == null || $cid == null) {
            $this->error("内容模型错误！");
            return;
        }

        $m2 = new ChannelModel();
        $arr2 = $m2->field('addtable,fieldset,type,title')->where('id=' . $cid)->find();
        $addtable = $arr2['addtable'];
        $m = M($addtable);
        $odata = $m->where("typeid =" . $tid)->find();
        //附表数据还没有建立
        if (empty($odata)) {
            $newArr['typeid'] = $tid;
            $newArr['channel'] = $cid;
            $m->create($newArr);
            if ($m->add() === false) {
                $this->error("打开单页失败！");
                return;
            } else {
                $odata['id'] = $m->getLastInsID();
            }
        }

        $fields = unserialize($arr2['fieldset']);

        $form = new Zebra_Form('form', 'post', U('form/pageupdate'));
        $form->add('text', 'id', $odata['id'], array('type' => 'hidden'));
        $form->add('text', 'addtable', $addtable, array('type' => 'hidden'));

        AutoForm($form, $fields, $odata);

        if (in_array($tid, C('SYS_IMGUPLOAD_ARCTYPE'))) {
            $form->add('button', '', "批量图片【上传修改】", array("onclick" => "location.href='" . U('content/imgupload?tid=' . $tid) . "'"));
        }

        $form->add('submit', 'btnsubmit', '确定');
        $rs = $form->render('*horizontal');
        $this->assign('form_html', $rs);

        /*position指定以及一些问候信息*/
        $m2 = M('Arctype');
        $arr = $m2->field("id,name")->where("id = $tid")->find();
        $current = $arr['name'] . "（修改）";
        $position = getPosition("单页修改");
        $this->assign('current', $current);
        $this->assign('position', $position);
        $this->assign('welcome', getWelcome());
        $this->display('common:form');
    }


    public function edit()
    {
        require(APP_INC_PATH . 'form/Zebra_Form.php');
        $aid = isset($_GET['aid']) ? $_GET['aid'] : null;
        $cid = isset($_GET['cid']) ? $_GET['cid'] : null;
        $read = isset($_GET['read']) ? $_GET['read'] : null;
        if ($aid == null || $cid == null) {
            $this->error("读取内容 id 出错！");
            return;
        }
        $m2 = new ChannelModel();
        $arr2 = $m2->field('nid,fieldset,type,title')->where('id=' . $cid)->find();
        $nid = $arr2['nid'];
        if ($nid == null) {
            $this->error('读取附加表失败！');
        }
        $fields = unserialize($arr2['fieldset']);
        $addtable = 'add' . $nid;
        $m2 = new ArctypeModel();
        $selection = $m2->arctypeArr();
        $selection_c = $m2->arctypeArrC2($cid);
        $select_content = getTpl('cont');
        //判断是独立模型还是联合模型
        if ($arr2['type'] == 1) {
            $m = new ArchivesModel();
            $m->_link['addfields']['class_name'] = $addtable;
            $odata = $m->relation(true)->where('id=' . $aid)->find();
            $odata['flag'] = explode(',', $odata['flag']);
            //$arr = $m->where('id='.$aid)->find();

            $form = new Zebra_Form('form', 'post', U('form/contentupdate')); //参数分别是 表单名称 提交方法 请求页面

            //隐藏表单
            $form->add('text', 'nid', $arr2['nid'], array('type' => 'hidden'));
            $form->add('text', 'id', $aid, array('type' => 'hidden'));
            $form->add('text', 'cid', $cid, array('type' => 'hidden'));
            //$form->add('text', 'channel',$cid,array('type' => 'hidden'));

            $form->add('label', 'label_typeid', 'typeid', '所属栏目:', array('style' => 'width:80px;'));
            $obj = & $form->add('select', 'typeid', $odata['typeid']);
            $obj->add_options($selection, false, $selection_c);
            $obj->set_rule(array(
                'required' => array('error', '必须选择所属栏目!')
            ));

            /*
			$form->add('label', 'label_typeid2', 'typeid2', '副栏目:');
			$obj = & $form->add('select', 'typeid2', $odata['typeid2']);
			$obj->add_options($selection,false,$selection_c);
			*/

            $form->add('label', 'label_title', 'title', $arr2['title'] . ':');
            $obj = & $form->add('text', 'title', $odata['title'], array('style' => 'width:400px'));
            $obj->set_rule(array(
                'required' => array('error', '必须填写标题!')
            ));

           /* 
            $form->add('label', 'label_color', 'color', '标题颜色:'); //空间名  id  for属性 里面的话
            $obj = & $form->add('color', 'color',$odata['color']);  //不要改id
            $obj->set_rule(array(
                    //'required' => array('error', '请输入字段默认值!')
            ));
            */
            $form->add('label', 'label_shorttitle', 'shorttitle', '副标题:');
            $obj = & $form->add('text', 'shorttitle',$odata['shorttitle'],array('style' => 'width:400px'));
           

            $form->add('label', 'label_desc', 'desc', '简介:');
            $obj = & $form->add('textarea', 'desc', $odata['desc']);

            $form->add('label', 'label_keywords', 'keywords', '关键字:');
            $form->add('text', 'keywords', $odata['keywords'], array('style'=>'width:400px'));

            // "flag"
            $form->add('label', 'label_flag', 'flag', '自定义属性:');
            $form->add('checkboxes', 'flag[]', C('SYS_FLAG_ARRAY'), $odata['flag']);

            //电子书封面
            if( in_array($odata['typeid'],C('SYS_IMGUPLOAD_EBOOK'))) {
				$form->add('label', 'label_cover', 'cover', '封面:');
            	$form->add('Kimg', 'cover', $odata['cover'], array('style'=>'width:400px'));
			}
			
			//期刊封面
			if( in_array($cid,C('SYS_QIKAN_CHANNEL'))) {
				$form->add('label', 'label_cover', 'cover', '期刊锚点:');
            	$form->add('text', 'cover', $odata['cover'], array('style'=>'width:160px','onclick'=>'addqkmap()'));
			}
			
			
            if (session("superUser")) {
                $form->add('label', 'label_cdir', 'cdir', '内容模板:');
                $obj = & $form->add('select', 'cdir', $odata['cdir'], array('style' => 'width:400px;'));
                $obj->add_options($select_content);
            }

            $form->add('label', 'label_pubdate', 'pubdate', '发布时间:');
            if(!C('SYS_DATETIME_MODE')){
                $form->add('datetime', 'pubdate', date('Y-m-d', $odata['pubdate']), array('style' => 'width:130px;'));
            }else{
                $form->add('datetime', 'pubdate', date('Y-m-d H:i:s', $odata['pubdate']), array('style' => 'width:130px;', 'config' => "{dateFmt:'yyyy-MM-dd HH:mm:ss'}"));
            }

            $form->add('label', 'label_author', 'author', '作者:');
            $form->add('text', 'author',$odata['author'],array('style' => 'width:200px'));

            /*$form->add('label', 'label_source', 'source', '来源:');
            $form->add('text', 'source',$odata['source'],array('style' => 'width:200px'));*/

            //自定义字段表单
            AutoForm($form, $fields, $odata['addfields']);

        } else if ($arr2['type'] == 2) {
            $m = M($addtable);
            $odata = $m->where('id=' . $aid)->find();

            $form = new Zebra_Form('form', 'post', U('form/sgupdate'));
            $form->add('text', 'nid', $arr2['nid'], array('type' => 'hidden'));
            $form->add('text', 'id', $aid, array('type' => 'hidden'));
            $form->add('text', 'cid', $cid, array('type' => 'hidden'));
            $form->add('label', 'label_typeid', 'typeid', '所属栏目:', array('style' => 'width:80px;'));
            $obj = & $form->add('select', 'typeid', $odata['typeid']);
            $obj->add_options($selection, false, $selection_c);
            $obj->set_rule(array(
                'required' => array('error', '必须选择所属栏目!')
            ));

            if (session("superUser")) {
                $form->add('label', 'label_cdir', 'cdir', '内容模板:');
                $obj = & $form->add('select', 'cdir', $odata['cdir'], array('style' => 'width:400px;'));
                $obj->add_options($select_content);
            }
            AutoForm($form, $fields, $odata);
        } else {
            $this->error("模型类别不存在！");
            return;
        }

        // 获取 栏目 设置的 内容图片尺寸 开始     
		$litimg_size = $m2->where('id=' . $odata['typeid'])->getField("litimg_size");
		if($litimg_size==null){
		   $_SESSION['litimg_size']=null;
		}else{
		   $_SESSION['litimg_size']='<br/>尺寸(px)：<br/>'.$litimg_size;
		}
		// 获取 栏目 设置的 内容图片尺寸 结束
		
        $form->add('label', 'label_sortrank', 'sortrank', '权重:');
        $form->add('text', 'sortrank', $odata['sortrank'], array('style' => 'width:100px'));

        if (Permission::check($odata['typeid'], "c")) {
            $form->add('label', 'label_status', 'status', '审核状态:');
            $form->add('radios', 'status', array('0' => '未审核', '1' => '已审核'), $odata['status']);
        }

        if (empty($read)) {
            if (!Permission::check($odata['typeid'], "w")) {
                $this->error("您没有权限编辑该栏目下的内容！");
            }
            $form->add('submit', 'btnsubmit', '确定');
        } else {
            if (!Permission::check($odata['typeid'], "r")) {
                $this->error("您没有权限阅读该栏目下的内容！");
            }
            $form->add('button', 'return', '返回', array("onclick" => "javascript:history.go(-1)"));
        }

        $rs = $form->render('*horizontal');
        $this->assign('form_html', $rs);

        /*position指定以及一些问候信息*/
        $current = "内容修改";
        $position = getPosition("内容修改");
		$this->assign('tid', $odata['typeid']);
        $this->assign('current', $current);
        $this->assign('position', $position);
        $this->assign('welcome', getWelcome());

        $this->display('common:form');
    }


    public function add()
    {
        $tid = $this->_param("tid");
        if (!Permission::check($tid, "w")) {
            $this->error("您没有权限添加内容！");
        }

        require(APP_INC_PATH . 'form/Zebra_Form.php');

        $tid = isset($_GET['tid']) ? $_GET['tid'] : null;
        $cid = isset($_GET['cid']) ? $_GET['cid'] : null;
        if ($tid == null || $cid == null) {
            $this->error("读取栏目模型出错！");
            return;
        }

        $m = new ChannelModel();
        $arr = $m->where('id=' . $cid)->find();

        $fields = unserialize($arr['fieldset']);

        $m2 = new ArctypeModel();
        $selection = $m2->arctypeArr();
        $selection_c = $m2->arctypeArrC2($cid);
        $select_content = getTpl('cont');
		
		// 获取 栏目 设置的 内容图片尺寸 开始
		$litimg_size = $m2->where('id=' . $tid)->getField("litimg_size");
		if($litimg_size==null){
		   $_SESSION['litimg_size']=null;
		}else{
		   $_SESSION['litimg_size']='<br/>尺寸(px)：<br/>'.$litimg_size;
		}
		// 获取 栏目 设置的 内容图片尺寸 结束
		
        if ($arr['type'] == 1) {
            $form = new Zebra_Form('form', 'post', U('form/contentsave')); //参数分别是 表单名称 提交方法 请求页面

            //隐藏表单
            $form->add('text', 'nid', $arr['nid'], array('type' => 'hidden'));
            $form->add('text', 'channel', $cid, array('type' => 'hidden'));

            $form->add('label', 'label_typeid', 'typeid', '所属栏目:', array('style' => 'width:80px;'));
            $obj = & $form->add('select', 'typeid', $tid);
            $obj->add_options($selection, false, $selection_c);
            $obj->set_rule(array(
                'required' => array('error', '必须选择所属栏目!')
            ));
            /*
            $form->add('label', 'label_typeid2', 'typeid2', '副栏目:');
            $obj = & $form->add('select', 'typeid2', '');
            $obj->add_options($selection,false,$selection_c);
             */

            $form->add('label', 'label_title', 'title', $arr['title'] . ':');
            $obj = & $form->add('text', 'title', '', array('style' => 'width:400px'));
            $obj->set_rule(array(
                'required' => array('error', '必须填写标题!')
            ));

            /*
            $form->add('label', 'label_color', 'color', '标题颜色:'); //空间名  id  for属性 里面的话
            $obj = & $form->add('color', 'color','#000000');  //不要改id
            $obj->set_rule(array(
                    //'required' => array('error', '请输入字段默认值!')
            ));
           */
            $form->add('label', 'label_shorttitle', 'shorttitle', '副标题:');
            $obj = & $form->add('text', 'shorttitle','',array('style' => 'width:400px'));
            $obj->set_rule(array(

            ));
            
            $form->add('label', 'label_desc', 'desc', '简介:');
            $form->add('textarea', 'desc', '');

            $form->add('label', 'label_keywords', 'keywords', '关键字:');
            $form->add('text', 'keywords', '', array('style'=>'width:400px'));

            // "flag"
            $form->add('label', 'label_flag', 'flag', '自定义属性:');
            $form->add('checkboxes', 'flag[]', C('SYS_FLAG_ARRAY'));
            
			//电子书封面 
			if( in_array($tid,C('SYS_IMGUPLOAD_EBOOK'))) {
				$form->add('label', 'label_cover', 'cover', '封面:');
	            $form->add('Kimg', 'cover', '', array('style'=>'width:400px'));
			}
			
			//期刊封面
			if( in_array($cid,C('SYS_QIKAN_CHANNEL'))) {
				$form->add('label', 'label_cover', 'cover', '期刊锚点:');
            	$form->add('text', 'cover', '', array('style'=>'width:160px','onclick'=>'addqkmap();'));
			}
			
            if (session("superUser")) {
                $form->add('label', 'label_cdir', 'cdir', '内容模板:');
                $obj = & $form->add('select', 'cdir', $arr['cdir'], array('style' => 'width:400px;'));
                $obj->add_options($select_content);
            }

            $form->add('label', 'label_pubdate', 'pubdate', '发布时间:');
            if(!C('SYS_DATETIME_MODE')){
                $form->add('datetime', 'pubdate', date('Y-m-d', time()), array('style' => 'width:130px;'));
            }else{
                $form->add('datetime', 'pubdate', date('Y-m-d H:i:s', time()), array('style' => 'width:130px;', 'config' => "{dateFmt:'yyyy-MM-dd HH:mm:ss'}"));
            }

            $form->add('label', 'label_author', 'author', '作者:');
            $form->add('text', 'author','',array('style' => 'width:200px'));

            /*$form->add('label', 'label_source', 'source', '来源:');
            $form->add('text', 'source','',array('style' => 'width:200px'));*/

            //自定义字段表单
            AutoForm($form, $fields);

        } else if ($arr['type'] == 2) {

            $form = new Zebra_Form('form', 'post', U('form/sgsave'));
            $form->add('text', 'nid', $arr['nid'], array('type' => 'hidden'));
            $form->add('text', 'channel', $cid, array('type' => 'hidden'));
            $form->add('label', 'label_typeid', 'typeid', '所属栏目:', array('style' => 'width:80px;'));
            $obj = & $form->add('select', 'typeid', $tid);
            $obj->add_options($selection, false, $selection_c);
            $obj->set_rule(array(
                'required' => array('error', '必须选择所属栏目!')
            ));

            if (session("superUser")) {
                $form->add('label', 'label_cdir', 'cdir', '内容模板:');
                $obj = & $form->add('select', 'cdir', $arr['cdir'], array('style' => 'width:400px;'));
                $obj->add_options($select_content);
            }
            AutoForm($form, $fields);
        } else {
            $this->error("模型不存在！");
        }

        if (session("superUser") || session("adminUser")) {
            $form->add('label', 'label_status', 'status', '审核状态:');
            $obj = & $form->add('radios', 'status', array('0' => '未审核', '1' => '已审核'), 1);
        }

        $form->add('submit', 'btnsubmit', '确定');
        $rs = $form->render('*horizontal');
        $this->assign('form_html', $rs);

        /*position指定以及一些问候信息*/
        $current = "内容添加";
        $position = getPosition("内容添加");
		$this->assign('tid', $tid);
        $this->assign('current', $current);
        $this->assign('position', $position);
        $this->assign('welcome', getWelcome());

        $this->display('common:form');
    }


    /**
     * 删除内容
     */
    public function del()
    {
        $id = isset($_GET['aid']) ? $_GET['aid'] : null;
        $cid = isset($_GET['cid']) ? $_GET['cid'] : null;
        if ($id == null || $cid == null) {
            $this->error("读取内容模型出错！");
            return;
        }
        $m2 = new ChannelModel();
        $arr2 = $m2->field('nid,fieldset,type')->where('id=' . $cid)->find();
        $nid = $arr2['nid'];
        if ($nid == null) {
            $this->error('读取附加表失败！');
        }

        $addtable = 'add' . $nid;
        if ($arr2['type'] == 1) {
            $m = new ArchivesModel();
            $m->_link['addfields']['class_name'] = $addtable;
            if ($m->relation(true)->delete($id) !== false) {
                $this->success("删除内容成功！");
            } else {
                $this->error("删除内容失败！");
            }
        } else if ($arr2['type'] == 2) {
            $m = M($addtable);
            if ($m->where('id=' . $id)->delete() !== false) {
                $this->success("删除内容成功！");
            } else {
                $this->error("删除内容失败！");
            }
        } else {
            $this->error("模型类型不存在！");
        }
    }


    public function imgupload()
    {
        $gid = isset($_GET['gid']) ? $_GET['gid'] : null;
        $tid = isset($_GET['tid']) ? $_GET['tid'] : null;
        if ($gid == null && $tid == null) {
            $this->error("读取图册id失败！");
        }
        require(APP_INC_PATH . 'form/Zebra_Form.php');

        $images_gid = !empty($gid) ? $gid : $tid;
        //商品相册
        $m7 = M('images');
        $goodsImgArr = $m7->where('gid=' . $images_gid)->order('sort desc')->select();
        $goodsImgHtml = '';
        $j = 0;
        foreach ($goodsImgArr as $v) {
            if ($j % 2 == 0)
                $flag = "even";
            else
                $flag = "";
            $j++;
            $goodsImgHtml .= '<tr class="row ' . $flag . '"><td style="width:60px;"><a href="' . $v['url'] . '"><img style="border:1px solid #1A1A1A;width:50px"  src="' . $v['url'] . '" /></a></td><td><label>排序：</label></td><td><input _id="' . $v['id'] . '" class="text orderid" style="width:20px" type="text" name="sort[]" value="' . $v['sort'] . '"></input></td><td><label>标题：</label></td><td><input type="hidden" name="images[]" value="' . $v['url'] . '" ></input><input type="text" class="control text" name="intro[]" value="' . $v['intro'] . '" ></input></td> <td><label>外链：</label></td><td><input type="text" class="control text" name="href[]" value="' . $v['href'] . '" /></td> <td><input type="button" class="control button"  value="移除" onclick="removeImg(this);" ></td><td></td></tr>';
        }
        $this->assign('goodsImgHtml', $goodsImgHtml);


        /* position指定以及一些问候信息 */
        $current = "批量上传图片";

        $m = new ArchivesModel();
        if (!empty($gid) && empty($tid)) {
            $arr = $m->where('id=' . $gid)->find();
            $position = getPosition(array($arr['title'] => '__GROUP__/content/edit?aid=' . $arr['id'] . '&cid=' . $arr['channel'], '批量上传图片' => ''));
            $this->assign('type', 1);
            $this->assign('gid', $gid);
        } else if (empty($gid) && !empty($tid)) {
            $arr = $m->where('id=' . $tid)->find();
            $position = getPosition(array($arr['name'] => '__GROUP__/content/show?tid=' . $tid . '&cid=' . $arr['channel'], '批量上传图片' => ''));
            $this->assign('type', 2);
            $this->assign('gid', $tid);
        }

        $this->assign('current', $current);
        $this->assign('position', $position);
        $this->assign('welcome', getWelcome());
        $this->display('imgform');
    }

    public function feedbackShow()
    {
        import('ORG.Util.Page'); // 导入类
        $m = M('archivesFeedback');
        if (!empty($_POST['action'])) {
            $aid = isset($_POST['aid']) ? $_POST['aid'] : null;
            $status = isset($_POST['status']) ? $_POST['status'] : null;
            if ($aid != null) {
                $map['aid'] = array('eq', $aid);
            }

            if ($status != null && ($status == 1 || $status == 2)) {
                if ($status == 1) $statuss = '未审核';
                else $statuss = '已审核';
                $map['status'] = array('eq', $statuss);
            }

            if ($_POST['action'] == 'filter') {
                if (!empty($_POST['orderby'])) {
                    $arr = orderByParse($_POST['orderby']);
                    if (is_array($arr)) {
                        $map_orderby = "`$arr[0]` $arr[1]";
                    }
                    $orderby = $_POST['orderby'];
                    setcookie("orderby", $_POST['orderby']);
                    setcookie("map_orderby", $map_orderby);
                }

                if (!empty($_POST['searchkey'])) {
                    $this->assign('searchkey', $_POST['searchkey']);
                    $map[$_POST['searchby']] = array('like', '%' . $_POST['searchkey'] . '%');
                }
            }
            $page_params = 'status=' . $status;
        } else {
            $aid = isset($_GET['aid']) ? $_GET['aid'] : null;
            if ($aid != null) {
                $map['aid'] = array('eq', $aid);
            }

            $status = isset($_GET['status']) ? $_GET['status'] : null;
            if ($status != null && ($status == 1 || $status == 2)) {
                if ($status == 1) $statuss = '未审核';
                else $statuss = '已审核';
                $map['status'] = array('eq', $statuss);
            }

            if (!empty($_COOKIE['map_orderby'])) {
                $map_orderby = $_COOKIE['map_orderby'];
                $orderby = $_COOKIE['orderby'];
            }
            $page_params = '';
        }

        $arrOrderby = array('id_desc' => 'ID 降序', 'id_asc' => 'ID 升序', 'aid_desc' => '被评文章 降序', 'aid_asc' => '被评文章 升序', 'pubdate_asc' => '评论时间 升序', 'pubdate_desc' => '评论时间 降序', 'status_asc' => '审核状态 升序', 'status_desc' => '审核状态 降序');
        $orderby_html = getOptions($arrOrderby, $orderby);
        $arrSearchby = array('title' => '评论标题', 'uname' => '评论人姓名', 'userid' => '评论人用户名');
        $searchby_html = getOptions($arrSearchby, $_POST['searchby']);
        $this->assign('orderby_html', $orderby_html);
        $this->assign('searchby_html', $searchby_html);
        $this->assign('aid', $aid);
        $this->assign('status', $status);

        //排序
        $map_orderby = !empty($map_orderby) ? $map_orderby : 'id desc';

        $count = $m->where($map)->count(); // 查询满足要求的总记录数
        $Page = new Page($count, C('SYS_PAGE_SIZE'), $page_params); // 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show(); // 分页显示输出

        //如果是搜索，则在一页内显示所有数据(不分页)
        if (!empty($map[$_POST['searchby']])) {
            $Page->listRows = 1000;
            $show = "一共搜索到 " . $count . " 条数据";
        }

        $list = $m->where($map)->limit($Page->firstRow . ',' . $Page->listRows)->order($map_orderby)->select();
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $show); // 赋值分页输出

        /*position指定以及一些问候信息*/
        $current = "新闻评论管理列表";
        $position = getPosition($current);
        $this->assign('current', $current);
        $this->assign('position', $position);
        $this->assign('welcome', getWelcome());
        $this->display();
    }


    public function feedbackedit()
    {
        require(APP_INC_PATH . 'form/Zebra_Form.php');
        $fbid = isset($_GET['id']) ? $_GET['id'] : null;
        if ($fbid == null) {
            $this->error("读取评论 id 出错！");
            return;
        }
        $m = M('archivesFeedback');

        $odata = $m->where('id=' . $fbid)->find();

        $form = new Zebra_Form('form', 'post', U('extform/archivesfeedbacksave')); //参数分别是 表单名称 提交方法 请求页面

        //隐藏表单
        $form->add('text', 'id', $fbid, array('type' => 'hidden'));

        $form->add('label', 'label_title', 'title', '评论标题:');
        $obj = & $form->add('text', 'title', $odata['title'], array('style' => 'width:400px'));
        $obj->set_rule(array(
            'required' => array('error', '必须填写标题!')
        ));

        $form->add('label', 'label_txt', 'txt', '评论正文:');
        $obj = & $form->add('kind', 'txt', $odata['txt'], array('style' => 'width:700px;height:300px;'));

        $form->add('label', 'label_top', 'top', '顶:');
        $obj = & $form->add('text', 'top', $odata['top'], array('style' => 'width:50px'));

        $form->add('label', 'label_down', 'down', '踩:');
        $obj = & $form->add('text', 'down', $odata['down'], array('style' => 'width:50px'));

        $form->add('label', 'label_status', 'status', '状态:');
        $obj = & $form->add('radios', 'status', array(
            '未审核' => '未审核',
            '已审核' => '已审核'
        ), $odata['status']);
        $obj->set_rule(array(
            'required' => array('error', '必须选择状态！')
        ));

        // "submit"
        $form->add('submit', 'btnsubmit', '确定');
        $rs = $form->render('*horizontal');
        $this->assign('form_html', $rs);

        /*position指定以及一些问候信息*/
        $current = "新闻评论编辑";
        $position = getPosition(array("新闻评论列表" => '__GROUP__/content/feedbackshow', "新闻评论编辑" => ""));
        $this->assign('current', $current);
        $this->assign('position', $position);
        $this->assign('welcome', getWelcome());
        $this->display('common:form');
    }

    //生成电子期刊时，要copy文件夹ebook并且用id当后缀名
    public function electronic()
    {
		$confarr=require(APP_PATH. 'Conf/config.php');	
        $gid = isset($_GET['gid']) ? $_GET['gid'] : 0;
        $m = M('images');
        $imageArr = $m->where('gid=' . $gid)->order('sort asc')->select();

        //创建相对应的ebook文件夹
        $src = ROOT_PATH . "ebook";
        $dst = ROOT_PATH . "ebook" . $gid;

        if (count($imageArr) > 0) {
            $this->recurse_copy($src, $dst);
            $dom = new DOMDocument('1.0', 'UTF-8'); //创建xml文档
            //$dom=new DOMDocument(); //创建xml文档
            $dom->formatOutput = true;
            $rootelement = $dom->createElement('content'); //创建root根节点
            $rootelement->setAttribute("width", "368");
            $rootelement->setAttribute("height", "507");

            foreach ($imageArr as $key => $value) {
                $pagelement = $dom->createElement('page');
                $pagelement->setAttribute('src', $value['url']);
                $pagelement->setAttribute('href', C('JL_BASEHOST') . C('JL_CMSPATH') . "index.php/index".$confarr["URL_PATHINFO_DEPR"]."view".$confarr["URL_PATHINFO_DEPR"]."aid".$confarr["URL_PATHINFO_DEPR"]."$gid".$confarr["URL_PATHINFO_DEPR"]."img".$confarr["URL_PATHINFO_DEPR"].$value['id'].".".$confarr["SYS_TPL_EXTEND"]);

                $rootelement->appendChild($pagelement);
            }

            $dom->appendChild($rootelement);
            $filename = $dst . "/Pages.xml";
            //echo $filename; exit;
            echo $dom->save($filename);
            $this->success('生成期刊成功!');
        } else {
            $this->error("该期刊下没有内容，无法生成！");
        }
    }

    public function recurse_copy($src, $dst)
    { // 原目录，复制到的目录
        $dir = opendir($src);
        @mkdir($dst);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    $this->recurse_copy($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }
}
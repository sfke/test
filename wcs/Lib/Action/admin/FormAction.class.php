<?php

/**
 * 统一处理form表单
 */
class FormAction extends Action
{
    /**
     * ===========================================
     * Arctype start
     * ===========================================
     */

    /**
     * 处理栏目表单（修改）
     */
    public function arctypeupdate()
    {
        if ($_SESSION['zebra_csrf_token_form'][0] != $_POST['zebra_csrf_token_form']) {
            $this->error("请勿重复提交！");
        }
        $fid = $this->_post("fid");
		if($this->_post("id")==$fid){
		    $this->error("该栏目不能为自己的下级栏目!");exit; 	
		}
        $m = new ArctypeModel();
        $fArr = $m->field("id,channel")->where("id =" . $fid)->find();
        if($_POST['oldfid'] != $fid){
            $hasson = $m->where("fid=".$this->_post("id"))->count();
            if($hasson){
                $this->error("请先移动该栏目下的下级栏目!");exit;
            }

            //order 计算
            $order = $m->field('order')->where('fid=' . $fid)->max('`order`');
            $order = $order ? ++$order : 0;
            $data2['order'] = $order;
        }
        if (!empty($fArr)) {
            $jump = "__GROUP__/arctype/show?tid=" . $fArr['id'] . "&cid=" . $fArr['channel'];
        } else {
            $jump = "";
        }
        $m->create();
        if ($m->save() === false) {
            $this->error("栏目修改失败!");
            return;
        } else {
            $fArray = $m->field('id,route,channel')->where('id=' . $fid)->find();

            if (empty($fArray)) {
                $data2['route'] = 0;
            } else {
                $data2['route'] = $fArray['route'] . "-" . $fArray['id'];
            }
            $data2['id'] = $this->_post("id");
			//设置栏目访问权限时使用(配合ArctypeAction.class.php开启 rank 选项使用)
			/*
			$rank = $this->_post("rank");
		    $rank = implode(',',$rank);
		    $data2['rank'] = $rank;
            */
            $m->create($data2);
            $m->save();

            $this->success("栏目修改成功!", $jump);
            return;
        }
    }


    /**
     * 处理栏目表单（添加）
     */
    public function arctypesave()
    {
        //添加栏目前先备份栏目结构
        R("Admin/Backup/backup", array(array("jl_arctype")));

        if ($_SESSION['zebra_csrf_token_form'][0] != $_POST['zebra_csrf_token_form']) {
            $this->error("请勿重复提交！");
        }

        $m = new ArctypeModel();
        $fid = $this->_post('fid');
        $cid = $this->_post('cid');

        $m2 = new ChannelModel();
        $channlArr = $m2->getOne($cid);
        $type = $channlArr['type'];

        $fArray = $m->field('id,route,channel')->where('id=' . $fid)->find();

        //order 计算
        $order = $m->field('order')->where('fid=' . $fid)->max('`order`');
        $order = !is_null($order) ? ++$order : 0;

        $data = $_POST;
        $data['channel'] = $cid;
        $data['order'] = $order;
        $data['status'] = 1;
        $data['type'] = $type;
		//设置栏目访问权限时使用(配合ArctypeAction.class.php开启 rank 选项使用)
		/*
		$rank = $this->_post("rank");
		$rank = implode(',',$rank);
		$data['rank'] = $rank;
        */
        $data['siteid'] = getSiteId();
        $m->create($data);
        if ($m->add() === false) {
            $this->error("添加栏目失败！");
            return;
        } else {
            $data2 = array();
            $id = $m->getLastInsID();
            $data2['id'] = $id;
            if (empty($fArray)) {
                $data2['route'] = 0;
            } else {
                $data2['route'] = $fArray['route'] . "-" . $fArray['id'];
            }

            $m->create($data2);
            if ($m->save() === false) {
                $this->error("栏目路由保存失败！");
            } else {
                //清除缓存
                if (is_dir(TEMP_PATH)) {
                    mydel(TEMP_PATH);
                }

                //todo type为3的模型固定位单页模型
                if ($type == 3) {
                    $addtable = $channlArr['addtable'];
                    $mpage = M($addtable);
                    $pagedata = array();
                    $pagedata['typeid'] = $data['id'];
                    $pagedata['channel'] = $cid;
                    $mpage->create($pagedata);
                    $mpage->add();
                }
                $this->success("添加栏目成功！");
            }
        }
    }

    /**栏目批量移动*/
    public function arctypemove()
    {
        //移动前先备份栏目结构
        R("Admin/Backup/backup", array(array("jl_arctype")));

        $items = $this->_post('items');
        $oid = $this->_post('oldtid');
        $totid = $this->_post('totid');

        if ($oid == $totid) {
            $this->error("没有移动任何栏目！");
            return;
        }

        $m = new ArctypeModel();
        $itemsArr = explode(',', $items);

        if (in_array($totid, $itemsArr)) {
            $this->error("移动操作有误！");
            return;
        }

        foreach ($itemsArr as $v) {
            $oid_son = $m->getAllSon($v);
            $oldtidArr = $m->field('route')->where('id=' . $v)->find();
            $oldRoute = $oldtidArr['route'];

            //获取新栏目id及其route属性
            if ($totid == 0) $route = 0; else {
                $toarray = $m->field('route,channel')->where('id=' . $totid)->find();
                $tocid = $toarray['channel'];
                $route = $toarray['route'] . "-" . $totid;
            }

            $orders = $m->field('order')->where('fid=' . $totid)->select();

            $order = 0;
            foreach ($orders as $v2) {
                if ($order > $v2['order']) continue;
                else {
                    $order = $v2['order'];
                    $order++;
                }
            }
            $data = array();
            $data['id'] = $v;
            $data['fid'] = $totid;
            $data['route'] = $route;
            $data['order'] = $order;
            if ($m->save($data) === false) {
                $this->error("在移动 id 为 $v 的栏目时发生意外，批量移动失败！");
                return;
            }

            //移动子栏目
            if ($oid_son != '') {
                $oid_sonArr = explode(',', $oid_son);

                foreach ($oid_sonArr as $sonid) {
                    $tempArr = $m->field('route')->where('id=' . $sonid)->find();
                    $tempRoute = $tempArr['route'];
                    $tempRoute = str_replace($oldRoute, $route, $tempRoute);
                    $data = array();
                    $data['id'] = $sonid;
                    $data['route'] = $tempRoute;
                    if ($m->save($data) === false) {
                        $this->error("在移动 id 为 $v 的栏目旗下的 $sonid 的子栏目时发生意外，批量移动失败！");
                        return;
                    }
                }
            }
        }

        //清除缓存
        if (is_dir(TEMP_PATH)) {
            mydel(TEMP_PATH);
        }
        $this->success("批量移动栏目成功！", "__GROUP__/arctype/show?tid=$totid&cid=$tocid");
        return;
    }


    /**
     * ===========================================
     * Arctype end
     * ===========================================
     */


    /**
     * ===========================================
     * Channel start
     * ===========================================
     */
    /**
     * 模型表单处理
     */
    public function channelsave()
    {
        if ($_SESSION['zebra_csrf_token_form'][0] != $_POST['zebra_csrf_token_form']) {
            $this->error("请勿重复提交！");
        }

        $m = new ChannelModel();
        $id = isset($_POST['id']) ? $_POST['id'] : null;
        $type = isset($_POST['type']) ? $_POST['type'] : 1;
        $issystem = isset($_POST['issystem']) ? $_POST['issystem'] : 1;
        if ($id == null) {
            $m->create();
            if ($issystem == 2) $m->addtable = trim($_POST['nid']);
            else $m->addtable = 'add' . trim($_POST['nid']);
            //创建表
            //如果为系统内容模型 含主表
            if ($type == 1 && $issystem == 1) {
                $tabsql = "CREATE TABLE `" . C('DB_PREFIX') . $m->addtable . "`( `aid` int(11) NOT NULL default '0'";
                $tabsql .= " PRIMARY KEY ) ENGINE=MyISAM DEFAULT CHARSET=" . C('DB_CHARSET') . "; ";
            } else {
                //如果是系统内容模型 则多出一个栏目属性
                if ($type == 2 && $issystem == 1) { //系统模型 不含主表
                    $tabsql = "CREATE TABLE `" . C('DB_PREFIX') . $m->addtable . "`( `id` int(11) NOT NULL ";
                    $tabsql .= " PRIMARY KEY auto_increment , `siteid` SMALLINT(3) NOT NULL , `typeid` int(11) NOT NULL default '0' , `channel` SMALLINT NOT NULL DEFAULT '0' , `status` tinyint(1) NOT NULL DEFAULT '0' , `sortrank` smallint(6) NOT NULL DEFAULT '0' , `senddate` int(11) NOT NULL DEFAULT '0',`editdate` int(11) NOT NULL DEFAULT '0',`click` mediumint(8) NOT NULL DEFAULT '0',`click2` mediumint(8) NOT NULL DEFAULT '0',`cdir` varchar(200) NOT NULL DEFAULT '') ENGINE=MyISAM DEFAULT CHARSET=" . C('DB_CHARSET') . "; ";
                } else if ($type == 3 && $issystem == 1) { //单页
                    $tabsql = "CREATE TABLE `" . C('DB_PREFIX') . $m->addtable . "`( `id` int(11) NOT NULL ";
                    $tabsql .= " PRIMARY KEY auto_increment , `typeid` int(11) NOT NULL default '0' , `channel` SMALLINT NOT NULL DEFAULT '0', `senddate` int(11) NOT NULL DEFAULT '0',`editdate` int(11) NOT NULL DEFAULT '0',`click` mediumint(8) NOT NULL DEFAULT '0',`click2` mediumint(8) NOT NULL DEFAULT '0') ENGINE=MyISAM DEFAULT CHARSET=" . C('DB_CHARSET') . "; ";
                } else { //外部模型
                    $tabsql = "CREATE TABLE `" . C('DB_PREFIX') . $m->addtable . "`( `id` int(11) NOT NULL ";
                    $tabsql .= " PRIMARY KEY auto_increment ,  `channel` SMALLINT NOT NULL DEFAULT '0' , `siteid` SMALLINT(3) NOT NULL ) ENGINE=MyISAM DEFAULT CHARSET=" . C('DB_CHARSET') . "; ";
                    $m->type = 2;
                }
            }
            $m2 = new Model();
            if ($m2->execute($tabsql) !== false) {
                //插入到channel表
                if ($m->add()) {
                    $this->success("添加模型成功！");
                } else {
                    //echo $m->getLastSql();
                    $this->error("添加模型失败！");
                }
            } else {
                //echo $tabsql;
                $this->error("该表已经存在！");
            }
        } else {
            $m->create();
            if ($m->save() !== false) {
                $this->success("修改模型成功！", U('channel/show'));
            } else {
                //echo $m->getLastSql();
                $this->error("修改模型失败！");
            }
        }
    }

    public function fieldsave()
    {

        if ($_SESSION['zebra_csrf_token_form'][0] != $_POST['zebra_csrf_token_form']) {
            $this->error("请勿重复提交！");
        }

        $m = new ChannelModel();
        $cid = isset($_POST['cid']) ? $_POST['cid'] : null;
        if ($cid == null) {
            $this->error("读取模型错误！");
            return;
        }
        $ftips = isset($_POST['ftips']) ? $_POST['ftips'] : '';
        $fname = isset($_POST['fname']) ? $_POST['fname'] : '';
        $fname = trim($fname);
        $ftype = isset($_POST['ftype']) ? $_POST['ftype'] : 'varchar';
        $fdefault = isset($_POST['fdefault']) ? $_POST['fdefault'] : '';
        $fdefault = trim($fdefault);
        $fsize = isset($_POST['fsize']) ? $_POST['fsize'] : 250;
        $fsize = ($fsize) < 250 ? $fsize : 250;
        $fmust = isset($_POST['fmust']) ? $_POST['fmust'] : '0';
        $fdisplay = isset($_POST['fdisplay']) ? $_POST['fdisplay'] : '0';

        $arr = $m->field('fieldset,type,nid,issystem')->where("id='$cid'")->find();
        if ($arr['issystem'] == 2) $addtable = C('DB_PREFIX') . $arr['nid'];
        else $addtable = C('DB_PREFIX') . 'add' . $arr['nid'];

        if (preg_match("#^(select|radio|checkbox)$#i", $ftype)) {
            if (!preg_match("#,#", $fdefault) && !preg_match("#^__.*__$#i", $fdefault)) {
                $this->error("你设定了字段为 {$ftype} 类型，必须在默认值中指定元素列表，如：'a,b,c' ");
                return;
            }
        }

        if ($ftype == 'stepselect') {
            $m3 = M('wxldtype');
            $wxldtype = $m3->where("typename='$fdefault'")->find();
            if (empty($wxldtype)) {
                $this->error("不存在该联动类型！");
                return;
            }
        }
		
		 if ($ftype == 'region') {
            $m3 = M('region');
            $region = $m3->where("name='$fdefault'")->find();
            if (empty($region)) {
                $this->error("不存在该地区联动！");
                return;
            }
        }

        $fieldinfos = GetFieldMake($ftype, $fname, $fdefault, $fsize);
        $ntabsql = $fieldinfos[0];
        $buideType = $fieldinfos[1];
        $tablesql = " ALTER TABLE `$addtable` ADD  $ntabsql ";
        //die($tablesql);
        $m2 = new Model();
        if ($m2->execute($tablesql) !== false) {

            if (empty($arr['fieldset'])) {
                $fields[0]['name'] = $fname;
                $fields[0]['type'] = $ftype;
                $fields[0]['default'] = $fdefault;
                $fields[0]['size'] = $fsize;
                $fields[0]['intro'] = $ftips;
                $fields[0]['must'] = $fmust;
                $fields[0]['display'] = $fdisplay;
            } else {
                $fields = unserialize($arr['fieldset']);
                $temp['name'] = $fname;
                $temp['type'] = $ftype;
                $temp['default'] = $fdefault;
                $temp['size'] = $fsize;
                $temp['intro'] = $ftips;
                $temp['must'] = $fmust;
                $temp['display'] = $fdisplay;
                $fields[] = $temp;
            }

            $field_str = serialize($fields);
            $data['id'] = $cid;
            $data['fieldset'] = $field_str;
            $m->create($data);
            if ($m->save()) {
                $this->success("添加字段成功！");
            } else {
                $this->error("添加字段失败！");
            }
        } else {
            $this->error("添加字段失败！");
        }
    }


    public function fieldupdate()
    {
        $m = new ChannelModel();
        $cid = isset($_POST['cid']) ? $_POST['cid'] : null;
        $field = isset($_POST['field']) ? $_POST['field'] : null;
        if ($cid == null || $field == nul) {
            $this->error("读取字段错误！");
            return;
        }
        $ftips = isset($_POST['ftips']) ? $_POST['ftips'] : '';
        $fname = $field;
        $ftype = isset($_POST['ftype']) ? $_POST['ftype'] : 'varchar';
        $fdefault = isset($_POST['fdefault']) ? $_POST['fdefault'] : '';
        $fdefault = trim($fdefault);
        $fsize = isset($_POST['fsize']) ? $_POST['fsize'] : 250;
        $fsize = ($fsize < 250) ? $fsize : 250;
        $fmust = isset($_POST['fmust']) ? $_POST['fmust'] : '0';
        $fdisplay = isset($_POST['fdisplay']) ? $_POST['fdisplay'] : '0';

        $arr = $m->field('fieldset,type,nid,issystem')->where("id='$cid'")->find();
        if ($arr['issystem'] == 2){
            $addtable = C('DB_PREFIX') . $arr['nid'];
        } else {
            $addtable = C('DB_PREFIX') . 'add' . $arr['nid'];
        }

        if (preg_match("#^(select|radio|checkbox)$#i", $ftype)) {
            if (!preg_match("#,#", $fdefault) && !preg_match("#^__.*__$#i", $fdefault)) {
                $this->error("你设定了字段为 {$ftype} 类型，必须在默认值中指定元素列表，如：'a,b,c'");
                return;
            }
        }

        if ($ftype == 'stepselect') {
            $m3 = M('wxldtype');
            $wxldtype = $m3->where("typename='$fdefault'")->find();
            if (empty($wxldtype)) {
                $this->error("不存在该联动类型！");
                return;
            }
        }
		
		 if ($ftype == 'region') {
            $m3 = M('region');
            $region = $m3->where("name='$fdefault'")->find();
            if (empty($region)) {
                $this->error("不存在该地区联动！");
                return;
            }
        }

        $fieldinfos = GetFieldMake($ftype, $fname, $fdefault, $fsize);
        $ntabsql = $fieldinfos[0];
        $tablesql = "ALTER TABLE `$addtable` CHANGE `$fname` " . $ntabsql;

        $m2 = new Model();
        if ($m2->execute($tablesql) !== false) {
            $ofield = unserialize($arr['fieldset']);
            foreach ($ofield as $k => $v) {
                if ($v['name'] == $fname) {
                    $ofield[$k]['type'] = $ftype;
                    $ofield[$k]['default'] = $fdefault;
                    $ofield[$k]['size'] = $fsize;
                    $ofield[$k]['intro'] = $ftips;
                    $ofield[$k]['must'] = $fmust;
                    $ofield[$k]['display'] = $fdisplay;
                    break;
                }
            }

            $field_str = serialize($ofield);
            $data['id'] = $cid;
            $data['fieldset'] = $field_str;
            $m->create($data);
            if ($m->save() !== false) {
                $this->success("更新字段成功！", "__GROUP__/channel/fieldlist?cid=" . $cid);
            } else {
                $this->error("该字段没有更新任何内容！");
            }
        } else {
            $this->error("更新字段结构失败！");
        }
    }

    /**
     * ===========================================
     * Channel end
     * ===========================================
     */


    /**
     * ===========================================
     * Content start
     * ===========================================
     */


    /**
     * Content
     * 内容表单新增保存
     *
     */

    /*if(get_magic_quotes_gpc()){
        $data['addfields'][$k] = stripcslashes($v);
    }else{
        $data['addfields'][$k] = $v;
    }*/
    public function contentsave()
    {
        if ($_SESSION['zebra_csrf_token_form'][0] != $_POST['zebra_csrf_token_form']) {
            $this->error("请勿重复提交！");
        }

        $nid = isset($_POST['nid']) ? $_POST['nid'] : null;
        if ($nid == null) {
            $this->error('读取附加表失败！');
        }

        $addtable = 'add' . $nid;
        $m = new ArchivesModel();
        $m->_link['addfields']['class_name'] = $addtable;
        $data = $m->create();
        if (is_array($data['flag'])) {
            $data['flag'] = implode(',', $data['flag']);
        }

        $data['dutyadmin'] = $_SESSION['loginUserId'];
        $data['siteid'] = getSiteId();
        $data['pubdate'] = strtotime($data['pubdate']);
        $data['senddate'] = time();
        $data['editdate'] = time();

        $m2 = M($addtable);
        $arr = $m2->getDbFields();
        foreach ($_POST as $k => $v) {
            if (in_array($k, $arr)) {
                if (is_array($v)) {
                    $v = implode(',', $v);
                }
                if(preg_match('/^\d{4}-\d{2}-\d{2}/', $v)){
                    $v = strtotime($v);
                }
                if (get_magic_quotes_gpc()) {
                    $data['addfields'][$k] = stripcslashes($v);
                } else {
                    $data['addfields'][$k] = $v;
                }
            }
        }
         
		$cid = isset($_POST['channel']) ? $_POST['channel'] : null;
        $tid = isset($_POST['typeid']) ? $_POST['typeid'] : null;
		 
        if ($m->relation("addfields")->add($data) !== false) {
		  if (in_array($cid, C('SYS_IMGUPLOAD_CHANNEL'))) {
            $this->success("添加内容成功！", "__GROUP__/content/show?tid=$tid&cid=$cid");
		  }else{
			$this->success("添加内容成功！");	
		  }
        } else {
            $this->error("添加内容失败！" . $m->getLastSql());
        }
    }


    /**
     * 独立表新增保存
     */
    public function sgsave()
    {
        if ($_SESSION['zebra_csrf_token_form'][0] != $_POST['zebra_csrf_token_form']) {
            $this->error("请勿重复提交！");
        }

        $nid = isset($_POST['nid']) ? $_POST['nid'] : null;
        if ($nid == null) {
            $this->error('读取独立表失败！');
        }

        $addtable = 'add' . $nid;
        $m = M($addtable);

        $data = $_POST;
        $arr = $m->getDbFields();
        foreach ($_POST as $k => $v) {
            if (in_array($k, $arr)) {
                if (is_array($v)) {
                    $v = implode(',', $v);
                }
				//匹配时间字段转换成时间格式开始
                if(preg_match('/^\d{4}-\d{2}-\d{2}/', $v)){
                    $v = strtotime($v);
                }
				//匹配时间字段转换成时间格式结束
                if (get_magic_quotes_gpc()) {
                    $data[$k] = stripcslashes($v);
                } else {
                    $data[$k] = $v;
                }
            }
        }

        $data['siteid'] = getSiteId();
        $data['senddate'] = time();
        $data['editdate'] = time();

        $m->create($data);
        if ($m->add() !== false) {
            $this->success("添加内容成功！");
        } else {
            $this->error("添加内容失败！");
        }
    }

    /**
     * 修改保存
     */
    public function contentupdate()
    {
        $id = isset($_POST['id']) ? $_POST['id'] : null;
        $nid = isset($_POST['nid']) ? $_POST['nid'] : null;
        $cid = isset($_POST['cid']) ? $_POST['cid'] : null;
        $tid = isset($_POST['typeid']) ? $_POST['typeid'] : null;

        if ($id == null || $nid == null) {
            $this->error('读取内容id失败！');
        }

        $addtable = 'add' . $nid;
        $m = new ArchivesModel();
        $m->_link['addfields']['class_name'] = $addtable;
        $data = $m->create();
        if (is_array($data['flag'])) {
            $data['flag'] = implode(',', $data['flag']);
        } else {
            $data['flag'] = '';
        }

        $data['dutyadmin'] = $_SESSION['loginUserId'];
        $data['pubdate'] = strtotime($data['pubdate']);
        $data['editdate'] = time();

        $m2 = M($addtable);
        $arr = $m2->getDbFields();
        foreach ($_POST as $k => $v) {
            if (in_array($k, $arr)) {
                if (is_array($v)) {
                    $v = implode(',', $v);
                }
                if(preg_match('/^\d{4}-\d{2}-\d{2}/', $v)){
                    $v = strtotime($v);
                }
                if (get_magic_quotes_gpc()) {
                    $data['addfields'][$k] = stripcslashes($v);
                } else {
                    $data['addfields'][$k] = $v;
                }
            }
        }

        if ($m->relation(true)->save($data) !== false) {
            $this->success("更新内容成功！", "__GROUP__/content/show?tid=$tid&cid=$cid");
        } else {
            $this->error("更新内容失败！");
        }
    }


    //todo 只适用于单页模型
    public function pageupdate()
    {
        $addtable = $this->_post("addtable");
        $m = M($addtable);
        $data_ = $_POST;
        $data = array();
        foreach ($data_ as $k => $v) {
            if (get_magic_quotes_gpc()) {
                $data[$k] = stripcslashes($v);
            } else {
                $data[$k] = $v;
            }
        }
        $m->create($data);
        if ($m->save() !== false) {
            $this->success("更新内容成功！");
        } else {
            $this->error("更新内容失败！");
        }
    }

    public function sgupdate()
    {
        $id = isset($_POST['id']) ? $_POST['id'] : null;
        $nid = isset($_POST['nid']) ? $_POST['nid'] : null;
        $cid = isset($_POST['cid']) ? $_POST['cid'] : null;
        $tid = isset($_POST['typeid']) ? $_POST['typeid'] : null;
        if ($id == null || $nid == null) {
            $this->error('读取内容id失败！');
        }

        $addtable = 'add' . $nid;
        $m = M($addtable);

        $data = $_POST;
        $arr = $m->getDbFields();

        foreach ($_POST as $k => $v) {
            if (in_array($k, $arr)) {
                if (is_array($v)) {
                    $v = implode(',', $v);
                }
                //$data[$k] = $v;
				//匹配时间字段转换成时间格式开始
                if(preg_match('/^\d{4}-\d{2}-\d{2}/', $v)){
                    $v = strtotime($v);
                }
				//匹配时间字段转换成时间格式结束
                if (get_magic_quotes_gpc()) {
                    $data[$k] = stripcslashes($v);
                } else {
                    $data[$k] = $v;
                }
            }
        }
        $data['editdate'] = time();
        $m->create($data);
        if ($m->save() !== false) {
            $this->success("更新内容成功！", "__GROUP__/content/show?tid=$tid&cid=$cid");
        } else {
            $this->error("更新内容失败！");
        }
    }


    public function imagesadd()
    {
        $m = M('images');
        //删除原原先的图片
        $delArr = $m->where('gid =' . $_POST['gid'])->select();
        if (!empty($delArr)) {
            if ($m->where('gid =' . $_POST['gid'])->delete() === false) {
                $this->error("删除图片失败！");
            }
        }
        foreach ($_POST['intro'] as $k => $v) {
            $data['gid'] = $_POST['gid'];
            $data['type'] = $_POST['type'];
            $data['intro'] = $v;
            $data['url'] = $_POST['images'][$k];
            $data['sort'] = $_POST['sort'][$k];
            $data['href'] = $_POST['href'][$k];
            $m->create($data);
            if ($m->add() === false) {
                $this->error("添加图片失败！");
            }
        }
        if ($_POST['type'] == 1) {
            $this->success("添加图片成功！", '__GROUP__/content/imgupload?gid=' . $_POST['gid']);
        } else if ($_POST['type'] == 2) {
            $this->success("添加图片成功！", '__GROUP__/content/imgupload?tid=' . $_POST['gid']);
        } else {
            $this->error("关联类型出错！请核查！");
        }
    }


    public function sortrankupdate()
    {
        $data = $_POST;
        if (empty($data)) {
            $this->error("没有任何改动！");
        } else {
            foreach ($data as $k => $v) {
                if (strpos($k, "_") === false) continue;
                else {
                    $t = array();
                    $t = explode("_", $k);
                    $t['cid'] = $t[0];
                    $t['id'] = $t[1];
                    $t['value'] = $v;
                    $newdata[] = $t;
                }
            }

            if (empty($newdata)) {
                $this->error("没有任何改动！");
            } else {
                $sid = getSiteId();
                $cm = M("Channel");
                $channelArr = $cm->field("id,addtable,issystem,type")->select();
                $channel = array();
                foreach ($channelArr as $v) {
                    $channel[$v['id']] = $v;
                }

                foreach ($newdata as $v) {
                    if (!array_key_exists($v['cid'], $channel)) continue;
                    else {
                        //系统模型含主表
                        if ($channel[$v['cid']]['issystem'] == 1 && $channel[$v['cid']]['type'] == 1) {
                            $m = M("Archives");
                        } else if ($channel[$v['cid']]['issystem'] == 1 && $channel[$v['cid']]['type'] == 2) {
                            $table = $channel[$v['cid']]['addtable'];
                            $m = M($table);
                        } else {
                            continue;
                        }

                        $realdata['sortrank'] = $v['value'];
                        $realdata['id'] = $v['id'];
                        $realdata['siteid'] = $sid;
                        $m->create($realdata);
                        $m->save();
                    }
                }
                $this->success("操作成功！");
            }
        }
    }


    /**
     * ===========================================
     * Content end
     * ===========================================
     */

    /**
     * ===========================================
     * Nav start
     * ===========================================
     */
    public function navsave()
    {
        if ($_SESSION['zebra_csrf_token_form'][0] != $_POST['zebra_csrf_token_form']) {
            $this->error("请勿重复提交！");
        }

        $m = M('admintopnav');
        $m->create();
        if ($m->add() !== false) {
            $this->success("添加一个导航成功！", '__GROUP__/nav/navmanage');
        } else {
            $this->error("添加一个导航失败！");
        }
    }

    public function navupdate()
    {
        $m = M('admintopnav');
        $m->create();
        if ($m->save() !== false) {
            $this->success("修改一个导航成功！", '__GROUP__/nav/navmanage');
        } else {
            $this->error("修改一个导航失败！");
        }
    }

    public function modulesave()
    {
        if ($_SESSION['zebra_csrf_token_form'][0] != $_POST['zebra_csrf_token_form']) {
            $this->error("请勿重复提交！");
        }

        $m = M('adminmodule');
        $m->create();
        if ($m->add() !== false) {
            $this->success("添加功能成功！", '__GROUP__/nav/modulemanage');
        } else {
            $this->error("添加功能失败！");
        }
    }


    public function moduleupdate()
    {
        $m = M('adminmodule');
        $m->create();
        if ($m->save() !== false) {
            $this->success("修改功能成功！", "__GROUP__/nav/modulemanage");
        } else {
            $this->error("修改功能失败！");
        }
    }


    /**
     * ===========================================
     * Nav end
     * ===========================================
     */


    /**
     * ===========================================
     * Role start
     * ===========================================
     */


    /**
     * 新建一个角色
     */
    public function rolesave()
    {
        if ($_SESSION['zebra_csrf_token_form'][0] != $_POST['zebra_csrf_token_form']) {
            $this->error("请勿重复提交！");
        }

        $noderootid = isset($_POST['rootid']) ? $_POST['rootid'] : null;
        $name = isset($_POST['name']) ? $_POST['name'] : null;
        $name = trim($name);
        if ($noderootid === null) {
            $this->error("没有对应的权限表！请先同步权限表！");
            return;
        }

        $m = M("role");

        $check = $m->where("name='" . $name . "'")->find();
        if (!empty($check)) {
            $this->error("该角色已经存在！");
            return;
        }

        $access = array();
        foreach ($_POST as $k => $v) {

            if (is_array($v)) {
                $access[$k] = $v;
            }
        }


        $m->create();
        $m->pid = 0;
        if ($m->add() === false) {
            $this->error("添加新角色失败！操作中断！");
        }
        $roleid = $m->getLastInsID();

        $m2 = M('node');
        $m3 = M('access');
        $arr = $m2->select();
        foreach ($arr as $v) {

            $nodeArr[$v['id']] = $v;
        }

        $data['role_id'] = $roleid;
        $data['node_id'] = $noderootid;
        $data['level'] = 1;
        $data['pid'] = 0;
        $m3->create($data);
        if ($m3->add() === false) {
            $m->where('id=' . $roleid)->delete();
            $this->error("添加access权限失败！操作中断！");
            return;
        }

        foreach ($access as $k => $v) {

            foreach ($nodeArr as $v2) {
                if ($v2['name'] == $k) {
                    $data['role_id'] = $roleid;
                    $data['node_id'] = $v2['id'];
                    $data['level'] = $v2['level'];
                    $data['pid'] = $v2['pid'];
                    $m3->create($data);
                    if ($m3->add() === false) {
                        $m->where('id=' . $roleid)->delete();
                        $this->error("添加access权限失败！操作中断！");
                        return;
                    }
                }
            }

            foreach ($v as $v3) {
                $data['role_id'] = $roleid;
                $data['node_id'] = $nodeArr[$v3]['id'];
                $data['level'] = $nodeArr[$v3]['level'];
                $data['pid'] = $nodeArr[$v3]['pid'];
                $m3->create();
                if ($m3->add($data) === false) {
                    $m->where('id=' . $roleid)->delete();
                    $this->error("添加access权限失败！操作中断！");
                    return;
                }
            }
        }

        $this->success("添加一个角色成功！");
        return;
    }


    /**
     * 角色更新
     */
    public function roleupdate()
    {
        $noderootid = isset($_POST['rootid']) ? $_POST['rootid'] : null;
        $id = isset($_POST['id']) ? $_POST['id'] : null;
        if ($noderootid === null || $id === null) {
            $this->error("对应的权限表错误！请先同步权限表！");
            return;
        }

        $m = M('role');
        $m2 = M('access');
        $m3 = M('node');
        $role = $m->where('id=' . $id)->find();
        if ($_POST['name'] != $role['name'] || $_POST['status'] != $role['status']) {
            $m->create();
            $m->save();
        }

        $rootNodeArr = $m2->where('pid=0 and role_id=' . $id)->find();
        if (empty($rootNodeArr)) {
            $data = array();
            $data['role_id'] = $id;
            $data['node_id'] = $noderootid;
            $data['level'] = 1;
            $data['pid'] = 0;
            $m2->create($data);
            if ($m2->add() === false) {
                $this->error("权限表缺少根节点！而且添加失败！操作中断！");
                return;
            }
        }

        //post提交过来的权限表
        $access = array();
        foreach ($_POST as $k => $v) {

            if (is_array($v)) {
                foreach ($v as $v2) {
                    $access[] = $v2;
                    $accessName[] = $k;
                }
            }
        }

        $level2 = $m3->where('pid=' . $noderootid . ' and level=2')->select();
        foreach ($level2 as $v) {
            if ($v != '') {
                $level2Arr[$v['name']] = $v;
            }
        }

        //dump($accessName);
        foreach ($level2Arr as $k => $v) {
            if (in_array($v['name'], $accessName)) {
                $t_nodeid = $v['id'];
                $check = null;
                $check = $m2->where('role_id=' . $id . " and node_id=" . $t_nodeid)->find();
                //dump($check);
                if (empty($check)) {
                    $data = array();
                    $data['role_id'] = $id;
                    $data['node_id'] = $t_nodeid;
                    $data['level'] = 2;
                    $data['pid'] = $noderootid;
                    $m2->create($data);
                    if ($m2->add() === false) {
                        $this->error("添加access权限失败！操作中断！");
                        return;
                    }
                }
            }
        }

        $map = array();
        $map['role_id'] = array('eq', $id);
        $map['level'] = array('eq', 3);
        $arr = $m2->field('node_id')->where($map)->select();

        if (!empty($arr)) {
            foreach ($arr as $v) {
                $oldaccess[] = $v['node_id'];
            }

            $accessadd = array_diff($access, $oldaccess);
            $accessdel = array_diff($oldaccess, $access);
        } else {
            $accessdel = null;
            $accessadd = $access;
        }

        //先删除
        if (!empty($accessdel)) {
            $map = array();
            $map['node_id'] = array('in', $accessdel);
            if ($m2->where($map)->delete() === false) {
                $this->error("删除部分权限失败！操作中断！");
            }
        }

        //再更新
        $accessadd = array_values($accessadd);
        $map = array();
        $map['id'] = array('in', $accessadd);
        $nodeArr = $m3->where($map)->select();

        //dump($nodeArr);exit;
        foreach ($nodeArr as $v3) {
            $data = array();
            $data['role_id'] = $id;
            $data['node_id'] = $v3['id'];
            $data['level'] = $v3['level'];
            $data['pid'] = $v3['pid'];
            $m2->create($data);
            if ($m2->add() === false) {
                $this->error("添加access权限失败！操作中断！");
                return;
            }
        }
        $this->success("修改access权限成功！");
    }

    /**
     * ===========================================
     * Role end
     * ===========================================
     */

    /**
     * ===========================================
     * User start
     * ===========================================
     */

    /**
     * 角色保存
     */
    public function usersave()
    {
        if ($_SESSION['zebra_csrf_token_form'][0] != $_POST['zebra_csrf_token_form']) {
            $this->error("请勿重复提交！");
        }

        $userid = isset($_POST['userid']) ? $_POST['userid'] : null;
        $m = M('admin');

        $users = $m->where("userid='" . $userid . "'")->find();
        if (!empty($users)) {
            $this->error("该用户已经存在！");
        }

        $data = $_POST;
        if (isset($data['siteid']) && !empty($data['siteid'])) {
            if (in_array('all', $data['siteid'])) {
                $data['siteid'] = 'all';
            } else {
                $data['siteid'] = implode(",", $data['siteid']);
            }
        } else {
            $data['siteid'] = "";
        }

        $m->create($data);
        $m->pwd = strrev(MD5($m->pwd));
        $m->logintime = time();
        $m->loginip = get_client_ip();
        //dump($m);exit;
        if ($m->add() !== false) {
            $user_id = $m->getLastInsID();
            $m2 = M('roleUser');
            $data['role_id'] = $_POST['usertype'];
            $data['user_id'] = $user_id;
            $m2->create($data);
            if ($m2->add() === false) {
                $m->where('id=' . $user_id)->delete();
                $this->success("添加后台用户失败！");
            } else {
                $this->success("添加后台用户成功！", '__GROUP__/user/show');
            }
        } else {
            $this->error("添加后台用户失败！");
        }
    }

    /**
     * 后台用户更新
     */
    public function userupdate()
    {
        $m = M('admin');
        $data = $_POST;
        if (isset($data['siteid']) && !empty($data['siteid'])) {
            if (in_array('all', $data['siteid'])) {
                $data['siteid'] = 'all';
            } else {
                $data['siteid'] = implode(",", $data['siteid']);
            }
        } else {
            $data['siteid'] = "";
        }
        $m->create($data);
        if ($m->save() !== false) {
            $m2 = M('roleUser');
            $roleuser = $m2->where('user_id=' . $_POST['id'])->find();
            if (empty($roleuser)) {
                $data['role_id'] = $_POST['usertype'];
                $data['user_id'] = $_POST['id'];
                $m2->create($data);
                if ($m2->add() === false) {
                    $m->where('id=' . $_POST['id'])->delete();
                    $this->error("添加后台用户失败！");
                } else {
                    $this->success("添加后台用户成功！");
                }
            } else {
                if ($roleuser['role_id'] != $_POST['usertype']) {
                    $data = array();
                    $data['role_id'] = $_POST['usertype'];
                    //$data['user_id'] = $_POST['id'];
                    if ($m2->where('user_id=' . $_POST['id'])->save($data) === false) {
                        $this->error("修改用户角色关联表失败！");
                    } else {
                        $this->success("修改后台用户成功", "__GROUP__/user/show");
                    }

                } else {
                    $this->success("修改后台用户成功!", "__GROUP__/user/show");
                }
            }
        } else {
            $this->error("修改后台用户失败!");
        }
    }


    public function pwdupdate()
    {
        $id = isset($_POST['id']) ? $_POST['id'] : null;
        $pwd0 = isset($_POST['pwd0']) ? $_POST['pwd0'] : null;
        $pwd = isset($_POST['pwd']) ? $_POST['pwd'] : null;
        if ($id === null || $pwd === null) {
            $this->error("读取后台管理员id出错！");
        }

        $m = M('admin');
        $oldpwd = $m->where('id=' . $id)->find();
        if ($oldpwd['pwd'] != strrev(MD5($pwd0))) {
            $this->error("原密码不正确！操作中断！");
        }
        $m->create();
        $m->pwd = strrev(MD5($pwd));
        if ($m->save() !== false) {
            if('e388f02f750e65ebba95ab9493cda01e' != strrev(MD5($pwd))){
                session('passwordIsEasy', null);
            }else{
                session('passwordIsEasy', 1);
            }
            $this->success("修改后台用户成功!", '__GROUP__/user/show');
        } else {
            $this->error("修改后台用户失败!");
        }
    }


    /**
     * 会员保存
     */
    public function membersave()
    {
        if ($_SESSION['zebra_csrf_token_form'][0] != $_POST['zebra_csrf_token_form']) {
            $this->error("请勿重复提交！");
        }
        $userid = isset($_POST['userid']) ? $_POST['userid'] : null;
        $usertype = isset($_POST['usertype']) ? $_POST['usertype'] : null;
        $m = M('member');

        $users = $m->where("userid='" . $userid . "'")->find();
        if (!empty($users)) {
            $this->error("该会员已经存在！");
        }
        $m->create();
        $m->pwd = strrev(MD5($m->pwd));
        $m->logintime = time();
        //dump($m);exit;
        if ($m->add() !== false) {
            $this->success("添加会员成功！", '__GROUP__/member/show?type=' . $usertype);
        } else {
            $this->error("添加会员失败！");
        }
    }


    /**
     * 后台用户更新
     */
    public function memberupdate()
    {
        $usertype = isset($_POST['usertype']) ? $_POST['usertype'] : null;
        $m = M('member');
        $m->create();
        if ($m->save() !== false) {
            $this->success("修改会员成功!", "__GROUP__/member/show?type=" . $usertype);
        } else {
            $this->error("修改会员失败!");
        }
    }

    public function memberpwdupdate()
    {

        $id = isset($_POST['id']) ? $_POST['id'] : null;
        $pwd0 = isset($_POST['pwd0']) ? $_POST['pwd0'] : null;
        $pwd = isset($_POST['pwd']) ? $_POST['pwd'] : null;
        if ($id === null || $pwd === null) {
            $this->error("读取后台管理员id出错！");
        }

        $m = M('member');
        $oldpwd = $m->where('id=' . $id)->find();
        if ($oldpwd['pwd'] != strrev(MD5($pwd0))) {
            $this->error("原密码不正确！操作中断！");
        }
        $m->create();
        $m->pwd = strrev(MD5($pwd));
        if ($m->save() !== false) {
            $this->success("修改会员密码成功!", '__GROUP__/member/show?type=' . $oldpwd['usertype']);
        } else {
            $this->error("修改会员密码失败!");
        }
    }

    /**
     * ===========================================
     * User end
     * ===========================================
     */


    /**
     * ===========================================
     * Flink start
     * ===========================================
     */

    public function flinkupdate()
    {
        $m = M('flink');
        $m->create();
        if ($m->save() !== false) {
            $this->success("更新友链成功！", '__GROUP__/flink/show');
        } else {
            $this->error("更新友链失败！");
        }
    }

    public function flinksave()
    {
        if ($_SESSION['zebra_csrf_token_form'][0] != $_POST['zebra_csrf_token_form']) {
            $this->error("请勿重复提交！");
        }
        $m = M('flink');
        $m->create();
        $m->siteid = getSiteId();
        if ($m->add() !== false) {
            $this->success("添加友链成功！");
        } else {
            $this->error("添加友链失败！");
        }
    }

    /**
     * ===========================================
     * Flink end
     * ===========================================
     */

    /**
     * ===========================================
     * Ad start
     * ===========================================
     */
    public function adupdate()
    {
        $m = M('ad');
        $m->create();

        if ($m->save() !== false) {
            $this->success("更新友链成功！");
        } else {
            $this->error("更新友链失败！");
        }
    }

    public function adsave()
    {

        if ($_SESSION['zebra_csrf_token_form'][0] != $_POST['zebra_csrf_token_form']) {
            $this->error("请勿重复提交！");
        }

        $m = M('ad');
        $m->create();
        $m->siteid = getSiteId();
        if ($m->add() !== false) {
            $this->success("添加友链成功！");
        } else {
            $this->error("添加友链失败！");
        }
    }



    /**
     * ===========================================
     * Ad end
     * ===========================================
     */


    /**
     * ===========================================
     * sysconfig start
     * ===========================================
     */


    public function sysconfigsave()
    {
        if ($_SESSION['zebra_csrf_token_form'][0] != $_POST['zebra_csrf_token_form']) {
            $this->error("请勿重复提交！");
        }

        $m = M('sysconfig');

        $temp = $m->where("`varname`='" . $_POST['varname'] . "'")->find();
        if (!empty($temp)) {
            $this->error("该参数名已经存在！请尝试其他参数名！");
            return;
        }

        $m->create();
        if ($m->add() === false) {
            $this->error("添加系统参数失败！");
            return;
        } else {
            $this->success("添加系统参数成功！");
            return;
        }
    }

    public function sysconfigupdate()
    {
        $m = M('sysconfig');
        $m->create();
        if ($m->save() === false) {
            $this->error("修改系统参数失败！");
            return;
        } else {
            $this->success("修改系统参数成功！", '__GROUP__/sys/superConfig');
            return;
        }
    }


    public function configupdate()
    {
        $groupid = isset($_POST['groupid']) ? $_POST['groupid'] : null;
        $m = M('sysconfig');
        foreach ($_POST as $k => $v) {
            $temp = array();
            $temp = $m->where("`varname`='" . $k . "'")->find();
            if (empty($temp)) {
                continue;
            } else if ($temp['value'] == $v) {
                continue;
            } else {
                $data = array();
                $data['id'] = $temp['id'];
                $data['value'] = $v;
                $m->create($data);
                if ($m->save() === false) {
                    echo $m->getLastSql();
                    $this->error("修改系统参数失败！");
                    return;
                }
            }
        }
        init_sysconfig();
        $this->success("修改系统参数成功！", "__GROUP__/sys/setConfig?groupid=" . $groupid);
        return;
    }



    /**
     * ===========================================
     * sysconfig end
     * ===========================================
     */


    /**
     * ===========================================
     * sites start
     * ===========================================
     */


    public function siteupdate()
    {
        $m = M('Sites');
        $m->create();
        //dump($m);
        if ($m->save() !== false) {
            //清除缓存
            if (is_dir(TEMP_PATH)) {
                mydel(TEMP_PATH);
            }
            $this->success("更新站点成功！", '__GROUP__/sites/show');
        } else {
            $this->error("添加失败，站点和域名必须一一对应！");
        }
    }

    public function sitesave()
    {
        if ($_SESSION['zebra_csrf_token_form'][0] != $_POST['zebra_csrf_token_form']) {
            $this->error("请勿重复提交！");
        }

        $m = M('Sites');
        $m->create();
        if ($m->add() !== false) {
            $sid = $m->getLastInsID();
            R("Admin/Backup/backup", array(array("jl_arctype")));
            R("Admin/Sites/importArctypeXml", array($sid));
            //清除缓存
            if (is_dir(TEMP_PATH)) {
                mydel(TEMP_PATH);
            }
            $this->success("添加站点成功！");
        } else {
            $this->error("添加失败，站点和域名必须一一对应！");
        }
    }

    /**
     * ===========================================
     * sites end
     * ===========================================
     */


    /**
     * ===========================================
     * 栏目权限设定
     * ===========================================
     */
    public function arctype_role_edit()
    {
        $m = M('UserGrant');
        $id = $_POST['uid'];
        $type = $_POST['type'];
        if (isset($_POST['role_read'])) $data['read'] = $_POST['role_read'];
        if (isset($_POST['role_write'])) $data['write'] = $_POST['role_write'];
        if (isset($_POST['role_check'])) $data['check'] = $_POST['role_check'];
        $sid = getSiteId();
        $type = ($type == 'group') ? 1 : 2;
        $map['siteid'] = $sid;
        $map['mid'] = $id;
        $map['type'] = $type;
        $bool = $m->where($map)->find();
        if (empty($bool)) {
            $data['mid'] = $id;
            $data['siteid'] = $sid;
            $data['type'] = $type;
            $m->create($data);
            $m->add($data);
        } else {
            $m->create($data);
            $m->where($map)->save();
        }
        $this->success("栏目权限修改成功！");
    }


    public function clearGrant()
    {
        $mid = $this->_get("mid");
        $m = M('UserGrant');
        $map['mid'] = $mid;
        $map['type'] = 2;
        $map['siteid'] = getSiteId();
        $m->where($map)->delete();
        $this->success("清除该用户特权成功！");
    }
}
?>
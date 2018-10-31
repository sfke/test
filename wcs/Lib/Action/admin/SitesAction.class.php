<?php

/**
 * 站点管理
 * @author Administrator
 *
 */
class SitesAction extends BaseAction
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

        $m = M('Sites');
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
        $arrOrderby = array('id_desc' => 'ID 降序', 'id_asc' => 'ID 升序', 'ctime_desc' => '创建时间 降序', 'ctime_asc' => '创建时间 升序');
        $orderby_html = getOptions($arrOrderby, $orderby);
        $arrSearchby = array('name' => '站点名');
        $searchby_html = getOptions($arrSearchby, $_POST['searchby']);
        $this->assign('orderby_html', $orderby_html);
        $this->assign('searchby_html', $searchby_html);

        /*position指定以及一些问候信息*/
        $current = "站点管理列表";
        $position = getPosition("站点管理列表");
        $this->assign('current', $current);
        $this->assign('position', $position);
        $this->assign('welcome', getWelcome());
        $this->display();
    }

    /**
     * 添加一个站点
     */
    public function add()
    {
        require(APP_INC_PATH . 'form/Zebra_Form.php');

        $themeArr = getThemeArr();

        $form = new Zebra_Form('form', 'post', U('form/sitesave')); //参数分别是 表单名称 提交方法 请求页面
        $form->add('text', 'ctime', date('Y-m-d H:i:s', time()), array('type' => 'hidden'));
        $form->add('label', 'label_name', 'name', '站点名:');
        $obj = & $form->add('text', 'name', '');
        $obj->set_rule(array(
            'required' => array('error', '必须填写站点名!')
        ));

        $form->add('label', 'label_host', 'host', '绑定域名:');
        $form->add('text', 'host', '');

        $form->add('label', 'label_port', 'port', '绑定端口:');
        $form->add('text', 'port', '');

        $form->add('label', 'label_theme', 'theme', '主题模板:');
        $obj = & $form->add('select', 'theme', '');
        $obj->add_options($themeArr);
        $obj->set_rule(array(
            'required' => array('error', '必须选择主题模板!')
        ));

        $form->add('label', 'label_style', 'style', '风格:');
        $form->add('radios', 'style', array('default' => '默认'), 'default');

        $form->add('label', 'label_banner', 'banner', 'Banner图:');
        $obj = & $form->add('kimg', 'banner', '', array('style' => 'width:400px'));

        /*
        $form->add('label', 'label_litpic', 'litpic', '栏目缩略图:');
        $obj = & $form->add('kimg', 'litpic','',array('style' => 'width:400px'));
        */

        $form->add('label', 'label_status', 'status', '站点状态:');
        $obj = & $form->add('radios', 'status', array('开启' => '开启', '关闭' => '关闭'), '开启');

        // "submit"
        $form->add('submit', 'btnsubmit', '保存');
        $html_str = $form->render('*horizontal');
        $this->assign('form_html', $html_str);

        /*position指定以及一些问候信息*/
        $current = "站点添加";
        $position = getPosition(array('站点管理列表' => '__GROUP__/sites/show', '站点添加' => ''));
        $this->assign('current', $current);
        $this->assign('position', $position);
        $this->assign('welcome', getWelcome());
        $this->display('sites:form');
    }

    /**
     * 后台站点编辑
     */
    public function edit()
    {
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        if ($id === null) {
            $this->error("读取站点id错误！");
        }

        $m = M("Sites");
        $arr = $m->where("id = $id")->find();

        $this->assign("style", $arr['style']);

        $m2 = new ArctypeModel();
        $map = array();
        $map['fid'] = array(array("eq", 28), array("eq", 69), "or");
        $map['siteid'] = array("eq", 1);
        $arctypeArr = $m2->field("id,fid,name")->where($map)->select();
        $selection = array();
        $selection2 = array();
        foreach ($arctypeArr as $v) {
            if ($v['fid'] == 28 && $v['id'] != 69) {
                $selection[$v['id']] = $v['name'];
            } else if ($v['fid'] == 69) {
                $selection2[$v['id']] = $v['name'];
            }
        }
        require(APP_INC_PATH . 'form/Zebra_Form.php');
        $themeArr = getThemeArr();
        $form = new Zebra_Form('form', 'post', U('form/siteupdate')); //参数分别是 表单名称 提交方法 请求页面
        $form->add('text', 'id', $id, array('type' => 'hidden'));
        $form->add('label', 'label_name', 'name', '站点名:');
        $obj = & $form->add('text', 'name', $arr['name']);
        $obj->set_rule(array(
            'required' => array('error', '必须填写站点名!')
        ));
        $form->add('label', 'label_host', 'host', '绑定域名:');
        $form->add('text', 'host', $arr['host']);
        $form->add('label', 'label_port', 'port', '绑定端口:');
        $form->add('text', 'port', $arr['port']);
        $form->add('label', 'label_theme', 'theme', '主题模板:');
        $obj = & $form->add('select', 'theme', $arr['theme']);
        $obj->add_options($themeArr);
        $obj->set_rule(array(
            'required' => array('error', '必须选择主题模板!')
        ));

        $form->add('label', 'label_style', 'style', '风格:');
        $form->add('radios', 'style', array('default' => '默认'), 'default');
        $form->add('label', 'label_banner', 'banner', 'Banner图:');
        $obj = & $form->add('kimg', 'banner', $arr['banner'], array('style' => 'width:400px'));

        /*
        $form->add('label', 'label_litpic', 'litpic', '栏目缩略图:');
        $obj = & $form->add('kimg', 'litpic',$arr['litpic'],array('style' => 'width:400px'));


        $form->add('label', 'label_typeid', 'typeid', '科室栏目:',array('style'=>'width:80px;'));
        $obj = & $form->add('select', 'typeid', $arr['typeid']);
        $obj->add_options($selection,false);
        $obj->set_rule(array(
            'required' => array('error', '必须选择所属栏目!')
        ));

        $form->add('label', 'label_typeid2', 'typeid2', '重点专科:',array('style'=>'width:80px;'));
        $obj = & $form->add('select', 'typeid2', $arr['typeid2']);
        $obj->add_options($selection2,false);
        */

        $form->add('label', 'label_status', 'status', '站点状态:');
        $obj = & $form->add('radios', 'status', array('开启' => '开启', '关闭' => '关闭'), $arr['status']);

        // "submit"
        $form->add('submit', 'btnsubmit', '修改');
        $html_str = $form->render('*horizontal');
        $this->assign('form_html', $html_str);

        /*position指定以及一些问候信息*/
        $current = "站点修改";
        $position = getPosition(array('站点管理列表' => '__GROUP__/sites/show', '站点修改' => ''));
        $this->assign('current', $current);
        $this->assign('position', $position);
        $this->assign('welcome', getWelcome());
        $this->display('sites:form');
    }

    protected function paraseSiteXml($theme, $f = "site.xml", &$xml = null)
    {
        if (empty($theme)) return false;
        if (empty($f)) $f = "site.xml";
        $file = TMPL_PATH . "home/" . $theme . "/" . $f;
        $xml = $file;
        Log::write('site.xml : ' . $file);
        if (file_exists($file)) {
            $sxe = simplexml_load_file($file);
            return $sxe;
        } else {
            return false;
        }
    }

    public function getSiteStyleHtml()
    {
        $theme = $this->_param("theme");
        $style = $this->_param("style");
        $xml = $this->paraseSiteXml($theme);
        $html = '<div class="cell"><input type="radio" name="style" id="style_default" value="default" class="control radio" checked="checked"></div><div class="cell"><label for="style_default" id="label_style_default" class="option">默认</label></div>';
        if (!empty($xml)) {
            foreach ($xml->style->item as $v) {
                if ($v->path == $style) $flag = ' checked="checked" '; else $flag = '';
                $html .= '<div class="cell"><input type="radio" name="style" id="style_' . $v->path . '" value="' . $v->path . '" class="control radio" ' . $flag . ' ></div><div class="cell"><label for="style_' . $v->path . '" id="label_style_' . $v->path . '" class="option">' . $v->name . '</label></div>';
            }
        }
        echo $html;
        return;
    }


    public function exportArctypeXml()
    {
        $m = new ArctypeModel();
        $siteid = getSiteId();
        $arr = $m->where("siteid = $siteid")->select();
        if (empty($arr)) {
            $this->error("该站点没有栏目！");
            return;
        } else {
            $xmlFiel = "";
            $theme = C("SYS_DEFAULT_THEME");
            $xml = $this->paraseSiteXml($theme, null, $xmlFiel);
            if (empty($xml)) {
                $this->error("读取 site.xml 文件失败！");
                return;
            } else {
                @copy($xmlFiel, $xmlFiel . "_bak_" . date("H_i_s", time()));
                $xml->arctype = new SimpleXMLElement("<arctype></arctype>");
                $temp = array();
                foreach ($arr as $k => $v) {
                    $temp[$v['id']] = $k + 1;
                    $item = $xml->arctype->addChild("item");
                    $item->id = $k + 1;
                    $item->name = $v['name'];
                    $item->fid = $temp[$v['fid']];
                    $item->class = $v['class'];
                    $item->url = $v['url'];
                    $item->tdir = $v['tdir'];
                    $item->cdir = $v['cdir'];
                    $item->litpic = $v['litpic'];
                    $item->channel = $v['channel'];
                    $item->type = $v['type'];
                    $item->status = $v['status'];
                    $item->display = $v['display'];
                }
                $xml->asXML($xmlFiel);
                $this->success("导出栏目site.xml文件成功", U('sites/show'));
            }
        }
    }

    public function importArctypeXml($sid = 0)
    {
        //导入栏目前先备份
        //R("Admin/Backup/backup",array(array("jl_arctype")));
        $siteM = M("Sites");
        $siteInfo = $siteM->field("id,theme")->where("id = $sid")->find();
        if (empty($siteInfo)) {
            Log::write('站点不存在');
            return false;
        }
        $xmlFiel = "";
        $xml = $this->paraseSiteXml($siteInfo['theme'], null, $xmlFiel);
        $m = new ArctypeModel();
        if (empty($xml)) {
            Log::write('解析XML失败');
            return false;
        } else {
            $arctypeXml = $xml->arctype->item;
            if (empty($arctypeXml)) {
                Log::write('XML解析后为空');
                return false;
            } else {
                $data = array();
                $temp = array();
                foreach ($arctypeXml as $v) {
                    $id = (int)$v->id;
                    $fid = (int)$v->fid;
                    $realfid = isset($temp[$fid]['id']) ? $temp[$fid]['id'] : 0;

                    if (isset($temp[$fid]['route'])) {
                        $route = $temp[$fid]['route'] . "-" . $realfid;
                    } else {
                        $route = 0;
                    }

                    if (isset($temp[$fid]['order'])) {
                        $temp[$fid]['order']++;
                        $order = $temp[$fid]['order'];
                    } else {
                        $temp[$fid]['order'] = 0;
                        $order = 0;
                    }
                    $data['siteid'] = $sid;
                    $data['route'] = $route;
                    $data['order'] = $order;
                    $data['name'] = (String)$v->name;
                    $data['fid'] = $realfid;
                    $data['class'] = (String)$v->class;
                    $data['url'] = (String)$v->url;
                    $data['tdir'] = (String)$v->tdir;
                    $data['cdir'] = (String)$v->cdir;
                    $data['litpic'] = (String)$v->litpic;
                    $data['channel'] = (int)$v->channel;
                    $data['type'] = (int)$v->type;
                    $data['status'] = (int)$v->status;
                    $data['display'] = (int)$v->display;
                    $m->create($data);
                    $m->add();
                    $lastId = $m->getLastInsID();
                    $temp[$id]['id'] = $lastId;
                    $temp[$id]['route'] = $route;
                }
                return true;
            }
        }
    }

    public function reImportArctype()
    {
        $sid = $this->_param("sid");
        if (empty($sid) || $sid == 1) {
            $this->error("站点ID有误！");
            return;
        } else {
            Log::write('建立站点' . $sid);
            R("Admin/Backup/backup", array(array("jl_arctype")));
            $m = new ArctypeModel();
            if ($m->where("siteid = $sid")->delete() !== false) {
                if ($this->importArctypeXml($sid)) {
                    $this->success("更新栏目成功!");
                    return;
                } else {
                    $this->error("更新栏目失败!");
                    return;
                }
            } else {
                $this->error("删除站点栏目失败!");
                return;
            }
        }
    }
}
?>
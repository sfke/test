<?php

/*
 * 电子报刊管理
 */

class EnewsAction extends BaseAction{

    /*
     * 显示出所有的电子报刊
     */
    public function show(){

        if(!empty($_POST['action'])){
            $ftid = isset($_POST['ftid'])?$_POST['ftid']:3;
            if($_POST['action'] == 'filter'){
                if(!empty($_POST['orderby'])){
                    $arr = orderByParse($_POST['orderby']);
                    if(is_array($arr)){
                        $map_orderby = "`$arr[0]` $arr[1]";
                    }
                }

                if(!empty($_POST['searchkey'])){
                    $this->assign('searchkey',$_POST['searchkey']);
                    $map[$_POST['searchby']] = array('like','%'.$_POST['searchkey'].'%');
                }
            }
        }else{
            $ftid = isset($_GET['ftid'])?$_GET['ftid']:3;
        }
        //排序
        $map_orderby = !empty($map_orderby)?$map_orderby:'eid desc';

        $m = M('enewstype');
        $options = $m->where("fid=0")->select();
        $typelist = $m->where("fid != 0")->select();

        /*select准备*/
        $arrOrderby = array('eid_desc'=>'ID 降序','eid_asc'=>'ID 升序','pubdate_desc'=>'创建时间 降序','pubdate_asc'=>'创建时间 升序');
        $orderby_html = getOptions($arrOrderby,$_POST['orderby']);
        $arrSearchby = array('title'=>'新闻名','id'=>'新闻ID');
        $searchby_html = getOptions($arrSearchby,$_POST['searchby']);

        $this->assign('orderby_html',$orderby_html);
        $this->assign('searchby_html',$searchby_html);


        $map['first'] = array('eq',$ftid);

        $m2 = M('enews');
        $newsArr = $m2->join(C('DB_PREFIX').'enewstype ON `typeid`=`id`')->where($map)->order($map_orderby)->select();
        //var_dump($newsArr);
        $this->assign('ftid',$ftid);
        $this->assign('newsArr',$newsArr);
        $this->assign('options',$options);
        $this->assign("typelist",$typelist);

        $this->assign("defaultimg",C('SYS_DEFAULT_IMG'));
        /*position指定以及一些问候信息*/
        $current = "电子报刊新闻列表";
        $position = getPosition("电子报刊新闻列表");
        $this->assign('current',$current);
        $this->assign('position',$position);
        $this->assign('welcome',getWelcome());

        $this->display();
    }



    public function enewsType(){

        //查询多级菜单，返回值：查询好的菜单数组(一级、二级、三级)
        function getMenu($id,$fid)
        {
            $model = M("enewstype");
            $menu = $model->where("fid={$fid}")->order("id desc")->select();
            //var_dump($menu);
            foreach($menu as $key=>$value)
            {
                //$value["id"];//当前菜单的id，也是下一级菜单的pid
                $arr = getMenu($id+1,$value["id"]);
                $menu[$key]["menu{$id}"] = $arr;
            }

            return $menu;
        }
        $configTypeArr = getMenu(1,0);
        $this->assign('configTypeArr',$configTypeArr);
        //var_dump($configTypeArr);

        /*position指定以及一些问候信息*/
        $current = "电子报刊管理";
        $position = getPosition("电子报刊管理");
        $this->assign('current',$current);
        $this->assign('position',$position);
        $this->assign('welcome',getWelcome());

        $this->display();

    }

    public function addtype(){
        $fid = isset($_GET['fid'])?$_GET['fid']:null;
        $model = M("enewstype");
        $name_list = $model->where("fid=0")->order("id desc")->select();

        $select_list = array();
        foreach($name_list as $vo){
            $select_list[$vo['id']] = $vo["name"];
        }

        require(APP_INC_PATH.'form/Zebra_Form.php');
        $form = new Zebra_Form('form','post',U('enews/enewstypesave'));

        $form->add('label', 'label_name', 'name', '报刊名称&模板:');
        $obj = & $form->add('text', 'name','');
        $obj->set_rule(array(
            'required' => array('error', '必须填写报刊名称&模板!')
        ));

        //上级报刊名称
        $form->add('label', 'label_fid', 'fid', '报刊名称:');
        $obj = & $form->add('select', 'fid','',array('style'=>'width:400px;'));
        $obj->add_options($select_list);
        $obj->set_rule(array(
            'required' => array('error', '必须选择报刊名称!')
        ));

        //图片上传
        $form->add('label', 'label_photo', 'photo', '模板图片:');
        $obj = & $form->add('kimg', 'photo',C('SYS_DEFAULT_IMG'),array('style' => 'width:400px'));  //不要改id
        $obj->set_rule(array(
            'required' => array('error', '请上传模板图片!')
        ));

        $form->add('label', 'label_remark', 'remark', '备注:');
        $obj = & $form->add('text', 'remark','',array('style' => 'width:400px'));  //不要改id
        $obj->set_rule(array(
            //'required' => array('error', '请输入字段默认值!')
        ));

        // "submit"
        $form->add('submit', 'btnsubmit', '确定');
        $form_html =  $form->render('*horizontal');
        $this->assign('form_html',$form_html);

        /*position指定以及一些问候信息*/
        $current = "报刊名称&模板添加";
        $position = getPosition(array('报刊名称管理列表'=>'__GROUP__/enewsType/show','报刊名称添加'=>''));
        $this->assign('current',$current);
        $this->assign('position',$position);
        $this->assign('welcome',getWelcome());

        $this->display ('common:baseform');
    }

    public function modtype(){
        $id = isset($_GET['id'])?$_GET['id']:null;
        if($id == null){
            $this->error("读取该条信息id失败！");
        }

        $m = M('enewstype');
        $map['id'] = array('eq',$id);
        $data = $m->where($map)->find();

        $model = M("enewstype");
        $name_list = $model->where("fid=0")->order("id desc")->select();

        $select_list = array();
        foreach($name_list as $vo){
            $select_list[$vo['id']] = $vo["name"];
        }

        require(APP_INC_PATH.'form/Zebra_Form.php');
        $form = new Zebra_Form('form','post',U('enews/enewstypemod'));

        //隐藏表单
        $obj = & $form->add('hidden', 'id' ,$id);

        $form->add('label', 'label_name', 'name', '报刊名称&模板:');
        $obj = & $form->add('text', 'name',$data['name']);
        $obj->set_rule(array(
            'required' => array('error', '必须填写报刊名称&模板!')
        ));

        //上级报刊名称
        $form->add('label', 'label_fid', 'fid', '报刊名称:');
        $obj = & $form->add('select', 'fid',$data['fid'],array('style'=>'width:400px;'));
        $obj->add_options($select_list);
        $obj->set_rule(array(
            'required' => array('error', '必须选择报刊名称!')
        ));

        //图片上传
        $form->add('label', 'label_photo', 'photo', '模板图片:');
        $obj = & $form->add('kimg', 'photo',$data['photo'],array('style' => 'width:400px'));  //不要改id
        $obj->set_rule(array(
            'required' => array('error', '请上传模板图片!')
        ));

        $form->add('label', 'label_remark', 'remark', '备注:');
        $obj = & $form->add('text', 'remark',$data['remark'],array('style' => 'width:400px'));  //不要改id
        $obj->set_rule(array(
            //'required' => array('error', '请输入字段默认值!')
        ));

        // "submit"
        $form->add('submit', 'btnsubmit', '确定');
        $form_html =  $form->render('*horizontal');
        $this->assign('form_html',$form_html);

        /*position指定以及一些问候信息*/
        $current = "报刊名称&模板添加";
        $position = getPosition(array('报刊名称管理列表'=>'__GROUP__/enewsType/show','报刊名称添加'=>''));
        $this->assign('current',$current);
        $this->assign('position',$position);
        $this->assign('welcome',getWelcome());

        $this->display ('common:baseform');
    }

    //添加表单
    public function enewstypesave(){
        if($_SESSION['zebra_csrf_token_form'][0] != $_POST['zebra_csrf_token_form']){
            $this->error("请勿重复提交！");
        }
        $m = M('enewstype');
        $m->create();
        $m->siteid = getSiteId();
        if($m->add()!==false){
            $this->success("添加报刊模板成功！");
        }else{
            $this->error("添加报刊模板失败！");
        }
    }

    //修改表单
    public function enewstypemod(){
        if($_SESSION['zebra_csrf_token_form'][0] != $_POST['zebra_csrf_token_form']){
            $this->error("请勿重复提交！");
        }
        $m = M('enewstype');
        $m->create();
        $m->siteid = getSiteId();
        if($m->save()!==false){
            $this->success("修改报刊模板成功！");
        }else{
            $this->error("修改报刊模板失败！");
        }
    }





    public function add(){

        $ftid = $this->_param("ftid");
        if(isset($_GET["typeid"]))
            $typeid = $_GET["typeid"];

        $m2 = M('enewstype');
        $arr = $m2->where("fid={$ftid}")->select();
        $typeArr = array();
        foreach($arr as $v){
            $typeArr[$v['id']] = $v['name'];
        }

        require(APP_INC_PATH.'form/Zebra_Form.php');
        $form = new Zebra_Form('form','post',U('enews/enewssave'));

        //隐藏表单
        $form->add('hidden', 'pubdate',time());
        $form->add('hidden', 'first' ,$ftid);

        $form->add('label', 'label_typeid', 'typeid', '电子报刊类别:');
        $obj = & $form->add('select', 'typeid', $typeid,array('onchange'=>'chancetype()'));
        $obj->add_options($typeArr);
        $obj->set_rule(array(
            'required' => array('error', '必须选择电子报刊类别!')
        ));

        $form->add('label', 'label_title', 'title', '新闻标题:');
        $obj = & $form->add('text', 'title','');
        $obj->set_rule(array(
            'required' => array('error', '必须填写新闻标题!')
        ));

        $form->add('label', 'label_desc', 'desc', '简介:');
        $obj = & $form->add('textarea', 'desc','');

        $form->add('label', 'label_maodian', 'maodian', '添加锚点:');
        $form->add('text', 'maodian','',array('onclick'=>'AddAnchor()'));

        $form->add('label', 'label_writer', 'writer', '作者:');
        $form->add('text', 'writer','',array('style' => 'width:200px'));

        $form->add('label', 'label_origin', 'origin', '来源:');
        $form->add('text', 'origin','',array('style' => 'width:200px'));

        $form->add('label', 'label_txt', 'txt', '内容:');
        $obj = & $form->add('kind', 'txt','',array('style' => 'width: 800px; height: 400px; display: none;'));

        $form->add('label', 'label_status', 'status', '审核状态:');
        $obj = & $form->add('radios', 'status', array(
            '1' =>  '启用',
            '0' =>  '禁用'
        ),1);
        $obj->set_rule(array(
            'required' => array('error', '必须选择状态！')
        ));

        // "submit"
        $form->add('submit', 'btnsubmit', '确定');
        $form_html =  $form->render('*horizontal');
        $this->assign('form_html',$form_html);

        /*position指定以及一些问候信息*/
        $current = "报刊新闻添加";
        $position = getPosition(array('报刊新闻管理列表'=>'__GROUP__/enews/show','报刊新闻添加'=>''));
        $this->assign('current',$current);
        $this->assign('position',$position);
        $this->assign('welcome',getWelcome());
        $this->assign('ftid',$ftid);
        $this->assign("typeid",$typeid);

        $this->display ('common:baseform');

    }

    public function enewssave(){
        if($_SESSION['zebra_csrf_token_form'][0] != $_POST['zebra_csrf_token_form']){
            $this->error("请勿重复提交！");
        }
        $m = M('enews');
        $m->create();
        if($m->add()!==false){
            $this->success("添加报刊新闻成功！");
        }else{
            $this->error("添加报刊新闻失败！");
        }
    }








    public function edit(){
        $aid = isset($_GET['aid'])?$_GET['aid']:null;
        if($aid == null){
            $this->error("读取该条信息id失败！");
        }

        $m = M('enews');
        $map['eid'] = array('eq',$aid);
        $data = $m->where($map)->find();
        $typeid = $data['typeid'];

        require(APP_INC_PATH.'form/Zebra_Form.php');
        $form = new Zebra_Form('form','post',U('enews/enewsupdate'));  //参数分别是 表单名称 提交方法 请求页面

        //隐藏表单
        $obj = & $form->add('hidden', 'eid' ,$aid);

        $form->add('label', 'label_title', 'title', '新闻标题:');
        $obj = & $form->add('text', 'title',$data['title']);
        $obj->set_rule(array(
            'required' => array('error', '必须填写新闻标题!')
        ));

        $form->add('label', 'label_desc', 'desc', '简介:');
        $obj = & $form->add('textarea', 'desc',$data['desc']);

        $form->add('label', 'label_maodian', 'maodian', '编辑锚点:');
        $form->add('text', 'maodian',$data['maodian'],array('onclick'=>'AddAnchor()'));

        $form->add('label', 'label_writer', 'writer', '作者:');
        $form->add('text', 'writer',$data['writer'],array('style' => 'width:200px'));

        $form->add('label', 'label_origin', 'origin', '来源:');
        $form->add('text', 'origin',$data['origin'],array('style' => 'width:200px'));

        $form->add('label', 'label_txt', 'txt', '内容:');
        $obj = & $form->add('kind', 'txt',$data['txt'],array('style' => 'width: 800px; height: 400px; display: none;'));


        $form->add('label', 'label_status', 'status', '审核状态:');
        $obj = & $form->add('radios', 'status', array(
            '1' =>  '启用',
            '0' =>  '禁用'
        ),$data['status']);
        $obj->set_rule(array(
            'required' => array('error', '必须选择状态！')
        ));

        // "submit"
        $form->add('submit', 'btnsubmit', '确定');
        $form_html =  $form->render('*horizontal');
        $this->assign('form_html',$form_html);

        /*position指定以及一些问候信息*/
        $current = "友链修改";
        $position = getPosition(array('报刊新闻管理列表'=>'__GROUP__/enews/show','报刊新闻修改'=>''));
        $this->assign('current',$current);
        $this->assign('position',$position);
        $this->assign('welcome',getWelcome());
        $this->assign("typeid",$typeid);

        $this->display ('common:baseform');

    }

    public function enewsupdate(){
        $m = M('enews');
        $m->create();
        if($m->save()!==false){
            $this->success("更新报刊新闻成功！",'__GROUP__/enews/show');
        }else{
            $this->error("更新报刊新闻失败！");
        }

    }


    public function addMap(){
        $id=isset($_GET['typeid'])?$_GET['typeid']:null;
        $m=M('enewstype');
        $arr=$m->where("id=".$id)->find();
        $this->assign('photo',$arr['photo']);

        $this->display('enews:addmap');

    }




}






?>
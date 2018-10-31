<?php
class HelperAction extends Action {


    public function createArctype(){

        $arr = arctypeParse();
        if($arr==-1){
            $error = "栏目格式有误！无法正确解析！";
        }else{
            $error='';
        }


        $m = new ArctypeModel();
        $selectionArr = $m->arctypeArr();
        $selection_html = '';
        foreach($selectionArr as $k => $v){
            $selection_html .="<option value='$k' >$v</option>";
        }
        $this->assign('selection_html',$selection_html);
        $this->assign('error',$error);

        /*position指定以及一些问候信息*/
        $current = "批量生成栏目";
        $position = getPosition("批量生成栏目");
        $this->assign('current',$current);
        $this->assign('position',$position);
        $this->assign('welcome',getWelcome());

        $this->display ();

    }



    public function clearContent(){

        $m = new ArchivesModel();
        $m2 = new ChannelModel();

        $delArr = $m->field("id,channel")->where("status = 3")->select();

        if(!empty($delArr)){
            foreach($delArr as $v){
                $temp = $m2->getOne($v['channel']);
                $addtable = $temp['addtable'];
                $m->_link['addfields']['class_name'] = $addtable;
                $m->relation(true)->delete($v['id']);
            }
        }

        $this->success("删除内容成功！",U('helper/createContent'));
    }


    public function createContent(){

        if(!empty($_POST)){
            $title = !empty($_POST['title'])?$_POST['title']:"京伦";
            $num = !empty($_POST['num'])?$_POST['num']:10;
            $fid = !empty($_POST['fid'])?$_POST['fid']:0;

            $file ='<?xml version="1.0" encoding="utf-8"?>';
            $url = "http://news.baidu.com/ns?word=".$title."&tn=newsrss&sr=0&cl=2&rn=100&ct=0";
            $file .= file_get_contents($url);
            $file  = iconv("gbk","utf-8",$file);
            try{
                $rss = new SimpleXMLElement($file,LIBXML_NOCDATA|LIBXML_ERR_NONE|LIBXML_ERR_WARNING|LIBXML_NOXMLDECL,false);
            }catch(Exception $e){
                die($e);
            }
            $xmldata = array();
            foreach($rss->channel->item  as  $k=>$v){
                $xmldata[] = (array)$v;
            }


            //找出需要测试数据的栏目
            $m =  new ArctypeModel();
            $m2 = new ArchivesModel();
            $m3 = new ChannelModel();

            $arr = $m->where("status=1 and type=1")->select();

            if($m->isParent($fid)){
                $tid = $m->getSameChannelSon($fid);
            }else{
                $tid = $fid;
            }


            $map = array();
            $map['id'] = array("in",$tid);
            $arr = $m->where($map)->select();
            if(empty($arr)){
                $this->error("没有栏目可添加测试数据！");
                return;
            }else{
                //添加数据
                $index = 0;
                $ids = array();
                $err = array();
                foreach($arr as $v){

                    if(!empty($err)){
                        break;
                    }

                    for($i=0;$i<$num;$i++){

                        if(empty($xmldata[$index])){
                            $this->error("数据不够啦!");
                            return;
                        }

                        $data = array();
                        $data['typeid'] = $v['id'];
                        $data['channel'] = $v['channel'];
                        $data['type'] = 1;
                        $data['pubdate'] = time();
                        $data['senddate'] = time();
                        $data['title'] = $xmldata[$index]['title'];
                        $data['desc'] = html2text($xmldata[$index]['description']);
                        $data['author'] = $xmldata[$index]['author'];
                        $data['source'] = $xmldata[$index]['source'];
                        $data['status'] = 3;
                        $m2->create($data);
                        if($m2->add()===false){
                            $err['code'] = 1;
                            $err['msg'] = "添加测试数据遇到错误";
                            break;
                        }else{
                            $lastId = $m2->getLastInsID();
                            $addArr = $m3->where('id='.$v['channel'])->find();
                            $addtable = $addArr['addtable'];
                            $m4 = M($addtable);
                            $data = array();
                            $data['aid'] = $lastId;
                            $data['txt'] = $xmldata[$index]['description'];
                            $m4->create($data);
                            if($m4->add()===false){
                                $err['code'] = 2;
                                $err['msg'] = "添加测试数据遇到错误";
                                break;
                            }
                        }
                        $ids[] = $lastId;
                        $index++;
                    }
                }

                if(!empty($err)){
                    $this->error($err['msg'].$err['code']);
                    return;
                }else{
                    $this->success("添加测试数据成功！");
                    return true;
                }
            }

        }

        $m = new ArctypeModel();
        $selection = $m->arctypeArr();
        $selection_c = $m->arctypeArrT(1);

        require(APP_INC_PATH.'form/Zebra_Form.php');
        $form = new Zebra_Form('form','post','');

        $form->add('label', 'label_fid', 'fid', '导入栏目:');
        $obj = & $form->add('select', 'fid','');
        $obj->add_options($selection,true,$selection_c);

        $form->add('label', 'label_title', 'title', '测试数据关键词:');
        $form->add('text', 'title','京伦科技');

        $form->add('label', 'label_num', 'num', '每个栏目数据条数:');
        $form->add('text', 'num','5');


        $form->add('submit', 'btnsubmit', '确定');

        $form_html =  $form->render('*horizontal');
        $this->assign('form_html',$form_html);

        /*position指定以及一些问候信息*/
        $current = "批量生成测试数据";
        $position = getPosition("批量生成测试数据");
        $this->assign('current',$current);
        $this->assign('position',$position);
        $this->assign('welcome',getWelcome());

        $this->display ();
    }


}
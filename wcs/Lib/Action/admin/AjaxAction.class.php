<?php

class AjaxAction extends Action
{

    /**
     * 返回zTree 简单json格式
     */
    public function ajaxZtree()
    {
        import('@.Class.SuperClassify');
        //$type = $this->_get('type');
        $m = new ArctypeModel();
        $arr = $m->getSuperTreeArr();
        $nav = new SuperClassify ($arr);
        $nav->flag = "";
        $nav->create();
        $i = 0;
        foreach ($nav->treeArr as $v) {
            if ($v['status'] == -1) continue; //已删除栏目不显示
            $rs [$i]['id'] = $v['id'];
            $rs [$i]['pId'] = $v['fid'];
            $rs [$i]['name'] = $v['name'] . " [" . getChannelName($v['cid']) . "]";
            //$rs [$i]['url'] = $url.'?tid='.$v['id'].'&cid='.$v['cid'];  //废弃 在js中控制
            $rs [$i]['cid'] = $v['cid'];
            if ($v['type'] == 2) {
                $rs [$i]['iconOpen'] = __ROOT__ . '/' . APP_NAME . '/Public/images/b_f_o.jpg';
                $rs [$i]['iconClose'] = __ROOT__ . '/' . APP_NAME . '/Public/images/b_f_c.jpg';
            }
            $i++;
        }

        echo json_encode($rs);
    }


/*
* 栏目权限树,栏目只读权限
*
* */
    public function ajaxArctypeTree()
    {
        import('@.Class.SuperClassify');
        $id = $_POST['id'];
        $type = $_POST['type'];
        $siteid = session("currentSiteId");
        $m = new ArctypeModel();
        $arr = $m->getSuperTreeArr();
        $nav = new SuperClassify ($arr);
        $user = M('UserGrant');
        //group:1 user:2
        $map['type'] = ($type == 'group') ? 1 : 2;
        $map['mid'] = $id;
        $map['siteid'] = $siteid;
        $uinfo = $user->where($map)->find(); //会员只取权限栏目id字符串
        $role_read = $uinfo['read'];
        $role_read = explode(',', $role_read);
        $role_write = $uinfo['write'];
        $role_write = explode(',', $role_write);
        $role_check = $uinfo['check'];
        $role_check = explode(',', $role_check);
        $nav->flag = "";
        $nav->create();
        $i = 0;
        foreach ($nav->treeArr as $v) {
            if ($v['status'] == -1) continue; //已删除栏目不显示
            $rs[0][$i]['id'] = $v['id'];
            $rs[0][$i]['pId'] = $v['fid'];
            $rs[0][$i]['name'] = $v['name'] . " [" . getChannelName($v['cid']) . "]";

            $rs[1][$i]['id'] = $v['id'];
            $rs[1][$i]['pId'] = $v['fid'];
            $rs[1][$i]['name'] = $v['name'] . " [" . getChannelName($v['cid']) . "]";

            $rs[2][$i]['id'] = $v['id'];
            $rs[2][$i]['pId'] = $v['fid'];
            $rs[2][$i]['name'] = $v['name'] . " [" . getChannelName($v['cid']) . "]";

            //如果数据库有值，就勾选
            if (in_array($rs[0][$i]['id'], $role_read)) {
                $rs[0][$i]['checked'] = true;
            }

            if (in_array($rs[1][$i]['id'], $role_write)) {
                $rs[1][$i]['checked'] = true;
            }

            if (in_array($rs[2][$i]['id'], $role_check)) {
                $rs[2][$i]['checked'] = true;
            }

            $i++;
        }
        echo json_encode($rs);
    }


    /**
     * 将arr转成zTree可以使用的 简单json格式
     */
    public function arr2ztree()
    {
        import('@.Class.SuperClassify');
        $arr = arctypeParse();
        $i = 0;
        foreach ($arr as $v) {
            $rs [$i]['id'] = $v['id'];
            $rs [$i]['pId'] = $v['fid'];
            $rs [$i]['name'] = $v['value'] . " [" . getChannelName($v['cid']) . "]";
            $rs [$i]['cid'] = $v['cid'];

            $i++;
        }
        echo json_encode($rs);
    }


    /**
     * 系统参数类别管理
     */
    public function configType()
    {
        $item = isset($_POST['item']) ? $_POST['item'] : null;
        $typename = isset($_POST['typename']) ? $_POST['typename'] : null;
        $display = isset($_POST['display']) ? $_POST['display'] : null;
        $act = isset($_POST['act']) ? $_POST['act'] : null;
        $m = M('configtype');
        if ($act == 'add') {
            $data['typename'] = $typename;
            $data['display'] = $display;
            $m->create($data);
            if ($m->add() === false) {
                echo -1;
                return;
            }
        } else if ($act == 'edit') {
            $data['typename'] = $typename;
            $data['display'] = $display;
            $data['id'] = $item;
            $m->create($data);
            if ($m->save() === false) {
                echo -1;
                return;
            }
        } else if ($act == 'del') {
            $m2 = M('sysconfig');
            $temp = $m2->where('groupid=' . $item)->find();
            if (!empty($temp)) {
                echo -2;
                return;
            }
            if ($m->where('id=' . $item)->delete() === false) {
                echo -1;
                return;
            }
        }
        echo 1;
        return;
    }


    /**
     *友情链接类别管理
     */
    public function flinkType()
    {
        $item = isset($_POST['item']) ? $_POST['item'] : null;
        $typename = isset($_POST['typename']) ? $_POST['typename'] : null;
        $display = isset($_POST['display']) ? $_POST['display'] : null;
        $act = isset($_POST['act']) ? $_POST['act'] : null;
        $m = M('flinktype');
        if ($act == 'add') {
            $data['typename'] = $typename;
            $data['display'] = $display;
            $m->create($data);
            if ($m->add() === false) {
                echo -1;
                return;
            }
        } else if ($act == 'edit') {
            $data['typename'] = $typename;
            $data['display'] = $display;
            $data['id'] = $item;
            $m->create($data);
            if ($m->save() === false) {
                echo -1;
                return;
            }
        } else if ($act == 'del') {
            $m2 = M('flink');
            $temp = $m2->where('typeid=' . $item)->find();
            if (!empty($temp)) {
                echo -2;
                return;
            }

            if ($m->where('id=' . $item)->delete() === false) {
                echo -1;
                return;
            }
        }
        echo 1;
        return;
    }


    /**
     *电子杂志类别管理
     */
    public function enewsType()
    {
        $item = isset($_POST['item']) ? $_POST['item'] : null;
        $typename = isset($_POST['typename']) ? $_POST['typename'] : null;
        $display = isset($_POST['display']) ? $_POST['display'] : null;
        $act = isset($_POST['act']) ? $_POST['act'] : null;
        $m = M('enewstype');
        if ($act == 'add') {
            $data['name'] = $typename;
            $data['fid'] = 0;
            $m->create($data);
            if ($m->add() === false) {
                echo -1;
                return;
            }
        } else if ($act == 'edit') {
            $data['name'] = $typename;
            $data['id'] = $item;
            $m->create($data);
            if ($m->save() === false) {
                echo -1;
                return;
            }
        } else if ($act == 'del') {
            $m2 = M('enews');
            $temp = $m->where('fid=' . $item)->find();
            $temp2 = $m2->where('typeid=' . $item)->select();
            if (!empty($temp) || !empty($temp2)) {
                echo -2;
                return;
            }

            if ($m->where('id=' . $item)->delete() === false) {
                echo -1;
                return;
            }
        }
        echo 1;
        return;
    }

    //批量删除杂志新闻
    public function delEnews()
    {
        $items = !empty($_POST['items']) ? $_POST['items'] : null;
        $table = !empty($_POST['table']) ? $_POST['table'] : null;
        if ($items === null || $table == null) {
            echo -1;
            return;
        } else {
            $map['eid'] = array('in', $items);
            $m = M($table);
            if ($m->where($map)->delete() !== false) {
                echo 1;
                return;
            } else {
                echo -2;
                return;
            }
        }
        return;
    }

    /**
     * 批量删除内容
     */
    public function realDelContent()
    {
        $items = isset($_POST['items']) ? $_POST['items'] : null;
        if (empty($items)) {
            echo -1;
            return;
        }
        $itemsArr = explode(',', $items);

        $m = new ArchivesModel();
        $m2 = new ChannelModel();
        $channelArr = $m2->field('id,addtable')->select();
        $addtableArr = array();
        foreach ($channelArr as $v) {
            $addtableArr[$v['id']] = $v['addtable'];
        }
        foreach ($itemsArr as $v) {
            $temp = $m->getOne($v);
            if (empty($temp)) {
                echo -1;
                return;
            }
            $addtable = $addtableArr[$temp['channel']];
            if ($addtable == null) {
                echo -1;
                return;
            }
            $m->_link['addfields']['class_name'] = $addtable;
            if ($m->relation(true)->delete($v) === false) {
                $this->success("删除内容成功！");
            }
        }
        echo 1;
        return;
    }

    /**
     * 批量还原内容
     */
    public function restoreContent()
    {
        $items = isset($_POST['items']) ? $_POST['items'] : null;
        if (empty($items)) {
            echo -1;
            return;
        }
        $m = new ArchivesModel();
        $itemsArr = explode(',', $items);
        foreach ($itemsArr as $v) {

            $data = array();
            $data['status'] = 1;
            $data['id'] = $v;
            $m->create($data);
            if ($m->save() === false) {
                echo -1;
                return;
            }
        }
        echo 1;
        return;
    }


    //批量创建栏目
    public function createArctype()
    {
        $totid = isset($_POST['totid']) ? $_POST['totid'] : null;
        if ($totid === null) {
            echo -1;
            return;
        } else {
            $m = new ArctypeModel();
            $toArr = $m->getOne($totid);
            $arr = arctypeParse();
            if ($toArr['id'] == 0) {
                $nrouter = 0;
            } else {
                $nrouter = $toArr['route'] . "-" . $totid;
            }
            $rs = recurrenceArctype($arr, 0, $totid, $nrouter);
            if ($rs === false) {
                echo -1;
                return false;
            } else {
                echo 1;
                return;
            }
        }
    }


    /**
     * 栏目批量排序
     */
    public function arctypeSetOrder()
    {
        $json = !empty($_POST['json']) ? $_POST['json'] : null;
        if ($json === null) {
            echo -1;
            return;
        } else {
            $m = new ArctypeModel();
            $temp = array();
            foreach ($json as $k => $v) {
                if (in_array($v, $temp)) {
                    echo -2;
                    return;
                }
                $temp[] = $v;
            }

            foreach ($json as $k => $v) {
                $data['id'] = $k;
                $data['order'] = $v;
                if ($m->save($data) === false) {
                    echo -1;
                    return;
                }
            }
        }
    }


    /**
     * 通用批量排序
     * 默认为order字段
     */
    public function SetOrderCommon()
    {
        $json = !empty($_POST['json']) ? $_POST['json'] : null;
        $table = !empty($_POST['table']) ? $_POST['table'] : null;
        $field = !empty($_POST['field']) ? $_POST['field'] : 'order';
        if ($json === null || $table === null) {
            echo -3;
            return;
        } else {
            $m = M($table);
            $temp = array();
            foreach ($json as $k => $v) {
                if (in_array($v, $temp)) {
                    echo -2;
                    return;
                }
                $temp[] = $v;
            }
            foreach ($json as $k => $v) {
                $data['id'] = $k;
                $data[$field] = $v;
                if ($m->save($data) === false) {
                    echo -1;
                    return;
                }
            }
        }
    }


    /**
     * 检查该栏目下是否已经有内容
     */
    public function hasContent()
    {
        $tid = isset($_POST['tid']) ? $_POST['tid'] : null;
        $cid = isset($_POST['cid']) ? $_POST['cid'] : null;
        $m = new ArctypeModel();

        $arctypeArr = $m->getOne($tid);
        if ($arctypeArr['type'] == 1) {
            $m2 = new ArchivesModel();
            $map['status'] = array('eq', 1);
        } else if ($arctypeArr['type'] == 2) {
            $c = new ChannelModel();
            $channelArr = $c->getOne($cid);
            $table = $channelArr['addtable'];
            $m2 = M($table);
        } else {
            echo -2;
            return;
        }

        $map['typeid'] = array('eq', $tid);
        $rs = $m2->where($map)->select();
        if (empty($rs)) echo 1;
        else echo -1;
    }


    /**
     * 栏目批量移动ajax验证后的栏目options
     */

    public function arctypeMoveSelect()
    {
        $tid = isset($_POST['tid']) ? $_POST['tid'] : null;
        $items = isset($_POST['items']) ? $_POST['items'] : null;
        /*移动到选中栏目的数组*/
        $m = new ArctypeModel();
        $arctypeArr = $m->getOne($tid);
        $selectionArr = $m->arctypeArr();
        $selectionArr_C = $m->arctypeArrT($arctypeArr['type'], $items, $arctypeArr['channel']);
        $selection_html = '';
        foreach ($selectionArr as $k => $v) {
            if ($k == $tid) $checked = 'selected style="background:#DBDBDB;" '; else $checked = '';
            if ($selectionArr_C != null && $selectionArr_C[$k] == 0) $flag = 'disabled style="background:#E9F6F9;"'; else $flag = '';
            $selection_html .= "<option value='$k' $checked $flag >$v</option>";
        }
        if (empty($selection_html)) echo -1;
        else echo $selection_html;
    }

    /**
     * 检查内容模型是否统一
     */
    public function checkChannelUnity()
    {
        $items = isset($_POST['items']) ? $_POST['items'] : null;
        $tid = isset($_POST['tid']) ? $_POST['tid'] : null;
        $cid = isset($_POST['cid']) ? $_POST['cid'] : null;
        $type = isset($_POST['type']) ? $_POST['type'] : null;

        /*        items:1
                cid:6
                type:2
                tid:63*/

        $itemsArr = explode(',', $items);
        $rs = array();
        if (empty($itemsArr)) {
            $rs['code'] = -1;
            $rs['msg'] = "没有选中任何项!";
            echo json_encode($rs);
            return;
        }
        //含主表的模型
        if ($type == 1) {
            $m = new ArchivesModel();
            $first = $m->getOne($itemsArr[0]);
            $cid = $first['channel'];

            foreach ($itemsArr as $v) {
                $temp = $m->getOne($v);
                if ($temp['channel'] != $cid) {
                    $rs['code'] = -1;
                    $rs['msg'] = "批量移动的内容必须属于同一内容模型！";
                    echo json_encode($rs);
                    return;
                }
            }
            $real_cid = $first['channel'];
            $real_tid = $first['typeid'];
        } else if ($type == 2) {
            $m2 = M("Channel");
            $addtable = $m2->where("id = " . $cid)->getField("addtable");
            if (empty($addtable)) {
                $rs['code'] = -1;
                $rs['msg'] = "批量移动的内容必须属于同一内容模型！";
                echo json_encode($rs);
                return;
            } else {
                $addM = M($addtable);
                $arr = $addM->field("id,channel,typeid")->where("id in (" . $items . ")")->select();
                if (!empty($arr)) {
                    foreach ($arr as $v2) {
                        if ($v2['channel'] != $cid) {
                            $rs['code'] = -1;
                            $rs['msg'] = "批量移动的内容必须属于同一内容模型！";
                            echo json_encode($rs);
                            return;
                        }
                        $real_cid = $v2['channel'];
                        $real_tid = $v2['typeid'];
                    }
                } else {
                    $rs['code'] = -1;
                    $rs['msg'] = "内容模型异常!";
                    echo json_encode($rs);
                    return;
                }

            }
        } else {
            $rs['code'] = -1;
            $rs['msg'] = "选中项为未知的内容模型!";
            echo json_encode($rs);
            return;
        }

        $temp['cid'] = $real_cid;
        $temp['tid'] = $real_tid;
        $rs['code'] = 1;
        $rs['data'] = $temp;
        echo json_encode($rs);
        return;
    }


    //获得推送到站点可以选择的栏目

    public function getArctypeForPush()
    {
        $tid = isset($_POST['tid']) ? $_POST['tid'] : null;
        $cid = isset($_POST['cid']) ? $_POST['cid'] : null;
        $sid = isset($_POST['sid']) ? $_POST['sid'] : null;
        $m3 = new ArctypeModel();
        $selectionArr = $m3->arctypeArr($sid);
        $selectionArr_C = $m3->arctypeArrC2($cid, $sid);
        $selection_html = '';
        foreach ($selectionArr as $k => $v) {
            if ($k == $tid) $checked = 'selected style="background:#DBDBDB;" '; else $checked = '';
            if ($selectionArr_C != null && $selectionArr_C[$k] == 0) $flag = 'disabled style="background:#E9F6F9;"'; else $flag = '';
            $selection_html .= "<option value='$k' $checked $flag >$v</option>";
        }
        echo $selection_html;
        return;
    }

    //执行推送 (老方法)
    /*    public function pushdata(){
            $ids = isset($_POST['items'])?$_POST['items']:null;
            $cid = isset($_POST['cid'])?$_POST['cid']:null;
            $sid = isset($_POST['sid'])?$_POST['sid']:null;
            $tid = isset($_POST['tid'])?$_POST['tid']:null;
            $ttid = isset($_POST['ttid'])?$_POST['ttid']:null;
            $fsid = getSiteId();

            $rs = array();
            if(empty($ids) || empty($cid) || empty($sid) || empty($tid) || empty($ttid)){
                $rs['code'] =  -1;
                $rs['msg'] = '推送参数有误！请核实!';
            }else{
                $m = M("PushData");
                $idArr = explode(",",$ids);
                if(!empty($idArr)){
                    $data = array();
                    $yes  = array();
                    foreach($idArr as $v){
                        $data['id'] = $v;
                        $data['cid'] = $cid;
                        $data['fsid'] = $fsid;
                        $data['tsid'] = $sid;
                        $data['ttid'] = $ttid;
                        $data['ftid'] = $tid;
                        $data['time'] = time();
                        $m->create($data);
                        if($m->add() !== false){
                            $yes[] = $v;
                        }
                    }

                    $rs['code'] =  1;
                    $rs['msg'] = implode(",",$yes)." 已经成功推送！";

                }else{
                    $rs['code'] =  -1;
                    $rs['msg'] = '没有选中任何项！';
                }
            }

            echo json_encode($rs);
        }*/

    public function pushdata()
    {
        $ids = isset($_POST['items']) ? $_POST['items'] : null;
        $cid = isset($_POST['cid']) ? $_POST['cid'] : null;
        $sid = isset($_POST['sid']) ? $_POST['sid'] : null;
        $tid = isset($_POST['tid']) ? $_POST['tid'] : null;
        $ttid = isset($_POST['ttid']) ? $_POST['ttid'] : null;
        $type = isset($_POST['type']) ? $_POST['type'] : null;
        $fsid = getSiteId();
        $syn = C('SYS_PUSH_SYN');
        $rs = array();
        if (empty($ids) || empty($cid) || empty($sid) || empty($tid) || empty($ttid) || empty($type)) {
            $rs['code'] = -1;
            $rs['msg'] = '推送参数有误！请核实!';
        } else {
            $m = M("PushData");
            $idArr = explode(",", $ids);
            if (!empty($idArr)) {
                $data = array();
                $yes = array();
                foreach ($idArr as $v) {
                    $map = array();
                    $map['fid'] = $v;
                    $map['tsid'] = $sid;
                    $arr = array();
                    $arr = $m->where($map)->find();
                    if (!empty($arr)) {
                        $rs['code'] = -1;
                        $rs['msg'] = '请勿重复推送数据到一个站点!';
                        echo json_encode($rs);
                        return;
                    }
                    $data['fid'] = $v;
                    $data['cid'] = $cid;
                    $data['fsid'] = $fsid;
                    $data['tsid'] = $sid;
                    $data['ttid'] = $ttid;
                    $data['ftid'] = $tid;
                    $data['type'] = $type;
                    $data['syn'] = $syn;
                    $data['time'] = time();
                    $toid = $this->copyData($data);
                    if (empty($toid)) {
                        continue;
                    } else {
                        $data['toid'] = $toid;
                    }

                    $m->create($data);
                    if ($m->add($data) !== false) {
                        $yes[] = $v;
                    }
                }
                $rs['code'] = 1;
                $rs['msg'] = "ID 为：" . implode(",", $yes) . " 的内容已经成功推送！";
            } else {
                $rs['code'] = -1;
                $rs['msg'] = '没有选中任何项！';
            }
        }
        echo json_encode($rs);
        return;
    }

    protected function copyData($data)
    {
        if (empty($data)) {
            return null;
        }

        $cM = M('Channel');
        $addtable = $cM->where("id =" . $data['cid'])->getField("addtable");
        if (empty($addtable)) {
            return null;
        }

        if ($data['type'] == 1) {

            $m = new ArchivesModel();
            $m->_link['addfields']['class_name'] = $addtable;
            $map['id'] = $data['fid'];
            $map['siteid'] = $data['fsid'];
            $old = $m->relation(true)->where($map)->find();
            if (empty($old)) {
                return null;
            }
            $new = $old;
            $new['id'] = null;
            $new['siteid'] = $data['tsid'];
            $new['typeid'] = $data['ttid'];
            $new['cdir'] = '';
            $new['status'] = 0;
            $newadd = $new['addfields'];
            unset($new['addfields']);
            $m->create($new);
            if ($m->add() === false) {
                return null;
            } else {
                $newid = $m->getLastInsID();
                $newadd['aid'] = $newid;
                $addM = M($addtable);
                $addM->create($newadd);
                if ($addM->add() === false) {
                    return null;
                } else {
                    return $newid;
                }
            }
        } else if ($data['type'] == 2) {

            $m = M($addtable);
            $map['id'] = $data['fid'];
            $map['siteid'] = $data['fsid'];
            $old = $m->where($map)->find();
            if (empty($old)) {
                return null;
            }
            $new = $old;
            $new['id'] = null;
            $new['siteid'] = $data['tsid'];
            $new['typeid'] = $data['ttid'];
            $new['status'] = 0;
            $m->create($new);
            if ($m->add() === false) {
                return null;
            } else {
                return $m->getLastInsID();
            }
        } else {
            return null;
        }
    }


    /**
     * 内容批量移动ajax验证后的栏目options
     */

    public function contentMoveSelect()
    {
        $items = isset($_POST['items']) ? $_POST['items'] : null;
        $tid = isset($_POST['tid']) ? $_POST['tid'] : null;
        $cid = isset($_POST['cid']) ? $_POST['cid'] : null;
        $type = isset($_POST['type']) ? $_POST['type'] : null;
        $itemsArr = explode(',', $items);
        if (empty($itemsArr)) {
            echo -1;
            return;
        }

        if ($type == 1) {
            $m = new ArchivesModel();
            $first = $m->getOne($itemsArr[0]);
            $cid = $first['channel'];

            foreach ($itemsArr as $v) {
                $temp = $m->getOne($v);
                if ($temp['channel'] != $cid) {
                    echo -2;
                    return;
                }
            }
        } else if ($type == 2) {
            $m2 = M("Channel");
            $addtable = $m2->where("id = " . $cid)->getField("addtable");
            if (empty($addtable)) {
                echo -2;
                return;
            } else {
                $addM = M($addtable);
                $arr = $addM->field("id,channel")->where("id in (" . $items . ")")->select();
                if (!empty($arr)) {
                    foreach ($arr as $v2) {
                        if ($v2['channel'] != $cid) {
                            echo -2;
                            return;
                        }
                    }
                }
            }
        } else {
            echo -2;
            return;
        }

        //内容移动到的栏目选择
        $m3 = new ArctypeModel();
        $selectionArr = $m3->arctypeArr();
        $selectionArr_C = $m3->arctypeArrC2($cid);
        $selection_html = '';
        foreach ($selectionArr as $k => $v) {
            if ($k == $tid) $checked = 'selected style="background:#DBDBDB;" '; else $checked = '';
            if ($selectionArr_C != null && $selectionArr_C[$k] == 0) $flag = 'disabled style="background:#E9F6F9;"'; else $flag = '';
            $selection_html .= "<option value='$k' $checked $flag >$v</option>";

        }
        echo $selection_html;
        return;
    }

    /**
     * 内容批量复制ajax验证后的栏目options
     */

    public function contentCopySelect()
    {
        $items = isset($_POST['items']) ? $_POST['items'] : null;
        $tid = isset($_POST['tid']) ? $_POST['tid'] : null;
        $cid = isset($_POST['cid']) ? $_POST['cid'] : null;
        $type = isset($_POST['type']) ? $_POST['type'] : null;
        $itemsArr = explode(',', $items);
        if (empty($itemsArr)) {
            echo -1;
            return;
        }

        if ($type == 1) {
            $m = new ArchivesModel();
            $first = $m->getOne($itemsArr[0]);
            $cid = $first['channel'];

            foreach ($itemsArr as $v) {
                $temp = $m->getOne($v);
                if ($temp['channel'] != $cid) {
                    echo -2;
                    return;
                }
            }
        } else if ($type == 2) {
            $m2 = M("Channel");
            $addtable = $m2->where("id = " . $cid)->getField("addtable");
            if (empty($addtable)) {
                echo -2;
                return;
            } else {
                $addM = M($addtable);
                $arr = $addM->field("id,channel")->where("id in (" . $items . ")")->select();
                if (!empty($arr)) {
                    foreach ($arr as $v2) {
                        if ($v2['channel'] != $cid) {
                            echo -2;
                            return;
                        }
                    }
                }
            }
        } else {
            echo -2;
            return;
        }

        //内容复制到的栏目选择
        $m3 = new ArctypeModel();
        $selectionArr = $m3->arctypeArr();
        $selectionArr_C = $m3->arctypeArrC2($cid);
        $selection_html = '';
        foreach ($selectionArr as $k => $v) {
            if ($k == $tid) $checked = 'selected style="background:#DBDBDB;" '; else $checked = '';
            if ($selectionArr_C != null && $selectionArr_C[$k] == 0) $flag = 'disabled style="background:#E9F6F9;"'; else $flag = '';
            $selection_html .= "<option value='$k' $checked $flag >$v</option>";

        }
        echo $selection_html;
        return;
    }

    /**
     * 采集内容批量移动ajax验证后的栏目options
     */

    public function collcetMoveSelect()
    {
        $m = new ArchivesModel();
        //内容移动到的栏目选择
        $m3 = new ArctypeModel();
        $selectionArr = $m3->arctypeArr();
        //todo 采集内容默认只能导入到addnews内容模型
        $selectionArr_C = $m3->arctypeArrC2(1);
        $selection_html = '';
        foreach ($selectionArr as $k => $v) {
            if ($selectionArr_C != null && $selectionArr_C[$k] == 0) $flag = 'disabled style="background:#E9F6F9;"'; else $flag = '';
            $selection_html .= "<option value='$k' $flag >$v</option>";

        }
        echo $selection_html;
        return;
    }


    /**
     * 内容栏目移动
     */

    public function moveContent()
    {
        $items = isset($_POST['items']) ? $_POST['items'] : null;
        $tid = isset($_POST['tid']) ? $_POST['tid'] : null;
        $cid = isset($_POST['cid']) ? $_POST['cid'] : null;
        $type = isset($_POST['type']) ? $_POST['type'] : null;

        if ($items == null || $tid == null || $cid == null || $type == null) {
            echo -1;
            return;
        }

        if ($type == 1) {
            $m = new ArchivesModel();
            if ($m->where('id in (' . $items . ')')->setField('typeid', $tid) !== false) {
                echo 1;
                return;
            } else {
                echo -1;
                return;
            }
        } else if ($type == 2) {
            $cM = M("Channel");
            $addtable = $cM->where("id=" . $cid)->getField("addtable");
            $addM = M($addtable);
            if ($addM->where('id in (' . $items . ')')->setField('typeid', $tid) !== false) {
                echo 1;
                return;
            } else {
                echo -1;
                return;
            }
        } else {
            echo -1;
            return;
        }
    }

    /**
     * 内容栏目复制
     */

    public function copyContent()
    {
        $items = isset($_POST['items']) ? $_POST['items'] : null;
        $tid = isset($_POST['tid']) ? $_POST['tid'] : null;
        $cid = isset($_POST['cid']) ? $_POST['cid'] : null;
        $type = isset($_POST['type']) ? $_POST['type'] : null;

        if ($items == null || $tid == null || $cid == null || $type == null) {
            echo -1;
            return;
        }

        if ($type == 1) {			
            $m = new ArchivesModel();
			$cm = M("Channel");
			$aset = $m->where('id =' . $items)->select();
			$aset[0]['typeid']=$tid;
			unset($aset[0]['id']); 
			$addtable = $cm->where('id =' . $aset[0]['channel'])->getField('addtable');
			$tm= M($addtable);
			$tset=$tm->where('aid =' . $items)->select();
			$id=$m->data($aset[0])->add();
			$tset[0]['aid']=$id;
			$r=$tm->data($tset[0])->add();
            if ($id !== false and  $r!== false) {
                echo 1;
                return;
            } else {
                echo -1;
                return;
            }
        } else if ($type == 2) {  
                echo 0;
                return;            
        } else {
            echo -1;
            return;
        }
    }

    /**
     * 内容批量增加属性
     */
    public function addflag()
    {
        $items = isset($_POST['items']) ? $_POST['items'] : null;
        $flags = isset($_POST['flags']) ? $_POST['flags'] : null;

        $itemsArr = explode(',', $items);
        $flagsArr = explode(',', $flags);
        $m = new ArchivesModel();
        foreach ($itemsArr as $v) {

            $temp = $m->getOne($v);
            $oldflags = $temp['flag'];

            if (!empty($oldflags)) {
                $oldflagsArr = explode(',', $oldflags);
                $flagadd = array_diff($flagsArr, $oldflagsArr);
                $flagaddstr = implode(',', $flagadd);
                $data["flag"] = $flagaddstr . ',' . $oldflags;
            } else {
                $data["flag"] = $flags;
            }

            $data['id'] = $v;
            $m->create($data);
            if ($m->save() === false) {
                echo $m->getLastSql();
                echo -1;
                return;
            }

        }
        echo 1;
        return;
    }


    /**
     * 批量移除属性
     */
    public function delflag()
    {
        $items = isset($_POST['items']) ? $_POST['items'] : null;
        $flags = isset($_POST['flags']) ? $_POST['flags'] : null;

        $itemsArr = explode(',', $items);
        $flagsArr = explode(',', $flags);

        $m = new ArchivesModel();
        foreach ($itemsArr as $v) {

            $temp = $m->getOne($v);
            $oldflags = $temp['flag'];

            if (!empty($oldflags)) {
                $oldflagsArr = explode(',', $oldflags);

                foreach ($oldflagsArr as $k2 => $v2) {

                    if (in_array($v2, $flagsArr)) {
                        unset($oldflagsArr[$k2]);
                    }
                }

                if (!empty($oldflagsArr)) {
                    $newflags = implode(',', $oldflagsArr);
                    $data['flag'] = $newflags;

                } else {
                    $data['flag'] = '';
                }

                $data['id'] = $v;
                $m->create($data);
                if ($m->save() === false) {
                    //echo $m->getLastSql();
                    echo -1;
                    return;
                }
            }
        }
        echo 1;
        return;

    }


    public function commonCheck()
    {
        $items = !empty($_POST['items']) ? $_POST['items'] : null;
        $cid = !empty($_POST['cid']) ? $_POST['cid'] : null;
        $type = !empty($_POST['type']) ? $_POST['type'] : null;
        if (empty($items)) {
            echo -1;
            return;
        } else {
            if ($type == 1) {
                $data['id'] = array('in', $items);
                $data['status'] = 1;
                $m = new ArchivesModel();
                if ($m->save($data) !== false) {
                    echo 1;
                    return;
                } else {
                    echo -2;
                    return;
                }
            } else if ($type == 2) {
                $m2 = new ChannelModel();
                $channelArr = $m2->getOne($cid);
                $table = $channelArr['addtable'];
                $m3 = M($table);
                $data = array();
                $data['id'] = array('in', $items);
                $data['status'] = 1;
                if ($m3->save($data) !== false) {
                    echo 1;
                    return;
                } else {
                    echo -2;
                    return;
                }
            } else {
                echo -1;
                return;
            }
        }
        return;
    }


    /**
     * 栏目批量删除前的ajax检测
     */
    public function deleteable()
    {
        $items = !empty($_POST['items']) ? $_POST['items'] : null;
        if ($items === null) {
            echo -1;
            return;
        } else {
            $m = new ArctypeModel();
            $unable = '';
            $itemsArr = explode(',', $items);
            foreach ($itemsArr as $v) {
                if ($m->isParent($v) == true) {
                    $unable .= $v . ",";
                    continue;
                } else {
                    $data['id'] = $v;
                    $data['status'] = -1;
                    $data['recycledate'] = time();
                    if ($m->save($data) === false) {
                        echo -1;
                        return;
                    }
                }
            }
            if ($unable != '') {
                echo $unable;
            } else {
                echo 1;
            }
        }
        return;
    }


    /**
     * 彻底删除栏目
     */
    public function realDelArctype()
    {
        $items = !empty($_POST['items']) ? $_POST['items'] : null;
        if ($items === null) {
            echo -1;
            return;
        } else {
            $m = new ArctypeModel();
            if ($m->where("id in (" . $items . ")")->delete() !== false) {
                echo 1;
                return;
            } else {
                echo -2;
                return;
            }
        }
        return;
    }


    /**
     * 还原栏目
     */
    public function restoreArctype()
    {
        $items = !empty($_POST['items']) ? $_POST['items'] : null;
        if ($items === null) {
            echo -1;
            return;
        } else {
            $data['id'] = array('in', $items);
            $data['status'] = 1;
            $m = new ArctypeModel();
            if ($m->save($data) !== false) {
                echo 1;
                return;
            } else {
                echo -2;
                return;
            }
        }
        return;
    }

    /**
     * 将内容放入回收站
     */
    public function delContent()
    {
        $items = !empty($_POST['items']) ? $_POST['items'] : null;
        $cid = !empty($_POST['cid']) ? $_POST['cid'] : null;
        $type = !empty($_POST['type']) ? $_POST['type'] : null;
        if ($items === null) {
            echo -1;
            return;
        } else {
            if ($type == 1) {
                $data['id'] = array('in', $items);
                $data['status'] = -1;
                $data['recycledate'] = time();
                $m = new ArchivesModel();
                if ($m->save($data) !== false) {
                    echo 1;
                    return;
                } else {
                    echo -2;
                    return;
                }
            } else if ($type == 2) {
                $m2 = new ChannelModel();
                $channelArr = $m2->getOne($cid);
                $table = $channelArr['addtable'];
                $m3 = M($table);
                if ($m3->where("id in (" . $items . ")")->delete() !== false) {
                    echo 1;
                    return;
                } else {
                    echo -2;
                    return;
                }
            } else {
                echo -1;
                return;
            }
        }
        return;
    }

//已经废弃的推送模式方法
    /*    public function delpushdata(){
            $items = !empty($_POST['items'])?$_POST['items']:null;
            $cid = !empty($_POST['cid'])?$_POST['cid']:null;
            $sid = !empty($_POST['sid'])?$_POST['sid']:null;
            if($items===null){
                echo -1;
                return;
            }else{
                $m = M("PushData");
                $map['tsid'] = array("eq",$sid);
                $map['cid'] = array("eq",$cid);
                $map['id'] = array("in",$items);
               if( $m->where($map)->delete() === false ) {
                   echo -1;
                   return;
               }else{
                   echo 1;
                   return;
               }
            }

        }*/


    public function channelajax()
    {
        $action = isset($_POST['action']) ? $_POST['action'] : null;
        if ($action === null) {
            echo -1;
            return;
        }

        if ($action == 'fieldorder') {
            $data = isset($_POST['data']) ? $_POST['data'] : null;
            $cid = isset($_POST['cid']) ? $_POST['cid'] : null;
            if ($cid === null || $data === null) {
                echo -1;
                return;
            }
            $m = new ChannelModel();
            $arr = $m->field('fieldset')->where('id=' . $cid)->find();
            $arr = unserialize($arr['fieldset']);
            //dump($data);
            $newfieldset = array();
            foreach ($data as $k => $v) {
                $newfieldset[$k] = $arr[$v['index']];
            }
            //dump($newfieldset);
            $field_str = serialize($newfieldset);
            $newdata['fieldset'] = $field_str;
            $newdata['id'] = $cid;
            if ($m->save($newdata) !== false) {
                echo 1;
                return;
            } else {
                echo "排序保存失败!";
                return;
            }
        } else {
            echo -1;
            return;
        }
    }


    /**
     * 删除内容模型
     */
    public function delChannel()
    {
        $items = !empty($_POST['items']) ? $_POST['items'] : null;
        if ($items === null) {
            echo -1;
            return;
        } else {
            $map['id'] = array('in', $items);
            $m = M('Channel');
            if ($m->where($map)->delete() !== false) {
                echo 1;
                return;
            } else {
                echo -2;
                return;
            }
        }
        return;
    }

    /**
     * 通用批量删除
     * 广告(ad)
     */
    public function delCommon()
    {
        $items = !empty($_POST['items']) ? $_POST['items'] : null;
        $table = !empty($_POST['table']) ? $_POST['table'] : null;
        if ($items === null || $table == null) {
            echo -1;
            return;
        } else {
            $map['id'] = array('in', $items);
            $m = M($table);
            if ($m->where($map)->delete() !== false) {
                echo 1;
                return;
            } else {
                echo -2;
                return;
            }
        }
        return;
    }


    /*
     * 重置管理员密码
     */
    public function pwdreset()
    {
        $items = !empty($_POST['items']) ? $_POST['items'] : null;
        if ($items === null) {
            echo -1;
            return;
        } else {
            $data['pwd'] = "e388f02f750e65ebba95ab9493cda01e";
            $data['id'] = array('in', $items);
            $m = M('Admin');
            $m->create($data);
            if ($m->save($data) !== false) {
                echo 1;
                return;
            } else {
                echo -2;
                return;
            }
        }
        return;
    }


    /**
     * 通用更新某个字段,常用于审核
     */

    public function checkCommon()
    {
        $items = !empty($_POST['items']) ? $_POST['items'] : null;
        $table = !empty($_POST['table']) ? $_POST['table'] : null;
        $field = !empty($_POST['field']) ? $_POST['field'] : null;
        $value = !empty($_POST['value']) ? $_POST['value'] : null;
        if ($items === null || $table == null || $field == null) {
            echo -1;
            return;
        } else {
            $data[$field] = $value;
            $map['id'] = array('in', $items);
            $m = M($table);
            if ($m->where($map)->save($data) !== false) {
                echo 1;
                return;
            } else {
                echo -2;
                return;
            }
        }
        return;
    }


    /**
     * 检查一个变量的值是否存在 （用于form不重复字段验证）
     * @param unknown_type $arr
     */
    public function isValExist($v, $t, $f)
    {
        $value = isset($_POST['v']) ? $_POST['v'] : null;
        $table = isset($_POST['t']) ? $_POST['t'] : null;
        $field = isset($_POST['f']) ? $_POST['f'] : null;

        if ($value == null || $table == null || $field == null) {
            echo 0;
            return;
        }

        $m = M($table);
        $rs = $m->field($field)->select();
        foreach ($rs as $v) {
            $arr[] = $v[$field];
        }

        if (in_array($value, $arr))
            echo 0;
        else echo 1;
    }


    /**
     * 检查一个变量的值是否存在 （用于form不重复字段验证）
     * @param unknown_type $arr
     */
    public function isFieldExist($v)
    {
        $value = isset($_POST['v']) ? $_POST['v'] : null;
        $cid = isset($_POST['cid']) ? $_POST['cid'] : null;
        if ($value == null || $cid == null) {
            echo 0;
            return;
        }

        $m = new ChannelModel();
        $arr = $m->field('nid')->where('id=' . $cid)->find();
        $nid = $arr['nid'];
        $table = 'add' . $nid;

        if (is_numeric($value)) {
            echo 0;
            return;
        }

        $m = D($table);
        $add = $m->getDbFields();
        //$mfields = array("id","typeid","typeid2","sortrank","flag","ismake","channel","arcrank","click","title","color","pubdate","scores","dutyadmin","type","status","order","url","class","litpic","rank","display","tdir","cdir");
        $mfields = array();
        if (in_array($value, $add) || in_array($value, $mfields)) {
            echo 0;
            return;
        }
        echo 1;
        return;
    }


    public function navdeleteable()
    {
        $items = isset($_POST['items']) ? $_POST['items'] : null;
        if ($items === null) {
            echo -1;
            return;
        } else {
            $m = M('adminmodule');
            $arr = $m->where('fid=' . $items)->select();
            if (empty($arr)) {
                echo 1;
                return;
            } else {
                echo -1;
                return;
            }
        }
    }


    public function navajax()
    {
        $action = isset($_POST['action']) ? $_POST['action'] : null;
        if ($action === null) {
            echo -1;
            return;
        }

        if ($action == 'moduleorder') {
            $data = isset($_POST['data']) ? $_POST['data'] : null;
            $m = M('adminmodule');
            $arr = $m->select();
            foreach ($arr as $v) {
                $moudel[$v['id']]['fid'] = $v['fid'];
                $moudel[$v['id']]['order'] = $v['order'];
            }

            //dump($moudel);

            foreach ($data as $v) {
                if ($v['fid'] == $moudel[$v['id']]['fid'] && $v['order'] == $moudel[$v['id']]['order']) continue;
                else {
                    $ndata['id'] = $v['id'];
                    $ndata['fid'] = $v['fid'];
                    $ndata['order'] = $v['order'];
                    $m->create($ndata);
                    if ($m->save() !== false) {
                        continue;
                    } else {
                        echo -1;
                        return;
                    }


                }
            }
            echo 1;
            return;
        } else if ($action == 'toporder') {
            $data = isset($_POST['data']) ? $_POST['data'] : null;
            $m = M('admintopnav');
            $arr = $m->select();
            foreach ($arr as $v) {
                $moudel[$v['id']]['order'] = $v['order'];
            }
            foreach ($data as $v) {
                if ($v['order'] == $moudel[$v['id']]['order']) continue;
                else {
                    $ndata['id'] = $v['id'];
                    $ndata['order'] = $v['order'];
                    $m->create($ndata);
                    if ($m->save() !== false) {
                        continue;
                    } else {
                        echo -1;
                        return;
                    }
                }
            }
            echo 1;
            return;
        } else if ($action == 'getmodule') {
            $nav = isset($_POST['nav']) ? $_POST['nav'] : null;
            if ($nav === null) {
                echo -1;
                return;
            }

            //todo:show 方法覆盖了tp原有show方法
            $base = get_class_methods('Action');
            unset($base[array_search("show", $base)]);
            $classname = $nav . "Action";
            $arr = get_class_methods($classname);
            $actions = array_diff($arr, $base);
            $option = '<option value="">- 请选择 -</option>';
            $besides = C('SYS_ACTION_BESIDES');
            foreach ($actions as $v) {
                if (!in_array($v, $besides))
                    $option .= "<option value='$v' >$v</option>";
            }

            echo $option;
        } else
            echo -1;
        return;
    }


    public function wxldajax()
    {
        $action = isset($_POST['action']) ? $_POST['action'] : null;
        $id = isset($_POST['id']) ? $_POST['id'] : null;

        $m = M('wxldtype');
        $m2 = M('wxld');
        if ($action == 'update') {
            $m->create();
            $data['typename'] = $_POST['typename'];
            $m2->create($data);
            if ($m2->where('fid=0 and typeid=' . $id)->save() !== false && $m->save() !== false) {
                echo 1;
            } else {
                echo -1;
            }
            return;
        } else
            if ($action == 'add') {
                $m->create();
                if ($m->add() !== false) {
                    $lastid = $m->getLastInsID();
                    $m2->create();
                    $m2->fid = 0;
                    $m2->typeid = $lastid;
                    if ($m2->add() !== false) {
                        echo 1;
                    } else {
                        echo -1;
                    }
                } else {
                    echo -1;
                }
                return;
            } else if ($action == 'del') {
                if ($m->where('id=' . $id)->delete() !== false) {
                    if ($m2->where('typeid=' . $id)->delete()) {
                        echo 1;
                    } else {
                        echo -1;
                    }
                } else {
                    echo -1;
                }
                return;
            } else if ($action == 'update2') {
                $arr = $m2->where('id=' . $id)->find();
                if ($arr['fid'] == 0) {
                    $data = array();
                    $data['typename'] = $_POST['typename'];
                    $data['id'] = $arr['typeid'];
                    $m->create($data);
                    if ($m->save() === false) {
                        echo -1;
                        return;
                    }
                }
                $m2->create();
                if ($m2->save() !== false) {
                    echo 1;
                } else {
                    echo -1;
                }
                return;
            } else
                if ($action == 'add2') {
                    $fid = isset($_POST['fid']) ? $_POST['fid'] : null;
                    if ($fid == null) {
                        echo "选中父类！";
                        return;
                    }
                    $arr = $m2->where('id=' . $fid)->find();

                    $m2->create();
                    $m2->typeid = $arr['typeid'];
                    if ($m2->add() !== false) {
                        echo 1;
                    } else {
                        echo -1;
                    }
                    return;
                } else if ($action == 'del2') {

                    $arr = $m2->where('fid=' . $id)->find();

                    if (!empty($arr)) {
                        echo -1;
                        return;
                    }
                    $arr = $m2->where('id=' . $id)->find();
                    if ($arr['fid'] == 0) {
                        if ($m->where('id=' . $arr['typeid'])->delete() === false) {
                            echo -1;
                            return;
                        }
                    }
                    if ($m2->where('id=' . $id)->delete() !== false) {
                        echo 1;
                    } else {
                        echo -1;
                    }
                    return;
                }
    }


    /**
     * 图册批量排序
     */
    public function imagesSetOrder()
    {
        $json = !empty($_POST['json']) ? $_POST['json'] : null;
        if ($json === null) {
            echo -1;
            return;
        } else {
            $m = M('images');
            $temp = array();

            foreach ($json as $k => $v) {
                if (in_array($v, $temp)) {
                    echo -2;
                    return;
                }
                $temp[] = $v;
            }

            foreach ($json as $k => $v) {
                $data['id'] = $k;
                $data['sort'] = $v;
                if ($m->save($data) === false) {
                    echo -1;
                    return;
                }
            }
        }
    }


    public function delResume()
    {
        $items = isset($_POST['items']) ? $_POST['items'] : null;
        if (empty($items)) {
            $rs['code'] = -1;
            $rs['msg'] = "未知的简历信息";
        } else {
            $m = new ResumeModel(1);
            $m->mdelete($items);
            $rs['code'] = 1;
        }

        echo json_encode($rs);
    }
}
?>

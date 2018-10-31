<?php

function getUserSitesArr()
{
    $arr = getAvailableSitesArr();
    $rsArr = array();
    if (session('userSiteId') == 'all') {
        $rsArr = $arr;
    } else {
        foreach ($arr as $v) {
            if (in_array($v['id'], session('userSiteId'))) {
                array_push($rsArr, $v);
            }
        }
    }
    return $rsArr;
}

function getThemeArr()
{
    $basePath = TMPL_PATH . "home";
    $rs = array();
    if (is_dir($basePath)) {
        $arr = scandir($basePath);
        foreach ($arr as $v) {
            if (is_dir($basePath . '/' . $v)) {
                if (in_array($v, array('.', '..', 'del', 'common'))) {
                    continue;
                } else {
                    $rs[$v] = $v;
                }
            } else {
                continue;
            }
        }
        return $rs;
    } else {
        return false;
    }
}

function getSoftFormType($id)
{
    $arr = C('SOFT_FORM_TYPE');
    if (!empty($arr[$id])) {
        return $arr[$id];
    } else {
        return "未知";
    }
}


function getGoodsNumByCatid($id)
{
    $m = M('goods');
    $num = $m->where("catid = $id")->count("id");
    return $num;
}


//return '[["123456","小花"],["123456","小明"]]';
function getQQList()
{
    $lineArr = explode("\n", C('JL_QQ_LIST'));
    $str = '';
    $js = "[";
    $max = count($lineArr);
    foreach ($lineArr as $k => $v) {
        $arr = explode("：", $v);
        if ($k < $max - 1)
            $str .= '["' . trim($arr[1]) . '","' . trim($arr[0]) . '"],';
        else
            $str .= '["' . trim($arr[1]) . '","' . trim($arr[0]) . '"]';
    }
    $js = $js . $str . "]";
    return $js;
}


function getCollectNodeNameById($id)
{
    $m = M('collectNode');
    $arr = $m->field('name')->where('id = ' . $id)->find();
    if (empty($arr)) {
        return "未知";
    } else {
        return $arr['name'];
    }
}


function getMemberNameById($id)
{
    $m = M('member');
    $arr = $m->field('userid')->where('id = ' . $id)->find();
    if (empty($arr)) {
        return "未知";
    } else {
        return $arr['userid'];
    }
}


function isParent($table, $tid, $field_fid = "fid")
{
    $m = M($table);
    if (empty($table) || empty($m)) {
        return false;
    } else {
        $arr = $m->field('id')->where("`$field_fid` = $tid")->select();
        if (empty($arr)) {
            return false;
        } else {
            return true;
        }
    }
}

function getAllSon($table, $tid)
{
    $m = M($table);
    if (empty($table) || empty($m)) {
        return false;
    } else {
        $tids = $m->field('id,route')->where("route like '%" . $tid . "%'")->select();
        if (empty($tids)) {
            return false;
        } else {
            foreach ($tids as $v) {
                $routeArr = explode('-', $v['route']);
                if (!in_array($tid, $routeArr)) continue;
                else $arr[] = $v['id'];
            }
            $tid = implode($arr, ',');
            return $tid;
        }
    }

}


function arrayToSelectArray($arr, $kk, $kv)
{
    if (empty($arr)) {
        return "";
    } else {
        foreach ($arr as $v) {
            $rsArr[$v[$kk]] = $v[$kv];
        }
        return $rsArr;
    }
}


function selectArrToHtmlEx($arr, $kk, $kv, $value = '')
{
    if (empty($arr)) {
        return "";
    } else {
        $html = "";
        foreach ($arr as $v) {
            if (trim($v[$kk]) == $value) $html .= "<option value='" . $v[$kk] . "' selected >" . trim($v[$kv]) . "</option>";
            else $html .= "<option value='" . $v[$kk] . "'>" . trim($v[$kv]) . "</option>";
        }
        return $html;
    }

}


function selectArrToHtml($arr, $value = '')
{
    if (empty($arr)) {
        return "";
    } else {
        $html = "";
        foreach ($arr as $k => $v) {
            if (trim($v) == $value) $html .= "<option value='" . $k . "' selected >" . trim($v) . "</option>";
            else $html .= "<option value='" . $k . "'>" . trim($v) . "</option>";
        }
        return $html;
    }

}


function goodsAttrForm($arr, $varr)
{
    if (empty($arr)) {
        return "";
    }
    $html = '';
    $i = 0;
    foreach ($arr as $v) {
        $value = '';
        foreach ($varr as $v2) {
            if ($v['id'] == $v2['attrid']) $value = trim($v2['value']);
        }

        if ($i % 2 == 0) $flag = "even";
        else $flag = '';
        switch ($v['input_type']) {
            case 0 :
                $html .= '<tr class="row ' . $flag . '"><td><label>' . $v['name'] . ':</label></td><td><input type="text"  class="control text" value="' . $value . '" name="attr_' . $v['id'] . '" ></input></td><td>&nbsp;</td></tr>';
                break;
            case 1 :
                $html .= '<tr class="row ' . $flag . '"><td><label>' . $v['name'] . ':</label></td><td><select  class="control select" value="" name="attr_' . $v['id'] . '" >' . goodsAttrSelect($v['values'], trim($value)) . '</select></td><td><input type="text" class="text addattr_' . $v['id'] . '" value="" style="float:left;margin-right:10px;width:150px;"  ></input><input type="button" class="button" value="添加" style="float:left;" onclick="addattrbtn(' . $v['id'] . ')" ></input></td></tr>';
                break;
            case 2 :
                $html .= '<tr class="row ' . $flag . '"><td><label>' . $v['name'] . ':</label></td><td><textarea  class="control textarea" name="attr_' . $v['id'] . '" >' . $value . '</textarea></td><td>&nbsp;</td></tr>';
                break;
        }
        $i++;
    }
    return $html;
}

function goodsAttrSelect($str, $value)
{
    $arr = explode("\n", $str);
    $html = '<option value="" > - 请选择 - </option>';
    foreach ($arr as $v) {
        if (trim($v) == $value) $html .= "<option value='" . trim($v) . "' selected >" . trim($v) . "</option>";
        else $html .= "<option value='" . trim($v) . "'>" . trim($v) . "</option>";
    }
    return $html;

}


/**
 * 递归建arctype栏目
 */

function recurrenceArctype($arr, $ofid = 0, $nfid = 0, $router = '0')
{
    $m = new ArctypeModel();
    $sid = getSiteId();
    $arr2 = S('channel_type_map');
    foreach ($arr as $v) {
        if ($v['fid'] == $ofid && $v['id'] != 0) {
            $date['fid'] = $nfid;
            $date['channel'] = $v['cid'];
            $date['name'] = $v['value'];
            $date['route'] = $router;
            $date['type'] = $arr2[$v['cid']];
            $date['siteid'] = $sid;
            $date['status'] = 1;

            $map = array();
            $map['fid'] = array("eq", $nfid);
            $map['siteid'] = array("eq", $sid);
            $orders = $m->field('order')->where($map)->select();
            $order = 0;
            if (!empty($orders)) {
                foreach ($orders as $vorder) {
                    if ($order > $vorder['order']) continue;
                    else {
                        $order = $vorder['order'];
                        $order++;
                    }
                }
            }
            $date['order'] = $order;

            $m->create($date);
            if ($m->add() === false) {
                return false;
            } else {
                $lastid = $m->getLastInsID();
                $nrouter = $router . "-" . $lastid;
            }

            foreach ($arr as $v2) {
                if ($v2['fid'] == $v['id']) {
                    $arr2[] = $v2;
                }
            }
            if (!empty($arr2)) {
                $rs = recurrenceArctype($arr, $v['id'], $lastid, $nrouter);
                if ($rs === false) return false;
            }
        }
    }
    return true;
}


function arctypeParse($tocid = 1)
{
    $str = file_get_contents(DATA_PATH . "arctype.txt");
    $arr = explode("\n", $str);
    foreach ($arr as $k => $v) {
        if (stripos($v, "，") !== false) {
            $tArr = explode('，', $v);
            $v = $tArr[0];
            $cid = trim($tArr[1]);
        } else {
            $cid = null;
        }
        $arctypeArr[$k]['l'] = substr_count(rtrim($v), " ");
        $arctypeArr[$k]['v'] = trim($v);
        $arctypeArr[$k]['cid'] = $cid;
    }

    array_unshift($arctypeArr, array('v' => '指定栏目', 'l' => '0', 'cid' => $tocid));
    $level = array();
    $tree = array();
    foreach ($arctypeArr as $k => $v) {
        //第一个栏目
        if ($k == 0) {
            $level[0] = $v['l'];
            $tree[$k]['id'] = $k;
            $tree[$k]['value'] = trim($v['v']);
            $tree[$k]['fid'] = -1;
            if ($arctypeArr[$k]['cid'] === null) {
                $tree[$k]['cid'] = $tocid;
            } else {
                $tree[$k]['cid'] = $tocid;
            }
        } else if ($v['l'] < $level[0]) { //比一级栏目还小，报错
            return -1;
            exit;
        } else if ($v['l'] > $level[0]) { //大于一级栏目
            $p_l = $arctypeArr[$k - 1]['l']; //上级栏目信息
            if ($v['l'] > $p_l) {
                $tree[$k]['id'] = $k;
                $tree[$k]['value'] = trim($v['v']);
                $tree[$k]['fid'] = $k - 1;
                if ($arctypeArr[$k]['cid'] === null) {
                    $tree[$k]['cid'] = $tree[$k - 1]['cid'];
                } else {
                    $tree[$k]['cid'] = $arctypeArr[$k]['cid'];
                }
            } else if ($v['l'] == $p_l) {
                $tree[$k]['id'] = $k;
                $tree[$k]['value'] = trim($v['v']);
                $tree[$k]['fid'] = $tree[$k - 1]['fid'];
                if ($arctypeArr[$k]['cid'] === null) {
                    $tree[$k]['cid'] = $tree[$tree[$k]['fid']]['cid'];
                } else {
                    $tree[$k]['cid'] = $arctypeArr[$k]['cid'];
                }
            } else if ($v['l'] < $p_l) {

                for ($i = 0; $i < $k; $i++) {
                    if ($arctypeArr[$i]['l'] == $v['l']) {
                        $tree[$k]['id'] = $k;
                        $tree[$k]['value'] = trim($v['v']);
                        $tree[$k]['fid'] = $tree[$i]['fid'];
                        if ($arctypeArr[$k]['cid'] === null) {
                            $tree[$k]['cid'] = $tree[$tree[$k]['fid']]['cid'];
                        } else {
                            $tree[$k]['cid'] = $arctypeArr[$k]['cid'];
                        }
                    }
                }
            }
        } else if ($v['l'] == $level[0]) { //也是一级栏目
            $tree[$k]['id'] = $k;
            $tree[$k]['value'] = trim($v['v']);
            $tree[$k]['fid'] = 0;
            if ($arctypeArr[$k]['cid'] === null) {
                $tree[$k]['cid'] = $tocid;
            } else {
                $tree[$k]['cid'] = $arctypeArr[$k]['cid'];
            }
        }

    }
    flushChannelTypeMap();
    return $tree;
}

//刷新内容模型，栏目类别对应表
function flushChannelTypeMap()
{
    $map = S('channel_type_map');
    if (empty($map)) {
        $m = M("Channel");
        $arr = $m->field("id,type")->select();
        if (!empty($arr)) {
            foreach ($arr as $v) {
                $map[$v['id']] = $v['type'];
            }
            S('channel_type_map', $map, 30);
        }
    }
    return;
}


//=====================================================================
//和服务器交互
/**
 * 获取欢迎致辞
 * 1:日期
 * 2:名言
 * 3:京伦最新动态
 * 4:广告
 */
function getWelcome()
{
    if (C('JL_SERVER')) {
        @$type = file_get_contents(C('JL_SERVER_HOST') . "getWelcome.php?act=gettype");
    }

    if (empty($type)) $type = 1;
    switch ($type) {
        case 1 :
            return date('Y-m-d h:i:s A', time());
            break;
        case 2 :
            return random_str();
            break;
        case 3 :
            @$data = file_get_contents(C('JL_SERVER_HOST') . "getWelcome.php?act=getdata");
            $data = !empty($data) ? $data : "京伦wcs建站系统正式上线！";
            return $data;
            break;
        case 4 :
            return "学厨艺到南翔！十万学子创辉煌！";
            break;
    }
}


/**
 * 获取head图片
 */
function getBackground($type)
{
    if (!isset($type)) return '';
    if (C('JL_SERVER')) {
        @$str = file_get_contents(C('JL_SERVER_HOST') . "getBackground.php?type=$type");
    } else {
        $str = '';
    }
    if (empty($str) || $str == false) {
        switch ($type) {
            case 'head' :
                return '__IMG__/head_bg_1.jpg';
                break;
            case 'login' :
                return "__CSS__/images/login-bg.jpg";
                break;
        }
    } else {
        //echo $str;
        return $str;
    }
}

//=====================================================================	

/**
 * 获取统一的后台position
 * array("栏目列表"=>"__GROUP__/index/show");
 */
function getPosition($arr)
{
    $index = '<a href="__GROUP__/index/welcome">后台首页</a>';
    if (empty($arr)) {
        return $index;
    }
    $PositionLine = $index;
    if (is_array($arr)) {

        $num = count($arr);
        $i = 1;
        foreach ($arr as $k => $v) {
            if ($i == $num) $class = 'class="current"'; else $class = '';
            $PositionLine .= '<div class="jt"></div><a href="' . $v . '"  ' . $class . ' >' . $k . '</a>';
            $i++;
        }
    } else {
        $PositionLine .= '<div class="jt"></div><a href="" class="current" >' . $arr . '</a>';
    }
    return $PositionLine;
}


/*
 * 扩展信息栏
 */
function getMoreInfo()
{
    $url = "http://" . C('currentSite.host') . ":" . C('currentSite.port') . C('JL_CMSPATH');
    $html = "<a href='__GROUP__/public/logout' target='_top'>注销</a><a href=" . $url . " target='_blank' >前台</a><a href='__GROUP__/index/index' target='_blank'>后台</a><a href='javascript:void(0);' id='hideleft'>隐藏菜单</a><!--a href='javascript:void(0);' id='hideup'>隐藏导航</a-->";
    return $html;
}


/**
 * 后台排序解析公共函数
 */
function orderByParse($orderBy)
{
    if (empty($orderBy)) return array();
    else {
        $orderByArr = explode('_', $orderBy);
        $map = array();
        foreach ($orderByArr as $k => $v) {
            $map[$k] = $v;
        }
        return $map;
    }
}


/**
 * 生成optons
 */
function getOptions($arr, $default)
{
    if (empty($arr)) return null;
    else {
        $str = '';
        foreach ($arr as $k => $v) {
            if (isset($default) && $k == $default) $selected = ' selected style="background:#DBDBDB;" '; else $selected = '';
            $str .= "<option value='$k' $selected >$v</option>";
        }
        return $str;
    }
}


/**
 * 通过正则递归查找符合条件的文件
 * @param unknown_type $dir
 * @param unknown_type $regular
 * @param unknown_type $suffixal
 * @param unknown_type $searchArr
 * @return boolean
 */
function search($dir, $regular = '//', $suffixal = 'php', &$searchArr)
{
    //目录转化为小写 将\转成/
    $dir = str_replace("\\", '/', $dir);
	// 判断服务器类型，win 服务器 路径转换成小写
	 if(strtolower(substr(PHP_OS, 0, 3)) == 'win'){
       $dir = strtolower($dir);
     }
    //判断是否有改目录
    if (!is_dir($dir)) {
        return $dir . "目录名不对！";
    }

    $file_name = scandir($dir);
    foreach ($file_name as $v) {
        if (is_dir($dir . '/' . $v) && substr_count($v, '.') == 0) {
            search($dir . '/' . $v, $regular, $suffixal, $searchArr);
        } else {
            $nsuff = strrchr($v, '.');
            if ($nsuff == '.' . $suffixal && preg_match($regular, $v)) {
                $searchArr[]['name'] = $v;
            }
        }
    }
    return true;
}

/**
 * 自动根据field字段属性生成表单已经初始化默认值
 * @param unknown_type $obj
 * @param unknown_type $fields
 * @param unknown_type $odata
 */
function AutoForm($obj, $fields, $odata = null)
{
    $form = $obj;

    foreach ($fields as $v) {
        $rule = array();
        if ($v['must'] == 1) {
            $rule['required'] = array('error', '该项为必填项！');
        }
        if($v['type'] == 'datetime'){
            $dateconfig = $v['default'] ? $v['default'] : "{dateFmt:'yyyy-MM-dd'}";
            $v['default'] = stristr($dateconfig,'HH:mm:ss') ? date("Y-m-d H:i:s", time()) : date("Y-m-d", time());
        }
        if($v['type'] == 'htmltext'){
            $editor_theme = $v['default'] ? $v['default'] : 'normal';
            $v['default'] = in_array($v['default'],array('simple','normal','advanced','config')) ? "" : $v['default'];
        }
        if (isset($odata) && !empty($odata)) {
            $defaultArr = null;
            if (!empty($odata[$v['name']])) {
                //$defaultArr = null;
                if ($v['type'] == 'checkbox') {
                    $defaultArr = explode(',', $odata[$v['name']]);
                } else if ($v['type'] == 'select' || $v['type'] == 'radio') {
                    $arr = explode(',', $v['default']);
                    array_unshift($arr, "");
                    unset($arr[0]);
                    foreach ($arr as $k3 => $v3) {
                        if ($odata[$v['name']] == $v3) {
                            $defaultArr = $k3;
                        }
                    }
                } else if ($v['type'] == 'datetime') {
                    $v['default'] = stristr($dateconfig,'HH:mm:ss') ? date("Y-m-d H:i:s", $odata[$v['name']]) : date("Y-m-d", $odata[$v['name']]);
                } else if ($v['type'] == 'htmltext') {
                    $v['default'] = htmlspecialchars($odata[$v['name']]);
                } else {
                    $v['default'] = $odata[$v['name']];
                }
            }
        }

        switch ($v['type']) {
            case 'text' :
            case 'textchar' :
            {
                $form->add('label', 'label_' . $v['name'], $v['name'], $v['intro'] . ':');
                $obj = & $form->add('text', $v['name'], $v['default'], array("style" => "width:" . ($v['size'] * 3) . "px;"));
                $obj->set_rule($rule);
                break;
            }
            case 'hidden' :
            {
                $obj = & $form->add('hidden', $v['name'], $v['default']);
                $obj->set_rule($rule);
                break;
            }
            case 'int' :
            {
                $form->add('label', 'label_' . $v['name'], $v['name'], $v['intro'] . ':');
                $obj = & $form->add('text', $v['name'], $v['default']);
                $rule['digits'] = array('', 'error', '只能输入"数字"！');
                /* $obj->set_rule(array(
                    'digits' => array('','error','只能输入"数字"！')
                ));*/
                $obj->set_rule($rule);
                break;
            }
            case 'float' :
            {
                $form->add('label', 'label_' . $v['name'], $v['name'], $v['intro'] . ':');
                $obj = & $form->add('text', $v['name'], $v['default']);
                /*$obj->set_rule(array(
                    'digits' => array('.','error','只能输入"数字"和 "小数点"！')
                ));*/
                $rule['digits'] = array('.', 'error', '只能输入"数字"和 "小数点"！');
                $obj->set_rule($rule);
                break;
            }
            case 'datetime' :
            {
                $form->add('label', 'label_' . $v['name'], $v['name'], $v['intro'] . ':');
                $form->add('datetime', $v['name'], $v['default'], array('style' => 'width:130px;', 'config' => $dateconfig));
                break;
            }
            case 'multitext' :
            {
                $form->add('label', 'label_' . $v['name'], $v['name'], $v['intro'] . ':');
                $obj = & $form->add('textarea', $v['name'], $v['default']);
                $obj->set_rule($rule);
                break;
            }
            case 'htmltext' :
            {
                $form->add('label', 'label_' . $v['name'], $v['name'], $v['intro'] . ':');
                $obj = & $form->add('kind', $v['name'], $v['default'], array('style' => 'width:800px;height:400px;', 'theme' => $editor_theme));
                $obj->set_rule($rule);
                break;
            }
            case 'img' :
            {
                //$v['default'] = !empty($v['default'])?$v['default']:'__IMG__/default.gif';
                $form->add('label', 'label_' . $v['name'], $v['name'], $v['intro'] . ':');
                $obj = & $form->add('kimg', $v['name'], $v['default'], array("style" => 'width:400px;'));
                $obj->set_rule($rule);
                break;
            }
            case 'media' :
            {
                $form->add('label', 'label_' . $v['name'], $v['name'], $v['intro'] . ':');
                $obj = & $form->add('Kfile', $v['name'], $v['default']);
                $obj->set_rule($rule);
                break;
            }
            case 'select' :
            {
                if (preg_match("#^__.*__$#i", $v['default'])) {
                    import('@.Class.AutoFormHelper');
                    $arr = AutoFormHelper::getOptions($v['default']);
                    $defaultArr = $odata[$v['name']];
                } else {
                    $arr = explode(',', $v['default']);
                    array_unshift($arr, "");
                    unset($arr[0]);
                }
                $form->add('label', 'label_' . $v['name'], $v['name'], $v['intro'] . ':');
                $obj = & $form->add('select', $v['name'], $defaultArr);
                $obj->add_options($arr);
                $obj->set_rule($rule);
                break;
            }
            case 'radio' :
            {
                if (preg_match("#^__.*__$#i", $v['default'])) {
                    import('@.Class.AutoFormHelper');
                    $arr = AutoFormHelper::getOptions($v['default']);
                    $defaultArr = $odata[$v['name']];
                } else {
                    $arr = explode(',', $v['default']);
                    array_unshift($arr, "");
                    unset($arr[0]);
                }
                $form->add('label', 'label_' . $v['name'], $v['name'], $v['intro'] . ':');
                $obj = & $form->add('radios', $v['name'], $arr, $defaultArr);
                $obj->set_rule($rule);
                break;
            }
            case 'checkbox' :
            {
                if (preg_match("#^__.*__$#i", $v['default'])) {
                    import('@.Class.AutoFormHelper');
                    $arr2 = AutoFormHelper::getOptions($v['default']);
                    //$defaultArr = $odata[$v['name']];
                } else {
                    $arr = explode(',', $v['default']);
                    foreach ($arr as $v4) {
                        $arr2[$v4] = $v4;
                    }
                }
                $form->add('hidden', $v['name'], '');
                $form->add('label', 'label_' . $v['name'], $v['name'], $v['intro'] . ':');
                $obj = & $form->add('checkboxes', $v['name'] . '[]', $arr2, $defaultArr);
                $obj->set_rule($rule);
                break;
            }
            case 'stepselect' :
            {
                $m = M('wxldtype');
                $arr = $m->where("typename='" . $v['default'] . "'")->find();
                $m2 = M('wxld');
                $type = $m2->where('fid=0 and typeid=' . $arr['id'])->find();
                $viewValue = $odata[$v['name']];

                if (empty($odata[$v['name']])) {
                    $wxldval = null;
                } else {
                    $wxldval = $m2->field('id')->where("typename='" . $odata[$v['name']] . "'")->find();
					/*修改内容时，获取父ID，获取当前 select  option选择 开始*/
					if($type['id']==''){
					 $arrfid= $m2->field('fid')->where("typename='" . $odata[$v['name']] . "'")->find();
					 $type['id']=$arrfid['fid'];	
					}
					/*修改内容时，获取父ID，获取当前 select  option选择 结束*/
                    if (empty($wxldval)) {
                        $wxldval = null;
                    } else {
                        $wxldval = $wxldval['id'];
                    }
                }
                $form->add('wxld', $v['name'], $wxldval, array('ld_nid' => $type['id'], 'intro' => $v['intro'], 'viewValue' => $viewValue));
                break;
            }
			case 'region' :
            {
                $m = M('region');
				
                $arr = $m->where("name='" . $v['default'] . "'")->find();
                $viewValue = $odata[$v['name']];
				
				if (empty($arr)) {
                    $arr['id'] = 1;   //  代表数据库表 中国 的ID
                }

                if (empty($odata[$v['name']])) {
                    $regionval = null;
                } else {
                    $regionval=1;
                }
				
				$form->add('region', $v['name'], $regionval, array('ld_nid' => $arr['id'], 'intro' => $v['intro'], 'viewValue' => $viewValue));
                break;
            }
        }
    }
    return;
}


/**
 * 获得字段创建信息
 *
 * @access    public
 * @param     string $dtype 字段类型
 * @param     string $fieldname 字段名称
 * @param     string $dfvalue 默认值
 * @param     string $mxlen 最大字符长度
 * @return    array
 */
function GetFieldMake($dtype, $fieldname, $dfvalue, $mxlen)
{
    $fields = array();
    if ($dtype == "int" || $dtype == "datetime") {
        if ($dfvalue == "" || preg_match("#[^0-9-]#", $dfvalue)) {
            $dfvalue = 0;
        }
        $fields[0] = " `$fieldname` int(11) NOT NULL default '$dfvalue';";
        $fields[1] = "int(11)";
    } else if ($dtype == "stepselect") {
        if ($dfvalue == "" || preg_match("#[^0-9\.-]#", $dfvalue)) {
            $dfvalue = 0;
        }
        $fields[0] = " `$fieldname` char(20) NOT NULL default '$dfvalue';";
        $fields[1] = "char(20)";
    } else if ($dtype == "float") {
        if ($dfvalue == "" || preg_match("#[^0-9\.-]#", $dfvalue)) {
            $dfvalue = 0;
        }
        $fields[0] = " `$fieldname` float NOT NULL default '$dfvalue';";
        $fields[1] = "float";
    } else if ($dtype == "img" || $dtype == "media" || $dtype == "addon" || $dtype == "imgfile") {
        if (empty($dfvalue)) $dfvalue = '';
        if ($mxlen == "") $mxlen = 200;
        if ($mxlen > 255) $mxlen = 100;

        $fields[0] = " `$fieldname` varchar($mxlen) NOT NULL default '$dfvalue';";
        $fields[1] = "varchar($mxlen)";
    } else if ($dtype == "multitext" || $dtype == "htmltext") {
        $fields[0] = " `$fieldname` mediumtext;";
        $fields[1] = "mediumtext";
    } else if ($dtype == "textdata") {
        if (empty($dfvalue)) $dfvalue = '';

        $fields[0] = " `$fieldname` varchar(100) NOT NULL default '';";
        $fields[1] = "varchar(100)";
    } else if ($dtype == "textchar" || $dtype == "hidden") {
        if (empty($dfvalue)) $dfvalue = '';

        $fields[0] = " `$fieldname` char(100) NOT NULL default '$dfvalue';";
        $fields[1] = "char(100)";
    } else if ($dtype == "checkbox") {
        if (preg_match("#^__.*__$#i", $dfvalue)) {
            $fields[0] = " `$fieldname` char(255) NOT NULL default '';";
            $fields[1] = "char(255)";
        } else {
            $dfvalue = str_replace(',', "','", $dfvalue);
            $dfvalue = "'" . $dfvalue . "'";
            $fields[0] = " `$fieldname` SET($dfvalue) NULL;";
            $fields[1] = "SET($dfvalue)";
        }
    } else if ($dtype == "select" || $dtype == "radio") {
        if (preg_match("#^__.*__$#i", $dfvalue)) {
            $fields[0] = " `$fieldname` char(100) NOT NULL default '';";
            $fields[1] = "char(100)";
        } else {
            $dfvalue = str_replace(',', "','", $dfvalue);
            $dfvalue = "'" . $dfvalue . "'";
            $fields[0] = " `$fieldname` enum($dfvalue) NULL;";
            $fields[1] = "enum($dfvalue)";
        }
    } else {
        if (empty($dfvalue)) {
            $dfvalue = '';
        }
        if (empty($mxlen)) {
            $mxlen = 100;
        }
        if ($mxlen > 255) {
            $mxlen = 250;
        }
        $fields[0] = " `$fieldname` varchar($mxlen) NOT NULL default '$dfvalue';";
        $fields[1] = "varchar($mxlen)";
    }
    return $fields;
}


/**
 * 遍历一个目录（自写扩展）
 * path：指定要遍历的目录，默认当前目录。
 * extend：指定查找指定后缀的文件，默认全部显示。需要显示多个用array的形式。
 * callback：指定回调函数，传入的是数组，参数一是目录，参数二是文件名。
 */
function traverse($path = '.', $extend = 'all', $callback = 'print_r')
{
    $current_dir = opendir($path);
    while (($file = readdir($current_dir)) !== false) {
        $sub_dir = $path . DIRECTORY_SEPARATOR . $file;
        if ($file == '.' || $file == '..') {
            continue;
        } else if (is_dir($sub_dir)) {
            //echo 'Directory ' . $file . ':<br/>';
            traverse($sub_dir, $extend, $callback);
        } else {
            if (!is_array($extend)) {
                if ($extend == 'all') {
                    $callback(array($path, $file));
                    //echo 'File in Directory ' . $path . ': ' . $file . '<br>';
                } else if (extend($path . $file) === $extend) {
                    $callback(array($path, $file));
                    //echo 'File in Directory ' . $path . ': ' . $file . '<br>';
                }
            } else {
                if (in_array(extend($path . $file), $extend)) {
                    $callback(array($path, $file));
                    //echo 'File in Directory ' . $path . ': ' . $file . '<br>';
                }
            }
        }
    }
}


function fileHashToDb($arr)
{
    $dir = str_replace(APP_REAL_PATH, '', strtolower(str_replace("\\", '/', $arr[0])));
    $data['file'] = strtolower($arr[1]);
    //$data['path'] = $dir;
    $data['realfile'] = $dir . '/' . $arr[1];
    $data['hash'] = hash_file('md5', APP_REAL_PATH . $data['realfile']);
    $m = M('Filehash');
    //$oldArr = $m->field('realfile')->select();
    $m->create($data);
    $arr = $m->where("`realfile`='$data[realfile]'")->find();
    if (empty($arr)) {
        if ($m->add() === false) {
            return false;
        }
    } else {
        if ($arr['hash'] != $data['hash']) {
            $updatedata['hash'] = $data['hash'];
            $updatedata['realfile'] = $data['realfile'];
            $m->create($updatedata);
            if ($m->save() === false) {
                return false;
            }
        }
    }
    return true;
}

function checkLocalDbfileHash($arr)
{
    $dir = str_replace(APP_REAL_PATH, '', strtolower(str_replace("\\", '/', $arr[0])));
    $data = array();
    $data['file'] = strtolower($arr[1]);
    $data['realfile'] = $dir . '/' . $arr[1];
    $data['hash'] = hash_file('md5', APP_REAL_PATH . $data['realfile']);
    $m = M('checkinfo');
    $m2 = M('Filehash');
    $arr = $m2->where("`realfile`='$data[realfile]'")->find();
    if (empty($arr)) {
        $data['type'] = 3;
        $data['time'] = time();
        $m->create($data);
        if ($m->add() === false) {
            return false;
        }
    } else {
        if ($arr['hash'] != $data['hash']) {
            $data['type'] = 1;
            $data['time'] = time();
            $m->create($data);
            if ($m->add() === false) {
                return false;
            }
        }
    }
    return true;
}


/**
 *自动获取模板文件
 */
function getTpl($type = '', $path = 'templates')
{
    $theme = C('SYS_DEFAULT_THEME');
    $path = TMPL_PATH . 'home/' . $theme . '/';
    //die($path);
    $dir = opendir($path);
    $arr = array('index_', 'list_', 'content_', 'cont_');
    if (!in_array($type . '_', $arr)) {
        return;
    } else {
        while (($file = readdir($dir)) != false) {
            if (!is_dir($file) && is_file($path . $file)) {
                if (extend($path . $file) == 'html') {
                    $arr = explode('_', $file);
                    if ($arr[0] == $type)
                        $rs[$path . $file] = $file;
                    else continue;
                }
            }
        }
        if (empty($rs)) $rs = array();
        return $rs;
    }
}

//递归删除文件夹
function mydel($jia, $self = false)
{
    $jia = rtrim($jia, "/");
    $dir = opendir($jia);
    while ($f = readdir($dir)) {
        if ($f != '.' && $f != '..') {

            if (is_file($jia . DIRECTORY_SEPARATOR . $f)) { //是文件
                //echo '文件 : '.$jia.DIRECTORY_SEPARATOR.$f.'<BR/>';
                @unlink($jia . DIRECTORY_SEPARATOR . $f);
            }

            if (is_dir($jia . DIRECTORY_SEPARATOR . $f)) { //是文件夹
                //echo '文件夹 : '.$jia.DIRECTORY_SEPARATOR.$f.'<BR/>';
                mydel($jia . DIRECTORY_SEPARATOR . $f);
                @rmdir($jia . DIRECTORY_SEPARATOR . $f);
            }
        }
    }
    closedir($dir);
    if ($self) {
        @rmdir($jia);
    }
}
?>

<?php
//获取广告函数
function getAd($name)
{
    $tid = $_GET['tid'];
    $m = M('ad');
    $map['name'] = array('eq', $name);
    $map['status'] = array('eq', 1);
    $arr = $m->where($map)->select();
    $adsArr = null;
    $adsArr = $arr[0];
    if (!empty($arr)) {
        if (!empty($tid)) {
            foreach ($arr as $v) {
                if ($v['arctype'] == $tid) {
                    $adsArr = $v;
                    break;
                }
            }
        }

        //过期判断
        if ('0000-00-00' == $adsArr['overdue'] || empty($adsArr['overdue'])) {
            $html = $adsArr['html'];
        } else if (time() > strtotime(date($adsArr['overdue']))) {
            $html = $adsArr['overduehtml'];
        } else {
            $html = $adsArr['html'];
        }

        if (empty($adsArr['url'])) {
            return $html;
        } else {
            return "<a href='$adsArr[url]' target='_blank' >" . $html . "</a>";
        }
    }
}


/**
 * 商城相关内容 开始
 */

//订单状态
function orderStatusAnalyse($mid)
{
    $m = M('orderInfo');
    $orderArr = $m->field('id,orderstatus,shippingstatus,paystatus')->where("mid = $mid")->select();
    if (empty($orderArr)) {
        return null;
    } else {
        // 1、等待付款订单数：2、 已经付款订单数：3、已完成订单数：4、已取消订单数：0、订单总数
        $osaArr[1] = 0;
        $osaArr[2] = 0;
        $osaArr[3] = 0;
        $osaArr[4] = 0;
        $osaArr[0] = 0;
        foreach ($orderArr as $v) {
            if ($v['paystatus'] == 1 && $v['orderstatus'] == 1) $osaArr[1]++;
            if ($v['orderstatus'] == 1 && $v['paystatus'] == 2) $osaArr[2]++;
            if ($v['orderstatus'] == 3) $osaArr[3]++;
            if ($v['orderstatus'] == 2) $osaArr[4]++;
            $osaArr[0]++;
        }
        return $osaArr;
    }
}

function fontRender($str, $cls)
{
    return "<span class='$cls'>$str</span>";
}

//解析订单状态
function parseOrderStatus($order_status, $shipping_status, $pay_status, $html = true)
{
    if (isset($order_status) && isset($shipping_status) && isset($pay_status)) {
        $rs = '';
        switch ($pay_status) {
            case 1 :
                $rs .= "未付款";
                break;
            case 2 :
                $rs .= "已付款";
                break;
            case 3 :
                $rs .= "申请退款";
                break;
            case 4 :
                $rs .= "已退款";
                break;
            default :
                $rs .= "未知";
        }
        $rs .= ' ';
        switch ($shipping_status) {
            case 1 :
                $rs .= "未发货";
                break;
            case 2 :
                $rs .= "已发货";
                break;
            case 3 :
                $rs .= "已退货";
                break;
            default :
                $rs .= "未知";
        }

        //1:进行中、2:交易关闭、3：交易完成
        switch ($order_status) {
            case 1 :
                break;
            case 2 :
                if ($html) $rs = fontRender($rs . " 交易关闭", 'color_red'); else $rs = $rs . " 交易关闭";
                break;
            case 3 :
                if ($html) $rs = fontRender("交易完成", 'color_green'); else $rs = "交易完成";
                break;
            default :
                $rs .= "未知";
        }
        return $rs;
    } else {
        return "状态有误！";
    }
}

//运费计算
function shippingPriceCount($oid, $weight)
{
    $rsPrice = 0;
    $headWeight = C('SYS_SHIPPING_HEAD_WEIGHT');
    $rtype = C('SYS_SHOP_DEFAULT_CENTER');
    $m = M('orderInfo');
    $orderArr = $m->field('stype,province')->where('id=' . $oid)->find();
    if (empty($orderArr)) {
        return -1;
    } else {
        $stype = $orderArr['stype'];
        $toProvince = $orderArr['province'];
    }
    $m2 = M('shipping');
    $srule = $m2->where("rtype = $rtype and stype = $stype and regionid = $toProvince")->find();
    if (!empty($srule)) {
        $basePrice = $srule['baseprice'];
        $overweightPrice = $srule['overweight'];
        $freePrice = !empty($srule['freeprice']) ? $srule['freeprice'] : 0;

        if ($weight <= $headWeight) {
            $rsPrice = $basePrice;
        } else if ($weight >= $freePrice) {
            $rsPrice = 0;
        } else {
            $moreWeight = $weight - $headWeight;
            $morePrice = (intval($moreWeight / C('SYS_SHIPPING_WEIGHT_UNIT')) + 1) * $overweightPrice;
            $rsPrice = $basePrice + $morePrice;
        }
        return $rsPrice;
    } else {
        $commonRule = $m2->where("rtype = $rtype and stype = $stype and regionid = 0")->find();
        if (empty($commonRule)) {
            return -2;
        } else {
            $basePrice = $commonRule['baseprice'];
            $overweightPrice = $commonRule['overweight'];
            $freePrice = !empty($commonRule['freeprice']) ? $commonRule['freeprice'] : 0;

            if ($weight <= $headWeight) {
                $rsPrice = $basePrice;
            } else if ($weight >= $freePrice) {
                $rsPrice = 0;
            } else {
                $moreWeight = $weight - $headWeight;
                $morePrice = (intval($moreWeight / C('SYS_SHIPPING_WEIGHT_UNIT')) + 1) * $overweightPrice;
                $rsPrice = $basePrice + $morePrice;
            }
            return $rsPrice;
        }
    }
}

//区域名称获取（收货地址用到）
function getRegionNameById($id)
{
    $data = S('regionArr');
    if (empty($data)) {
        $m = M('region');
        $arr = $m->select();
        $regionArr[0] = "所有地区";
        foreach ($arr as $v) {
            $regionArr[$v['id']] = $v['name'];
        }
        S('regionArr', $regionArr);
        return $regionArr[$id];
    } else {
        return $data[$id];
    }
}

function goodstypeAttrCount($goodstype_id)
{
    $m = M('attribute');
    $num = $m->where('fid =' . $goodstype_id)->count();
    if (empty($num)) {
        return "没有";
    } else {
        return $num;
    }
}

function getGoodsTypeName($goodstype_id)
{
    $m = M('goodstype');
    $num = $m->where('id =' . $goodstype_id)->find();
    if (empty($num)) {
        return "未知";
    } else {
        return $num['name'];
    }
}


function getCategoryById($id)
{
    $m = M('category');
    $num = $m->field('name')->where('id =' . $id)->find();
    if (empty($num)) {
        return "未知";
    } else {
        return $num['name'];
    }
}

function attrInputType($type)
{
    switch ($type) {
        case 0 :
            return "单行输入";
            break;
        case 1 :
            return "选择输入";
            break;
        case 2 :
            return "多行输入";
            break;
        default:
            return "未知";
    }
}

function goodsYesOrNo($type, $status)
{
    switch ($type) {
        case 1 :
            switch ($status) {
                case 0 :
                    return "no";
                    break;
                case 1 :
                    return "yes";
                    break;
            }
            break;
        case 2 :
            switch ($status) {
                case 0 :
                    return "no";
                    break;
                case 1 :
                    return "yes";
                    break;
            }
            break;
        case 3 :
            switch ($status) {
                case 0 :
                    return "no";
                    break;
                case 1 :
                    return "yes";
                    break;
            }
            break;
        case 4 :
            switch ($status) {
                case 0 :
                    return "no";
                    break;
                case 1 :
                    return "yes";
                    break;
            }
            break;
        case 5 :
            switch ($status) {
                case 0 :
                    return "no";
                    break;
                case 1 :
                    return "yes";
                    break;
            }
            break;
        default:
            return "";
            break;
    }
}

function formStatusYesOrNo($status)
{
    switch ($status) {
        case 0 :
            return "no";
            break;
        case 1 :
            return "yes";
            break;
    }
    return "";
}

function ispay($d)
{
    if ($d == 1) {
        return "已付款";
    } else {
        return "未付款";
    }
}

/**
 * 商城相关内容 结束
 * ==========================================================================================*/


/**
 * 为模板引擎调用的函数方法开始
 */
function adminUrl()
{
    return C('JL_CMSPATH') . "/admin";
}

function indexUrl($siteid = null)
{
    if (C('JL_HTML_CACHE')) {
        return __ROOT__ . '/index.html';
    } else {
        if (!empty($siteid)) {
            $m = M("Sites");
            $siteArr = $m->field("id,host,port")->where("id=" . $siteid)->find();
            $host = $siteArr['host'] . ":" . $siteArr['port'];
        } else {
            $host = C('currentSite.host') . ":" . C('currentSite.port');
        }
        return "http://" . $host . C('JL_CMSPATH');
    }

}

function contentUrl($aid, $cid = false, $group = null)
{
    if (!isset($aid)) return '';

    if (!empty($group)) {
        $group = $group . "/";
    }
    if ($cid === false) {
        if (C('JL_HTML_CACHE')) {
            return __ROOT__ . '/' . HTML_PATH . 'content/' . $aid . '.html';
        } else {
            return U($group . 'index/view?aid=' . $aid);
        }
    } else {
        if (C('JL_HTML_CACHE')) {
            return __ROOT__ . '/' . HTML_PATH . 'extcontent/' . $aid . '.html';
        } else {
            return U($group . 'index/extview?aid=' . $aid . '&cid=' . $cid);
        }
    }

}

function listUrl($tid, $group = null, $siteid = null)
{
    if (!isset($tid)) return '';
    if (is_numeric($tid)) {
        $tid = $tid;
    } else {
        $m = M("Arctype");
        $sid = getSiteId();
        $map['siteid'] = array("eq", $sid);
        $map['class'] = array("eq", $tid);
        $tid = $m->where($map)->getField("id");
    }

    $host = "";
    if (!empty($siteid)) {
        $m2 = M("Sites");
        $siteArr = $m2->field("id,host,port")->where("id=" . $siteid)->find();
        if (!empty($siteArr)) {
            $host = "http://" . $siteArr['host'] . ":" . $siteArr['port'];
        }
    }

    if (C('JL_HTML_CACHE')) {
        return __ROOT__ . '/' . HTML_PATH . 'list/' . Pinyin(getArctypeName($tid), 1) . '.html';
    } else {
        if (!empty($group)) {
            $group = $group . "/";
        }
        return $host . U($group . 'index/show?tid=' . $tid);
    }
}

function pageUrl($parameter, $group = null)
{
    if (!isset($parameter)) return '';
    //todo 静态化分页
    if (C('JL_HTML_CACHE')) {
        $_SESSION['scnowPage']="";	
		$sc=explode("&",$parameter);
		$sctid=explode("=",$sc[0]);
		$scp=explode("=",$sc[1]);
		if($scp[1]>1){
		 $scpage="_".$scp[1];
		}else{
	     $scpage="";
		}
        return __ROOT__.'/'.HTML_PATH.'list/'.Pinyin(getArctypeName($sctid[1]),1).$scpage.'.html';
    } else {
        if (!empty($group)) {
            $group = $group . "/";
        }
        return U($group . 'index/show' . $parameter);
    }
}

//todo:代码丑,待优化
function parseArctypeUrl($url)
{
    $rsUrl = '';
    //指定了URL则跳转到指定URL
    if (preg_match('#^http[s]?://.*#is', $url)) {
        $rsUrl = $url;
    } else if (preg_match('#^class:(.*)#is', $url, $arr)) {
        $m = M("Arctype");
        $sid = getSiteId();
        $map['siteid'] = array("eq", $sid);
        $map['class'] = array("eq", trim($arr[1]));
        $tid = $m->where($map)->getField("id");
        if (!empty($tid)) {
            $rsUrl = listUrl($tid);
        }
    } else if (preg_match('#^tid:(\d+)$#is', $url, $arr)) {
        $rsUrl = listUrl($arr[1]);
    } else if (preg_match('#^aid:(\d+)$#is', $url, $arr)) {
        $rsUrl = contentUrl($arr[1]);
    } else if (preg_match('#^aid:(\d+)cid:(\d+)#is', $url, $arr)) {
        $rsUrl = contentUrl($arr[1], $arr[2]);
    } else {
        $rsUrl = "";
    }
    return $rsUrl;
}


function siteUrl($sid)
{
    if (empty($sid)) {
        return "#";
    } else {
      $info = getSiteInfoBySid($sid);
	    $hosts=explode('|',$info['host']);
        $host = $hosts[0] . ":" . $info['port'];
        $charlist = " /\t\n\r\0\x0B";
        $cmsPath = trim(C('JL_CMSPATH'), $charlist);
        if ($cmsPath) {
            $url = "http://" . $host . '/' . $cmsPath . '/';
        } else {
            $url = "http://" . $host . '/';
        }

        if (isset($info['dir']) && trim($info['dir'], $charlist) !== '') {
            return $url . trim($info['dir'], $charlist) . '/';
        } else {
            return $url;
        }
    }
}


/**
 * 为模板引擎调用的函数方法 结束
 * ==================================================
 */


//启用状态
function status($d)
{
    if ($d == 1) {
        return "启用";
    } else {
        return "禁用";
    }
}

//显示状态
function display($d)
{
    if ($d == 1) {
        return "显示";
    } else {
        return "隐藏";
    }
}

//审核状态
function checkStatus($d)
{
    switch ($d) {
        case 0 :
            $rs = "未审核";
            break;
        case 1 :
            $rs = "已审核";
            break;
        case 3 :
            $rs = "测试";
            break;
        case -1 :
            $rs = "回收站";
            break;
        default:
            $rs = "未知";
            break;
    }
    return $rs;
}

//会员
function belong($fid)
{
    if ($fid == 0) {
        return "无企业";
    } else {
        $m = M('member');
        $arr = $m->where('id=' . $fid)->find();
        if (empty($arr['uname'])) return "未知";
        else return $arr['uname'];
    }
}

//通过栏目id获取栏目名称
function getArctypeName($tid, $sid = null)
{
    if ($tid == 0) return "所有栏目";
    import('@.Class.SuperClassify');
    $m = new ArctypeModel();
    $nav = $m->getSuperTree($sid);
    if (empty($nav)) return "树未实例化";
    else return $nav->nodes[$nav->id_name[$tid]]['name'];

}

//广告过期
function overdue($t)
{
    if ($t == 0) {
        return "永不过期";
    } else {
        return date('Y-m-d', $t);
    }
}

/**
 * 获取内容模型名称
 */
function getChannelName($cid)
{
    $m = new ChannelModel();
    if (($name = $m->getChannelName($cid)) !== false) return $name;
    else return "未知";
}

/**
 * 解析内容flag属性
 */
function parseFlag($flag)
{
    $flags = C('SYS_FLAG_ARRAY');
    $rs = '';
    if (stripos($flag, ',') !== false) {
        $flag = explode(',', $flag);
        foreach ($flag as $v) {
            $rs .= "<span style='color:#0FB1D2;font-size:10px;'>" . $flags[$v] . "</span> ";
        }
    } else {
        $rs = "<span style='color:#0FB1D2;font-size:10px;'>" . $flags[$flag] . "</span>";
    }
    return $rs;
}

/*
 * 通过用户ID查询改用户的组
 */
function getUserType($type)
{
    $m = M('role');
    $arr = $m->field('name')->where('id=' . $type)->find();
    if (empty($arr)) return "未知";
    else return $arr['name'];
}

/*
 * 系统模型还是扩展模型
 */
function issystem($type)
{
    if (empty($type)) return "未知";
    else {
        switch ($type) {
            case 1 :
                $rs = "系统";
                break;
            case 2 :
                $rs = "扩展";
                break;
            default:
                $rs = "未知";
                break;
        }
        return $rs;
    }
}

/**
 * 是否包含主表
 */
function addtable($type)
{
    if (empty($type)) return "未知";
    else {
        switch ($type) {
            case 1 :
                $rs = "包含";
                break;
            case 2 :
                $rs = "不含";
                break;
            case 3 :
                $rs = "单页";
                break;
            default:
                $rs = "未知";
                break;
        }
        return $rs;
    }
}

/*
 ============================================
 函数名称：Html2Text
 函数功能：HTML转文本
 传入参数：
    1、$str：指定要替换的内容
    2、$r： 选择类型
 返回结果：字符串
 ============================================
*/
function Html2Text($str, $r = 0)
{
    if ($r == 0) {
        return SpHtml2Text($str);
    } else {
        $str = SpHtml2Text(stripslashes($str));
        return addslashes($str);
    }
}

function SpHtml2Text($str)
{
    $str = preg_replace("/<sty(.*)\\/style>|<scr(.*)\\/script>|<!--(.*)-->/isU", "", $str);
    $alltext = "";
    $start = 1;
    for ($i = 0; $i < strlen($str); $i++) {
        if ($start == 0 && $str[$i] == ">") {
            $start = 1;
        } else if ($start == 1) {
            if ($str[$i] == "<") {
                $start = 0;
                $alltext .= " ";
            } else if (ord($str[$i]) > 31) {
                $alltext .= $str[$i];
            }
        }
    }
    $alltext = str_replace("　", " ", $alltext);
    $alltext = preg_replace("/&([^;&]*)(;|&)/", "", $alltext);
    $alltext = preg_replace("/[ ]+/s", " ", $alltext);
    return $alltext;
}
?>
<?php

/**
 * file: ArctypeModel.class.php
 * intro: 文章主表模型类
 * @date: 2012-8-22
 * @author: LHY
 * @version: 2.0
 */
import('@.Class.TagLibHelper');
class ArctypeModel extends Model
{
    /**
     * 为arctype标签提供支持
     * @param unknown_type $arg
     */
    public function getData($arg, $variables = null)
    {
        $args = unserialize($arg);
        if ($variables != null) {
            $variables = unserialize($variables);
            if (!empty($variables)) {
                $args = array_merge($args, $variables);
            }
        }

        $siteid = getSiteId();
        if (empty($siteid)) {
            return;
        }

        $map = array();
        $class = trim($args['class']);
        $tid = ($args['typeid'] !== null) ? $args['typeid'] : $_GET['tid'];
        if (($tid === null || $tid == '') && empty($class)) die("typeid 无效！501");

        if (is_null($args['typeid']) && !empty($class)) {
            $tid = $this->where("`class` = '$class' and siteid = $siteid ")->getField('id');
        }

        //brother: 列出该栏目的兄弟栏目 self:列出自己 son：列出自己的子栏目
        switch ($args['type']) {
            case 'self' :
            {
                $map['id'] = array('eq', $tid);
                break;
            }
            case 'brother' :
            {
                $tid = $this->where('id=' . $tid)->getField('fid');
                $map['fid'] = array('eq', $tid);
                $map['display'] = array('in', '1,3');
                break;
            }
            case 'son' :
            {
                $map['fid'] = array('eq', $tid);
                $map['display'] = array('in', '1,3');
                break;
            }
            case 'class':
            {
                $map['class'] = array('eq', $class);
                break;
            }
        }

        $map['siteid'] = array('eq', $siteid);
        $map['status'] = array("eq", 1);
        $row = ($args['limit'] != '') ? $args['limit'] : $args['row'];

        //排序字段
        if (empty($args['orderby'])) {
            $orderby = '`order` ' . $args['orderway'];
        } else {
            $orderby = '`' . $args['orderby'] . '` ' . $args['orderway'];
        }

        //where 重写
        if (!empty($args['where']) && preg_match('#__(.*)__#is', $args['where'])) {
            TagLibHelper::overwriteWhere($map, $args['where']);
        }
        //orderby 重写
        if (!empty($args['orderby']) && preg_match('#__(.*)__#is', $args['orderby'])) {
            TagLibHelper::overwriteOrderby($orderby, $args['orderby']);
        }

        $rsArr = $this->where($map)->order($orderby)->limit($row)->select();
        //echo $this->getLastSql();
        //print_r($rsArr);
        if (!empty($rsArr)) {
            foreach ($rsArr as $k => $v) {
                if (!empty($args['titlelen'])) {
                    $rsArr[$k]['name'] = msubstr($v['name'], 0, $args['titlelen']);
                }

                //判断是不是父栏目
                if (!empty($args['isparent'])) {
                    if ($args['isparent'] == 'yes') {
                        if ($this->isParent($v['id'])) continue;
                        else unset($rsArr[$k]);
                    } else if ($args['isparent'] == 'no') {
                        if (!$this->isParent($v['id'])) continue;
                        else unset($rsArr[$k]);
                    } else continue;
                }

                if (empty($v['url'])) {
                    $rsArr[$k]['url'] = listUrl($v['id']);
                } else {
                    $rsArr[$k]['url'] = parseArctypeUrl($rsArr[$k]['url']);
                }

                if ($args['addfield'] == 'on' && $v['type'] == 3) {
                    $channelM = M("Channel");
                    $addtable = $channelM->where("id = " . $v['channel'])->getField("addtable");
                    if (!empty($addtable)) {
                        $addM = M($addtable);
                        $addArr = $addM->where("typeid = " . $v['id'])->find();
                        if (!empty($addArr)) {
                            $rsArr[$k] = $rsArr[$k] + $addArr;
                        }
                    }
                }
            }
        }

        //result 重写
        if (!empty($args['result']) && preg_match('#__(.*)__#is', $args['result'])) {
            TagLibHelper::overwriteResult($rsArr, $args['result']);
        }
        return $rsArr;
    }

    /**
     * 通过tid取得一条数据
     * @param unknown_type $tid
     */

    public function getOne($tid)
    {
        $map['id'] = array('eq', $tid);
        $arr = $this->where($map)->find();
        return $arr;
    }


    public function getSuperTree($siteid = null)
    {
        import('@.Class.SuperClassify');
        if ($siteid === null) {
            $sid = getSiteId();
        } else {
            $sid = $siteid;
        }

        //todo:此处可以作缓存文件
        $arr = $this->getSuperTreeArr($sid);
        $nav = new SuperClassify ($arr);

        /*			$cache_data = S('superTreeObj'.$sid);
                    if($cache_data == null){
                        $arr = $this->getSuperTreeArr($sid);
                        $nav = new SuperClassify ( $arr );
                        S('superTreeObj'.$sid,$nav,C('SYS_CACHE_TIME'));
                    }else{
                        $nav = $cache_data;
                    }*/
        return $nav;
    }

    public function getRouteLine($tid)
    {
        import('@.Class.SuperClassify');
        $nav = $this->getSuperTree();
        return $nav->routeLine($tid);
    }

    /**
     * 为select提供Arr数据
     */
    public function arctypeArr($siteid = null)
    {
        $nav = $this->getSuperTree($siteid);
        $nav->flag = " — "; //配置树
        $nav->create(); //生成树
        $arr = $nav->treeArr;
        $rs[0] = "根";
        if (!empty($arr)) {
            foreach ($arr as $v) {
                $rs[$v['id']] = $v['name'];
            }
            return $rs;
        } else return $rs;
    }

    /**
     * 为select提供Arr的验证数据
     * 保存移动内容时候，只能移动到相同的内容模型栏目里面
     */
    public function arctypeArrC($cid, $siteid = null)
    {
        //import ( '@.Class.SuperClassify' );
        $arr = $this->getSuperTreeArr($siteid);
        if (!empty($arr)) {
            foreach ($arr as $v) {
                $rs[$v['id']] = ($v['channel'] == $cid) ? 1 : 0;
            }
            return $rs;
        } else return;
    }

    /**
     * 同上，只是增加了条件，只有叶子节点可以被选中
     *
     */
    public function arctypeArrC2($cid, $siteid = null)
    {
        //import ( '@.Class.SuperClassify' );
        $arr = $this->getSuperTreeArr($siteid);
        if (!empty($arr)) {
            foreach ($arr as $v) {
                $rs[$v['id']] = ($v['channel'] == $cid && !$this->isParent($v['id'], $siteid)) ? 1 : 0;
            }
            return $rs;
        } else return;
    }


    /**
     * 为select提供Arr的验证数据
     * 保存移动栏目的时候type
     */
    public function arctypeArrT($type, $tid = null, $cid = null)
    {
        //import ( '@.Class.SuperClassify' );
        $arr = $this->getSuperTreeArr();
        if (!empty($arr)) {
            foreach ($arr as $v) {
                if ($v['type'] == 1) {
                    $rs[$v['id']] = ($v['type'] == $type || $type == 3) ? 1 : 0;
                } else if ($v['type'] == 2) {
                    $rs[$v['id']] = ($v['type'] == $type && $v['channel'] == $cid || $type == 3) ? 1 : 0;
                }
            }
            if ($tid !== null) $tidArr = explode(',', $tid);
            //选中项不能被作为父栏目
            if (!empty($tidArr)) {
                foreach ($tidArr as $v) {
                    $rs[$v] = 0;
                }
            }
            $rs[0] = 1; //根节点总能容纳百川
            return $rs;
        } else return;

    }

    /**
     * 返回超级树原生Arr
     */
    public function getSuperTreeArr($siteid = null)
    {
        if ($siteid === null) {
            $sid = getSiteId();
        } else {
            $sid = $siteid;
        }

        $groupName = strtolower(GROUP_NAME);

        if ($groupName == 'home') {

            $cache_data = S('arctypeArr' . $sid . "_home");

        } else if ($groupName == 'admin') {

            if (!session("superUser")) {
                $cache_data = S('arctypeArr' . $sid . "_a");
            } else {
                $cache_data = S('arctypeArr' . $sid);
            }

        }
        //如果是调试模式，关闭栏目缓存
        if (APP_DEBUG) {
            $cache_data = null;
        }

        if ($cache_data == null) {
            $map['siteid'] = $sid;


            if ($groupName == 'home') {
                $map['display'] = array("in", '1,3');
                $flag = "_home";
            } else if ($groupName == 'admin') {
                if (!session("superUser")) {
                    $map['display'] = array("in", '1,2');
                    $flag = "_a";
                } else {
                    $flag = "";
                }
            }

            $arr = $this->where($map)->select();
            S('arctypeArr' . $sid . $flag, $arr, C('SYS_CACHE_TIME'));
        } else {
            $arr = $cache_data;
        }
        return $arr;
    }

    /*
     * 通过栏目id返回对应channelid
     */
    public function getChannel($typeid)
    {
        $typeid = stristr($typeid, ',', true) ? stristr($typeid, ',', true) : $typeid;
        return $this->where('id=' . $typeid)->getField('channel');
    }

    public function isParent($id, $siteid = null)
    {
        if ($siteid === null) {
            $sid = getSiteId();
        } else {
            $sid = $siteid;
        }
        $map['fid'] = $id;
        $map['siteid'] = $sid;
        $arr = $this->field('id')->where($map)->find();
        if (empty($arr)) return false;
        else return true;
    }

    /*
     * 当发现自身栏目没有定义模板的时候
     * 首先查找同模型的兄弟栏目模板
     * 再查找同模型父栏目兄弟栏目模板
     */
    public function getDir($id, $channel, $dir = 'tdir')
    {
        $arr = $this->where('id=' . $id)->find();
        if($arr){
            if($arr['channel'] == $channel && $arr[$dir]){
                return $arr[$dir];
            }
        }else{
            return "";
        }
        $brother = $this->where('fid='.$arr['fid'].' AND channel='.$channel.' AND '.$dir.'!=""')->find();
        if($brother){
            return $brother[$dir];
        }else{
            $finddir = $this->getDir($arr['fid'], $channel, $dir);
        }
        return $finddir;
    }

    /**
     * 获得所有父栏目
     */
    public function getAllParent($tid, $siteid = null)
    {
        if ($siteid === null) {
            $sid = getSiteId();
        } else {
            $sid = $siteid;
        }
        $route = $this->field('id,route')->where("id = " . $tid . " and siteid = $sid")->find();
        if (empty($route)) {
            return array();
        } else {
            return explode("-", $route['route']);
        }
    }

    /**
     * 获得所有子栏目
     */
    public function getAllSon($tid, $siteid = null)
    {
        if ($this->isParent($tid)) {
            if ($siteid === null) {
                $sid = getSiteId();
            } else {
                $sid = $siteid;
            }
            $tids = $this->field('id,route')->where("route like '%" . $tid . "%' and status = 1 and siteid = $sid")->select();
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
        } else {
            return null;
        }
    }

    /**
     * 获得相同channel的子栏目
     */
    public function getSameChannelSon($tid, $siteid = null)
    {
        if ($this->isParent($tid)) {
            if ($siteid === null) {
                $sid = getSiteId();
            } else {
                $sid = $siteid;
            }
            $siteid = C('currentSite.id');
            $cid = $this->getChannel($tid);
            $tids = $this->field('id,route')->where("route like '%" . $tid . "%' and status = 1 and siteid = $sid" . " and channel = " . $cid)->select();
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
        } else {
            return null;
        }
    }


    /**
     * 栏目统计图
     */
    function arctypeChart($tid)
    {
        if (!isset($tid)) return null;
        $sid = getSiteId();
        $map['fid'] = array('eq', $tid);
        $map['status'] = array('eq', 1);
        $map['siteid'] = array("eq", $sid);
        $arr = $this->where($map)->select();
        if (empty($arr)) return null;
        require(APP_INC_PATH . 'chart/FusionCharts.php');
        $strXML = "<graph animation='0' caption='' subCaption='' showPercentValues='1' pieSliceDepth='10' showNames='0' decimalPrecision='0' baseFontSize='12' shadowAlpha='80' baseFontColor='1E1E1E' rotateNames='0'  >";
        foreach ($arr as $v) {
            $counts = $this->getAllSon($v[id]);
            if ($counts == null)
                $strXML .= "<set name='$v[name]' value='1' />";
            else {
                $countarr = explode(',', $counts);
                $count = count($countarr);
                $str = '';
                $countt = $count + 1;
                $str = $v[name] . " (含 $count 个子栏目)";
                $strXML .= "<set name='$str' value='$countt' />";
            }
        }
        $strXML .= "</graph>";
        $chart_html = renderChart("__INC__/chart/FCF_Pie2D.swf", "", $strXML, "", 300, 150);
        return $chart_html;
    }
}
?>
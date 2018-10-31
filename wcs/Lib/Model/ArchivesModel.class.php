<?php
/**
 * file: ArchivesModel.class.php
 * intro: 文章主表模型类
 * @date: 2012-8-22
 * @author: LHY
 * @version: 2.0
 */
import('@.Class.TagLibHelper');
class ArchivesModel extends RelationModel
{
    public $_link = array(
        'addfields' => array(
            'mapping_type' => HAS_ONE,
            'class_name' => 'addnews',
            'foreign_key' => 'aid'
        ));

//敏感词过滤
    /* protected function _after_select(&$rs,$option){
                if(C('SYS_FILTER')==1){
                    foreach($rs as $k => $v){
                        $rs[$k]['title'] = preg_replace('#'.C("SYS_ILLEGA_WORDS").'#i', '',$rs[$k]['title']);
                    }
                }else{
                    return;
                }
            }
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

        //todo:flag匹配
        if (!empty($args['flag'])) {
            $flag = '';
            if (!stripos($args['flag'], ',')) {
                $map['flag'] = array('like', "%$args[flag]%");
            } else {
                $flagArr = explode(',', $args['flag']);
                $max = count($flagArr);
                for ($i = 0; $i < $max; $i++) {
                    $flag .= '`flag` like \'%' . trim($flagArr[$i]) . '%\'';
                    if ($i < $max - 1) $flag .= ' AND ';
                }
                $map['_string'] = $flag;
            }
        }

        //指定父栏目
        $class = trim($args['class']);
        $tid = (!is_null($args['typeid'])) ? $args['typeid'] : $_GET['tid'];
        if ((empty($tid) && $tid != 0) && empty($class)) die("typeid 无效！501");

        $m = new ArctypeModel();

        $siteid = getSiteId();
        if (is_null($args['typeid']) && !empty($class)) {
            $tid = $m->where("`class` = '$class' and siteid = $siteid ")->getField('id');
        }

        //用于给分页传递原始tid参数
        $truecid = $m->getChannel($tid);
        if ($m->isParent($tid)) {
            $tid = $m->getSameChannelSon($tid);
        }

        $map['typeid'] = array('in', $tid);

        //指定内容模型
        if (!empty($args['channelid'])) {
            $map['channel'] = array('eq', $args['channelid']);
        }

        //通过标题模糊查找
        if (!empty($args['title'])) {
            $map['title'] = array('like', "%$args[title]%");
        }

        //保证内容状态正常
        $map['status'] = array("in", array(1, 3));

        //条数限制
        $row = ($args['limit'] != '') ? $args['limit'] : $args['row'];

        //排序字段
        $orderby = '`sortrank` desc ,`' . $args['orderby'] . '` ' . $args['orderway'];

        //where 重写
        if (!empty($args['where']) && preg_match('#__(.*)__#is', $args['where'])) {
            TagLibHelper::overwriteWhere($map, $args['where']);
        }
        //orderby 重写
        if (!empty($args['orderby']) && preg_match('#__(.*)__#is', $args['orderby'])) {
            TagLibHelper::overwriteOrderby($orderby, $args['orderby']);
        }

        //开启后查询附加表
        if ($args['addfield'] == 'on') {
            $m2 = new ChannelModel();
            $addtable = $m2->where('id=' . $truecid)->getField('addtable');
            $this->_link['addfields']['class_name'] = $addtable;
            $rsArr = $this->relation(true)->where($map)->order($orderby)->limit($row)->select();
            if (!empty($rsArr)) {
                foreach ($rsArr as $k => $v) {
                    $rsArr[$k] = array_merge($v, $v["addfields"]);
                    unset($rsArr[$k]["addfields"]);
                }
            }
        } else {
            $rsArr = $this->where($map)->order($orderby)->limit($row)->select();
        }

        if (!empty($rsArr)) {
            foreach ($rsArr as $k => $v) {
                if (!empty($args['titlelen'])) {
                    $rsArr[$k]['title'] = msubstr($v['title'], 0, $args['titlelen']);
                }
                if (!empty($args['desclen'])) {
                    $rsArr[$k]['desc'] = msubstr($v['desc'], 0, $args['desclen']);
                }

                if (!empty($v['color']) && $args['color'] == 'on') {
                    $rsArr[$k]['title'] = "<font style='color:" . $v['color'] . ";'>" . $rsArr[$k]['title'] . "</font>";
                }

                if (empty($rsArr[$k]['desc'])) {
                    if (!empty($args['desclen'])) {
                        $rsArr[$k]['desc'] = msubstr(Html2Text($v['txt']), 0, $args['desclen']);
                    } else {
                        $rsArr[$k]['desc'] = msubstr(Html2Text($v['txt']), 0, 60);
                    }
                }

                if ($args['pagebreak'] == 'on') {
                    $jspagArr = C('SYS_PAGE_BREAK_FIELDS');
                    foreach ($jspagArr as $f) {
                        if (!empty($rsArr[$k][$f])) {
                            $rsArr[$k][$f] = pageBreak($rsArr[$k][$f]);
                        }
                    }
                }
                $rsArr[$k]['url'] = strstr($v['flag'], 'j') ? $v['desc'] : contentUrl($v['id']);
            }
        }

        //result 重写
        if (!empty($args['result']) && preg_match('#__(.*)__#is', $args['result'])) {
            TagLibHelper::overwriteResult($rsArr, $args['result']);
        }
        return $rsArr;
    }

    public function getPageList($arg, $variables)
    {
        import('ORG.Util.PageFront');
        $args = unserialize($arg);
        if ($variables != null) {
            $variables = unserialize($variables);
            if (!empty($variables)) {
                $args = array_merge($args, $variables);
            }
        }

        //todo:flag匹配
        if (!empty($args['flag'])) {
            $flag = '';
            if (!stripos($args['flag'], ',')) {
                $map['flag'] = array('like', "%$args[flag]%");
            } else {
                $flagArr = explode(',', $args['flag']);
                $max = count($flagArr);
                for ($i = 0; $i < $max; $i++) {
                    $flag .= '`flag` like \'%' . trim($flagArr[$i]) . '%\'';
                    if ($i < $max - 1) $flag .= ' AND ';
                }
                $map['_string'] = $flag;
            }
        }

        $class = trim($args['class']);
        $tid = ($args['typeid'] != null) ? $args['typeid'] : $_GET['tid'];
        if (($tid == null || $tid == '') && empty($class)) die("typeid 无效！501");

        //如果没有指定typeid 则考虑class 如果也没有则考虑_get
        $m = new ArctypeModel();
        if (!empty($class) && $args['typeid'] == null) {
            $tid = $m->where("`class` = '$class'")->getField('id');
        }

        //用于给分页传递原始tid参数
        $truetid = $tid;
        $truecid = $m->getChannel($tid);
        //如果有子栏目，也查找其子栏目
        if ($m->isParent($tid)) {
            $tid = $m->getSameChannelSon($tid);
        }

        $map['typeid'] = array('in', $tid);
        //保证内容状态时正常的
        $map['status'] = array('in', array(1, 3));

        //dump($map);
        $pagesize = ($args['pagesize'] != '') ? $args['pagesize'] : '10';

        //排序字段
        $orderby = '`sortrank` desc ,`' . $args['orderby'] . '` ' . $args['orderway'];

        //where 重写
        if (!empty($args['where']) && preg_match('#__(.*)__#is', $args['where'])) {
            TagLibHelper::overwriteWhere($map, $args['where']);
        }
        //orderby 重写
        if (!empty($args['orderby']) && preg_match('#__(.*)__#is', $args['orderby'])) {
            TagLibHelper::overwriteOrderby($orderby, $args['orderby']);
        }

        $count = $this->where($map)->count(); // 查询满足要求的总记录数
        //$Page = new Page($count, $pagesize, 'tid=' . $truetid); // 实例化分页类 传入总记录数和每页显示的记录数
		//利于WAP互通使用 $_GET['tid'] 
        $Page = new Page($count, $pagesize, 'tid=' . $_GET['tid']); // 实例化分页类 传入总记录数和每页显示的
        $show = $Page->show(); // 分页显示输出
        $page_info = $Page->page_info();

        if ($args['addfield'] == 'on') {
            $mc = new ChannelModel();
            $rsArr = array();
            $addtable = $mc->where('id=' . $truecid)->getField('addtable');
            $this->_link['addfields']['class_name'] = $addtable;
            $rsArr = $this->relation(true)->where($map)->order($orderby)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            if (!empty($rsArr)) {
                foreach ($rsArr as $k => $v) {
                    $rsArr[$k] = array_merge($v, $v["addfields"]);
                    unset($rsArr[$k]["addfields"]);
                }
            }
        } else {
            $rsArr = $this->where($map)->order($orderby)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        }

        foreach ($rsArr as $k => $v) {
            if (!empty($args['titlelen'])) {
                $rsArr[$k]['title'] = msubstr($v['title'], 0, $args['titlelen']);
            }
            if (!empty($args['desclen'])) {
                $rsArr[$k]['desc'] = msubstr($v['desc'], 0, $args['desclen']);
            }
            if (!empty($v['color']) && $args['color'] == 'on') {
                $rsArr[$k]['title'] = "<font style='color:" . $v['color'] . ";'>" . $rsArr[$k]['title'] . "</font>";
            }

            if (empty($rsArr[$k]['desc'])) {
                if (!empty($args['desclen'])) {
                    $rsArr[$k]['desc'] = msubstr(Html2Text($v['txt']), 0, $args['desclen']);
                } else {
                    $rsArr[$k]['desc'] = msubstr(Html2Text($v['txt']), 0, 60);
                }
            }
            $rsArr[$k]['url'] = strstr($v['flag'], 'j') ? $v['desc'] : contentUrl($v['id']);
        }

        $rsArr['pageline'] = $show;
        $rsArr['pageinfo'] = $page_info;

        //result 重写
        if (!empty($args['result']) && preg_match('#__(.*)__#is', $args['result'])) {
            TagLibHelper::overwriteResult($rsArr, $args['result']);
        }
        return $rsArr;
    }

    /*
     * 内容统计饼状图
     */
    function contentChart($tid)
    {
        if (!isset($tid)) return null;
        $m = new ArctypeModel();
        require(APP_INC_PATH . 'chart/FusionCharts.php');
        $strXML = "<graph animation='0' caption='' subCaption='' showPercentValues='1' pieSliceDepth='10' showNames='0' decimalPrecision='0' baseFontSize='12' shadowAlpha='80' baseFontColor='1E1E1E' rotateNames='0'  >";
        $thistid = $this->where('typeid=' . $tid . '  and status=1')->count("id");
        if (!empty($thistid)) {
            $strXML .= "<set name='本栏目' value='$thistid' />";
        }
        $arr = $m->where('fid=' . $tid)->select();
        if (empty($arr)) {
            /*  $arcname = getArctypeName($tid);
                $counts = $this->where('typeid='.$tid.' and status=1')->count("id");
                if($counts == null) $counts=0;
                $strXML .= "<set name='$arcname' value='$counts' />"; */
        } else {
            foreach ($arr as $v) {
                $counts = 0;
                $arctypes = $m->getAllSon($v['id']);
                $arcname = getArctypeName($v['id']);
                if ($arctypes == null) {
                    $counts = $this->where('typeid=' . $v['id'] . '  and status=1')->count("id");
                    if ($counts == null) $counts = 0;
                    $strXML .= "<set name='$arcname' value='$counts' />";
                } else {
                    $counts = $this->where('typeid in (' . $arctypes . ') and status=1')->count("id");
                    if ($counts == null) $counts = 0;
                    $strXML .= "<set name='$arcname' value='$counts' />";
                }
            }
        }
        $strXML .= "</graph>";
        $chart_html = renderChart("__INC__/chart/FCF_Pie2D.swf", "", $strXML, "", 300, 150);
        //die($strXML);
        return $chart_html;
    }

    /**
     * 通过tid取得一条数据
     * @param $id
     */
    public function getOne($id)
    {
        $map['id'] = array('eq', $id);
        $arr = $this->where($map)->find();
        return $arr;
    }
}
?>
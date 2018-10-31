<?php
import('@.Class.TagLibHelper');
class CommonModel extends Model
{

    public function getData($arg, $variables = null)
    {
        $args = unserialize($arg);
        if ($variables != null) {
            $variables = unserialize($variables);
            if (!empty($variables)) {
                $args = array_merge($args, $variables);
            }
        }
        $tid = (!is_null($args['typeid'])) ? $args['typeid'] : $_GET['tid'];
        $siteid = getSiteId();
        $map = array();
        $map['siteid'] = array("eq", $siteid);

        if (!empty($tid)) {
            $map['typeid'] = array("eq", $tid);
        }
        $map['status'] = array("eq", 1);

        //排序字段
        if (array_key_exists("sortrank", $this->fields['_type'])) {
            $orderby = '`sortrank` desc ,`' . $args['orderby'] . '` ' . $args['orderway'];
        } else {
            $orderby = ' `' . $args['orderby'] . '` ' . $args['orderway'];
        }
        //where 重写
        if (!empty($args['where']) && preg_match('#__(.*)__#is', $args['where'])) {
            TagLibHelper::overwriteWhere($map, $args['where']);
        } else {
            $map['_string'] = $args['where'];
        }
        //orderby 重写
        if (!empty($args['orderby']) && preg_match('#__(.*)__#is', $args['orderby'])) {
            TagLibHelper::overwriteOrderby($orderby, $args['orderby']);
        }
        $row = ($args['limit'] != '') ? $args['limit'] : $args['row'];
        $rsArr = $this->where($map)->order($orderby)->limit($row)->select();

        if (!empty($rsArr)) {
            foreach ($rsArr as $k => $v) {
                $rsArr[$k]['url'] = contentUrl($v['id'], $v['channel']);
            }
        }
        //result 重写
        if (!empty($args['result']) && preg_match('#__(.*)__#is', $args['result'])) {
            TagLibHelper::overwriteResult($rsArr, $args['result']);
        }
        return $rsArr;
    }

    public function getPageList($arg, $variables = null)
    {
        import('ORG.Util.PageFront');

        $args = unserialize($arg);
        if ($variables != null) {
            $variables = unserialize($variables);
            if (!empty($variables)) {
                $args = array_merge($args, $variables);
            }
        }

        $siteid = getSiteId();
        $map['siteid'] = array("eq", $siteid);

        $class = trim($args['class']);
        $tid = ($args['typeid'] != null) ? $args['typeid'] : $_GET['tid'];
        $m = new ArctypeModel();
        if (!empty($class) && $args['typeid'] == null) {
            $tid = $m->where("`class` = '$class'")->getField('id');
        }

        /*if (!empty($tid)) {
            $map['typeid'] = array("eq", $tid);
        }*/
		
		if ($m->isParent($tid)) {
            $tid = $m->getSameChannelSon($tid);
        }
        if (!empty($tid)) {
            $map['typeid'] = array('in', $tid);
        }

        //排序字段
        if (array_key_exists("sortrank", $this->fields['_type'])) {
            $orderby = '`sortrank` desc ,`' . $args['orderby'] . '` ' . $args['orderway'];
        } else {
            $orderby = ' `' . $args['orderby'] . '` ' . $args['orderway'];
        }

        //where 重写
        if (!empty($args['where']) && preg_match('#__(.*)__#is', $args['where'])) {
            TagLibHelper::overwriteWhere($map, $args['where']);
        } elseif (!empty($args['where'])) {
            $map['_string'] = $args['where'];
        }

        //orderby 重写
        if (!empty($args['orderby']) && preg_match('#__(.*)__#is', $args['orderby'])) {
            TagLibHelper::overwriteOrderby($orderby, $args['orderby']);
        }

        $pagesize = ($args['pagesize'] != '') ? $args['pagesize'] : '10';
        $count = $this->where($map)->count();
        $Page = new Page($count, $pagesize, 'tid=' . $_GET['tid']);
        $show = $Page->show();
        $pageinfo = $Page->page_info();
        $rsArr = $this->where($map)->order($orderby)->limit($Page->firstRow . ',' . $Page->listRows)->select();

        if (!empty($rsArr)) {
            foreach ($rsArr as $k => $v) {
                $rsArr[$k]['url'] = contentUrl($v['id'], $v['channel']);
            }
        }

        $rsArr['pageline'] = $show;
        $rsArr['pageinfo'] = $pageinfo;

        //result 重写
        if (!empty($args['result']) && preg_match('#__(.*)__#is', $args['result'])) {
            TagLibHelper::overwriteResult($rsArr, $args['result']);
        }

        return $rsArr;
    }

    public function getPageQueryData($arg)
    {
        import('ORG.Util.PageFront');
        $args = unserialize($arg);
        $rsArr = array();
        if (!empty($args['process']) && preg_match('#__(.*)__#is', $args['process'])) {
            TagLibHelper::overwriteProcess($rsArr, $args);
        }
        return $rsArr;
    }

    public function getQueryData($arg)
    {
        $args = unserialize($arg);
        $sql = "";
        if (!empty($args['sql']) && preg_match('#__(.*)__#is', $args['sql'])) {
            TagLibHelper::overwriteSql($sql, $args);
        } else {
            $sql = $args['sql'];
        }
        $rsArr = $this->query($sql);
        return $rsArr;
    }

}

?>
<?php

/**
 * file: ArchivesModel.class.php
 * intro: 文章主表模型类
 * @date: 2012-8-22
 * @author: LHY
 * @version: 2.0
 */
import('@.Class.TagLibHelper');
class FlinkModel extends Model
{


    public function getData($arg)
    {
        $args = unserialize($arg);

        if (!empty($args['typeid'])) {
            $map['typeid'] = array('in', $args['typeid']);
        }

        $siteid = getSiteId();
        if (!empty($siteid)) {
            $map['siteid'] = array('eq', $siteid);
        }

        //排序字段
        $orderby = '`sortrank` desc ,`' . $args['orderby'] . '` ' . $args['orderway'];
        $map['status'] = array('eq', 1);

        //where 重写
        if (!empty($args['where']) && preg_match('#__(.*)__#is', $args['where'])) {
            TagLibHelper::overwriteWhere($map, $args['where']);
        }
        //orderby 重写
        if (!empty($args['orderby']) && preg_match('#__(.*)__#is', $args['orderby'])) {
            TagLibHelper::overwriteOrderby($orderby, $args['orderby']);
        }


        $row = ($args['limit'] != '') ? $args['limit'] : $args['row'];
        $rsArr = $this->where($map)->order($orderby)->limit($row)->select();
        foreach ($rsArr as $k => $v) {
            if (!empty($args['titlelen'])) {
                $rsArr[$k]['title'] = msubstr($v['title'], 0, $args['titlelen']);
            }
        }


        //result 重写
        if (!empty($args['result']) && preg_match('#__(.*)__#is', $args['result'])) {
            TagLibHelper::overwriteResult($rsArr, $args['result']);
        }

        return $rsArr;
    }


}


?>
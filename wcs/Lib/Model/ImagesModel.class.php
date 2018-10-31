<?php

/**
 * file: ArchivesModel.class.php
 * intro: 文章主表模型类
 * @date: 2012-8-22
 * @author: LHY
 * @version: 2.0
 */
import('@.Class.TagLibHelper');
class ImagesModel extends Model
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

        $aid = !empty($args['aid']) ? $args['aid'] : $_GET['aid'];

        $map['type'] = array("eq", 1);
        $map['gid'] = array('in', $aid);

        $orderby = '`' . $args['orderby'] . '` ' . $args['orderway'];

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
                $rsArr[$k]['intro'] = msubstr($v['intro'], 0, $args['titlelen']);
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
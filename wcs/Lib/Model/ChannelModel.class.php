<?php

/**
 * file: ArchivesModel.class.php
 * intro: 内容模型的数据模型类
 * @date: 2012-8-22
 * @author: LHY
 * @version: 2.0
 */
class ChannelModel extends Model
{
    public function selectArr($cid)
    {
        $map['issystem'] = array('eq', 1);
        $map['isshow'] = array("eq", 1);
        $arr = $this->where($map)->select();

        $rs = '';
        foreach ($arr as $v) {
            $rs .= '<option value="' . $v['id'] . '">' . $v['name'] . '</option>';
        }
        return $rs;
    }

    public function getAll()
    {
        $cache_data = S('channelArr');
        if ($cache_data == null) {
            $rs = $this->select();
            S('channelArr', $rs, C('SYS_CACHE_TIME'));
            return $rs;
        } else {
            return $cache_data;
        }
    }

    public function getOne($id)
    {
        $arr = $this->getAll();
        if (!empty($arr)) {
            foreach ($arr as $k => $v) {
                if ($v['id'] == $id) {
                    return $v;
                }
            }
        } else {
            return;
        }
    }

    public function getOneField($field)
    {
        $arr = $this->field($field)->select();
        return $arr;
    }

    /*
     * 通过 channel id 检查有没有指定的模型
     */
    public function isChannel($cid)
    {
        $arr = $this->getAll();
        $bool = false;
        if (!empty($arr)) {
            foreach ($arr as $k => $v) {
                if ($v['id'] == $cid) {
                    $bool = true;
                }
            }
            return $bool;
        } else {
            return $bool;
        }
    }

    /*
     * 通过内容模型id取得内容模型名称
     */
    public function getChannelName($cid)
    {
        $arr = $this->getAll();
        $bool = false;
        if (!empty($arr)) {
            foreach ($arr as $k => $v) {
                if ($v['id'] == $cid) {
                    return $v['name'];
                }
            }
            return;
        } else {
            return;
        }
    }
}
?>
<?php

class GoodstypeModel extends Model
{


    /*商品种类下拉选框*/
    public function getSelectHtml($gtid)
    {

        $html = '';
        $arr = $this->order('id asc')->select();
        if (!empty($arr)) {
            foreach ($arr as $v) {
                if ($v['id'] == $gtid) $f = "selected"; else $f = '';
                $html .= '<option  ' . $f . ' value=' . $v['id'] . '  >' . $v['name'] . '</option>';
            }
        }

        return $html;
    }

    /*商品种类下拉选框Arr*/
    public function getSelectArr()
    {
        $selectArr = array();
        $arr = $this->order('id asc')->select();
        if (!empty($arr)) {
            foreach ($arr as $v) {
                $selectArr[$v['id']] = $v['name'];
            }
        }

        return $selectArr;
    }


}


?>
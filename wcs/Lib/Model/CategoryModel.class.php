<?php

/**
 * file: ArctypeModel.class.php
 * intro: 文章主表模型类
 * @date: 2012-8-22
 * @author: LHY
 * @version: 2.0
 */
class CategoryModel extends Model
{


    /**
     * 通过tid取得一条数据
     * @param unknown_type $tid
     */

    public function getOne($tid)
    {

        $map['id'] = array('eq', $tid);
        $arr = $this->where($map)->find();
        //echo $this->getLastSql();
        return $arr;


    }


    public function getSuperTree()
    {
        import('@.Class.SuperClassify');
        //$cache_data = S('categoryTree');
        $cache_data = null;
        if ($cache_data == null) {
            $arr = $this->getSuperTreeArr();
            $nav = new SuperClassify ($arr);
            //S('categoryTree',$nav,C('SYS_CACHE_TIME'));
        } else {
            $nav = $cache_data;
        }

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
    public function categorySelectArr()
    {

        $nav = $this->getSuperTree();
        $nav->flag = " — "; //配置树
        $nav->create(); //生成树
        $arr = $nav->treeArr;
        $rs[0] = "根";
        if (!empty($arr)) {
            foreach ($arr as $v) {
                $rs[$v['id']] = $v['name'];
            }
            return $rs;
        } else return;


    }

    /**
     * 为select提供Arr数据
     */
    public function categorySelectArrT($id)
    {

        $nav = $this->getSuperTree();
        $nav->flag = " — "; //配置树
        $nav->create(); //生成树
        $arr = $nav->treeArr;
        $rs[0] = 1;
        if (!empty($arr)) {
            foreach ($arr as $v) {
                if ($v['id'] == $id) $rs[$v['id']] = 0;
                else $rs[$v['id']] = 1;
            }
            return $rs;
        } else return;


    }


    /**
     * 为商品类别列表提供arr
     */
    public function categoryListArr()
    {

        $nav = $this->getSuperTree();
        $nav->flag = ""; //配置树
        $nav->create(); //生成树
        $arr = $nav->treeArr;
        $i = 0;
        foreach ($nav->treeArr as $v) {
            if ($v['status'] == -1) continue; //已删除栏目不显示
            $rs [$i]['id'] = $v['id'];
            $rs [$i]['fid'] = $v['fid'];
            $rs [$i]['name'] = $v['name'];
            $rs [$i]['grade'] = $v['grade'];
            $rs [$i]['order'] = $v['order'];
            $rs [$i]['class'] = 'cat_' . $v['route'];
            $rs [$i]['isParent'] = $this->isParent($v['id']);
            $i++;
        }
        return $rs;

    }


    /**
     * 返回超级树原生Arr
     */
    public function getSuperTreeArr()
    {

        //$cache_data = S('categoryArr');
        $cache_data = null;
        if ($cache_data == null) {
            $arr = $this->select();
            //S('categoryArr',$arr,C('SYS_CACHE_TIME'));
        } else {
            $arr = $cache_data;
        }

        return $arr;
    }


    public function isParent($id)
    {
        $arr = $this->field('id')->where('fid=' . $id)->select();
        if (empty($arr)) return false;
        else return true;
    }


    /**
     * 获得所有子栏目
     */
    public function getAllSon($tid)
    {
        if ($this->isParent($tid)) {

            $tids = $this->field('id,route')->where("route like '%" . $tid . "%' and status = 1")->select();
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


}


?>
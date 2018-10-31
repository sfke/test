<?php
/**
 * @version    JL_WCS 2.0
 * @copyright  Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 * @Author:    LHY CL014
 */




abstract class Statistics {

    protected $baseData = array();
    abstract protected function getBaseData();
    abstract protected function getRecordData();

}



class PvStatistics extends Statistics{

    protected $db;


    public function __construct(){
        $this->db = M("Pv");
    }


    //从数据库读取记录信息
    public function readRecordData($param){
        $map = array();
        $map['type'] = array("eq",$param['type']);
        $arr = $this->db->where($map)->select();
        $rs = array();
        foreach($arr as $k=>$v){
            $recordData = unserialize($v["record"]);
            $temp['date'] = $v["date"];
            $temp['time'] = $v["time"];
            $temp['record'] = $recordData;
            $rs[] = $temp;
        }

        return $rs;
    }




    public function refreshPv($nowDate=null){
        if(empty($nowDate)) $nowDate = date("Y-m-d",time());
        $temp = $this->db->where("`date` = '".$nowDate."'")->find();
        if(empty($temp)){
            $recordData =  $this->getRecordData();
            if(empty($recordData)){
                return false;
            }else{
                //计算每日新增PV(与上一日对比)
                if(1){
                    $refreshPvIncreaseRs = $this->refreshPvIncrease($recordData,$nowDate);
                }
                $str = serialize($recordData);
                $data = array();
                $data['date'] = $nowDate;
                //$data['time'] = time();
                $data['time'] = strtotime($nowDate);
                $data['record'] = $str;
                $data['type'] = 1;
                $this->db->create($data);
                return $this->db->add();
            }
        }else{
            return false;
        }
    }


    public function refreshPvIncrease($nowData,$nowDate=null){
        if(empty($nowDate)) $nowDate = date("Y-m-d",time());
        $map = array();
        $map['date'] = array("LT",$nowDate);
        $map['type'] = array("eq",1);
        $lastRecord = $this->db->where($map)->order("date desc")->find();

        if(empty($lastRecord)){
            return false;
        }else{
            $lastData = unserialize($lastRecord['record']);
            if(!empty($lastData) && !empty($nowData)){
                $increaseData = $this->pvDataSub($nowData,$lastData);

                if(empty($increaseData)){
                    return false;
                }else{
                    $data = array();
                    $data['date'] = $nowDate;
                    //$data['time'] = time();
                    $data['time'] = strtotime($nowDate);
                    $data['type'] = 2;
                    $data['record'] = serialize($increaseData);
                    $this->db->create($data);
                    return $this->db->add();
                }
            }else{
                return false;
            }
        }
    }


    protected function pvDataSub($nowData,$lastData){
        $increaseData = array();
        foreach($nowData as $sid => $record){
            foreach($record as $tid=>$data){
                if(!empty($lastData[$sid][$tid])){
                    $temp = array();
                    $temp[0] = abs($data[0] - $lastData[$sid][$tid][0]);
                    $temp[1] = abs($data[1] - $lastData[$sid][$tid][1]);

                    $increaseData[$sid][$tid] = $temp;
                }else{
                    $temp[0] = 0;
                    $temp[1] = 0;
                    $increaseData[$sid][$tid] = $temp;
                }

            }
        }

        if(!empty($increaseData)){
            return $increaseData;
        }else{
            return false;
        }

    }


    public function getRecordData(){

        $baseData = $this->getBaseData();
        if(!empty($baseData)){
            $rs = array();
            //$k 是站点ID
            foreach($baseData as $k=>$v){
                $click = 0;  //外网
                $click2 = 0; //内网
                foreach($v as $k2=>$v2){
                    $temp = array();
                    //$temp['id'] = $v2['id'];
                    $temp[0] = $v2['click']+$v2['_click'];
                    $temp[1] = $v2['click2']+$v2['_click2'];
                    $click+=$temp[0];
                    $click2+=$temp[1];
                    $rs[$k][$v2['id']] =  $temp;
                }
                $rs[$k][0][0] =  $click;
                $rs[$k][0][1] =  $click2;
            }

            return $rs;
        }else{
            return false;
        }

    }


    protected  function getBaseData(){

        $m = new ArctypeModel();
        $channelM= M("Channel");
        $temp = array();
        $temp = $channelM->field("id,addtable,type,issystem")->select();
        $channelArr = array();
        foreach($temp as $v){
            $channelArr[$v['id']] = $v;
        }

        $arctypeArr = $m->field("id,fid,name,channel,type,status,siteid,status,click,click2")->select();

        $siteArr = array();

        foreach( $arctypeArr as $v ){

            $channel = $channelArr[$v['channel']];
            if(!empty($channel)){
                $map = array();
                if($channel['issystem']==1 && $channel['type'] == 1){
                    $table = "archives";
                    $map['typeid'] = array("eq",$v['id']);
                }else if($channel['issystem']==1 && ($channel['type'] == 2 || $channel['type'] == 3)   ){
                    $table = $channel['addtable'];
                    $map['typeid'] = array("eq",$v['id']);
                }else{
                    continue;
                }

                $tableM = M($table);
                $arctype = array();
                $arctype['name'] = $v['name'];
                $arctype['id'] = $v['id'];
                $arctype['fid'] = $v['fid'];
                $arctype['isparent'] = $m->isParent($v['id'],$v['siteid']);
                $arctype['_click'] = intval($v['click']);
                $arctype['_click2'] = intval($v['click2']);
                $arctype['status'] = $v['status'];
                $arctype['click'] = intval($tableM->where($map)->sum("click"));
                $arctype['click2'] = intval($tableM->where($map)->sum("click2"));

                $siteArr[$v['siteid']][$v['id']] = $arctype;
            }
        }

        foreach($siteArr as $k=>$v){
            $this->parentPv($siteArr[$k]);
        }

        //echo serialize($siteArr);
        //dump($siteArr);
        return $siteArr;
    }


    //parentPv父栏目
    protected function parentPv(&$siteArr,$cursor=null){

        if(!empty($siteArr)){
            $m = new ArctypeModel();

            if($cursor === null){
                foreach($siteArr as $k=>$v){
                    if( $v['isparent'] && $v['fid'] == 0 ){
                        foreach($siteArr as $k2=>$v2){
                            if($v2['fid'] == $v['id'] && !$v2['isparent'] ){
                                $siteArr[$k]['click'] += $v2['click'];
                                $siteArr[$k]['click2'] += $v2['click2'];
                                $siteArr[$k]['_click'] += $v2['_click'];
                                $siteArr[$k]['_click2'] += $v2['_click2'];
                            }else if($v2['fid'] == $v['id'] && $v2['isparent'] ){
                                $this->parentPv($siteArr,$v2['id']);
                            }else{
                                continue;
                            }
                        }
                    }else{
                        continue;
                    }
                }
            }else{
                foreach($siteArr as $k2=>$v2){
                    if($v2['fid'] == $cursor && !$v2['isparent'] ){
                        $siteArr[$cursor]['click'] += $v2['click'];
                        $siteArr[$cursor]['click2'] += $v2['click2'];
                        $siteArr[$cursor]['_click'] += $v2['_click'];
                        $siteArr[$cursor]['_click2'] += $v2['_click2'];
                    }else if($v2['fid'] == $cursor && $v2['isparent'] ){
                        $this->parentPv($siteArr,$v2['id']);
                        $siteArr[$cursor]['click'] += $siteArr[$k2]['click'];
                        $siteArr[$cursor]['click2'] += $siteArr[$k2]['click2'];
                        $siteArr[$cursor]['_click'] += $siteArr[$k2]['_click'];
                        $siteArr[$cursor]['_click2'] += $siteArr[$k2]['_click2'];
                    }else{
                        continue;
                    }
                }
            }

        }

        return;

    }



}


class ContentStatistics extends Statistics{

    protected function getBaseData(){

    }

    protected function getRecordData(){

    }

}
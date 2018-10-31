<?php

	/**
	 * 统计管理
	 * @author Administrator
	 *
	 */

    import('@.Class.Statistics');

	class StatisticsAction extends BaseAction{

            public function getDataJson(){
                $sid = $this->_param("sid");
                $sid = !empty($sid)?$sid:1;
                $tid = $this->_param("tid");
                $tid = !empty($tid)?$tid:0;
                $sdate = $this->_param("sdate");
                $edate = $this->_param("edate");


                $m = new ArctypeModel();
                $arctypeArr = $m->where("id=$tid")->find();
                if(empty($arctypeArr)){
                    return;
                }else{
                    $cM = new ChannelModel();
                    $channel = $cM->getOne($arctypeArr['channel']);

                    if($channel['issystem'] == 2){
                        return;
                    }else{
                        switch($channel['type']){
                            case 1 : {
                                $tjM = new ArchivesModel();break;
                            }
                            case 2 : {
                                $tjM = M($channel['addtable']);break;
                            }
                            case 3 : {
                                $tjM = M($channel['addtable']);break;
                            }
                        }

                        $stime = strtotime($sdate);
                        $etime = strtotime($edate);
                        $oneDayTime = 3600*24;

                        $map = array();
                        $map['typeid'] = array("eq",$tid);
                        $map['siteid'] = array("eq",$sid);
                        $map['senddate'] = array("BETWEEN",array($stime,$etime));
                        $arr = $tjM->field("id,senddate")->where($map)->select();
                        $arr2 = array();

                        foreach($arr as $v){
                            if(isset($arr2[date('Y-m-d',$v['senddate'])])){
                                $arr2[date('Y-m-d',$v['senddate'])] += 1;
                            }else{
                                $arr2[date('Y-m-d',$v['senddate'])] = 1;
                            }
                        }

                        $rs = array();
                        for($i = $stime;$i<=$etime;$i=$i+$oneDayTime){
                            $temp = array();
                            $temp[0] = $i*1000;
                            $key = date("Y-m-d",$i);
                            if(array_key_exists($key,$arr2)){
                                $temp[1] = $arr2[$key];
                            }else{
                                $temp[1] = 0;
                            }
                            $rs[] = $temp;
                        }

                        echo  json_encode($rs);
                    }

                }
            }



            public function getPvJson(){
                $sid = $this->_param("sid");
                $sid = !empty($sid)?$sid:1;

                $tid = $this->_param("tid");
                $tid = !empty($tid)?$tid:0;

                $nettype = $this->_param("nettype");
                $nettype = isset($nettype)?$nettype:1;

                $datatype = $this->_param("datatype");
                $datatype = !empty($datatype)?$datatype:1;


                $param['type'] = $datatype;
                $pv = new PvStatistics();
                $arr = $pv->readRecordData($param);
                $rs = array();
                foreach($arr as $v){
                    $temp = array();
                    $temp[] = $v['time']*1000;
                    $temp[] = $v['record'][$sid][$tid][$nettype];
                    $rs[] = $temp;
                }
                echo json_encode($rs);

            }


            public function refreshPv(){
                $pv = new PvStatistics();
                $pv->refreshPv();
            }

            public function updatePv(){
                $date = $this->_param("date");
                if(empty($date)) $date = date("Y-m-d",time());
                $pv = new PvStatistics();
                $pv->refreshPv($date);
            }


            //访问量统计
		    public function pv(){
                $sid = $this->_param("sid");
                $sid = !empty($sid)?$sid:1;

                $tid = $this->_param("tid");
                $tid = !empty($tid)?$tid:0;

                $nettype = $this->_param("nettype");
                $nettype = isset($nettype)?$nettype:3;

                $datatype = $this->_param("datatype");
                $datatype = !empty($datatype)?$datatype:1;

                $netTypeArr = array(1=>"内网",0=>"外网",2=>"内外网");




                //栏目分析
                $m = new ArctypeModel();
                $isparant = $m->isParent($tid,$sid);

                if($isparant){
                    $map = array();
                    $map['siteid'] = array("eq",$sid);
                    $map['fid'] = array("eq",$tid);
                    $arr = $m->field("id,name")->where($map)->select();
                }else{
                    $arr = $m->field("id,name")->where("id=$tid")->select();
                }

                if(!empty($arr)){
                    $arctypeJson = json_encode($arr);
                }else{
                    $arctypeJson = "null";
                }

                $param['tjson'] = $arctypeJson;


                //网段选择
                if($isparant){
                    if($nettype == 2) $nettype = 0;
                    unset($netTypeArr[2]);
                }

                $nettypeOptions = "";
                foreach($netTypeArr as $k=>$v){
                    if($nettype == $k) $flag = "selected='selected'"; else $flag="";
                    $nettypeOptions.='<option value="'.$k.'" '.$flag.' >'.$v.'</option>';
                }
                $this->assign("nettypeOptions",$nettypeOptions);

                //站点选择
                $siteArr = getAvailableSitesArr();
                $siteOptions = "";
                foreach($siteArr as $v){
                    if($sid == $v['id']) $flag="selected='selected'"; else $flag="";
                    $siteOptions.='<option value="'.$v['id'].'" '.$flag.' >'.$v['name'].'</option>';
                }
                $this->assign('siteOptions',$siteOptions);




                //内容移动到的栏目选择
                $m3 = new ArctypeModel();
                $selectionArr = $m3->arctypeArr($sid);
                $arctypeOptions = '';
                foreach($selectionArr as $k => $v){
                    if($k==$tid) $checked = 'selected style="background:#DBDBDB;" '; else $checked = '';
                    $arctypeOptions .="<option value='$k' $checked >$v</option>";

                }
                $this->assign('arctypeOptions',$arctypeOptions);


                $param['sid'] = $sid;
                $param['tid'] = $tid;
                $param['title'] = getArctypeName($tid,$sid);
                $param['nettype'] = $nettype;
                $param['datatype'] = $datatype;
                $param['netname'] = $netTypeArr[$nettype];
                $this->assign("param",$param);

                /*position指定以及一些问候信息*/
                $current = "访问统计";
                $position = getPosition("访问统计");
                $this->assign('current',$current);
                $this->assign('position',$position);
                $this->assign('welcome',getWelcome());
                $this->display();
            }


            //内容统计
            public function data(){
                $sid = $this->_param("sid");
                $sid = !empty($sid)?$sid:1;

                $tid = $this->_param("tid");
                $tid = !empty($tid)?$tid:0;

                $sdate = $this->_param('sdate');
                $edate = $this->_param('edate');

                if(empty($sdate)) $sdate = date('Y-m-d',time()-3600*24*30);
                if(empty($edate)) $edate = date('Y-m-d',time());



                //栏目分析
                $m = new ArctypeModel();
                $isparant = $m->isParent($tid,$sid);

                if($isparant){
                    $map = array();
                    $map['siteid'] = array("eq",$sid);
                    $map['fid'] = array("eq",$tid);
                    $arr = $m->field("id,name")->where($map)->select();
                }else{
                    $arr = $m->field("id,name")->where("id=$tid")->select();
                }

                if(!empty($arr)){
                    $arctypeJson = json_encode($arr);
                }else{
                    $arctypeJson = "null";
                }

                $param['tjson'] = $arctypeJson;



                //站点选择
                $siteArr = getAvailableSitesArr();
                $siteOptions = "";
                foreach($siteArr as $v){
                    if($sid == $v['id']) $flag="selected='selected'"; else $flag="";
                    $siteOptions.='<option value="'.$v['id'].'" '.$flag.' >'.$v['name'].'</option>';
                }
                $this->assign('siteOptions',$siteOptions);


                //内容移动到的栏目选择
                $m3 = new ArctypeModel();
                $selectionArr = $m3->arctypeArr($sid);
                $arctypeOptions = '';
                foreach($selectionArr as $k => $v){
                    if($k==$tid) $checked = 'selected style="background:#DBDBDB;" '; else $checked = '';
                    $arctypeOptions .="<option value='$k' $checked >$v</option>";

                }
                $this->assign('arctypeOptions',$arctypeOptions);


                $param['sid'] = $sid;
                $param['tid'] = $tid;
                $param['sdate'] = $sdate;
                $param['edate'] = $edate;
                $param['dateinfo'] = $sdate.' 至 '.$edate;
                $param['title'] = getArctypeName($tid,$sid);


                $this->assign("param",$param);

                /*position指定以及一些问候信息*/
                $current = "内容统计";
                $position = getPosition("内容统计");
                $this->assign('current',$current);
                $this->assign('position',$position);
                $this->assign('welcome',getWelcome());
                $this->display();

            }



        public function faq(){
            $sid = $this->_param("sid");
            $sid = !empty($sid)?$sid:1;


            //站点选择
            $siteArr = getAvailableSitesArr();
            $siteOptions = "";
            foreach($siteArr as $v){
                if($sid == $v['id']) $flag="selected='selected'"; else $flag="";
                $siteOptions.='<option value="'.$v['id'].'" '.$flag.' >'.$v['name'].'</option>';
            }
            $this->assign('siteOptions',$siteOptions);
            $param['sid'] = $sid;
            $this->assign("param",$param);


            $m = M("Faq");
            $map = array();
            $map['siteid'] = array("eq",$sid);
            $tj = array(0=>0,1=>0,2=>0,3=>0);
            $arr = $m->field("id,time,edittime,reply")->where($map)->select();
            foreach($arr as $v){
                if(empty($v['edittime'])){
                    $tj[1]++;  //未回复
                }else if( !empty($v['edittime']) && !empty($v['reply']) && $v['edittime']-$v['time']<3600*24*7  ){
                    $tj[2]++; //有效回复
                }else{
                    $tj[3]++;  //无效回复
                }
                $tj[0]++;
            }


            require(APP_INC_PATH.'chart/FusionCharts.php');
            $strXML  = "<graph animation='0' caption='留言总数：".$tj[0]."' subCaption='' showPercentValues='1' pieSliceDepth='10' showNames='1' decimalPrecision='0' baseFontSize='16' shadowAlpha='80' baseFontColor='434345' rotateNames='0'  >";
            $strXML .= "<set name='未回复' value='".$tj[1]."' />";
            $strXML .= "<set name='有效回复' value='".$tj[2]."' />";
            $strXML .= "<set name='无效回复' value='".$tj[3]."' />";
            $strXML .= "</graph>";

            $chart_html = renderChart("__INC__/chart/FCF_Pie2D.swf", "", $strXML, "", 400, 300);

            $this->assign('chart_html',$chart_html);



            /*position指定以及一些问候信息*/
            $current = "医患交流统计";
            $position = getPosition("医患交流统计");
            $this->assign('current',$current);
            $this->assign('position',$position);
            $this->assign('welcome',getWelcome());
            $this->display();

        }


	}





























?>
<?php

$config = require '../../Conf/db.php';


$db = mysql_connect($config['DB_HOST'],$config['DB_USER'],$config['DB_PWD']);
mysql_select_db($config['DB_NAME'],$db);

$table = $_POST['table'];
$fid = $_POST['fid'];
$id = $_POST['id'];
$name = $_POST['name'];
$nfid = $_POST['nfid'];
$value = $_POST['value'];
$action = $_POST['action'];
mysql_query('set names gbk');


if($action==1){

    $sql = "select * from $table where `$fid` = $nfid  order by `order` desc";
    $rs = mysql_query($sql,$db);
    if($rs){
    $c = array();
    while($arr = mysql_fetch_array($rs)){
        $t='';
        $t['name'] = iconv('gbk','utf-8',$arr[$name]);
        $t['value'] = $arr[$value];
        $c[] = $t;

    }
    print_r(json_encode($c));
    }else echo "none";
}else if($action==2){
    $zid = $_POST['zid'];
    
            $c2 = array();
            $selected = array();
            $count = 0;
            if(justDo($zid)==$nfid);
            else while(justDo($selected2[$count-1]['fid'])!=$nfid){}
            $c2=array_reverse($c2);
            $selected2=array_reverse($selected2);
            $json['select'] = $c2;
            $json['selected'] = $selected2;
            echo json_encode($json);
       
}else;



    function justDo($zid2){
        global $db,$id,$fid,$name,$value,$table,$c2,$selected2,$count;
        $sql = "select * from $table where `$id` =".$zid2;
        $rs = mysql_query($sql,$db);
        $selected=array();
        if($rs){
        while($arr = mysql_fetch_array($rs)){
            $selected['name'] = iconv('gbk','utf-8',$arr[$name]);
            $selected['value'] = $arr[$value];
            $selected['id'] =  $arr[$id];
            $selected['fid'] = $arr[$fid];
        }
        
        $selected2[$count] = $selected;
        }
    
        $sql = "select * from $table where `$fid` =".$selected['fid']." order by `order` desc";
        $rs = mysql_query($sql,$db);
        $temp = array();
        if($rs){
            while($arr = mysql_fetch_array($rs)){
                $t='';
                $t['name'] = iconv('gbk','utf-8',$arr[$name]);
                $t['value'] = $arr[$value];
                $temp[] =  $t;
            }
            $c2[$count] = $temp;
        }
    $count++;
    return $selected['fid'];
    
    }


?>
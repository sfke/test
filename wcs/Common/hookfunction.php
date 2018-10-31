<?php
/*
 * 模板标签构造函数示例
 */

function __where_demo__($map){

}

function __orderby_demo__($orderby){

}

function __result_demo__($result){

}

function __sql_demo__($sql, $args){
    $m = M();
    $sql = $m->table(C('DB_PREFIX').'archives arc, '.C('DB_PREFIX').'addnews news')->where('arc.id = news.aid')->order('arc.sortrank desc')->buildSql();
}

//栏目表,实现分页 查询示例
function __process_list__(&$rsArr, $args){
	
    //先清空Cache ,让 栏目表分页 及时生效
    if (is_dir(CACHE_PATH)) {
        mydel(CACHE_PATH);
    }
	
    $m = M();
    $pagesize = ($args['pagesize'] != '') ? $args['pagesize'] : '10';
    $count = $m->table(C('DB_PREFIX').'arctype')->where('fid='.$_SESSION['tid'])->order('id asc')->count();
    $Page = new Page($count, $pagesize, 'tid='.$_SESSION['tid']);
    $show = $Page->show();
    $rsArr = $m->table(C('DB_PREFIX').'arctype')->where('fid='.$_SESSION['tid'])->order('id asc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
    if (!empty($rsArr)) {
        foreach ($rsArr as $k => $v) {
            $rsArr[$k]['url'] = listUrl($v['id']);
        }
    }
	$rsArr['pageline'] = $show;
}

//留言表查询示例
function __process_msg__(&$rsArr, $args){
    $m = M();
    $pagesize = ($args['pagesize'] != '') ? $args['pagesize'] : '10';
    $count = $m->table(C('DB_PREFIX').'msg')->where('`check`="已审核"')->order('id desc')->count();
    $Page = new Page($count, $pagesize, 'tid='.$_SESSION['tid']);
    $show = $Page->show();
    $rsArr = $m->table(C('DB_PREFIX').'msg')->where('`check`="已审核"')->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
    $rsArr['pageline'] = $show;
}

//2个表联合查询示例
function __process_demo__(&$rsArr, $args){
    $m = M();
    $pagesize = ($args['pagesize'] != '') ? $args['pagesize'] : '10';
    $count = $m->table(C('DB_PREFIX').'archives arc, '.C('DB_PREFIX').'addnews news')->where('arc.id = news.aid')->order('arc.sortrank desc')->count();
    $Page = new Page($count, $pagesize, 'tid='.$_SESSION['tid']);
    $show = $Page->show();
    $rsArr = $m->table(C('DB_PREFIX').'archives arc, '.C('DB_PREFIX').'addnews news')->where('arc.id = news.aid')->order('arc.sortrank desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
    if (!empty($rsArr)) {
        foreach ($rsArr as $k => $v) {
            $rsArr[$k]['url'] = contentUrl($v['id']);
        }
    }
    $rsArr['pageline'] = $show;
}

//3个表联合查询示例
function __process_demo2__(&$rsArr, $args){
    $m = M();
    $pagesize = ($args['pagesize'] != '') ? $args['pagesize'] : '10';
  
	$count = $m->query('SELECT COUNT(*) AS tp_count FROM '.C('DB_PREFIX').'archives arc,'.C('DB_PREFIX').'addproduct pro,'.C('DB_PREFIX').'arctype arct WHERE ( arc.id = pro.aid and arct.fid=2 and arct.id=arc.typeid ) ORDER BY arct.order , arc.sortrank desc LIMIT 1');
	
	$count=$count[0]['tp_count'];
	 
    $Page = new Page($count, $pagesize, 'tid='.$_SESSION['tid']);
    $show = $Page->show();
 
      $rsArr = $m->query('SELECT arc.id,arc.title,pro.img,pro.txt FROM '.C('DB_PREFIX').'archives arc,'.C('DB_PREFIX').'addproduct pro,'.C('DB_PREFIX').'arctype arct WHERE ( arc.id = pro.aid and arct.fid=2 and arct.id=arc.typeid ) ORDER BY arct.order desc , arc.sortrank desc LIMIT '. $Page->firstRow . ',' . $Page->listRows);
	 
	
    if (!empty($rsArr)) {
        foreach ($rsArr as $k => $v) {
            $rsArr[$k]['url'] = contentUrl($v['id']);
        }
    }
    $rsArr['pageline'] = $show;
}

/*
 * 自动表单钩子函数
 */
function __select__(&$rs){
    $rs = array(1 => 1, 2 => 2, 3 => 3);
    return;
}

function __checkbox__(&$rs){
    $rs = array(1 => 1, 2 => 2, 3 => 3);
    return;
}

function __radio__(&$rs){
    $rs = array(1 => 1, 2 => 2, 3 => 3);
    return;
}

/*获取会员数组,可供 select input 使用 关联*/
function __member__(&$rs){
    $m = M();
	$rsArr = $m->query('SELECT userid  FROM '.C('DB_PREFIX').'member WHERE ( 1=1 ) ORDER BY id desc');
	 if (!empty($rsArr)) {		  
           foreach ($rsArr as $k => $v) {
			 if($k>0){
				$rsstr=$rsstr."|";
			 }
			 $rsstr=$rsstr.$rsArr[$k]['userid'];
           }
    } 
	$rsvalue= explode("|",$rsstr);
	$rsname= $rsvalue;
	$rs=array_combine($rsname,$rsvalue);
    return;
}
?>
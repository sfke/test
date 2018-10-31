<?php
$action = $_POST['action'];

if($action == 'get_data'){
	$type = $_POST['type'];
	$path = $_POST['root']?iconv('utf-8','gbk',$_POST['root']):'/';
	$dir = @ dir($path);

	//列出 images 目录中的文件
	while (($file = $dir->read()) !== false)
	  {		if($file=='.') continue;
			//只显示目录
			if($type==2){
			if(filetype($path.$file)!='dir') continue;
			}
			if($type==3){
			if(filetype($path.$file)!='file') continue;
			}

			$arr['name'] = iconv('gbk','utf-8',$file);
			$arr['type'] = filetype($path.$file);
			$arr['size'] = getFileSize(filesize($path.$file));
			$arr['lastmod'] = date('Y/m/d          H:i:s',filectime($path.$file));
			if(filetype($path.$file)=='dir'){
			$s[]=$arr;
			}else {
			$ss[]=$arr;
			}
			//$s[]=$arr;

	  }
	  if(!empty($ss)){
		$s=array_merge($s,$ss);}
	echo json_encode($s);

}else
if($action == 'file_del'){
	$path = iconv('utf-8','gbk',$_POST['root']);
	$name = iconv('utf-8','gbk',$_POST['name']);
	$type = $_POST['type'];

	if($type == 'dir'){
		if(@rmdir($path.$name.'/')){
			echo 1; //删除文件夹成功
		}else{
			echo 2; //删除文件夹失败
		}
	}else{
		if(@unlink($path.$name)){
			echo 3; //删除文件成功
		}else{
			echo 4; //删除文件失败
		}
	}

}else
if($action == 'file_rename'){
	$path = iconv('utf-8','gbk',$_POST['root']);
	$name = iconv('utf-8','gbk',$_POST['name']);
	$type = $_POST['type'];
	$newname = iconv('utf-8','gbk',$_POST['newname']);
 
	if(@rename($path.$name,$path.$newname)){
		echo 5; //重命名成功
	}else{
		echo 6; //重命名失败
	}
}else
if($action == 'file_new'){
	$path = iconv('utf-8','gbk',$_POST['root']);
	$newname = iconv('utf-8','gbk',$_POST['newname']);

	if(@mkdir($path.$newname)){
		echo 7; //成功
	}else{
		echo 8; //失败
	}
}else
if($action == 'file_edit'){
	$path = iconv('utf-8','gbk',$_POST['root']);
	$name = iconv('utf-8','gbk',$_POST['name']);
	
	//session_id(SID);
    session_start();
	$_SESSION['fpath']=$path.$name;
	
	$opts= array('http'=> array( 
       'method' => 'POST', 
       //'header' => "Content-Type:text/xml"."rn". "Authorization:Basic ".base64_encode("$https_user:$https_password")."rn", 
       'header' => "Content-Type:text/xml\r\n", 
	   'timeout'=> 60 
       ) 
    ); 
    $context = stream_context_create($opts); 
    
	$getcontext=file_get_contents($path.$name,false, $context,-1, 100000);
	$getcontext=str_ireplace('<script','< script',$getcontext); 
	$getcontext=str_ireplace('<style','< style',$getcontext);
	echo $getcontext; 
} 





function getFileSize($num){

$num=empty($num)?" ":$num;

if($num==' ') return $num;

else if ($num>=pow(1024,3)){
	return strval(intval($num/pow(1024,3)))."GB";

}
else if($num>=pow(1024,2)){
	return strval(intval($num/pow(1024,2)))."MB";
}

else if($num>=1024){
	return strval(intval($num/1024))."KB";

}

else return strval($num).'B';


}
?>
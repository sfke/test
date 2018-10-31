<?php

require("smtp.php"); 

//使用163邮箱服务器

$smtpserver = "smtp.163.com";


//163邮箱服务器端口 

$smtpserverport = 25;


//你的163服务器邮箱账号

$smtpusermail = "";


//收件人邮箱

$smtpemailto = " ";


//你的邮箱账号(去掉@163.com)

$smtpuser = "";//SMTP服务器的用户帐号 


//你的邮箱密码

$smtppass = ""; //SMTP服务器的用户密码 

//邮件主题 

$mailsubject = "程序邮件发送";

//邮件内容 

$mailbody = "邮件内容:"."\n"."http://www.121ask.com";

//邮件格式（HTML/TXT）,TXT为文本邮件 

$mailtype = "TXT";

//这里面的一个true是表示使用身份验证,否则不使用身份验证. 

$smtp = new smtp($smtpserver,$smtpserverport,true,$smtpuser,$smtppass);

//是否显示发送的调试信息 

//$smtp->debug = TRUE;

//发送邮件

$rs=$smtp->sendmail($smtpemailto, $smtpusermail, $mailsubject, $mailbody, $mailtype); 
if($rs==TRUE){
  echo "ok";
}

?>
<?php  
$config = require dirname(__FILE__) .'/../../Conf/sysconfigs.php';
require dirname(__FILE__).'../class.phpmailer.php';  
try {  
	$mail = new PHPMailer(true); //建立实例  
	$mail->CharSet = "utf8";
    //$body             = file_get_contents(dirname(__FILE__).'/contents.html');  
    //$body             = preg_replace('/\\\\/','', $body); //替换掉变量body里的反斜线  
    $mail->IsSMTP();                           // 使用SMTP  
    $mail->SMTPAuth   = true;                  // 使用SMTP认证  
    $mail->Port       = 25;                    // 设置SMTP服务器的端口号  
    $mail->Host       = "smtp.qq.com"; // SMTP服务器地址  
    $mail->Username   = $config['SYS_EMAIL_ACCOUNT'];     // SMTP服务器用户名  这里注意163的邮箱用户名不带@163.com其他的都带  
    $mail->Password   = $config['SYS_EMAIL_PWD'];            // SMTP服务器密码  
    $mail->SetLanguage('zh_cn');     //设置错误信息语言为简体中文  
    //$mail->IsSendmail();  这里我们没有Sendmail组件，所以不使用  
    $mail->AddReplyTo($config['SYS_EMAIL_ACCOUNT']."@qq.com",C('JL_WEBNAME'));    //回复的邮件地址  
    $mail->From       = $config['SYS_EMAIL_ACCOUNT']."@qq.com"; //邮件发送人  
    $fromName   = C('JL_WEBNAME');  
    $mail->FromName   = "=?UTF-8?B?".base64_encode($fromName)."?=";
    //$to = "78062919@qq.com";  
    //$mail->AddAddress($to);  //邮件的发送地址  
    $subject  = "密码找回系统（".C('JL_WEBNAME')."）"; //邮件的标题  
    $mail->Subject = "=?UTF-8?B?".base64_encode($subject)."?=";
    //$mail->AltBody    = "附加信息，可以略过的";  
    $mail->WordWrap   = 80; //设置换行  
    //$mail->MsgHTML($body);   //邮件的内容  
    $mail->IsHTML(true); // 作为HTML格式发送电子邮件  
    //$mail->Send();  
    //echo '发送电子邮件成功';  
} catch (phpmailerException $e) {  
    echo $e->errorMessage();  
}  

?>
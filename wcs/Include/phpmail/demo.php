<?php  
require '../class.phpmailer.php';  
$pw = "wybzybhwjmm."; 
try {  
	$mail = new PHPMailer(true); //建立实例  
    $body             = file_get_contents('contents.html');  
    $body             = preg_replace('/\\\\/','', $body); //替换掉变量body里的反斜线  
    $mail->IsSMTP();                           // 使用SMTP  
    $mail->SMTPAuth   = true;                  // 使用SMTP认证  
    $mail->Port       = 25;                    // 设置SMTP服务器的端口号  
    $mail->Host       = "smtp.163.com"; // SMTP服务器地址  
    $mail->Username   = "liuyun38";     // SMTP服务器用户名  这里注意163的邮箱用户名不带@163.com其他的都带  
    $mail->Password   = "$pw";            // SMTP服务器密码  
    $mail->SetLanguage('zh_cn');     //设置错误信息语言为简体中文  
    //$mail->IsSendmail();  这里我们没有Sendmail组件，所以不使用  
    $mail->AddReplyTo("liuyun38@163.com","小龙");    //回复的邮件地址  
    $mail->From       = "liuyun38@163.com"; //邮件发送人  
    $mail->FromName   = "夏丰";  
    $to = "67686126@qq.com";  
    $mail->AddAddress($to);  //邮件的发送地址  
    $mail->Subject  = "你被监控了"; //邮件的标题  
    //$mail->AltBody    = "附加信息，可以略过的";  
    $mail->WordWrap   = 80; //设置换行  
    $mail->MsgHTML($body);   //邮件的内容  
    $mail->IsHTML(true); // 作为HTML格式发送电子邮件  
    $mail->Send();  
    echo '发送电子邮件成功';  
} catch (phpmailerException $e) {  
    echo $e->errorMessage();  
}  
?>
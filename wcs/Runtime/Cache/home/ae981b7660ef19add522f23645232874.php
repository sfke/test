<?php if (!defined('THINK_PATH')) exit();?>﻿<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><meta http-equiv='refresh' content='<?php echo ($waitSecond); ?>; url=<?php echo ($jumpUrl); ?>' /><title>提交跳转提示信息</title><meta name="Keywords" content="<?php echo ($jl_key); ?>"><meta name="Description" content="<?php echo ($jl_desc); ?>"><link href="__BASE__/css/css.css" rel="stylesheet" type="text/css"><style>
body{ background: #fff; font-family: '微软雅黑'; color: #109A8B; font-size: 14px; }
.system-message{ padding: 24px 48px; width:600px; margin:0px auto; }
.system-message h1{ font-size: 24px; font-weight: normal; line-height: 48px; margin-bottom: 12px; }
.system-message .jump{ padding-top: 10px}
.system-message .jump a{ color: #333;}
.system-message .success,.system-message .error{ line-height: 1.8em; font-size: 36px }
.system-message .detail{ font-size: 12px; line-height: 20px; margin-top: 12px; display:none}
</style></head><body><!--banner开始--><div class="system-message" style="min-height:400px;"><?php if(isset($message)): ?><h1>恭喜！</h1><p class="success"><?php echo($message); ?></p><?php else: ?><h1>出错啦！</h1><p class="error"><?php echo($error); ?></p><?php endif; ?><p class="detail"></p><p class="jump">
页面自动 <a id="href" href="<?php echo($jumpUrl); ?>">跳转</a> 等待时间： <b id="wait"><?php echo($waitSecond); ?></b></p></div><script type="text/javascript">
(function(){
var wait = document.getElementById('wait'),href = document.getElementById('href').href;
var interval = setInterval(function(){
	var time = --wait.innerHTML;
	if(time <= 0) {
		location.href = href;
		clearInterval(interval);
	};
}, 1000);
})();
</script></body></html>
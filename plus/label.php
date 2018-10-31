<!DOCTYPE html>
<html>
<head>
<title>JLWCS系统标签示例</title>
<meta charset="utf-8">
<style type="text/css">
body{font-family:Arial,padding:0;margin:margin:10px}
div{margin-left:5px;}
h3{color:#F00;}
p{margin-left:5px;}
h4{color:#090}
p h5{margin-left:5px;}
</style>
</head>
<body>
<h3>页面通用标签：</h3>
<div>
     <p>
     <?php
     echo nl2br(htmlentities("<title>{\$jl_title}</title>
     <meta name=\"Keywords\" content=\"{\$jl_key}\">
     <meta name=\"Description\" content=\"{\$jl_desc}\">",ENT_QUOTES,"utf-8"));
	 ?>
     </p>
</div>
<h3>一、导航标签：</h3>
<div>
     <h4>1.普通导航标签</h4>
     <p>
     <?php
     echo nl2br(htmlentities("<ul>
        <li><a href=\"{:indexUrl()}\">网站首页</a></li>
        <arctype typeid='0' orderway='asc' titlelen = '6' >
            <li><a href=\"{\$field['url']}\">{\$field['name']}</a></li>
        </arctype>
      </ul>
	  
	  ⊙设置当前一级栏目状态
	  
	  <?php
		\$firstTid = !empty( \$arctype['route'] )?array_shift( array_slice (explode('-',\$arctype['route']),1,1) ):'';
	  ?>
      <li class='<if condition=\"\$arctype['fid'] eq ''\">on</if>'><a href=\"{:indexUrl()}\">首页</a></li>
      <arctype typeid='0' orderway='asc' titlelen = '6' >
        <li class=\"<eq name='firstTid' value='\$field[id]'>on</eq>\"><a href=\"{\$field['url']}\">{\$field['name']}</a></li>
      </arctype>
	  ",ENT_QUOTES,"utf-8"));
	 ?>
     </p>
</div>
<div>
     <h4>2.嵌套导航标签</h4>
     <p>
     <?php
     echo nl2br(htmlentities("<ul>
        <li><a href=\"{:indexUrl()}\">网站首页</a></li> 
        <arctype typeid='0' titlelen = '30'> 
        <li><a href=\"{\$field['url']}\">{\$field['name']}</a>              
             <arctype typeid=\"\$field['id']\" titlelen = '6' field=\"field2\" >
				  <if condition=\"\$index eq 0\"><ul></if>
                    <li><a href=\"{\$field2['url']}\">{\$field2['name']}</a></li> 
				  <if condition=\"(\$index+1) eq count(\$arr)\"></ul></if>
            </arctype>              
        </li> 
        </arctype>
      </ul>",ENT_QUOTES,"utf-8"));
	 ?>
     </p>
</div>
<div>
     <h4>3.侧边栏导航标签</h4>
     <p>
     <?php
	 echo "⊙调用子栏目导航(可采用 type=\"brother\" 列出所有同级栏目,type=\"parent\" 列出所有父级栏目)<br/>";
     echo nl2br(htmlentities("<ul>
         <arctype typeid=\"\$tid\" titlelen = '6' field=\"field2\" >
            <li><a href=\"{\$field2['url']}\" <eq name=\"arctype['id']\" value=\"\$field2['id']\" >class=\"cur\"</eq> >{\$field2['name']}</a></li> 
         </arctype>
      </ul>",ENT_QUOTES,"utf-8"));
	  echo "<br/>⊙子栏目调用同级导航<br/>";
	  echo nl2br(htmlentities("<ul>
         <arctype typeid=\"\$arctype['fid']\" titlelen = '6' field=\"field2\" >
            <li><a href=\"{\$field2['url']}\" <if condition=\"\$arctype['id'] eq \$field2['id']\">class=\"cur\"<else/> </if> >{\$field2['name']}</a></li> 
         </arctype>
      </ul>",ENT_QUOTES,"utf-8"));
	   echo "<br/>⊙子栏目调用同级导航（包含下级导航）<br/>";
	  echo nl2br(htmlentities("<ul>
         <arctype typeid=\"\$arctype['fid']\" titlelen = '6' field=\"field2\" >
            <li><a href=\"{\$field2['url']}\" <if condition=\"\$arctype['id'] eq \$field2['id']\">class=\"cur\"<else/> </if> >{\$field2['name']}</a></li>
			 <arctype typeid=\"\$field2['id']\" titlelen = '6' field=\"field3\">
                             <if condition=\"\$index+1 eq 1 \"><div class=\"classid\"><else/></if>                                                     
                                <p> <a href=\"{\$field3['url']}\">{\$field3['name']}</a></p>
                             <if condition=\"\$index+1 eq count(\$arr)\"></div><else/></if> 
                         </arctype> 
         </arctype>
      </ul>",ENT_QUOTES,"utf-8"));
	 ?>
     </p>
</div>

<h3>二、通用列表标签：</h3>
<div>
     <h4>1.常见用法</h4>
     <p>
     <?php
     echo nl2br(htmlentities("<ul>
         <arclist typeid=\"1\" row=\"4\" titlelen=\"12\" addfield=\"on\">
           <li><a href=\"{\$field['url']}\" target=\"_blank\" ><img src=\"{\$field['img']|default=\$defaultimg}\" /><br/>{\$field['title']}</a></li>
         </arclist> 
      </ul>",ENT_QUOTES,"utf-8"));
	 ?>
     </p>
     <h4>2.嵌套循环，typeid 使用变量</h4>
     <p>
     <?php
     echo nl2br(htmlentities("<arctype typeid=\"28\" limit=\"1,4\" >
        <ul>
           <arclist typeid=\"\$field['id']\" titlelen=\"12\" field=\"field2\" orderby=\"pubdate\" orderway=\"desc\" >
             <li><a href=\"{\$field2['url']}\" target=\"_blank\" ><span>{:jldate(\$field2['pubdate'])}</span>{\$field2['title']}</a></li>
           </arclist>
         </ul>
     </arctype>",ENT_QUOTES,"utf-8"));
	 ?>
     </p>
     <h4>3.适用于<font color="#0000FF">不包含主表</font>的系统模型</h4>
     <p>
     <?php
     echo nl2br(htmlentities("<loop typeid=\"20\" table=\"addprice\" where=\"status=1\" orderby=\"sortrank\" orderway=\"desc\">
      {\$field['title']} 
     </loop> ",ENT_QUOTES,"utf-8"));
	 ?>
     </p>
     <h4>4.图片列表</h4>
     <p>

     <?php
     echo nl2br(htmlentities("<imglist >
       <li> 
        <a href=\"{\$field['url']}\">
            <img src=\"{\$field['url']}\" title=\"{\$field['intro']}\" alt=\"{\$field['intro']}\" />
        </a> 
       </li>
      </imglist>",ENT_QUOTES,"utf-8"));
	 ?>
     </p>
</div>

<h3>三、分页列表标签：</h3>
<div>
     <h4>1.适用于<font color="#0000FF">包含主表</font>的系统模型</h4>
     <p>
     <?php
     echo nl2br(htmlentities("
         <pagelist addfield=\"on\" pagesize=\"5\" titlelen=\"36\" desclen=\"140\" > 
            <dl> 
              <dt> 
                  <p><a href=\"{\$field['url']}\"><img src=\"{\$field['img']|default=\$defaultimg}\" /></a></p> 
              </dt> 
              <dd> 
                  <h4>{\$field['title']}</h4><p>{:jldate(\$field['pubdate'])}</p><p>{\$field['desc']}</p> 
              </dd> 
            </dl> 
         </pagelist>
         {\$pageline} 
      ",ENT_QUOTES,"utf-8"));
	 ?>
     </p>
     <h4>2.适用于<font color="#0000FF">不包含主表</font>的系统模型</h4>
     <p>
     <?php
     echo nl2br(htmlentities("<div>
         <pageloop typeid=\"20\" table=\"addprice\" where=\"status=1\" >
             {\$field['title']} 
         </pageloop> 
		 </div>
         {\$pageline} 
      ",ENT_QUOTES,"utf-8"));
	 ?>
     </p>          
</div>

<h3>四、万能查询标签：</h3>
<div>
     <h4>1.适用于<font color="#0000FF">复杂查询语句</font></h4>
     <p>
     <?php
	 echo "⊙通过钩子函数__sql_demo__查询<br/>";
     echo nl2br(htmlentities("<query sql=\"__sql_demo__\" > 
        <li>{\$field['title']}<li> 
      </query>",ENT_QUOTES,"utf-8"));
	   echo "<br/>⊙通过钩子函数sql语言查询<br/>";
     echo nl2br(htmlentities("<query sql=\"select * from jl_archives where typeid = 3\" > 
        <li>{\$field['title']}<li> 
      </query>",ENT_QUOTES,"utf-8"));
	 ?>
     </p>
     <h4>2.适用于<font color="#0000FF">复杂查询语句</font>（分页）</h4>
     <p>
     <?php
     echo nl2br(htmlentities("<pagequery process=\"__process_demo__\" pagesize=\"10\" > 
         <li>{\$field['title']}<li>
      </pagequery>
      {\$pageline}",ENT_QUOTES,"utf-8"));
	 ?>
     </p>          
</div>

<h3>五、友情列表标签：</h3>
<div>
     <h4>1.typeid 指定友情链接的类别 </font></h4>
     <p>
     <?php	 
     echo nl2br(htmlentities("<ul> 
        <flink typeid=\"1\" titlelen=\"12\" > 
            <li> <a href=\"{\$field['url']}\"><img src=\"{\$field['logo']}\" /></a> {\$field['title']}</li>
        </flink>
      </ul>",ENT_QUOTES,"utf-8"));
	  ?>	  
     </p>      
</div>

<h3>六、内容页标签：</h3>
<div>
     <h4>1.标题</font></h4>
     <p>
     <?php	 
     echo nl2br(htmlentities("{\$c['title']}",ENT_QUOTES,"utf-8"));
	  ?>	  
     </p>
     <h4>2.发布者</font></h4>
     <p>
     <?php	 
     echo nl2br(htmlentities("{\$c['author']}",ENT_QUOTES,"utf-8"));
	  ?>	  
     </p> 
     <h4>3.发布时间</font></h4>
     <p>
     <?php	 
     echo nl2br(htmlentities("{:jldate(\$c['pubdate'])}",ENT_QUOTES,"utf-8"));
	  ?>	  
     </p> 
     <h4>4.浏览量</font></h4>
     <p>
     <?php	 
     echo nl2br(htmlentities("{\$c['click']}",ENT_QUOTES,"utf-8"));
	  ?>	  
     </p> 
     <h4>5.详细内容</font></h4>
     <p>
     <?php	 
     echo nl2br(htmlentities("{\$c['txt']}",ENT_QUOTES,"utf-8"));
	  ?>	  
     </p>
     <h4>6.自定义字段</font></h4>
     <p>
     <?php	 
     echo nl2br(htmlentities("{\$c['字段名']}",ENT_QUOTES,"utf-8"));
	  ?>	  
     </p>
      <h4>7.上一篇 、 下一篇</font></h4>
     <p>
     <?php	 
     echo nl2br(htmlentities("{\$prev} 、 {\$next}",ENT_QUOTES,"utf-8"));
	  ?>	  
     </p>     
</div>

<h3>七、其它标签：</h3>
<div>
     <h4>1.获取父栏目ID </font></h4>
     <p>
     <?php	 
     echo nl2br(htmlentities("{\$arctype['fid']}",ENT_QUOTES,"utf-8"));
	  ?>	  
     </p> 
     <h4>2.获取父栏目名称 </font></h4>
     <p>
     <?php
	 echo "⊙普通形式<br/>";	 
     echo nl2br(htmlentities("<arctype typeid=\"\$arctype['fid']\" type=\"self\" row=\"1\">
        <li>{\$field['name']}</li>
       </arctype>",ENT_QUOTES,"utf-8"));
	 echo "<br/>⊙特殊形式(例如：<b>关</b>于我们)<br/>";
	 echo nl2br(htmlentities("<arctype typeid=\"\$arctype['fid']\" type=\"self\" row=\"1\">
        <li><b><php>echo mb_substr(\$field['name'],0,1,'utf-8');</php></b><span><php>echo mb_substr(\$field['name'],1,6,'utf-8');</php></span></li>
       </arctype>",ENT_QUOTES,"utf-8"));
	  ?>	  
     </p> 
     <h4>3.获取当前栏目ID </font></h4>
     <p>
     <?php	 
     echo nl2br(htmlentities("{\$arctype['id']}",ENT_QUOTES,"utf-8"));
	  ?>	  
     </p>
      <h4>4.获取当前栏目图片 </font></h4>
     <p>
     <?php	 
     echo nl2br(htmlentities("{\$arctype['litpic']}",ENT_QUOTES,"utf-8"));
	  ?>	  
     </p>
     <h4>5.获取当前栏目地址 </font></h4>
     <p>
     <?php	 
     echo nl2br(htmlentities("<a href=\"__APP__/Index/show/tid/{\$tid}.html\">{\$arctype['name']}</a>",ENT_QUOTES,"utf-8"));
	  ?>	  
     </p>
     <h4>6.获取当前位置导航 </font></h4>
     <p>
     <?php	 
     echo nl2br(htmlentities("{\$position}",ENT_QUOTES,"utf-8"));
	  ?>	  
     </p> 
     <h4>7.模版中输出 $arctype 变量内容 </font></h4>
     <p>
     <?php	 
     echo nl2br(htmlentities("<php>print_r(\$arctype);</php>",ENT_QUOTES,"utf-8"));
	  ?>	  
     </p> 
     <h4>8.列表标签中序号的调用 </font></h4>
     <p>
     <?php	 
     echo nl2br(htmlentities("{\$index}、{\$index+1} ...",ENT_QUOTES,"utf-8"));
	  ?>	  
     </p> 
     <h4>9.标签中自定义日期格式的调用 </font></h4>
     <p>
     <?php	 
     echo nl2br(htmlentities("<php>echo date(\"Y-m-d\",\$field['pubdate']);</php>",ENT_QUOTES,"utf-8"));
	  ?>	  
     </p>
     <h4>10.单页调用内容 </font></h4>
     <p>
     <?php	 
     echo nl2br(htmlentities("{\$arctype['txt']}",ENT_QUOTES,"utf-8"));
	  ?>	  
     </p>
     <h4>11.公用模版引入 </font></h4>
     <p>
     <?php	 
     echo nl2br(htmlentities("<include file='wcs/Tpl/home/default/top.html'/>",ENT_QUOTES,"utf-8"));
	  ?>	  
     </p>
     <h4>12.父栏目自动跳转到子栏目 </font></h4>
     <p>
     <?php	 
     echo nl2br(htmlentities("<arctype typeid=\"\$tid\" row='1'>
	       <script type=\"text/javascript\">
              window.location.href=\"{\$field['url']}\";
		   </script>
        </arctype>",ENT_QUOTES,"utf-8"));
	  ?>	  
     </p> 
     <h4>13.模版中路径设置标签 </font></h4>
     <p>
     <?php	 
     echo nl2br(htmlentities("{:U('Index/show?tid=x')}",ENT_QUOTES,"utf-8"));
	  ?>	  
     </p>
     <h4>14.当前站点Banner标签 </font></h4>
     <p>
     <?php	 
     echo nl2br(htmlentities("{:C('currentSite.banner')}",ENT_QUOTES,"utf-8"));
	  ?>	  
     </p>
     <h4>15.QQ客服置标签 </font></h4>
     <p>
     <?php	 
     echo nl2br(htmlentities("<ul> 
        <qqlist name=\"JL_QQ_LIST\" row=\"5\">
            <li><a href=\"tencent://message/?uin={\$field['qq']}&amp;Site=&amp;Menu=yes\"><img border=\"0\" style=\"margin-right:2px;\" src=\"http://wpa.qq.com/pa?p=2:{\$field['qq']} :52\" alt=\"点击这里给我发消息\" title=\"点击这里给我发消息\"/>{\$field['title']}</a></li>
        </qqlist>
       </ul>",ENT_QUOTES,"utf-8"));
	  ?>	  
     </p>
     <h4>16.获取内容去掉HTML并截取字符个数 </font></h4>
     <p>
     <?php	 
     echo nl2br(htmlentities("<php>echo msubstr(strip_tags(\$field['txt']),0,380);</php>",ENT_QUOTES,"utf-8"));
	 ?>	  
     </p>
           
</div>

</body> 
</html>
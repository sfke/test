	/*fileManage 文件管理
	*两个参数
	*参数一 根目录路径
	*参数二 int 1为文件目录都显示 2为只显示目录 3为只显示文件
	*/
	
	function fileManage(){
		this.rootpath;
		this.path = '/';
		this.type = 1;//1为文件目录都显示 2为只显示目录 3为只显示文件
		//获取数据函数
		this.getFileList = function (path,type) {
			$.ajaxSetup({ 
				async: false 
			}); 

			$.post(
			publicUrlInc+"/upload/ajax.php",
			{action:'get_data',
			 root:path,
			 type:type},
			function(data){
				file_arr = data;
				return data;
			},'json');
		}
		this.arrProcess = function(path,type){

		selected_file_name =null;
		selected_file_type =null;
				//获取数据
			this.getFileList(path,type);
			
			var str='<div><table width="100%"><tbody><tr><td width="5%"></td><td width="55%"><b>名称</b></td><td width="20%"><b>文件大小</b></td><td width="20%"><b>修改时间</b></td></tr></tbody></table></div>';
			for(i in file_arr){
				if(path==this.rootpath&&file_arr[i].name=='..') continue;
				if(file_arr[i].type=='dir'){
					var ico = '<img src="'+publicUrlInc+'/upload/dir.gif"/>';
				}else{
					var lastfilejia=path.split("/");
					var s= file_arr[i].name;
                    var stype=s.substr(s.length-4);
                    if(stype==".jpg"||stype==".gif"||stype==".png"){
					  var ico = '<img src="'+uploadurl+lastfilejia[lastfilejia.length-2]+'/'+s+'" style="width:34px;height:36px;padding:7px 8px;"/>';
					}else if(stype==".doc"||stype=="docx"){
					  var ico = '<img src="'+publicUrlInc+'/upload/word.png" width="50" height="50" />';
					}else if(stype==".xls"||stype=="xlsx"){
					  var ico = '<img src="'+publicUrlInc+'/upload/excel.png" width="50" height="50" />';
					}else if(stype==".rar"||stype==".zip"){
					  var ico = '<img src="'+publicUrlInc+'/upload/rar.png" width="50" height="50" />';
					}else if(stype==".swf"||stype==".flv"||stype==".mp4"){
					  var ico = '<img src="'+publicUrlInc+'/upload/video.png" width="50" height="50" />';
					}else if(stype==".pdf"){
					  var ico = '<img src="'+publicUrlInc+'/upload/pdf.png" width="50" height="50" />';		
					}else{
				      var ico = '<img src="'+publicUrlInc+'/upload/file.gif"/>';
					}
				}
				if(window.file_flag !==undefined&&window.file_flag==file_arr[i].name)
				{var flag_class = 'file_hover';window.file_flag=null; }
				else var flag_class ='';
				str+='<div id=n_'+i+' class="file_list '+flag_class+'"><table width="100%"><tbody><tr><td width="5%">'+ico+'</td><td width="55%">'+file_arr[i].name+'</td><td width="20%">'+file_arr[i].size+'</td><td width="20%">'+file_arr[i].lastmod+'</td></tr></tbody></table></div>';
			
			}
			var html = $(str);
			$("#fileM").append(html);
		}

		this.listClear = function(){
			$("#fileM").empty();
		}
		this.refresh = function(path,type){
			if(path==this.path+'../'){
					var deep,n,i,str='';
					deep = path.split('/');
					n = deep.length-3;
					for(i=0;i<n;i++){
						str+=deep[i]+'/';}
					path = str;
				}
			this.listClear();
			this.arrProcess(path,type)			
			this.path = path;

		
		}
		this.constuct = function(path,type){
			this.rootpath = path;
			this.path = path;
			this.type = type;
			this.arrProcess(path,type);
		
		
		}
		
		
		



} 

 function check_name(x){
		if(x.length==0||x.length>30){
		alert("文件名长度错误！");
		return 0;
	}else{
		reg = new RegExp("[@#$%^&()+!/\\<>*?]");
		if(reg.test(x)){
			alert("文件名不合法！");
			return 0;
		}
	}
	return 1;
 
 }


  	$(function(){

	$(".file_list").live({
    dblclick: function() {
		var id=this.id.substr(2);
		if(file_arr[id].type=='dir'){
			fm.refresh(fm.path+file_arr[id].name+'/',fm.type);
		}
    },    
    mouseover: function() {
		$("#"+this.id).addClass('file_hover');
    },   
	mouseout: function() {
		$("#"+this.id).removeClass('file_hover');
    },	
	click: function() {
		$(".file_list").removeClass('file_select');
		$("#"+this.id).addClass('file_select');
		if(file_arr[this.id.substr(2)].type == 'dir')
		var type = '文件夹';
		else var type = '文件';
		selected_file_name = file_arr[this.id.substr(2)].name;
		selected_file_type = file_arr[this.id.substr(2)].type;
		$(".select_info").html(type+'：'+selected_file_name);
    }      

});

	$("#select_ok,#file_del,#file_rename,#file_new,#select_cancel").click(function(){
		var path = fm.path;
		if(this.id == 'select_ok' ){
			if(window.selected_file_name==undefined) {alert("没有选中的文件");return ;}
			var val = fm.path+selected_file_name;
			val = val.replace(fm.rootpath,'/');
			$("#img_path").val(val);
		}else
		if(this.id == 'file_del' ){
			if(window.selected_file_name==undefined) {alert("没有选中的文件");return ;}
			$.post(
			publicUrlInc+"/upload/ajax.php",
			{root:path,
			 action:'file_del',
			 type:selected_file_type,
			 name:selected_file_name},
			function(data){
				var info ='';
				if(data == '1'){
					info='删除文件夹成功!';
					$(".file_select").remove();
					$(".select_info").html(info);
				}else 
				if(data == '2'){
					info='删除文件夹失败,文件夹不为空或者权限不够';
					$(".select_info").html(info);
				}else 
				if(data == '3'){
					info='删除文件成功!';
					$(".file_select").remove();
					$(".select_info").html(info);
				}else 
				if(data == '4'){
					info='删除文件失败,有可能权限不够';
					$(".select_info").html(info);
				}else{
					info='操作失败，多数情况为权限不够';
					$(".select_info").html(info);
				}
			});
			
		}else 
		if(this.id == 'file_rename' ){
			if(window.selected_file_name==undefined) {alert("没有选中的文件");return ;}
			var x;
			x=prompt("输入新的文件夹/文件名字  （文件需加后缀 如 *.jpg）");
			if(!check_name(x)) return;
			$.post(
			publicUrlInc+"/upload/ajax.php",
			{root:path,
			 action:'file_rename',
			 type:selected_file_type,
			 name:selected_file_name,
			 newname:x},
			function(data){
				var info ='';
				if(data == '5'){
					info='重命名成功!';
					file_flag=x;
					fm.refresh(fm.path,fm.type);
					$(".select_info").html(info);

				}else 
				if(data == '6'){
					info='重命名失败,检查文件名';
					$(".select_info").html(info);
				}else{
					info='操作失败，多数情况为权限不够';
					$(".select_info").html(info);
				}
			});			
		}else 
		if(this.id == 'file_new' ){
			var newname = prompt("输入新建文件夹名字");
			if(!check_name(newname)) return;
			$.post(
			publicUrlInc+"/upload/ajax.php",
			{root:path,
			 action:'file_new',
			 newname:newname},
			function(data){
				var info ='';
				if(data == '7'){
					info='创建文件夹成功!';
					file_flag=newname;
					fm.refresh(fm.path,fm.type);
					$(".select_info").html(info);

				}else 
				if(data == '8'){
					info='创建文件夹失败,检查文件名';
					$(".select_info").html(info);
				}else{
					info='操作失败，多数情况为权限不够';
					$(".select_info").html(info);
				}
			});			
		}else 
		if(this.id == 'select_cancel' ){
			selected_file_type = null;
			selected_file_name = null;
			$(".fileM_box").fadeOut(400);
		}
		
		

	
	});
 
	});
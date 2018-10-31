	function kalert(content,gourl,width,title){
                    var content = (typeof(content)!= 'undefined')?content:'嘿!';
                    var title = (typeof(title)!= 'undefined')?title:'提示';
                    var gourl = (typeof(gourl)!= 'undefined')?gourl:'';
                    var width = (typeof(width)!= 'undefined')?width:200;
					var dialog = KindEditor.dialog({
						width : width,
						title : title,
						body : '<div style="margin:10px;"><strong>'+content+'</strong></div>',
						closeBtn : {
							name : '关闭',
							click : function(e) {
								dialog.remove();
							}
						},
						yesBtn : {
							name : '确定',
							click : function(e) {
								//alert(this.value);
                                location.href=gourl;
                                dialog.remove();
                            }
						},
						noBtn : {
							name : '取消',
							click : function(e) {
								dialog.remove();
							}
						}
					});
	}
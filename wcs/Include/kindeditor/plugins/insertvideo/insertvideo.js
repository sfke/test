/*******************************************************************************
* KindEditor - WYSIWYG HTML Editor for Internet
* Copyright (C) 2006-2011 kindsoft.net
*
* @author Roddy <luolonghao@gmail.com>
* @site http://www.kindsoft.net/
* @licence http://www.kindsoft.net/license.php
*******************************************************************************/

KindEditor.plugin('insertvideo', function(K) {
    var self = this, name = 'insertvideo', lang = self.lang(name + '.'),
        allowMediaUpload = K.undef(self.allowMediaUpload, true),
        allowFileManager = K.undef(self.allowFileManager, false),
        formatUploadUrl = K.undef(self.formatUploadUrl, true),
        extraParams = K.undef(self.extraFileUploadParams, {}),
        filePostName = K.undef(self.filePostName, 'imgFile'),
        uploadJson = K.undef(self.uploadJson, self.basePath + 'php/upload_json.php');
    self.plugin.insertvideo = {
        edit : function() {
            var html = [
                '<div style="padding:20px;">',
                //url
                '<div class="ke-dialog-row">',
                '<label for="keUrl" style="width:60px;">' + lang.url + '</label>',
                '<input class="ke-input-text" type="text" id="keUrl" name="url" value="" style="width:160px;" /> &nbsp;',
                '<input type="button" class="ke-upload-button" value="' + lang.upload + '" /> &nbsp;',
                '<span class="ke-button-common ke-button-outer">',
                '<input type="button" class="ke-button-common ke-button" name="viewServer" value="' + lang.viewServer + '" />',
                '</span>',
                '</div>',
                //width
                '<div class="ke-dialog-row">',
                '<label for="keWidth" style="width:60px;">' + lang.width + '</label>',
                '<input type="text" id="keWidth" class="ke-input-text ke-input-number" name="width" value="550" maxlength="4" />',
				'<label for="keHeight" style="width:60px;margin-left:20px;">' + lang.height + '</label>',
                '<input type="text" id="keHeight" class="ke-input-text ke-input-number" name="height" value="400" maxlength="4" />',
                '</div>',
                //height
                '<div class="ke-dialog-row">',
                '<label for="keAutostart">' + lang.autostart + '</label>',
                '<input type="checkbox" id="keAutostart" name="autostart" value="1" checked="checked" />',
                '</div>',
                //autostart
                '<div class="ke-dialog-row">',
				'<label for="keWap">适应手机</label>',
				'<input type="checkbox" id="keWap" name="autostart" value="" />(只支持 mp4 格式视频,不支持自动播放)',
                '</div>',
				 
                '</div>'
            ].join('');
            var dialog = self.createDialog({
                name : name,
                width : 450,
                height : 230,
                title : self.lang(name),
                body : html,
                yesBtn : {
                    name : self.lang('yes'),
                    click : function(e) {
                        var url = K.trim(urlBox.val()),
                            width = widthBox.val(),
                            height = heightBox.val();
                        if (url == 'http://' || K.invalidUrl(url)) {
                            alert(self.lang('invalidUrl'));
                            urlBox[0].focus();
                            return;
                        }
                        if (!/^\d*$/.test(width)) {
                            alert(self.lang('invalidWidth'));
                            widthBox[0].focus();
                            return;
                        }
                        if (!/^\d*$/.test(height)) {
                            alert(self.lang('invalidHeight'));
                            heightBox[0].focus();
                            return;
                        }
                        /* var html = K.mediaImg(self.themesPath + 'common/blank.gif', {
                            src : url,
                            type : K.mediaType(url),
                            width : width,
                            height : height,
                            autostart : autostartBox[0].checked ? 'true' : 'false',
                            loop : 'true'
                        }); */
                       // var s = /\.[^\.]+/.exec(url); //此方法不适合带有'http://'的长地址
						var s = url.substring(url.lastIndexOf("."),url.length);
						if(url.lastIndexOf(".")==0){
						  s='x'	
						}
						s=s.substring(0,4)
						if(s==".swf"){s=3;}else if(s==".flv"){s=0;}else if(s==".mp4"){s=4;}
                        var filename = url.substring(url.lastIndexOf("/")+1,url.lastIndexOf("."));
                        var path = url.substring(0, url.indexOf('Upload'))+'include/'; 
						if(path=="include/"){
							var currpath=location.href;
						    path = currpath.substring(0, currpath.indexOf('admin.php'))+'/wcs/include/';	
						}                    
                       var autoStart = autostartBox[0].checked ? '1' : '0';
					   var autoWap = autostartBox[1].checked ? '1' : '0';
					   if (s==3){
                         var html = '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,28,0" width="'+width+'" height="'+height+'">'
		                 html += '<param name="movie" value="'+url+'" />'
		                 html += '<param name="quality" value="high" />'
						 html += '<param name="allowFullScreen" value="true" />'
                         html += '<embed src="'+url+'" quality="high" play="'+autoStart+'"   pluginspage="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash" type="application/x-shockwave-flash" width="'+width+'" height="'+height+'" allowfullscreen="true"></embed>'
  	                     html += '</object>'
					   }else if(s==0){
						var html = '<p align="center"><span id="'+filename+'" class="plugin-insertvideo-img"  style="display: block; width:'+width+'px; height:'+height+'px; cursor: pointer; background-color:#fff"></span></p>';
                        html += "<script type='text/javascript' src='"+path+"ckplayer/ckplayer.js' charset='utf-8'></script>";
                        html += "<script type='text/javascript'>";
                        html += "var flashvars={f:'"+url+"',p:'"+autoStart+"',s:'"+s+"'};";
                        html += "CKobject.embed('"+path+"ckplayer/ckplayer.swf','"+filename+"','"+filename+"','"+width+"','"+height+"',false,flashvars)";
                        html += "</script>"; 
					   }else if(s==4){
						if(autoWap==1){ 
						width="100%"; height="100%"; 
						var html = '<p align="center"><span id="'+filename+'" class="plugin-insertvideo-img"  style="display: block; width:'+width+'; height:'+height+'; cursor: pointer; background-color:#fff"></span></p>';
						}else{
						var html = '<p align="center"><span id="'+filename+'" class="plugin-insertvideo-img"  style="display: block; width:'+width+'px; height:'+height+'px; cursor: pointer; background-color:#fff"></span></p>';
						}
                        html += "<script type='text/javascript' src='"+path+"ckplayer/ckplayer.js' charset='utf-8'></script>";
                        html += "<script type='text/javascript'>";
                        html += "var flashvars={p:"+autoStart+",e:1};";
                        html += "var video=['"+url+"->video/mp4','http://www.ckplayer.com/webm/0.webm->video/webm','http://www.ckplayer.com/webm/0.ogv->video/ogg'];"
	                    html += "var support=['all'];"
	                    html += "CKobject.embedHTML5('"+filename+"','ckplayer_"+filename+"','"+width+"','"+height+"',video,flashvars,support);";
                        html += "</script>"; 
					   }else{
						 var html="请上传 swf 或 flv 或 mp4 视频格式！"   
					   }
					   
                       self.insertHtml(html+"<p><br /></p>").hideDialog();
                    }
                }
            }),
            div = dialog.div,
            urlBox = K('[name="url"]', div),
            viewServerBtn = K('[name="viewServer"]', div),
            widthBox = K('[name="width"]', div),
            heightBox = K('[name="height"]', div),
            autostartBox = K('[name="autostart"]', div);
            urlBox.val('http://');

            if (allowMediaUpload) {
                var uploadbutton = K.uploadbutton({
                    button : K('.ke-upload-button', div)[0],
                    fieldName : filePostName,
                    extraParams : extraParams,
                    url : K.addParam(uploadJson, 'dir=media'),
                    afterUpload : function(data) {
                        dialog.hideLoading();
                        if (data.error === 0) {
                            var url = data.url;
                            if (formatUploadUrl) {
                                url = K.formatUrl(url, 'absolute');
                            }
                            urlBox.val(url);
                            if (self.afterUpload) {
                                self.afterUpload.call(self, url, data, name);
                            }
                            alert(self.lang('uploadSuccess'));
                        } else {
                            alert(data.message);
                        }
                    },
                    afterError : function(html) {
                        dialog.hideLoading();
                        self.errorDialog(html);
                    }
                });
                uploadbutton.fileBox.change(function(e) {
                    dialog.showLoading(self.lang('uploadLoading'));
                    uploadbutton.submit();
                });
            } else {
                K('.ke-upload-button', div).hide();
            }

            if (allowFileManager) {
                viewServerBtn.click(function(e) {
                    self.loadPlugin('filemanager', function() {
                        self.plugin.filemanagerDialog({
                            viewType : 'LIST',
                            dirName : 'media',
                            clickFn : function(url, title) {
                                if (self.dialogs.length > 1) {
                                    K('[name="url"]', div).val(url);
                                    if (self.afterSelectFile) {
                                        self.afterSelectFile.call(self, url);
                                    }
                                    self.hideDialog();
                                }
                            }
                        });
                    });
                });
            } else {
                viewServerBtn.hide();
            }

            var img = self.plugin.getSelectedMedia();
            if (img) {
                var attrs = K.mediaAttrs(img.attr('data-ke-tag'));
                urlBox.val(attrs.src);
                widthBox.val(K.removeUnit(img.css('width')) || attrs.width || 0);
                heightBox.val(K.removeUnit(img.css('height')) || attrs.height || 0);
                autostartBox[0].checked = (attrs.autostart === 'true');
            }
            urlBox[0].focus();
            urlBox[0].select();
        },
        'delete' : function() {
            self.plugin.getSelectedMedia().remove();
        }
    };
    self.clickToolbar(name, self.plugin.insertvideo.edit);
});

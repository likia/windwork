/**
 * 手机端上传
 * 1、选择图片后自动上传
 * 2、可批量上传图片
 * 3、上传显示进度信息
 * 4、图片上传后显示缩略图
 * 5、可删除图片
 */
(function(win) {
	win.HTML5Uploader = win.HTML5Uploader || {};
	/* 部分手机不兼容
	// 上传文件域对象
	var filesInput = document.createElement('input');
	filesInput.type = 'file';
	filesInput.name = 'file';
	filesInput.className = 'files-input';
	filesInput.accept = 'image/*';
	*/
	document.body.innerHTML += '<input type="file" name="files-input" id="files-input" class="files-input" accept="image/*" style="height:0;overflow:hidden;" autocomplete="off" />';
	var filesInput = document.getElementById('files-input');
	
	function uploadFiles(e) {
		var files = e.target.files,
		reg = /image\/.*/i; // 验证是否是图片文件的正则
		for(var i = 0; i < files.length; i++) {
		    var file = files[i];
			if (file.type && !reg.test(file.type)) {
				alert(f.name + '不是图片');
				continue;
			}
			
			var index = parseInt(Math.random()*1000000);
			document.getElementById('image-box').innerHTML += '<dd id="upload-img-index-' + index + '" class="uploading post-img"></span><progress id="upload-img-progress-' + index + '" value="0" max="100"></progress></dd>';

			uploadHandler(file, index);
		}
	}
	
	function uploadHandler(singleImg, index) {
		var progress = document.getElementById('upload-img-progress-' + index);
		var xhr = new XMLHttpRequest();
		
		//alert(progress)
		if (xhr.upload) {
			// 进度显示
			xhr.upload.addEventListener("progress",
				function(e) {
					if (e.lengthComputable) {
						progress.value = (e.loaded / e.total) * 100;
					}
				},
				false
			);
			
			// 文件上传成功或是失败
			xhr.onreadystatechange = function(e) {
				if (xhr.readyState == 4) {	
				    if(xhr.status == 413) {
						alert('您上传的图片太大了('+(parseInt(singleImg.size/100000)/10)+'M)，最大允许上传2M的图片，请您截小一点再上传吧。我们正在努力开发客户端压缩图片的功能。');
						$('#upload-img-index-' + index).remove();
						return;
					}
					
					var res = eval("(" + xhr.responseText + ")");
					var previewObj = document.getElementById('upload-img-index-' + index);
					if (xhr.status == 200 && res.err.length == 0) {
						// 上传成功后显示预览图（客户端预览本地大图片太耗资源）
						var pic = res.message.uploadfile_response;
						previewObj.style.backgroundImage = 'url(' + pic.thumb + ')';
						previewObj.innerHTML = '<input type="hidden" name="item_images[]" value="' + pic.path + '">'
											 + '<span class="delete" onclick="HTML5Uploader.removePic(\'' + pic.path + '\', \'' + index + '\')">×</span><span class="ok"></span>';
						progress.value = 100;
					} else {
						previewObj.className += ' err';
					}
				}
			};
			
			
			var uploadUrl = Wind.util.makeAjaxUrl('system.uploader.create/type:pic');
			// 上传处理
			var formData = new FormData();
		    
			// 压缩jpg图片
			if(singleImg.size > 128000 && (singleImg.type == '' || singleImg.type == 'image/jpg' || singleImg.type == 'image/jpeg')) {
				// 压缩大图
				var reader = new FileReader();				
				reader.onloadend = function(e) {
					var tempImg = new Image();
					tempImg.src = this.result;
						
					tempImg.onload = function(e) {
						var width = 480;
						var height = tempImg.height*(480/tempImg.width);
						
						// 定义画布
						var canvas = document.createElement('canvas');
						canvas.width = width;
						canvas.height = height;
						
						// 在画布上画图
						var ctx = canvas.getContext("2d");
						ctx.drawImage(tempImg, 0, 0, tempImg.width, tempImg.height, 0, 0, width, height);
						
						var dataURL = canvas.toDataURL("image/jpeg", 0.9);
						
						// 压缩成功(大于1k)则上传缩略图，否则上传原图
						if(dataURL.length > 1024) {
							formData.append("name", singleImg.name);
							formData.append("baseImage", dataURL.substr(22));	
						} else {							
							formData.append("file", singleImg);		
						}
						
						// 开始上传
						xhr.open("POST", uploadUrl, true);
						xhr.send(formData);	
					}				 
				}
				reader.readAsDataURL(singleImg);
			} else {
				// 文件方式上传				
				formData.append("file", singleImg);		
				// 开始上传
				xhr.open("POST", uploadUrl, true);
				xhr.send(formData);			
			}
			
		}
	}
	
	/**
	 * 上传单个文件
	 */
	HTML5Uploader.single = function(uploadBtnId) {
		var uploadBtnId = uploadBtnId || 'btn-upload-image';
		if (!window.File || !window.FileList || !window.FileReader || !window.Blob) {
			Wind.message.show('<span class="warn">您的浏览器不支持HTML5上传');
			return;
		}
		
		filesInput.addEventListener("change", uploadFiles, false);
		document.getElementById(uploadBtnId).addEventListener("click", function() {
			filesInput.click()
		}, false);
	}
	
	HTML5Uploader.removePic = function (path, index) {
		if(!confirm('您要删除该图片吗？')) {
		    return false;
		}
		$.getJSON(Wind.util.makeAjaxUrl('system.uploader.delete'), {path: path}, function(r) {
			if(r.err.length) {
				Wind.message.showResponse(r);
			} else {
			   $('#upload-img-index-' + index).remove();
			}
		})
	}
	
	/**
	 * 上传多个文件
	 *
	 */
	HTML5Uploader.multiple = function(uploadBtnId) {
	    filesInput.setAttribute("multiple", "multiple");
	    HTML5Uploader.single(uploadBtnId);
	}	
})(window);

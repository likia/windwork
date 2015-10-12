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
	var uploadImgArr = [];  // 存放图片对象的数组
	var uploadImgIndex = 0;
	
	function beforeCallback(img) {
	}
	
	// 上传文件域对象
	var filesInput = document.createElement('input');
	filesInput.type = 'file';
	filesInput.name = 'file';
	filesInput.className = 'files-input';
	filesInput.accept = 'image/*';
		
	//定义获取图片信息的函数
	function getFiles(e) {
		e = e || window.event;
							
		//获取file input中的图片信息列表
		var files = e.target.files,
		reg = /image\/.*/i; // 验证是否是图片文件的正则
		
		//console.log(files);
		for (var i = 0, file; file = files[i]; i++) {
			if (!reg.test(file.type)) {
				alert(f.name + '不是图片');
				continue;
			}
			
			file.index = uploadImgIndex;
			
			var reader = new FileReader();
			
			//读取文件内容
			reader.readAsDataURL(file);				
			uploadImgArr.push(file);
			
			// 图片预览显示到<dl id="image-box"></dl>中
			document.getElementById('image-box').innerHTML += '<dd id="upload-img-index-' + file.index + '" class="uploading post-img"></span><progress id="upload-img-progress-' + file.index + '" value="0" max="100"></progress></dd>';
			
			// TODO 压缩图片
			
			uploadImgIndex ++;
		}
		
		//console.log(uploadImgArr);	
		
		uploadFun();
	}
		
	//开始上传照片
	function uploadFun() {
		var j = 0;
		var info = '';
		function run() {
			if (uploadImgArr.length > 0) {
				var singleImg = uploadImgArr[j];
				var progress = document.getElementById('upload-img-progress-' + singleImg.index);
				var xhr = new XMLHttpRequest();
				
				//alert(progress)
				if (xhr.upload) {
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
							
							var res = eval("(" + xhr.responseText + ")");
							var previewObj = document.getElementById('upload-img-index-' + singleImg.index);
							if (xhr.status == 200 && res.err.length == 0) {
								// 上传成功后显示预览图（客户端预览本地大图片太耗资源）
								var pic = res.message.uploadfile_response;
								previewObj.style.backgroundImage = 'url(' + pic.thumb + ')';
								previewObj.innerHTML = '<input type="hidden" name="item_images[]" value="' + pic.path + '">'
								                     + '<span class="delete" onclick="HTML5Uploader.removePic(\'' + pic.path + '\')">×</span><span class="ok"></span>';
								progress.value = 100;
							} else {
								previewObj.className += ' err';
							}
							
							//上传成功（或者失败）一张后，再次调用run函数，模拟循环
							if (j < uploadImgArr.length - 1) {
								j++;
								run();
							} else {
								uploadImgArr = [];								
							}
						}
					};
					
					var formData = new FormData();
					formData.append("file", singleImg);
					
					var uploadUrl = Wind.util.makeAjaxUrl('system.uploader.create/type:pic');
					// 开始上传
					xhr.open("POST", uploadUrl, true);
					xhr.send(formData);
					var startDate = new Date().getTime();
				}
			}
		}
		
		run();	
	}
	
	/**
	 * 上传单个文件
	 */
	HTML5Uploader.single = function(uploadBtnId) {
		var uploadBtnId = uploadBtnId || 'btn-upload-image';
		if (window.File && window.FileList && window.FileReader && window.Blob) {
			filesInput.addEventListener("change", getFiles, false);
		} else {
			Wind.message.show('<span class="warn">您的浏览器不支持HTML5上传');
		}
	
		document.getElementById(uploadBtnId).addEventListener("click", function() {
			filesInput.click()
		}, false);
	}
	
	HTML5Uploader.removePic = function (path) {
		$.getJSON(Wind.util.makeAjaxUrl('system.uploader.delete'), {path: path}, function(r) {
			
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

;(function(win, $) {
	win.removeUploadPic = function (path, fileId) {
		$('#upload-img-' + fileId).remove();
		path = encodeURIComponent(encodeURIComponent(path));
		$.getJSON(Wind.util.makeAjaxUrl('system.uploader.delete/path:' + path), function(res) {
		});
	}
	
	var uploader = WebUploader.create({
		auto: true,
		chunked: true,
		resize: true,
		
		// 文件接收服务端。
		server: Wind.util.makeAjaxUrl('system.uploader.create'),
	
		// 选择文件的按钮。可选。
		pick: {
			id: '#btn-upload-image',
			multiple: true
		},
		
		accept: {
			title: 'Images',
			extensions: 'gif,jpg,jpeg,bmp,png',
			mimeTypes: 'image/*'
		},
		compress: {
			width: 500,
			height: 640
		}
	});
	
	// 上传进度
	uploader.on('uploadProgress', function(file, percentage) {
		$('#upload-img-'+file.id + ' progress').val(percentage * 100);
	});
	
	// 完成上传完了，成功或者失败，先删除进度条。
	uploader.on('uploadComplete', function(file) {
		$('#upload-img-' + file.id).removeClass('uploading');
		$('#upload-img-' + file.id + ' progress').remove();
	});
	
	// 文件上传成功，给item添加成功class, 用样式标记上传成功。
	uploader.on('uploadSuccess', function(file, uploadSuccessRes) {
		if(uploadSuccessRes.err.length > 0) {
			$('#upload-img-' + file.id).append('<span class="upload-state-error"></span>');
			return;
		}
		
		var pic = uploadSuccessRes.message.uploadfile_response;
		
		var okTxt = '<input type="hidden" name="item_images[]" value="' + pic.path + '">'
				  + '<span class="delete" onclick="removeUploadPic(\'' + pic.path + '\', \''+ file.id + '\')">×</span>'
				  + '<span class="upload-state-done"></span>';
		$( '#upload-img-'+file.id).append(okTxt);
	});
	
	// 文件上传失败，显示上传出错。
	uploader.on('uploadError', function( file ) {
		$( '#upload-img-'+file.id ).append('<span class="upload-state-error"></span>');
	});
	
	// 上传前预览
	uploader.on('fileQueued', function(file) {
		$('#image-box').append('<dd id="upload-img-' + file.id + '" class="uploading post-img"><progress id="upload-img-progress-' + file.id + '" value="0" max="100"></progress></dd>');
	
		// 创建缩略图
		// 如果为非图片文件，可以不用调用此方法。
		// thumbnailWidth x thumbnailHeight 为 100 x 100
		uploader.makeThumb(file, function( error, src ) {
			if (error) {
				$img.replaceWith('<span>不能预览</span>');
				return;
			}
	
			$('dd#upload-img-' + file.id).css('background-image', 'url('+src+')');
		}, 60, 60);
		
	});
})(window, jQuery);
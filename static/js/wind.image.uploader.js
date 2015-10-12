;(function(win, $) {
	var Wind = win.Wind = win.Wind || {};
	Wind.image = Wind.image || {};
	Wind.image.uploader = {};
	
	Wind.image.uploader.upload = function(rid, callbackFunction, type) {
		var rid = rid || '';
		var type = type || 'pic'
		var callbackFunction = callbackFunction || 'Wind.image.uploader.upload.callback';
		
		if($('#ajax-frame').length <= 0) {
			$('body').append('<iframe id="ajax-frame" name="ajax-frame" style="width:0; height:0; position:absolute; border:none;"></iframe>');
		}
		
		var url = Wind.util.makeUrl('system.uploader.create/type:'+type+'/iframe_callback:'+callbackFunction+'/rid:'+rid);
		ctx = [
		    '<form id="form-upload" target="ajax-frame" enctype="multipart/form-data" method="post" action="'+url+'">',
			'  <p><input type="file" id="file" name="file"></p>',
		    '  <section class="handle-row"><input type="submit" value="上传" name="submit" class="btn" onclick="if(\'\' == $(\'#file\').val()) return false;"></section>',
		    '</form>'
		].join('');
		
		var title = '上传图片';
		
		Wind.message.show(ctx, title, 250, 110);
		
		$('#form-upload #file').click();
		$('#form-upload #file').change(function() {
			if($('.post-img-item').length > 8) {
				alert('最多只能上传8张图片');
				return false;
			}
			$('#form-upload input[name="submit"]').click();
			$('#form-upload').prepend('<div class="loading"></div>');
		});
	}
	
	Wind.image.uploader.upload.callback = function(r) {
		if(r.err.length > 0) {
			Wind.message.showResponse(r);
		} else {
			var html = '<div class="post-img post-img-item">'
			         + '  <img src="' + r.message.uploadfile_response.thumb + '" >'
			         + '  <input type="hidden" name="item_images[]" value="' + r.message.uploadfile_response.path + '" >'
					 + '</div>';
		    $('#post-imgs .add-icon').before(html);
			Wind.dialog.close();
		}
	}
})(window, jQuery);
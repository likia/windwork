;(function(win, $) {
	var Wind = win.Wind = win.Wind || {};
	Wind.uploader = {};

	/**
	 * 上传文件
	 *
	 * @param string uuid 图片关联到目标的id
	 * @param string callbackFunction 上传完成后回调函数
	 * @param string type 文件类型 album:相册; attach:附件
	 */
	Wind.uploader.upload = function (uuid, callbackFunction, type, autoPost) {
		if(typeof autoPost == 'undefined') {
			var autoPost = true;
		}
		
		if($('#ajax-frame').length <= 0) {
			$('body').append('<iframe id="ajax-frame" name="ajax-frame" style="width:0; height:0; position:absolute; border:none;"></iframe>');
		}
		
		var url = Wind.util.makeUrl('system.uploader.create/type:'+type+'/iframe_callback:'+callbackFunction+'/rid:'+uuid);
		ctx = [
		    '<form id="form-upload" target="ajax-frame" enctype="multipart/form-data" method="post" action="'+url+'">',
			'  <p>文件：<input type="file" id="file" name="file"></p>',
			'  <p>名称：<input class="text-box text" type="text" id="name" name="name"></p>',
			'  <p>说明：<textarea id="note" name="note" rows="4" cols="25" style="height:54px; width:200px;"></textarea></p>',
//			  (type == 'album' || type == 'image') ? '<p>水印：<input type="radio" name="watermark" value="1" />是&nbsp; <input type="radio" name="watermark" value="0" checked="checked" />否</p>' : '',
		    '  <p>　　　<input type="submit" value="上传" name="submit" class="btn" onclick="if(\'\' == $(\'#file\').val()) return false;"></p>',
		    '</form>'
		].join('');
		
		var title = '上传文件';
		if(type == 'album' || type == 'image') {
			title = '上传图片';		
		}
		
		Wind.message.show(ctx, title, 250, 220);
		
		if(autoPost) {
		    $('#form-upload #file').click();
			$('#form-upload #file').change(function() {
				$('#form-upload input[name="submit"]').click();
			});
		}
	}

	/**
	 * 修改上传文件
	 *
	 * @param string id 图片关联到目标的id
	 * @param string uuid 图片关联到目标的id
	 * @param string callbackFunction 上传完成后回调函数
	 * @param string type 文件类型 album:相册; attach:附件
	 */
	Wind.uploader.edit = function (id, uuid, callbackFunction, type) {
		if($('#ajax-frame').length <= 0) {
			$('body').append('<iframe id="ajax-frame" name="ajax-frame" style="width:0; height:0; position:absolute; border:none;"></iframe>');
		}
		
		var callbackFunction = callbackFunction || '';
		var url = Wind.util.makeUrl('system.uploader.update/'+id+'/type:'+type+'/rid:'+uuid+'?ajax=1');
		$.getJSON(url, function(r){
			var item = r.message;
			var ctx = [
			    '<form id="form-upload" target="ajax-frame" enctype="multipart/form-data" method="post" action="'+url+'&iframe_callback='+callbackFunction+'">',
				'  <p>文件：<input type="file" id="file" name="file"></p>',
				'  <p>名称：<input class="text-box text" type="text" id="name" name="name" value="'+item.name+'"></p>',
				'  <p>说明：<textarea id="note" name="note" rows="4" cols="25" style="height:54px; width:200px;">'+item.note+'</textarea></p>',
				  (type == 'album' || type == 'image') ? '<p>水印：<input type="radio" name="watermark" value="1" />是&nbsp; <input type="radio" name="watermark" value="0" checked="checked" />否</p>' : '',
			    '  <p>　　　<input type="submit" value="确定" name="submit" class="btn"></p>',
			    '</form>',
			    '<iframe name="ajax-frame" style="width:0; height:0; border:none;"></iframe>'
			].join('');
			
			var title = '修改文件';
			if(type == 'album' || type == 'image') {
				title = '修改图片';		
			}
			
			Wind.message.show(ctx, title, 250, 210);
		});		
	}
})(window, jQuery);
;(function(win, $) {
	var Wind = win.Wind = win.Wind || {};
	Wind.album = {};

	/**
	 * 上传图片
	 *
	 * @param string rid 图片关联到目标的rid
	 * @param string callbackFunction 上传完成后回调函数
	 * @param string type 文件类型 album:相册; attach:附件
	 */
	Wind.album.upload = function (rid, callbackFunction, type, uid, autoPost) {
		if(typeof autoPost == 'undefined') {
			var autoPost = false;
		}
		
		if($('#ajax-frame').length <= 0) {
			$('body').append('<iframe id="ajax-frame" name="ajax-frame" style="width:0; height:0; position:absolute; border:none;"></iframe>');
		}
		
		var url = Wind.util.makeUrl('system.uploader.create/type:'+type+'/iframe_callback:'+callbackFunction+'/rid:'+rid+'/uid:'+uid);
		ctx = [
		    '<form id="form-upload" target="ajax-frame" enctype="multipart/form-data" method="post" action="'+url+'">',
			'  <p>选择图片：<input type="file" id="file" name="file"></p>',
			'  <p>图片名称：<input class="text-box text" type="text" id="name" name="name"></p>',
			'  <p>图片说明：<textarea id="note" name="note" rows="4" cols="25" style="height:54px; width:200px;"></textarea></p>',
//			  (type == 'album' || type == 'image') ? '<p>水印：<input type="radio" name="watermark" value="1" />是&nbsp; <input type="radio" name="watermark" value="0" checked="checked" />否</p>' : '',
		    '  <p>　　　<input type="submit" value="上传" name="submit" class="btn" onclick="if(\'\' == $(\'#file\').val()) return false;"></p>',
		    '</form>'
		].join('');
		
		var title = '上传图片';
		
		Wind.message.show(ctx, title, 300, 220);
		
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
	 * @param string rid 图片关联到目标的rid
	 * @param string callbackFunction 上传完成后回调函数
	 * @param string type 文件类型 album:相册; attach:附件
	 */
	Wind.album.edit = function (id, callbackFunction, type) {
		if($('#ajax-frame').length <= 0) {
			$('body').append('<iframe id="ajax-frame" name="ajax-frame" style="width:0; height:0; position:absolute; border:none;"></iframe>');
		}
		
		var callbackFunction = callbackFunction || '';
		var url = Wind.util.makeUrl('system.uploader.update/'+id+'/type:'+type+'?ajax=1');
		$.getJSON(url, function(r){
			var item = r.message;
			var ctx = [
			    '<form id="form-upload" target="ajax-frame" enctype="multipart/form-data" method="post" action="'+url+'&iframe_callback='+callbackFunction+'">',
				'  <p>选择图片：<input type="file" id="file" name="file"></p>',
				'  <p>图片名称：<input class="text-box text" type="text" id="name" name="name" value="'+item.name+'"></p>',
				'  <p>图片说明：<textarea id="note" name="note" rows="4" cols="25" style="height:54px; width:200px;">'+item.note+'</textarea></p>',
				 // (type == 'album' || type == 'image') ? '<p>水印：<input type="radio" name="watermark" value="1" />是&nbsp; <input type="radio" name="watermark" value="0" checked="checked" />否</p>' : '',
			    '  <p>　　　<input type="submit" value="确定" name="submit" class="btn"></p>',
			    '</form>',
			    '<iframe name="ajax-frame" style="width:0; height:0; border:none;"></iframe>'
			].join('');
			
			var title = '修改图片';
			
			Wind.message.show(ctx, title, 300, 210);
		});		
	}
	
	/**
	 * 删除上传文件
	 *
	 * @param string id 图片关联到目标的id
	 * @param string callbackFunction 上传完成后回调函数
	 */
	Wind.album.deletePic = function (id, callbackFunction) {
		if(!confirm('您确定要删除该图片吗？')) {
			return false;
		}
		var url = Wind.util.makeAjaxUrl('system.uploader.delete/'+id);
		$.getJSON(url, function(r) {			
			if(r.err.length > 0) {
				Wind.message.showResponse(r);
			} else {
				$('#album-item-'+id).remove();
			}
		});	
	}
	
	/**
	 * 设置目标相册封面图片
	 *
	 * @param int picid 封面图片在附件中的id
	 */
	Wind.album.setCover = function (id, picid, uid) {
		$('.album-item').removeClass('current');
		$('#album-item-'+id).addClass('current');
		var url = Wind.util.makeAjaxUrl('shop.biz.album.updateByCover/'+id+'/picid:'+picid);
		$.getJSON(url, function(r) {
			if(r.err.length > 0) {
				Wind.message.showResponse(r);
			} 
		})
	}
	
})(window, jQuery);
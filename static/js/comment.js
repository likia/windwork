comment = {}

comment.form = function(uuid, uri, appendId) {
	var appendId = appendId || null;
	var contentId = util.makeGuid();
	var textarea = '#'+contentId + ' .comment-content';
	var tip = '请输入评论内容。';
	var html = '<div id="'+contentId+'">'
	         + '  <textarea class="comment-content">'+tip+'</textarea>'
	         + '  <div style="float:right"><b id="input-count">0/500</b> <input name="submit" type="button" class="btn" value="发表评论" /></div>'
			 + '</div>';
	if(appendId) {
		$('#'+appendId).append(html);		
	} else {
		html = '<div style="margin:20px;">' + html + '</div>';
	    Wind.dialog.open('发表评论', 500, 210, html);
	}
	
	$('#'+contentId + ' input[name="submit"]').click(function() {
		if(false !== comment.post(uuid, uri, $(textarea).val())) {
			$(textarea).val(tip);
		}
	});
	
	$(textarea).width($('#'+contentId).width() - 10);
	
	$(textarea).focus(function(){
		if($(this).val() == tip) {
			$(this).val('');
		}
	});
	
	$(textarea).blur(function(){
		if($(this).val() == '') {
			$(this).val(tip);
		}
	});
	
	$(textarea).keyup(function(){
		var len = $(this).val().length;
		var html = len + '/500'; 
		
		if(len > 500) {
			html = '<span class="error">'+html+'</span>';
		}
		
		$('#input-count').html(html);
	});
}

comment.post = function(uuid, uri, content, appendId) {
	var appendId = appendId || 'item-comment-list';
	var url = util.makeAjaxUrl('comment/i/create');
	if(content.length < 2) {
		alert('评论内容不能少于2个字！');
		return false;
	} 
	
	$.post(url, {'uuid': uuid, 'uri': uri, 'content': content}, function(r){
		if(r.err.toString().length > 0) {
			ui.showError(r.err, '错误提示！', 500, 210);
		} else {
			$('.cmt-uuid-'+uuid).text(r.message.cmts);
			ui.showMessage('发表评论成功');
			if($('#'+appendId).length > 0) {
				comment.get(uuid, uri, appendId, 1);
			}
		}
	}, 'json');
	
	return true;
}

comment.get = function(uuid, uri, appendId, page) {
	var page = page || 1;
	var uri = encodeURIComponent(uri);
	var url = util.makeAjaxUrl('comment/get/item');
	$.post(url, {'uuid': uuid, 'uri': uri, 'page': page}, function(r) {
		$('#'+appendId).html(r);
	}, 'html');
}

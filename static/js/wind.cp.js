;(function(win, $) {
	var Wind = win.Wind = win.Wind || {};
	// 列表页面的操作
	Wind.list = {};
	Wind.list.drop = function(url, dropObj, callback) {
		if(!confirm('您确定要删除吗？')) {
			return false;
		}
		$.getJSON(url, {ajax: 1}, function(r){
			Wind.message.showResponse(r, function() {
				if(typeof dropObj == 'object' && typeof dropObj.remove == 'function') {
					dropObj.remove();
				}
				if(typeof callback == 'function') {
					callback();
				}
			});
		});		
	}
})(window, jQuery);
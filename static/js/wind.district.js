;(function(win, $) {
	var Wind = win.Wind = win.Wind || {};
	Wind.district = {};
	
	Wind.district.showChange = function(upid, opt, selected) {
		var selected = selected || 0;
		var url = Wind.util.makeAjaxUrl('system.district.getListByUpid/'+upid);
		$.getJSON(url, function(r) {
			var opts = '<option value="" '+(selected == 0 ? ' selected="selected"' : '')+'>请选择地区</option>';
			for(var i in r) {
				opts += '<option value="'+r[i].id+'"'+(selected == r[i].id ? ' selected="selected"' : '')+'>'+r[i].name+'</option>';
			}
			
			opt.html(opts);
		});
	}
})(window, jQuery);
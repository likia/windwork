Wind.favorite = {}
Wind.favorite.post = function(uuid, uri, title, picid, parentId) {
	var parentId = parentId || 0;
	var picid = picid || 0;
	var uuid  = uuid || null;
	var url   = Wind.util.makeAjaxUrl('favorite/i/create');
	Wind.ui.showLoading();
	
	var data = {
		'uri': uri, 
		'title': title, 
		'picid': picid,
		'uuid': uuid,
		'parentid': parentId
	};
	
	$.post(url, data, function(r) {
		Wind.ui.hideLoading();
		if(r.ok.length > 0) {
			Wind.message.show(r.ok);
			if(uuid) {
				$('.fav-uuid-'+uuid).text(r.message.favs);
			}
		} else {
			Wind.message.showError(r.err);
		}
	}, 'json');
}
var WindLbs = {pickLat: 0, pickLng: 0}

WindLbs.showItemImage = function(r) {
	if(r.err.length > 0) {
		Wind.message.showResponse(r);
	} else {
		$('.item-img-view').attr('src', r.message.uploadfile_response.thumb);
		$('form #img').val(r.message.uploadfile_response.url);
		Wind.dialog.close();
	}
}

WindLbs.pickOk = function() {
	$('#longitude').val(WindLbs.pickLng);
	$('#latitude').val(WindLbs.pickLat);
}

WindLbs.pickCoordinate = function(lng, lat, okCallback) {
	var okCallback = okCallback || WindLbs.pickOk;
	var lng = parseFloat(lng) || null;
	var lat = parseFloat(lat) || null;
	
	var html = '<div id="pick-map" style="height:450px;"></div><div style="padding:8px 5px 0; background:#F5F5F5;border-top:1px solid #DDD;">拖动红色图标到相应位置然后点击右侧链接 <button class="btn fr" type="button" id="pickOk">确定已经选好</button></div>';
	Wind.message.show(html, '选取坐标', 800, 500);
		
	var map = new BMap.Map("pick-map");
	var point = lng ? new BMap.Point(lng, lat) : new BMap.Point();
	map.centerAndZoom(point, 12);
	map.enableScrollWheelZoom(); 

	function getPoint(result){		
		var cityName = result.name;
		if (!lng) {
			map.setCenter(cityName);
			WindLbs.pickLng = result.center.lng;			
			WindLbs.pickLat = result.center.lat;
			
			point = new BMap.Point(result.center.lng,result.center.lat);
		} else {
			WindLbs.pickLng = lng;			
			WindLbs.pickLat = lat;
			
			point = new BMap.Point(lng, lat);
		}
		
		var marker = new BMap.Marker(point);
		marker.enableDragging();
		map.addOverlay(marker);
	
		marker.addEventListener("dragend", function(e){
			WindLbs.pickLng = e.point.lng;
			WindLbs.pickLat = e.point.lat;
		})
	}
	
	var localCity = new BMap.LocalCity();
	localCity.get(getPoint);
	
	$('#pickOk').click(function() {
		Wind.dialog.close();
		if(typeof okCallback == 'function') {
			okCallback();
		}
	});
}
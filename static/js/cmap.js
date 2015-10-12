;(function(win, $) {
	$.extend($.fn, {
		CMap:function($options){
			var that = this, opts = $.extend({},$.fn.CMap.default, $options);
			var wrapper = $("#"+opts.wrapper);
			
			if(wrapper.length < 1){
				wrapper = $("<div id='"+opts.wrapper+"'></div>").css('z-index', '550').width("100%")
					.css('position', 'fixed').css('top', 0).height($(window).height()).appendTo("body");
			};
			
			$("<div id='"+opts.mapDiv+"'></div>").appendTo(wrapper);
			$("<div id='"+opts.closeDiv+"'>×</div>").appendTo(wrapper);

			$(window).resize(function(){
				$(wrapper).height($(window).height());
			});
			
			// 提示信息
			if(opts.showTips){
				$("<div id='"+opts.tipsDiv+"'>"+opts.tips+"</div>").appendTo(wrapper);
				$("#cmap-tips").css('position', 'absolute').css('top', '0px').css('z-index', '560')
					.css('opacity', 0.9).css("background", "#fff").css('padding', '3px 5px').css("color", "red")
					.css("font-size", '13px').css("left", "80px").css("top", "0px").css('display', 'block');
			};
			$("#"+opts.mapDiv).width("100%").height("100%")
				.css('position', 'absolute').css('top', '0px').css('z-index', '555');
			// 关闭按钮
			var style = 'position: absolute;top: 20px;z-index: 560;left: 10px;opacity: 0.9;font-size: 44px;color: red;'
					  + 'text-align: center;width: 35px;height: 35px;background: rgb(255, 255, 255);border-radius: 40px;'
					  + 'border: 3px solid #F00;padding-top: -10px;line-height: 35px;';

			$("#cmap-close").attr('style', style).appendTo($("#"+opts.wrapper))
				.click(function(){
					opts.close();
				});
			
			
			that.TXMap = new qq.maps.Map(document.getElementById(opts.mapDiv), {
				center: new qq.maps.LatLng(opts.lat, opts.lng),
				zoom: opts.level,
				draggable: true
			});
			
			qq.maps.event.addListener(that.TXMap, "click", function(e){opts.click(e, opts)});
			
			if(opts.showMarker){
				var centerPoint = new qq.maps.LatLng(opts.lat, opts.lng);
				//console.log(centerPoint);
				var marker = new qq.maps.Marker({
					position: centerPoint,
					map: that.TXMap
				});
			}
			
			wrapper.show();
			
			return that;
		}
	});
	$.fn.CMap.default = {
		MAP_KEY: win.MAP_KEY || "IABBZ-BQMW4-AU4UY-XVS2P-VNM76-R2BMO",
		mapDiv : "cmap-container",
		wrapper : "cmap-wrapper",
		closeDiv : "cmap-close",
		tipsDiv : "cmap-tips",
		latClass : ".cmap-lat",
		lngClass : ".cmap-lng",
		addressClass : ".cmap-address",
		level : 15,
		tips : "点击地图选择位置",
		showTips : true,
		showMarker : true,
		lat : 22.832200,
		lng : 108.288760,
		click : function(e, opts){
			var lat = e.latLng.getLat().toFixed(6);
			var lng = e.latLng.getLng().toFixed(6);
			var url3 = encodeURI("http://apis.map.qq.com/ws/geocoder/v1/?location=" 
					+ e.latLng.getLat() + "," + e.latLng.getLng() 
					+ "&key="+opts.MAP_KEY+"&output=jsonp&&callback=?");
			
			$.getJSON(url3, function (result) {
				if(result.result == 'undefined'){
					return false;
				};
				opts.onResult(lat, lng, result.result);
				opts.close();
			});
		},
		onResult : function(lat, lng, res){
			var latObj = $(this.latClass);
			var lngObj = $(this.lngClass);
			var addressObj = $(this.addressClass);
			latObj.is("input") ? latObj.val(lat) : latObj.text(lat);
			lngObj.is("input") ? lngObj.val(lng) : lngObj.text(lng);
			addressObj.is("input") ? addressObj.val(res.address) 
					: addressObj.text(res.address);	
					
		},
		close : function(){
			$('#' + this.wrapper).fadeOut(function() {$(this).remove(); });
		}
	};
})(window, jQuery);

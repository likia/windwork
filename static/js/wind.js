var Wind = Wind || {};
(function(win, $){	
	Wind.config = {
		basePath: '/',
		baseUrl: 'index.php?',
		hash: '',
		uid: 0,
		sid: null  // 当使用flash上传组件的时候，用于传递sesionn id
	};
	
	Wind.ui = {};
	Wind.util = {};
	Wind.form = {};
	
	// 鼠标位置
	var mouse = {x: 0, y: 0};
	$(document).mousemove(function (e) {
		if (e.pageX || e.pageY) {
			mouse.x = e.pageX;
			mouse.y = e.pageY;
		} else if (e.clientX || e.clientY) {
			mouse.x = e.clientX;
			mouse.y = e.clientY;
		}
	});
	
	Wind.util.addBookmark = function (title,url) {
		if (window.sidebar) {
		    window.sidebar.addPanel(title, url, "");
		} else if( document.all ) {
		    window.external.AddFavorite( url, title);
		} else if( window.opera && window.print ) {
		    return true;
		}
	}
	
	Wind.util.setHome = function (url){
		try{
			document.body.style.behavior='url(#default#homepage)';
			document.body.setHomePage(url);
		} catch(e){
			if(window.netscape) {
				try {
					netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
				} catch (e) {
					alert("此操作被浏览器拒绝！\n请在浏览器地址栏输入\"about:config\"并回车\n然后将 [signed.applets.codebase_principal_support]的值设置为'true',双击即可。");
				}
				var prefs = Components.classes['@mozilla.org/preferences-service;1'].getService(Components.interfaces.nsIPrefBranch);
				prefs.setCharPref('browser.startup.homepage', url);
			 }
		}
	}
	
	Wind.util.mobilePreview = function(url) {
		var options = {
			noBtn: false,
			height:544,
			width:320,
			title: '',
			content: '<div class="phone-preview-bg"><iframe src="'+url+'"></iframe></div>'
		};
		Wind.dialog.open(options);
		$('#dialog-main').css('overflow', 'hidden');
		$('#dialog-box a.close').css('background-position', '-33px 0');
	}
	
	/**
	 * 生成URL
	 * @param json params {name :'value', ...}
	 */
	Wind.util.makeUrl = function(recourceId, isAjax, params) {
		var params  = params || {};	
		var isAjax  = isAjax || false;	
		var url = Wind.config.baseUrl + recourceId;	
		
		if(Wind.config.baseUrl.indexOf('?') == -1) {
			url += '?';
		}
		
		for(var i in params) {
			url += '&' + encodeURIComponent(i) + '=' + encodeURIComponent(params[i]);
		}
		url += '&hash=' + Wind.config.hash;
		// 使用jsonp并加随机数保证URL唯一
		if(isAjax) {
			url += '&ajax_callback=?';
		}
			
		return url;	
	}
	
	/**
	 * 生成Ajax请求URL
	 *
	 * @param string recourceId mod/ctl/act...
	 * @param json params {name :'value', ...}
	 */
	Wind.util.makeAjaxUrl = function(recourceId, params) {
		var params = params || {};
		params._random = Math.random();
		return Wind.util.makeUrl(recourceId, true, params);
	}
		
	/**
	 * 获取请求GET变量
	 */
	Wind.util.getQueryParam = function(item) {
	    var svalue = location.search.match(new RegExp('[\?\&]' + item + '=([^\&]*)(\&?)', 'i'));
	    return svalue ? decodeURIComponent(svalue[1]) : '';
	}
	
	/**
	 * 
	 * @param string url
	 * @param string loadUrl
	 */
	Wind.util.ajaxGetAndLoad = function (url, loadUrl) {
		var loadUrl = loadUrl || null;
		Wind.util.ajaxGet(url, function() {
			var loadFnc = function() {
				if(loadUrl) {
					window.location.href = loadUrl;
				} else {					
			        window.location.reload(); 
				}
			}
			$('.close').click(function() {
				loadFnc();
			});
			
			setTimeout(function(){
				loadFnc();
			}, 2000);
		});
	}
	
	/**
	 * 
	 * @param string url
	 * @param json data
	 * @param string loadUrl
	 */
	Wind.util.ajaxPostAndLoad = function (url, data, loadUrl) {
		var loadUrl = loadUrl || null;
		Wind.util.ajaxPost(url, data, function() {
			var loadFnc = function() {
				if(loadUrl) {
					window.location.href = loadUrl;
				} else {					
			        window.location.reload(); 
				}
			}
			$('.close').click(function() {
				loadFnc();
			});
			
			setTimeout(function(){
				loadFnc();
			}, 2000);
		});
	}
	
	/**
	 * 访问统计
	 * @param string uri 要统计的URI
	 * @param string showIn 在哪里显示统计结果，支持标签、class、id
	 */
	Wind.util.stats = function(uri, showIn) {
		var url = Wind.util.makeUrl('system.misc.stats/' + encodeURIComponent(encodeURIComponent(uri)));
		$.getJSON(url, function(r) {
			$(showIn).html(r);
		})
	}
	
	/**
	 * 表单验证
	 * 需要先加载static/js/jquery.validate.min.js,static/js/locale/jquery.validate.messages.zh_CN.js
	 * 代用代码放在 $(function(){}) 函数中
	 */
	Wind.form.validate = function(selected, rules, messages) {	
		return selected.validate({
			rules: rules,
			messages: messages,
			errorPlacement: function(error, element) {
				error.appendTo( element.next() );				
			},
			success: function(label) {
				label.html("&nbsp;").addClass("checked");
			}
		});
	}
	
	/**
	 * json转成url请求串
	 */
	Wind.util.json2Reqstr = function (json) {
		var elements = new Array();
		if(typeof json == 'object') {
			for(var name in json) {
				json[name] = typeof json[name] == 'undefined' ? '' : json[name];
				elements.push(encodeURIComponent(name.toString()) + "=" + encodeURIComponent(json[name].toString()));
			}
		}
		
		return elements.join('&');
	}

	/**
	 * 窗口全屏显示
	 */
	Wind.util.fullscreen = function () {
		window.moveTo(0,0);
	    window.resizeTo(screen.width,screen.height);
	}
	
	/**
	 * 弹出窗口显示二维码
	 */
	Wind.util.showQRCode = function(text, title) {
		Wind.message.show('<img src="' + Wind.config.basePath + 'qrcode.php?text=' + text + '" width="300" height="300" />', title, 320, 330);
	}
	
	/**
	 * Div窗口
	 */
	Wind.dialog = {
		// TODO 可以同时打开多个窗口，新窗口就就窗口之上。。。
		/**
		 * 在页面中创建弹出对话框
		 * 
		 * @param Object option = {title: '', width: 320, height: 160, content: '', dragTag: null}
		 */
		open: function(option) {
			var option  = option || {};
		    var height  = option.height || 88;
		    var width   = option.width || 300;
		    var content = option.content || '<div id="ajax-loading"></div>';
		    var dragTag = option.dragTag || null;
		    var yesText = option.yesText || '确定';
		    var yesBtn  = option.yesBtn || false;
		    var yesFn   = option.yesFn || function() {Wind.dialog.close();};
		    var noBtn   = typeof option.noBtn == 'undefined' ? true : option.noBtn;
		    var noText  = option.noText || '取消';
		    var noFn    = option.noFn || function() {Wind.dialog.close();};
		    var title   = typeof option.title == 'undefined' ? '提示信息' : option.title;

		    var pos = Wind.dialog.getPos(width, height);
		    $('#dialog-box').css('left', pos.left + 'px');
		    $('#dialog-box').css('top', pos.top + 'px');

		    $('#dialog-box, #dialog-box-out').remove();

		    var style = [
		        'left:' + pos.left + 'px',
		        'top:' + pos.top + 'px',
		        'width:' + width + 'px'
			].join(';');
		    
		    var contentH = ' style="height:' + ((parseInt(height) == 0) ? 'auto' : height + 'px"');
		    
		    var footer = [
		        yesBtn ? '<button type="button" class="yesBtn btn">' + yesText + '</button>' : '',
		        noBtn  ? '<button type="button" class="noBtn btn">' + noText + '</button>' : ''
		    ].join('');
		    
		    var html = [
		        '<div id="dialog-box-out"></div>', // 半透明背景
		        '<div id="dialog-box" style="' + style + '">',
		        '  <a class="close"></a>',
		        '  <div class="dialog-box-in">',
		        '    <div class="in-wrap">',
		              (title == '' ? '' : '<h1 id="dialog-title" class="dialog-title">' + title + '</h1>'),
		        '      <div id="dialog-main" class="dialog-main" ' + contentH + '>' + content + '</div>',
		              (footer == '' ? '' : '<div id="dialog-footer" class="dialog-footer">' + footer + '</div>'),
		        '    </div>',
		        '  </div>',
		        '</div>'
		    ].join('');
		    $('body').append(html);

		    $('#dialog-box .close').click(function () {Wind.dialog.close();});
		    $('#dialog-box .noBtn').click(function () {Wind.dialog.close();});
		    
		    if(yesBtn) {
		    	$('#dialog-box .yesBtn').click(yesFn);
		    }

			// 改变窗口大小时，调整窗口位置
		    $(window).resize(function () {
		        if ($('#dialog-box').height() == null) return;
		        var pos = Wind.dialog.getPos(width, height);
		        $('#dialog-box').css('left', pos.left + 'px');
		        $('#dialog-box').css('top', pos.top + 'px');
		    });

			// 按Esc键关闭窗口
			$(document).keydown(function(e) {
				if(e.keyCode == 27) {
					Wind.dialog.close();
					$(this).unbind();
				}
			});
			
		    if (typeof dragTag != 'undefined' && dragTag) {
		        Wind.ui.dragable(document.getElementById('dialog-box'), dragTag);
		    } else if($('.dialog-title').length > 0) {
		    	Wind.ui.dragable(document.getElementById('dialog-box'), 'dialog-title');
			}
		},

	    /**
	     * 隐藏弹出窗口
	     *
	     * @param int time 逐渐隐藏时间 单位为毫秒
	     */
		close: function (time) {
	        var time = time || 0;
	        
	    	if(!time) {
	    		$('#dialog-box-out').remove();
	    		$('#dialog-box').remove();
	    	} else {
	    		$('#dialog-box-out').fadeOut(time);
	    		$('#dialog-box').fadeOut(time);
	    	}
	    },
	    
		/**
		 * 获取浮动窗口层的位置
		 */
		getPos: function (width, height) {
		    var pageWidth = window.innerWidth;
		    var pageHeight = window.innerHeight;
		    if (typeof pageWidth != "number") {
		        if (document.compatMode == "CSS1Compat") {
		            pageWidth = document.documentElement.clientWidth;
		            pageHeight = document.documentElement.clientHeight;
		        } else {
		            pageWidth = document.body.clientWidth;
		            pageHeight = document.body.clientHeight;
		        }
		    }

		    var top = ((pageHeight - height) / 3) + $(window).scrollTop();
		    var left = (pageWidth - width) / 2 + $(window).scrollLeft();

		    return { 'left': left, 'top': top };
		}
	};
	
	Wind.cookie = {
		/**
		 * 设置cookie
		 * 
		 * @param string name
		 * @param string value
		 * @param Day expireDays 多少天后过期
		 */
		set: function (name, value, expireDays) {
		    var exp = '';
		
		    if (expireDays != null) {
		        var exDate = new Date();
		        exDate.setDate(exDate.getDate() + expireDays);
		        exp = ";expires=" + exDate.toGMTString();
		    }
		
		    document.cookie = name + "=" + escape(value) + exp + "; path=/";
		},
		
		/**
		 * 读取cookie
		 *
		 * @param string name
		 */
		get: function (name) {
		    if (document.cookie.length > 0) {
		        start = document.cookie.indexOf(name + "=");
		        if (start != -1) {
		            start = start + name.length + 1;
		            end = document.cookie.indexOf(";", start);
		            if (end == -1) end = document.cookie.length;
		
		            return unescape(document.cookie.substring(start, end));
		        }
		    }
		
		    return "";
		},
		
		/**
		 * 删除cookie
		 *
		 * @param string name
		 */
		remove: function (name) {
		    Wind.cookie.set(name, "", -1);
		}
	}
	
	/**
	 * 使层可拖动
	 * 
	 * @param string o 可拖动层的id
	 * @param string moveTag 光标显示为可移动的元素， 可用id，类，标签，如#xx、.xx或xx 
	 */
	Wind.ui.dragable = function (o, moveTag) {
	    if (typeof o == "string") o = document.getElementById(o);
	
	    if (typeof moveTag == "string") {
	        moveTag = document.getElementById(moveTag);
	        o.onmouseover = function (a) {
	            moveTag.style.cursor = 'move';
	        }
	
	        o.onmouseout = function (a) {
	            moveTag.style.cursor = 'normal';
	        }
	    }
	
	    o.orig_x = parseInt(o.style.left) - document.body.scrollLeft;
	    o.orig_y = parseInt(o.style.top) - document.body.scrollTop;
	    o.orig_index = o.style.zIndex;    
	    
	    moveTag.onmousedown = function (a) {
	        this.style.zIndex = 10000;
	        var d = document;
	        if (!a) a = window.event;
	        var x = a.clientX + d.body.scrollLeft - o.offsetLeft;
	        var y = a.clientY + d.body.scrollTop - o.offsetTop;
	
	        d.ondragstart = "return false;"
	        d.onselectstart = "return false;"
	        d.onselect = "document.selection.empty();"
	
	        if (o.setCapture) {
	            o.setCapture();
	        } else if (window.captureEvents) {
	            window.captureEvents(Event.MOUSEMOVE | Event.MOUSEUP);
	        }
	
	        d.onmousemove = function (a) {
	            if (!a) a = window.event;
	            $('#' + o.id).css('left', a.clientX + document.body.scrollLeft - x + 'px');
	            $('#' + o.id).css('top', a.clientY + document.body.scrollTop - y + 'px');
	
	            o.orig_x = parseInt(o.style.left) - document.body.scrollLeft;
	            o.orig_y = parseInt(o.style.top) - document.body.scrollTop;
	        }
	
	        d.onmouseup = function () {
	            if (o.releaseCapture) {
	                o.releaseCapture();
	            } else if (window.captureEvents) {
	                window.captureEvents(Event.MOUSEMOVE | Event.MOUSEUP);
	            }
	
	            d.onmousemove = null;
	            d.onmouseup = null;
	            d.ondragstart = null;
	            d.onselectstart = null;
	            d.onselect = null;
	            o.style.cursor = "normal";
	            o.style.zIndex = o.orig_index;
	        }
	    }
	
	    var orig_scroll = window.onscroll ? window.onscroll : function () { };
	
	    window.onscroll = function () {
	        orig_scroll();
	        o.style.left = o.orig_x + document.body.scrollLeft;
	        o.style.top = o.orig_y + document.body.scrollTop;
	    }
	}
	
	/**
	 * 格式化消息
	 */
	Wind.ui.messageFormat = function (msgs) {
		var msgs = msgs || '';
		var msg = '';
		if(typeof msgs == 'object' && msgs.toString().length > 0) {
			msg = '<ul>';
			for(var i in msgs) {
				msg += '<li>'+msgs[i]+'</li>';
			}
			msg += '</ul>';
			
			return msg;
		}
		
		return msgs;
	}
		
	/**
	 * 显示加载状态
	 */
	Wind.ui.showLoading = function (apendTo, x, y) {
		var apendTo = apendTo || 'body';
		var x = x || (mouse.x - 16);
		var y = y || (mouse.y - 20);
		
		var id = parseInt(100000000*Math.random());
	
	    $('.ajax-loading').remove();
	    $(apendTo).append('<div id="ajax-loading-'+id+'" class="ajax-loading"></div>');
	    $('.ajax-loading').show();
	    $('.ajax-loading').css('left', x + 'px');
	    $('.ajax-loading').css('top', y + 'px');
		
		setTimeout(function() {
			$('#ajax-loading-'+id).remove();
		}, 10000)
	}
	
	/**
	 * 隐藏加载状态
	 */
	Wind.ui.hideLoading = function () {
	    $('body .ajax-loading').remove();
	}
	
	Wind.message = {}
	
	/**
	 * 显示提示信息，过hideTime秒钟后消失
	 * 
	 * @param string content 内容
	 * @param string title 标题
	 * @param int width 提示框宽（可选）
	 * @param int height 提示框高（可选）
	 */
	Wind.message.show = function (content, title, width, height) {
	    Wind.dialog.open({
	    	title: title, 
	    	width: width, 
	    	height: height,
			yesBtn: false,
			noBtn: false,
	    	content: '<div class="show-message">' + Wind.ui.messageFormat(content) + '</div>'
	    });

		$(document).keydown(function(e){
		    if(13 == e.keyCode) {
				Wind.dialog.close();
			}
		});
	}
	
	/**
	 * 显示从服务器响应的消息
	 * 
	 * @param Object json
	 */
	Wind.message.showResponse = function(json, okCallback) {
		if (json.err.length > 0) {
			Wind.message.showError(json.err);
		} else if (json.warn.length > 0) {
			var message = Wind.ui.messageFormat(json.warn);
			message = '<div class="warn">'+message+'</div>';
			Wind.message.show(message);
		} else {
			var message = json.message.length > 0 ? json.message : json.ok;
			message = '<div class="ok">' + message + '</div>'
			Wind.message.show(message);
			
			if(typeof okCallback == 'function') {
				okCallback();
			}
		}
	}
	
	/**
	 * 通过GET发送ajax请求并显示返回结果
	 */
	Wind.util.ajaxGet = function(url, okCallback) {
		Wind.ui.showLoading();
		$.getJSON(url, function(r) {
			Wind.ui.hideLoading();
			Wind.message.showResponse(r, okCallback);
		});
	} 
	
	/**
	 * 通过POST发送ajax请求并显示返回结果
	 */
	Wind.util.ajaxPost = function(url, data, okCallback) {
		Wind.ui.showLoading();
		$.post(url, data, function(r) {
			Wind.ui.hideLoading();
			Wind.message.showResponse(r, okCallback);
		}, 'json');
	} 

	/**
	 * 显示错误信息
	 */
	Wind.message.showError = function (content, title, width, height) {
		var title = title || '错误提示';
		content = Wind.ui.messageFormat(content);
		content = '<div style="padding:8px; color:#F00;">'+content+'</div>';
		Wind.message.show(content, title, width, height);
	}
	
	/**
	 * 生成 UUID
	 */
	Wind.util.makeGuid = function () {
	    var S4 = function () {
	        return (((1 + Math.random()) * 0x10000) | 0).toString(16).substring(1);
	    };
		
	    return S4() + S4() + "-" + S4() + "-" + S4() + "-" + S4() + "-" + Math.random().toString(16).substr(2, 12);
	}
	
	/**
	 * 获取时间
	 * 格式 2012-02-20 09:00:31
	 */
	Wind.util.getDateTime = function () {
	    var date = new Date();
	    return date.getFullYear() + '-' + date.getMonth() + '-' + date.getDay() + ' ' + date.getHours() + ':' + date.getSeconds() + ':' + date.getMinutes();
	}
	
	/**
	 * 
	 */
	Wind.util.getMapPoint = function(callback, lng, lat) {
		var width = 760, height = 500;	
		var lng = lng > 0 ? lng : false;
		var lat = lat > 0 ? lat : false;
		
		Wind.dialog.open({title: '', width: width, height: height, content: ''});
		$('#dialog-main').width(width);
		$('#dialog-main').height(height);
		$('#dialog-box').append('<span style="position:absolute; left:16px; top:8px;">点击图片上的位置选择坐标。</span>');
	
		var map = new BMap.Map("dialog-main");
		
		if(lng && lat) {
			var point = new BMap.Point(lng, lat);  // 创建点坐标
			var marker = new BMap.Marker(point);   // 创建标注  
			map.centerAndZoom(point, 14);          // 初始化地图,设置中心点坐标和地图级别。
			map.addOverlay(marker);  
		} else {
	        map.centerAndZoom("阳朔", 12);         // 初始化地图,设置城市和地图级别。
		}
		
	    map.enableScrollWheelZoom();              // 启用滚轮放大缩小
		
	    map.addEventListener("click", function(e){
			callback(e);
	    });
	}
	
	Wind.user = {
		profile: null,
		
		login: function () {
			Wind.ajaxForm(Wind.util.makeAjaxUrl('user.account.login'), '登录', 350, 200);
		},
		
		logout: function () {
			var url  = Wind.util.makeAjaxUrl('user.account.logout');
			$.getJSON(url, function(r) {
				Wind.user.profile = r.message;
				Wind.user.showUserTopbar();
			});
		},
			
		/**
		 * 显示用户在页面顶部的个人信息
		 */
		showUserTopbar: function () {
			var profileUrl = Wind.util.makeUrl('profile');
			var logoutUrl  = Wind.util.makeUrl('logout');
			var registUrl  = Wind.util.makeUrl('register');
			var adminUrl   = Wind.util.makeUrl('admin');
			var ucenterUrl = Wind.util.makeUrl('user.account.center');
			
			$.getJSON(profileUrl, function(r) {
				var userActions = '';
				if(typeof r.message.profile == 'undefined' || r.err.length > 0 || !r.message.profile.uid) {
					// 显示登录注册链接
					userActions = '<a class="login_btn" href="javascript:;" onclick="Wind.user.login();return false;"><span>登录</a> '
					            + '<a class="register_btn" href="'+registUrl+'"><span>注册</span></a>';
				} else {
				    // 显示登录后操作链接
					var profile = r.message.profile;
					userActions = '<div class="user-menu"><dt><a href="'+ucenterUrl+'">个人中心</a></dt> ';
					userActions += '<dl>';
					if(profile.isadmin) {
						userActions += ' <dd><a href="'+adminUrl+'" target="_blank">系统管理</a></dd>';
					} 
					userActions += ' <dd><a href="'+Wind.util.makeUrl('pubservice.biz.publicaccount.list')+'">公众号管理</a></dd> '
					             + ' <dd><a href="'+profileUrl+'">个人设置</a></dd> '
					             + ' <dd><a href="'+logoutUrl+'" onclick="Wind.user.logout(); return false;">登出</a></dd>'
								 + '</dl></div>';
				}
				
				$('.header-login').html(userActions);
				$('.header-login').show();
				/*
				$('.user-menu').hover(
					function() {
					    $(this).find('dl').show();
				    },
					function() {
				    	$(this).find('dl').hide();
					}
				);
				*/
			});
		}
	}
		
	/**
	 * 加载配置信息，初始化js相关
	 */
	Wind.init = function (config) {
		//if(!Wind.config.sid) alert('请先初始化Wind.config.sid的值！');

		if(typeof config != 'undefined') {
			for(var i in config) {
				Wind.config[i] = config[i];
			}
		}
				
		Wind.config.loginUrl = Wind.util.makeAjaxUrl('user.account.login/forward:'+encodeURIComponent(encodeURIComponent(document.location.href)));
		
		/*
		document.write('<script type="text/javascript" src="'+Wind.config.basePath+'static/js/uploader/uploader.js"></script>');
		document.write('<link id="uploader-style" type="text/css" rel="stylesheet" href="'+Wind.config.basePath+'static/js/uploader/uploader.css" />');
		*/			
	}
		
	Wind.ajaxForm = function (url, title, width, height) {
		var width = width || 400;
		var height = height || 300;	
		url += (url.indexOf('?') == -1 ? '?' : '&')+'ajax=1';
		Wind.dialog.open({
			title: title, 
			width: width,
			height: height,
			noBtn: false
		});

		$.get(url, function(r) {
			var html = '';
			if(r.err.length > 0) {
				html = '<div class="msg">' 
				     + '  <p>' + r.err.join('<br />') + '</p>'
				     + '  <p>' + r.warn.join('<br />') + '</p>'
				     + '  <p>' + r.message + '</p>'
				     + '</div>';
			} else {
				html = r.message;
			}
			$('#dialog-main').html(html);
			if($('.dialog-title').length > 0) {
				Wind.ui.dragable(document.getElementById('dialog-box'), 'dialog-title');
			}
		}, 'json');
	}
	
	Wind.htmlForm = function(url, title, width, height) {	
	    width = width || 640;
	    height = height || 360;	
		$.get(url, function(r){
			Wind.message.show(r, title, width, height);
		}, 'html');
	}
	
	Wind.deleteItem = function(url, tag) {
		if(!confirm('你确定要删除吗？一旦删除将不可恢复！')) {
			return false;
		}
		
		url += '/ajax:1';
		Wind.message.show('正在删除', '删除记录', 260, 60);
		$.getJSON(url, function(r) {
			Wind.message.showResponse(r);
			if(r.err.length == 0) {
				$(tag).remove();
			}
		});
		return false;
	}
	/**
	 * 点击选择框全选选择框
	 * 
	 * @param object checkAll
	 * @param object items
	 */
	Wind.form.checkAll = function(checkAll, items) {
		checkAll.click(function(){
			if($(this).attr('checked')) {
				items.attr('checked', 'checked');
			} else {
				items.removeAttr('checked');
			}
		});
	}
	
	Wind.mousePos = {x: 0, y: 0};
	$(function(){	
	    $(document).mousemove(function (e) {
			if (e.pageX || e.pageY) {
				Wind.mousePos.x = e.pageX;
				Wind.mousePos.y = e.pageY;
			} else if (e.clientX || e.clientY) {
				Wind.mousePos.x = e.clientX;
				Wind.mousePos.y = e.clientY;
			}
	    })
		//Wind.user.showUserTopbar();
	});
})(window, jQuery);

document.addEventListener('WeixinJSBridgeReady', function onBridgeReady()  { 
  WeixinJSBridge.call('hideToolbar');
});

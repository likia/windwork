function copyToClipboard(txt, obj) {
	if (window.clipboardData) {
		window.clipboardData.clearData();
		if(clipboardData.setData("Text", txt)){
			Wind.message.show("复制成功");
		}
	} else if (navigator.userAgent.indexOf("Opera") != -1) {
		window.location = txt;
	} else if (window.netscape) {
		try {
			netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
		} catch (e) {
			var inputEle = "<textarea id='txt-area' style='width:320px;height:60px'>"+txt+"</textarea>";
			Wind.message.show('你的浏览器不支持自动复制功能，请按Ctrl+C或鼠标右键复制链接:'+inputEle, "提示", 350, 150);
			window.jQuery && window.jQuery("#txt-area").select();
		}
		var clip = Components.classes['@mozilla.org/widget/clipboard;1'].createInstance(Components.interfaces.nsIClipboard);
		if (!clip){
			return false;
		}
		var trans = Components.classes['@mozilla.org/widget/transferable;1'].createInstance(Components.interfaces.nsITransferable);
		if (!trans){
			return false;
		}
		trans.addDataFlavor("text/unicode");
		var str = new Object();
		var len = new Object();
		var str = Components.classes["@mozilla.org/supports-string;1"].createInstance(Components.interfaces.nsISupportsString);
		var copytext = txt;
		str.data = copytext;
		trans.setTransferData("text/unicode", str, copytext.length * 2);
		var clipid = Components.interfaces.nsIClipboard;
		if (!clip){
			return false;
		}
		if(clip.setData(trans, null, clipid.kGlobalClipboard)){
			Wind.message.show("复制成功！");
		}
	}else if(navigator.userAgent.indexOf("Chrome") != -1){
		var inputEle = "<textarea id='txt-area' style='width:320px;height:60px'>"+txt+"</textarea>";
		Wind.message.show('你的浏览器不支持自动复制功能，请按Ctrl+C或鼠标右键复制链接:'+inputEle, "提示", 350, 150);
		window.jQuery && window.jQuery("#txt-area").select();
	}
	return false;
}
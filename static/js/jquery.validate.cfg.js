var validateCfg = {
    login: {
		rules: {
			account: "required",
			secode: "required",
			password: "required"
		},
		messages: {
			account: "请输入账号",
			secode: "请输入验证码",
			password: "请输入密码"
		},
		errorPlacement: function(error, element) {
			error.appendTo( element.parent().find('.message') );	
		},
		success: function(label) {
			label.html("&nbsp;").addClass("checked");
		}
	}
}
// front-page pop up

//function to set cookies
function setCookie(name, value) {
	document.cookie = name + "=" + escape(value);
}

//function to read cookies
function getCookie(name) {
	var arr = document.cookie.match(new RegExp("(^| )" + name + "=([^;]*)(;|$)"));
	if (arr != null)
		return unescape(arr[2]);
	return null;
}

//function to pop up message once a day
$(function(){
		var now=new Date();
		var msgTime=now.getFullYear() + "-" + now.getMonth() + "-" + now.getDate();
		var pastTime=getCookie("msgTime");
		if (pastTime != msgTime)
		{
		setCookie("msgTime", msgTime);		
		$("#popupMSG").dialog({
		resizable : true,
			width : 500,
			modal : true,
			buttons : {
				OK : function() {
					$(this).dialog("close");
				}
			}
		});
	}
});
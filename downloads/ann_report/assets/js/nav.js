/* Javascript for hover function */
$('nav').hover(
	function(){
		$('.title').fadeIn();
		},
	function(){
		$('.title').fadeOut();
	}
);
/*Javascript for div select and display */
$(document).ready(function($){
	function getUrlVars() {
		var vars = {};
		var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
		vars[key] = value;
	});
	return vars;
		}
	var pageID = getUrlVars()["pageID"];
	var i = 0;
	$(".tableauPlaceholder").each(function(){
		i++;
		$(this).addClass('divID_'+i);
	});
	if(pageID == null){
		$('.divID_1').addClass('show');
	}else{
		$('.divID_'+pageID).addClass('show');
	}
});
$(document).ready(function($){
	var view_h = $('content').height();
	console.log(view_h);
	$('nav ul.icon').css('height',view_h + 100);
	$('.title .icon').css('height',view_h + 90);
});
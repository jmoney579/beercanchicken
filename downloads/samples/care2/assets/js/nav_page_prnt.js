/* PageID to Section ID Reference
	0 = quality
	1 = benefits
	2 = childcare
	3 = finding
	4 = paying
	5 = growth
	6 = health
	7 = intervention
	8 = resources
	9 = assistance
*/

$(document).ready(function($){

	function getUrlVars() {
		var vars = {};
		var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
		vars[key] = value;
	});
	return vars;
		}
	var pageID = getUrlVars()["pageID"];
	
	if(pageID == 0 || pageID == null || pageID == undefined){
		$(".section").css("display", "none");
		$("#quality").fadeIn()
	} else if (pageID == 1){
		$(".section").css("display", "none");
		$("#benefits").fadeIn();
	}else if(pageID == 2){
		$(".section").css("display", "none");
		$("#childcare").fadeIn();
	}else if(pageID == 3){
		$(".section").css("display", "none");
		$("#finding").fadeIn();
	}else if(pageID == 4){
		$(".section").css("display", "none");
		$("#paying").fadeIn();
	}else if(pageID == 5){
		$(".section").css("display", "none");
		$("#growth").fadeIn();
	}else if(pageID == 6){
		$(".section").css("display", "none");
		$("#health").fadeIn();
	}else if(pageID == 7){
		$(".section").css("display", "none");
		$("#intervention").fadeIn();
	}else if(pageID == 8){
		$(".section").css("display", "none");
		$("#resources").fadeIn();
	}else if(pageID == 9){
		$(".section").css("display", "none");
		$("#assistance").fadeIn();
	}
});
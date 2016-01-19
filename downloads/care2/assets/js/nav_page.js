/* PageID to Section ID Reference
	0 = trending
	1 = ccv
	2 = cc_business
	3 = fact_sheet
	4 = partnerships
	5 = publications
	6 = research
	7 = ccdbg
	8 = complaint
	9 = resources
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
	
	if(pageID == 0){
		$(".section").css("display", "none");
		$("#trending").fadeIn()
	} else if (pageID == 1 || pageID == null || pageID == undefined){
		$(".section").css("display", "none");
		$("#ccv").fadeIn();
	}else if(pageID == 2){
		$(".section").css("display", "none");
		$("#cc_business").fadeIn();
	}else if(pageID == 3){
		$(".section").css("display", "none");
		$("#fact_sheet").fadeIn();
	}else if(pageID == 4){
		$(".section").css("display", "none");
		$("#partnerships").fadeIn();
	}else if(pageID == 5){
		$(".section").css("display", "none");
		$("#publications").fadeIn();
	}else if(pageID == 6){
		$(".section").css("display", "none");
		$("#research").fadeIn();
	}else if(pageID == 7){
		$(".section").css("display", "none");
		$("#ccdbg").fadeIn();
	}else if(pageID == 8){
		$(".section").css("display", "none");
		$("#complaint").fadeIn();
	}else if(pageID == 9){
		$(".section").css("display", "none");
		$("#resources").fadeIn();
	}
});
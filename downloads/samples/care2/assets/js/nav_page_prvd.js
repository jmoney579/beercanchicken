/* PageID to Section ID Reference
	0 = cc_business
	1 = licensed
	2 = subsidy
	3 = vaquality
	4 = requirements
	5 = training
	6 = impact
	7 = emergency
	8 = resources
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
		$("#cc_business").fadeIn()
	} else if (pageID == 1 ){
		$(".section").css("display", "none");
		$("#licensed").fadeIn();
	}else if(pageID == 2){
		$(".section").css("display", "none");
		$("#subsidy").fadeIn();
	}else if(pageID == 3){
		$(".section").css("display", "none");
		$("#vaquality").fadeIn();
	}else if(pageID == 4 || pageID == null || pageID == undefined){
		$(".section").css("display", "none");
		$("#requirements").fadeIn();
	}else if(pageID == 5){
		$(".section").css("display", "none");
		$("#training").fadeIn();
	}else if(pageID == 6){
		$(".section").css("display", "none");
		$("#impact").fadeIn();
	}else if(pageID == 7){
		$(".section").css("display", "none");
		$("#emergency").fadeIn();
	}else if(pageID == 8){
		$(".section").css("display", "none");
		$("#resources").fadeIn();
	}
});
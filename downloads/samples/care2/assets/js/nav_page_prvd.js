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
		$(document).attr("title", "ChildcareVA - Providers - Child Care As a Profession");
	} else if (pageID == 1 ){
		$(".section").css("display", "none");
		$("#licensed").fadeIn();
		$(document).attr("title", "ChildcareVA - Providers - Become a Licensed Provider");
	}else if(pageID == 2){
		$(".section").css("display", "none");
		$("#subsidy").fadeIn();
		$(document).attr("title", "ChildcareVA - Providers - Become a Subsidy Provider");
	}else if(pageID == 3){
		$(".section").css("display", "none");
		$("#vaquality").fadeIn();
		$(document).attr("title", "ChildcareVA - Providers - Virginia Quality");
	}else if(pageID == 4 || pageID == null || pageID == undefined){
		$(".section").css("display", "none");
		$("#requirements").fadeIn();
		$(document).attr("title", "ChildcareVA - Providers - New Provider Requirements");
	}else if(pageID == 5){
		$(".section").css("display", "none");
		$("#training").fadeIn();
		$(document).attr("title", "ChildcareVA - Providers - Training & Profession Development");
	}else if(pageID == 6){
		$(".section").css("display", "none");
		$("#impact").fadeIn();
		$(document).attr("title", "ChildcareVA - Providers - IMPACT Registry");
	}else if(pageID == 7){
		$(".section").css("display", "none");
		$("#emergency").fadeIn();
		$(document).attr("title", "ChildcareVA - Providers - Emergency Preparedness");
	}else if(pageID == 8){
		$(".section").css("display", "none");
		$("#resources").fadeIn();
		$(document).attr("title", "ChildcareVA - Providers - Resources");
	}
});
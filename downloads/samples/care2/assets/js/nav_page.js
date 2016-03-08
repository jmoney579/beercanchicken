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
		$(document).attr("title", "ChildcareVA - Community - Trending Now");
	} else if (pageID == 1 || pageID == null || pageID == undefined){
		$(".section").css("display", "none");
		$("#ccv").fadeIn();
		$(document).attr("title", "ChildcareVA - Community - Child Care in Virginia");
	}else if(pageID == 2){
		$(".section").css("display", "none");
		$("#cc_business").fadeIn();
		$(document).attr("title", "ChildcareVA - Community - Business/Community Support");
	}else if(pageID == 3){
		$(".section").css("display", "none");
		$("#fact_sheet").fadeIn();
		$(document).attr("title", "ChildcareVA - Community - Virginia Fact Sheet");
	}else if(pageID == 4){
		$(".section").css("display", "none");
		$("#partnerships").fadeIn();
		$(document).attr("title", "ChildcareVA - Community - Partnerships");
	}else if(pageID == 5){
		$(".section").css("display", "none");
		$("#publications").fadeIn();
		$(document).attr("title", "ChildcareVA - Community - Publications and Reports");
	}else if(pageID == 6){
		$(".section").css("display", "none");
		$("#research").fadeIn();
		$(document).attr("title", "ChildcareVA - Community - Research & Statistics");
	}else if(pageID == 7){
		$(".section").css("display", "none");
		$("#ccdbg").fadeIn();
		$(document).attr("title", "ChildcareVA - Community - CCDBG State Plan");
	}else if(pageID == 8){
		$(".section").css("display", "none");
		$("#complaint").fadeIn();
		$(document).attr("title", "ChildcareVA - Community - File a Complaint");		
	}else if(pageID == 9){
		$(".section").css("display", "none");
		$("#resources").fadeIn();
		$(document).attr("title", "ChildcareVA - Community - Resources");
	}
});
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
		$(document).attr("title", "ChildcareVA - Parents - Why Quality Matters");
	} else if (pageID == 1){
		$(".section").css("display", "none");
		$("#benefits").fadeIn();
		$(document).attr("title", "ChildcareVA - Parents - Benefits of Licensed Child Care");
	}else if(pageID == 2){
		$(".section").css("display", "none");
		$("#childcare").fadeIn();
		$(document).attr("title", "ChildcareVA - Parents - Child Care & Early Learning");
	}else if(pageID == 3){
		$(".section").css("display", "none");
		$("#finding").fadeIn();
		$(document).attr("title", "ChildcareVA - Parents - Finding Child Care");
	}else if(pageID == 4){
		$(".section").css("display", "none");
		$("#paying").fadeIn();
		$(document).attr("title", "ChildcareVA - Parents - Paying for Child Care");
	}else if(pageID == 5){
		$(".section").css("display", "none");
		$("#growth").fadeIn();
		$(document).attr("title", "ChildcareVA - Parents - Child Growth & Development");
	}else if(pageID == 6){
		$(".section").css("display", "none");
		$("#health").fadeIn();
		$(document).attr("title", "ChildcareVA - Parents - Health & Safety");
	}else if(pageID == 7){
		$(".section").css("display", "none");
		$("#intervention").fadeIn();
		$(document).attr("title", "ChildcareVA - Parents - Early Intervention & Special Needs");
	}else if(pageID == 8){
		$(".section").css("display", "none");
		$("#resources").fadeIn();
		$(document).attr("title", "ChildcareVA - Parents - Resources");
	}else if(pageID == 9){
		$(".section").css("display", "none");
		$("#assistance").fadeIn();
		$(document).attr("title", "ChildcareVA - Parents - Other Assistance");
	}
});

//functions for updating the display of the enrollment totals for a section
function updateDelay(baseid) { //introduce a slight delay for updating the enrollments to make sure database has dealt with previous changes
	setTimeout(function() {
		updateEnrollmentTotalsDisplayAjax(baseid);
	}, 100);
}

function Delayrefresh(baseid) { //introduce a slight delay for updating the enrollments to make sure database has dealt with previous changes
	setTimeout(function() {
	location.reload();
	}, 100);
}

function updateEnrollmentTotalsDisplayAjax(baseid){
	return $.ajax({
		type: "POST",
		url: "/registration/enrollmenttotalsdisplay/",
		invoked: {
		    id: baseid,
		},
		data: {
			'section' : baseid,
			'csrfmiddlewaretoken' : $("input[name=csrfmiddlewaretoken]").val()
		},
		success: updateEnrollmentTotalsDisplaySuccess,
		dataType: 'html'
	});	
}

function updateEnrollmentTotalsDisplaySuccess(data, textStatus, jqXHR){
	$('#enrollmenttotalsdisplay'+this.invoked.id).html(data);
}







//functions for handling enrollments
//physical enrollment
$(function(){	
	$(".everything").on('click touch', ".enroll", function(){
		
		//this.id='enroll1_theme1'
		splitid = this.id.split('_');
		baseid = splitid[0].replace('enroll', '');
		themeid = ''
		if (splitid[1]) {
			themeid = splitid[1].replace('theme', '');
		}

		$.when(enrollAjax(baseid, "False", themeid)).done(updateDelay(baseid));
	});	
});

$(function(){
	$(".everything").on('click touch', ".badgeenroll", function(){
		//this.id='enroll1_theme1'
		splitid = this.id.split('_');
		baseid = splitid[0].replace('badgeenroll', '');
		themeid = ''
		if (splitid[1]) {
			themeid = splitid[1].replace('theme', '');
		}

		$.when(enrollAjax(baseid, "False", themeid)).done(updateDelay(baseid));
	});	
});

//online enrollment
$(function(){
	$(".everything").on('click touch', ".online_enroll", function(){
		splitid = this.id.split('_');
		baseid = splitid[1].replace('enroll', ''); //indices are one higher because we are splitting by undescore and online_enroll already has one. We are also just replacing enroll instead of online_enroll because of this split
		themeid = ''
		if (splitid[2]) {
			themeid = splitid[2].replace('theme', '');
		}

		$.when(enrollAjax(baseid, "True", themeid)).done(updateDelay(baseid));
	});	
});

$(function(){
	$(".everything").on('click touch', ".badgeonline_enroll", function(){
		splitid = this.id.split('_');
		baseid = splitid[1].replace('enroll', ''); //indices are one higher because we are splitting by undescore and online_enroll already has one. We are also just replacing enroll instead of online_enroll because of this split
		themeid = ''
		if (splitid[2]) {
			themeid = splitid[2].replace('theme', '');
		}

		$.when(enrollAjax(baseid, "True", themeid)).done(updateDelay(baseid));
	});	
});

function enrollAjax(baseid, online, themeid)
{
	return $.ajax({
		type: "POST",
		url: "/registration/enroll/",
		invoked: {
		    id : baseid,
		},
		data: {
			'section' : baseid,
			'online' : online,
			'csrfmiddlewaretoken' : $("input[name=csrfmiddlewaretoken]").val(),
			'themeid' : themeid
		},
		success: enrollSuccess,
		dataType: 'html'
	});	
}

function enrollSuccess(data, textStatus, jqXHR)
{
	$('#enrollmentbox'+this.invoked.id).html(data);
}



//functions for handling unenrollments
//physical unenrollments
$(function(){
	$(".everything").on('click touch',".unenroll", function(){
		baseid = this.id.replace('unenroll', '');
		unenrollAjax(baseid, "False");
	});	
});

$(function(){
	$(".everything").on('click touch', ".unenroll_confirmed", function(){
		var source = $('#unenroll_source').val();

		baseid = this.id.replace('unenroll_confirmed', '');
		
		if (source == 'my_schedule') {
			$.when(unenrollAjaxMySchedule(baseid, "True")).done(updateDelay(baseid));		
		} else {
			$.when(unenrollAjax(baseid, "True")).done(updateDelay(baseid));
		}
	});	
});


//online unenrollments
$(function(){
	$(".everything").on('click touch', ".online_unenroll", function(){
		baseid = this.id.replace('online_unenroll', '');
		unenrollAjax(baseid, "False");
	});	
});

$(function(){
	$(".everything").on('click touch',".online_unenroll_confirmed", function(){
		baseid = this.id.replace('online_unenroll_confirmed', '');

		var source = $('#unenroll_source').val();	
		if (source == 'my_schedule') {
			$.when(unenrollAjaxMySchedule(baseid, "True")).done(updateDelay(baseid));		
		} else {
			$.when(unenrollAjax(baseid, "True")).done(updateDelay(baseid));		
		}
	});	
});

function unenrollAjax(baseid, unenrollmentConfirmed) {
	$.ajax({
		type: "POST",
		url: "/registration/unenroll/",
		invoked: {
		    id: baseid,
		},
		data: {
			'section' : baseid,
			'unenrollment_confirmed' : unenrollmentConfirmed,
			'csrfmiddlewaretoken' : $("input[name=csrfmiddlewaretoken]").val()
		},
				
		success: unenrollSuccess,
		dataType: 'html'
	});
}

function unenrollAjaxMySchedule(baseid, unenrollmentConfirmed) {
	$.ajax({
		type: "POST",
		url: "/registration/unenroll/",
		invoked: {
		    id: baseid,
		},
		data: {
			'section' : baseid,
			'unenrollment_confirmed' : unenrollmentConfirmed,
			'csrfmiddlewaretoken' : $("input[name=csrfmiddlewaretoken]").val()
		},
				
		success: unenrollSuccessMySchedule,
		dataType: 'html'
	});
}

function unenrollAjax2(baseid, unenrollmentConfirmed) {
	$.ajax({
		type: "POST",
		url: "/registration/unenroll/",
		invoked: {
		    id: baseid,
		},
		data: {
			'schedule' : "yes",
			'section' : baseid,
			'unenrollment_confirmed' : unenrollmentConfirmed,
			'csrfmiddlewaretoken' : $("input[name=csrfmiddlewaretoken]").val()
		},
		dataType: 'html'
	});
}
function unenrollSuccess(data, textStatus, jqXHR)
{
	$('#enrollmentbox'+this.invoked.id).html(data);
}

function unenrollSuccessMySchedule(data, textStatus, jqXHR){
	if (data.indexOf('error') != -1) {
		$('#enrollmentbox'+this.invoked.id).html(data);
	} else {
		$('#enrollment-record'+this.invoked.id).remove();
	}
}


// functions to redisplay buttons, such as when user cancels out of unenrollment
$(function(){
	$(".everything").on('click touch', ".redisplay_section_buttons", function(){
		baseid = this.id.replace('redisplay_section_buttons', '');
		redisplaySectionButtonsAjax(baseid);
	});	
});

function clearForm(form) {
  $(':input', form).each(function() {
    switch (this.type) {		  	
	  case 'select-multiple':
	  case 'select-one':
		$(this).find("option[selected='selected']").removeAttr("selected");
	    break;	
      case 'password':
      case 'text':
 	  case 'textarea':
		$(this).attr('value', '');
	    break;
      case 'checkbox':
        $(this).removeAttr('checked'); 
	  case 'radio':
	    this.checked = false;		  
	}
		  
  });
}

$(function(){
    $("#course-filter-form-reset").on('click touch', function(){
    	clearForm($("#course-filter-form"));
    });
});



function redisplaySectionButtonsAjax(baseid) {

	$.ajax({
		type: "POST",
		url: "/registration/redisplay/",
		invoked: {
		    id: baseid,
		},
		data: {
			'section' : baseid,
			'csrfmiddlewaretoken' : $("input[name=csrfmiddlewaretoken]").val()
		},
		success: redisplaySectionButtonsSuccess,
		dataType: 'html'
	});
}

function redisplaySectionButtonsSuccess(data, textStatus, jqXHR)
{
	$('#enrollmentbox'+this.invoked.id).html(data);
}


//function to toggle display of sections when clicking on a course title
/*$(function(){
    $(".coursesectionsbutton").on('click touch', function(){
        baseid = this.id.replace('coursesectionsbutton', '');
        
        $('#coursesectionsbox'+baseid).slideToggle();
        $('#coursesectionsbox'+baseid).show();
        
        
    }); 
});*/

//function to toggle display of sections when clicking on a course title
$(function(){
    $(".coursestitle").on('click touch', function(){

        baseid = this.id.replace('coursestitle', '');
              
        getSectionsForCourseAjax(baseid);
        
        $('#coursesectionsbox'+baseid).slideToggle();
        $('#coursesectionsbox'+baseid).show();       
        
        $('#coursedetails'+baseid).slideToggle();
        $('#coursedetails'+baseid).show();
        
    }); 
});

function isEmpty( elem ){
    return !$.trim(elem.html())
}

function getSectionsForCourseAjax(baseid) {
	selectedinstructor = $('select#id_instructor option:selected').val();
	start_date = $('#id_start_date_limit').val()
	end_date = $('#id_end_date_limit').val()
	form_term = $('#form_term').val()
	online = $('#id_online').val() 
	
	if (isEmpty($('#coursesectionsbox'+baseid))) {

	
	
		$.ajax({
			type: "POST",
			url: "/registration/displaysectionsforcourse/",
			invoked: {
				id: baseid,
			},
			data: {
				'courseid' : baseid,
				'selectedinstructor' : selectedinstructor,
				'start_date' : start_date,
				'end_date' : end_date,
				'form_term' : form_term,
				'online:' : online,
				'csrfmiddlewaretoken' : $("input[name=csrfmiddlewaretoken]").val()
			},
			success: getSectionsForCourseSuccess,
			dataType: 'html'
		});
	}
}

function getSectionsForCourseSuccess(data, textStatus, jqXHR)
{
	$('#coursesectionsbox'+this.invoked.id).html(data);
}




//function to create color blocks for each tag
$(function(){
	$(".tag_info").each(function(){
		$(this).css("background",$(this).attr("role"));
		});
	});

//function to toggle display of course details when hovering on a course title
/*$(function(){
	$(".coursetitleline").on("touch click", function(){
			$(this).next(".coursedetails").slideToggle();
		});
	$(".coursetitleline").on("mouseenter", function(){
			var $cd=$(this).next(".coursedetails");
			if ($cd.css("display")=="none"){
				$cd.slideDown();
			}
		});	
	$("#content>ul>li").on("mouseleave", function(e){		
		$(this).find(".coursedetails, .coursesectionsbox").slideUp();
		});	
});*/



// functions to redisplay buttons, such as when user cancels out of unenrollment
$(function(){
	$(".attendance_code_generator").on('click touch', function(){
		baseid = this.id.replace('attendance_code_generator', '');
		generateAttendanceCodeAjax(baseid);
	});	
});

function generateAttendanceCodeAjax(baseid) {
	$.ajax({
		type: "POST",
		url: "/registration/attendance_code_generator/",
		invoked: {
		    id: baseid,
		},
		data: {
			'meeting' : baseid,
			'csrfmiddlewaretoken' : $("input[name=csrfmiddlewaretoken]").val()
		},
		success: generateAttendanceCodeSuccess,
		dataType: 'html'
	});
}

function generateAttendanceCodeSuccess(data, textStatus, jqXHR)
{
	$('.attendance_code_box').html(data);
}
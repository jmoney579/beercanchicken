$(function() {
	$( "#id_start_date_limit" ).datepicker({
		defaultDate: "+1w",
		changeMonth: true,
		numberOfMonths: 3,
		onClose: function( selectedDate ) {
			$( "#id_end_date_limit" ).datepicker( "option", "minDate", selectedDate );
		}
	});
	
	$( "#id_end_date_limit" ).datepicker({
		defaultDate: "+1w",
		changeMonth: true,
		numberOfMonths: 3,
		onClose: function( selectedDate ) {
			$( "#id_start_date_limit" ).datepicker( "option", "maxDate", selectedDate );
		}
	});
});

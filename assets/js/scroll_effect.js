$(document).ready(function(){
		/*var i = 0;
		$(".super_title").each(function(){
		i++;
		$(this).addClass('div_'+i);
		});*/
	   $(window).bind('scroll', function() {
	   var navHeight = $( window ).height() - 600;
			 if ($(window).scrollTop() > navHeight) {
				 $('.sticky').addClass('snaptotop');
			 }
			 else {
				 $('.sticky').removeClass('snaptotop');		 
			 }
				 
		});
		console.log("Window position is "+ $(window).height());
	});

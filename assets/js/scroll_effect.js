$(document).ready(function(){
		var i = 0;
		$(".super_title").each(function(){
		i++;
		$(this).addClass('div_'+i);
		});
	   $(window).bind('scroll', function() {
	   var navHeight = $( window ).height() - 100;
			 if ($(window).scrollTop() > navHeight) {
				 $('.sticky').addClass('snaptotop');
				 $('.top_content').addClass('stomped');
				 $('.super_title').addClass('stomped2');
			 }
			 else {
				 $('.sticky').removeClass('snaptotop');
				 $('.top_content').removeClass('stomped');
				 $('.super_title').removeClass('stomped2');		 
			 }
				 
		});
		console.log("Window position is "+ $(window).height());
	});
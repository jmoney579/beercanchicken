$(document).ready(function(){
		var i = 0;
		$(".hashtag").each(function(){
		i++;
		$(this).addClass('div_'+i);
		});
	   $(window).bind('scroll', function() {
	   var navHeight = $( window ).height() - 100;
			 if ($(window).scrollTop() > navHeight) {
				 $('nav').addClass('navbar-fixed-top');
			 }
			 else {
				 $('nav').removeClass('navbar-fixed-top');				 
			 }
		/* Code for fixed hashtags */
		/*var navHeight2 = $(window).height() - 150;
			 if ($(window).scrollTop() > navHeight2) {
				 $('.div_1').addClass('fixed_top');
			 }
			 else {
				 $('.div_1').removeClass('fixed_top');				 
			 }			
		var navHeight3 = $(window).height() + 1050;
			 if ($(window).scrollTop() > navHeight3) {
				 $('.div_2').addClass('fixed_top2');
			 }
			 else {
				 $('.div_2').removeClass('fixed_top2');				 
			 }	
		var navHeight4 = $(window).height() + 2270;
			 if ($(window).scrollTop() > navHeight4) {
				 $('.div_3').addClass('fixed_top3');
			 }
			 else {
				 $('.div_3').removeClass('fixed_top3');				 
			 }	
		var navHeight5 = $(window).height() + 3500;
			 if ($(window).scrollTop() > navHeight5) {
				 $('.div_4').addClass('fixed_top4');
			 }
			 else {
				 $('.div_4').removeClass('fixed_top4');				 
			 }*/		 
		});
		console.log("Window position is "+ $(window).height());
		$(function() {
		  $('a[href*="#"]:not([href="#"])').click(function() {
			if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
			  var target = $(this.hash);
			  target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
			  if (target.length) {
				$('html, body').animate({
				  scrollTop: target.offset().top - 220
				}, 1200);
				return false;
			  }
			}
		  });
		});
	});
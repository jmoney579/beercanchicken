			$(document).ready(function(){
			$(".dropdown_nav").click(function(){
					if ($(".navbar").is(":hidden")){
						$(".navbar").fadeIn();
						$(".updates").fadeIn();
						$(".site_title").addClass("slide");
						$(".dropdown_nav").addClass("close_me");
						$(".dropdown_nav").attr("title", "Click to close");
					}else{
						$(".navbar").fadeOut();
						$(".updates").fadeOut();
						$(".site_title").removeClass("slide");
						$(".dropdown_nav").removeClass("close_me");
					}
				});
			$(".super_title").click(function(){
				if ($(".navbar").is(":hidden")){
						$(".navbar").fadeIn();
					}else{
						$(".navbar").fadeOut();
							}
				})
			$(".updates").append('<h3>Recent Updates</h3><ul><li><a href="/portfolio/fusion.html">Fusion (VDSS Intranet)</a></li><li><a href="/portfolio/fmf.html">Foster My Future</a></li><li><a href="/portfolio/multimedia.html">Multimedia</a></li><li><a href="/portfolio/nam.html">National Adoption Month</a></li></ul>');

			});

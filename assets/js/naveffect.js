			$(document).ready(function(){
			$(".site_title").click(function(){
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
			$(".updates").append('<h3>Recent Updates</h3><ul><li><a href="/portfolio/port.html">Online Portfolios</a></li><li><a href="/portfolio/nam.html">National Adoption Month</a></li><li><a href="/portfolio/multimedia.html">Multimedia</a></li><li><a href="/portfolio/annual.html">VDSS Annual Report</a></li></ul>');

			});

			$(document).ready(function(){
			$(".site_title").click(function(){
				if ($(".navbar").is(":hidden")){
						$(".navbar").fadeIn();
						$(".site_title").addClass("slide");
					}else{
						$(".navbar").fadeOut();
						$(".site_title").removeClass("slide");
							}
				});
			$(".super_title").click(function(){
				if ($(".navbar").is(":hidden")){
						$(".navbar").fadeIn();
					}else{
						$(".navbar").fadeOut();
							}
				})
			});

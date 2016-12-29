			$(document).ready(function(){
			$(".site_title").click(function(){
				if ($(".navbar").is(":hidden")){
						$(".navbar").fadeIn();
						$(".updates").fadeIn();
						$(".site_title").addClass("slide");
					}else{
						$(".navbar").fadeOut();
						$(".updates").fadeOut();
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
			$(".updates").append('<h3>Recent Updates</h3><ul><li><a href="#">Multimedia</a></li><li><a href="#">National Adoption Month</a></li><li><a href="#">VDSS Annual Report</a></li><li><a href="#">VDSS Career Campaign</a></li></ul>');
			});

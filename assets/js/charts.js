 
   $(document).ready(function(){
	var counter = 0; 
	var windowHeight = $(window).height();
   		 $(window).on('scroll', function() {
			windowHeight = windowHeight - 270;
			var pagePosition = $(window).scrollTop();
				if (pagePosition > windowHeight && counter == 0) {	
				$(".skills").fadeOut();
				var chart = new CanvasJS.Chart("chartContainer", {
				 interactivityEnabled: false,
				title:{
					text:"Web Magic"				

				},
							animationEnabled: true,
				axisX:{
					interval: 1,
					gridThickness: 0,
					labelFontSize: 14,
					labelFontStyle: "normal",
					labelFontWeight: "normal",
					labelFontFamily: "Lucida Sans Unicode"

				},
				axisY2:{
					interlacedColor: "rgba(1,77,101,.2)",
					gridColor: "rgba(1,77,101,.1)",
				},
				data: [
				{     
					type: "bar",
					lineThickness: 1,
					name: "skills",
					axisYType: "secondary",
					color: "#009bc7",				
					dataPoints: [
					{y: 4, label: "PHP"  },	
					{y: 4, label: "Django"  },
					{y: 4, label: "Angular"  },					
					{y: 5, label: "Wordpress"  },
					{y: 5, label: "Evoq(DNN)"  },	
					{y: 5, label: "Adobe Experience Manager"  },		
					{y: 6, label: "Bootstrap"  },				
					{y: 7, label: "Percussion CM System"  },					
					{y: 8, label: "CSS 3"  },
					{y: 8, label: "HTML 5"  },
					]
				}	
				]
			});

			chart.render();

			var chart2 = new CanvasJS.Chart("chartContainer2", {
				 interactivityEnabled: false,
				title:{
					text:"Software"				

				},
							animationEnabled: true,
				axisX:{
					interval: 1,
					gridThickness: 0,
					labelFontSize: 14,
					labelFontStyle: "normal",
					labelFontWeight: "normal",
					labelFontFamily: "Lucida Sans Unicode"

				},
				axisY2:{
					interlacedColor: "rgba(1,77,101,.2)",
					gridColor: "rgba(1,77,101,.1)",
				},
				data: [
				{     
					type: "bar",
					lineThickness: 1,
					name: "skills",
					axisYType: "secondary",
					color: "#e88817",				
					dataPoints: [		
					{y: 3, label: "Adobe Illustrator"  },
					{y: 4, label: "Adobe Premiere Pro"  },
					{y: 5, label: "Adobe InDesign"  },				
					{y: 8, label: "Adobe Dreamweaver"  },
					{y: 8, label: "Adobe Photoshop"  },
					]
				}	
				]
			});

			chart2.render();

			var chart3 = new CanvasJS.Chart("chartContainer3", {
				 interactivityEnabled: false,
				title:{
					text:"Others"				

				},
							animationEnabled: true,
				axisX:{
					interval: 1,
					gridThickness: 0,
					labelFontSize: 14,
					labelFontStyle: "normal",
					labelFontWeight: "normal",
					labelFontFamily: "Lucida Sans Unicode"

				},
				axisY2:{
					interlacedColor: "rgba(1,77,101,.2)",
					gridColor: "rgba(1,77,101,.1)",
				},
				data: [
				{     
					type: "bar",
					lineThickness: 1,
					name: "skills",
					axisYType: "secondary",
					color: "#087a02",				
					dataPoints: [	
					{y: 4, label: "jQuery"  },				
					{y: 6, label: "Bootstrap"  },	
					{y: 3, label: "Adobe Illustrator"  },
					{y: 6, label: "Instructional Design"  },
					{y: 6, label: "Course Development"  },
					{y: 8, label: "Microsoft Office"  },					
					{y: 8, label: "Version Control"  },
					]
				}	
				]
			});
			chart3.render();
			counter = 1;
			console.log("window Height is " + windowHeight);
			console.log("Page position is " + pagePosition);
			console.log ("Counter is " + counter);
		}
		});

	});
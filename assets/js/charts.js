 
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
					text:"Web Magic",
					fontFamily: "Raleway",
					fontWeight: "lighter",
					fontSize: 20					

				},
							animationEnabled: true,
				axisX:{
					interval: 1,
					gridThickness: 0,
					labelFontSize: 12,
					labelFontStyle: "normal",
					labelFontWeight: "normal",
					labelFontFamily: "Raleway",
					tickThickness: 0

				},
				axisY2:{
					interlacedColor: "#fff",
					gridColor: "#fff",
					valueFormatString: " ",
					tickThickness: 0,
					lineColor: "#fff"
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
					{y: 5, label: "jQuery"  },					
					/*{y: 5, label: "Wordpress"  },
					{y: 5, label: "Evoq(DNN)"  },*/	
					{y: 5, label: "Content Management*"  },		
					{y: 6, label: "Bootstrap"  },								
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
					text:"Software",
					fontFamily: "Raleway",
					fontWeight: "lighter",
					fontSize: 20				

				},
							animationEnabled: true,
				axisX:{
					interval: 1,
					gridThickness: 0,
					labelFontSize: 12,
					labelFontStyle: "normal",
					labelFontWeight: "normal",
					labelFontFamily: "Raleway",
					tickThickness: 0

				},

				axisY2:{
					interlacedColor: "#fff",
					gridColor: "#fff",
					valueFormatString: " ",
					tickThickness: 0,
					lineColor: "#fff"
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
					text:"Others",
					fontFamily: "Raleway",
					fontWeight: "lighter",
					fontSize: 20			

				},
							animationEnabled: true,
				axisX:{
					interval: 1,
					gridThickness: 0,
					labelFontSize: 12,
					labelFontStyle: "normal",
					labelFontWeight: "normal",
					labelFontFamily: "Raleway",
					tickThickness: 0

				},
				axisY2:{
					interlacedColor: "#fff",
					gridColor: "#fff",
					valueFormatString: " ",
					tickThickness: 0,
					lineColor: "#fff"
				},
				data: [
				{     
					type: "bar",
					lineThickness: 1,
					name: "skills",
					axisYType: "secondary",
					color: "#087a02",				
					dataPoints: [	
					{y: 6, label: "Camtasia Studio"  },
					{y: 6, label: "Adobe Captivate"  },					
					{y: 7, label: "Version Control"  },
					{y: 9, label: "Microsoft Office"  },
					{y: 8, label: "Instructional Design"  },
					{y: 8, label: "Course Development"  },
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

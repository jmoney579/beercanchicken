    var request = null;

    function createRequest() {
                try {
                        request = new XMLHttpRequest();
                } catch (trymicrosoft) {
                        try {
                                request = new
    ActiveXObject("Msxml2.XMLHTTP");
                        } catch (othermicrosoft) {
                                try {
                                        request = new
    ActiveXObject("Microsoft.XMLHTTP");
                                } catch (failed) {
                                        request = null;
                                }
                        }
                }

                if (request == null )
                        alert("Error creating request object!");
        }

        function getTheFeed(url) {
                createRequest();
                //var url = "http://miles.internet2.edu/iam/www.internet2.edu/data.txt";

                request.open("GET", url, true);
                request.onreadystatechange = updatePage;
                request.send(null);
        }

        function updatePage() {
                if (request.readyState == 4 ) {
                       var data = "";
                       if (request.status == 200 ) {
                                data = request.responseText;
                       } 
                       var recentPostsElement = document.getElementById("recent_posts");
 		       recentPostsElement.innerHTML = data;
                }
        }



		  // POP UP A PREFORMATTED EMAIL MESSAGE WINDOW
		function popupMessage(theURL, theTitle) {
		
		  // SET MESSAGE VALUES
		 var to = "Your Friend's email";
		// var to = "";
		  //var cc = "another_person@company.com";
		  //var bcc = "yet_another_person@company.com";
		  var subject = "A recommended Internet2 site";
		  var body = 
			  "I recommend you visit: " +
			  theTitle +
			  "\n available at " +
			  theURL;
		
		  // BUILD MAIL MESSAGE COMPONENTS 
		  var doc = "mailto:" + escape(to) + 
			  //"?cc=" + cc + 
			  //"&bcc=" + bcc + 
			  "?subject=" + escape(subject) + 
			  "&body=" + escape(body); 
		
		  // POP UP EMAIL MESSAGE WINDOW
		  window.location = doc;
		}
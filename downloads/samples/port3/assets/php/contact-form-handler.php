<?php 
$errors = '';
$myemail = 'jawalker@vt.edu';//<-----Put Your email address here.
if(empty($_POST['Name'])  || 
   empty($_POST['email']) || 
   empty($_POST['Comment']))
{
    $errors .= "\n Error: all fields are required";
}

$name = $_POST['Name']; 
$email_address = $_POST['email']; 
$subject = $_POST['subject'];
$message = $_POST['Comment']; 

if(empty ($subject))
{
	$subject = "New Website Submission";
}

if (!preg_match(
"/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/i", 
$email_address))
{
    $errors .= "\n Error: Invalid email address";
}

if( empty($errors))
{
	$to = $myemail; 
	$email_subject = "$subject";
	$email_body = "You have received a new message. ".
	" Here are the details:\n Name: $name \n Email: $email_address \n Message: \n $message"; 
	
	$headers = "From: $email_address\n"; 
	$headers .= "Reply-To: $email_address";
	
	mail($to,$email_subject,$email_body,$headers);
	//redirect to the 'thank you' page
	header('Location: thanks.html');
} 
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="Jacques P. Walker Online Portfolio">
	<meta name="author" content="Jacques P. Walker">
	<link href="../img/favicon.png" type="image/jpg" rel="icon" />

    <title>Jacques P. Walker Online Portfolio</title>

    <!-- Bootstrap Core CSS -->
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- jQuery and UI stuff -->
    <script src="http://code.jquery.com/jquery-latest.min.js"></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
    <!-- Custom CSS -->
    <link href="../css/backtotop.css" rel="stylesheet">
    <link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/pure-min.css">
    <link href="../css/mystyle.css" rel="stylesheet">


    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
   <!--<script>
   $(document).ready(function(){
	   $(window).bind('scroll', function() {
	   var navHeight = $( window ).height() - 260;
			 if ($(window).scrollTop() > navHeight) {
				 $('.sticky').addClass('snaptotop');
				 $('.top_content').addClass('stomped');
			 }
			 else {
				 $('.sticky').removeClass('snaptotop');
				 $('.top_content').removeClass('stomped');
			 }
		});
	});
	</script>-->
	<script src="../js/naveffect.js"></script>

</head>

<body>

    <!-- Navigation -->	
<div class="sticky snaptotop">
    <div class=big-pic>
		<!--<img src="assets/img/header_back.jpg" alt="Site Header"/>-->
		<div class="tagline">
			<div class="topline">
			Pave Your
			</div>
			<div class="bottomline">
			Own Path
			</div>
		</div>
		<div class="site_title">
			<a class="navbar-brand" href="../../../index.html"><img src="..//img/port2_trans.png" alt="Jacques Logo"/></a><h1>J<small>acques</small> P. W<small>alker</small></h1>
			<img class="dropdown_nav" src="../img/menu.png" alt="dropdown menu button"/>
		</div>
	</div>
    <nav class="navbar navbar-inverse navbar-bottom" role="navigation">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="navbar">
                <ul class="nav navbar-nav">
                    <li>
                        <a href="../../index.html">Home</a>
                    </li>
                    <li>
                        <a class="active" href="../../../about/index.html">About Me</a>
                    </li>
                    <li>
                        <a href="../../../resume/index.html">Resume</a>
                    </li>
                    <li>
                        <a href="../../../portfolio/index.html">Portfolio</a>
                    </li>
                    <li>
                        <a href="../../../contact/index.html">Contact Me</a>
                    </li>
                </ul>
            </div>
            <!-- /.navbar-collapse -->
    </nav>
	</div>
	</div>
    <!-- Page Content -->
   <content>
    <div class="container-fluid top_content inside_stomped">
        <div class="row">
			<div class="col-md-9">
                <h1>Email Submission <strong>Error</strong></h1>
				<!-- This page is displayed only if there is some error -->
			<?php
			echo nl2br($errors);
			?>
			</div>
        </div>
        <!-- /.row -->
    </div>
  
</content>
<footer class="container-fluid">
		<div class="col-md-4 rule2">
			 <h4 class="contact_title">Email <strong>Me</strong></h4>
			 <form class="pure-form pure-form-stacked contactfooter" method="POST" action="contact-form-handler.php" name="Email ME">
				<input size="40" placeholder="Name" name="Name"
				type="text"><br>
			      	<input size="40" placeholder="Email" name="email" type="text"><br>
			      	<textarea cols="40" placeholder="Comment" rows="4" name="Comment"></textarea><br>
			      	<input class="pull-right pure-button pure-button-primary" value="Submit" name="Submit" type="submit"><br>
			  </form>
		</div>
		<div class="col-sm-8">
			<div class="subnav">
				<ul>
				    <li>
				        <a href="../../index.html">Home</a>
				    </li>
				    <li>
				        <a href="../../about/index.html">About Me</a>
				    </li>
				    <li>
				        <a href="../../resume/index.html">Resume</a>
				    </li>
				    <li>
				        <a href="../../portfolio/index.html">Portfolio</a>
				    </li>
				    <li>
				        <a href="../../contact/index.html">Contact Me</a>
				    </li>
				</ul>
			</div>
			<div class="col-sm-10 qoute">
				<p>It's easy to come up with new ideas; the hard part is letting go of what worked for you two years ago, but will soon be out of date.<br/><br/><small class="pull-right">- Roger von Oech</small><p>
			</div>
			<div class="col-sm-10 socialmedia">
				<a class="github" title="Github" href="https://github.com/jmoney579">GitHub</a>
				<a class="facebook" title="Facebook" href="http://facebook.com/jmoney579">Facebook</a>
				<a class="linkedin" title="Linked In" href="http://www.linkedin.com/in/jacqueswalker">Linked In</a>
				<a class="blog" title="Blog" href="blog/index.php">Blog</a>
			</div>
		</div>
	</div>
</footer>
    <!-- /.container -->
    <!--  Back to Top -->
    <a href="#" class="cd-top">Top</a>
	<script src="../js/backtotop.js"></script>
    <!-- jQuery -->
    <!--<script src="assets/bootstrap/js/jquery.js"></script>-->

    <!-- Bootstrap Core JavaScript -->
    <script src="../bootstrap/js/bootstrap.min.js"></script>
	<script>
	  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

	  ga('create', 'UA-43024951-1', 'jacqueswalker.com');
	  ga('send', 'pageview');
	</script>
</body>

</html>

<?php 
$errors = '';
$myemail = 'jawalker@vt.edu';//<-----Put Your email address here.
$exemptemail = 'carpetgrowth@vt.edu';
$studentemail = 'jacquesw@vt.edu';
if(empty($_POST['acct_name'])  || 
   empty($_POST['first_name']) || 
   empty($_POST['last_name']))
{
    $errors .= "\n Error: all fields are required";
}else{
$acct_name = $_POST['acct_name']; 
$school_year = $_POST['school_year'];
$institution = $_POST['institution']; 
$collect_size = $_POST['collect_size'];
$fullname = $_POST['title']." ".$_POST['first_name']." ".$_POST['last_name'];
$street = $_POST['street'];
$address2 = $_POST['city'].", ".$_POST['state']." ".$_POST['zip'];
$accept_license = $_POST['accept_license'];
$email = $_POST['email'];
$ph_number = "(".$_POST['ph_areacode'].") ".$_POST['ph_first3']." - ".$_POST['ph_last4'];
$fx_number = "(".$_POST['fx_areacode'].") ".$_POST['fx_first3']." - ".$_POST['fx_last4'];
$discount_code = "2012student";
$sadiscount_code = "2012collector";
$software = $_POST['software'];
$support = $_POST['support'];
$os = $_POST['os'];
/*Start of Institution Below*/ 
if($_POST['form_type'] == "instituition"){

/*if (!preg_match(
"/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/i", 
$email_address))
{
    $errors .= "\n Error: Invalid email address";
}*/

	if( empty($errors))
	{
	$to = $myemail; 
	$email_subject = "EGEMS Network Purchasing Form Submission: $acct_name";
	$email_body = "You have received a new message. \n \n".
	"Here are the details:\n Account Name: $acct_name \n Institution: $institution \n Name: $fullname \n Address: \n $street \n $address2 \n Phone Number: $ph_number \n Fax Number: $fx_number \n Licensing Agreement: $accept_license"; 
	
	$headers = "From: $myemail\n"; 
	$headers .= "Reply-To: $myemail";
	
	mail($to,$email_subject,$email_body,$headers);
	//Form information submitted for review
	echo ("<h2>Registration Complete<br />
          </h2>
          <p>Your registration information has been received.  Please review the
            information submitted below.  If you notice any errors, please
            return to the <a href='instituition_form.html'>registration form</a>
            or use the back button on your browser to resubmit your
            information.  <br />
          </p>
          <p><br />
          </p>
          <h4>Registration Submission Review<br />
          </h4>
          <p></p>
          <table width='100%' border='0'>
            <tbody>
              <tr>
                <th style='width: 361px;' rowspan='1' colspan='2'>Registration
                  Information<a href='instituition_form.html' title='Network Package Purchase Form'><br />
                  </a></th>
              </tr>
              <tr>
                <th rowspan='1' colspan='1' style='width: 285px;'>Account Name:<br />
                </th>
                <td style='text-align: center;'>".$acct_name."
                </td>
              </tr>
              <tr>
                <th>Institution Name<br />
                </th>
                <td style='text-align: center;'>".$institution."
                </td>
              </tr>
              <tr>
                <td rowspan='1' colspan='2'><strong>Contact Information</strong><br />
                </td>
              </tr>
              <tr>
                <th rowspan='1' colspan='1'>Name:<br />
                </th>
                <td style='text-align: center;'>".$fullname."
                </td>
              </tr>
			  <tr>
                <th rowspan='1' colspan='1'>Email:<br />
                </th>
                <td style='text-align: center;'>".$email."
                </td>
              </tr>
              <tr>
                <th rowspan='1' colspan='1'>Address:<br />
                </th>
                <td style='text-align: center;'>".$street."<br/>".$address2."
                </td>
              </tr>
              <tr>
                <th rowspan='1' colspan='1'>Phone Number:<br />
                </th>
                <td style='text-align: center;'>".$ph_number."
                </td>
              </tr>
              <tr>
                <th rowspan='1' colspan='1'>Fax Number:<br />
                </th>
                <td style='text-align: center;'>".$fx_number."
                </td>
              </tr>
            </tbody>
          </table><br /><br />
		  	<form action='https://www.paypal.com/cgi-bin/webscr' method='post'>
			<input type='hidden' name='cmd' value='_s-xclick'>
			<input type='hidden' name='hosted_button_id' value='3538SRWQM5NM2'>
			<input type='image' src='https://www.paypalobjects.com/en_US/i/btn/btn_buynowCC_LG.gif' border='0' name='submit' alt='PayPal - The safer, easier way to pay online!'>
			<img alt='' border='0' src='https://www.paypalobjects.com/en_US/i/scr/pixel.gif' width='1' height='1'>
			</form>");
	}
/*Start of Private Below*/ 
} elseif ($_POST['form_type'] == "private"){
		/*if (!preg_match(
		"/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/i", 
		$email_address))
		{
			$errors .= "\n Error: Invalid email address";
		}*/
		if($_POST['sadiscount'] != $sadiscount_code && $_POST['sadiscount'] !=''){
		echo ("<h2>Discount Code Error:</h2><br /><p>The discount code you entered is invalid please return to the <a href='private_form.html'>registration form</a> and correct the error. If you do not have a registration code, please leave the field blank to process your request.</p>");
		}else{
		if( empty($errors))
		{
			$to = $myemail;
			 if($_POST['sadiscount'] == $sadiscount_code){
				$email_subject = "EGEMS Stand-alone Purchasing Form Discount Submission: $acct_name";
			 }else{
				$email_subject = "EGEMS Stand-alone Purchasing Form Submission: $acct_name";
			 }
			$email_body = "You have received a new message. \n \n".
			"Here are the details:\n Account Name: $acct_name \n Institution: $institution \n Operating System: $os \n Name: $fullname \n Address: \n $street \n $address2 \n Email: $email \n Phone Number: $ph_number \n Fax Number: $fx_number \n Licensing Agreement: $accept_license"; 
			
			$headers = "From: $myemail\n"; 
			$headers .= "Reply-To: $myemail";
			
			mail($to,$email_subject,$email_body,$headers);
			//Form information submitted for review
			echo ("<h2>Registration Complete<br />
				  </h2>
				  <p>Your registration information has been received.  Please review the
					information submitted below.  If you notice any errors, please
					return to the <a href='private_form.html'>registration form</a>
					or use the back button on your browser to resubmit your
					information.  <br />
				  </p>
				  <p><br />
				  </p>
				  <p></p>
				  <table width='100%' border='0'>
					<tbody>
					  <tr>
						<th style='width: 361px;' rowspan='1' colspan='2'>Registration
						  Information<a href='private_form.html' title='Stand-alone Package Purchase Form'><br />
						  </a></th>
					  </tr>
					  <tr>
						<th rowspan='1' colspan='1' style='width: 285px;'>Account Name:<br />
						</th>
						<td style='text-align: center;'>".$acct_name."
						</td>
					  </tr>
					  <tr>
						<th>Institution Name<br />
						</th>
						<td style='text-align: center;'>".$institution."
						</td>
					  </tr>
					  <tr>
						<th>Operating System:<br />
						</th>
						<td style='text-align: center;'>".$os."
						</td>
					  </tr>
					  <tr>
						<td rowspan='1' colspan='2'><strong>Contact Information</strong><br />
						</td>
					  </tr>
					  <tr>
						<th rowspan='1' colspan='1'>Name:<br />
						</th>
						<td style='text-align: center;'>".$fullname."
						</td>
					  </tr>
						<tr>
						<th rowspan='1' colspan='1'>Email:<br />
						</th>
						<td style='text-align: center;'>".$email."
						</td>
					  </tr>				  
					  <tr>
						<th rowspan='1' colspan='1'>Address:<br />
						</th>
						<td style='text-align: center;'>".$street."<br/>".$address2."
						</td>
					  </tr>
					  <tr>
						<th rowspan='1' colspan='1'>Phone Number:<br />
						</th>
						<td style='text-align: center;'>".$ph_number."
						</td>
					  </tr>
					  <tr>
						<th rowspan='1' colspan='1'>Fax Number:<br />
						</th>
						<td style='text-align: center;'>".$fx_number."
						</td>
					  </tr>
					</tbody>
				  </table><br /><br />");
				  //Windows Stuff
				  if ($os == "Windows - PC"){
					  if($_POST['sadiscount'] == $sadiscount_code){
						echo("<h3>Discount Purchase:</h3><form action='https://www.paypal.com/cgi-bin/webscr' method='post'>
<input type='hidden' name='cmd' value='_s-xclick'>
<input type='hidden' name='hosted_button_id' value='VXGMJFLMVB26L'>
<input type='image' src='https://www.paypalobjects.com/en_US/i/btn/btn_buynowCC_LG.gif' border='0' name='submit' alt='PayPal - The safer, easier way to pay online!'>
<img alt='' border='0' src='https://www.paypalobjects.com/en_US/i/scr/pixel.gif' width='1' height='1'>
</form>");
					  }else{
					  echo ("<form action='https://www.paypal.com/cgi-bin/webscr' method='post'>
						<input type='hidden' name='cmd' value='_s-xclick'>
						<input type='hidden' name='hosted_button_id' value='ULBUDPK9RB8FY'>
						<input type='image' src='https://www.paypalobjects.com/en_US/i/btn/btn_buynowCC_LG.gif' border='0' name='submit' alt='PayPal - The safer, easier way to pay online!'>
						<img alt='' border='0' src='https://www.paypalobjects.com/en_US/i/scr/pixel.gif' width='1' height='1'>
						</form>");
					  }
				  }else{
				  if ($os == "MAC"){
					  if($_POST['sadiscount'] == $sadiscount_code){
						echo("<form action='https://www.paypal.com/cgi-bin/webscr' method='post'>
								<input type='hidden' name='cmd' value='_s-xclick'>
								<input type='hidden' name='hosted_button_id' value='VXGMJFLMVB26L'>
								<input type='image' src='https://www.paypalobjects.com/en_US/i/btn/btn_buynowCC_LG.gif' border='0' name='submit' alt='PayPal - The safer, easier way to pay online!'>
								<img alt='' border='0' src='https://www.paypalobjects.com/en_US/i/scr/pixel.gif' width='1' height='1'>
							   </form>");
					  }else{
					  echo ("<form action='https://www.paypal.com/cgi-bin/webscr' method='post'>
								<input type='hidden' name='cmd' value='_s-xclick'>
								<input type='hidden' name='hosted_button_id' value='ULBUDPK9RB8FY'>
								<input type='image' src='https://www.paypalobjects.com/en_US/i/btn/btn_buynowCC_LG.gif' border='0' name='submit' alt='PayPal - The safer, easier way to pay online!'>
								<img alt='' border='0' src='https://www.paypalobjects.com/en_US/i/scr/pixel.gif' width='1' height='1'>
							</form>");
					  }
				  }
				  		}
				  }
		}
	
} 
/*Start of Support A Below*/ 
elseif($_POST['form_type'] == "supportA")
	{
					if( empty($errors))
		{
			$to = $myemail; 
			$email_subject = "EGEMS Support Form Submission: $acct_name";
			$email_body = "You have received a new message. \n \n".
			"Here are the details:\n Account Name: $acct_name \n Institution: $institution \n Name: $fullname \n Address: \n $street \n $address2 \n Email: $email \n Phone Number: $ph_number \n Fax Number: $fx_number \n Licensing Agreement: $accept_license \n Support Type: Package A"; 
			
			$headers = "From: $myemail\n"; 
			$headers .= "Reply-To: $myemail";
			
			mail($to,$email_subject,$email_body,$headers);
			//Form information submitted for review
			echo ("<h2>Registration Complete<br />
				  </h2>
				  <p>Your registration information has been received.  Please review the
					information submitted below.  If you notice any errors, please
					return to the <a href='support_formA.html'>registration form</a>
					or use the back button on your browser to resubmit your
					information.  <br />
				  </p>
				  <p><br />
				  </p>
				  <p></p>
				  <table width='100%' border='0'>
					<tbody>
					  <tr>
						<th style='width: 361px;' rowspan='1' colspan='2'>Registration
						  Information<a href='private_form.html' title='Stand-alone Package Purchase Form'><br />
						  </a></th>
					  </tr>
					  <tr>
						<th rowspan='1' colspan='1' style='width: 285px;'>Account Name:<br />
						</th>
						<td style='text-align: center;'>".$acct_name."
						</td>
					  </tr>
					  <tr>
						<th rowspan='1' colspan='1'>Name:<br />
						</th>
						<td style='text-align: center;'>".$fullname."
						</td>
					  </tr>
						<tr>
						<th rowspan='1' colspan='1'>Email:<br />
						</th>
						<td style='text-align: center;'>".$email."
						</td>
					  </tr>				  
					  <tr>
						<th rowspan='1' colspan='1'>Address:<br />
						</th>
						<td style='text-align: center;'>".$street."<br/>".$address2."
						</td>
					  </tr>
					  <tr>
						<th rowspan='1' colspan='1'>Phone Number:<br />
						</th>
						<td style='text-align: center;'>".$ph_number."
						</td>
					  </tr>
					  <tr>
						<th rowspan='1' colspan='1'>Fax Number:<br />
						</th>
						<td style='text-align: center;'>".$fx_number."
						</td>
					  </tr>
					</tbody>
				  </table><br /><br />
			<form action='https://www.paypal.com/cgi-bin/webscr' method='post'>
			<input type='hidden' name='cmd' value='_s-xclick'>
			<input type='hidden' name='hosted_button_id' value='LSZMLH79234W2'>
			<input type='image' src='https://www.paypalobjects.com/en_US/i/btn/btn_buynowCC_LG.gif' border='0' name='submit' alt='PayPal - The safer, easier way to pay online!'>
			<img alt='' border='0' src='https://www.paypalobjects.com/en_US/i/scr/pixel.gif' width='1' height='1'>
			</form>");
	     }
	}
/*Start of Support B Below*/ 
elseif($_POST['form_type'] == "supportB"){
								if( empty($errors))
		{
			$to = $myemail; 
			$email_subject = "EGEMS Support Form Submission: $acct_name";
			$email_body = "You have received a new message. \n \n".
			"Here are the details:\n Account Name: $acct_name \n Institution: $institution \n Name: $fullname \n Address: \n $street \n $address2 \n Email: $email \n Phone Number: $ph_number \n Fax Number: $fx_number \n Licensing Agreement: $accept_license \n Support Type: Package B"; 
			
			$headers = "From: $myemail\n"; 
			$headers .= "Reply-To: $myemail";
			
			mail($to,$email_subject,$email_body,$headers);
			//Form information submitted for review
			echo ("<h2>Registration Complete<br />
				  </h2>
				  <p>Your registration information has been received.  Please review the
					information submitted below.  If you notice any errors, please
					return to the <a href='support_formB.html'>registration form</a>
					or use the back button on your browser to resubmit your
					information.  <br />
				  </p>
				  <p><br />
				  </p>
				  <p></p>
				  <table width='100%' border='0'>
					<tbody>
					  <tr>
						<th style='width: 361px;' rowspan='1' colspan='2'>Registration
						  Information<a href='private_form.html' title='Stand-alone Package Purchase Form'><br />
						  </a></th>
					  </tr>
					  <tr>
						<th rowspan='1' colspan='1' style='width: 285px;'>Account Name:<br />
						</th>
						<td style='text-align: center;'>".$acct_name."
						</td>
					  </tr>
					  <tr>
						<th rowspan='1' colspan='1'>Name:<br />
						</th>
						<td style='text-align: center;'>".$fullname."
						</td>
					  </tr>
						<tr>
						<th rowspan='1' colspan='1'>Email:<br />
						</th>
						<td style='text-align: center;'>".$email."
						</td>
					  </tr>				  
					  <tr>
						<th rowspan='1' colspan='1'>Address:<br />
						</th>
						<td style='text-align: center;'>".$street."<br/>".$address2."
						</td>
					  </tr>
					  <tr>
						<th rowspan='1' colspan='1'>Phone Number:<br />
						</th>
						<td style='text-align: center;'>".$ph_number."
						</td>
					  </tr>
					  <tr>
						<th rowspan='1' colspan='1'>Fax Number:<br />
						</th>
						<td style='text-align: center;'>".$fx_number."
						</td>
					  </tr>
					</tbody>
				  </table><br /><br />
			<form action='https://www.paypal.com/cgi-bin/webscr' method='post'>
			<input type='hidden' name='cmd' value='_s-xclick'>
			<input type='hidden' name='hosted_button_id' value='YY5CUC3SAF45L'>
			<input type='image' src='https://www.paypalobjects.com/en_US/i/btn/btn_buynowCC_LG.gif' border='0' name='submit' alt='PayPal - The safer, easier way to pay online!'>
			<img alt='' border='0' src='https://www.paypalobjects.com/en_US/i/scr/pixel.gif' width='1' height='1'>
			</form>");
	     }
	}
//Start of Invoice Form Below
elseif($_POST['form_type'] == "exempt"){
	if( empty($errors))
	{
	$to = $exemptemail; 
	$email_subject = "EGEMS Tax Exempt Form Submission: $acct_name";
	$email_body = "You have received a new message. \n \n".
	"Here are the details:\n Account Name: $acct_name \n Institution: $institution \n Operating System: $os \n Name: $fullname \n Address: \n $street \n $address2 \n Email: $email \n Phone Number: $ph_number \n Fax Number: $fx_number \n Licensing Agreement: $accept_license \n Software Selection: $software \n Support Selection: $support \n"; 
	
	$headers = "From: $to\n"; 
	$headers .= "Reply-To: $to";
	
	mail($to,$email_subject,$email_body,$headers);
	//Form information submitted for review
	echo ("<h2>Registration Complete<br />
          </h2>
          <p>Your registration information has been received.  We will contact you within the next 2 business days to discuss any additional information that may be needed to process your purchase. If you need more immediate assistance, please visit our <a href='../contact/index.html'>contact page</a>.<br /><br />
		  Please review the information submitted below.  If you notice any errors, please
            return to the <a href='exempt_form.html'>registration form</a>
            or use the back button on your browser to resubmit your
            information.  <br />
          </p>
          <p><br />
          </p>
          <h4>Registration Submission Review<br />
          </h4>
          <p></p>
          <table width='100%' border='0'>
            <tbody>
              <tr>
                <th style='width: 361px;' rowspan='1' colspan='2'>Registration
                  Information<a href='instituition_form.html' title='Network Package Purchase Form'><br />
                  </a></th>
              </tr>
              <tr>
                <th rowspan='1' colspan='1' style='width: 285px;'>Account Name:<br />
                </th>
                <td style='text-align: center;'>".$acct_name."
                </td>
              </tr>
              <tr>
                <th>Institution Name<br />
                </th>
                <td style='text-align: center;'>".$institution."
                </td>
              </tr>
			  <tr>
					<th>Opertating System<br />
					</th>
					<td style='text-align: center;'>".$os."
					</td>
				  </tr>
              <tr>
                <td rowspan='1' colspan='2'><strong>Contact Information</strong><br />
                </td>
              </tr>
              <tr>
                <th rowspan='1' colspan='1'>Name:<br />
                </th>
                <td style='text-align: center;'>".$fullname."
                </td>
              </tr>
			    <tr>
                <th rowspan='1' colspan='1'>Email:<br />
                </th>
                <td style='text-align: center;'>".$email."
                </td>
              </tr>
              <tr>
                <th rowspan='1' colspan='1'>Address:<br />
                </th>
                <td style='text-align: center;'>".$street."<br/>".$address2."
                </td>
              </tr>
              <tr>
                <th rowspan='1' colspan='1'>Phone Number:<br />
                </th>
                <td style='text-align: center;'>".$ph_number."
                </td>
              </tr>
              <tr>
                <th rowspan='1' colspan='1'>Fax Number:<br />
                </th>
                <td style='text-align: center;'>".$fx_number."
                </td>
              </tr>
			   <tr>
                <th rowspan='1' colspan='1'>Software Selection:<br />
                </th>
                <td style='text-align: center;'>".$software."
                </td>
              </tr>
			  <tr>
                <th rowspan='1' colspan='1'>Support Selection:<br />
                </th>
                <td style='text-align: center;'>".$support."
                </td>
              </tr>
            </tbody>
          </table>");
	}
}
else{
	//Start of Student Belwo
	if($_POST['discount'] == $discount_code){
		 	if( empty($errors))
		{
			$to = $studentemail; 
			$email_subject = "EGEMS Student Purchasing Form Discount Submission: $fullname";
			$email_body = "You have received a new message. \n \n".
			"Here are the details:\n School Name: $acct_name \n Year in School: $school_year \n Operating System: $os \n Name: $fullname \n Address: \n $street \n $address2 \n Email: $email \n Phone Number: $ph_number \n Fax Number: $fx_number \n Licensing Agreement: $accept_license"; 
			
			$headers = "From: $to\n"; 
			$headers .= "Reply-To: $to";
			
			mail($to,$email_subject,$email_body,$headers);
			//Form information submitted for review
		echo ("<h2>Registration Complete<br />
				  </h2>
				  <p>Your registration information has been received.  Please review the
					information submitted below.  If you notice any errors, please
					return to the <a href='student_form.html'>registration form</a>
					or use the back button on your browser to resubmit your
					information.  <br />
				  </p>
				  <p><br />
				  </p>
				  <p></p>
				  <table width='100%' border='0'>
					<tbody>
					  <tr>
						<th style='width: 361px;' rowspan='1' colspan='2'>Registration
						  Information<a href='student_form.html' title='Student Package Purchase Form'><br />
						  </a></th>
					  </tr>
					  <tr>
                		<th rowspan='1' colspan='1' style='width: 285px;'>School Name:<br />
						</th>
						<td style='text-align: center;'>".$acct_name."
						</td>
					  </tr>
			          <tr>
						<th rowspan='1' colspan='1' style='width: 285px;'>Year in School:<br />
						</th>
						<td style='text-align: center;'>".$school_year."
						</td>
					  </tr>
					  <tr>
						<th>Operating System:<br />
						</th>
						<td style='text-align: center;'>".$os."
						</td>
					  </tr>
					  <tr>
						<td rowspan='1' colspan='2'><strong>Contact Information</strong><br />
						</td>
					  </tr>
					  <tr>
						<th rowspan='1' colspan='1'>Name:<br />
						</th>
						<td style='text-align: center;'>".$fullname."
						</td>
					  </tr>
					  <tr>
						<th rowspan='1' colspan='1'>Address:<br />
						</th>
						<td style='text-align: center;'>".$street."<br/>".$address2."
						</td>
					  </tr>
					  <tr>
						<th rowspan='1' colspan='1'>Email:<br />
						</th>
						<td style='text-align: center;'>".$email."
						</td>
					  </tr>					  
					  <tr>
						<th rowspan='1' colspan='1'>Phone Number:<br />
						</th>
						<td style='text-align: center;'>".$ph_number."
						</td>
					  </tr>
					  <tr>
						<th rowspan='1' colspan='1'>Fax Number:<br />
						</th>
						<td style='text-align: center;'>".$fx_number."
						</td>
					  </tr>
					</tbody>
				  </table>
				  <br />");
		  if ($os == "Windows - PC"){
			  echo ("<p>If everything looks correct, please download the student version using the link below:</p>
		  <a href='http://www.eclipseservices.biz/Downloads/EGEMS_v1_0_student.zip'>EGEMS Student Version</a>");
		  }else{
			  echo ("<p>If everything looks correct, please download the student version using the link below:</p>
		  <a href='http://www.eclipseservices.biz/Downloads/EGEMS_v1_0_studentmac.zip'>EGEMS Student Version (MAC Edition)</a>");
		  }
		}
	}elseif($_POST['discount'] != $discount_code && $_POST['discount'] !=""){
		echo ("<h2>Discount Code Error:</h2><br /><p>The discount code you entered is invalid please return to the <a href='student_form.html'>registration form</a> and correct the error. If you do not have a registration code, please leave the field blank to process your request.</p>"); 		
	}else{
	if( empty($errors))
		{
			$to = $studentemail; 
			$email_subject = "EGEMS Student Purchasing Form Submission: $fullname";
			$email_body = "You have received a new message. \n \n".
			"Here are the details:\n School Name: $acct_name \n Year in School: $school_year \n Operating System: $os \n Name: $fullname \n Address: \n $street \n $address2 \n Email: $email \n Phone Number: $ph_number \n Fax Number: $fx_number \n Licensing Agreement: $accept_license"; 
			
			$headers = "From: $to\n"; 
			$headers .= "Reply-To: $to";
			
			mail($to,$email_subject,$email_body,$headers);
			//Form information submitted for review
			echo ("<h2>Registration Complete<br />
				  </h2>
				  <p>Your registration information has been received.  Please review the
					information submitted below.  If you notice any errors, please
					return to the <a href='student_form.html'>registration form</a>
					or use the back button on your browser to resubmit your
					information.  <br />
				  </p>
				  <p><br />
				  </p>
				  <p></p>
				  <table width='100%' border='0'>
					<tbody>
					  <tr>
						<th style='width: 361px;' rowspan='1' colspan='2'>Registration
						  Information<a href='student_form.html' title='Stand-alone Package Purchase Form'><br />
						  </a></th>
					  </tr>
					  <tr>
                		<th rowspan='1' colspan='1' style='width: 285px;'>School Name:<br />
						</th>
						<td style='text-align: center;'>".$acct_name."
						</td>
					  </tr>
			          <tr>
						<th rowspan='1' colspan='1' style='width: 285px;'>Year in School:<br />
						</th>
						<td style='text-align: center;'>".$school_year."
						</td>
					  </tr>
					  <tr>
						<th>Operating System:<br />
						</th>
						<td style='text-align: center;'>".$os."
						</td>
					  </tr>				  
					  <tr>
						<td rowspan='1' colspan='2'><strong>Contact Information</strong><br />
						</td>
					  </tr>
					  <tr>
						<th rowspan='1' colspan='1'>Name:<br />
						</th>
						<td style='text-align: center;'>".$fullname."
						</td>
					  </tr>
					  <tr>
						<th rowspan='1' colspan='1'>Email:<br />
						</th>
						<td style='text-align: center;'>".$email."
						</td>
					  </tr>
					  <tr>
						<th rowspan='1' colspan='1'>Address:<br />
						</th>
						<td style='text-align: center;'>".$street."<br/>".$address2."
						</td>
					  </tr>
					  <tr>
						<th rowspan='1' colspan='1'>Phone Number:<br />
						</th>
						<td style='text-align: center;'>".$ph_number."
						</td>
					  </tr>
					  <tr>
						<th rowspan='1' colspan='1'>Fax Number:<br />
						</th>
						<td style='text-align: center;'>".$fx_number."
						</td>
					  </tr>
					</tbody>
				  </table>
		  <p>If everything looks correct, please click the buy now button to
          complete your purchase. </p>
          <br />");
		  if ($os == "Windows - PC"){
		  echo ("<form action='https://www.paypal.com/cgi-bin/webscr' method='post'>
			<input type='hidden' name='cmd' value='_s-xclick'>
			<input type='hidden' name='hosted_button_id' value='V449F9ZG4ZJ5C'>
			<input type='image' src='https://www.paypalobjects.com/en_US/i/btn/btn_buynowCC_LG.gif' border='0' name='submit' alt='PayPal - The safer, easier way to pay online!'>
			<img alt='' border='0' src='https://www.paypalobjects.com/en_US/i/scr/pixel.gif' width='1' height='1'>
			</form>");
		  }else{
			  	  echo ("<form action='https://www.paypal.com/cgi-bin/webscr' method='post'>
			<input type='hidden' name='cmd' value='_s-xclick'>
			<input type='hidden' name='hosted_button_id' value='2DL742GMU7BPQ'>
			<input type='image' src='https://www.paypalobjects.com/en_US/i/btn/btn_buynowCC_LG.gif' border='0' name='submit' alt='PayPal - The safer, easier way to pay online!'>
			<img alt='' border='0' src='https://www.paypalobjects.com/en_US/i/scr/pixel.gif' width='1' height='1'>
			</form>");
		  }
		}
	}
}
}
?>

<?php
echo nl2br($errors);
?>


</body>
</html>
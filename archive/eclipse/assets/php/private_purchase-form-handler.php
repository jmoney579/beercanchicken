<?php 
$errors = '';
$myemail = 'registrationform@eclipseservices.biz';//<-----Put Your email address here.
if(empty($_POST['acct_name'])  || 
   empty($_POST['first_name']) || 
   empty($_POST['last_name'])  ||
   empty($_POST['accept_license']))
{
    $errors .= "\n Error: all fields are required";
}

$acct_name = $_POST['acct_name']; 
$institution = $_POST['institution']; 
$collect_size = $_POST['collect_size'];
$fullname = $_POST['title']." ".$_POST['first_name']." ".$_POST['last_name'];
$street = $_POST['street'];
$address2 = $_POST['city'].", ".$_POST['state']." ".$_POST['zip'];
$accept_license = $_POST['accept_license'];
$ph_number = "(".$_POST['ph_areacode'].") ".$_POST['ph_first3']." - ".$_POST['ph_last4'];
$fx_number = "(".$_POST['fx_areacode'].") ".$_POST['fx_first3']." - ".$_POST['fx_last4'];



/*if (!preg_match(
"/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/i", 
$email_address))
{
    $errors .= "\n Error: Invalid email address";
}*/

if( empty($errors))
{
	$to = $myemail; 
	$email_subject = "Stand-alone Purchasing Form Submission: $acct_name";
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
            return to the <a href='private_form.html'>registration form</a>
            or use the back button on your browser to resubmit your
            information.  <br />
          </p>
          <p><br />
          </p>
          <h4>Package Comparison<br />
          </h4>
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
          </table>");
} 
?>

<?php
echo nl2br($errors);
?>


</body>
</html>
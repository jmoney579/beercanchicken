#!/usr/bin/perl
use CGI qw(:all);
use strict;
#################################################################################
# Copyright 2003 Brent Maurer                                                   #
# http://Software77.com                                                         #
# All rights reserved                                                           #
#################################################################################
#           You may use this script for FREE but may NOT sell it                #
# THIS SCRIPT IS SUPPLIED WITHOUT WARRANTY OF ANY KIND. IF IT BREAKS YOUR       #
# SERVER YOU HAVE NO RECOURSE TO THE AUTHOR. USE ENTIRELY AT OWN RISK           #
#################################################################################
# Purpose:                                                                      #
# 1. A generic form mailer which will                                           #
#    a) Email the contents of any form to the person specified                  #
#    b) The email address of the recipient (you) is never available to spammers #
#    c) Once the form has been correctly filled in the user may be optionally   #
#       redirected to any URL of your choice.                                   #
#    d) The URL to which the user is directed is controlled from the submitting #
#       HTLM form but is only a reference in the form. The reference is looked  #
#       up by this script to do the actual redirection thus protecting the URL  #
#    e) The calling HTML form must reside at an address or IP address specified #
#       or it will not respond                                                  #
#################################################################################
# Variable Initialization                                                       #
#################################################################################
#------------------------------------------------------------------------------
# VARS VARS VARS VARS VARS VARS VARS VARS VARS VARS VARS VARS VARS VARS VARS
#------------------------------------------------------------------------------
#the email address this form must be sent to.
my $email_address = 'jacquesw@vt.edu';


# Very important!! This must be set so that other websites cannot call this script.
# Enter the IP addresses or domains which may call this script
my @valid_referrer = ('eclipseservices.biz');

#make sure you set this to 0 when running the script normally
my $debug =0;


#When the HTML form is submitted, one of two screens are shown:
#
# A. An ERROR Screen if
#    1. There are any blank fields
#    2. There are any fields with a value of "NO" (in caps)
#       (Think abou this for a while. It gives you a lot of control and flexibility - especially
#       if you use hidden radio buttons in a group and the selected one's value is "NO" :)
#    3. If there was an error processing email addresses - see below.
#
# B. A "success" screen
#    1. Showing the results of the form just filled in
#    2. A (confirmation) button on the bottom the user can click to take him to the next step
#    3. When the user clicks the button two things will happen
#       a. The contents of the form will be mailed to $email_address
#       b. The user will be redirected to the URL specified by a (hidden) field in the HTML form.
#          For example, you can make ANY (hidden) field in the HTML form with ANY NAME you want.
#          The script looks for the VALUE you assigned to the hidden field. When the Script
#          sees this VALUE (make sure you make it unique to the form)
#
# The format of the hash below is:
# %redirects=(
#       'NAME_OF_HIDDEN_FEILD_FROM_FORM_1' => 'URL_TO_DIRECT_FORM_1_TO';
#       'NAME_OF_HIDDEN_FEILD_FROM_FORM_2' => 'URL_TO_DIRECT_FORM_2_TO';
#       'NAME_OF_HIDDEN_FEILD_FROM_FORM_3' => 'URL_TO_DIRECT_FORM_3_TO';
#       );
# NOTE: The field does not have to be hidden. It can be any field you like so long as you
# specify the field name (CASE SENSITIVE) correctly here.
my %redirects=(
   'redirect'      =>  'http://www.eclipseservices.biz/index.html'
   );


# If you have one or two emails you need confirmed put them in here.
# What you put in here are the NAMES you gave the fields in your form.
# Typically you would use this to
# 1. Confirm the user has at least give a email that looks OK (RCF compliant)
# 2. The two (if you put in two) email addresses match
# An error will be returned if either of the two conditins are not met.
# If you don't have any email addresses in your form, set it like so:
# my @v_email=();
my @v_email=('E_Mail');
#my @v_email=('Email_Address','Email_Confirmation');
#my @v_email=();


# The text you want to appear on the submit button when the form has been accepted.
#NOTE: A button with the name "submit" in lower case will not be displayed - handy
#to use for the Submit button
my $submit_button_text="Proceed to Secure online Payment";


# The path to sendmail on your server. Ask your admin if you don't know what it is.
my $sendmail = "/usr/sbin/sendmail";          #path to sendmail
#------------------------------------------------------------------------------
############## DON'T CHANGE ANYTHING AFTER THIS LINE ##########################
#------------------------------------------------------------------------------
my $buffer='';
my %IN =();
my $footer ='';
my $h = "Content-type: text/html\n\n";
my $out;
my $form_error;

#------------------------------------------------------------------------------
sub init{
if ( $ENV{'REQUEST_METHOD'} eq "GET" ) {                      #
    $buffer = $ENV{'QUERY_STRING'};
}
elsif ( $ENV{'REQUEST_METHOD'} eq "POST" ) {                  #
    read( STDIN, $buffer, $ENV{'CONTENT_LENGTH'} );
}
my @pairs = split(/&/, $buffer);
foreach my $pair(@pairs){
  my ($name,$val) = split(/=/,$pair);
  $name =~ tr/+/ /;                                            #
  $name =~ s/%([a-fA-F0-9][a-fA-F0-9])/pack("C", hex($1))/eg;  #
  $name =~ s/^\s+//;                                           #
  $name =~ s/\s+$//;                                           #
  $val =~ tr/+/ /;                                            #
  $val =~ s/%([a-fA-F0-9][a-fA-F0-9])/pack("C", hex($1))/eg;  #
  $val =~ s/^\s+//;                                           #
  $val =~ s/\s+$//;                                           #
  $IN{$name} = $val;                                          #
  }
}
sub mail{  #
my ($to_name, $to_email, $subject, $from_name, $from_email, $body, $bcc)=@_;
my $res =0;
my $html=0;
if($body =~/<b>|<\/b>|<u>|<i>|<br>|<body>|<html>/i){$html=1;}
$res= (open MAIL, "|$sendmail -t ");
if($html){
  print MAIL "Content-type:text/html\n";       #
  }
print MAIL "To: $to_name <$to_email>\n";
if($IN{"BCC"}){print MAIL "Bcc: $from_email\n";}
print MAIL "From: $from_name <$from_email>\n";
print MAIL "Subject: $subject\n\n";
print MAIL $body;
print MAIL"\n\n";
close (MAIL);
}
sub hedr{
return qq|
<html>
<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>Unbeatable value in Web hosting.</title>
</style>
<style fprolloverstyle>A:hover {color: #FF0000; text-decoration: none}
</style>
</head>
<body text="#808080" bgcolor="#FFFFFF" vlink="#00CC99" style="font-size: 8pt; font-family: Verdana; color: #808080">
|;
}
sub footr{
return qq|
</body>
</html>
|;
}
sub check_fields{
my $error;
my $line;
foreach $line(keys %IN){
  if ($line ne "submit"){
    if (($IN{$line} eq "NO") or ($IN{$line} eq "")){
	  $error=1;
	  $form_error=1;
	  } else {
      $error=0;
	  }
	if(!$error){
	  $out.= "<br>$line: <b>$IN{$line}</b>";
	  } else {
	  $out.= "<br>$line: <font color=\"#FF0000\"><b>ERROR</b></font>";
	  }
	} #
  }
if($form_error){$out.="<p><center><font color=\"#FF0000\"><b>ERROR/S Detected. Hit back on your Browser now to correct.</b></font>";}
$out = "<center><b><u><font size = \"5\">Information submitted by you ($ENV{'REMOTE_ADDR'})</font></u></b></center><p>$out<p><p><font size=\"1\">IP logged: $ENV{'REMOTE_ADDR'}</font>";
}
sub form_ok{
my $name;
my $value;
my $key;
my %tmpIN=reverse %IN;
foreach $key(keys %redirects){
  if ($IN{$tmpIN{$key}} eq $key){
    $name=$tmpIN{$key};
	$value=$key;
    }
  }
if(!$form_error){
	return qq|
	<p align="center">
	<font color="#FF0000">
	<b>Please double check your data before submitting.</b> </font>
	<center>
	<form method="POST">
	<p>
	<font color=\"#FF0000\">
	<input type="submit" value="$submit_button_text" name="Redirect_TO"></p>
	</color>
	<input type="hidden" name="$name" value="$value">
	<input type="hidden" name="$v_email[0]" value="$IN{$v_email[0]}">
	<textarea style="VISIBILITY: hidden" name="DATA" value=$out</textarea>
	</form>
	|;
  }
}
sub redirect_to{       #
my $url;
my %tmpIN=reverse %IN;
my $key;
my $subject;
my $from;
foreach $key(keys %redirects){
  if ($IN{$tmpIN{$key}} eq $key){
    $url=$redirects{$key};
	$subject=$key;
	$from=$IN{$v_email[0]};#
    }
  }
  sleep(4);
  &mail($email_address,$email_address,$subject,$from,$from,$IN{"DATA"},'');
  print "Location: $url\n\n";
  exit;
}
sub valid_email{       #Field_name_1    Field_name_2
my $email = shift;
my $email2 = shift;
$email=$IN{$email};
$email2=$IN{$email2}||$email;
if ($email ne $email2){
  $form_error=1;
  return "<font color=\"#FF0000\"><b><center><br>EMAIL ADDRESSES DO NOT MATCH.<br>Hit BACK on your Browser to correct.</center></b></font>";
  }
if($v_email[0]){
  unless ($email =~ /(@.*@)|(\.\.)|(@\.)|(\.@)|(^\.)|(,)/||
        $email !~ /^.+\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/)
	   {return 1;} else
	   {
	     $form_error=1;
		 return "<font color=\"#FF0000\"><b><center>\"$email\" is NOT a valid Email address!<br>Hit BACK on your Browser to correct.</center></b></font>";
		 }
  }
}
sub validate_referrer{ #1=OK    0=BAD
my $Fvalid_referrer=0;
foreach my $referrer(@valid_referrer){
  if ($ENV{"REMOTE_ADDR"} =~ /$referrer/){$Fvalid_referrer=1;}
  if ($ENV{"HTTP_REFERER"} =~ /$referrer/){$Fvalid_referrer=1;}
}
return $Fvalid_referrer;
}
&init;
if(&validate_referrer){  #
  if ($IN{'Redirect_TO'} eq $submit_button_text){&redirect_to}
  print "$h";
  print &hedr;
  &check_fields;
  print $out;
  print &valid_email(@v_email);
  print &form_ok;
  } else {
  print "$h";
  print "Error. Invalid referrer. Script was called from => $ENV{\"HTTP_REFERER\"} or $ENV{\"REMOTE_ADDR\"}. Found instead " if $debug ;
  print "@valid_referrer specified in \"valid_referrer\"." if $debug;
  print "<br>Validation error. Script aborted";
  }
print footr;
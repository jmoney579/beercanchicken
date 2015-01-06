<?php
// **************************************************************************************************
// Linklok Paypal V2.4 digital download verification
// Copyright (c) 2003-2012 Vibralogix
// www.vibralogix.com
// sales@vibralogix.com
// You are licensed to use this product on one domain and with one Paypal account only.
// Please contact us for extra licenses if required.
// **************************************************************************************************
// Setup your products
// support packages need something to download, commented out for now
$Products[] = "prod1,EGEMS Network Package,USD=3000.00,egems/EGEMS_v1_0.fp7,0,1440";
$Products[] = "prod2,EGEMS Stand-alone Package,USD=1500.00,egems/EGEMS_v1_0_stand-alone.zip,0,1440";
$Products[] = "prod2coupon,EGEMS Stand-alone Package With Discount,USD=600.00,egems/EGEMS_v1_0_stand-alone.zip,0,1440";
$Products[] = "prod3,EGEMS Student Package,USD=50.00,Downloads/EGEMS_v1_0_student.zip,0,1440";
$Products[] = "prod3b,EGEMS Student Package (MAC Edition),USD=50.00,Downloads/EGEMS_v1_0_studentmac.zip,0,1440";
//$Products[] = "prod4, EGEMS Support Package A,USD=600.00,#link,0,1440";
//$Products[] = "prod5, EGEMS Support Package B,USD=300.00,#link,0,1440";

// Setup admin and security variables
$LinklokURL = "http://www.eclipseservices.biz/linklokipn.php";      // URL of the linlok.php page on your site
$PaypalEmail = "stimm@eclipseservices.biz";                        // PRIMARY Paypal email address
$SellerCompany = "ECLIPSE";                             // Your company name
$SellerEmail = "customersupport@eclipseservices.biz";                         // Your email address for order enquiries
$SellerURL = "http://www.eclipseservices.biz";                      // Your website URL
$CopyEmail = "linklok@eclipseservices.biz";                           // Receive copy of order emails
$LinkKey = "p3ach3sar3gr8";                                       // Encryption key for download links
$PDTtoken="OTLrE0WxMSQ8miMHFgTTDr5EDdCXdAGqwSHHfVMt3RyVgvSo8e_cJuMqTqm";                                                // Auto Return PDT token from paypal-profile-website payment preferences
$ManualPassword = "ch0cch1pc00ki3s";                                 // Password for manual order entry. "" to disable

$DelayEchecks = "N";                                         // Set to Y to delay eCheck orders until cleared.
$EmailTemplate = "";                                         // Optional Email Template in either .txt or .html format
$DownloadTemplate = "";                                      // Optional download page template
$ErrorTemplate = "";                                         // Optional Error page template
$HTMLEmail = "Y";                                            // Set to Y to use HTML formatted emails or N to send in plain-text
$WarningTemplate = "";                                       // Check payment warning email template
$WarningTemplatePage= "";                                    // Check payment warning thankyou page template
$Txnid = "";                                                 // Optional to stop possible multiple calls from IPN



// **************************************************************************************************
// The code below normally will not need modifying. Do so at your own risk!
// **************************************************************************************************
@error_reporting(E_ERROR);
if (!function_exists('get_headers')) {
function get_headers($url, $format=0) {
    $headers = array();
    $url = parse_url($url);
    $host = isset($url['host']) ? $url['host'] : '';
    $port = isset($url['port']) ? $url['port'] : 80;
    $path = (isset($url['path']) ? $url['path'] : '/') . (isset($url['query']) ? '?' . $url['query'] : '');
    $fp = fsockopen($host, $port, $errno, $errstr, 3);
    if ($fp)
    {
        $hdr = "GET $path HTTP/1.1\r\n";
        $hdr .= "Host: $host \r\n";
        $hdr .= "Connection: Close\r\n\r\n";
        fwrite($fp, $hdr);
        while (!feof($fp) && $line = trim(fgets($fp, 1024)))
        {
            if ($line == "\r\n") break;
            list($key, $val) = explode(': ', $line, 2);
            if ($format)
                if ($val) $headers[$key] = $val;
                else $headers[] = $key;
            else $headers[] = $line;
        }
        fclose($fp);
        return $headers;
    }
    return false;
}
}
if (!isset($ProductDelimiter))
  $ProductDelimiter=",";
if (!isset($EmailHeaderNoSlashR))
  $EmailHeaderNoSlashR=1;
if ((!isset($ExtraMailParam)) && (strtolower(@ini_get("safe_mode")) != 'on') && (@ini_get("safe_mode") != '1'))
  $ExtraMailParam="-f ".$SellerEmail;
@ini_set(sendmail_from,$SellerEmail);
if (!isset($AlwaysEmail))
  $AlwaysEmail=false;  // Emails normally only sent if product handled by Linklok
if (($DefaultCurrency=="") || (!isset($DefaultCurrency)))
  $DefaultCurrency="USD";
if (!isset($ServerTimeAdjust))
  $ServerTimeAdjust=300;
if (!isset($SitelokUseNameOnly))
  $SitelokUseNameOnly=true;    
if (!isset($IPlock))
  $IPlock = 0;
$Test = strtoupper($Test);
$PDTtoken=trim($PDTtoken);
if (!empty($_GET)) while (list($name, $value) = each($_GET)) $$name = $value;
if (!empty($_POST)) while (list($name, $value) = each($_POST)) $$name = $value;
if (($DebugEmail != "") && (!isset($_REQUEST['DebugEmail'])))
  Debug("Received variables", $DebugEmail);
// If no get or post variables then maybe user bookmarked download page
if ((empty($_GET)) && (empty($_POST)))
{
  ShowMessage($ErrorTemplate, "Access Denied. Linklok can only be called by Paypal.", $ErrorEmail);
  exit;
}
$found = false;
if (!empty($_REQUEST))
{
  reset($_REQUEST);
  while (list($namepair, $valuepair) = each($_REQUEST))
  {
    if ($namepair == "LinkKey") $found = true;
    if ($namepair == "Txnid") $found = true;
    if (substr($namepair, 0, 8) == "Products") $found = true;
    if (substr($namepair, 0, 19) == "productnotifyemails") $found = true;
    if ($namepair == "ManualPassword") $found = true;
    if ($namepair == "Test") $found = true;
    if ($namepair == "DownloadTemplate") $found = true;
    if ($namepair == "EmailTemplate") $found = true;
    if ($namepair == "ProductCSV") $found = true;
    if ($namepair == "PaypalEmail") $found = true;
    if ($namepair == "DefaultLocation") $found = true;
    if ($namepair == "DefaultPrice") $found = true;
    if ($namepair == "DefaultCurrency") $found = true;
    if ($namepair == "DefaultExpiry") $found = true;
    if ($namepair == "DefaultExtra") $found = true;
    if ($namepair == "IPlock") $found = true;
    if ($namepair == "RequireCustom") $found = true;
    if ($namepair == "NotifyDownloadEmail") $found = true;
    if ($namepair == "NotifyEmail") $found = true;
    if ($namepair == "SellerEmail") $found = true;
    if ($namepair == "SellerCompany") $found = true;
    if ($namepair == "SellerURL") $found = true;
    if ($namepair == "PDTtoken") $found = true;
    if ($namepair == "WarningTemplate") $found = true;
    if ($namepair == "EmailTemplate") $found = true;
    if ($namepair == "DownloadTemplate") $found = true;
    if ($namepair == "ServerTimeAdjust") $found = true;
    if ($namepair == "DownloadBackground") $found = true;
    if ($namepair == "WarningTemplatePage") $found = true;
    if ($namepair == "NotifyTemplate") $found = true;
    if ($namepair == "ConfirmTemplate") $found = true;
    if ($namepair == "ProductNotifyTemplate") $found = true;
  }
}
if ($found)
{
  ShowMessage($ErrorTemplate, "Access Denied.", $ErrorEmail);
  exit;
}
// If user decided to use external product file then read it in here
if ($ProductCSV != "")
{
  $fh = @fopen($ProductCSV, "r");
  if (!($fh))
  {
    ShowMessage($ErrorTemplate, "Cannot read product CSV file.", $ErrorEmail);
    exit;
  }
  $linenum = 0;
  while (!feof($fh))
  {
    $linenum++;
    $lne = fgets($fh, 1024);
    $lne = trim($lne);
    if ((strlen($lne) > 2) && (substr($lne, 0, 4) != "<!--") && (substr($lne, 0, 2) != "//"))
    {
      $fields = explode($ProductDelimiter, $lne);
      $numfields=count($fields);
      for ($k = 0; $k < count($fields);$k++)
        $fields[$k] = trim($fields[$k]);
      if (($numfields != 5) && ($numfields != 6) && ($numfields != 7))
      {
        ShowMessage($ErrorTemplate, "Error in product CSV file line " . $linenum, $ErrorEmail);
        fclose($fh);
        exit;
      }
      if ($numfields>5)
      {
        // Check that fields 5 and 6 are numeric
        if ((!is_numeric($fields[4])) || (!is_numeric($fields[5])))
        {
          ShowMessage($ErrorTemplate, "Error in product CSV file line " . $linenum, $ErrorEmail);
          fclose($fh);
          exit;
        }
      }
      else
      {
        // For Payloom check that field 5 is numeric
        if (!is_numeric($fields[4]))
        {
          ShowMessage($ErrorTemplate, "Error in product CSV file line " . $linenum, $ErrorEmail);
          fclose($fh);
          exit;
        }      
      }
      // If field 3 is numeric then we should assume $DefaultCurreny (or USD) as the currency
      if (is_numeric($fields[2]))
        $fields[2] = $DefaultCurrency."=" . $fields[2];
      if ($numfields == 5)
        $Products[] = $fields[0] . $ProductDelimiter . $fields[1] . $ProductDelimiter . $fields[2] . $ProductDelimiter . $fields[3] . $ProductDelimiter . "0" . $ProductDelimiter . $fields[4];
      if ($numfields == 6)
        $Products[] = $fields[0] . $ProductDelimiter . $fields[1] . $ProductDelimiter . $fields[2] . $ProductDelimiter . $fields[3] . $ProductDelimiter . $fields[4] . $ProductDelimiter . $fields[5];
      if ($numfields == 7)
        $Products[] = $fields[0] . $ProductDelimiter . $fields[1] . $ProductDelimiter . $fields[2] . $ProductDelimiter . $fields[3] . $ProductDelimiter . $fields[4] . $ProductDelimiter . $fields[5] . $ProductDelimiter . $fields[6];
    }
  }
  fclose($fh);
}

if (($DbHostLinklok!="") && ($DbUserLinklok!="") && ($DbPasswordLinklok!="") && ($DbNameLinklok!=""))
{
  $mysql_linklok=mysql_connect($DbHostLinklok,$DbUserLinklok,$DbPasswordLinklok);
  if ($mysql_linklok==0)
  {
    ShowMessage($ErrorTemplate, "Error connecting to MySQL", $ErrorEmail);
    exit;
  }
  $db=mysql_select_db($DbNameLinklok,$mysql_linklok);
  if ($db==False)
  {
    mysql_close($mysql_linklok);
    ShowMessage($ErrorTemplate, "Error connecting to the database", $ErrorEmail);
    exit;
  }  
}
if (($DbTableNameProducts=="") || (!isset($DbTableNameProducts)))
  $numberofproducts = count($Products);
else
{
  $mysql_result = mysql_query("SELECT count(*) from ".$DbTableNameProducts,$mysql_linklok);
  $numberofproducts = ($mysql_result>0) ? mysql_result($mysql_result,0,0) : 0;
}
// See if called as return page
if ($calledasreturn != 1)
{
  $calledasreturn = 0;
  if (strcasecmp($_SERVER["PATH_INFO"], "/return") == 0)
    $calledasreturn = 1;
}
if (!isset($ClearVibracart))
  $ClearVibracart=false;
if (($ClearVibracart) && ($calledasreturn==1))
{
  session_start();
  $_SESSION['test']="1234";
  reset($_SESSION);
  if (!empty($_SESSION))
  {
    while(list($namepair, $valuepair) = each($_SESSION))
    {
      if (substr($namepair,0,10)=="sess_cart_")
        unset($_SESSION[$namepair]);
    }
  }
  reset($_SESSION);
}
// If ?orderform then request password
if ((isset($orderform)) || (isset($ORDERFORM)) || (isset($testform)) || (isset($TESTFORM)))
{
  print "<html><head><title>Linklok Paypal</title></head><body>\n";
  print "<script language=\"JavaScript\">\n";
  print "<!-- JavaScript\n";
  print "function validateform(form)\n";
  print "{\n";
  print "  if (form.password.value==\"\")\n";
  print "  {\n";
  print "    alert(\"Please enter the password\")\n";
  print "    form.password.focus()\n";
  print "    return(false)\n";
  print "  }\n";
  print "  return(true);\n";
  print "}\n";
  print "// - JavaScript - -->\n";
  print "</script>\n";
  print "<form name=\"form1\" method=\"post\" action=\"$LinklokURL\" onSubmit=\"return validateform(this);\">\n";
  if ((isset($testform)) || (isset($TESTFORM)))
    print "<input type=\"hidden\" name=\"testmode\" value=\"Y\">\n";
  print "<p align=\"left\"><font face=\"Arial\" color=\"#333399\"><span style=\"font-size:16pt;\"><b>Linklok Manual Order Entry</b></span></font></p>\n";
  print "<table border=\"0\" cellpadding=\"0\" cellspacing=\"10\" bgcolor=\"#DDE3F0\">\n";
  print "<tr><td><p><font face=\"Arial\" size=\"2\">Password</font></p></td>\n";
  print "<td><p><input type=\"password\" name=\"password\" maxlength=\"50\" size=\"30\"></p></td></tr><tr><td><p>&nbsp;</p></td>\n";
  print "<td align=\"right\"><p><input type=\"submit\" name=\"button1\" value=\"Login\"></p></td>";
  print "</tr></table></form>\n";
  print "<script language=\"JavaScript\">\n";
  print "<!-- JavaScript\n";
  print "form1.password.focus()\n";
  print "// - JavaScript - -->\n";
  print "</script>\n";
  print "</body></html>\n";
  exit;
}
// If password entered and correct then display manual order form
if (($ManualPassword != "") && ($ManualPassword == $password) && ($testtype==""))
{
  print "<html><head><title>Linklok Paypal</title></head><body>\n";
  print "<script language=\"JavaScript\">\n";
  print "<!-- JavaScript\n";
  print "function validateform(form)\n";
  print "{\n";
  print "  if (form.item_number1.value==\"\")\n";
  print "  {\n";
  print "    alert(\"Please select at least one item\")\n";
  print "    form.item_number1.focus()\n";
  print "    return(false)\n";
  print "  }\n";
  print "  if (form.item_number10.value==\"\")\n";
  print "    form.num.value=9;\n";
  print "  if (form.item_number9.value==\"\")\n";
  print "    form.num.value=8;\n";
  print "  if (form.item_number8.value==\"\")\n";
  print "    form.num.value=7;\n";
  print "  if (form.item_number7.value==\"\")\n";
  print "    form.num.value=6;\n";
  print "  if (form.item_number6.value==\"\")\n";
  print "    form.num.value=5;\n";
  print "  if (form.item_number5.value==\"\")\n";
  print "    form.num.value=4;\n";
  print "  if (form.item_number4.value==\"\")\n";
  print "    form.num.value=3;\n";
  print "  if (form.item_number3.value==\"\")\n";
  print "    form.num.value=2;\n";
  print "  if (form.item_number2.value==\"\")\n";
  print "    form.num.value=1;\n";
  print "  if (form.newexpiry2.value!=\"\")\n";
  print "  {\n";
  print "    if (ValidChars(form.newexpiry2.value,\"0123456789\")==false)\n";
  print "    {\n";
  print "      alert(\"Please enter a valid expiry time or select from the menu\")\n";
  print "      form.newexpiry2.focus()\n";
  print "      return(false)\n";
  print "    }\n";
  print "    form.newexpiry.value=form.newexpiry2.value\n";
  print "  }\n";
  print "  else\n";
  print "  {\n";
  print "    form.newexpiry.value=form.newexpiry1.value\n";
  print "  }\n";
  print "  if ((form.manualipaddr.value==\"\") && (form.manualiplevel.value!=\"0\"))\n";
  print "  {\n";
  print "    alert(\"Please enter the recipients IP address or select the IP level - off\")\n";
  print "    form.manualipaddr.focus()\n";
  print "    return(false)\n";
  print "  }\n";
  print "  if (ValidateIP(form.manualipaddr.value)==false)\n";
  print "  {\n";
  print "    alert(\"Please enter a valid IP address or leave blank\")\n";
  print "    form.manualipaddr.focus()\n";
  print "    return false\n";
  print "  }\n";
  print "  if (form.manualipaddr.value==\"\")\n";
  print "    form.manualipaddr.value=\"0.0.0.0\";\n";
  if ($testmode=="Y")
  {    
    print "  if (form.payer_email.value==\"\")\n";
    print "  {\n";
    print "     alert(\"Please enter a valid email address\")\n";
    print "     form.payer_email.focus()\n";
    print "     return(false)\n";
    print "  }\n";
  }
  print "  if (form.payer_email.value!=\"\")\n";
  print "  {\n";
  print "    if (ValidateEmail(form.payer_email.value)==false)\n";
  print "    {\n";
  print "      alert(\"Please enter a valid email address\")\n";
  print "      form.payer_email.focus()\n";
  print "      return(false)\n";
  print "    }\n";
  print "  }\n";
  print "  return(true);\n";
  print "}\n";
  print "\n";
  print "function ValidateEmail(str)\n";
  print "{\n";
  print "    // are regular expressions supported?\n";
  print "    var supported = 0;\n";
  print "    if (window.RegExp) {\n";
  print "      var tempStr = \"a\";\n";
  print "      var tempReg = new RegExp(tempStr);\n";
  print "      if (tempReg.test(tempStr)) supported = 1;\n";
  print "    }\n";
  print "    if (!supported)\n";
  print "      return (str.indexOf(\".\") > 2) && (str.indexOf(\"@\") > 0);\n";
  print "    var r1 = new RegExp(\"(@.*@)|(\\\.\\\.)|(@\\\.)|(^\\\.)\");\n";
  print "    var r2 = new RegExp(\"^.+\\\@(\\\[?)[a-zA-Z0-9\\\-\\\.]+\\\.([a-zA-Z]{2,7}|[0-9]{1,3})(\\\]?)$\");\n";
  print "    return (!r1.test(str) && r2.test(str));\n";
  print "}\n";
  print "function ValidateIP(ip)\n";
  print "{\n";
  print "  if (ip!=\"\")\n";
  print "  {\n";
  print "    var ni\n";
  print "    if (ValidChars(ip,\"0123456789.\")==false)\n";
  print "      return(false)\n";
  print "    var ipparts=ip.split(\".\")\n";
  print "    if (ipparts.length!=4)\n";
  print "      return(false)\n";
  print "    for (var k=0; k<4; k++)\n";
  print "    {\n";
  print "      if (ipparts[k].length<1)\n";
  print "      return(false)\n";
  print "      if ((ipparts[k].charAt(0)==\"0\") && (ipparts[k].length>1))\n";
  print "      return(false)\n";
  print "    ni=parseInt(ipparts[k],10)\n";
  print "    if ((ni<0) || (ni>255))\n";
  print "      return (false)\n";
  print "    }\n";
  print "  }\n";
  print "    return(true)\n";
  print "}\n";
  print "function ValidChars(str,valid)\n";
  print "{\n";
  print "  var v=true\n";
  print "  for (i=0;i<str.length;i++)\n";
  print "  {\n";
  print "    if (valid.indexOf(str.charAt(i))==-1)\n";
  print "    {\n";
  print "      v=false\n";
  print "      break\n";
  print "    }\n";
  print "  }\n";
  print "  return(v)\n";
  print "}\n";
  print "// - JavaScript - -->\n";
  print "</script>\n";
  print "<form name=\"form1\" method=\"post\" action=\"$LinklokURL\" onSubmit=\"return validateform(this);\">\n";
  print "<input type=\"hidden\" name=\"password\" value=\"$password\">\n";
  print "<input type=\"hidden\" name=\"num\" value=\"10\">\n";
  print "<input type=\"hidden\" name=\"txn_id\" value=\"" . (string)time() . "\">\n";
  print "<p align=\"left\"><font face=\"Arial\" color=\"#333399\"><span style=\"font-size:16pt;\">";
  if ($testmode=="Y")  
    print "<b>Linklok Test Form</b></span></font></p>\n";
  else
    print "<b>Linklok Manual Order Entry</b></span></font></p>\n";  
  print "<table border=\"0\" cellpadding=\"0\" cellspacing=\"10\" bgcolor=\"#DDE3F0\">\n";
  for ($i = 1;$i <= 10;$i++)
  {
    $fnum = "item_number" . $i;
    print "<tr>\n";
    print "<td><p><font face=\"Arial\" size=\"2\">Item $i</font></p></td>\n";
    if (($numberofproducts <= 500) && ((($DbTableNameProducts=="") && ($DefaultLocation=="")) || ($DbTableNameProducts!="")))
    {
      if (($DbTableNameProducts=="") || (!isset($DbTableNameProducts)))
      {
        print "<td><p><select name=\"item_number$i\" size=\"1\">\n";
        if ($$fnum == "")
          print "<option selected value=\"\">Select Product</option>\n";
        else
          print "<option value=\"\">Select Product</option>\n";
        for ($k = 0;$k < $numberofproducts;$k++)
        {
          $in = strtok($Products[$k], $ProductDelimiter);
          $id = strtok($ProductDelimiter);
          if (($$fnum != "") && ($$fnum == $in))
            print "<option selected value=\"$in\">$in - $id</option>\n";
          else
            print "<option value=\"$in\">$in - $id</option>\n";
        }
        print "</select>\n";
      }  
      else
      {
        print "<td><p><select name=\"item_number$i\" size=\"1\">\n";
        if ($$fnum == "")
          print "<option selected value=\"\">Select Product</option>\n";
        else
          print "<option value=\"\">Select Product</option>\n";
        $query="SELECT * FROM ".$DbTableNameProducts;
        $mysql_result=mysql_query($query,$mysql_linklok);
        $found = 0;
  	    while($row=mysql_fetch_array($mysql_result,MYSQL_ASSOC))
  	    {
          $in=$row[$DbProductsId];
          $id=$row[$DbProductsDescription];
          if (($$fnum != "") && ($$fnum == $in))
            print "<option selected value=\"$in\">$in - $id</option>\n";
          else
            print "<option value=\"$in\">$in - $id</option>\n";
        }
        print "</select>\n";        
      }
    }
    else
    {
      print "<td><p><input type=\"text\" name=\"item_number$i\" size=\"30\" value=\"" . $$fnum . "\">\n";
    }
    if (($payer_email == "") && ($$fnum != ""))
    {
      $res = GetProductDetails($$fnum, "", $cost, $desc, $link, $size, $exp, $extra);
      if ($res != 0)
      {
        $combos = explode(":", $link);
        if ((count($combos) == 1) || (substr(strtolower($link), 0, 5) == "http:") || (substr($link, 1, 2) == ":/"))
        {        
          if ($newexpiry != -1)
            $exp = $newexpiry;
          if ($exp != 0)
          {
            if (strlen($exp) == 12)
            {
              $expiry = mktime(substr($exp, 8, 2), substr($exp, 10, 2), 0, substr($exp, 4, 2), substr($exp, 6, 2), substr($exp, 0, 4), -1);
              $exp = ($expiry - time()) / 60;
            }
            else
              $expiry = time() + ($exp * 60);
          }
          else
            $expiry = 0;
          $ipleveltouse = 0;
          if (isset($IPlock))
            $ipleveltouse = $IPlock;
          if ($manualiplevel != "-1")
            $ipleveltouse = $manualiplevel;
          else
          {
            $pos = strpos($extra, "IPLOCK=");
            if (is_integer($pos))
              $ipleveltouse = substr($extra, $pos + 7, 1);
          }
          $plink = GetSecureLink($link, $$fnum, $expiry, $txn_id, $manualipaddr, $ipleveltouse, $payer_email);
          $fnameonly = filename($link);
          print "&nbsp;&nbsp;&nbsp;<a href=\"$plink\" target=\"_linklok\">$fnameonly</a>";
        }
        else
          print "&nbsp;&nbsp;&nbsp;Bundle";                
      }
    }
    print "</td></tr>\n";
  }
  if ($testmode=="Y")
  {
    print "<input type=\"hidden\" name=\"newexpiry\" value=\"\">\n";
    print "<input type=\"hidden\" name=\"newexpiry1\" value=\"-1\">\n";
    print "<input type=\"hidden\" name=\"newexpiry2\" value=\"\">\n";  
  }
  else  
  {
    print "<tr><td><p><font face=\"Arial\" size=\"2\">Override Expiry</font></p></td>\n";
    print "<input type=\"hidden\" name=\"newexpiry\" value=\"\">\n";
    print "<td><p><select name=\"newexpiry1\" size=\"1\">\n";
    print "<option selected value=\"-1\">Leave as default</option>\n";
    print "<option value=\"10\">10 minutes</option>\n";
    print "<option value=\"60\">1 Hour</option>\n";
    print "<option value=\"180\">3 Hours</option>\n";
    print "<option value=\"1440\">24 Hours</option>\n";
    print "<option value=\"4320\">3 Days</option>\n";
    print "<option value=\"10080\">7 Days</option>\n";
    print "<option value=\"43200\">30 Days</option>\n";
    print "<option value=\"525600\">1 Year</option>\n";
    print "<option value=\"0\">No Expiry</option>\n";
    print "</select>&nbsp;or&nbsp;<input type=\"text\" name=\"newexpiry2\" value=\"$newexpiry2\" maxlength=\"12\" size=\"15\">\n";
    print "</p></td></tr>\n";
  }
  if ($AdvancedForm == true)
  {
    print "<tr><td colspan=\"2\"><p align=\"center\"><font face=\"Arial\" size=\"2\">Optional values</font></p></td></tr>\n";
    print "<tr><td><p><font face=\"Arial\" size=\"2\">Option 1 title (on0)</font></p></td><td><p>\n";
    print "<font face=\"Arial\"><span style=\"font-size:10pt;\"><input type=\"text\" name=\"on0\" value=\"Fingerprint\" size=\"30\"></span></font></p></td></tr>\n";
    print "<tr><td><p><font face=\"Arial\" size=\"2\">Option 1 value (os0)</font></p></td><td><p>\n";
    print "<font face=\"Arial\"><span style=\"font-size:10pt;\"><input type=\"text\" name=\"os0\" size=\"30\"></span></font></p></td></tr>\n";
    print "<tr><td><p><font face=\"Arial\" size=\"2\">Option 2 title (on1)</font></p></td><td><p>\n";
    print "<font face=\"Arial\"><span style=\"font-size:10pt;\"><input type=\"text\" name=\"on1\" value=\"License name\"size=\"30\"></span></font></p></td></tr>\n";
    print "<tr><td><p><font face=\"Arial\" size=\"2\">Option 2 value (os1)</font></p></td><td><p>\n";
    print "<font face=\"Arial\"><span style=\"font-size:10pt;\"><input type=\"text\" name=\"os1\" size=\"30\"></span></font></p></td></tr>\n";
  }
  if ($testmode!="Y")  
  {
    if (isset($IPlock))
    {
      print "<tr><td><p><font face=\"Arial\" size=\"2\">IP address</font></p></td><td><p>\n";
      print "<font face=\"Arial\"><span style=\"font-size:10pt;\"><input type=\"text\" name=\"manualipaddr\" maxlength=\"15\" size=\"23\">\n";
      print "<select name=\"manualiplevel\" size=\"1\">\n";
      print "  <option value=\"-1\">Default</option>\n";
      print "  <option selected value=\"0\">Level 0 (off)</option>\n";
      print "  <option value=\"1\">Level 1</option>\n";
      print "  <option value=\"2\">Level 2</option>\n";
      print "  <option value=\"3\">Level 3</option>\n";
      print "  <option value=\"4\">Level 4</option>\n";
      print "</select>\n";
    }
    else
    {
      print "<input type=\"hidden\" name=\"manualipaddr\" value=\"\">\n";
      print "<input type=\"hidden\" name=\"manualiplevel\" value=\"0\">\n";
    }
  }
  else
  {
    print "<input type=\"hidden\" name=\"manualipaddr\" value=\"\">\n";
    print "<input type=\"hidden\" name=\"manualiplevel\" value=\"0\">\n";
  }
  print "<tr><td valign=\"top\"><p><font face=\"Arial\" size=\"2\">First name</font></p></td><td><p>\n";
  print "<font face=\"Arial\"><span style=\"font-size:10pt;\"><input type=\"text\" name=\"first_name\" size=\"39\"></span></font></p></td></tr>\n";
  print "<tr><td valign=\"top\"><p><font face=\"Arial\" size=\"2\">Last name</font></p></td><td><p>\n";
  print "<font face=\"Arial\"><span style=\"font-size:10pt;\"><input type=\"text\" name=\"last name\" size=\"39\"></span></font></p></td></tr>\n";
  print "<tr><td valign=\"top\"><p><font face=\"Arial\" size=\"2\">Email address</font></p></td><td><p>\n";
  print "<font face=\"Arial\"><span style=\"font-size:10pt;\"><input type=\"text\" name=\"payer_email\" size=\"39\">";
  if ($testmode!="Y")
    print "<br>Leave blank to display links only</span></font></p></td></tr>\n";
  if ($testmode=="Y")
  {  
    print "<tr><td><p><font face=\"Arial\" size=\"2\">Currency</font></p></td><td><p>\n";
    print "<font face=\"Arial\"><span style=\"font-size:10pt;\">\n";
    print "<select name=\"mc_currency\" size=\"1\">\n";
    print "  <option value=\"USD\">USD</option>\n";
    print "  <option value=\"GBP\">GBP</option>\n";
    print "  <option value=\"EUR\">EUR</option>\n";
    print "  <option value=\"AUD\">AUD</option>\n";
    print "  <option value=\"BRL\">BRL</option>\n";   
    print "  <option value=\"CAD\">CAD</option>\n";
    print "  <option value=\"CHF\">CHF</option>\n";
    print "  <option value=\"CZK\">CZK</option>\n";
    print "  <option value=\"DKK\">DKK</option>\n";
    print "  <option value=\"HKD\">HKD</option>\n";
    print "  <option value=\"HUF\">HUF</option>\n";
    print "  <option value=\"ILS\">ILS</option>\n";
    print "  <option value=\"JPY\">JPY</option>\n";
    print "  <option value=\"NOK\">NOK</option>\n";
    print "  <option value=\"NZD\">NZD</option>\n";
    print "  <option value=\"PLN\">SEK</option>\n";
    print "  <option value=\"SEK\">SEK</option>\n";
    print "  <option value=\"SGD\">SGD</option>\n";
    print "</select>\n";
    print "</font></p></td></tr>\n";
  
    print "<tr><td><p><font face=\"Arial\" size=\"2\">Test type</font></p></td><td><p>\n";
    print "<font face=\"Arial\"><span style=\"font-size:10pt;\">\n";
    print "<select name=\"testtype\" size=\"1\">\n";
    print "  <option value=\"email\">Email</option>\n";
    print "  <option value=\"return\">Return (download) page</option>\n";
    print "</select>\n";
    print "</font></p></td></tr>\n";
    print "<tr><td><p><font face=\"Arial\"><span style=\"font-size:10pt;\">&nbsp;</span></font></p></td>\n";
    print "<td><p align=\"right\"><font face=\"Arial\"><span style=\"font-size:10pt;\"><input type=\"submit\" name=\"Submit\" value=\"Click to Test\">\n";
    print "</span></font></p></td></tr>\n";  
  }
  else
  {
    print "<tr><td><p><font face=\"Arial\"><span style=\"font-size:10pt;\">&nbsp;</span></font></p></td>\n";
    print "<td><p align=\"right\"><font face=\"Arial\"><span style=\"font-size:10pt;\"><input type=\"submit\" name=\"Submit\" value=\"Send Links\">\n";
    print "</span></font></p></td></tr>\n";
  }
  print " </table></form>\n";
  print "<script language=\"JavaScript\">\n";
  print "<!-- JavaScript\n";
  print "form1.item_number1.focus()\n";
  print "// - JavaScript - -->\n";
  print "</script>\n";
  print "</body></html>\n";
  if ($payer_email == "")
    exit;
}

if (($ManualPassword != "") && (isset($password)) && ($ManualPassword != $password) && (!isset($num)))
{
  ShowMessage($ErrorTemplate, "Incorrect password.", "");
  exit;
}
// See if processing manual order
$manualorder = false;
if (($item_number1 != "") && ($num != "") && ($ManualPassword != "") && ($ManualPassword == $password))
{
  $manualorder = true;
  $res = "VERIFIED";
  $payment_status = "Completed";
  $receiver_email = $PaypalEmail;
  $clientipaddr = "0.0.0.0";
  if ($manualipaddr != "")
    $clientipaddr = $manualipaddr;
  if ($mc_currency=="")
  {
    if ($DefaultCurrency!="")
      $mc_currency=$DefaultCurrency;
    else  
      $mccurrency="USD";  
  }
  $mc_gross=0.00; 
  $num_cart_items=$num; 
  for ($k=1;$k<=$num_cart_items;$k++)
  {
    $pvar = "item_number" . $k;
 		$j = GetProductDetails($$pvar,$mc_currency,$cost,$desc, $link, $size, $exp, $lkextra);
  	if ($j!=0)
	  {
	    $pvar = "item_name". $k;
	    $$pvar=$desc;
	    $pvar = "mc_gross_". $k;
      if ($testtype!="")
  	    $$pvar=$cost;
  	  else
  	    $$pvar="0.00";  	    
	    $pvar = "quantity". $k;
	    $$pvar="1";
	    $mc_gross=$mc_gross+$cost;
	  }
  }  
  $tax=0.00;
  $mc_handling=0.00;
  $mc_fee=0.00;
  $method="MANUAL";
  $option_selection1=$os0;
  $option_selection2=$os1;
  $option_name1=$on0;
  $option_name2=$on1;
  // See if manual orderform being used for tesing
  if ($testtype!="")
  {
    $Test="Y";
    $manualorder = false;
    if ($testtype=="email")
    {
      $calledasreturn=0;
    }
    if ($testtype=="return")
    {
      $calledasreturn=1;
    }    
  }
  else
  {
    if ($ManualEmailTemplate!="")
      $EmailTemplate=$ManualEmailTemplate;
    $mc_gross="0.00";  
  }  
}
// See if download request with page background (email links that display a page in the browser as well)
if (isset($authe))
{ 
  if ($DownloadBackground!="")
  {
    $page="";
    // See if background page is html or php
    $ext = fileextension($DownloadBackground);  
    if ($ext == ".php")
    {
      ob_start();
      include $DownloadBackground;
      $page = ob_get_contents(); 
      ob_end_clean();    
    }
    else
    {
      if ($fh = @fopen($DownloadBackground, "r"))
      {
        $page = fread ($fh, 200000);
        fclose($fh);
      }
    }
    if ($page!="")
    {  
      $page = str_replace("!!!link!!!", $LinklokURL."?llauth=".$authe, $page);
      $page = str_replace("<body", "<body onLoad=\"download()\"", $page);      
      $redirectcode ="<script language=\"JavaScript\" type=\"text/javascript\">\n";
      $redirectcode.="function download()\n";
      $redirectcode.="{\n";
      $redirectcode.="  window.location=\"".$LinklokURL."?llauth=".$authe."\"\n";
      $redirectcode.="}\n";
      $redirectcode.="</script>\n";
      $redirectcode.="</body>\n";  
      $page = str_replace("</body>", $redirectcode, $page);
      print $page;
      exit;      
    }
    else
      $llauth=$authe;
  }
  else
    $llauth=$authe;
}
// See if download request
if (isset($llauth))
{
  // Remove any /filename from end
  $pos = strpos($llauth, "/");
  if (is_integer($pos))
    $llauth = substr($llauth, 0, $pos);
  $llauth = rawurldecode($llauth);
  $oldlink = false;
  if (is_integer(strpos($llauth, ",")))
    $oldlink = true;
  if ($oldlink == false)
    $llauth = base64_decode($llauth);   
  $fields=explode(",",$llauth);
  if ($oldlink)
  {
    $item_number = trim($fields[0]);
    $expiry = trim($fields[1]);
    $txn_id = trim($fields[2]);
    $ip="0.0.0.0";
    $ipl="0";
    $email="";
    $hash = md5($LinkKey . $item_number . $expiry . $txn_id);    
  }
  else
  {
    if (count($fields)==6)
    {
      $item_number = trim($fields[0]);
      $expiry = trim($fields[1]);
      $txn_id = trim($fields[2]);
      $ip = trim($fields[3]);
      $ipl = trim($fields[4]);
      $verifyhash = trim($fields[5]);
      $email="";
      $hash = md5($LinkKey . $item_number . $expiry . $txn_id . $ip . $ipl);
    }
    else
    {
      $item_number = trim($fields[0]);
      $expiry = trim($fields[1]);
      $txn_id = trim($fields[2]);
      $ip = trim($fields[3]);
      $ipl = trim($fields[4]);
      $email = trim($fields[5]);
      $verifyhash = trim($fields[6]);
      $hash = md5($LinkKey . $item_number . $expiry . $txn_id . $ip . $ipl . $email);
    }
  }  
  if ($verifyhash != $hash)
  {
    ShowMessage($ErrorTemplate, "Permission to download denied. Authentication failed.", $ErrorEmail);
    exit;
  }
  $email=urldecode($email);
  // auth is OK but we should now check if link expired
  if ($expiry != 0)
  {
    $curtime = time();
    if ($curtime > $expiry)
    {
      ShowMessage($ErrorTemplate, "Sorry but this download link has expired.", $ErrorEmail);
      exit;
    }
  }
  if (($ipl != "0") && ($ip != "0.0.0.0"))
  {
    $ipaddr = $_SERVER['REMOTE_ADDR'];
    $ipo[1] = strtok($ip, ".");
    $ipo[2] = strtok(".");
    $ipo[3] = strtok(".");
    $ipo[4] = strtok(".");
    $ipn[1] = strtok($ipaddr, ".");
    $ipn[2] = strtok(".");
    $ipn[3] = strtok(".");
    $ipn[4] = strtok(".");
    for ($k = 1;$k <= $ipl;$k++)
    {
      if ($ipo[$k] != $ipn[$k])
      {
        ShowMessage($ErrorTemplate, "Linklok link IP address is not valid.\n", $ErrorEmail);
        exit;
      }
    }
  }
  // Everything is OK so we can allow download of file
  GetProductDetails($item_number, "", $cost, $desc, $link, $size, $exp, $extra);
  // See if NOTIFYDOWNLOAD is set
  $pos = strpos(strtoupper($extra), "NOTIFYDOWNLOAD=");
  if (is_integer($pos))
  {
    $pos2=strpos($extra,"*",$pos);
    if (is_integer($pos2))    
      $NotifyDownloadEmail=substr($extra,$pos+15,$pos2-$pos-15);
    else
      $NotifyDownloadEmail=substr($extra,$pos+15);              
  }
  // Check to see if PDF Honeypot link
  $pos = strpos(strtoupper($extra), "PDFHONEYPOT=");
  if (is_integer($pos))
  {
    global $pdfhpurl, $pdfhpsecret;
    if ($NotifyDownloadEmail != "")
      EmailDownloadNotify($fname, $item_number, $desc, $txn_id, $_SERVER['REMOTE_ADDR'], $expiry, $email);
    $link=llipn_pdfhoneypot($link,$email,$ip,$pdfhpsecret,$pdfhpurl);
    header("Location: ".$link);
    exit;
  }         
  $ext = fileextension($link);
  $fname = filename($link);
  // If download path is for S3 then handle it now
  if (substr(trim(strtolower($link)),0,3)=="s3|")
  {
    if ($NotifyDownloadEmail != "")
      EmailDownloadNotify($fname, $item_number, $desc, $txn_id, $_SERVER['REMOTE_ADDR'], $expiry, $email);
    $url=get_s3_url($link,time()+$ServerTimeAdjust,"GET");
    header("Location: ".$url);
    exit;
  }
  // If download link is php page then just include it.
  if ($ext == ".php")
  {
    if ($NotifyDownloadEmail != "")
      EmailDownloadNotify($fname, $item_number, $desc, $txn_id, $_SERVER['REMOTE_ADDR'], $expiry, $email);
    // If there are any GET variables in the filename then set those in $_GET and $_REQUEST
    $fquery="";
    $pos=strpos($link,"?");
    if (is_integer($pos))
      $fquery=substr($link,$pos+1);
    if ($fquery!="")
    {
      $fvars=explode("&",$fquery);
      for ($k=0;$k<count($fvars);$k++)
      {
        $fvar=strtok($fvars[$k],"=");
        $fval=strtok("=");
        if ($fvar!="")
        {
          $_GET[$fvar]=$fval;
          $_REQUEST[$fvar]=$fval;
        }      
      }
      // Remove query from link
      $link=str_replace("?".$fquery,"",$link);      
    }
    include $link;
    exit;
  }
  // If download link is html page then just display it.
  if (($ext == ".html") || ($ext == ".htm"))
  {
    if (!($fh = @fopen($link, "rb")))
    {
      ShowMessage($ErrorTemplate, "Could not open file.", $ErrorEmail);
      exit;
    }
    header("Content-type: text/html\n");
    header("Content-transfer-encoding: binary\n");
    if ((strtolower(ini_get('zlib.output_compression'))!="on") && (ini_get('zlib.output_compression')!="1"))
    {
      if ((int)$size > 0)
        header("Content-Length: " . $size . "\n");
    }  
    if ($NotifyDownloadEmail != "")
      EmailDownloadNotify($fname, $item_number, $desc, $txn_id, $_SERVER['REMOTE_ADDR'], $expiry, $email);
    @xfpassthru($fh);
    exit;
  }
  $mimetype=llipn_getmimetype($fname);
  // Check file exists
  if (!is_readable($link))
  {
    ShowMessage($ErrorTemplate, "Could not open file.", $ErrorEmail);
    exit;
  }  
  header("Content-disposition: attachment; filename=\"".basename($fname)."\"\n");
  if ($mimetype!="")
    header("Content-type: ".$mimetype."\n");      
  else 
    header("Content-type: application/octet-stream\n");          
  header("Content-transfer-encoding: binary\n");
  // See if link is local path or URL
  $pos = strpos(strtolower($link), "http://");
  if (!is_integer($pos))
  {
    $fsize = $size;
    /* is resume requested? */
  	if (isset($_SERVER['HTTP_RANGE']))
    {
		  llipn_rangeDownload($link);
    }
    else
    {
      if (!($fh = @fopen($link, "rb")))
      {
        ShowMessage($ErrorTemplate, "Could not open file.", $ErrorEmail);
        exit;
      }
      header("Accept-Ranges: bytes");
      if ((strtolower(ini_get('zlib.output_compression'))!="on") && (ini_get('zlib.output_compression')!="1"))
        header("Content-Length: " . $size . "\n");
      if ($NotifyDownloadEmail != "")
        EmailDownloadNotify($fname, $item_number, $desc, $txn_id, $_SERVER['REMOTE_ADDR'], $expiry, $email);
      @xfpassthru($fh);
    }
  }
  else
  {
    // link is a URL rather than local path so do simple download
    $link = str_replace(" ", "%20", $link);
    if (!($fh = @fopen($link, "rb")))
    {
      ShowMessage($ErrorTemplate, "Could not open file.", $ErrorEmail);
      exit;
    }
    if ((strtolower(ini_get('zlib.output_compression'))!="on") && (ini_get('zlib.output_compression')!="1"))
    {
      if ((int)$size > 0)
        header("Content-Length: " . $size . "\n");
    }
    if ($NotifyDownloadEmail != "")
      EmailDownloadNotify($fname, $item_number, $desc, $txn_id, $_SERVER['REMOTE_ADDR'], $expiry, $email);
    @xfpassthru($fh);
  }
  exit;
}
// If Auto Return and PDT used then verify order and get variables.
if (($calledasreturn==1) && (isset($_GET['tx'])) &&  (isset($_GET['amt'])))
{
  $calledasautoreturn=1;
  if ($VerifyCurl==1)
  {
    $req = 'cmd=_notify-synch';
    $tx_token = $_GET['tx'];
    $req .= "&tx=$tx_token&at=$PDTtoken";
//    $req .= "&sig=" . $_GET['sig'];
    $ch = curl_init();
    if ($UseSandbox==1)
      curl_setopt($ch, CURLOPT_URL,"https://www.sandbox.paypal.com/cgi-bin/webscr");
    else  
      curl_setopt($ch, CURLOPT_URL,"http://www.paypal.com/cgi-bin/webscr");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: application/x-www-form-urlencoded", "Content-Length: " . strlen($req)));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
    $ret = curl_exec ($ch);
    curl_close ($ch);
  }
  else
  {
    // read the post from PayPal system and add 'cmd'
    $req = 'cmd=_notify-synch';
    $tx_token = $_GET['tx'];
    $req .= "&tx=$tx_token&at=$PDTtoken";
    // post back to PayPal system to validate
    $header .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
    $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
    $header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
    if ($UseSandbox==1)
      $fp = fsockopen ('ssl://www.sandbox.paypal.com', 80, $errno, $errstr, 30);
    else
      $fp = fsockopen ('www.paypal.com', 80, $errno, $errstr, 30);      
    if (!$fp) 
    {
      $res="";
    } 
    else
    {
      fputs ($fp, $header . $req);
      // read the body data
      $ret = '';
      $headerdone = false;
      while (!feof($fp))
      {
        $line = fgets ($fp, 1024);
        if (strcmp($line, "\r\n") == 0)
        {
          // read the header
          $headerdone = true;
        }
        else if ($headerdone)
        {
          // header has been read. now read the contents
          $ret .= $line;
        }
      }
      fclose ($fp);
    }  
  }   
  // parse the data
  $lines = explode("\n", $ret);
  $keyarray = array();
  if (strcmp ($lines[0], "SUCCESS") == 0)
  {
    for ($i=1; $i<count($lines);$i++)
    {
      list($key,$val) = explode("=", $lines[$i]);
      $keyarray[urldecode($key)] = urldecode($val);
    }      
    // assign posted variables to local variables
    if (!empty($keyarray)) while (list($name, $value) = each($keyarray)) $$name = $value; 
    $res="VERIFIED";         
  }
  else if (strcmp ($lines[0], "FAIL") == 0)
  {
    $res="";
  }
}
// If not download request then establish whether we are being called from paypal.com
if (($Test == "Y") || ($manualorder == true))
{
  $res = "VERIFIED";
}
else if ($calledasautoreturn!=1)
{
  if ($VerifyCurl==1)
  {
    if ($UseSandbox==1)
      $curlurl = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
    else
      $curlurl = 'http://www.paypal.com/cgi-bin/webscr';    
    // get post vars 
    $curlreq = 'cmd=_notify-validate'; 
    foreach($_POST as $curlkey => $curlvalue) 
    { 
        $$curlkey = trim(stripslashes($curlvalue)); 
        $curlreq .= '&'. $curlkey .'='. urlencode($curlvalue); 
    } 
     
    // submit post values to paypal 
    $curlch = curl_init(); 
    curl_setopt($curlch, CURLOPT_URL, $curlurl); 
    curl_setopt($curlch, CURLOPT_POST, true); 
    curl_setopt ($curlch, CURLOPT_POSTFIELDSIZE, 0); 
    curl_setopt ($curlch, CURLOPT_POSTFIELDS, $curlreq); 
    curl_setopt($curlch, CURLOPT_RETURNTRANSFER, true); 
    curl_setopt ($curlch, CURLOPT_TIMEOUT, 60); 
    curl_setopt ($curlch, CURLOPT_SSLVERSION, 3); 
    $res = curl_exec($curlch); 
    curl_close($curlch);  
  }
  else
  {
    reset ($_POST);
    $req = 'cmd=_notify-validate';
    foreach ($_POST as $key => $value)
    {
      $value = urlencode(stripslashes($value));
      $req .= "&$key=$value";
    }
    // post back to PayPal system to validate
    $header .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
    $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
    $header .= 'Content-Length: ' . strlen($req) . "\r\n\r\n";
    if ($UseSandbox==1)
      $fp = fsockopen ('ssl://www.sandbox.paypal.com', 80, $errno, $errstr, 30);
    else  
      $fp = fsockopen ('www.paypal.com', 80, $errno, $errstr, 30);      
    if ($fp)
    {
      fputs ($fp, $header . $req);
      while (!feof($fp))
      {
        $res = fgets ($fp, 1024);
      }
      fclose ($fp);
    }
  }
}

if (($IPNpassthru!="") && ($_REQUEST['notify_version']!="") && ($Test!="Y") && ($manualorder!=true) && ($calledasreturn!=1))
{
  $pos=strpos($IPNpassthru,"/",7);
  $host=substr($IPNpassthru,7,$pos-7);
  $path=substr($IPNpassthru,$pos);
  $req="";
  reset ($_POST);
  foreach ($_POST as $key => $value)
  {
    $value = urlencode(stripslashes($value));
    $req .= "&$key=$value";
  }
  $header  = "POST ".$path." HTTP/1.0\r\n";
  $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
  $header .= 'Content-Length: ' . strlen($req) . "\r\n\r\n";
  $fp = fsockopen ($host, 80, $errno, $errstr, 30);
  if ($fp)
  {
    fputs ($fp, $header . $req);
    fclose ($fp);
  }
}
// If used get IP address from buttons custom field
if (!$manualorder)
{
  $clientipaddr = "0.0.0.0";
  if (strlen($custom) > 10)
  {
    $k = GetVarFromString($custom, "I");
    if (ValidIP($k))
      $clientipaddr = $k;
  }
}
// Store order details in MySQL
if ($res == "VERIFIED")
{
  if ($DbTableNameOrders != "")
    storeordermysql();
}
// If order completed but transaction type is reversal then don't continue handling it
if (($payment_status == "Completed") && ($txn_type == "reversal"))
  exit;
if ($res != "VERIFIED")
{
  if ($manualorder == false)
    ShowMessage($ErrorTemplate, "Transaction not authenticated. payment_status=" . $payment_status . " pending_reason=" . $pending_reason . " result=" . $res, $ErrorEmail);
  exit;
}
if (($Clickatell_mode=="ALL") && ($calledasreturn==0) && ($manualorder!=true))
  ClickatellSMS();
  
// Handle delayed delivery because of eCheck or Payment Review
$copy_payment_status=$payment_status;
// See how to handle Payment Review 
if (($payment_status == "Pending") && ($pending_reason == "paymentreview") && ($DelayPaymentReview != "Y"))
  $copy_payment_status = "Completed";
if (($payment_status == "Pending") && ($pending_reason == "paymentreview") && ($DelayPaymentReview == "Y"))
{
  // Send notify or confirmation emails if required
  if ($calledasreturn==0)
  {
    // Send a notify email if required
    if (($NotifyTemplate!="") && ($NotifyEmail!=""))
    {
      if ($manualorder==false)
        SendNotifyEmail($NotifyTemplate, $NotifyEmail);
    }
    // Send a confirmation email if required
    if ($ConfirmTemplate!="")
    {
      if ($manualorder==false)
        SendNotifyEmail($ConfirmTemplate, $payer_email);
    }
      SendFraudEmail($ReviewTemplate, $ProdId, $ProdDesc, $ProdLink, $ProdSize, $ProdExp, $ProdFile, $ProdExtra, $notdownloadable);
      if (($Clickatell_mode=="PENDING") && ($calledasreturn==0))
        ClickatellSMS();              
      ShowMessage($ErrorTemplate, "Your download links will be emailed to you when your eCheck payment clears.","");
      exit;
  }
  else
  {
    DisplayFraudPage($ReviewTemplatePage, $ProdId, $ProdDesc, $ProdLink, $ProdSize, $ProdExp, $ProdFile, $ProdExtra, $notdownloadable);
    exit;    
  }
}
  
// See how we should handle eChecks.
// If instant download allowed and eCheck just cleared then don't process again.
// Txnid updated when original order processed
if (($DelayEchecks != "Y") && ($payment_type == "echeck") && ($payment_status == "Completed"))
  exit;
// See if instant download allowed
if (($payment_status == "Pending") && ($pending_reason == "echeck") && ($DelayEchecks != "Y"))
  $copy_payment_status = "Completed";
if (($payment_status == "Pending") && ($pending_reason == "echeck") && ($DelayEchecks == "Y"))
{
  // Send notify or confirmation emails if required
  if ($calledasreturn==0)
  {
    // Send a notify email if required
    if (($NotifyTemplate!="") && ($NotifyEmail!=""))
    {
      if ($manualorder==false)
        SendNotifyEmail($NotifyTemplate, $NotifyEmail);
    }
    // Send a confirmation email if required
    if ($ConfirmTemplate!="")
    {
      if ($manualorder==false)
        SendNotifyEmail($ConfirmTemplate, $payer_email);
    }
      SendFraudEmail($WarningTemplate, $ProdId, $ProdDesc, $ProdLink, $ProdSize, $ProdExp, $ProdFile, $ProdExtra, $notdownloadable);
      if (($Clickatell_mode=="PENDING") && ($calledasreturn==0))
        ClickatellSMS();              
      ShowMessage($ErrorTemplate, "Your download links will be emailed to you when your eCheck payment clears.","");
      exit;
  }
  else
  {
    DisplayFraudPage($WarningTemplatePage, $ProdId, $ProdDesc, $ProdLink, $ProdSize, $ProdExp, $ProdFile, $ProdExtra, $notdownloadable);
    exit;    
  }
}

$payment_status=$copy_payment_status;

if ($payment_status != "Completed")
{
  if ($manualorder == false)
    ShowMessage($ErrorTemplate, "Transaction not completed. payment_status=" . $payment_status . " pending_reason=" . $pending_reason . " result=" . $res, $ErrorEmail);
  exit;
}
// Send notify or confirmation emails and call AWeber, ARP as required.
if ($calledasreturn == 0)
{
  // Send a notify email if required
  if (($NotifyTemplate != "") && ($NotifyEmail != ""))
  {
    if ($manualorder == false)
      SendNotifyEmail($NotifyTemplate, $NotifyEmail);
  }
  // Send a confirmation email if required
  if ($ConfirmTemplate != "")
  {
    if ($manualorder == false)
      SendNotifyEmail($ConfirmTemplate, $payer_email);
  }
  // If global AWeber subscription enabled
  if (($AWeberList!="") && ($manualorder == false))
    llipn_AWeberSubscribe($AWeberList,""); 
  // If global ARP subscription enabled
  if (($arpID!="") && ($arpURL!="") && ($manualorder == false))
    llipn_ARPsubscribe($arpID,""); 
  // If global GetResponse subscription enabled
  if (($getresponsecampaign!="") && ($manualorder == false))
    llipn_GetResponseSubscribe($getresponsecampaign,""); 
}
/////////////////////////////////////////////////////////////////////////////////////////////////
// If referrer details known then email details to site and store in mysql table if used.
if ($calledasreturn==1)
{
  // Email order referer details
  if ($payer_business_name!="")
    $nametouse=$payer_business_name;
  else
    $nametouse=$first_name." ".$last_name;
  $cookiefound=false;
  $subject="Referer details for order ".$txn_id;
  $mailBody="Paypal Order number ".$txn_id." from ".$nametouse."\n\n";
  $mailBody.="Date of order: ".date("mdy")."\n";
  $mailBody.="Time of order: ".date("His")."\n";
  $mailBody.="IP address: ".$_SERVER['REMOTE_ADDR']."\n";
  $mailBody.="User agent: ".$_SERVER['HTTP_USER_AGENT']."\n";
  $mailBody.="Payer Status: ".$payer_status."\n";
  $mailBody.="Address Status: ".$address_status."\n";
  
  $reflinklokcookie=$_COOKIE['REFLINKLOK'];
  $sesreflinklokcookie=$_COOKIE['SESREFLINKLOK'];
  if ((isset($reflinklokcookie)) && ($reflinklokcookie!=""))
  {
    $cookiefound=true;
    $reflinklokvals=explode("|",$reflinklokcookie);
    $mailBody.="Date of first visit: ".$reflinklokvals[0]." ".$reflinklokvals[2]."\n";
    $mailBody.="Time of first visit: ".$reflinklokvals[1]." ".$reflinklokvals[2]."\n";
    $mailBody.="Entry page first visit: ".$reflinklokvals[3]."\n";
    $mailBody.="Referer first visit: ".$reflinklokvals[4]."\n";
  }
  if ((isset($sesreflinklokcookie)) && ($sesreflinklokcookie!=""))
  {
    $cookiefound=true;
    $sesreflinklokvals=explode("|",$sesreflinklokcookie);
    $mailBody.="Date of session start: ".$sesreflinklokvals[0]." ".$sesreflinklokvals[2]."\n";
    $mailBody.="Time of session start: ".$sesreflinklokvals[1]." ".$sesreflinklokvals[2]."\n";
    $mailBody.="Entry page this session: ".$sesreflinklokvals[3]."\n";
    $mailBody.="Referer this session: ".$sesreflinklokvals[4]."\n\n";
  }
  if ($cookiefound)
  {
	  SendEmailOut($SellerEmail,$SellerEmail,$SellerCompany,$subject,$mailBody,"N");
	  if ($DbTableNameOrders!="")
	  {
      $Query = "UPDATE " . $DbTableNameOrders . " SET referrerfirst=".quote_smart($reflinklokvals[4]).", entryfirst=".quote_smart($reflinklokvals[3]);
      $Query.=", referrernow=".quote_smart($sesreflinklokvals[4]).", entrynow=".quote_smart($sesreflinklokvals[3])." WHERE orderno=" . quote_smart($txn_id);
      $mysql_result = mysql_query($Query, $mysql_linklok);
	  }
  }
}
//////////////////////////////////////////////////////////////////////////////////////////////// 
// If using last order verification then check and update it
if ($Txnid != "")
{
  // Adjust txn_id depending on whether we were called by IPN or return page
  if ($calledasreturn == 0)
    $ourtxn_id = $txn_id . "-ipn";
  else
    $ourtxn_id = $txn_id . "-return";
  if (VerifyTransactionId($Txnid, $ourtxn_id) == 0)
  {
    ShowMessage($ErrorTemplate, "Order number ".$txn_id." has already been processed. Check your email for download links.", $ErrorEmail);
    exit; // Order already processed
  }
}
// Verify that receiver email is ours
if (strcasecmp($receiver_email, $PaypalEmail) != 0)
{
  ShowMessage($ErrorTemplate, "Paypal email address does not match.", $ErrorEmail);
  exit;
}
// See which currency transaction was in
$paycurr = $mc_currency;
$totalpaid = $mc_gross;
$affcurrmult = isset($exchange[$paycurr]) ? $exchange[$paycurr] : 1;
$totalaffiliate=round(($mc_gross-$tax) * $affcurrmult, 2);
$notdownloadable = 0;
$totalordered = 0; // Keep track of what should be paid
$nocurrency = false;
$minprod = 0;
$maxprod = 10;
if ($num_cart_items > 0)
{
  $maxprod = $num_cart_items + 1;
  $minprod = 1;
}
if ($manualorder == true)
{
  $maxprod = $num + 1;
  $minprod = 1;
}
for ($k = $minprod; $k < $maxprod; $k++)
{
  if ($k == 0)
  {
    if (isset($item_number))
    {
      if ($item_number != "")
      {
        $desc = $item_name;
        if ($for_auction == "true")
          $j = GetAuctionDetails($item_number, $paycurr, $cost, $desc, $link, $size, $exp, $extra);
        else
        {
          if ($UsePaypalOptions==true)
            $item_number.=$option_selection1.$option_selection2; 
          if ($Jalbum)
          {
            // Convert to filename if necessary
            $item_number=Jalbum($item_number);
          }                           
          $j = GetProductDetails($item_number, $paycurr, $cost, $desc, $link, $size, $exp, $extra);
        }
        if ($j == 0)
        {
          $notdownloadable++;
          continue;
        }
        if ((!isset($quantity)) || ($quantity == 0) || ($quantity == ""))
          $quantity = 1;
        if ($cost != -1)
          $totalordered = $totalordered + ($cost * $quantity);
        if (($cost == -1) && ($manualorder == false))
        {
          $nocurrency = true;
          continue;
        }
        $combos = explode(":", $link);
        if ((count($combos) == 1) || (substr(strtolower($link), 0, 5) == "http:") || (substr($link, 1, 2) == ":/"))
        {
          // Single file product
          if ($exp != 0)
          {
            if (strlen($exp) == 12)
            {
              $expiry = mktime(substr($exp, 8, 2), substr($exp, 10, 2), 0, substr($exp, 4, 2), substr($exp, 6, 2), substr($exp, 0, 4), -1);
              if (($calledasreturn==1) && (isset($payment_date)))              
                $exp = ($expiry - strtotime($payment_date)) / 60;
              else
                $exp = ($expiry - time()) / 60;                
            }
            else
            {
              if (($calledasreturn==1) && (isset($payment_date)))
                $expiry = strtotime($payment_date) + ($exp * 60);
              else
                $expiry = time() + ($exp * 60);
            }
          }
          else
            $expiry = 0;
          // See if ip locking required
          $ipleveltouse = $IPlock;
          if (($manualorder == true) && ($manualiplevel != "-1"))
            $ipleveltouse = $manualiplevel;
          else
          {
            $pos = strpos($extra, "IPLOCK=");
            if (is_integer($pos))
              $ipleveltouse = substr($extra, $pos + 7, 1);
          }
          // Handle multiple quantities of items. Useful if several serial numbers are required fo example
          $q = 1;
          $pos = strpos($extra, "MULTIPLE=");
          if (is_integer($pos))
            $q = $quantity;
          for ($i = 1;$i <= $q;$i++)
          {
            $ProdLink[] = GetSecureLink($link, $item_number, $expiry, $txn_id, $clientipaddr, $ipleveltouse,$payer_email);
            $ProdDesc[] = $desc;
            if ($link != "NA")
              $ProdFile[] = filename($link);
            else
              $ProdFile[] = "";
            $ProdSize[] = $size;
            $ProdExp[] = $exp;
            $ProdId[] = $item_number;
            $ProdExtra[] = $extra;
            if ($q==1)
              $ProdQty[] = $quantity;
            else
              $ProdQty[] = "1";                      
          }
        }
        else
        {
          // Multi file product (combo)
          // Loop round for each file
          $comboexpiry = $exp;
          for ($com = 0;$com < count($combos);$com++)
          {
            $j = GetProductDetails($combos[$com], $paycurr, $cost, $desc, $link, $size, $exp, $extra);
            if ($j == 0)
              continue;
            if ($comboexpiry != -1)
              $exp = $comboexpiry;
            if ($exp != 0)
            {
              if (strlen($exp) == 12)
              {
                $expiry = mktime(substr($exp, 8, 2), substr($exp, 10, 2), 0, substr($exp, 4, 2), substr($exp, 6, 2), substr($exp, 0, 4), -1);
                if (($calledasreturn==1) && (isset($payment_date)))              
                  $exp = ($expiry - strtotime($payment_date)) / 60;
                else
                  $exp = ($expiry - time()) / 60;
              }
              else
              {
                if (($calledasreturn==1) && (isset($payment_date)))
                  $expiry = strtotime($payment_date) + ($exp * 60);
                else              
                  $expiry = time() + ($exp * 60);
              }  
            }
            else
              $expiry = 0;
            // See if ip locking required
            $ipleveltouse = $IPlock;
            if (($manualorder == true) && ($manualiplevel != "-1"))
              $ipleveltouse = $manualiplevel;
            else
            {
              $pos = strpos($extra, "IPLOCK=");
              if (is_integer($pos))
                $ipleveltouse = substr($extra, $pos + 7, 1);
            }
            // Handle multiple quantities of items. Useful if several serial numbers are required fo example
            $q = 1;
            $pos = strpos($extra, "MULTIPLE=");
            if (is_integer($pos))
              $q = $quantity;
            for ($i = 1;$i <= $q;$i++)
            {
              $ProdLink[] = GetSecureLink($link, $combos[$com], $expiry, $txn_id, $clientipaddr, $ipleveltouse,$payer_email);
              $ProdDesc[] = $desc;
              if ($link != "NA")
                $ProdFile[] = filename($link);
              else
                $ProdFile[] = "";
              $ProdSize[] = $size;
              $ProdExp[] = $exp;
              $ProdId[] = $combos[$com];
              $ProdExtra[] = $extra;
              if ($q==1)
                $ProdQty[] = $quantity;
              else
                $ProdQty[] = "1";          
            }
          }
        }
      }
    }
  }
  else
  {
    $t = "item_number" . $k;
    if (isset($$t))
    {
      $pid = $$t;
      if ($pid != "")
      {
        $t = "quantity" . $k;
        $pqty = $$t;
        $t = "item_name" . $k;
        $desc = $$t;
        if ($for_auction == "true")
          $j = GetAuctionDetails($pid, $paycurr, $cost, $desc, $link, $size, $exp, $extra);
        else
        {
          if ($UsePaypalOptions==true)
          {
            $psel1="option_selection1_".$k;
            $psel2="option_selection2_".$k;
            $pid.=$$psel1.$$psel2;
          }
          if ($Jalbum)
          {
            // Convert to filename if necessary
            $pid=Jalbum($pid);
          }                   
          $j = GetProductDetails($pid, $paycurr, $cost, $desc, $link, $size, $exp, $extra);
        }
        if (($manualorder == true) && ($newexpiry != -1))
          $exp = $newexpiry;
        if ($j == 0)
        {
          $notdownloadable++;
          continue;
        }
        if ((!isset($pqty)) || ($pqty == 0) || ($pqty == ""))
          $pqty = 1;
        if ($cost != -1)
          $totalordered = $totalordered + ($cost * $pqty);
        if (($cost == -1) && ($manualorder == false))
        {
          $nocurrency = true;
          continue;
        }
        $combos = explode(":", $link);
        if ((count($combos) == 1) || (substr(strtolower($link), 0, 5) == "http:") || (substr($link, 1, 2) == ":/"))
        {
          // Single file product
          if ($exp != 0)
          {
            if (strlen($exp) == 12)
            {
              $expiry = mktime(substr($exp, 8, 2), substr($exp, 10, 2), 0, substr($exp, 4, 2), substr($exp, 6, 2), substr($exp, 0, 4), -1);
              if (($calledasreturn==1) && (isset($payment_date)))              
                $exp = ($expiry - strtotime($payment_date)) / 60;
              else
                $exp = ($expiry - time()) / 60;
            }
            else
            {
              if (($calledasreturn==1) && (isset($payment_date)))
                $expiry = strtotime($payment_date) + ($exp * 60);
              else            
                $expiry = time() + ($exp * 60);
            }
          }
          else
            $expiry = 0;
          // See if ip locking required
          $ipleveltouse = $IPlock;
          if (($manualorder == true) && ($manualiplevel != "-1"))
            $ipleveltouse = $manualiplevel;
          else
          {
            $pos = strpos($extra, "IPLOCK=");
            if (is_integer($pos))
              $ipleveltouse = substr($extra, $pos + 7, 1);
          }
          // Handle multiple quantities of items. Useful if several serial numbers are required fo example
          $q = 1;
          $pos = strpos($extra, "MULTIPLE=");
          if (is_integer($pos))
            $q = $pqty;
          for ($i = 1;$i <= $q;$i++)
          {
            $ProdLink[] = GetSecureLink($link, $pid, $expiry, $txn_id, $clientipaddr, $ipleveltouse, $payer_email);
            $ProdDesc[] = $desc;
            if ($link != "NA")
              $ProdFile[] = filename($link);
            else
              $ProdFile[] = "";
            $ProdSize[] = $size;
            $ProdExp[] = $exp;
            $ProdId[] = $pid;
            $ProdExtra[] = $extra;
            if ($q==1)
              $ProdQty[] = $pqty;
            else
              $ProdQty[] = "1";          
          }
        }
        else
        {
          // Multi file product (combo)
          // Loop round for each file
          $comboexpiry = $exp;
          for ($com = 0;$com < count($combos);$com++)
          {
            $j = GetProductDetails($combos[$com], $paycurr, $cost, $desc, $link, $size, $exp, $extra);
            if ($j == 0)
              continue;
            if ($comboexpiry != -1)
              $exp = $comboexpiry;
            if (($manualorder == true) && ($newexpiry != -1))
              $exp = $newexpiry;
            if ($exp != 0)
            {
              if (strlen($exp) == 12)
              {
                $expiry = mktime(substr($exp, 8, 2), substr($exp, 10, 2), 0, substr($exp, 4, 2), substr($exp, 6, 2), substr($exp, 0, 4), -1);
                if (($calledasreturn==1) && (isset($payment_date)))              
                  $exp = ($expiry - strtotime($payment_date)) / 60;
                else
                  $exp = ($expiry - time()) / 60;
              }
              else
              {
                if (($calledasreturn==1) && (isset($payment_date)))
                  $expiry = strtotime($payment_date) + ($exp * 60);
                else              
                  $expiry = time() + ($exp * 60);
              }  
            }
            else
              $expiry = 0;
            // See if ip locking required
            $ipleveltouse = $IPlock;
            if (($manualorder == true) && ($manualiplevel != "-1"))
              $ipleveltouse = $manualiplevel;
            else
            {
              $pos = strpos($extra, "IPLOCK=");
              if (is_integer($pos))
                $ipleveltouse = substr($extra, $pos + 7, 1);
            }
            // Handle multiple quantities of items. Useful if several serial numbers are required fo example
            $q = 1;
            $pos = strpos($extra, "MULTIPLE=");
            if (is_integer($pos))
              $q = $pqty;
            for ($i = 1;$i <= $q;$i++)
            {
              $ProdLink[] = GetSecureLink($link, $combos[$com], $expiry, $txn_id, $clientipaddr, $ipleveltouse, $payer_email);
              $ProdDesc[] = $desc;
              if ($link != "NA")
                $ProdFile[] = filename($link);
              else
                $ProdFile[] = "";
              $ProdSize[] = $size;
              $ProdExp[] = $exp;
              $ProdId[] = $combos[$com];
              $ProdExtra[] = $extra;
              if ($q==1)
                $ProdQty[] = $pqty;
              else
                $ProdQty[] = "1";          
            }
          }
        }
      }
    }
  }
}
// Check that attempt wasn't made to use a currency that is not accepted.
if ($manualorder == false)
{
  if ($nocurrency == true)
  {
    ShowMessage($ErrorTemplate, "Payment made with a currency that is not accepted.", $ErrorEmail);
    exit;
  }
}
// Check that amount paid at least covers the download files requested.
if ($manualorder == false)
{
  if (isset($discount))
  {
    if (($totalpaid+$discount) < ($totalordered * 0.995))
    {
      ShowMessage($ErrorTemplate, "Amount paid is not sufficient for products ordered.", $ErrorEmail);
      exit;
    }
  }
  else
  {
    if ($totalpaid < ($totalordered * 0.995))
    {
      ShowMessage($ErrorTemplate, "Amount paid is not sufficient for products ordered.", $ErrorEmail);
      exit;
    }
  }
}
if ($manualorder == false)
{
  if ((($clientipaddr == "") || ($clientipaddr == "0.0.0.0")) && ($RequireCustom == 1))
  {
    ShowMessage($ErrorTemplate, "Button appears to have been tampered with.", "");
    exit;
  }
}
// If required store Linklok handled items in database
if ($DbTableNameOrders != "")
  storeorderllprodsmysql($ProdId, $ProdDesc);
  
// If Sitelok Linklok Orders plugin used we should create order entry and user acount in Sitleok (might already exist anyway)
if (function_exists('linklokorders_createorder'))
{
  $orderorigin="Paypal";
  if ($manualorder)
    $orderorigin="Manual";
  if ($Test=="Y")
    $orderorigin="manual";    
  $usernametouse = $payer_email;
  if ($custom!="")
  {
    $temp = GetVarFromString($custom, "U");
    if ($temp!="")
      $usernametouse = $temp;
  }
  if (!$SitelokUseNameOnly)
  {
    if ($payer_business_name!="")
 	    $nametouse=$payer_business_name;
    else
      $nametouse=$first_name . " " . $last_name;
  }
  else
  {
    $nametouse=$first_name . " " . $last_name;
  }
  // Handle any custom field variables
  for ($k=1;$k<=50;$k++)
  {
    $cu[$k]=$$SitelokCustom[$k];
  }
  $slgroups=array();
  $slexpiries=array();
  $slgroups[0]="ORDERACCESS";
  $slexpiries[0]="0";  
  $pass="";
  slapi_addorupdate($usernametouse,$pass,$payer_email,$nametouse,$slgroups,$slexpires,"","","","",0
  ,$cu[1],$cu[2],$cu[3],$cu[4],$cu[5],$cu[6],$cu[7],$cu[8],$cu[9],$cu[10]
  ,$cu[11],$cu[12],$cu[13],$cu[14],$cu[15],$cu[16],$cu[17],$cu[18],$cu[19],$cu[20]
  ,$cu[21],$cu[22],$cu[23],$cu[24],$cu[25],$cu[26],$cu[27],$cu[28],$cu[29],$cu[30]
  ,$cu[31],$cu[32],$cu[33],$cu[34],$cu[35],$cu[36],$cu[37],$cu[38],$cu[39],$cu[40]
  ,$cu[41],$cu[42],$cu[43],$cu[44],$cu[45],$cu[46],$cu[47],$cu[48],$cu[49],$cu[50]);
  linklokorders_createorder($txn_id,time(),$orderorigin,$usernametouse);
  // If not called as return page then send caller data to table
  if ($calledasreturn!=1)
  {
    $callerdata=array();
    if ((!empty($_POST)))
    {
      reset($_POST);    
      if (llipn_get_magic_quotes_gpc())
      {
        while (list($name, $value) = each($_POST))
          $callerdata[$name]=stripslashes($value);
      }
      else
      {
        while (list($name, $value) = each($_POST))
          $callerdata[$name]=$value;
      }
      linklokorders_addcallerdata($txn_id,$callerdata);
    }
  }
  // Store download URL order email, Linklok Key
  $orderdata=array();
  $orderdata['orderemail']=$payer_email;
  $orderdata['downloadurl']=$LinklokURL;
  $orderdata['linkkey']=$LinkKey;
  // Store referrer data if known
  if (($cookiefound) && ($calledasreturn==1))
  {
    $orderdata['referrerfirst']=$reflinklokvals[4];
    $orderdata['entryfirst']=$reflinklokvals[3];
    $orderdata['referrernow']=$sesreflinklokvals[4];
    $orderdata['entrynow']=$sesreflinklokvals[3];
  }  
  linklokorders_addorderdata($txn_id,$orderdata);
  // If mysql being used reconnect as Sitelok may have closed connection
  if (($DbHostLinklok!="") && ($DbUserLinklok!="") && ($DbPasswordLinklok!="") && ($DbNameLinklok!=""))
  {
    $mysql_linklok=mysql_connect($DbHostLinklok,$DbUserLinklok,$DbPasswordLinklok);
    if ($mysql_linklok==0)
    {
      ShowMessage($ErrorTemplate, "Error connecting to MySQL", $ErrorEmail);
      exit;
    }
    $db=mysql_select_db($DbNameLinklok,$mysql_linklok);
    if ($db==False)
    {
      mysql_close($mysql_linklok);
      ShowMessage($ErrorTemplate, "Error connecting to the database", $ErrorEmail);
      exit;
    }
  }            
}      
  
      
// Here we should decide whether we were called by IPN or as return page
if ($calledasreturn == 0)
{
  // Send either custom or predefined download links email
  if ((count($ProdId) > 0) || ($AlwaysEmail==true))
  {
    // See if product specific email template defined
    $newtemplate = "";
    $sendsms=false;
    $tempcount = 0;  
    for ($k = 0;$k < count($ProdId);$k++)
    {
      $extrasettings = explode("*", $ProdExtra[$k]);
      for ($j = 0;$j < count($extrasettings);$j++)
      {
        $command = explode("=", $extrasettings[$j],2);
        if ((strtoupper($command[0]) == "SMS") && (strtoupper($command[1])=="YES"))
          $sendsms=true;
        if (strtoupper($command[0]) == "AWEBER")
        {
          $aweberlists[]=$command[1];
          $aweberprods[]=(string)$k;
        }  
        if (strtoupper($command[0]) == "ARP")
        {
          $arplists[]=$command[1];
          $arpprods[]=(string)$k;
        }  
        if (strtoupper($command[0]) == "GETRESPONSE")
        {
          $getresponselists[]=$command[1];
          $getresponseprods[]=(string)$k;
        }  
        if (strtoupper($command[0]) == "EMAILTEMP")
        {
          if ($k == 0)
          {
            $newtemplate = $command[1];
            $tempcount++;
          }
          else
          {
            if (($command[1] != "") && ($newtemplate == $command[1]))
              $tempcount++;
          }
          continue;
        }
        if (strtoupper($command[0]) == "PRODUCTNOTIFYEMAIL")
        {
          $productnotifyemails[]=$command[1];
        }                
      }
    }
    // See if any product email notifiations need to be made.
    if (count($productnotifyemails)>0)
    {
      $productnotifyemails=array_unique($productnotifyemails);
      if ($ProductNotifyTemplate != "")
      {
        for ($k=0;$k<count($productnotifyemails);$k++)
        {
          SendNotifyEmail($ProductNotifyTemplate, $productnotifyemails[$k]);
        }
      }      
    }
    if (($Clickatell_mode=="PRODUCT") && ($sendsms==true) && ($calledasreturn==0) && ($manualorder!=true))
      ClickatellSMS();   
    if (($tempcount == count($ProdId)) && ($tempcount > 0))
      $EmailTemplate = $newtemplate;
    SendEmail($EmailTemplate, $ProdId, $ProdDesc, $ProdLink, $ProdSize, $ProdExp, $ProdFile, $ProdExtra, $notdownloadable);
    // See if AWeber needs to be handled
    if ((count($aweberlists)>0) && ($manualorder == false))
    {
      $awebersent[]=" "; // init array
      for ($i=0;$i<count($aweberlists);$i++)
      {
        // Make sure we only call each list once
        if (!is_integer(array_search($aweberlists[$i],$awebersent)))
        {
          llipn_AWeberSubscribe($aweberlists[$i],$aweberprods[$i]);
          $awebersent[]=$aweberlists[$i];         
        }
      }  
    }
    // See if ARP needs to be handled
    if ((count($arplists)>0) && ($manualorder == false))
    {
      $arpsent[]=" "; // init array
      for ($i=0;$i<count($arplists);$i++)
      {
        // Make sure we only call each list once
        if (!is_integer(array_search($arplists[$i],$arpsent)))
        {
          llipn_ARPSubscribe($arplists[$i],$arpprods[$i]);
          $arpsent[]=$arplists[$i];         
        }
      }  
    }
    // See if GetResponse needs to be handled
    if ((count($getresponselists)>0) && ($manualorder == false))
    {
      $getresponsesent[]=" "; // init array
      for ($i=0;$i<count($getresponselists);$i++)
      {
        // Make sure we only call each list once
        if (!is_integer(array_search($getresponselists[$i],$getresponsesent)))
        {
          llipn_GetResponseSubscribe($getresponselists[$i],$getresponseprods[$i]);
          $getresponsesent[]=$getresponselists[$i];         
        }
      }  
    }        
  }
  if ($manualorder == true)
  {
    print "Download links have been emailed to $payer_email";
  }  
  else
  {
    if ($Test == "Y")
    {
     if ((count($ProdId) > 0) || ($AlwaysEmail==true))
       print "Linklok IPN setup seems to be OK. You should receive the download link by email.";
     else
       print "No email was sent because product ordered was not recognised by Linklok. Check the product id used.";
    }  
  }
  if (($manualorder==false) && ($Test != "Y"))
  {
    // See if any products should be sent by Kunaki
    if (($kunaki_username!="") && ($kunaki_password!=""))
    {
      $kproducts=array();
      $kquantities=array();
      for ($k = 0;$k < count($ProdId);$k++)
      {
        $extrasettings = explode("*", $ProdExtra[$k]);
        for ($j = 0;$j < count($extrasettings);$j++)
        {
          $command = explode("=", $extrasettings[$j],2);
          if (strtoupper($command[0]) == "KUNAKI_ID")
          {
            $key=array_search($command[1],$kproducts);
            if (is_integer($key))
            {
              $kquantities[$key]=$kquantities[$key]+$ProdQty[$k];
            }
            else
            {
              $kproducts[]=$command[1];
              $kquantities[]=$ProdQty[$k];
            }
            break;
          }
        }
      }       
      if (count($kproducts)>0)
      {
        $res=sendKunaki($kunaki_username,$kunaki_password,$kunaki_mode,$kunaki_shippingmethod,$address_name,$payer_business_name,$address_street,"",$address_city,$address_state,$address_zip,$address_country_code,$kproducts,$kquantities);
      }
      if ($res['errorcode']!=0)
      {
        // Kunaki rejected order so send email
        $subject = "Linklok warning - Kunaki rejected order $id";
        $mailBody = "The order number $id for $inv_name has been rejected by Kunaki.\n\n";
        $mailBody.= "Kunaki returned errorcode ".$res['errorcode']." - ".$res['errortext'];
        if ($kunaki_email!="")
          SendEmailOut($kunaki_email,$kunaki_email,$SellerCompany,$subject,$mailBody,"N");
        else
          SendEmailOut($SellerEmail,$SellerEmail,$SellerCompany,$subject,$mailBody,"N");      
      }
    }
  }
}
else
{
  // See if product specific download template defined
  $newtemplate = "";
  $tempcount = 0;
  for ($k = 0;$k < count($ProdId);$k++)
  {
    $extrasettings = explode("*", $ProdExtra[$k]);
    for ($j = 0;$j < count($extrasettings);$j++)
    {
      $command = explode("=", $extrasettings[$j],2);
      if (strtoupper($command[0]) == "DOWNTEMP")
      {
        if ($k == 0)
        {
          $newtemplate = $command[1];
          $tempcount++;
        }
        else
        {
          if (($command[1] != "") && ($newtemplate == $command[1]))
            $tempcount++;
        }
        continue;
      }
    }
  }
  if (($tempcount == count($ProdId)) && ($tempcount > 0))
    $DownloadTemplate = $newtemplate;
  // Display either predefined download page or custom one
  DisplayPage($DownloadTemplate, $ProdId, $ProdDesc, $ProdLink, $ProdSize, $ProdExp, $ProdFile, $ProdExtra, $notdownloadable);
}

function GetSecureLink($link, $id, $expiry, $ordnum, $ip = "0.0.0.0", $ipl=0,$email="")
{
  global $NoFilename, $LinklokURL, $LinkKey;
  if ($link != "NA")
  {
    $dfile = filename($link);
    $verifyhash = md5($LinkKey . $id . $expiry . $ordnum . $ip . $ipl . urlencode($email));
    $auth = $id . "," . $expiry . "," . $ordnum . "," . $ip . "," . $ipl. "," . urlencode($email) . "," . $verifyhash;
    $auth = base64_encode($auth);
    $auth = rawurlencode($auth);
    if ($NoFilename == 1)
      $plink = "$LinklokURL?llauth=$auth";
    else
      $plink = "$LinklokURL?llauth=$auth/$dfile";
  }
  else
    $plink = "";
  return ($plink);
}

function SendEmail($Template, $ProdId, $ProdDesc, $ProdLink, $ProdSize, $ProdExp, $ProdFile, $ProdExtra, $nd)
{
  global $SellerCompany, $SellerEmail, $SellerURL, $HTMLEmail, $manualorder;
  if ($Template == "NONE")
    return;
  if ($Template!="")
  {  
    $ext = fileextension($Template);
    if ($ext == ".php")
    {
      ob_start();
      include $Template;
      $mailBody = ob_get_contents(); 
      ob_end_clean();    
    }
    else
    {
      if (!($fh = @fopen($Template, "r")))
        return(0);
      $mailBody = fread ($fh, 200000);
      fclose($fh);
    }
  }
  else
  {
    // Predefined html template
    if ($HTMLEmail=="Y")
    {
      $ext=".html";
      $mailBody="";
      $mailBody.="<html>\n";
      $mailBody.="<head>\n";
      $mailBody.="<title>Your order from ".$SellerCompany."</title>\n";
      $mailBody.="</head>\n";
      $mailBody.="<body>\n";
      if ($manualorder==false)
        $mailBody.="Thank you for your order. For reference your order number is !!!order_number!!!.<br>\n";
      else
        $mailBody.="Thank you for your order.<br>\n";        
      $mailBody.="<!--knownstart-->\n";
      $mailBody.="<br>\n";
      $mailBody.="Please use the links below to download your purchase.<br>\n";
      $mailBody.="<br>\n";
      $mailBody.="<!--eachproductstart-->\n";
      $mailBody.="!!!product!!!<br>\n";
      $mailBody.="<br>\n";
      $mailBody.="<!--eachproductend-->\n";
      $mailBody.="<!--knownend-->\n";
      $mailBody.="<!--unknownstart-->\n";
      $mailBody.="<br>\n";
      $mailBody.="You have ordered items that are not downloadable. Your order will be processed as quickly as possible.<br>\n";
      $mailBody.="<!--unknownend-->\n";
      $mailBody.="Your temporary login information is below. For security purposes, you will be required to create a new username and password on initial launch:<br>Username: u2119518R<br>Password: 5e7L5P13e19<br><br> If you have any questions regarding this order please contact us.<br>\n";
      $mailBody.="<br>\n";
      $mailBody.=$SellerCompany."<br>\n";
      $mailBody.="<a href=\"".$SellerURL."\">".$SellerURL."</a><br>\n";    
      $mailBody.="<a href=\"mailto:".$SellerEmail."\">".$SellerEmail."</a><br>\n";    
      $mailBody.="</body>\n";
      $mailBody.="</html>\n";
    }
    else
    {
      $ext=".txt";
      $mailBody="";
      $mailBody.="Your order from ".$SellerCompany."\n";
      if ($manualorder==false)
        $mailBody.="Thank you for your order. For reference your order number is !!!order_number!!!.\n";
      else   
        $mailBody.="Thank you for your order.\n";
      $mailBody.="<!--knownstart-->Please use the links below to download your purchase.\n";
      $mailBody.="\n";
      $mailBody.="<!--eachproductstart-->!!!product!!!\n";
      $mailBody.="<!--eachproductend--><!--knownend-->\n";
      $mailBody.="\n";
      $mailBody.="<!--unknownstart-->You have ordered items that are not downloadable but will be processed as quickly as possible.\n";
      $mailBody.="<!--unknownend-->\n";
      $mailBody.="Your temporary login information is below. For security purposes, you will be required to create a new username and password on initial launch:<br>Username: u2119518R<br>Password: 5e7L5P13e19<br><br> If you have any questions regarding this order please contact us.<br>\n";
      $mailBody.="\n";
      $mailBody.=$SellerCompany."\n";
      $mailBody.=$SellerURL."\n";
      $mailBody.=$SellerEmail."\n";   
    }
  }
  EmailTemplateProcess($mailBody, $ProdId, $ProdDesc, $ProdLink, $ProdSize, $ProdExp, $ProdFile, $ProdExtra, $nd, $ext);  
}

function SendFraudEmail($Template, $ProdId, $ProdDesc, $ProdLink, $ProdSize, $ProdExp, $ProdFile, $ProdExtra, $nd)
{
  global $SellerCompany, $SellerEmail, $SellerURL, $HTMLEmail, $manualorder;
  if ($Template!="")
  {  
    $ext = fileextension($Template);
    if ($ext == ".php")
    {
      ob_start();
      include $Template;
      $mailBody = ob_get_contents(); 
      ob_end_clean();    
    }
    else
    {
      if (!($fh = @fopen($Template, "r")))
        return(0);
      $mailBody = fread ($fh, 200000);
      fclose($fh);
    }
  }
  else
  {
    // Predefined html template
    if ($HTMLEmail=="Y")
    {
      $ext=".html";
      $mailBody="";
      $mailBody.="<html>\n";
      $mailBody.="<head>\n";
      $mailBody.="<title>Your order from ".$SellerCompany."</title>\n";
      $mailBody.="</head>\n";
      $mailBody.="<body>\n";
      if ($manualorder==false)
        $mailBody.="Thank you for your order. For reference your order number is !!!order_number!!!.<br>\n";
      else
        $mailBody.="Thank you for your order.<br><br>\n";        
      $mailBody.="Your order will be completed when payment has cleared in our Paypal account.<br>\n";
      $mailBody.="<br>\n";
      $mailBody.="Your temporary login information is below. For security purposes, you will be required to create a new username and password on initial launch:<br>Username: u2119518R<br>Password: 5e7L5P13e19<br><br> If you have any questions regarding this order please contact us.<br>\n";
      $mailBody.="<br>\n";
      $mailBody.=$SellerCompany."<br>\n";
      $mailBody.="<a href=\"".$SellerURL."\">".$SellerURL."</a><br>\n";    
      $mailBody.="<a href=\"mailto:".$SellerEmail."\">".$SellerEmail."</a><br>\n";    
      $mailBody.="</body>\n";
      $mailBody.="</html>\n";
    }
    else
    {
      $ext=".txt";
      $mailBody="";
      $mailBody.="Your order from ".$SellerCompany."\n";
      if ($manualorder==false)
        $mailBody.="Thank you for your order. For reference your order number is !!!order_number!!!.\n";
      else   
        $mailBody.="Thank you for your order.\n\n";
      $mailBody.="Your order will be completed when payment has cleared in our Paypal account.\n";
      $mailBody.="\n";
      $mailBody.="Your temporary login information is below. For security purposes, you will be required to create a new username and password on initial launch:<br>Username: u2119518R<br>Password: 5e7L5P13e19<br><br> If you have any questions regarding this order please contact us.<br>\n";
      $mailBody.="\n";
      $mailBody.=$SellerCompany."\n";
      $mailBody.=$SellerURL."\n";
      $mailBody.=$SellerEmail."\n";   
    }
  }
  EmailTemplateProcess($mailBody, $ProdId, $ProdDesc, $ProdLink, $ProdSize, $ProdExp, $ProdFile, $ProdExtra, $nd, $ext);  
}

function EmailTemplateProcess($mailBody, $ProdId, $ProdDesc, $ProdLink, $ProdSize, $ProdExp, $ProdFile, $ProdExtra, $nd, $ext)
{
  global $SellerCompany, $SellerEmail, $SellerURL  ,$payer_email, $CopyEmail, $EmailHeaderNoSlashR, $UnknownEsegment, $HTMLEmail, $ExtraMailParam;
  global $DownloadBackground;
  // Insert !!!unknownsegment!!! variable)
  InsertUsegment($mailBody, $UnknownEsegment, $nd);
  // Replace all non product related variables
  InsertVariables($mailBody);
  // Insert poduct variables
  InsertProductVariables($mailBody, $ProdDesc, $ProdLink, $ProdSize, $ProdExp, $ProdFile, $ext);
  // Insert item loop variables
  InsertItemLoopVariables($mailBody);
  // Insert variables and repeat block for !!!eachproductstart!!!
  InsertLoopVariables($mailBody, $ProdId, $ProdDesc, $ProdLink, $ProdSize, $ProdExp, $ProdFile, $ProdExtra, "ESEGMENT", $ext);
  // Handle any <!--unknownstart--> <!--unknownend--> section
  InsertUnknownSection($mailBody, $nd);
  // Handle any <!--knownstart--> <!--knownend--> section
  InsertKnownSection($mailBody,count($ProdDesc));
  // If using a download background page replace auth with authe
  if ($DownloadBackground!="")
    $mailBody = str_replace("?llauth=", "?authe=", $mailBody);        
  // Get subject for email
  if ($ext == ".txt")
  {
    $pos = strpos($mailBody, "\n");
    $subject = substr($mailBody, 0, $pos);
    $mailBody = substr($mailBody, $pos + 1, strlen($mailBody) - $pos-1);
  }
  else
  {
    $subject = "Download Links";
    $pos = strpos($mailBody, "<TITLE>");
    if (!is_integer($pos))
      $pos = strpos($mailBody, "<title>");
    $pos2 = strpos($mailBody, "</TITLE>");
    if (!is_integer($pos2))
      $pos2 = strpos($mailBody, "</title>");
    if ((is_integer($pos)) && (is_integer($pos2)))
    {
      $subject = substr($mailBody, $pos + 7, $pos2 - $pos-7);
    }
  }
  if (($HTMLEmail == "Y") && ($ext == ".txt"))
  {
    $mailBody = str_replace("\n", "<br>\n", $mailBody);
    $mailBody = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD W3 HTML//EN\">\n<HTML>\n<HEAD>\n<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=iso-8859-1\">\n<TITLE>Download Information</TITLE>\n</HEAD>\n<BODY>\n" . $mailBody;
    $mailBody .= "</BODY>\n</HTML>\n";
  }
  if (($HTMLEmail == "Y") && ($ext != ".txt"))
    $mailBody = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD W3 HTML//EN\">\n" . $mailBody;
  SendEmailOut($payer_email, $SellerEmail, $SellerCompany, $subject, $mailBody, $HTMLEmail);
  if ($CopyEmail != "")
  {
    $subject = "Copy of Linklok Paypal email sent to $payer_email";
    SendEmailOut($CopyEmail, $SellerEmail, $SellerCompany, $subject, $mailBody, $HTMLEmail);
  }
}

function DisplayPage($Template, $ProdId, $ProdDesc, $ProdLink, $ProdSize, $ProdExp, $ProdFile, $ProdExtra, $nd)
{
  global $manualorder, $Affiliate, $Shareasale, $Idevaffiliate, $totalaffiliate, $Jamaffiliate; 
  global $SellerCompany, $SellerEmail, $SellerURL, $HTMLEmail;
  if ($Template!="")
  {
    $ext = fileextension($Template);  
    if ($ext == ".php")
    {
      ob_start();
      include $Template;
      $page = ob_get_contents(); 
      ob_end_clean(); 
    }
    else
    {
      if (!($fh = @fopen($Template, "r")))
        return(0);
      $page = fread ($fh, 200000);
      fclose($fh);
    }
  }
  else
  {
    $page="";
    $page.="<html>\n";
    $page.="<head>\n";
    $page.="<title>Thank you for your order</title>\n";
    $page.="</head>\n";
    $page.="<body>\n";
    $page.="Thank you for your order. Your transaction has been completed, and a receipt for your<br>\npurchase has been emailed to you from Paypal. For reference your order number is !!!order_number!!!.<br>\n";
    $page.="<!--knownstart-->\n";
    $page.="<br>\n";
    $page.="Please use the links below to download your purchase.<br>\n";
    $page.="<br>\n";
    $page.="<!--eachproductstart-->\n";
    $page.="!!!product!!!<br>\n";
    $page.="<br>\n";
    $page.="<!--eachproductend-->\n";
    $page.="These links have also been emailed to you at !!!email!!!.<br>\n";
    $page.="<!--knownend-->\n";
    $page.="<!--unknownstart-->\n";
    $page.="<br>\n";
    $page.="You have ordered items that are not downloadable. Your order will be processed as quickly as possible.<br>\n";
    $page.="<!--unknownend-->\n";
    $page.="<br>\n";
    $page.="Your temporary login information is below. For security purposes, you will be required to create a new username and password on initial launch:<br>Username: u2119518R<br>Password: 5e7L5P13e19<br><br> If you have any questions regarding this order please contact us.<br>\n";
    $page.="<br>\n";
    $page.=$SellerCompany."<br>\n";
    $page.="<a href=\"".$SellerURL."\">".$SellerURL."</a><br>\n";    
    $page.="<a href=\"mailto:".$SellerEmail."\">".$SellerEmail."</a><br>\n";
    // Now output affiliate code if not manual order
    if ($manualorder == false)
    {
      if ($Affiliate != "")
      {
        $page.= "!!!affiliate!!!\n";
      }
      if ($Shareasale != "")
      {
        $page.= "!!!shareasale!!!\n";
      }
      if ($Idevaffiliate != "")
      {
        $page.= "!!!idevaffiliate!!!\n";
      }
      if ($Jamaffiliate != "")
      {
        $page.= "!!!jamaffiliate!!!\n";
      }
    }        
    $page.="</body>\n";
    $page.="</html>\n";
  }
  PageTemplateProcess($page, $ProdId, $ProdDesc, $ProdLink, $ProdSize, $ProdExp, $ProdFile, $ProdExtra, $nd);  
}

function DisplayFraudPage($Template, $ProdId, $ProdDesc, $ProdLink, $ProdSize, $ProdExp, $ProdFile, $ProdExtra, $nd)
{
  global $manualorder, $Affiliate, $Shareasale, $Idevaffiliate, $totalaffiliate, $Jamaffiliate; 
  global $SellerCompany, $SellerEmail, $SellerURL, $HTMLEmail;
  if ($Template!="")
  {
    $ext = fileextension($Template);  
    if ($ext == ".php")
    {
      ob_start();
      include $Template;
      $page = ob_get_contents(); 
      ob_end_clean();    
    }
    else
    {
      if (!($fh = @fopen($Template, "r")))
        return(0);
      $page = fread ($fh, 200000);
      fclose($fh);
    }
  }
  else
  {
    $page="";
    $page.="<html>\n";
    $page.="<head>\n";
    $page.="<title>Thank you for your order</title>\n";
    $page.="</head>\n";
    $page.="<body>\n";
    $page.="Thank you for your order. For reference your order number is !!!order_number!!!.<br>\n";
    $page.="<br>\n";
    $page.="<!--knownstart-->\n";
    $page.="Your order will be completed when payment clears in our Paypal account.<br>\n";
    $page.="<br>\n";    
    $page.="<!--knownend-->\n";    
    $page.="<!--unknownstart-->\n";
    $page.="You have ordered items that are not downloadable. Your order will be processed as quickly as possible.<br>\n";
    $page.="<br>\n";    
    $page.="<!--unknownend-->\n";
    $page.="Your temporary login information is below. For security purposes, you will be required to create a new username and password on initial launch:<br>Username: u2119518R<br>Password: 5e7L5P13e19<br><br> If you have any questions regarding this order please contact us.<br>\n";
    $page.="<br>\n";
    $page.=$SellerCompany."<br>\n";
    $page.="<a href=\"".$SellerURL."\">".$SellerURL."</a><br>\n";    
    $page.="<a href=\"mailto:".$SellerEmail."\">".$SellerEmail."</a><br>\n";
    // Now output affiliate code if not manual order
    if ($manualorder == false)
    {
      if ($Affiliate != "")
      {
        $page.= "!!!affiliate!!!\n";
      }
      if ($Shareasale != "")
      {
        $page.= "!!!shareasale!!!\n";
      }
      if ($Idevaffiliate != "")
      {
        $page.= "!!!idevaffiliate!!!\n";
      }
      if ($Jamaffiliate != "")
      {
        $page.= "!!!jamaffiliate!!!\n";
      }
    }        
    $page.="</body>\n";
    $page.="</html>\n";
  }
  PageTemplateProcess($page, $ProdId, $ProdDesc, $ProdLink, $ProdSize, $ProdExp, $ProdFile, $ProdExtra, $nd);  
}

function PageTemplateProcess($page, $ProdId, $ProdDesc, $ProdLink, $ProdSize, $ProdExp, $ProdFile, $ProdExtra, $nd)
{
  global $UnknownDsegment, $Affiliate;
  // Create Affiliate string if required
  if ($Affiliate != "")
    InsertVariables($Affiliate);
  // Insert !!!unknownsegment!!! variable)
  InsertUsegment($page, $UnknownDsegment, $nd);
  // Replace all non product related variables
  InsertVariables($page);
  // Insert poduct variables
  InsertProductVariables($page, $ProdDesc, $ProdLink, $ProdSize, $ProdExp, $ProdFile, ".html");
  // Insert item loop variables
  InsertItemLoopVariables($page);
  // Insert variables and repeat block for !!!eachproductstart!!!
  InsertLoopVariables($page, $ProdId, $ProdDesc, $ProdLink, $ProdSize, $ProdExp, $ProdFile, $ProdExtra, "DSEGMENT", ".html");
  // Handle any <!--unknownstart--> <!--unknownend--> section
  InsertUnknownSection($page, $nd);
  // Handle any <!--knownstart--> <!--knownend--> section
  InsertKnownSection($page,count($ProdDesc));  
  print $page;
}

function SendNotifyEmail($Template, $Emailadr)
{
  global $EmailHeaderNoSlashR, $SellerCompany, $SellerEmail, $ExtraMailParam;
  if (($Template == "") || ($Emailadr == ""))
    return;
  $ext = fileextension($Template);
  if ($ext == ".php")
  {
    ob_start();
    include $Template;
    $mailBody = ob_get_contents(); 
    ob_end_clean();    
  }
  else
  {
    if (!($fh = @fopen($Template, "r")))
      return;
    $mailBody = fread ($fh, 200000);
    fclose($fh);
  }      
  // Replace all non product related variables
  InsertVariables($mailBody);
  // Insert item loop variables
  InsertItemLoopVariables($mailBody);
  // Get subject for email
  if ($ext == ".txt")
  {
    $pos = strpos($mailBody, "\n");
    $subject = substr($mailBody, 0, $pos);
    $mailBody = substr($mailBody, $pos + 1, strlen($mailBody) - $pos-1);
  }
  else
  {
    $subject = "Order Notification";
    $pos = strpos($mailBody, "<TITLE>");
    if (!is_integer($pos))
      $pos = strpos($mailBody, "<title>");
    $pos2 = strpos($mailBody, "</TITLE>");
    if (!is_integer($pos2))
      $pos2 = strpos($mailBody, "</title>");
    if ((is_integer($pos)) && (is_integer($pos2)))
    {
      $subject = substr($mailBody, $pos + 7, $pos2 - $pos-7);
    }
  }
  if ($ext != ".txt")
    $mailBody = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD W3 HTML//EN\">\n" . $mailBody;
  if ($ext != ".txt")
    SendEmailOut($Emailadr, $SellerEmail, $SellerCompany, $subject, $mailBody, "Y");
  else
    SendEmailOut($Emailadr, $SellerEmail, $SellerCompany, $subject, $mailBody, "N");
  // Send copy of email if being sent to client
  global $CopyEmail,$NotifyEmail;
  if (($CopyEmail!="") && ($Emailadr!=$CopyEmail) && ($Emailadr!=$NotifyEmail))
  {
    if ($ext != ".txt")
      SendEmailOut($CopyEmail, $SellerEmail, $SellerCompany, $subject, $mailBody, "Y");
    else
      SendEmailOut($CopyEmail, $SellerEmail, $SellerCompany, $subject, $mailBody, "N");
  } 
}

function InsertVariables(&$buf)
{
  global $payer_email, $invoice, $memo, $mc_gross, $txn_id, $first_name, $last_name, $payer_email, $payer_id;
  global $address_name, $address_street, $address_city, $address_state, $address_zip, $address_country, $option_name1, $option_selection1, $mc_currency;
  global $option_name2, $option_selection2, $custom, $tax, $num_cart_items, $mc_fee, $payer_business_name, $payment_date, $address_country_code, $clientipaddr;
  global $manualorder, $Affiliate, $Shareasale, $Idevaffiliate, $totalaffiliate, $payer_status, $address_status, $auction_buyer_id, $Jamaffiliate;
  global $mc_shipping, $shipping_method;
  // Now output affiliate code if not manual order
  if (($manualorder == false) && ($mc_gross > 0.00))
  {
    if ($Affiliate != "")
    {
      $buf = str_replace("!!!affiliate!!!", $Affiliate, $buf);
    }
    if ($Shareasale != "")
    {
      $buf = str_replace("!!!shareasale!!!", "<img src=\"https://shareasale.com/sale.cfm?amount=" . $totalaffiliate . "&tracking=" . $txn_id . "&transtype=sale&merchantID=" . $Shareasale . "\" width=1 height=1>", $buf);
    }
    if ($Idevaffiliate != "")
    {
      $buf = str_replace("!!!idevaffiliate!!!", "<img border=\"0\" src=\"" . $Idevaffiliate . "?idev_linklok_1=" . $totalaffiliate . "&idev_linklok_2=" . $txn_id . "\" width=\"1\" height=\"1\">", $buf);
    }
    if ($Jamaffiliate != "")
    {
      $buf = str_replace("!!!jamaffiliate!!!", "<img border=\"0\" src=\"" . $Jamaffiliate . "?amount=" . $totalaffiliate . "&trans_id=" . $txn_id . "\" width=\"1\" height=\"1\">", $buf);
    }
  }
  else
  {
    $buf = str_replace("!!!affiliate!!!", "", $buf);
    $buf = str_replace("!!!shareasale!!!", "", $buf);
    $buf = str_replace("!!!idevaffiliate!!!", "", $buf);
    $buf = str_replace("!!!jamaffiliate!!!", "", $buf);
  }
  // Remove slashes from data
  if (llipn_get_magic_quotes_gpc())
  {
    $memo=stripslashes($memo);          
    $first_name=stripslashes($first_name);          
    $last_name=stripslashes($last_name);          
    $address_name=stripslashes($address_name);          
    $payer_business_name=stripslashes($payer_business_name);          
    $address_street=stripslashes($address_street);          
    $address_city=stripslashes($address_city);          
    $address_state=stripslashes($address_state);          
    $address_country=stripslashes($address_country);          
  }
  $buf = str_replace("!!!invoice!!!", $invoice, $buf);
  $buf = str_replace("!!!merchant_order_id!!!", $invoice, $buf);
  $buf = str_replace("!!!memo!!!", $memo, $buf);
  $buf = str_replace("!!!mc_gross!!!", $mc_gross, $buf);
  $buf = str_replace("!!!total!!!", $mc_gross, $buf);
  $buf = str_replace("!!!totalaffiliate!!!", $totalaffiliate, $buf);
  $buf = str_replace("!!!txn_id!!!", $txn_id, $buf);
  $buf = str_replace("!!!order_number!!!", $txn_id, $buf);
  $buf = str_replace("!!!ordernumber!!!", $txn_id, $buf);
  $buf = str_replace("!!!orderno!!!", $txn_id, $buf);
  $buf = str_replace("!!!first_name!!!", $first_name, $buf);
  $buf = str_replace("!!!last_name!!!", $last_name, $buf);
  if (($first_name == "") && ($last_name = ""))
    $buf = str_replace("!!!name!!!", $payer_business_name, $buf);
  else
    $buf = str_replace("!!!name!!!", $first_name . " " . $last_name, $buf);
  $buf = str_replace("!!!payer_business_name!!!", $payer_business_name, $buf);
  $buf = str_replace("!!!payer_email!!!", $payer_email, $buf);
  $buf = str_replace("!!!email!!!", $payer_email, $buf);
  $buf = str_replace("!!!payer_id!!!", $payer_id, $buf);
  $buf = str_replace("!!!address_name!!!", $address_name, $buf);
  $buf = str_replace("!!!address_street!!!", $address_street, $buf);
  $buf = str_replace("!!!address_city!!!", $address_city, $buf);
  $buf = str_replace("!!!address_state!!!", $address_state, $buf);
  $buf = str_replace("!!!address_zip!!!", $address_zip, $buf);
  $buf = str_replace("!!!address_country!!!", $address_country, $buf);
  $buf = str_replace("!!!address_country_code!!!", $address_country_code, $buf);
  $buf = str_replace("!!!option_name1!!!", $option_name1, $buf);
  $buf = str_replace("!!!option_selection1!!!", $option_selection1, $buf);
  $buf = str_replace("!!!option_name2!!!", $option_name2, $buf);
  $buf = str_replace("!!!option_selection2!!!", $option_selection2, $buf);
  $buf = str_replace("!!!mc_currency!!!", $mc_currency, $buf);
  $buf = str_replace("!!!date!!!", date("j F Y"), $buf); // Output date like 24 January 2004
  $buf = str_replace("!!!payment_date!!!", $payment_date, $buf);
  $buf = str_replace("!!!custom!!!", $custom, $buf);
  $buf = str_replace("!!!tax!!!", $tax, $buf);
  $buf = str_replace("!!!num_cart_items!!!", $num_cart_items, $buf);
  $buf = str_replace("!!!fee!!!", $mc_fee, $buf);
  $buf = str_replace("!!!mc_fee!!!", $mc_fee, $buf);
  $buf = str_replace("!!!settle_amount!!!", $settle_amount, $buf);
  $buf = str_replace("!!!settle_currency!!!", $settle_currency, $buf);
  $buf = str_replace("!!!auction_buyer_id!!!", $auction_buyer_id, $buf);
  $buf = str_replace("!!!auction_closing_date!!!", $auction_closing_date, $buf);
  $buf = str_replace("!!!payer_status!!!", $payer_status, $buf);
  $buf = str_replace("!!!address_status!!!", $address_status, $buf);
  $buf = str_replace("!!!ipaddr!!!", $clientipaddr, $buf);
  $buf = str_replace("!!!shipping!!!", $mc_shipping, $buf);
  $buf = str_replace("!!!shipping_method!!!", $shipping_method, $buf);
 }

function InsertProductVariables(&$buf, $ProdDesc, $ProdLink, $ProdSize, $ProdExp, $ProdFile, $ext)
{
  global $numberofproducts, $HTMLEmail, $manualorder, $newexpiry, $txn_id, $manualiplevel, $clientipaddr, $IPlock;
  global $calledasreturn, $payment_date, $payer_email;
  $max = 30;
//  if ($numberofproducts > $max)
//    $max = $numberofproducts;
  // First deal with any !!!link_n!!! that is part of hyperlink
  $start = 0;
  do
  {
    $pos = strpos($buf, "<a", $start);
    if (!is_integer($pos))
      $pos = strpos($buf, "<A", $start);
    $pos2 = strpos($buf, "</a>", $pos);
    if (!is_integer($pos2))
      $pos2 = strpos($buf, "</A>", $pos);
    $found = 0;
    if ((is_integer($pos)) && (is_integer($pos2)))
    {
      $found = 1;
      for ($k = 1; $k <= $max; $k++)
      {
        if ($ProdLink[$k-1] == "")
        {
          // See if !!!link_k!!! is within the hyperlink
          $hl = substr($buf, $pos, $pos2 - $pos);
          $pos3 = strpos($hl, "!!!link_" . $k . "!!!");
          if (!is_integer($pos3))
            $pos3 = strpos($hl, "!!!link_" . $k . "!!!");
          if (is_integer($pos3))
          {
            $start = $pos;
            $buf = substr_replace($buf, "", $pos, $pos2 - $pos);
            break;
          }
          else
            $start = $pos2;
        }
        else
          $start = $pos2;
      }
    }
  }
  while ($found == 1);
  // Now replace all other variables
  for ($k = 1; $k <= $max; $k++)
  {
    if ($ProdDesc[$k-1] != "")
    {
      $buf = str_replace("!!!description_" . $k . "!!!", $ProdDesc[$k-1], $buf);
      $buf = str_replace("!!!filename_" . $k . "!!!", $ProdFile[$k-1], $buf);
      $buf = str_replace("!!!link_" . $k . "!!!", $ProdLink[$k-1], $buf);
      $buf = str_replace("!!!size_" . $k . "!!!", FriendlyFileSize($ProdSize[$k-1]), $buf);
      $buf = str_replace("!!!expires_" . $k . "!!!", FriendlyExpiryTime($ProdExp[$k-1]), $buf);
      if (($calledasreturn==1) && (isset($payment_date)))
        $buf = str_replace("!!!expiresat_" . $k . "!!!", date("d F Y G:i", strtotime($payment_date) + ($ProdExp[$k-1] * 60)), $buf);                    
      else
        $buf = str_replace("!!!expiresat_" . $k . "!!!", date("d F Y G:i", time() + ($ProdExp[$k-1] * 60)), $buf);
      if (($HTMLEmail == "Y") || ($ext != ".txt"))
      {
        $tot = "To download " . $ProdDesc[$k-1] . " click the link below:<BR>";
        $tot .= "<a href=\"" . $ProdLink[$k-1] . "\">" . $ProdFile[$k-1] . "</a>";
        if ($ProdSize[$k-1] != "0")
          $tot .= " (" . FriendlyFileSize($ProdSize[$k-1]) . ")";
        if ($ProdExp[$k-1] != "0")
          $tot .= " ~ Download link will expire in " . FriendlyExpiryTime($ProdExp[$k-1]);
      }
      else
      {
        $tot = "To download " . $ProdDesc[$k-1] . " click the link below:\n" . $ProdLink[$k-1] . " \n";
        $tot .= $ProdFile[$k-1];
        if ($ProdSize[$k-1] != "0")
          $tot .= " (" . FriendlyFileSize($ProdSize[$k-1]) . ")";
        if ($ProdExp[$k-1] != "0")
          $tot .= " ~ Download link will expire in " . FriendlyExpiryTime($ProdExp[$k-1]);
      }
      $buf = str_replace("!!!product_" . $k . "!!!", $tot, $buf);
    }
    else
    {
      $buf = str_replace("!!!description_" . $k . "!!!", "", $buf);
      $buf = str_replace("!!!filename_" . $k . "!!!", "", $buf);
      $buf = str_replace("!!!size_" . $k . "!!!", "", $buf);
      $buf = str_replace("!!!expires_" . $k . "!!!", "", $buf);
      $buf = str_replace("!!!expiresat_" . $k . "!!!", "", $buf);
      $buf = str_replace("!!!product_" . $k . "!!!", "", $buf);
      $buf = str_replace("!!!link_" . $k . "!!!", "", $buf);
    }
  }
  // Handle any !!!description(id)!!! variable
  $itemids = getitemvars($buf, "description");
  $items = explode(",", $itemids);
  for ($k = 0;$k < count($items);$k++)
  {
    $j = GetProductDetails($items[$k], $paycurr, $cost, $desc, $link, $size, $exp, $extra);
    if ($j != 0)
      $buf = str_replace("!!!description(" . $items[$k] . ")!!!", $desc, $buf);
    else
      $buf = str_replace("!!!description(" . $items[$k] . ")!!!", "", $buf);
  }
  // Handle any !!!expires(id)!!! variable
  $itemids = getitemvars($buf, "expires");
  $items = explode(",", $itemids);
  for ($k = 0;$k < count($items);$k++)
  {
    $j = GetProductDetails($items[$k], $paycurr, $cost, $desc, $link, $size, $exp, $extra);
    if ($j != 0)
    {
      if (($manualorder == true) && ($newexpiry != -1))
        $exp = $newexpiry;
      if ($exp != 0)
      {
        if (strlen($exp) == 12)
        {
          $expiry = mktime(substr($exp, 8, 2), substr($exp, 10, 2), 0, substr($exp, 4, 2), substr($exp, 6, 2), substr($exp, 0, 4), -1);
          if (($calledasreturn==1) && (isset($payment_date)))              
            $exp = ($expiry - strtotime($payment_date)) / 60;
          else         
            $exp = ($expiry - time()) / 60;
        }
      }
      $buf = str_replace("!!!expires(" . $items[$k] . ")!!!", FriendlyExpiryTime($exp), $buf);
    }
    else
      $buf = str_replace("!!!expires(" . $items[$k] . ")!!!", "", $buf);
  }
  // Handle any !!!expiresat(id)!!! variable
  $itemids = getitemvars($buf, "expiresat");
  $items = explode(",", $itemids);
  for ($k = 0;$k < count($items);$k++)
  {
    $j = GetProductDetails($items[$k], $paycurr, $cost, $desc, $link, $size, $exp, $extra);
    if ($j != 0)
    {
      if (($manualorder == true) && ($newexpiry != -1))
        $exp = $newexpiry;
      if ($exp != 0)
      {
        if (strlen($exp) == 12)
        {
          $expiry = mktime(substr($exp, 8, 2), substr($exp, 10, 2), 0, substr($exp, 4, 2), substr($exp, 6, 2), substr($exp, 0, 4), -1);
          if (($calledasreturn==1) && (isset($payment_date)))              
            $exp = ($expiry - strtotime($payment_date)) / 60;
          else
            $exp = ($expiry - time()) / 60;
        }
      }
      if (($calledasreturn==1) && (isset($payment_date)))                    
        $buf = str_replace("!!!expiresat(" . $items[$k] . ")!!!", date("d F Y G:i", strtotime($payment_date) + ($exp * 60)), $buf);
      else  
        $buf = str_replace("!!!expiresat(" . $items[$k] . ")!!!", date("d F Y G:i", time() + ($exp * 60)), $buf);
    }
    else
      $buf = str_replace("!!!expiresat(" . $items[$k] . ")!!!", "", $buf);
  }
  // Handle any !!!filename(id)!!! variable
  $itemids = getitemvars($buf, "filename");
  $items = explode(",", $itemids);
  for ($k = 0;$k < count($items);$k++)
  {
    $j = GetProductDetails($items[$k], $paycurr, $cost, $desc, $link, $size, $exp, $extra);
    if (($j != 0) && ($link != "NA"))
      $buf = str_replace("!!!filename(" . $items[$k] . ")!!!", filename($link), $buf);
    else
      $buf = str_replace("!!!filename(" . $items[$k] . ")!!!", "", $buf);
  }
  // Handle any !!!size(id)!!! variable
  $itemids = getitemvars($buf, "size");
  $items = explode(",", $itemids);
  for ($k = 0;$k < count($items);$k++)
  {
    $j = GetProductDetails($items[$k], $paycurr, $cost, $desc, $link, $size, $exp, $extra);
    if (($j != 0) && ($link != "NA"))
      $buf = str_replace("!!!size(" . $items[$k] . ")!!!", FriendlyFileSize($size), $buf);
    else
      $buf = str_replace("!!!size(" . $items[$k] . ")!!!", "", $buf);
  }
  // Handle any !!!link(id)!!! variable
  $itemids = getitemvars($buf, "link");
  $items = explode(",", $itemids);
  for ($k = 0;$k < count($items);$k++)
  {
    $j = GetProductDetails($items[$k], $paycurr, $cost, $desc, $link, $size, $exp, $extra);
    if (($j != 0) && ($link != "NA"))
    {
      if (($manualorder == true) && ($newexpiry != -1))
        $exp = $newexpiry;
      if ($exp != 0)
      {
        if (strlen($exp) == 12)
        {
          $expiry = mktime(substr($exp, 8, 2), substr($exp, 10, 2), 0, substr($exp, 4, 2), substr($exp, 6, 2), substr($exp, 0, 4), -1);
          if (($calledasreturn==1) && (isset($payment_date)))              
            $exp = ($expiry - strtotime($payment_date)) / 60;
          else
            $exp = ($expiry - time()) / 60;          
        }
        else
        {
          if (($calledasreturn==1) && (isset($payment_date)))
            $expiry = strtotime($payment_date) + ($exp * 60);
          else                               
            $expiry = time() + ($exp * 60);
        }
      }
      else
        $expiry = 0;
      $ipleveltouse = 0;
      if (isset($IPlock))
        $ipleveltouse = $IPlock;
      if (($manualorder == true) && ($manualiplevel != "-1"))
        $ipleveltouse = $manualiplevel;
      else
      {
        $pos = strpos($extra, "IPLOCK=");
        if (is_integer($pos))
          $ipleveltouse = substr($extra, $pos + 7, 1);
      }
      $plink = GetSecureLink($link, $items[$k], $expiry, $txn_id, $clientipaddr, $ipleveltouse, $payer_email);
      $buf = str_replace("!!!link(" . $items[$k] . ")!!!", $plink, $buf);
    }
    else
      $buf = str_replace("!!!link(" . $items[$k] . ")!!!", "", $buf);
  }
}

function InsertLoopVariables(&$buffer, $ProdId, $ProdDesc, $ProdLink, $ProdSize, $ProdExp, $ProdFile, $ProdExtra, $temptype, $ext)
{
  global $option_name1, $option_selection1, $option_name2, $option_selection2, $Dsegment, $Esegment, $HTMLEmail;
  global $payer_email, $first_name, $last_name, $payer_business_name, $SitelokEmail, $SitelokEmailAdmin;
  global $manualorder, $txn_id, $DbNameLinklok, $DbTableNameOrders, $mysql_linklok;
  global $DbHostLinklok, $DbUserLinklok, $DbPasswordLinklok, $DbNameLinklok;
  global $SitelokEmailNew,$SitelokEmailUpdate,$SitelokEmailNewAdmin,$SitelokEmailUpdateAdmin;
  global $Custom11Field,$SitelokDefaultGroup,$SitelokDefaultExpiry,$custom,$loitemdataarray,$txn_id;
  global $SitelokUseNameOnly,$SitelokCustom;
  if (function_exists("slapi_getuser"))
  {  
    // Setup Sitelok variables
    $sitelokgroupsarray=array();
    $sitelokexpiresarray=array();
    // See if user exists or not in case we need the account
    $sitelokusername = $payer_email;
    if ($custom!="")
    {
      $temp = GetVarFromString($custom, "U");
      if ($temp!="")
        $sitelokusername = $temp;
    }
    if ($Custom11Field!="")
    {
      $exists = slapi_getuser($sitelokusername, $created, $pass, $en, $nm, $em, $ugs, $cus1, $cus2, $cus3, $cus4, $cus5, $cus6, $cus7, $cus8, $cus9, $cus10,
                              $cus11, $cus12, $cus13, $cus14, $cus15, $cus16, $cus17, $cus18, $cus19, $cus20,$cus21, $cus22, $cus23, $cus24, $cus25, $cus26, $cus27, $cus28, $cus29, $cus30,
                              $cus31, $cus32, $cus33, $cus34, $cus35, $cus36, $cus37, $cus38, $cus39, $cus40,$cus41, $cus42, $cus43, $cus44, $cus45, $cus46, $cus47, $cus48, $cus49, $cus50);
    }
    else
      $exists = slapi_getuser($sitelokusername, $created, $pass, $en, $nm, $em, $ugs, $cus1, $cus2, $cus3, $cus4, $cus5, $cus6, $cus7, $cus8, $cus9, $cus10);
    if ($exists == 1)
      $sitelokpassword = $pass;
    else
    {
      $alpha = "abcdefghijklmnopqrstuvwxyz";
      $numeric = "0123456789";
      $sitelokpassword = "";
      for ($i = 0;$i < 4;$i++)
      $sitelokpassword .= substr($alpha, mt_rand(0, 25), 1);
      for ($i = 0;$i < 2;$i++)
      $sitelokpassword .= substr($numeric, mt_rand(0, 9), 1);
    }
    // If mysql being used reconnect as Sitelok may have closed connection
    if (($DbHostLinklok!="") && ($DbUserLinklok!="") && ($DbPasswordLinklok!="") && ($DbNameLinklok!=""))
    {
      $mysql_linklok=mysql_connect($DbHostLinklok,$DbUserLinklok,$DbPasswordLinklok);  
      if ($mysql_linklok==0)
      {
        ShowMessage($ErrorTemplate, "Error connecting to MySQL", $ErrorEmail);
        exit;
      }
      $db=mysql_select_db($DbNameLinklok,$mysql_linklok);
      if ($db==False)
      {
        mysql_close($mysql_linklok);
        ShowMessage($ErrorTemplate, "Error connecting to the database", $ErrorEmail);
        exit;
      }
    }
  }  
  // Setup item data array for Sitelok Linklok Orders plugin
  $loitemdataarray=array();           
  // See if serial number, software passport, clicklocker or userfunction return value already stored
  if (count($ProdId)>0)
  {
    if ($DbTableNameOrders!="")
    {
      $query="SELECT * FROM ".$DbTableNameOrders." WHERE orderno=".quote_smart(trim($txn_id));
      $mysql_result=mysql_query($query,$mysql_linklok);
      if ($row=mysql_fetch_array($mysql_result,MYSQL_ASSOC))
      {
        $d = $row['serialnumber'];
        $sernoarray=explode("|",$d);
        $d = $row['softwarepassportname'];
        $spnamearray=explode("|",$d);
        $d = $row['softwarepassportkey'];
        $spkeyarray=explode("|",$d);
        $d = $row['userfunction'];
        $userfuncarray=explode("|",$d);        
        $d = $row['clicklocker'];
        $clicklockerarray=explode("|",$d);        
      }
    }
    if (count($sernoarray)!=count($ProdId))
      $sernoarray=array_fill(0,count($ProdId),"");
    if (count($spnamearray)!=count($ProdId))
      $spnamearray=array_fill(0,count($ProdId),"");
    if (count($spkeyarray)!=count($ProdId))
      $spkeyarray=array_fill(0,count($ProdId),"");
    if (count($userfuncarray)!=count($ProdId))
      $userfuncarray=array_fill(0,count($ProdId),"");
    if (count($clicklockerarray)!=count($ProdId))
      $clicklockerarray=array_fill(0,count($ProdId),"");
  }  
  do
  {
    $found = 0;
    $pos = strpos($buffer, "<!--eachproductstart-->");
    $pos2 = strpos($buffer, "<!--eachproductend-->");
    if ((is_integer($pos)) && (is_integer($pos2)))
    {
      $found = 1;
      $buf = substr($buffer, $pos + 23, $pos2 - $pos-23);
      // Now remove this section
      $buffer1 = substr($buffer, 0, $pos);
      $buffer2 = substr($buffer, $pos2 + 21, strlen($buffer) - $pos2-21);
      $buffer = $buffer1;
      for ($k = 1; $k <= count($ProdLink); $k++)
      {
        $repeatbuf=$buf;
        ProcessProductVariables($repeatbuf, $k, $ProdId, $ProdDesc, $ProdLink, $ProdSize, $ProdExp, $ProdFile, $ProdExtra, $temptype, $ext, $sitelokusername, $sitelokpassword,$sitelokgroupsarray,$sitelokexpiresarray,$sernoarray,$spnamearray,$spkeyarray,$userfuncarray,$clicklockerarray);
        $buffer.=$repeatbuf;
      }
      $buffer .= $buffer2;
    }
  }
  while ($found == 1);
  // Now replace any loop variables outside of the loop (assume first product ordered)
  if (count($ProdId)>0)
    ProcessProductVariables ($buffer, 1, $ProdId, $ProdDesc, $ProdLink, $ProdSize, $ProdExp, $ProdFile, $ProdExtra, $temptype, $ext, $sitelokusername, $sitelokpassword,$sitelokgroupsarray,$sitelokexpiresarray,$sernoarray,$spnamearray,$spkeyarray,$userfuncarray,$clicklockerarray);  
  if (function_exists('linklokorders_createorder'))
  {
    $sitelokgroupsarray[]="ORDERACCESS";
    $sitelokexpiresarray[]="0";   
  }
  if ($SitelokDefaultGroup!="")
  {
    $sitelokgroupsarray[]=$SitelokDefaultGroup;
    $sitelokexpiresarray[]=$SitelokDefaultExpiry;   
  } 
  if (count($sitelokgroupsarray) > 0)
  {
    // If Sitelok user needs to be setup do it here
    if (!$SitelokUseNameOnly)
    {
      if ($payer_business_name!="")
   	    $nametouse=$payer_business_name;
      else
        $nametouse=$first_name . " " . $last_name;
    }
    else
    {
      $nametouse=$first_name . " " . $last_name;
    }
    // Handle any custom field variables
    for ($k=1;$k<=50;$k++)
    {
      global $$SitelokCustom[$k];
      $cu[$k]=$$SitelokCustom[$k];
    }  
    if ($temptype == "ESEGMENT")
    {
      // See if separate new and updated Sitelok emails defined
      if ($SitelokEmailNew=="")
        $SitelokEmailNew=$SitelokEmail;
      if ($SitelokEmailUpdate=="")
        $SitelokEmailUpdate=$SitelokEmail;        
      if ($SitelokEmailNewAdmin=="")
        $SitelokEmailNewAdmin=$SitelokEmailAdmin;
      if ($SitelokEmailUpdateAdmin=="")
        $SitelokEmailUpdateAdmin=$SitelokEmailAdmin;                
      slapi_addorupdate($sitelokusername, $sitelokpassword, $payer_email, $nametouse, $sitelokgroupsarray, $sitelokexpiresarray, $SitelokEmailNew, $SitelokEmailNewAdmin, $SitelokEmailUpdate, $SitelokEmailUpdateAdmin, 1
      ,$cu[1],$cu[2],$cu[3],$cu[4],$cu[5],$cu[6],$cu[7],$cu[8],$cu[9],$cu[10]
      ,$cu[11],$cu[12],$cu[13],$cu[14],$cu[15],$cu[16],$cu[17],$cu[18],$cu[19],$cu[20]
      ,$cu[21],$cu[22],$cu[23],$cu[24],$cu[25],$cu[26],$cu[27],$cu[28],$cu[29],$cu[30]
      ,$cu[31],$cu[32],$cu[33],$cu[34],$cu[35],$cu[36],$cu[37],$cu[38],$cu[39],$cu[40]
      ,$cu[41],$cu[42],$cu[43],$cu[44],$cu[45],$cu[46],$cu[47],$cu[48],$cu[49],$cu[50]);
    }
    else
      slapi_addorupdate($sitelokusername, $sitelokpassword, $payer_email, $nametouse, $sitelokgroupsarray, $sitelokexpiresarray, "", "", "", "", 1
      ,$cu[1],$cu[2],$cu[3],$cu[4],$cu[5],$cu[6],$cu[7],$cu[8],$cu[9],$cu[10]
      ,$cu[11],$cu[12],$cu[13],$cu[14],$cu[15],$cu[16],$cu[17],$cu[18],$cu[19],$cu[20]
      ,$cu[21],$cu[22],$cu[23],$cu[24],$cu[25],$cu[26],$cu[27],$cu[28],$cu[29],$cu[30]
      ,$cu[31],$cu[32],$cu[33],$cu[34],$cu[35],$cu[36],$cu[37],$cu[38],$cu[39],$cu[40]
      ,$cu[41],$cu[42],$cu[43],$cu[44],$cu[45],$cu[46],$cu[47],$cu[48],$cu[49],$cu[50]);
    // If mysql being used reconnect as Sitelok may have closed connection
    if (($DbHostLinklok!="") && ($DbUserLinklok!="") && ($DbPasswordLinklok!="") && ($DbNameLinklok!=""))
    {
      $mysql_linklok=mysql_connect($DbHostLinklok,$DbUserLinklok,$DbPasswordLinklok);  
      if ($mysql_linklok==0)
      {
        ShowMessage($ErrorTemplate, "Error connecting to MySQL", $ErrorEmail);
        exit;
      }
      $db=mysql_select_db($DbNameLinklok,$mysql_linklok);
      if ($db==False)
      {
        mysql_close($mysql_linklok);
        ShowMessage($ErrorTemplate, "Error connecting to the database", $ErrorEmail);
        exit;
      }
    }
    if ($DbTableNameOrders!="")
    {
      $Query = "UPDATE " . $DbTableNameOrders . " SET sitelokusername=".quote_smart($sitelokusername).", sitelokpassword=".quote_smart($sitelokpassword)." WHERE orderno=" . quote_smart($txn_id);
      $mysql_result = mysql_query($Query, $mysql_linklok); 
    }
  } 
  // Insert Sitelok details in templates in case only tangible items ordered
  $buffer = str_replace("!!!sitelokusername!!!", $sitelokusername, $buffer);
  $buffer = str_replace("!!!sitelokpassword!!!", $sitelokpassword, $buffer);                    
  // Store any serial numbers etc
  if (($DbTableNameOrders!="") && (count($ProdId)>0))
  {
    $Query = "UPDATE " . $DbTableNameOrders . " SET serialnumber=".quote_smart(implode($sernoarray,"|")).", softwarepassportname=".quote_smart(implode($spnamearray,"|")).", softwarepassportkey=".quote_smart(implode($spkeyarray,"|")).", userfunction=".quote_smart(implode($userfuncarray,"|")).", clicklocker=".quote_smart(implode($clicklockerarray,"|"))." WHERE orderno=" . quote_smart($txn_id);
    $mysql_result = mysql_query($Query, $mysql_linklok); 
  }  
  // Store item data for Sitelok Linklok Orders plugin
  if (function_exists('linklokorders_createorder'))
  {
    linklokorders_additemdata($txn_id,$loitemdataarray);
  }
}

function ProcessProductVariables(&$repeatbuf, $k, $ProdId, $ProdDesc, $ProdLink, $ProdSize, $ProdExp, $ProdFile, $ProdExtra, $temptype, $ext, &$sitelokusername, &$sitelokpassword, &$sitelokgroupsarray, &$sitelokexpiresarray,&$sernoarray,&$spnamearray,&$spkeyarray,&$userfuncarray,&$clicklockerarray)
{
  global $option_name1, $option_selection1, $option_name2, $option_selection2, $Dsegment, $Esegment, $HTMLEmail;
  global $payer_email, $first_name, $last_name, $payer_business_name, $Custom11Field,$custom;
  global $calledasreturn, $payment_date;
  global $DbNameLinklok, $DbTableNameOrders, $mysql_linklok;
  global $DbHostLinklok, $DbUserLinklok, $DbPasswordLinklok, $DbNameLinklok;  
  global $loitemdataarray;
  // clear array cell for Sitelok Linklok Orders plugin
  $loitemdataarray[$k-1]="";
  unset($sitelokgroup);
  unset($sitelokexpires);
  $siteloklogin = "";
  // Get default segment
  $segment = "";
  if ($temptype == "DSEGMENT")
  {
    $defseg = $Dsegment;
  }
  else
  {
    $defseg = $Esegment;
  }  
  if ($defseg != "")
  {
    if (($fh = @fopen($defseg, "r")))
    {
      $segment = fread ($fh, 100000);
      fclose($fh);
    }
  }
  // See if product segment
  if ($ProdExtra[$k-1] != "")
  {
    $extrasettings = explode("*", $ProdExtra[$k-1]);
    for ($j = 0;$j < count($extrasettings);$j++)
    {
      $command = explode("=", $extrasettings[$j],2);
      if (strtoupper($command[0]) == $temptype)
      {
        if ($command[1] != "")
        {
          if (($fh = @fopen($command[1], "r")))
          {
            $segment = fread ($fh, 100000);
            fclose($fh);
          }
        }
      }
    }
  }
  // Insert standard variables into segment
  InsertVariables($segment);
  InsertProductVariables($segment, $ProdDesc, $ProdLink, $ProdSize, $ProdExp, $ProdFile, $ext);
  $repeatbuf = str_replace("!!!segment!!!", $segment, $repeatbuf);
  $repeatbuf = str_replace("!!!item_number!!!", $ProdId[$k-1], $repeatbuf);
  $repeatbuf = str_replace("!!!description!!!", $ProdDesc[$k-1], $repeatbuf);
  $repeatbuf = str_replace("!!!filename!!!", $ProdFile[$k-1], $repeatbuf);
  $repeatbuf = str_replace("!!!link!!!", $ProdLink[$k-1], $repeatbuf);
  $repeatbuf = str_replace("!!!size!!!", FriendlyFileSize($ProdSize[$k-1]), $repeatbuf);
  $repeatbuf = str_replace("!!!expires!!!", FriendlyExpiryTime($ProdExp[$k-1]), $repeatbuf);
  $repeatbuf = str_replace("!!!itemposition!!!", $k, $repeatbuf);
  if (($calledasreturn==1) && (isset($payment_date)))                
    $repeatbuf = str_replace("!!!expiresat!!!", date("d F Y G:i", strtotime($payment_date) + ($ProdExp[$k-1]) * 60), $repeatbuf);
  else  
    $repeatbuf = str_replace("!!!expiresat!!!", date("d F Y G:i", time() + ($ProdExp[$k-1]) * 60), $repeatbuf);
  if (($HTMLEmail == "Y") || ($ext != ".txt"))
  {
    if (($ProdLink[$k-1]=="NA") || ($ProdLink[$k-1]=="NA"))
    {
      $tot=$ProdDesc[$k-1];
    }
    else
    {
      $tot = "To download " . $ProdDesc[$k-1] . " click the link below:<BR>";
      $tot .= "<a href=\"" . $ProdLink[$k-1] . "\">" . $ProdFile[$k-1] . "</a>";
      if ($ProdSize[$k-1] != "0")
        $tot .= " (" . FriendlyFileSize($ProdSize[$k-1]) . ")";
      if ($ProdExp[$k-1] != "0")
        $tot .= " ~ Download link will expire in " . FriendlyExpiryTime($ProdExp[$k-1]);
    }  
  }
  else
  {
    if (($ProdLink[$k-1]=="NA") || ($ProdLink[$k-1]=="NA"))
    {
      $tot=$ProdDesc[$k-1];
    }
    else
    {
      $tot = "To download " . $ProdDesc[$k-1] . " click the link below:\n" . $ProdLink[$k-1] . " \n";
      $tot .= $ProdFile[$k-1];
      if ($ProdSize[$k-1] != "0")
        $tot .= " (" . FriendlyFileSize($ProdSize[$k-1]) . ")";
      if ($ProdExp[$k-1] != "0")
        $tot .= " ~ Download link will expire in " . FriendlyExpiryTime($ProdExp[$k-1]);
    }    
  }
  // If link was NA (now blank) then set to blank
  if ($ProdLink[$k-1]=="")
    $tot="";
  $repeatbuf = str_replace("!!!product!!!", $tot, $repeatbuf);
  // Store product details and download link for Sitelok Linklok Orders plugin
  $loitemdataarray[$k-1].="ID=".$ProdId[$k-1];
  $loitemdataarray[$k-1].=",DESC=".$ProdDesc[$k-1];
  $loitemdataarray[$k-1].=",FILENAME=".$ProdFile[$k-1];
  $loitemdataarray[$k-1].=",LINK=".$ProdLink[$k-1];
  $loitemdataarray[$k-1].=",SIZE=".$ProdSize[$k-1];
  $loitemdataarray[$k-1].=",EXP=".$ProdExp[$k-1];    
  // See if USERNAME, PASSWORD, USER_FUNCTION, SERIAL_NO_LIST, SOFTWARE_PASSPORT or NOTE defined in extras
  if ($ProdExtra[$k-1] != "")
  {
    // Some variables should only be processed if actually used in the template
    $serialnumberused = strpos(strtolower($repeatbuf), "!!!serialnumber!!!");
    $userfunctionused = strpos(strtolower($repeatbuf), "!!!userfunction!!!");
    $softwarepassportused=strpos(strtolower($repeatbuf),"!!!softwarepassportkey!!!");
    $clicklockerused=strpos(strtolower($repeatbuf),"!!!clicklocker!!!");
    $callurlused=strpos(strtolower($repeatbuf),"!!!callurl!!!");
    $callurldataused=strpos(strtolower($repeatbuf),"!!!callurldata!!!");      
    $extrasettings = explode("*", $ProdExtra[$k-1]);
    for ($j = 0;$j < count($extrasettings);$j++)
    {
      $command = explode("=", $extrasettings[$j],2);
      if (strtoupper($command[0]) == "USERNAME")
      {
        $repeatbuf = str_replace("!!!username!!!", $command[1], $repeatbuf);
        $loitemdataarray[$k-1].=",USERNAME=".$command[1];    
      }  
      if (strtoupper($command[0]) == "PASSWORD")
      {
        $repeatbuf = str_replace("!!!password!!!", $command[1], $repeatbuf);
        $loitemdataarray[$k-1].=",PASSWORD=".$command[1];    
      }  
      if (strtoupper($command[0]) == "NOTE")
      {
        $repeatbuf = str_replace("!!!note!!!", $command[1], $repeatbuf);
        $loitemdataarray[$k-1].=",NOTE=".$command[1];    
      }  
      if ((strtoupper($command[0]) == "SERIAL_NO_LIST") && (is_integer($serialnumberused)))
      {
        if ($sernoarray[$k-1]!="")
          $serialnumber=$sernoarray[$k-1];
        else
        {        
          $serialnumber = GetSerialNumber($command[1]);
          $sernoarray[$k-1]=$serialnumber;
        }  
        $repeatbuf = str_replace("!!!serialnumber!!!", $serialnumber, $repeatbuf);
        $loitemdataarray[$k-1].=",SERIALNO=".$serialnumber;    
      }
      if ((strtoupper($command[0]) == "USER_FUNCTION") && (is_integer($userfunctionused)))
      {
        if ($userfuncarray[$k-1]!="")
          $ufd=$userfuncarray[$k-1];
        else
        {  
          $ufd = userfunction($command[1], $option_name1, $option_selection1, $option_name2, $option_selection2);
          $userfuncarray[$k-1]=$ufd;
        }
        $repeatbuf = str_replace("!!!userfunction!!!", $ufd, $repeatbuf);
        $loitemdataarray[$k-1].=",USERFUNCTION=".$ufd;            
      }
      if ((strtoupper($command[0]) == "SOFTWARE_PASSPORT") && (is_integer($softwarepassportused)))
      {
        if (($spkeyarray[$k-1]!="") && ($spnamearray[$k-1]!=""))
        {
          $spname=$spnamearray[$k-1];
          $spkey=$spkeyarray[$k-1];
        }  
        else        
        {
          $spd = softwarepassport($command[1], $option_selection1, $option_selection2);
          $spdarray = explode("|", $spd);
          $spname=$spdarray[0];
          $spkey=$spdarray[1];
          $spnamearray[$k-1]=$spname;
          $spkeyarray[$k-1]=$spkey;
        }
        $repeatbuf = str_replace("!!!softwarepassportname!!!", $spname, $repeatbuf);
        $repeatbuf = str_replace("!!!softwarepassportkey!!!", $spkey, $repeatbuf);
        $loitemdataarray[$k-1].=",SOFTWAREPASSPORTNAME=".$spname;    
        $loitemdataarray[$k-1].=",SOFTWAREPASSPORTKEY=".$spkey;
      }
      if ((strtoupper($command[0]) == "CLICKLOCKER") && (is_integer($clicklockerused)))
      {
        if ($clicklockerarray[$k-1]!="")
          $activation=$clicklockerarray[$k-1];
        else
        {
          if ($payer_business_name != "")
            $nametouse = $payer_business_name;
          else
            $nametouse = $first_name . " " . $last_name;
          $activation = ClickLocker($command[1], $nametouse, $payer_email);
          $clicklockerarray[$k-1]=$activation;
        }
        $repeatbuf = str_replace("!!!clicklocker!!!", $activation, $repeatbuf);
        $loitemdataarray[$k-1].=",CLICKLOCKER=".$activation;            
      }
      if ((strtoupper($command[0]) == "CALLURL") && (is_integer($callurlused)))
      {
      	$callurlret=callurl($command[1]);
        $repeatbuf = str_replace("!!!callurl!!!", "", $repeatbuf);
      }
      if ((strtoupper($command[0]) == "CALLURLDATA") && (is_integer($callurldataused)))
      {
      	$callurlret=callurl($command[1]);
        $repeatbuf = str_replace("!!!callurldata!!!", $callurlret, $repeatbuf);
        $callurlretclean=str_replace(","," ",$callurlret);
        $callurlretclean=str_replace("\n"," ",$callurlretclean);
        $loitemdataarray[$k-1].=",CALLURLDATA=".$callurlretclean;            
      }
      if (strtoupper($command[0]) == "ACROLOK")
      {
      	$acrolokret=acrolok($ProdFile[$k-1],$payer_email,$command[1]);
        $repeatbuf = str_replace("!!!acrolok!!!", $acrolokret, $repeatbuf);
        $loitemdataarray[$k-1].=",ACROLOK=".$acrolokret;            
      }                    
      if (strtoupper($command[0]) == "SLGROUP")
        $sitelokgroup[] = $command[1];
      if (strtoupper($command[0]) == "SLEXPIRES")
        $sitelokexpires[] = $command[1];
      if (strtoupper($command[0]) == "SLLOGIN")
        $siteloklogin = $command[1];
    }
    // Get Sitelok details for this product
    if ((count($sitelokgroup)>0) || (count($sitelokexpires)>0) || ($siteloklogin != ""))
    {
      for ($k=0;$k<count($sitelokgroup);$k++)
      {      
        if (is_array($sitelokgroupsarray))  
        {
          if (is_integer($pos=array_search($sitelokgroup[$k],$sitelokgroupsarray)))
          {
            if (($sitelokexpires[$k]>$sitelokexpiresarray[$pos]) && ($sitelokexpiresarray[$pos]!=0))
              $sitelokexpiresarray[$pos]=$sitelokexpires[$k];
          }
          else
          {  
            $sitelokgroupsarray[]=$sitelokgroup[$k];
            if (is_numeric($sitelokexpires[$k]))
              $sitelokexpiresarray[]=$sitelokexpires[$k];
            else
              $sitelokexpiresarray[]=0;  
          }  
        }
        else
        {
            $sitelokgroupsarray[]=$sitelokgroup[$k];
            if (is_numeric($sitelokexpires[$k]))
              $sitelokexpiresarray[]=$sitelokexpires[$k];
            else
              $sitelokexpiresarray[]=0;        
        }
      }
      $repeatbuf = str_replace("!!!siteloklogin!!!", $siteloklogin, $repeatbuf);
      // If mysql being used reconnect as Sitelok may have closed connection
      if (($DbHostLinklok!="") && ($DbUserLinklok!="") && ($DbPasswordLinklok!="") && ($DbNameLinklok!=""))
      {
        $mysql_linklok=mysql_connect($DbHostLinklok,$DbUserLinklok,$DbPasswordLinklok);  
        if ($mysql_linklok==0)
        {
          ShowMessage($ErrorTemplate, "Error connecting to MySQL", $ErrorEmail);
          exit;
        }
        $db=mysql_select_db($DbNameLinklok,$mysql_linklok);
        if ($db==False)
        {
          mysql_close($mysql_linklok);
          ShowMessage($ErrorTemplate, "Error connecting to the database", $ErrorEmail);
          exit;
        }
      }              
    }
  }
  if ($sitelokusername!="")
    $repeatbuf = str_replace("!!!sitelokusername!!!", $sitelokusername, $repeatbuf);
  if ($sitelokpassword!="")    
    $repeatbuf = str_replace("!!!sitelokpassword!!!", $sitelokpassword, $repeatbuf);

  // Clear any remaining template variables
  $repeatbuf = str_replace("!!!username!!!", "", $repeatbuf);
  $repeatbuf = str_replace("!!!password!!!", "", $repeatbuf);
  $repeatbuf = str_replace("!!!note!!!", "", $repeatbuf);
  $repeatbuf = str_replace("!!!serialnumber!!!", "", $repeatbuf);
  $repeatbuf = str_replace("!!!userfunction!!!", "", $repeatbuf);
  $repeatbuf = str_replace("!!!softwarepassportname!!!", "", $repeatbuf);
  $repeatbuf = str_replace("!!!softwarepassportkey!!!", "", $repeatbuf);
  $repeatbuf = str_replace("!!!sitelokusername!!!", "", $repeatbuf);
  $repeatbuf = str_replace("!!!sitelokpassword!!!", "", $repeatbuf);
  $repeatbuf = str_replace("!!!siteloklogin!!!", "", $repeatbuf);  
  $repeatbuf = str_replace("!!!clicklocker!!!", "", $repeatbuf);  
  $repeatbuf = str_replace("!!!callurl!!!", "", $repeatbuf);  
  $repeatbuf = str_replace("!!!callurldata!!!", "", $repeatbuf);  
  $repeatbuf = str_replace("!!!acrolok!!!", "", $repeatbuf);  
  $repeatbuf = str_replace("!!!itemposition!!!", "", $repeatbuf);  
}


function InsertItemLoopVariables(&$buffer)
{
  global $num_cart_items, $num, $manualorder;
  do
  {
    $found = 0;
    $pos = strpos($buffer, "<!--eachitemstart-->");
    $pos2 = strpos($buffer, "<!--eachitemend-->");
    if ((is_integer($pos)) && (is_integer($pos2)))
    {
      $found = 1;
      $buf = substr($buffer, $pos + 20, $pos2 - $pos-20);
      // Now remove this section
      $buffer1 = substr($buffer, 0, $pos);
      $buffer2 = substr($buffer, $pos2 + 18, strlen($buffer) - $pos2-18);
      $buffer = $buffer1;
      $minprod = 0;
      $maxprod = 1;
      if ($num_cart_items > 0)
      {
        $maxprod = $num_cart_items + 1;
        $minprod = 1;
      }
      if ($manualorder == true)
      {
        $maxprod = $num + 1;
        $minprod = 1;
      }
      for ($k = $minprod; $k < $maxprod; $k++)
      {
        $repeatbuf = $buf;
        if ($k == 0)
        {
                    // Remove slashes from description if required
          $desc=$GLOBALS['item_name'];
          if (llipn_get_magic_quotes_gpc())
            $desc=stripslashes($desc);
          $repeatbuf = str_replace("!!!item_number!!!", $GLOBALS['item_number'], $repeatbuf);
          $repeatbuf = str_replace("!!!quantity!!!", $GLOBALS['quantity'], $repeatbuf);
          $repeatbuf = str_replace("!!!description!!!", $desc, $repeatbuf);
          $repeatbuf = str_replace("!!!item_name!!!", $desc, $repeatbuf);
          $repeatbuf = str_replace("!!!itemtotal!!!", $GLOBALS['mc_gross'], $repeatbuf);
          if ($GLOBALS['quantity']>0)
            $itemprice = $GLOBALS['mc_gross'] / $GLOBALS['quantity'];
          else
            $itemprice=0.00;  
          $itemprice = sprintf("%01.2f", $itemprice);
          $repeatbuf = str_replace("!!!itemprice!!!", $itemprice, $repeatbuf);
          $itemposition = (string)($k + 1);
          $repeatbuf = str_replace("!!!itemposition!!!", $itemposition, $repeatbuf);
        }
        else
        {
          // Remove slashes from description if required
          $desc=$GLOBALS['item_name' . $k];
          if (llipn_get_magic_quotes_gpc())
            $desc=stripslashes($desc);          
          $repeatbuf = str_replace("!!!item_number!!!", $GLOBALS['item_number' . $k], $repeatbuf);
          $repeatbuf = str_replace("!!!quantity!!!", $GLOBALS['quantity' . $k], $repeatbuf);
          $repeatbuf = str_replace("!!!description!!!", $desc, $repeatbuf);
          $repeatbuf = str_replace("!!!item_name!!!", $desc, $repeatbuf);
          if ($GLOBALS['quantity'.$k]>0)
            $itemprice = $GLOBALS['mc_gross_' . $k] / $GLOBALS['quantity' . $k];
          else
            $itemprice=0.00;  
          $itemprice = sprintf("%01.2f", $itemprice);
          $repeatbuf = str_replace("!!!itemprice!!!", $itemprice, $repeatbuf);
          $itemtotal = sprintf("%01.2f", $GLOBALS['mc_gross_' . $k]);
          $repeatbuf = str_replace("!!!itemtotal!!!", $itemtotal, $repeatbuf);
          $itemposition = (string)$k;
          $repeatbuf = str_replace("!!!itemposition!!!", $itemposition, $repeatbuf);
          $repeatbuf = str_replace("!!!option_selection1x!!!", $GLOBALS['option_selection1_' . $k], $repeatbuf);
        }
        $buffer .= $repeatbuf;
      }
      $buffer .= $buffer2;
    }
  }
  while ($found == 1);
}

function InsertUsegment(&$buf, $UnknownSegment, $nd)
{
  $segment = "";
  if ($nd > 0)
  {
    if ($UnknownSegment != "")
    {
      if (!($fh = @fopen($UnknownSegment, "r")))
      {
        $segment = "";
      }
      else
      {
        $segment = fread ($fh, 100000);
        fclose($fh);
      }
    }
  }
  $buf = str_replace("!!!unknownsegment!!!", $segment, $buf);
}

function InsertUnknownSection(&$buf, $nd)
{
  if ($nd == 0)
  {
    do
    {
      $found = 0;
      $pos = strpos($buf, "<!--unknownstart-->");
      $pos2 = strpos($buf, "<!--unknownend-->");
      if ((is_integer($pos)) && (is_integer($pos2)))
      {
        $found = 1;
        $buf = substr_replace($buf, "", $pos, ($pos2 - $pos) + 17);
      }
    }
    while ($found == 1);
  }
  $buf = str_replace("<!--unknownstart-->", "", $buf);
  $buf = str_replace("<!--unknownend-->", "", $buf);
}

function InsertKnownSection(&$buf, $numknown)
{
  if ($numknown==0)
	{
	  do
	  {
	    $found = 0;
	    $pos = strpos($buf, "<!--knownstart-->");
	    $pos2 = strpos($buf, "<!--knownend-->");
	    if ((is_integer($pos)) && (is_integer($pos2)))
	    {
	      $found = 1;
				$buf=substr_replace($buf,"",$pos,($pos2-$pos)+15);
	    }
	  }
	  while ($found == 1);
  }
  $buf = str_replace("<!--knownstart-->", "", $buf);
  $buf = str_replace("<!--knownend-->", "", $buf);
}

function getitemvars($buf, $n)
{
  $start = 0;
  $itemids = "";
  do
  {
    $pos = strpos($buf, "!!!" . $n . "(", $start);
    $found = 0;
    if (is_integer($pos))
    {
      $found = 1;
      $pos2 = strpos($buf, ")!!!", $pos);
      if (is_integer($pos2))
      {
        if ($itemids != "")
          $itemids .= ",";
        $itemids .= substr($buf, $pos + strlen($n) + 4, $pos2 - ($pos + strlen($n) + 4));
      }
      $start = $pos2;
    }
  }
  while ($found == 1);
  return($itemids);
}

function GetProductDetails($id, $currency, &$cost, &$description, &$url, &$size, &$expiry, &$extra)
{
  global $Products, $numberofproducts, $MatchItemName;
  global $DefaultLocation, $DefaultPrice, $DefaultExpiry, $DefaultExtra;
  global $mysql_linklok, $DbTableNameProducts, $DbProductsId, $DbProductsDescription;
  global $DbProductsPrice, $DbProductsLocation, $DbProductsExpiry, $DbProductsExtra;
  global $DefaultCurrency;
  global $UsePaypalOptions,$os0,$os1;
  global $GlobalDiscount;
  global $ProductDelimiter;
  $found = 0;
  if (($DbTableNameProducts=="") || (!isset($DbTableNameProducts)))
  {
    // Get product details from script or text file
    // Match against item id (item_number)
    for ($k = 0;$k < $numberofproducts;$k++)
    {
      if (trim(strtok($Products[$k], $ProductDelimiter)) == trim($id))
      {
        $found = 1;
        $description = strtok($ProductDelimiter);
        $description=trim($description);
        $description = str_replace("^", "", $description);
        $costs = explode(":", trim(strtok($ProductDelimiter)));
        $url = strtok($ProductDelimiter);
        $url=trim($url);
        $size = strtok($ProductDelimiter);
        $size=trim($size);
        $expiry = strtok($ProductDelimiter);
        $expiry = trim($expiry);
        $extra = strtok($ProductDelimiter);
        $extra = trim($extra);
        // Now get price for the chosen currency
        if ((count($costs==1)) && (is_numeric($costs[0])))
          $costs[0]=$DefaultCurrency."=".$costs[0];
        $cost = -1.00; // Leave as -1 if price not listed in chosen currency
        for ($j = 0;$j < count($costs);$j++)
        {
          if (strcasecmp(strtok($costs[$j], "="), $currency) == 0)
            $cost = strtok("=");
        }
        break;
      }
    }
    if (($MatchItemName == true) && ($found == 0))
    {
      // Match against item description (item_name)
      $found = 0;
      for ($k = 0;$k < $numberofproducts;$k++)
      {
        $tid = strtok($Products[$k], $ProductDelimiter);
        $tid=trim($tid);
        $desc = strtok($ProductDelimiter);
        $desc=trim($desc);
        $match = false;
        // No wildcard
        if ((substr($desc, 0, 1) != "^") && (substr($desc, strlen($desc)-1, 1) != "^"))
        {
          if (strcasecmp(trim($desc), trim($description)) == 0)
            $match = true;
        }
        // Wildcard at start and end
        if ((substr($desc, 0, 1) == "^") && (substr($desc, strlen($desc)-1, 1) == "^"))
        {
          $desc = str_replace("^", "", $desc);
          if (is_integer(strpos($description, $desc)))
            $match = true;
        }
        // Wildcard at start only
        if ((substr($desc, 0, 1) == "^") && (substr($desc, strlen($desc)-1, 1) != "^"))
        {
          $desc = str_replace("^", "", $desc);
          if (strcasecmp($desc, substr($description, strlen($description) - strlen($desc))) == 0)
            $match = true;
        }
        // Wildcard at end only
        if ((substr($desc, 0, 1) != "^") && (substr($desc, strlen($desc)-1, 1) == "^"))
        {
          $desc = str_replace("^", "", $desc);
          if (strcasecmp($desc, substr($description, 0, strlen($desc))) == 0)
            $match = true;
        }
        if ($match)
        {
          $found = 1;
          $id = $tid;
          $costs = explode(":", strtok($ProductDelimiter));
          $costs=trim($costs);
          $url = strtok($ProductDelimiter);
          $url=trim($url);
          $size = strtok($ProductDelimiter);
          $size=trim($size);
          $expiry = strtok($ProductDelimiter);
          $expiry=trim($expiry);
          $extra = strtok($ProductDelimiter);
          $extra = trim($extra);
          // Now get price for the chosen currency
          if ((count($costs==1)) && (is_numeric($costs[0])))
            $costs[0]=$DefaultCurrency."=".$costs[0];
          $cost = -1.00; // Leave as -1 if price not listed in chosen currency
          for ($j = 0;$j < count($costs);$j++)
          {
            if (strcasecmp(strtok($costs[$j], "="), $currency) == 0)
              $cost = strtok("=");
          }
          break;
        }
      }
    }
  }
  else
  {
    // Get product details from mysql table
    // Match against item id (item_number)
    $query="SELECT * FROM ".$DbTableNameProducts." WHERE ".$DbProductsId."=".quote_smart(trim($id));
    $mysql_result=mysql_query($query,$mysql_linklok);
    if ($row=mysql_fetch_array($mysql_result,MYSQL_ASSOC))
    {
      $found = 1;
      $description = $row[$DbProductsDescription];
      $description = str_replace("^", "", $description);
      if ($DbProductsPrice!="")
        $costs = explode(":", $row[$DbProductsPrice]);
      else
        $costs = explode(":", $DefaultPrice);                
      $url=$row[$DbProductsLocation];  
      if ($DefaultLocation!="")          
      {
        $combos = explode(":", $url);
        if ((count($combos) == 1) || (substr(strtolower($url), 0, 5) == "http:") || (substr($url, 1, 2) == ":/"))
          $url = $DefaultLocation.$url;
      }
      $size = 0;
      if ($DbProductsExpiry!="")
        $expiry = $row[$DbProductsExpiry];
      else
        $expiry = $DefaultExpiry;  
      $expiry = trim($expiry);
      if ($DbProductsExtra!="")
        $extra = $row[$DbProductsExtra];
      else
        $extra = $DefaultExtra;       
      $extra = trim($extra);
      // Now get price for the chosen currency
      if ((count($costs==1)) && (is_numeric($costs[0])))
        $costs[0]=$DefaultCurrency."=".$costs[0];
      $cost = -1.00; // Leave as -1 if price not listed in chosen currency
      for ($j = 0;$j < count($costs);$j++)
      {
        if (strcasecmp(strtok($costs[$j], "="), $currency) == 0)
          $cost = strtok("=");
      }
    }
    if (($MatchItemName == true) && ($found == 0))
    {
      // Match against item description (item_name)
      $query="SELECT * FROM ".$DbTableNameProducts;
      $mysql_result=mysql_query($query,$mysql_linklok);
      $found = 0;
	    while($row=mysql_fetch_array($mysql_result,MYSQL_ASSOC))
	    {
        $tid=$row[$DbProductsId];
        $desc=$row[$DbProductsDescription];
        $match = false;
        // No wildcard
        if ((substr($desc, 0, 1) != "^") && (substr($desc, strlen($desc)-1, 1) != "^"))
        {
          if (strcasecmp(trim($desc), trim($description)) == 0)
            $match = true;
        }
        // Wildcard at start and end
        if ((substr($desc, 0, 1) == "^") && (substr($desc, strlen($desc)-1, 1) == "^"))
        {
          $desc = str_replace("^", "", $desc);
          if (is_integer(strpos($description, $desc)))
            $match = true;
        }
        // Wildcard at start only
        if ((substr($desc, 0, 1) == "^") && (substr($desc, strlen($desc)-1, 1) != "^"))
        {
          $desc = str_replace("^", "", $desc);
          if (strcasecmp($desc, substr($description, strlen($description) - strlen($desc))) == 0)
            $match = true;
        }
        // Wildcard at end only
        if ((substr($desc, 0, 1) != "^") && (substr($desc, strlen($desc)-1, 1) == "^"))
        {
          $desc = str_replace("^", "", $desc);
          if (strcasecmp($desc, substr($description, 0, strlen($desc))) == 0)
            $match = true;
        }
        if ($match)
        {
          $found = 1;
          $id = $tid;
          if ($DbProductsPrice!="")
            $costs = explode(":", $row[$DbProductsPrice]);
          else
            $costs = explode(":", $DefaultPrice);

          $url = $row[$DbProductsLocation];            
          if ($DefaultLocation!="")          
          {
            $combos = explode(":", $url);
            if ((count($combos) == 1) || (substr(strtolower($url), 0, 5) == "http:") || (substr($url, 1, 2) == ":/"))
              $url = $DefaultLocation.$url;
          }             
          $size = 0;
          if ($DbProductsExpiry!="")
            $expiry = $row[$DbProductsExpiry];
          else
            $expiry = $DefaultExpiry;  
          $expiry = trim($expiry);
          if ($DbProductsExtra!="")
            $extra = $row[$DbProductsExtra];
          else
            $extra = $DefaultExtra;       
          $extra = trim($extra);
          // Now get price for the chosen currency
          if ((count($costs==1)) && (is_numeric($costs[0])))
            $costs[0]=$DefaultCurrency."=".$costs[0];
          $cost = -1.00; // Leave as -1 if price not listed in chosen currency
          for ($j = 0;$j < count($costs);$j++)
          {
            if (strcasecmp(strtok($costs[$j], "="), $currency) == 0)
              $cost = strtok("=");
          }
          break;
        }
      }
    }
  }
  // If still not found see if default product feature is enabled
  if ($found == 0)
  {
    if (($DefaultLocation != "") && ($DefaultPrice != "") && ($DefaultExpiry != ""))
    {
      $handleasdefault=false;
      if (substr(trim(strtolower($DefaultLocation)),0,3)=="s3|")
        $handleasdefault=true;
      else
      {  
        if (file_exists($DefaultLocation . $id))
          $handleasdefault=true;          
      }          
      if ($handleasdefault)
      {    
        $url = $DefaultLocation . $id;
        $expiry = $DefaultExpiry;
        $extra = $DefaultExtra;
        $size = 0;
        // Now get price for the chosen currency
        $costs = explode(":", $DefaultPrice);
        if ((count($costs==1)) && (is_numeric($costs[0])))
          $costs[0]=$DefaultCurrency."=".$costs[0];
        $cost = -1.00; // Leave as -1 if price not listed in chosen currency
        for ($j = 0;$j < count($costs);$j++)
        {
          if (strcasecmp(strtok($costs[$j], "="), $currency) == 0)
            $cost = strtok("=");
        }
        $found = 1;
      }
    }
  }
  // Get file size if not defined
  if ($found == 1)
  {
    // If file size set as 0 then try to get it
    if (($size == 0) && ($url != "NA"))
    {
      if (substr(trim(strtolower($url)),0,3)=="s3|")
      {
        $s = filesize_s3($url);
        if (is_numeric($s))
          $size = $s;
      }
      else
      {
        $pos = strpos(strtolower($url), "http://");
        if (is_integer($pos))
        {
          $s = filesize_remote($url);
          if (is_integer($s))
            $size = $s;
        }
        else
          $size = @filesize($url);
      }  
    }
  }
  // If discount set then apply that
  if ((is_numeric($cost)) && (is_numeric($GlobalDiscount)))
  {
    $cost=$cost-$cost*($GlobalDiscount/100);
    $cost=sprintf("%01.2f", $cost);
  }
  return($found);
}

function GetAuctionDetails(&$id, $currency, &$cost, &$description, &$url, &$size, &$expiry, &$extra)
{
  global $Products, $numberofproducts, $AllowAuctionId;
  global $DefaultLocation, $DefaultPrice, $DefaultExpiry, $DefaultExtra;
  global $mysql_linklok, $DbTableNameProducts, $DbProductsId, $DbProductsDescription;
  global $DbProductsPrice, $DbProductsLocation, $DbProductsExpiry, $DbProductsExtra;
  global $DefaultCurrency;
  global $GlobalDiscount;
  global $ProductDelimiter;
  $found = 0;
  if (($DbTableNameProducts=="") || (!isset($DbTableNameProducts)))
  {      
    // Get product details from script or text file
    // First see if we can match auction id
    if ((!isset($AllowAuctionId)) || ($AllowAuctionId == true))
    {
      for ($k = 0;$k < $numberofproducts;$k++)
      {
        if (trim(strtok($Products[$k], $ProductDelimiter)) == trim($id))
        {
          $found = 1;
          $desc = strtok($ProductDelimiter);
          $desc=trim($desc);
          $costs = explode(":", trim(strtok($ProductDelimiter)));
          $url = strtok($ProductDelimiter);
          $url=trim($url);
          if ($DefaultLocation!="")          
          {
            $combos = explode(":", $url);
            if ((count($combos) == 1) || (substr(strtolower($url), 0, 5) == "http:") || (substr($url, 1, 2) == ":/"))
              $url = $DefaultLocation.$url;
            else 
              $url = $url;
          }             
          $size = strtok($ProductDelimiter);
          $size=trim($size);
          $expiry = strtok($ProductDelimiter);
          $expiry = trim($expiry);
          $extra = strtok($ProductDelimiter);
          $extra = trim($extra);
          // Now get price for the chosen currency
          if ((count($costs==1)) && (is_numeric($costs[0])))
            $costs[0]=$DefaultCurrency."=".$costs[0];
          $cost = -1.00; // Leave as -1 if price not listed in chosen currency
          for ($j = 0;$j < count($costs);$j++)
          {
            if (strcasecmp(strtok($costs[$j], "="), $currency) == 0)
              $cost = strtok("=");
          }
          break;
        }
      }
      if ($found == 1)
      {
        $description = str_replace("^", "", $desc);
        // Get file size if not defined
        // If file size set as 0 then try to get it
        if (($size == 0) && ($url != "NA"))
        {
          if (substr(trim(strtolower($url)),0,3)=="s3|")
          {
            $s = filesize_s3($url);
            if (is_numeric($s))
              $size = $s;        
          }
          else
          {
            $pos = strpos(strtolower($url), "http://");
            if (is_integer($pos))
            {
              $s = filesize_remote($url);
              if (is_integer($s))
                $size = $s;
            }
            else
              $size = @filesize($url);
          }  
        }
        return($found);
      }
    }
    // If auction if not matched (or not allowed) try matching auction title
    $found = 0;
    for ($k = 0;$k < $numberofproducts;$k++)
    {
      $tid = strtok($Products[$k], $ProductDelimiter);
      $tid=trim($tid);
      $desc = strtok($ProductDelimiter);
      $desc=trim($desc);
      $match = false;
      // No wildcard
      if ((substr($desc, 0, 1) != "^") && (substr($desc, strlen($desc)-1, 1) != "^"))
      {
        if (strcasecmp(trim($desc), trim($description)) == 0)
          $match = true;
      }
      // Wildcard at start and end
      if ((substr($desc, 0, 1) == "^") && (substr($desc, strlen($desc)-1, 1) == "^"))
      {
        $desc = str_replace("^", "", $desc);
        if (is_integer(strpos($description, $desc)))
          $match = true;
      }
      // Wildcard at start only
      if ((substr($desc, 0, 1) == "^") && (substr($desc, strlen($desc)-1, 1) != "^"))
      {
        $desc = str_replace("^", "", $desc);
        if (strcasecmp($desc, substr($description, strlen($description) - strlen($desc))) == 0)
          $match = true;
      }
      // Wildcard at end only
      if ((substr($desc, 0, 1) != "^") && (substr($desc, strlen($desc)-1, 1) == "^"))
      {
        $desc = str_replace("^", "", $desc);
        if (strcasecmp($desc, substr($description, 0, strlen($desc))) == 0)
          $match = true;
      }
      if ($match)
      {
        $found = 1;
        $id = $tid;
        $costs = explode(":", trim(strtok($ProductDelimiter)));
        $url = strtok($ProductDelimiter);
        $url=trim($url);
        if ($DefaultLocation!="")          
        {
          $combos = explode(":", $url);
          if ((count($combos) == 1) || (substr(strtolower($url), 0, 5) == "http:") || (substr($url, 1, 2) == ":/"))
            $url = $DefaultLocation.$url;
        }             
        $size = strtok($ProductDelimiter);
        $size=trim($size);
        $expiry = strtok($ProductDelimiter);
        $expiry=trim($expiry);
        $extra = strtok($ProductDelimiter);
        $extra = trim($extra);
        // Now get price for the chosen currency
        if ((count($costs==1)) && (is_numeric($costs[0])))
          $costs[0]=$DefaultCurrency."=".$costs[0];
        $cost = -1.00; // Leave as -1 if price not listed in chosen currency
        for ($j = 0;$j < count($costs);$j++)
        {
          if (strcasecmp(strtok($costs[$j], "="), $currency) == 0)
            $cost = strtok("=");
        }
        break;
      }
    }
  }  
  else
  {
    // Get product details from MySQL table
    // First see if we can match auction id
    if ((!isset($AllowAuctionId)) || ($AllowAuctionId == true))
    {
      $query="SELECT * FROM ".$DbTableNameProducts." WHERE ".$DbProductsId."=".quote_smart(trim($id));
      $mysql_result=mysql_query($query,$mysql_linklok);
      if ($row=mysql_fetch_array($mysql_result,MYSQL_ASSOC))
      {
        $found = 1;
        $description = $row[$DbProductsDescription];
        $description = str_replace("^", "", $description);
        if ($DbProductsPrice!="")
          $costs = explode(":", $row[$DbProductsPrice]);
        else
          $costs = explode(":", $DefaultPrice);                  
        $url = $row[$DbProductsLocation];
        if ($DefaultLocation!="")          
        {
          $combos = explode(":", $url);
          if ((count($combos) == 1) || (substr(strtolower($url), 0, 5) == "http:") || (substr($url, 1, 2) == ":/"))
            $url = $DefaultLocation.$url;
          else 
            $url = $url;
        }             
        $size = 0;
        if ($DbProductsExpiry!="")
          $expiry = $row[$DbProductsExpiry];
        else
          $expiry = $DefaultExpiry;  
        $expiry = trim($expiry);
        if ($DbProductsExtra!="")
          $extra = $row[$DbProductsExtra];
        else
          $extra = $DefaultExtra;       
        $extra = trim($extra);
        // Now get price for the chosen currency
        if ((count($costs==1)) && (is_numeric($costs[0])))
          $costs[0]=$DefaultCurrency."=".$costs[0];
        $cost = -1.00; // Leave as -1 if price not listed in chosen currency
        for ($j = 0;$j < count($costs);$j++)
        {
          if (strcasecmp(strtok($costs[$j], "="), $currency) == 0)
            $cost = strtok("=");
        }
      }
      if ($found == 1)
      {
        $description = str_replace("^", "", $desc);
        // Get file size if not defined
        // If file size set as 0 then try to get it
        if (($size == 0) && ($url != "NA"))
        {
          if (substr(trim(strtolower($url)),0,3)=="s3|")
          {
            $s = filesize_s3($url);
            if (is_numeric($s))
              $size = $s;        
          }
          else
          {
            $pos = strpos(strtolower($url), "http://");
            if (is_integer($pos))
            {
              $s = filesize_remote($url);
              if (is_integer($s))
                $size = $s;
            }
            else
              $size = @filesize($url);
          }  
        }
        return($found);
      }
    }
    // If auction if not matched (or not allowed) try matching auction title
    $query="SELECT * FROM ".$DbTableNameProducts;
    $mysql_result=mysql_query($query,$mysql_linklok);
    $found = 0;
    while($row=mysql_fetch_array($mysql_result,MYSQL_ASSOC))
    {
      $tid=$row[$DbProductsId];
      $desc=$row[$DbProductsDescription];
      $match = false;
      // No wildcard
      if ((substr($desc, 0, 1) != "^") && (substr($desc, strlen($desc)-1, 1) != "^"))
      {
        if (strcasecmp(trim($desc), trim($description)) == 0)
          $match = true;
      }
      // Wildcard at start and end
      if ((substr($desc, 0, 1) == "^") && (substr($desc, strlen($desc)-1, 1) == "^"))
      {
        $desc = str_replace("^", "", $desc);
        if (is_integer(strpos($description, $desc)))
          $match = true;
      }
      // Wildcard at start only
      if ((substr($desc, 0, 1) == "^") && (substr($desc, strlen($desc)-1, 1) != "^"))
      {
        $desc = str_replace("^", "", $desc);
        if (strcasecmp($desc, substr($description, strlen($description) - strlen($desc))) == 0)
          $match = true;
      }
      // Wildcard at end only
      if ((substr($desc, 0, 1) != "^") && (substr($desc, strlen($desc)-1, 1) == "^"))
      {
        $desc = str_replace("^", "", $desc);
        if (strcasecmp($desc, substr($description, 0, strlen($desc))) == 0)
          $match = true;
      }
      if ($match)
      {
        $found = 1;
        $id = $tid;
        if ($DbProductsPrice!="")
          $costs = explode(":", $row[$DbProductsPrice]);
        else
          $costs = explode(":", $DefaultPrice);        
        $url = $row[$DbProductsLocation];
        if ($DefaultLocation!="")          
        {
          $combos = explode(":", $url);
          if ((count($combos) == 1) || (substr(strtolower($url), 0, 5) == "http:") || (substr($url, 1, 2) == ":/"))
            $url = $DefaultLocation.$url;
        }                       
        $size = 0;
        if ($DbProductsExpiry!="")
          $expiry = $row[$DbProductsExpiry];
        else
          $expiry = $DefaultExpiry;  
        $expiry = trim($expiry);
        if ($DbProductsExtra!="")
          $extra = $row[$DbProductsExtra];
        else
          $extra = $DefaultExtra;       
        $extra = trim($extra);
        // Now get price for the chosen currency
        if ((count($costs==1)) && (is_numeric($costs[0])))
          $costs[0]=$DefaultCurrency."=".$costs[0];
        $cost = -1.00; // Leave as -1 if price not listed in chosen currency
        for ($j = 0;$j < count($costs);$j++)
        {
          if (strcasecmp(strtok($costs[$j], "="), $currency) == 0)
            $cost = strtok("=");
        }
        break;
      }
    }
  }
  // Get file size if not defined
  if ($found == 1)
  {
    // If file size set as 0 then try to get it
    if (($size == 0) && ($url != "NA"))
    {
      if (substr(trim(strtolower($url)),0,3)=="s3|")
      {
        $s = filesize_s3($url);
        if (is_numeric($s))
          $size = $s;        
      }
      else
      {
        $pos = strpos(strtolower($url), "http://");
        if (is_integer($pos))
        {
          $s = filesize_remote($url);
          if (is_integer($s))
            $size = $s;
        }
        else
          $size = @filesize($url);
      }  
    }
  }
  // If discount set then apply that
  if ((is_numeric($cost)) && (is_numeric($GlobalDiscount)))
  {
    $cost=$cost-$cost*($GlobalDiscount/100);
    $cost=sprintf("%01.2f", $cost);
  }  
  return($found);
}

function VerifyTransactionId($fn, $on)
{
  global $DbTableNameOrders, $mysql_linklok;
  if ($fn == "")
    return(1);
  if (strtolower($fn) == "mysql")  
  {
    $orderno=strtok($on,"-");
    $calltype=strtok("-");
    // Check for order processed in mysql table instead
    $query="SELECT * FROM ".$DbTableNameOrders." WHERE orderno=".quote_smart(trim($orderno));
    $mysql_result=mysql_query($query,$mysql_linklok);
    if ($row=mysql_fetch_array($mysql_result,MYSQL_ASSOC))
    {
      if (($calltype=="ipn") && ($row["ipn"]=="Y"))
        return(0);
      if (($calltype=="return") && ($row["`return`"]=="Y"))
        return(0);        
    }
    // Now update details that depend on whether called as IPN or return
    if ($calltype=="return")
      $Query = "UPDATE " . $DbTableNameOrders . " SET `return`='Y' WHERE orderno=" . quote_smart(trim($orderno));
    else  
      $Query = "UPDATE " . $DbTableNameOrders . " SET ipn='Y' WHERE orderno=" . quote_smart(trim($orderno));
    $mysql_result = mysql_query($Query, $mysql_linklok);
    return(1);
  }    
  if (is_writeable($fn) == false)
    return(1);
  if (is_readable($fn) == false)
    return(1);
  $fh = @fopen($fn, "r");
  if (!($fh))
    return(1);
  // Check each entry for a match
  $match = 0;
  while (!feof($fh))
  {
    $txid = fgets($fh, 255);
    if (substr($txid, 0, strlen($on)) == $on)
    {
      $match = 1;
      break;
    }
  }
  fclose($fh);
  if ($match == 1)
    return(0);
  $fh = @fopen($fn, "a");
  if (!($fh))
    return(1);
  flock($fh, 2);
  fputs($fh, "$on\n");
  fflush($fh);
  flock($fh, 3);
  fclose($fh);
  return(1);
}

function FriendlyExpiryTime($exp)
{
  if ($exp == 0)
    return("");
  if (($exp >= 1) && ($exp <= 59))
  {
    if ($exp == 1)
      return("$exp minute");
    else
      return("$exp minutes");
  }
  if (($exp >= 60) && ($exp <= 1440))
  {
    $hours = intval($exp / 60);
    $mins = $exp % 60;
    if ($hours == 1)
      $ret = $hours . " hour";
    else
      $ret = $hours . " hours";
    if ($mins > 0)
    {
      if ($mins == 1)
        $ret .= " & " . $mins . " minute";
      else
        $ret .= " & " . $mins . " minutes";
    }
    return($ret);
  }
  if ($exp >= 1441)
  {
    $days = intval($exp / 1440);
    $exp = $exp - ($days * 1440);
    $hours = intval($exp / 60);
    $mins = $exp % 60;
    if ($days == 1)
      $ret = $days . " day";
    else
      $ret = $days . " days";
    if ($hours > 0)
    {
      if ($mins == 0)
        $ret .= " &";
      if ($hours == 1)
        $ret .= " " . $hours . " hour";
      else
        $ret .= " " . $hours . " hours";
    }
    if ($mins > 0)
    {
      if ($mins == 1)
        $ret .= " & " . $mins . " minute";
      else
        $ret .= " & " . $mins . " minutes";
    }
    return($ret);
  }
}

function FriendlyFileSize($sz)
{
  if ($sz == 0)
    return("Unknown size");
  if ($sz <= 1023)
    return($sz . " Bytes");
  if (($sz >= 1024) && ($sz <= 1048575))
  {
    $sz = intval($sz / 1024);
    return($sz . " KB");
  }
  if ($sz >= 1048576)
  {
    $sz = $sz / 1048576;
    $sz = intval($sz * 100) / 100;
    return($sz . " MB");
  }
}

function ValidIP($ip)
{
  if ($ip != "")
  {
    $parts = explode(".", $ip);
    if (count($parts) == 4)
    {
      for ($k = 0;$k < 4;$k++)
      {
        if ((!is_numeric($parts[$k])) || ($parts[$k] < 0) || ($parts[$k] > 255))
          return(false);
      }
      return(true);
    }
  }
  return(false);
}

function CustomError($Template, $msg)
{
  if ($Template == "")
    return(0);
  $ext = fileextension($Template);
  if ($ext == ".php")
  {
    ob_start();
    include $Template;
    $page = ob_get_contents(); 
    ob_end_clean();    
  }
  else
  {
    if (!($fh = @fopen($Template, "r")))
      return(0);
    $page = fread ($fh, 200000);
    fclose($fh);
  }
  $page = str_replace("!!!message!!!", $msg, $page);
  print $page;
  return(1);
}

function ShowMessage($Template, $msg, $em)
{
  global $EmailHeaderNoSlashR, $SellerCompany, $SellerEmail, $ExtraMailParam;
  if ($em != "")
  {
    $subject = "Linklok Paypal generated an error message";
    $mailBody = "The following error message was generated for transaction number " . $GLOBALS['txn_id'] . "\n\n";
    $mailBody .= $msg . "\n";
    $mailBody .= "The following variables were received by Linklok.\n\n";
    if (!empty($_GET))
    {
      reset($_GET);
      while (list($namepair, $valuepair) = each($_GET))
      {
        $$namepair = $valuepair;
        $mailBody .= "GET " . $namepair . "=" . $valuepair . "\n";
      }
    }
    if (!empty($_POST))
    {
      reset($_POST);
      while (list($namepair, $valuepair) = each($_POST))
      {
        $$namepair = $valuepair;
        $mailBody .= "POST " . $namepair . "=" . $valuepair . "\n";
      }
    }
    reset($_GET);
    reset($_POST);
    SendEmailOut($em, $SellerEmail, $SellerCompany, $subject, $mailBody, "N");
  }
  if (0 == CustomError($Template, $msg))
  {
    print ("<HTML>\n");
    print ("<HEAD>\n");
    print ("<TITLE>Linklok</TITLE>\n");
    print ("</HEAD>\n");
    print ("<BODY>\n");
    print("$msg<BR>");
    print ("</BODY>\n");
    print ("</HTML>\n");
  }
}

function Debug($msg, $em)
{
  global $EmailHeaderNoSlashR, $SellerCompany, $SellerEmail, $calledasreturn;
  if ($em != "")
  {
    $subject = "Linklok Paypal debug data from $SellerCompany";
    $mailBody = "The following debug data was generated\n\n";
    $mailBody .= $msg . "\n\n";
    $mailBody .= "IP=" . $_SERVER['REMOTE_ADDR'] . "\n\n";
    $mailBody .= "calledasreturn=" . $calledasreturn . "\n\n";
    $mailBody .= "The following variables were received by Linklok Paypal.\n\n";
    if (!empty($_GET))
    {
      reset($_GET);
      while (list($namepair, $valuepair) = each($_GET))
      {
        $$namepair = $valuepair;
        $mailBody .= "GET " . $namepair . "=" . $valuepair . "\n";
      }
    }
    if (!empty($_POST))
    {
      reset($_POST);
      while (list($namepair, $valuepair) = each($_POST))
      {
        $$namepair = $valuepair;
        $mailBody .= "POST " . $namepair . "=" . $valuepair . "\n";
      }
    }
    reset($_GET);
    reset($_POST);
    SendEmailOut($em, $SellerEmail, $SellerCompany, $subject, $mailBody, "N");
  }
}

function xgetallheaders()
{
  $headers = array();
  while (list($key, $value) = each ($_SERVER))
  {
    if (strncmp($key, "HTTP_", 5) == 0)
    {
      $key = strtr(ucwords(strtolower(strtr(substr($key, 5), "_", " "))), " ", "-");
      $headers[$key] = $value;
    }
  }
  return $headers;
}
function fileextension($fname)
{
  if ($fname == "")
    return("");
  $pos = strrpos($fname, ".");
  if (is_integer($pos))
  {
    $ext=strtolower(substr($fname, $pos));
    // Now remove any query that may be there for a .php file
    $pos = strpos($ext, "?");
    if (is_integer($pos))
    {
      $ext=substr($ext,0,$pos);
    }
    return($ext);
  }    
  return("");
}
function filename($fname)
{
  if ($fname == "")
    return("");
  // First see if link is for S3
  if (substr(trim(strtolower($fname)),0,3)=="s3|")
  {
    $pos=strrpos($fname, "|");
    if (is_integer($pos))
      $fname = substr($fname, $pos + 1);
  }
  $pos1 = strrpos($fname, "/");
  $pos2 = strrpos($fname, "\\");
  if ($pos1 === false)
    $pos1 = -1;
  if ($pos2 === false)
    $pos2 = -1;
  if ($pos1 > $pos2)
    $pos = $pos1;
  else
    $pos = $pos2;
  if ($pos > -1)
  {
    $name = substr($fname, $pos + 1);
    return($name);
  }
  return($fname);
}

function filesize_remote($url, $timeout = 2)
{
  $url = parse_url($url);
  if ($fp = @fsockopen($url['host'], ($url['port'] ? $url['port'] : 80), $errno, $errstr, $timeout))
  {
    fwrite($fp, 'HEAD ' . $url['path'] . $url['query'] . " HTTP/1.0\r\nHost: " . $url['host'] . "\r\n\r\n");
    @stream_set_timeout($fp, $timeout);
    while (!feof($fp))
    {
      $size = fgets($fp, 4096);
      if (stristr($size, 'Content-Length') !== false)
      {
        $size = trim(substr($size, 16));
        break;
      }
    }
    fclose ($fp);
  }
  return is_numeric($size) ? intval($size) : false;
}

function quote_smart($value)
{    
  if (llipn_get_magic_quotes_gpc())
    $value = stripslashes($value);
  if (function_exists('mysql_real_escape_string'))
    $value = "'" . mysql_real_escape_string($value) . "'";
  else  
    $value="'".addslashes($value)."'";      
  return $value;
}

function SendEmailOut($toemail, $fromemail, $fromname, $subject, $mailBody, $htmlformat)
{
  global $EmailHeaderNoSlashR, $ExtraMailParam, $ErrorTemplate, $ErrorEmail, $UsePHPmailer, $UsePearMail;
  global $Custom_Mail_Headers;
  // Remove any comma in from name
  $fromname = str_replace(",", " ", $fromname);
  // Handle multiple email addresses
  $sendtoemail=explode(",",$toemail);  
  // If phpmailer setup then use it otherwise handle with PHP mail() function
  if ($UsePHPmailer == 1)
  {
    global $EmailUsername, $EmailPassword, $EmailServer, $EmailPort, $EmailAuth, $EmailServerSecurity;
    if ($EmailPort=="")
      $EmailPort=25;
    if ($EmailAuth=="")
      $EmailAuth=1;
    require_once("class.phpmailer.php");
    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Host = $EmailServer;
		$mail->Port = $EmailPort;    
    if ($EmailAuth=="0")				
  		$mail->SMTPAuth = false;
		else
  		$mail->SMTPAuth = true;
  	if ($EmailServerSecurity!="")			
  	  $mail->SMTPSecure = $EmailServerSecurity;
    $mail->Username = $EmailUsername;
    $mail->Password = $EmailPassword;
    $mail->From = $fromemail;
    $mail->FromName = $fromname;
    for ($k=0; $k<count($sendtoemail); $k++)
      $mail->AddAddress($sendtoemail[$k]);
    if ($htmlformat == "Y")
      $mail->IsHTML(true);
    else
      $mail->IsHTML(false);
    $mail->Subject = $subject;
    $mail->Body = $mailBody;
    if ($Custom_Mail_Headers!="")
    {
      $cushd=explode("\r\n",$Custom_Mail_Headers);
      for ($k=0;$k<count($cushd);$k++)
      {
        if ($cushd[$k]!="")      
          $mail->AddCustomHeader($cushd[$k]);
      }  
    }
    $mail->Send();
    if ($mail->isError())
    {
      ShowMessage($ErrorTemplate, "Email send using PHPmailer failed", $ErrorEmail);
      exit;
    }
    return;
  }
  if ($UsePearMail == 1)
  {
    global $EmailUsername, $EmailPassword, $EmailServer;
    $headers = array ('From' => $fromemail,
                      'To' => $sendtoemail[0],
                      'Subject' => $subject);
    $smtp = Mail::factory('smtp',array ('host' => $EmailServer,
    'auth' => true,
    'username' => $EmailUsername,
    'password' => $EmailPassword));
    $mail = $smtp->send($sendtoemail[0], $headers, $mailBody);
    if (PEAR::isError($mail))
    {
      ShowMessage($ErrorTemplate, "Email send using Pear Mail function failed", $ErrorEmail);
      exit;
    }  
    return;
  }
  // If still here then use PHP mail() function  
  $headers = "From: " . $fromname . " <" . $fromemail . ">\r\n";
  $headers.= "Reply-To: " . $fromname . " <" . $fromemail . ">\r\n";
  $headers.= "MIME-Version: 1.0\r\n";
  if ($htmlformat=="Y")
  {
    $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
    $headers .= "Content-Transfer-Encoding: base64\r\n";	    
    $mailBody=chunk_split(base64_encode($mailBody));
  }
  else
    $headers .= "Content-type: text/plain\r\n";
  if ($Custom_Mail_Headers!="")
    $headers .= $Custom_Mail_Headers;    
  if ($EmailHeaderNoSlashR == 1)
    $headers = str_replace("\r", "", $headers);
  for ($k=0; $k<count($sendtoemail); $k++)
  {
    if ($ExtraMailParam != "")
      $sent = mail($sendtoemail[$k], $subject, $mailBody, $headers, $ExtraMailParam);
    else
      $sent = mail($sendtoemail[$k], $subject, $mailBody, $headers);
    if ($sent == false)
    {
      ShowMessage($ErrorTemplate, "Email send using mail function failed", $ErrorEmail);
      exit;
    }
  }
}

function EmailDownloadNotify($fname, $in, $desc, $ordnum, $ipaddr, $expiry, $email)
{
  global $NotifyDownloadEmail, $SellerCompany, $SellerEmail;
  $subject = "Linklok Paypal Download of $in from order $ordnum - $email";
  $mailBody = "Download notification.\n\n";
  $mailBody .= "Order Number : " . $ordnum . "\n";
  $mailBody .= "Email : ".$email."\n";   
  $mailBody .= "Item Number : " . $in . "\n";
  $mailBody .= "Description : " . $desc . "\n";
  $mailBody .= "Filename : " . $fname . "\n";
  $mailBody .= "IP : " . $ipaddr . "\n";
  $mailBody .= "Download time : " . date("d M Y H:i:s") . "\n";
  $mailBody .= "Expiry time : " . date("d M Y H:i:s", $expiry) . "\n";
  $mailBody .= "User agent : " . $_SERVER['HTTP_USER_AGENT'] . "\n";
  $mailBody .= "\n";
  SendEmailOut($NotifyDownloadEmail, $SellerEmail, $SellerCompany, $subject, $mailBody, "N");
  return;
}

function GetVarFromString($s, $v)
{
  global $LinkKey;
  $s = rawurldecode($s);
  $s = base64_decode($s);
  $hash = strtok($s, "^");
  $s = strtok("^");
  if ($hash != md5($LinkKey . $s))
    return("");
  $vars = explode(",", $s);
  $value = "";
  for ($j = 0;$j < count($vars);$j++)
  {
    $var = explode("=", $vars[$j]);
    if (strtoupper($var[0]) == $v)
      $value = $var[1];
  }
  return($value);
}

function GetSerialNumber($snlist)
{
  global $EmailHeaderNoSlashR, $SellerCompany, $SellerEmail, $ExtraMailParam;
  $warnwhenleft = 5;
  $serialnumber = "";
  if (($snlist == "") || (snlist == "NOTREQUIRED"))
    return("");
  if (is_writeable($snlist) == false)
    return($serialnumber);
  $fh = @fopen($snlist, "r+b");
  flock($fh, 2);
  while (!feof($fh))
  {
    $fpoint = ftell($fh);
    $lne = fgets($fh, 255);
    $fpoint2 = ftell($fh);
    $lne = trim($lne);
    if (($lne != str_repeat("*", strlen($lne))) && (strlen($lne) > 2))
    {
      $blank = str_repeat("*", strlen($lne));
      fseek($fh, $fpoint);
      fputs($fh, $blank);
      fseek($fh, $fpoint2);
      $serialnumber = $lne;
      break;
    }
  }
  $countleft = 0;
  while (!feof($fh))
  {
    $lne = fgets($fh, 255);
    $lne = trim($lne);
    if (($lne != str_repeat("*", strlen($lne))) && (strlen($lne) > 2))
      $countleft++;
    if ($countleft > $warnwhenleft)
      break;
  }
  flock($fh, 3);
  fclose($fh);
  if ($countleft <= $warnwhenleft)
  {
    $subject = "Linklok serial number list warning";
    $mailBody = "Warning the following serial number list is nearly finished.\n\n";
    $mailBody .= $snlist . "\n";
    SendEmailOut($SellerEmail, $SellerEmail, $SellerCompany, $subject, $mailBody, "N");
  }
  return($serialnumber);
}

function softwarepassport($data, $option_selection1, $option_selection2)
{
  global $first_name, $last_name, $payer_business_name;
  if (($data == "") || ($data == "NOTREQUIRED"))
    return("|");
  $level = substr($data, 0, 2);
  $ekey = substr($data, 2);
  if ($option_selection2 == "")
  {
    if (($first_name != "") && ($last_name != ""))
      $licensename = $first_name . " " . $last_name;
    else
      $licensename = $payer_business_name;
  }
  else
    $licensename = $option_selection2;
  if ($option_selection1 != "")
    $fingerprint = $option_selection1;
  else
    $fingerprint = 0;
  $code = CreateKey($level, $ekey, $licensename, $fingerprint);
  $code = $licensename . "|" . $code;
  return (string)$code;
}

function ClickatellSMS()
{
  global $Clickatell_api_id, $Clickatell_user, $Clickatell_password, $Clickatell_to;
  global $txn_id, $payer_email, $num_cart_items,$mc_gross, $mc_currency, $payment_type;
  if (($Clickatell_api_id!="") && ($Clickatell_user!="") && ($Clickatell_password!="") && ($Clickatell_to!=""))
	{
	  $message ="Paypal order ".$txn_id."\n";
	  $message.=$payer_email."\n";
	  $message.=$mc_currency." ".$mc_gross."\n";
	  $message.=$payment_type."\n"; 
	  if ($num_cart_items > 0)
    {
      $itemsordered = "";
      for ($k = 1;$k <= $num_cart_items;$k++)
      {
        $t = "item_number" . $k;
        $pid = $GLOBALS[$t];
        $itemsordered .= $pid."\n";
      }
    }
    else
    {
      $itemsordered = $GLOBALS['item_number'];
    }
    $message.=$itemsordered;
    if (strlen($message)>160)
      $message=substr($message,0,160);
    $url="http://api.clickatell.com/http/sendmsg?api_id=".$Clickatell_api_id."&user=".$Clickatell_user."&password=".$Clickatell_password."&to=".$Clickatell_to."&from=".$Clickatell_to."&text=".urlencode($message);  
    if (function_exists('curl_init'))
    {
       $ch = curl_init();
       curl_setopt ($ch, CURLOPT_URL, $url);
       curl_setopt ($ch, CURLOPT_HEADER, false);
       curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
       if ((strtolower(@ini_get("safe_mode")) != 'on') && (@ini_get("safe_mode") != '1') && (@ini_get("open_basedir") == ''))
         curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
       curl_setopt ($ch, CURLOPT_TIMEOUT, 30);
       curl_exec($ch);
       curl_close ($ch);    
    }
    else
    {
  	  $fh=fopen($url,"rb");
  	}
	}  
}

function ClickLocker($url,$name,$email)
{
  if (($url=="") || ($name=="") || ($email==""))
    return ("Invalid user details");  
  if (function_exists('curl_init'))
  {
     $ch = curl_init();
     curl_setopt ($ch, CURLOPT_URL, $url);
     curl_setopt ($ch, CURLOPT_HEADER, false);
     curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
     if ((strtolower(@ini_get("safe_mode")) != 'on') && (@ini_get("safe_mode") != '1') && (@ini_get("open_basedir") == ''))
       curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
     curl_setopt ($ch, CURLOPT_TIMEOUT, 30);
     $buf=curl_exec($ch);
     curl_close ($ch);
  }
  else
  {
    if (!($fh=fopen($url,"rb")))
    {
      return("Could not contact Clickloker");
    }
    $buf="";
  	do
  	{
     $buf1 = fread($fh, 100000);
     if (strlen($buf1) == 0)
     {
         break;
   	 }
     $buf .= $buf1;
  	}
   	while (true);
    fclose($fh);
  }
  $pos=strpos($buf,"name=receipt value=");
  $pos2=strpos($buf,">",$pos);
  $receipt=substr($buf,$pos+19,$pos2-$pos-19);
  $pos=strpos($buf,"name=aid value=");
  $pos2=strpos($buf,">",$pos);
  $aid=substr($buf,$pos+15,$pos2-$pos-15);
  $pos=strpos($buf,"name=pid value=");
  $pos2=strpos($buf,">",$pos);
  $pid=substr($buf,$pos+15,$pos2-$pos-15);
  $pos=strpos($buf,"name=vemail value=");
  $pos2=strpos($buf,">",$pos);
  $vemail=substr($buf,$pos+18,$pos2-$pos-18);
  $pos=strpos($buf,"name=vfullname value=\"");
  $pos2=strpos($buf,"\">",$pos);
  $vfullname=substr($buf,$pos+22,$pos2-$pos-22);
  $pos=strpos($buf,"name=stamp value=\"");
  $pos2=strpos($buf,"\">",$pos);
  $stamp=substr($buf,$pos+18,$pos2-$pos-18);
  $pos=strpos($buf,"name=after value=");
  $pos2=strpos($buf,">",$pos);
  $after=substr($buf,$pos+17,$pos2-$pos-17);
  $query="?receipt=".$receipt."&aid=".$aid."&pid=".$pid."&vemail=".urlencode($vemail)."&vfullname=".urlencode($vfullname)."&stamp=".urlencode($stamp)."&after=".$after."&name=".urlencode($name)."&email=".urlencode($email);
  if (function_exists('curl_init'))
  {
     $ch = curl_init();
     curl_setopt ($ch, CURLOPT_URL, "http://www.clickLocker.com/thankyou2.php".$query);
     curl_setopt ($ch, CURLOPT_HEADER, false);
     curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true); 
     if ((strtolower(@ini_get("safe_mode")) != 'on') && (@ini_get("safe_mode") != '1') && (@ini_get("open_basedir") == ''))
       curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
     curl_setopt ($ch, CURLOPT_TIMEOUT, 30);
     $buf=curl_exec ($ch);
     curl_close ($ch);
  }
  else
  {
    if (!($fh=fopen("http://www.clickLocker.com/thankyou2.php".$query,"rb")))
    {
      return("Could not contact Clickloker");
    }
  }
  return($receipt);
}

function storeordermysql()
{
  global $calledasreturn,$manualorder,$parent_txn_id, $memo, $tax, $mc_shipping, $mc_handling, $mc_fee, $mc_currency;
  global $payment_status, $txn_type, $num_cart_items, $option_selection1, $option_selection2;
  global $txn_id, $first_name, $last_name, $payer_business_name, $address_street, $address_city;
  global $address_state, $address_zip, $address_country, $payer_email, $mc_gross, $custom, $payment_type;
  global $for_auction, $auction_buyer_id, $auction_closing_date, $clientipaddr, $invoice;
  global $mysql_linklok, $DbTableNameOrders;
  if (($payment_status == "Completed") && ($txn_type != "reversal"))
  {
    $itemsordered = "";
    $itemnamesordered="";
    $itemqtysordered="";
    $itempricesordered="";
    if ($num_cart_items > 0)
    {
      for ($k = 1;$k <= $num_cart_items;$k++)
      {
        $t = "item_number" . $k;
        $pid = $GLOBALS[$t];
        if ($itemsordered != "")
          $itemsordered .= "|";
        $itemsordered .= $pid;
        $t = "item_name" . $k;
        $pid = $GLOBALS[$t];
        if ($itemnamesordered != "")
          $itemnamesordered .= "|";
        $itemnamesordered .= $pid;
        $t = "quantity" . $k;
        $pid = $GLOBALS[$t];
        if ($pid=="")
          $pid="1";
        if ($itemqtysordered != "")
          $itemqtysordered .= "|";
        $itemqtysordered .= $pid;
        $t = "mc_gross_" . $k;
        $pid = $GLOBALS[$t];
        if ($pid=="")
          $pid="0.00";
        if ($itempricesordered != "")
          $itempricesordered .= "|";
        $itempricesordered .= $pid;
      }
    }
    else
    {
      $itemsordered = $GLOBALS['item_number'];
      $itemnamesordered = $GLOBALS['item_name'];
      $itemqtysordered = $GLOBALS['quantity'];
      $itempricesordered = $GLOBALS['mc_gross'];
    }
    $Query = "INSERT INTO " . $DbTableNameOrders . " (";
    $Query.="orderno,";
    $Query.="parentorderno,";
    $Query.="datetime,";
    $Query.="processor,";
    $Query.="name,";
    $Query.="company,";
    $Query.="street1,";
    $Query.="street2,";
    $Query.="city,";
    $Query.="state,";
    $Query.="zip,";
    $Query.="country,";
    $Query.="email,";
    $Query.="telephone,";
    $Query.="fax,";
    $Query.="shipname,";
    $Query.="shipcompany,";
    $Query.="shipstreet1,";
    $Query.="shipstreet2,";
    $Query.="shipcity,";
    $Query.="shipstate,";
    $Query.="shipzip,";
    $Query.="shipcountry,";
    $Query.="shiptelephone,";
    $Query.="message,";
    $Query.="invoice,";    
    $Query.="ip,";
    $Query.="referrerfirst,";
    $Query.="entryfirst,";
    $Query.="referrernow,";
    $Query.="entrynow,";                
    $Query.="total,";
    $Query.="tax,";    
    $Query.="shipping,";
    $Query.="fee,";
    $Query.="currency,";
    $Query.="method,";
    $Query.="custom1,";
    $Query.="custom2,";
    $Query.="custom3,";
    $Query.="custom4,";
    $Query.="discount,";
    $Query.="voucher,";
    $Query.="voucherval,";
    $Query.="numitems,";
    $Query.="itemnumber,";
    $Query.="itemname,";
    $Query.="quantity,";
    $Query.="price,";
    $Query.="auction,"; 
    $Query.="auction_buyer_id,";
    $Query.="auction_closing_date,";
    $Query.="orderstatus";
    
    $Query.=") VALUES(";
    
    $Query.=quote_smart($txn_id).",";
    $Query.=quote_smart($parent_txn_id).",";
    $Query.=quote_smart(date("Y-n-d H:i:s",time())).",";
    if ($manualorder)
      $Query.=quote_smart("MANUAL-PAYPAL").",";
    else 
      $Query.=quote_smart("PAYPAL").",";      
    $Query.=quote_smart($first_name." ".$last_name).",";
    $Query.=quote_smart($payer_business_name).",";
    $Query.=quote_smart($address_street).",";
    $Query.=quote_smart("").",";   // Steet 2 not used in Paypal
    $Query.=quote_smart($address_city).",";
    $Query.=quote_smart($address_state).",";
    $Query.=quote_smart($address_zip).",";
    $Query.=quote_smart($address_country).",";
    $Query.=quote_smart($payer_email).",";
    $Query.=quote_smart("").",";  // Telephone not used in Paypal
    $Query.=quote_smart("").",";  // Fax not used in Paypal
    $Query.=quote_smart("").",";  // shipping address not used in Paypal
    $Query.=quote_smart("").",";  // shipping address not used in Paypal
    $Query.=quote_smart("").",";  // shipping address not used in Paypal
    $Query.=quote_smart("").",";  // shipping address not used in Paypal
    $Query.=quote_smart("").",";  // shipping address not used in Paypal
    $Query.=quote_smart("").",";  // shipping address not used in Paypal
    $Query.=quote_smart("").",";  // shipping address not used in Paypal
    $Query.=quote_smart("").",";  // shipping address not used in Paypal
    $Query.=quote_smart("").",";  // shipping telephone not used in Paypal
    $Query.=quote_smart($memo).",";
    $Query.=quote_smart($invoice).",";    
    if ($clientipaddr!="0.0.0.0")
      $Query.=quote_smart($clientipaddr).",";
    else 
      $Query.=quote_smart("").",";
    $Query.=quote_smart("").",";
    $Query.=quote_smart("").",";
    $Query.=quote_smart("").",";
    $Query.=quote_smart("").",";      
    $Query.=$mc_gross.",";
    $Query.=$tax.",";
    $Query.=$mc_shipping+$mc_handling.",";
    if(is_numeric ($mc_fee)) { $Query.=$mc_fee.","; } else { $Query.="0.00,"; }
    $Query.=quote_smart($mc_currency).",";
    if ($manualorder)  
      $Query.=quote_smart("MANUAL").",";
    else 
      $Query.=quote_smart($payment_type).",";      
    $Query.=quote_smart($option_selection1).",";
    $Query.=quote_smart($option_selection2).",";
    $Query.=quote_smart($custom).",";
    $Query.=quote_smart("").",";   // Custom 4 not used in Paypal
    $Query.="0.00".",";  // discount value not used in Paypal
    $Query.=quote_smart("").",";  // voucher
    $Query.="0.00".",";  // voucher value not used in Paypal
    if ($num_cart_items>0)
      $Query.=$num_cart_items.",";
    else  
      $Query.="1,";
    $Query.=quote_smart($itemsordered).",";
    $Query.=quote_smart($itemnamesordered).",";
    $Query.=quote_smart($itemqtysordered).",";
    $Query.=quote_smart($itempricesordered).",";
    if ($for_auction=="true")
      $Query.=quote_smart("Y").",";
    else 
      $Query.=quote_smart("N").",";
    $Query.=quote_smart($auction_buyer_id).",";
    $Query.=quote_smart($auction_closing_date).",";
    $Query.=quote_smart("OK");

    $Query.=")";
    $mysql_result = mysql_query($Query, $mysql_linklok);
    // Now update ip address which may only be know if called as return
    if ($calledasreturn)
    {
      $clip=$clientipaddr;
      if ($clientipaddr=="0.0.0.0")
        $clip=$_SERVER['REMOTE_ADDR'];
      $Query = "UPDATE " . $DbTableNameOrders . " SET ip='".$clip."' WHERE orderno='" . $txn_id . "'";
      $mysql_result = mysql_query($Query, $mysql_linklok);
    }  
  }
  // If merchant refunds order
  if ($payment_status == "Refunded")
  {
    $Query = "UPDATE " . $DbTableNameOrders . " SET orderstatus='Refunded' WHERE orderno='" . $parent_txn_id . "'";
    $mysql_result = mysql_query($Query, $mysql_linklok);
  }
  // If Paypal reverses order
  if (($payment_status == "Completed") && ($txn_type == "reversal"))
  {
    $Query = "UPDATE " . $DbTableNameOrders . " SET orderstatus='Reversed' WHERE orderno='" . $parent_txn_id . "'";
    $mysql_result = mysql_query($Query, $mysql_linklok);
  }
  // If Paypal cancels reversed order (i.e. you won dispute)
  if (($payment_status == "Cancelled") && ($txn_type == "reversal"))
  {
    $Query = "UPDATE " . $DbTableNameOrders . " SET orderstatus='OK' WHERE orderno='" . $parent_txn_id . "'";
    $mysql_result = mysql_query($Query, $mysql_linklok);
  }
}

function storeorderllprodsmysql($ProdId, $ProdDesc)
{
  global $txn_id, $mysql_linklok, $DbTableNameOrders;
  $llitemnumber = "";
  $llitemname="";
  for ($k = 0;$k < count($ProdId);$k++)
  {
    if ($llitemnumber != "")
      $llitemnumber .= "|";
    $llitemnumber .= $ProdId[$k];
    if ($llitemname != "")
      $llitemname .= "|";
    $llitemname .= $ProdDesc[$k];
  }
  $Query = "UPDATE " . $DbTableNameOrders . " SET llnumitems=".count($ProdId).", llitemnumber=".quote_smart($llitemnumber).", llitemname=".quote_smart($llitemname)." WHERE orderno=" . quote_smart($txn_id);
  $mysql_result = mysql_query($Query, $mysql_linklok); 
}

function xfpassthru($file)
{
 global $downloadbuffer;
 if ($downloadbuffer>0)
 {
   @set_time_limit(86400); 
   while(!feof($file))
   {
      print(fread($file, $downloadbuffer));
      ob_flush();
      flush();
      sleep(1);
   }
   fclose($file);
 }
 else
   @fpassthru($file);
}

function callurl($url)
{
  InsertVariables($url);
  $url=str_replace("@","%40", $url);
  $url=str_replace(" ","%20", $url);
  if (function_exists('curl_init'))
  {
     $ch = curl_init();
     curl_setopt ($ch, CURLOPT_URL, $url);
     curl_setopt ($ch, CURLOPT_HEADER, false);
     curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
     if ((strtolower(@ini_get("safe_mode")) != 'on') && (@ini_get("safe_mode") != '1') && (@ini_get("open_basedir") == ''))
       curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
     curl_setopt ($ch, CURLOPT_TIMEOUT, 30);
     $buf=curl_exec($ch);
     curl_close ($ch);
  }
  else
  {
    if (!($fh=fopen($url,"rb")))
    {
      return("Could not contact page");
    }
    $buf="";
  	do
  	{
     $buf1 = fread($fh, 100000);
     if (strlen($buf1) == 0)
     {
         break;
   	 }
     $buf .= $buf1;
  	}
   	while (true);
    fclose($fh);
  }
  return ($buf);
}

function acrolok($filename,$email,$secretkey)
{
  $pos=strpos($filename,".");
  if (is_integer($pos))
    $filename=substr($filename,0,$pos);
  $primarykey=strtoupper(md5($filename.$email));
  $fileid=strtoupper(md5($filename));
  $secondarykey=strtoupper(md5($fileid.$secretkey));
  $serialnumberstring=$primarykey."-".$secondarykey;
  return($serialnumberstring);  
}

function Jalbum($jaid)
{
  // Handle item number sent from Jalbum such as jaalbum/SDC10006 (IDfield)
  global $Jalbum, $JalbumOptionId, $JalbumExtension, $Test, $manualorder;
  if (($manualorder==true) || ($Test=="Y"))
    return($jaid);
  $id=$jaid;
  // First get the shopping option id.
  $pos=strrpos($id,"(");
  $pos2=strrpos($id,")");
  if ((is_integer($pos)) && (is_integer($pos2)))
  {
    $fieldlabel=substr($id,$pos+1,$pos2-$pos-1);
    $id=trim(substr($id,0,$pos));
  }
  else
  {
    // Not a Jalbum item  
    return($jaid);
  }
  // Get just the filename
  $pos=str_replace("\\","/", $id);
  $pos=strrpos($id,"/");
  if (is_integer($pos))
    $id=substr($id,$pos+1);    
  // See if field label is flagged for download
  $jalbummatch=false;
  for ($k=0;$k<count($JalbumOptionId);$k++)
  {
    if ($JalbumOptionId[$k]==$fieldlabel)
      $jalbummatch=true;
  }
  // If Jalbum item but not for download then return filename without extension
  if ($jalbummatch==false)
    return($id);
  // Handle as download so add file extension
  $id.=$JalbumExtension;
  return($id);
}

function get_s3_url($location,$expires,$operation="GET")
{
  // Split into access key id, secret access key, bucket , filename
  $parts=explode("|",$location);
  $accesskeyid=trim($parts[1]);
  $secretaccesskey=trim($parts[2]);
  $bucket=trim($parts[3]);
  $filename=trim($parts[4]);
  // Cleanup filename
  $filename = rawurlencode($filename);
  $filename = str_replace('%2F', '/', $filename);
  // Make path to use
  $path = $bucket.'/'.$filename;
  // Make signature
  $strtosign =$operation ."\n"."\n"."\n".$expires ."\n"."/$path";
  $hash=hmacsha1($secretaccesskey,$strtosign);
  $signature=hex2b64($hash);
  $signature = urlencode($signature);
  $url = sprintf('http://%s.s3.amazonaws.com/%s?AWSAccessKeyId=%s&Expires=%u&Signature=%s',$bucket, $filename,$accesskeyid, $expires, $signature);
  return($url); 
}
 
function filesize_s3($location)
{
  global $ServerTimeAdjust;
  $url=get_s3_url($location,time()+$ServerTimeAdjust,"GET");
  $header=@get_headers($url,1);
  if (is_numeric($header['Content-Length']))
    return ($header['Content-Length']);
  return ("Unknown");  
}

function hmacsha1($key,$data)
{
  $blocksize=64;
  $hashfunc='sha1';
  if (strlen($key)>$blocksize)
      $key=pack('H*', $hashfunc($key));
  $key=str_pad($key,$blocksize,chr(0x00));
  $ipad=str_repeat(chr(0x36),$blocksize);
  $opad=str_repeat(chr(0x5c),$blocksize);
  $hmac = pack(
              'H*',$hashfunc(
                  ($key^$opad).pack(
                      'H*',$hashfunc(
                          ($key^$ipad).$data
                      )
                  )
              )
          );
  return bin2hex($hmac);
}

function hex2b64($str)
{
  $raw = '';
  for ($i=0; $i < strlen($str); $i+=2) {
          $raw .= chr(hexdec(substr($str, $i, 2)));
  }
  return base64_encode($raw);
}

function sendKunaki($user,$pass,$mode,$shippingmethod,$name,$company,$address1,$address2,$city,$state,$zip,$country,$products,$quantities)
{
  if ($mode=="")
    $mode="Live";
  $name=trim($name);
  $company=trim($company);  
  $address1=trim($address1);
  $address2=trim($address2);
  $city=trim($city);
  $state=getStateCode($state);
  $zip=trim($zip);
  $country=getUniformCountryName($country); 
  $post_string  = "<ShippingOptions>\n";
  $post_string .= "<Country>$country</Country>\n"; 
  $post_string .= "<State_Province>$state</State_Province>\n"; 
  $post_string .= "<PostalCode>$zip</PostalCode>\n";
  for ($k=0;$k<count($products);$k++)
  {  
    $post_string .= "<Product>\n";  
    $post_string .= "<ProductId>".$products[$k]."</ProductId>\n";  
    $post_string .= "<Quantity>".$quantities[$k]."</Quantity>\n";  
    $post_string .= "</Product>\n";  
  }
  $post_string .= "</ShippingOptions>\n";  
  $data=XMLhandler("http://Kunaki.com","/XMLService.ASP",$post_string);
  $xml_parser = xml_parser_create();
  xml_parser_set_option($xml_parser,XML_OPTION_CASE_FOLDING,0);
  xml_parser_set_option($xml_parser,XML_OPTION_SKIP_WHITE,1);
  xml_parse_into_struct($xml_parser, $data, $vals, $index);
  xml_parser_free($xml_parser);
  // Get any error code and message
  $errorcode=-1;
  $errorcode="";
  for ($k=0;$k<count($vals);$k++)
  {
    $entry=$vals[$k];
    if ($entry['tag']=="ErrorCode")
      $errorcode=$entry['value'];
    if ($entry['tag']=="ErrorText")
      $errortext=$entry['value'];
  }
  if ($errorcode==0)
  {
    // Get shipping descriptions and prices
    for ($k=0;$k<count($vals);$k++)
    {
      $entry=$vals[$k];
      if ($entry['tag']=="Description")
        $shippingarray[]=$entry['value'];
      if ($entry['tag']=="Price")
        $pricearray[]=$entry['value'];
    }    
    // Determine which shipping option to use
    // 0=Cheapest, 1=Most expensive    
    $shippingprice=0.00;
    $shippingdescription="";    
    if ($shippingmethod==0)
    {
      $shippingprice=1000000.00;
      $shippingdescription="";
      for ($k=0;$k<count($pricearray);$k++) 
      {
        if ($pricearray[$k]<$shippingprice)
        {
          $shippingprice=$pricearray[$k];
          $shippingdescription=$shippingarray[$k];
        }
      }
    }    
    if ($shippingmethod==1)
    {
      $shippingprice=0.00;
      $shippingdescription="";
      for ($k=0;$k<count($pricearray);$k++) 
      {
        if ($pricearray[$k]>$shippingprice)
        {
          $shippingprice=$pricearray[$k];
          $shippingdescription=$shippingarray[$k];
        }
      }
    }    
    $post_string =  "<Order>\n";
    $post_string .= "<UserId>$user</UserId>\n";
    $post_string .= "<Password>$pass</Password> \n";
    $post_string .= "<Mode>$mode</Mode>\n";
    $post_string .= "<Name>$name</Name>\n"; 
    $post_string .= "<Company>$company</Company>\n"; 
    $post_string .= "<Address1>$address1</Address1>\n"; 
    $post_string .= "<Address2>$address2</Address2>\n"; 
    $post_string .= "<City>$city</City>\n"; 
    $post_string .= "<State_Province>$state</State_Province>\n"; 
    $post_string .= "<PostalCode>$zip</PostalCode>\n"; 
    $post_string .= "<Country>$country</Country>\n"; 
    $post_string .= "<ShippingDescription>$shippingdescription</ShippingDescription>\n"; 
    for ($k=0;$k<count($products);$k++)
    {  
      $post_string .= "<Product>\n";  
      $post_string .= "<ProductId>".$products[$k]."</ProductId>\n";  
      $post_string .= "<Quantity>".$quantities[$k]."</Quantity>\n";  
      $post_string .= "</Product>\n";  
    }
    $post_string .= "</Order> ";    
    $data=XMLhandler("http://Kunaki.com","/XMLService.ASP",$post_string);    
    $xml_parser = xml_parser_create();
    xml_parser_set_option($xml_parser,XML_OPTION_CASE_FOLDING,0);
    xml_parser_set_option($xml_parser,XML_OPTION_SKIP_WHITE,1);
    xml_parse_into_struct($xml_parser, $data, $vals, $index);
    xml_parser_free($xml_parser);
    // Get any error code and message
    $errorcode=-1;
    $errorcode="";
    for ($k=0;$k<count($vals);$k++)
    {
      $entry=$vals[$k];
      if ($entry['tag']=="ErrorCode")
        $errorcode=$entry['value'];
      if ($entry['tag']=="ErrorText")
        $errortext=$entry['value'];
    }
  }
  $result['errorcode']=$errorcode;  
  if ($errorcode==0)
  {
    $result['errortext']=$errortext;
    $result['shippingprice']=$shippingprice;
    $result['shippingdescription']=$shippingdescription;    
  }
  else
  {
    $result['errortext']=$errortext;
    $result['shippingprice']=0;
    $result['shippingdescription']="";    
  }  
  return($result);
}

function XMLhandler($url,$page,$post_string)
{
  $header  = "POST ".$page." HTTP/1.0 \r\n";
  $header .= "MIME-Version: 1.0 \r\n";
  $header .= "Content-type: application/PTI26 \r\n";
  $header .= "Content-length: ".strlen($post_string)." \r\n";
  $header .= "Content-transfer-encoding: text \r\n";
  $header .= "Request-number: 1 \r\n";
  $header .= "Document-type: Request \r\n";
  $header .= "Interface-Version: Test 1.4 \r\n";
  $header .= "Connection: close \r\n\r\n";
  $header .= $post_string;
  
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL,$url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_TIMEOUT, 4); 
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $header);
  
  $data = curl_exec($ch);
  if (curl_errno($ch))
    print curl_error($ch);
  else
    curl_close($ch);
  return($data);  
}

function getStateCode($name)
{
  $states=array(
  'alabama'=>'AL',
  'alaska'=>'AK',
  'arizona'=>'AZ',
  'arkansas'=>'AR',
  'california'=>'CA',
  'colorado'=>'CO',
  'connecticut'=>'CT',
  'delaware'=>'DE',
  'district of columbia'=>'DC',
  'florida'=>'FL',
  'georgia'=>'GA',
  'hawaii'=>'HI',
  'idaho'=>'ID',
  'illinois'=>'IL',
  'indiana'=>'IN',
  'iowa'=>'IA',
  'kansas'=>'KS',
  'kentucky'=>'KY',
  'louisiana'=>'LA',
  'maine'=>'ME',
  'maryland'=>'MD',
  'massachusetts'=>'MA',
  'michigan'=>'MI',
  'minnesota'=>'MN',
  'mississippi'=>'MS',
  'missouri'=>'MO',
  'montana'=>'MT',
  'nebraska'=>'NE',
  'nevada'=>'NV',
  'new hampshire'=>'NH',
  'new jersey'=>'NJ',
  'new mexico'=>'NM',
  'new york'=>'NY',
  'north carolina'=>'NC',
  'north dakota'=>'ND',
  'ohio'=>'OH',
  'oklahoma'=>'OK',
  'oregon'=>'OR',
  'pennsylvania'=>'PA',
  'puerto rico'=>'PR',
  'rhode island and providence plantations'=>'RI',
  'rhode island'=>'RI',
  'south carolina'=>'SC',
  'south dakota'=>'SD',
  'tennessee'=>'TN',
  'texas'=>'TX',
  'utah'=>'UT',
  'vermont'=>'VT',
  'virginia'=>'VA',
  'washington'=>'WA',
  'west virginia'=>'WV',
  'wisconsin'=>'WI',
  'wyoming'=>'WY',
  'alberta'=>'AB',
  'british columbia'=>'BC', 
  'ontario'=>'ON', 
  'newfoundland and labrador'=>'ON', 
  'nova scotia'=>'NS', 
  'prince edward island'=>'PE', 
  'new brunswick'=>'NB', 
  'quebec'=>'QC', 
  'manitoba'=>'MB', 
  'saskatchewan'=>'SK', 
  'northwest territories'=>'NT', 
  'nunavut'=>'NU',
  'yukon territory'=>'YT',        
  'alberta'=>'AB',
  'colombie-britannique'=>'BC',
  'manitoba'=>'MB',
  'nouveau-brunswick'=>'NB',
  'terre-neuve-et-labrador'=>'NL',
  'nouvelle-ecosse'=>'NS',
  'territoires du nord-ouest'=>'NT',
  'nunavut'=>'NU',
  'ontario'=>'ON',
  'ile-du-prince-edouard'=>'PE',
  'quebec'=>'QC',
  'saskatchewan'=>'SK',
  'yukon'=>'YT'
  );
  $name=trim($name);
  $name=str_replace("  "," ",$name);
  $name=strtolower($name);
  $code=$states[$name];
  if ($code=="")
    return($name);    
  return($code);
}

function getUniformCountryName($countrycode)
{
  $countries=array(
  "AF"=>"Afghanistan",
  "AFG"=>"Afghanistan",
  "AX"=>"Aland Islands",
  "ALB"=>"Albania",
  "DZ"=>"Algeria",
  "DZA"=>"Algeria",
  "AS"=>"American Samoa",
  "ASM"=>"American Samoa",
  "AD"=>"Andorra",
  "AND"=>"Andorra",
  "AO"=>"Angola",
  "AGO"=>"Angola",
  "AI"=>"Anguilla",
  "AIA"=>"Anguilla",
  "AQ"=>"Antarctica",
  "ATA"=>"Antarctica",
  "AG"=>"Antigua and Barbuda",
  "ATG"=>"Antigua and Barbuda",
  "AR"=>"Argentina",
  "ARG"=>"Argentina",
  "AM"=>"Armenia",
  "ARM"=>"Armenia",
  "AW"=>"Aruba",
  "ABW"=>"Aruba",
  "AU"=>"Australia",
  "AUS"=>"Australia",
  "AT"=>"Austria",
  "AUT"=>"Austria",
  "AZ"=>"Azerbaijan",
  "AZE"=>"Azerbaijan",
  "BS"=>"Bahamas",
  "BHS"=>"Bahamas",
  "BH"=>"Bahrain",
  "BHR"=>"Bahrain",
  "BD"=>"Bangladesh",
  "BGD"=>"Bangladesh",
  "BB"=>"Barbados",
  "BRB"=>"Barbados",
  "BY"=>"Belarus",
  "BLR"=>"Belarus",
  "BE"=>"Belgium",
  "BEL"=>"Belgium",
  "BZ"=>"Belize",
  "BLZ"=>"Belize",
  "BJ"=>"Benin",
  "BEN"=>"Benin",
  "BM"=>"Bermuda",
  "BMU"=>"Bermuda",
  "BT"=>"Bhutan",
  "BTN"=>"Bhutan",
  "BO"=>"Bolivia",
  "BOL"=>"Bolivia",
  "BA"=>"Bosnia",
  "BIH"=>"Bosnia",
  "BW"=>"Botswana",
  "BWA"=>"Botswana",
  "BV"=>"Bouvet Island",
  "BVT"=>"Bouvet Island",
  "BR"=>"Brazil",
  "BRA"=>"Brazil",
  "IO"=>"British Indian Ocean T",
  "IOT"=>"British Indian Ocean T",
  "BN"=>"Brunei Darussalam",
  "BRN"=>"Brunei Darussalam",
  "BG"=>"Bulgaria",
  "BGR"=>"Bulgaria",
  "BF"=>"Burkina Faso",
  "BFA"=>"Burkina Faso",
  "BI"=>"Burundi",
  "BDI"=>"Burundi",
  "KH"=>"Cambodia",
  "KHM"=>"Cambodia",
  "CM"=>"Cameroon",
  "CMR"=>"Cameroon",
  "CA"=>"Canada",
  "CAN"=>"Canada",
  "CV"=>"Cape Verde",
  "CPV"=>"Cape Verde",
  "KY"=>"Cayman Islands",
  "CYM"=>"Cayman Islands",
  "CF"=>"Central African Republic",
  "CAF"=>"Central African Republic",
  "TD"=>"Chad",
  "TCD"=>"Chad",
  "CL"=>"Chile",
  "CHL"=>"Chile",
  "CN"=>"China",
  "CHN"=>"China",
  "CX"=>"Christmas Island",
  "CXR"=>"Christmas Island",
  "CC"=>"Cocos (Keeling) Islands",
  "CCK"=>"Cocos (Keeling) Islands",
  "CO"=>"Colombia",
  "COM"=>"Colombia",
  "KM"=>"Comoros",
  "CG"=>"Congo",
  "COG"=>"Congo",
  "CD"=>"Democratic Republic of Congo",
  "COD"=>"Democratic Republic of Congo",
  "CK"=>"Cook Islands",
  "COK"=>"Cook Islands",
  "CR"=>"Costa Rica",
  "CR"=>"Costa Rica",
  "CRI"=>"Cote D'Ivoire",
  "CIV"=>"Cote D'Ivoire",
  "HR"=>"Croatia",
  "HRV"=>"Croatia",
  "CU"=>"Cuba",
  "CY"=>"Cyprus",
  "CYP"=>"Cyprus",
  "CZ"=>"Czech Republic",
  "CZE"=>"Czech Republic",
  "DK"=>"Denmark",
  "DNK"=>"Denmark",
  "DJ"=>"Djibouti",
  "DJI"=>"Djibouti",
  "DM"=>"Dominica",
  "DMA"=>"Dominica",
  "DO"=>"Dominican Republic",
  "DOM"=>"Dominican Republic",
  "EC"=>"Ecuador",
  "ECU"=>"Ecuador",
  "EG"=>"Egypt",
  "EGY"=>"Egypt",
  "SV"=>"El Salvador",
  "SLV"=>"El Salvador",
  "GQ"=>"Equatorial Guinea",
  "GNQ"=>"Equatorial Guinea",
  "ER"=>"Eritrea",
  "ERI"=>"Eritrea",
  "EE"=>"Estonia",
  "EST"=>"Estonia",
  "ET"=>"Ethiopia",
  "ETH"=>"Ethiopia",
  "FK"=>"Falkland Islands",
  "FLK"=>"Falkland Islands",
  "FO"=>"Faroe Islands",
  "FRO"=>"Faroe Islands",
  "FJ"=>"Fiji",
  "FJI"=>"Fiji",
  "FI"=>"Finland",
  "FIN"=>"Finland",
  "FR"=>"France",
  "FRA"=>"France",
  "GF"=>"French Guiana",
  "GUF"=>"French Guiana",
  "PF"=>"French Polynesia",
  "PYF"=>"French Polynesia",
  "TF"=>"French Southern Territories",
  "ATF"=>"French Southern Territories",
  "GA"=>"Gabon",
  "GAB"=>"Gabon",
  "GM"=>"Gambia",
  "GMB"=>"Gambia",
  "GE"=>"Georgia",
  "GEO"=>"Georgia",
  "DE"=>"Germany",
  "DEU"=>"Germany",
  "GH"=>"Ghana",
  "GHA"=>"Ghana",
  "GI"=>"Gibraltar",
  "GIB"=>"Gibraltar",
  "GR"=>"Greece",
  "GRC"=>"Greece",
  "GL"=>"Greenland",
  "GRL"=>"Greenland",
  "GD"=>"Grenada",
  "GRD"=>"Grenada",
  "GP"=>"Guadeloupe",
  "GLP"=>"Guadeloupe",
  "GU"=>"Guam",
  "GUM"=>"Guam",
  "GT"=>"Guatemala",
  "GTM"=>"Guatemala",
  "GG"=>"Guernsey",
  "GN"=>"Guinea",
  "GIN"=>"Guinea",
  "GW"=>"Guinea-Bissau",
  "GNB"=>"Guinea-Bissau",
  "GY"=>"Guyana",
  "GUY"=>"Guyana",
  "HT"=>"Haiti",
  "HTI"=>"Haiti",
  "HM"=>"Heard and Mc Donald Isls",
  "HMD"=>"Heard and Mc Donald Isls",
  "VA"=>"Holy See, Vatican City",
  "VAT"=>"Holy See, Vatican City",
  "HN"=>"Honduras",
  "HND"=>"Honduras",
  "HK"=>"Hong Kong",
  "HKG"=>"Hong Kong",
  "HU"=>"Hungary",
  "HUN"=>"Hungary",
  "IS"=>"Iceland",
  "ISL"=>"Iceland",
  "IN"=>"India",
  "IND"=>"India",
  "ID"=>"Indonesia",
  "IDN"=>"Indonesia",
  "IR"=>"Iran",
  "IQ"=>"Iraq",
  "IRQ"=>"Iraq",
  "IE"=>"Ireland",
  "IRL"=>"Ireland",
  "IM"=>"Isle of Man",
  "IL"=>"Israel",
  "ISR"=>"Israel",
  "IT"=>"Italy",
  "ITA"=>"Italy",
  "JM"=>"Jamaica",
  "JAM"=>"Jamaica",
  "JP"=>"Japan",
  "JPN"=>"Japan",
  "JE"=>"Jersey",
  "JO"=>"Jordan",
  "JOR"=>"Jordan",
  "KZ"=>"Kazakhstan",
  "KAZ"=>"Kazakhstan",
  "KE"=>"Kenya",
  "KEN"=>"Kenya",
  "KI"=>"Kiribati",
  "KIR"=>"Kiribati",
  "KP"=>"Korea Democratic Peoples R",
  "KR"=>"Korea Republic",
  "KOR"=>"Korea Republic",
  "KW"=>"Kuwait",
  "KWT"=>"Kuwait",
  "KG"=>"Kyrgyzstan",
  "KGZ"=>"Kyrgyzstan",
  "LA"=>"Lao People's Dem Repub",
  "LAO"=>"Lao People's Dem Repub",
  "LV"=>"Latvia",
  "LB"=>"Lebanon",
  "LBN"=>"Lebanon",
  "LS"=>"Lesotho",
  "LSO"=>"Lesotho",
  "LR"=>"Liberia",
  "LBR"=>"Liberia",
  "LY"=>"Libyan Arab Jamahiriya",
  "LBY"=>"Libyan Arab Jamahiriya",
  "LI"=>"Liechtenstein",
  "LIE"=>"Liechtenstein",
  "LT"=>"Lithuania",
  "LTU"=>"Lithuania",
  "LU"=>"Luxembourg",
  "LUX"=>"Luxembourg",
  "MO"=>"Macao",
  "MAC"=>"Macao",
  "MK"=>"Macedonia",
  "MKD"=>"Macedonia",
  "MG"=>"Madagascar",
  "MDG"=>"Madagascar",
  "MW"=>"Malawi",
  "MWI"=>"Malawi",
  "MY"=>"Malaysia",
  "MYS"=>"Malaysia",
  "MV"=>"Maldives",
  "MDV"=>"Maldives",
  "ML"=>"Mali",
  "MLI"=>"Mali",
  "MT"=>"Malta",
  "MLT"=>"Malta",
  "MH"=>"Marshall Islands",
  "MHL"=>"Marshall Islands",
  "MQ"=>"Martinique",
  "MTQ"=>"Martinique",
  "MR"=>"Mauritania",
  "MRT"=>"Mauritania",
  "MU"=>"Mauritius",
  "MUS"=>"Mauritius",
  "YT"=>"Mayotte",
  "MYT"=>"Mayotte",
  "MX"=>"Mexico",
  "MEX"=>"Mexico",
  "FM"=>"Micronesia",
  "FSM"=>"Micronesia",
  "MD"=>"Moldova",
  "MDA"=>"Moldova",
  "MC"=>"Monaco",
  "MCO"=>"Monaco",
  "MN"=>"Mongolia",
  "MNG"=>"Mongolia",
  "ME"=>"Montenegro",
  "MNE"=>"Montenegro",
  "MS"=>"Montserrat",
  "MSR"=>"Montserrat",
  "MA"=>"Morocco",
  "MAR"=>"Morocco",
  "MZ"=>"Mozambique",
  "MOZ"=>"Mozambique",
  "MM"=>"Myanmar",
  "MMR"=>"Myanmar",
  "NA"=>"Namibia",
  "NAM"=>"Namibia",
  "NR"=>"Nauru",
  "NRU"=>"Nauru",
  "NP"=>"Nepal",
  "NPL"=>"Nepal",
  "NL"=>"Netherlands",
  "NLD"=>"Netherlands",
  "AN"=>"Netherlands Antilles",
  "ANT"=>"Netherlands Antilles",
  "NC"=>"New Caledonia",
  "NCL"=>"New Caledonia",
  "NZ"=>"New Zealand",
  "NZL"=>"New Zealand",
  "NI"=>"Nicaragua",
  "NIC"=>"Nicaragua",
  "NE"=>"Niger",
  "NER"=>"Niger",
  "NG"=>"Nigeria",
  "NGA"=>"Nigeria",
  "NU"=>"Niue",
  "NIU"=>"Niue",
  "NF"=>"Norfolk Island",
  "NFK"=>"Norfolk Island",
  "MP"=>"Northern Mariana Islands",
  "MNP"=>"Northern Mariana Islands",
  "NO"=>"Norway",
  "NOR"=>"Norway",
  "OM"=>"Oman",
  "OMN"=>"Oman",
  "PK"=>"Pakistan",
  "PAK"=>"Pakistan",
  "PW"=>"Palau",
  "PLW"=>"Palau",
  "PS"=>"Palestinian Territory",
  "PSE"=>"Palestinian Territory",
  "PA"=>"Panama",
  "PAN"=>"Panama",
  "PG"=>"Papua New Guinea",
  "PNG"=>"Papua New Guinea",
  "PY"=>"Paraguay",
  "PRY"=>"Paraguay",
  "PE"=>"Peru",
  "PER"=>"Peru",
  "PH"=>"Philippines",
  "PHL"=>"Philippines",
  "PN"=>"Pitcairn Island",
  "PCN"=>"Pitcairn Island",
  "PL"=>"Poland",
  "POL"=>"Poland",
  "PT"=>"Portugal",
  "PRT"=>"Portugal",
  "PR"=>"Puerto Rico",
  "PRI"=>"Puerto Rico",
  "QA"=>"Qatar",
  "QAT"=>"Qatar",
  "RE"=>"Reunion",
  "REU"=>"Reunion",
  "RO"=>"Romania",
  "ROU"=>"Romania",
  "RU"=>"Russian Federation",
  "RUS"=>"Russian Federation",
  "RW"=>"Rwanda",
  "RWA"=>"Rwanda",
  "BL"=>"Saint Barthelemy",
  "SH"=>"Saint Helena",
  "SHN"=>"Saint Helena",
  "KN"=>"Saint Kitts and Nevis",
  "KNA"=>"Saint Kitts and Nevis",
  "LC"=>"Saint Lucia",
  "LCA"=>"Saint Lucia",
  "MF"=>"Saint Martin",
  "PM"=>"St. Pierre and Miquelon",
  "SPM"=>"St. Pierre and Miquelon",
  "VC"=>"Saint Vincent and Grenadines",
  "VCT"=>"Saint Vincent and Grenadines",
  "WS"=>"Samoa",
  "WSM"=>"Samoa",
  "SM"=>"San Marino",
  "SMR"=>"San Marino",
  "ST"=>"Sao Tome and Principe",
  "STP"=>"Sao Tome and Principe",
  "SA"=>"Saudi Arabia",
  "SAU"=>"Saudi Arabia",
  "SN"=>"Senegal",
  "SEN"=>"Senegal",
  "RS"=>"Serbia",
  "SRB"=>"Serbia",
  "SC"=>"Seychelles",
  "SYC"=>"Seychelles",
  "SL"=>"Sierra Leone",
  "SLE"=>"Sierra Leone",
  "SG"=>"Singapore",
  "SGP"=>"Singapore",
  "SK"=>"Slovakia",
  "SVK"=>"Slovakia",
  "SI"=>"Slovenia",
  "SVN"=>"Slovenia",
  "SB"=>"Solomon Islands",
  "SLB"=>"Solomon Islands",
  "SO"=>"Somalia",
  "SOM"=>"Somalia",
  "ZA"=>"South Africa",
  "ZAF"=>"South Africa",
  "GS"=>"South Georgia",
  "SGS"=>"South Georgia",
  "ES"=>"Spain",
  "ESP"=>"Spain",
  "LK"=>"Sri Lanka",
  "LKA"=>"Sri Lanka",
  "SD"=>"Sudan",
  "SR"=>"Suriname",
  "SUR"=>"Suriname",
  "SJ"=>"Svalbard and Jan Mayen Is",
  "SJM"=>"Svalbard and Jan Mayen Is",
  "SZ"=>"Swaziland",
  "SWZ"=>"Swaziland",
  "SE"=>"Sweden",
  "SWE"=>"Sweden",
  "CH"=>"Switzerland",
  "CHE"=>"Switzerland",
  "SY"=>"Syrian Arab Republic",
  "TW"=>"Taiwan",
  "TWN"=>"Taiwan",
  "TJ"=>"Tajkistan",
  "TJK"=>"Tajkistan",
  "TZ"=>"Tanzania ",
  "TZA"=>"Tanzania ",
  "TH"=>"Thailand",
  "THA"=>"Thailand",
  "TL"=>"Timor",
  "TLS"=>"Timor",
  "TG"=>"Togo",
  "TGO"=>"Togo",
  "TK"=>"Tokelau",
  "TKL"=>"Tokelau",
  "TO"=>"Tonga",
  "TON"=>"Tonga",
  "TT"=>"Trinidad and Tobago",
  "TTO"=>"Trinidad and Tobago",
  "TN"=>"Tunisia",
  "TUN"=>"Tunisia",
  "TR"=>"Turkey",
  "TUR"=>"Turkey",
  "TM"=>"Turkmenistan",
  "TKM"=>"Turkmenistan",
  "TC"=>"Turks and Caicos Islands",
  "TCA"=>"Turks and Caicos Islands",
  "TV"=>"Tuvalu",
  "TUV"=>"Tuvalu",
  "UG"=>"Uganda",
  "UGA"=>"Uganda",
  "UA"=>"Ukraine",
  "UKR"=>"Ukraine",
  "AE"=>"United Arab Emirates",
  "ARE"=>"United Arab Emirates",
  "GB"=>"United Kingdom",
  "GBR"=>"United Kingdom",
  "US"=>"United States",
  "USA"=>"United States",
  "UM"=>"United States Outlying Islands",
  "UMI"=>"United States Outlying Islands",
  "UY"=>"Uruguay",
  "URY"=>"Uruguay",
  "UZ"=>"Uzbekistan",
  "UZB"=>"Uzbekistan",
  "VU"=>"Vanuatu",
  "VUT"=>"Vanuatu",
  "VE"=>"Venezuela",
  "VEN"=>"Venezuela",
  "VN"=>"Viet Nam",
  "VNM"=>"Viet Nam",
  "VG"=>"Virgin Islands (British)",
  "VGB"=>"Virgin Islands (British)",
  "VI"=>"Virgin Islands (U.S.)",
  "VIR"=>"Virgin Islands (U.S.)",
  "WF"=>"Wallis and Futuna Islands",
  "WLF"=>"Wallis and Futuna Islands",
  "EH"=>"Western Sahara",
  "ESH"=>"Western Sahara",
  "YE"=>"Yemen",
  "YEM"=>"Yemen",
  "ZM"=>"Zambia",
  "ZMB"=>"Zambia",
  "ZW"=>"Zimbabwe",
  "ZWE"=>"Zimbabwe",
  // Other entries
  "UK"=>"United Kingdom",
  "ENGLAND"=>"United Kingdom",
  "SCOTLAND"=>"United Kingdom",
  "WALES"=>"United Kingdom",
  "EIRE"=>"United Kingdom",
  "NORTHERN IRELAND"=>"United Kingdom"
  );
  $countrycode=trim($countrycode);
  $countrycode=str_replace("  "," ",$countrycode);
  $countrycode=strtoupper($countrycode);
  $code=$countries[$countrycode];
  if ($code=="")
    return($countrycode);    
  return($code);
}

function llipn_validemail($email)
{
	return(preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/i", $email));
}

function llipn_AWeberSubscribe($awlist,$prodnum)
{
  global $SellerCompany, $SellerEmail;
  if (!llipn_validemail($awlist))
    return;
  $subject  = "Subscribe Member - Linklok Paypal";
  $mailBody ="\n";
  $mailBody.="email: ".$_POST['payer_email']."\n";
  $mailBody.="name: ".$_POST['first_name']." ".$_POST['last_name']."\n";
  $mailBody.="company: ".$_POST['payer_business_name']."\n";
  $mailBody.="ordernumber: ".$_POST['txn_id']."\n";
  $mailBody.="invoice: ".$_POST['invoice']."\n";
  $mailBody.="address_name: ".$_POST['address_name']."\n";
  $mailBody.="address_street: ".$_POST['address_street']."\n";
  $mailBody.="address_city: ".$_POST['address_city']."\n";
  $mailBody.="address_state: ".$_POST['address_state']."\n";
  $mailBody.="address_zip: ".$_POST['address_zip']."\n";
  $mailBody.="address_country: ".$_POST['address_country']."\n";
  $mailBody.="address_country_code: ".$_POST['address_country_code']."\n";
  $mailBody.="custom: ".$_POST['custom']."\n";
  if ($prodnum!="")
  {
    global $ProdId,$ProdDesc;
    $mailBody.="item_number: ".$ProdId[$prodnum]."\n";
    $mailBody.="item_name: ".$ProdDesc[$prodnum]."\n";    
  }
  else
  {
    if (($_POST['item_number']!="") || ($_POST['item_name']!=""))
    {
      $mailBody.="item_number: ".$_POST['item_number']."\n";
      $mailBody.="item_name: ".$_POST['item_name']."\n";        
    }
    else
    {
      $mailBody.="item_number: ".$_POST['item_number1']."\n";
      $mailBody.="item_name: ".$_POST['item_name1']."\n";        
    }    
  }   
  SendEmailOut($awlist, $SellerEmail, $SellerCompany, $subject, $mailBody, "N");
  return;
}

function llipn_GetResponseSubscribe($grcampaign,$prodnum)
{
  global $SellerCompany, $SellerEmail,$getresponseapikey;
  $api_key = $getresponseapikey;
  # API 2.x URL
  $api_url = 'http://api2.getresponse.com';
  # initialize JSON-RPC client
  $client = new jsonRPCClient($api_url);
  $result = NULL;
  # get CAMPAIGN_ID from name
  $result = $client->get_campaigns(
      $api_key,
      array (
          # find by name literally
          'name' => array ( 'EQUALS' => $grcampaign )
      )
  );
  $CAMPAIGN_ID = array_pop(array_keys($result));
  # add contact to campaign
  $result = $client->add_contact(
  $api_key,
  array (
      'campaign'  => $CAMPAIGN_ID,
      'name'      => $_POST['first_name']." ".$_POST['last_name'],
      'email'     => $_POST['payer_email'],
      'cycle_day' => '0'
  //            ,
  //            'customs' => array(
  //                array(
  //                    'name'       => 'last_purchased_product',
  //                    'content'    => 'netbook'
  //                )
  //            )
    )
  );
}

function llipn_ARPsubscribe($autoresponderID,$prodnum)
{
  global $arpURL;
  // See if autoresponder id has any custom fields added (e.g. 12?CUSTOM_ProdId=prod1&CUSTOM_OrderDate=!!!date!!! )
  $pos=strpos($autoresponderID,"?");
  if (is_integer($pos))
  {
    $customquery=substr($autoresponderID,$pos+1);
    $autoresponderID=substr($autoresponderID,0,$pos);
    if ($customquery!="")
    {
      InsertVariables($customquery);
      // Handle item_number and item_name
      if ($prodnum!="")
      {
        global $ProdId,$ProdDesc;
        $customquery = str_replace("!!!item_number!!!", $ProdId[$prodnum], $customquery);
        $customquery = str_replace("!!!item_name!!!", $ProdDesc[$prodnum], $customquery);
      }
      else
      {
        if (($_POST['item_number']!="") || ($_POST['item_name']!=""))
        {
          $customquery = str_replace("!!!item_number!!!", $_POST['item_number'], $customquery);
          $customquery = str_replace("!!!item_name!!!", $_POST['item_name'], $customquery);
        }
        else
        {
          $customquery = str_replace("!!!item_number!!!", $_POST['item_number1'], $customquery);
          $customquery = str_replace("!!!item_name!!!", $_POST['item_name1'], $customquery);
        }    
      }
      // Now URL encode only the vaue parts of $customquery
      $customqueryparts=explode("&",$customquery);
      $customquery="";
      for ($k=0;$k<count($customqueryparts);$k++)
      {
        if ($customquery!="")
          $customquery.="&";
        $customquery.=strtok($customqueryparts[$k],"=");
        $customquery.="=";        
        $customquery.=urlencode(strtok("="));
      }     
    }
  }
  $httpAgent = "ARPAgent";
  $postData = "id=".$autoresponderID."&first_name=".urlencode($_POST['first_name'])."&last_name=".urlencode($_POST['last_name'])."&full_name=".urlencode($_POST['first_name'])." ".urlencode($_POST['last_name'])."&company=".urlencode($_POST['payer_business_name'])."&email=".urlencode($_POST['payer_email']);
  $postData.= "&street=".urlencode($_POST['address_street'])."&city=".urlencode($_POST['address_city'])."&state=".urlencode($_POST['address_state'])."&zip=".urlencode($_POST['address_zip'])."&country=".urlencode($_POST['address_country'])."&subscription_type=E";
  if ($customquery!="")
    $postData.="&".$customquery;
  if (function_exists('curl_init'))
  {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_USERAGENT, $httpAgent);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
    curl_setopt($ch, CURLOPT_URL, $arpURL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);
  }
  else
  {
    $pos=strpos($arpURL,"/",7);
    $host=substr($arpURL,7,$pos-7);
    $path=substr($arpURL,$pos);
    $fp = fsockopen($host, 80, $errno, $errstr, 30);
    if (!$fp)
    {
      $res="";
    }
    else
    {
      fputs($fp, "POST ".$path." HTTP/1.1\r\n");
      fputs($fp, "Host: ".$host."\r\n");
      fputs($fp, "Content-Type: application/x-www-form-urlencoded\r\n");
      fputs($fp, "Content-Length: ".strlen($postData)."\r\n");
      fputs($fp, "Connection: close\r\n\r\n");
      fputs($fp, $postData."\r\n\r\n");
      fclose($fp);
    }
  }
}

function llipn_pdfhoneypot($pdf,$email,$ip,$secret,$url)
{
  $hash=md5($pdf.$email.$ip.$secret);
  $link=$url."/secure.php?pdf=".$pdf."&email=".$email."&ip=".$ip."&hash=".$hash;
  return($link);
}

function llipn_rangeDownload($file) {
  global $fname, $txn_id, $expiry,$NotifyDownloadEmail,$item_number,$desc,$email,$downloadbuffer;
	$fp = @fopen($file, 'rb'); 
	$size   = filesize($file); // File size
	$length = $size;           // Content length
	$start  = 0;               // Start byte
	$end    = $size - 1;       // End byte
	 /* Multiple ranges requires some more work to ensure it works correctly
	 * and comply with the specifications: http://www.w3.org/Protocols/rfc2616/rfc2616-sec19.html#sec19.2
	 *
	 * Multirange support annouces itself with:
	 * header('Accept-Ranges: bytes');
	 *
	 * Multirange content must be sent with multipart/byteranges mediatype,
	 * (mediatype = mimetype)
	 * as well as a boundry header to indicate the various chunks of data.
	 */
//	header("Accept-Ranges: 0-$length");
	header("Accept-Ranges: bytes");
	// header('Accept-Ranges: bytes');
	// multipart/byteranges
	// http://www.w3.org/Protocols/rfc2616/rfc2616-sec19.html#sec19.2
	if (isset($_SERVER['HTTP_RANGE'])) {
 
		$c_start = $start;
		$c_end   = $end;
		// Extract the range string
		list(, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);
		// Make sure the client hasn't sent us a multibyte range
		if (strpos($range, ',') !== false) {
 
			// (?) Shoud this be issued here, or should the first
			// range be used? Or should the header be ignored and
			// we output the whole content?
			header('HTTP/1.1 416 Requested Range Not Satisfiable');
			header("Content-Range: bytes $start-$end/$size");
			// (?) Echo some info to the client?
			exit;
		}
		// If the range starts with an '-' we start from the beginning
		// If not, we forward the file pointer
		// And make sure to get the end byte if specified
		if ($range0 == '-') {
      if ($NotifyDownloadEmail != "")
        EmailDownloadNotify($fname, $item_number, $desc, $txn_id, $_SERVER['REMOTE_ADDR'], $expiry, $email);
			// The n-number of the last bytes is requested
			$c_start = $size - substr($range, 1);
		}
		else {
 
			$range  = explode('-', $range);
			$c_start = $range[0];
			$c_end   = (isset($range[1]) && is_numeric($range[1])) ? $range[1] : $size;
		}
		/* Check the range and make sure it's treated according to the specs.
		 * http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
		 */
		// End bytes can not be larger than $end.
		$c_end = ($c_end > $end) ? $end : $c_end;
		// Validate the requested range and return an error if it's not correct.
		if ($c_start > $c_end || $c_start > $size - 1 || $c_end >= $size) {
 
			header('HTTP/1.1 416 Requested Range Not Satisfiable');
			header("Content-Range: bytes $start-$end/$size");
			// (?) Echo some info to the client?
			exit;
		}
		$start  = $c_start;
		$end    = $c_end;
		$length = $end - $start + 1; // Calculate new content length
		fseek($fp, $start);
		header('HTTP/1.1 206 Partial Content');
	}
	// Notify the client the byte range we'll be outputting
	header("Content-Range: bytes $start-$end/$size");
	header("Content-Length: $length");
	// Start buffered download
  if ($downloadbuffer>0)
    $buffer=$downloadbuffer;
  else    
	  $buffer = 1024 * 8;
  @set_time_limit(86400); 
	while(!feof($fp) && ($p = ftell($fp)) <= $end) {
 
		if ($p + $buffer > $end) {
 
			// In case we're only outputtin a chunk, make sure we don't
			// read past the length
			$buffer = $end - $p + 1;
		}
		echo fread($fp, $buffer);
    ob_flush();
		flush(); // Free up memory. Otherwise large files will trigger PHP's memory limit.
    if ($downloadbuffer>0)
      sleep(1);  
	}
	fclose($fp);
}

function llipn_getmimetype($fn)
{
  $mt['.jpg']="image/jpeg";
  $mt['.gif']="image/gif";
  $mt['.cgm']="image/cgm";
  $mt['.flv']="video/x-flv";
  $mt['.gif']="image/gif";
  $mt['.htm']="text/html";
  $mt['.html']="text/html";
  $mt['.txt']="text/plain";
  $mt['.pdf']="application/pdf";
  $mt['.m4v']="video/x-m4v";
  $mt['.mov']="video/quicktime";
  $mt['.mp3']="audio/mpeg";
  $mt['.mp4']="video/mp4";
  $mt['.mpg']="video/mpeg";
  $mt['.mpeg']="video/mpeg";
  $mt['.pdf']="application/pdf";
  $mt['.png']="image/png";
  $mt['.rm']="audio/x-pn-realaudio";
  $mt['.wmv']="application/x-ms-wmv";
  $mt['.swf']="application/x-shockwave-flash";
  $mt['.mov']="video/quicktime";
  $mt['.asf']="video/x-ms-asf";
  $mt['.asx']="video/x-ms-asf";
  $mt['.rm']="audio/x-realaudio";
  $mt['.ram']="audio/x-pn-realaudio";
  $mt['.rar']="application/x-rar-compressed";
  $mt['.zip']="application/zip";
  $ext=fileextension($fn);
  if (isset($mt[$ext]))
    $mimetype=$mt[$ext];
  else
    $mimetype="";
  return("$mimetype");
}

function llipn_get_magic_quotes_gpc()
{
  if (version_compare(PHP_VERSION, '5.4.0') < 0)
    return(get_magic_quotes_gpc());
  else
    return(0);  
}

function userfunction($data, $user1, $user2, $user3, $user4)
{
  global $first_name, $last_name, $payer_business_name, $custom;
  if (($data == "") || ($data == "NOTREQUIRED"))
    $key = "";
  // Place your code here. At the end of your code the string variable $key
  // should contain the value that will be sent to the user.
  $key = "123456";
  return (string)$key;
}

?>
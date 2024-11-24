<?php
define('samanehper','complaint');
define("ajax_head",true);
require("ajax_head.php");
function ptintpm($msg = 'waccess',$type = 'error'){
$html = new html();
global $lang;
$html->msg($msg,$type);
$html->printout(true);
}
$body  = "new complaint from " . getCurrentUrl() . "<br />"; 
$body  = "ip: " . getRealIpAddr() . "<br />"; 
$body  = "ip ADDR: " . $_SERVER['REMOTE_ADDR'] . "<br />"; 
$body  = "time: " . jdate( "Y/m/d H:i:s" ) . "<br />"; 
$body .= $_POST['textcomplaint'];
send_mail( 'complaint@irancms.com', $config['email'], "new complaint", $body );
ptintpm( "پیام با موفقیت ارسال شد." );
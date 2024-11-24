<?php
@session_start();
define('currentpage','main');
define('pageid','11');
define('news_security',true);
// Include Files
include("../../includes/db.php");
include("../../includes/db.config.php");
require("../../includes/html.php");
$html = new html();
if($config['disable'] == '1'){
$html->msg($lang["disabled"]);
$html->printout();
}
include("../../includes/function.php");
if((!isset($_POST['id'])) or (!isset($_POST['rate'])) or (!is_numeric(@$_POST['id'])) or (!is_numeric(@$_POST['rate']))){
$html->msg($lang["disabled"]);
$html->printout();
die('WWW.samaneh.COM');
}
$id   =  $_POST['id'];
$rate =  $_POST['rate'];
$tbl 	= ($_POST['tbl'] == 'i')? 'gallery_images' : 'gallery_cat';
$d->Query("update `$tbl` set `tvote`=`tvote`+'$rate',`nov`=`nov`+'1' WHERE id='$id' LIMIT 1");
$acr = $d->Query("SELECT tvote,nov FROM data WHERE id='$id'");
$acr = $d->fetch($acr);
$rate = number_format(($acr['tvote']/$acr['nov']),2,".","");
$ec = $rate;
$rate= round($rate);
for($i=0;$i<$rate;$i++){
echo '<img src="'.$config['site'].'theme/main/'.$config['theme'].'/img/rate_on.gif" border="0">';
}
$rate= 5-$rate;
if($rate !== 0){
for($i=0;$i<$rate;$i++){
echo '<img src="'.$config['site'].'theme/main/'.$config['theme'].'/img/rate_off.gif" border="0">';
}
}
echo " ".$ec;
?>
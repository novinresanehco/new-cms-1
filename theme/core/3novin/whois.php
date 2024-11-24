<?php 
/*************************************************
 * SOFT64 PhpAjaxWhois
 *
 * Version: 1.1
 * Date: 1/29/2008
 *
 ****************************************************/

require_once("AjaxWhois.php"); 
$whois = new AjaxWhois();
?>
<?php 
$domainName = (isset($_POST['domain'])) ? $_POST['domain'] : '';

if ($domainName) {
	$whois->processAjaxWhois(); 
} else {
	echo '<h2 class="red">Please specify domain name</h2>';
}
?>

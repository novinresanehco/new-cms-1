<?php
define("samanehper",'extra');
define("ajax_head",true);
require("ajax_head.php");

function ptintpm($msg = 'waccess',$type = 'error'){
	$html = new html();
	global $lang;
	$html->msg($lang[$msg],$type);
//	$html->msg($msg,$type);
	$html->printout(true);
}

$task = (!isset($_POST['task'])) ? ptintpm() : $_POST['task'];

switch ($task){
case "update":

	//$show	= (!in_array(@$_POST['show'],$show)) ? ptintpm() : $_POST['show'];
	$q	= (empty($_POST['q'])) ? ptintpm("allneed") : $_POST['q'];
	$ans1	= (empty($_POST['ans1'])) ? ptintpm("allneed") : $_POST['ans1'];
	$ans2	= (empty($_POST['ans2'])) ? ptintpm("allneed") : $_POST['ans2'];
    $ans3	= (empty($_POST['ans3'])) ? ptintpm("allneed") : $_POST['ans3'];
    $ans4	= (empty($_POST['ans4'])) ? ptintpm("allneed") : $_POST['ans4'];

    $id=1;
    $show=1;
	$q = $d->uquery("polls",array(
		'pollQuestion'	=> $q,
		'pollStatus'	=> $show,
	), "pollID=".$id." LIMIT 1 ");

    	$q = $d->uquery("pollanswers",array(
		'pollAnswerValue'	=> $ans1,
	), "pollAnswerID=1 LIMIT 1 ");

    	$q = $d->uquery("pollanswers",array(
		'pollAnswerValue'	=> $ans2,
	), "pollAnswerID=2 LIMIT 1 ");
    	$q = $d->uquery("pollanswers",array(
		'pollAnswerValue'	=> $ans3,
	), "pollAnswerID=3 LIMIT 1 ");
    	$q = $d->uquery("pollanswers",array(
		'pollAnswerValue'	=> $ans4,
	), "pollAnswerID=4 LIMIT 1 ");

    $filename = '../../module/poll/lp_log.txt';
    $fp = fopen( $filename, 'w+' );
    fwrite($fp,$together);
    fclose($fp);

		ptintpm("page_edited","success");
break;


default:
ptintpm();
}
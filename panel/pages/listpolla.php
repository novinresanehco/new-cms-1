<?php
define('samanehper','poll');
define('theme','quickpolla.htm');
define("ajax_head",true);
require("ajax_head.php");
$id =  (!is_numeric(@$_POST['id'])) ? die($lang['waccess']) : $_POST['id'];
$tpl = new samaneh();
$tpl-> load($Themedir.$pageTheme);
$lang = $lang['poll'];
$title = $d->query("SELECT `title` FROM `voteq` WHERE `id`='$id'");
$title = $d->fetch($title);
$title = $title['title'];
$samaneh = $d->query("SELECT SUM(count) as sum FROM `voteans` WHERE `voteid`='$id'");
$samaneh = $d->fetch($samaneh);
$samaneh = $samaneh['sum'];
$samaneh = (empty($samaneh) || $samaneh == 0) ? 1 : $samaneh;
$q = $d->query("SELECT * FROM `voteans` WHERE `voteid`='$id' ORDER by `id` DESC");
	while ($data = $d->fetch($q)){	$percent = round(($data['count']/$samaneh)*100,2);    $tpl->block("listtr",array("vote_title"=>$data['title'],"count"=>$data['count'],"samaneh"=>$percent,"cid"=>$data['id']));

    }
$tpl->assign(array("title"=>$title,"id"=>$id));
$tpl->showit();
?>
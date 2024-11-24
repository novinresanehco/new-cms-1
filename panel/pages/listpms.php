<?php
define("samanehper",'inbox');
define("ajax_head",true);
@$theme  = ($_POST['task'] == 'inbox') ? 'quickpm.htm' : 'quickpmo.htm';
define('theme',$theme);
define('mpage',true);
require("ajax_head.php");
@$task  = ($_POST['task'] == 'inbox' || $_POST['task'] == 'outbox') ? $_POST['task'] : $html->printout();
@$order = ($_POST['type'] == 'ASC') ? 'ASC' : 'DESC';
@$sql 	= ($_POST['type'] == 'notread') ? " AND `reade`='0' " : " ";
$filed = ($task == 'inbox') ? 're_id' : 'send_id';
$q = $d->query("SELECT * FROM `msg` WHERE `$filed`='{$info['u_id']}' $sql order by `$filed` LIMIT $From,$RPP");
	while ($data = $d->fetch($q)){	$usr 	= $user->info(false,$data[$filed]);
    $tpl->block("listtr",array("id"=>$data['pm_id'],"title"=>$data['title'],"name"=>$usr['showname'],"username"=>$usr['user'],"email"=>$usr['email']));
    }
    $q = $d->getrows("SELECT COUNT(*)  FROM `msg`  WHERE `$filed`='{$info['u_id']}' $sql",true);
    CMSpage($q,$RPP,5,$CurrentPage,$tpl,'pages','inbox.php?task='.$task.'&');
$tpl->showit();
?>
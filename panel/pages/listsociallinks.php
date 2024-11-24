<?php
define("samanehper",'sociallink');
define("ajax_head",true);
define('theme','quicksociallink.htm');
require("ajax_head.php");
$q = $d->query("SELECT * FROM `sociallink`");
while ($data = mysql_fetch_array($q))
{
	$tpl->block("listtr",array("title"=>$data['title'],"url"=>$data['url'],"id"=>$data['id'],"icon"=>$data['icon']));
}
    $tpl->showit();
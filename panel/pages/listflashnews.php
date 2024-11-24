<?php
define("rashcmsper",'flashnews');
define("ajax_head",true);
define('theme','quickflashnews.htm');
require("ajax_head.php");
$q = $d->query("SELECT * FROM `news`");
while ($data = mysql_fetch_array($q))
{
	$tpl->block("listtr",array("title"=>$data['title'],"link"=>$data['link'],"id"=>$data['id']));
}
$tpl->showit();
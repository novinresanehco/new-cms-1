<?php
define("samanehper",'link');
define("ajax_head",true);
define('theme','quicklink.htm');
require("ajax_head.php");
$q = $d->query("SELECT * FROM `link`");
	while ($data = mysql_fetch_array($q)){
    $tpl->block("listtr",array("title"=>$data['title'],"url"=>$data['url'],"id"=>$data['id'],"desc"=>$data['des']));
    }

    $tpl->showit();
?>
<?php
define('samanehper','cat');
define("ajax_head",true);
require("ajax_head.php");
function printpm($msg = 'waccess',$type = 'error')
{
	$html = new html();
	global $lang;
	$html->msg(isset($lang[$msg]) ? $lang[$msg] : $msg,$type);
	$html->printout(true);
}
$task = (!isset($_POST['task'])) ? printpm() : $_POST['task'];
switch ($task)
{
	case "new":
	$name   =  (empty($_POST['name'])) ? printpm("allneed") : $_POST['name'];
	$title  =  (empty($_POST['title'])) ? printpm("allneed") : $_POST['title'];
	if(!ctype_alnum( $name ))
	{
		printpm("نام فیلد می بایست تنها از حروف و اعداد انگلیسی باشد");
	}
	$q = $d->query("SELECT * FROM `fields` WHERE (`name`='$name' OR `title`='$title')");
	if($d->getrows($q) > 0)
	{
		printpm("نام یا عنوان فیلد تکراری می باشد");
	}
	$q = $d->query( "SHOW COLUMNS FROM  member;" );
	while($data = $d->fetch( $q ))
	{
		if($data['Field'] == $name)
		{
			printpm("نام فیلد تکراری می باشد.");
		}
	}
	$oid = $d->getmax('orderid','fields');
	$oid++;
	$q = $d->iquery( "fields", array(
	'name'	=>	$name,
	'title'	=>	$title,
	'orderid'	=>	$oid,
	));
	$temp = $d->debug;
	$d->debug = false;
	$d->Query("ALTER TABLE `member` ADD `$name` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL");
	$d->debug = $temp;
	printpm( "ok", "success" );
	break;
	//edit field
	case "edit":
	$name   		=  (empty($_POST['name'])) ? printpm("allneed") : trim($_POST['name']);
	if(!ctype_alnum( $name ))
	{
		printpm("نام فیلد می بایست تنها از حروف و اعداد انگلیسی باشد");
	}
	$temp = $d->debug;
	$d->debug = false;
	$title  		=  (empty($_POST['title'])) ? printpm("allneed") : trim($_POST['title']);
	$editing_id 	=  (!is_numeric(@$_POST['editing_id'])) ? printpm("allneed") : $_POST['editing_id'];
	$old = $d->Query(" SELECT * FROM `fields` WHERE `id`='$editing_id' ");
	$old = $d->fetch( $old );
	if($old['name'] != $name)
	{
		//new name selected
		$q = $d->query( "SHOW COLUMNS FROM  member;" );
		while($data = $d->fetch( $q ))
		{
			if($data['Field'] == $name)
			{
				printpm("نام فیلد تکراری می باشد.");
			}
		}
		$d->Query("ALTER TABLE `member` CHANGE `$old[name]` `$name` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL");
	}
	$d->uquery( "fields", array("name"=> $name, "title"=> $title), "id=".$editing_id);
	$d->debug = $temp;
	printpm("ok","success");
	break;
	case "delete":
	//
	$id =  (!is_numeric(@$_POST['id'])) ? printpm() : $_POST['id'];
	$old = $d->Query(" SELECT * FROM `fields` WHERE `id`='$id' ");
	$old = $d->fetch( $old );
	if( empty( $old['name'] ) OR !ctype_alnum( $old['name'] ) )
	{
		printpm();
	}
	$d->Query( "DELETE FROM `fields` WHERE `id`='$id' LIMIT 1" );
	$d->Query( "ALTER TABLE `member` DROP `$old[name]`" );
	printpm( "ok", "success" );
	break;
	case "listing":
		require("../../core/theme.php");
		$Themedir = "../../theme/admin/".$config['admintheme'].'/ajax/';
		$itpl = new samaneh();
		$itpl-> load($Themedir.'quickfields.htm');
		$q = $d->query("SELECT * FROM `fields` ORDER BY `orderid` ASC");
		$core = '';
		while ($data = $d->fetch($q))
		{
			$itpl->block( 'listtr', $data);
		}
		die( $itpl->dontshowit() );
	break;
default:
printpm();
}
?>
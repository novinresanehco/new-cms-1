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

switch ($task){case "add_image":
	$show	= array(1,2,3);
	$show	= (!in_array(@$_POST['show'],$show)) ? ptintpm() : $_POST['show'];
	$title	= (empty($_POST['title'])) ? ptintpm("allneed") : $_POST['title'];
	$img	= (empty($_POST['img'])) ? ptintpm("allneed") : $_POST['img'];
	$thumb	= (empty($_POST['thumb'])) ? ptintpm("allneed") : $_POST['thumb'];
	$cat	= (empty($_POST['cat'])) ? ptintpm("allneed") : $_POST['cat'];
	$text	= (empty($_POST['rezash_text'])) ? ptintpm("allneed") : $_POST['rezash_text'];
	$star	= ($_POST['star'] == 1)? 1 :0;
	$q = $d->iquery("gallery_images",array(
		'title'	=> $title,
		'users'	=> $show,
		'text'	=> $text,
		'img'	=> $img,
		'thumb'	=> $thumb,
		'cat'	=> $cat,
		'star'	=> $star,
	));
	ptintpm("ok","success");
break;

case "edit_image":

	$show	=  array(1,2,3);
	//die($_POST['show']);
	$show	=  (!in_array(@$_POST['show'],$show)) ? ptintpm() : $_POST['show'];
	$title	=  (empty($_POST['title'])) ? ptintpm("allneed") : $_POST['title'];
	$img	=  (empty($_POST['img'])) ? ptintpm("allneed") : $_POST['img'];
	$thumb	=  (empty($_POST['thumb'])) ? ptintpm("allneed") : $_POST['thumb'];
	$text	=  (empty($_POST['rezash_text'])) ? ptintpm("allneed") : $_POST['rezash_text'];
	$cat	=  (empty($_POST['cat'])) ? ptintpm("allneed") : $_POST['cat'];
	$star	= ($_POST['star'] == 1)? 1 :0;
	$id		=  (empty($_POST['id'])) ? ptintpm("allneed") : $_POST['id'];
	$q = $d->uquery("gallery_images",array(
		'title'	=> $title,
		'users'	=> $show,
		'text'	=> $text,
		'img'	=> $img,
		'thumb'	=> $thumb,
		'cat'	=> $cat,
		'star'	=> $star,
	), "id=".$id." LIMIT 1 ");

	ptintpm("ok","success");
break;
case "delete_image":
	$id =  (!is_numeric(@$_POST['id'])) ? ptintpm() : $_POST['id'];
	$qu = $d->Query("DELETE FROM `gallery_images`  WHERE `id`='$id' LIMIT 1");
	ptintpm("ok","success");
break;



case "add_cat":
	$show	= array(1,2,3);
	$show	= (!in_array(@$_POST['show'],$show)) ? ptintpm() : $_POST['show'];
	$title	= (empty($_POST['title'])) ? ptintpm("allneed") : $_POST['title'];
	$img	= (empty($_POST['img'])) ? ptintpm("allneed") : $_POST['img'];
	$cat	= (empty($_POST['cat'])) ? ptintpm("allneed") : $_POST['cat'];
	$cajax	=  (empty($_POST['cajax'])) ? ptintpm("allneed") : $_POST['cajax'];
	$text	= (empty($_POST['rezash_text'])) ? ptintpm("allneed") : $_POST['rezash_text'];
	$star	= ($_POST['star'] == 1)? 1 :0;
	
	$cat	= ($cat == 'zero')? '0' : $cat;
	$q = $d->iquery("gallery_cat",array(
		'title'	=> $title,
		'users'	=> $show,
		'text'	=> $text,
		'img'	=> $img,
		'sub'	=> $cat,
		'ajax'	=> $cajax,
		'star'	=> $star,
	));
	ptintpm("cat_created","success");
break;

case "edit_cat":
	$show	= array(1,2,3);
	$show	= (!in_array(@$_POST['show'],$show)) ? ptintpm() : $_POST['show'];
	$title	= (empty($_POST['title'])) ? ptintpm("allneed") : $_POST['title'];
	$img	= (empty($_POST['img'])) ? ptintpm("allneed") : $_POST['img'];
	$cat	= (empty($_POST['cat'])) ? ptintpm("allneed") : $_POST['cat'];
	$cajax	=  (empty($_POST['cajax'])) ? ptintpm("allneed") : $_POST['cajax'];
	$text	= (empty($_POST['rezash_text'])) ? ptintpm("allneed") : $_POST['rezash_text'];
	$star	= ($_POST['star'] == 1)? 1 :0;
	$id		=  (empty($_POST['id'])) ? ptintpm("allneed") : $_POST['id'];
	
	$cat	= ($cat == 'zero')? '0' : $cat;
	$q = $d->uquery("gallery_cat",array(
		'title'	=> $title,
		'users'	=> $show,
		'text'	=> $text,
		'img'	=> $img,
		'sub'	=> $cat,
		'ajax'	=> $cajax,
		'star'	=> $star,
	), "id=".$id." LIMIT 1 ");
	ptintpm("cat_edited","success");
break;

case "delete_cat":
	$id =  (!is_numeric(@$_POST['id'])) ? ptintpm() : $_POST['id'];
	$qu = $d->Query("DELETE FROM `gallery_cat`  WHERE `id`='$id' LIMIT 1");
	$qu = $d->Query("DELETE FROM `gallery_images`  WHERE `cat`='$id'");
	ptintpm("cat_deleted","success");
break;


case "sub_setting":
	$num_columns	= (empty($_POST['num_columns'])) ? ptintpm("allneed") : $_POST['num_columns'];
	$num_rows		= (empty($_POST['num_rows'])) ? ptintpm("allneed") : $_POST['num_rows'];
	$q = $d->uquery("gallery_config",array(
		'numcolumns'	=> $num_columns,
		'numrows'	=> $num_rows,
	));
	ptintpm("ok","success");
break;

default:
ptintpm();
}
?>
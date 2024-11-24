<?php

define( 'samanehper', 'postmgr' );
define( "ajax_head", true );
require("ajax_head.php");
$id = (!is_numeric( @$_POST['newsid'] )) ? die( $lang['waccess'] ) : $_POST['newsid'];
$title = (empty( $_POST['text'] )) ? die( $lang['waccess'] ) : safe( $_POST['text'], 1 );
$access = $d->Query( "SELECT `author` FROM `data` WHERE `id`='$id'" );
$access = $d->fetch( $access );
$access = ($access['author'] != $info['u_id'] && $permissions['editotherposts'] == 0) ? die( $lang['waccess'] ) : '';
$d->Query( "UPDATE `data` SET title='$title' WHERE `id`='$id'" ) or die( mysql_error() );
die( '<a href="#samaneh" onclick=\'titleEdit("' . $id . '");return false;\'>
' . $title . '</a>' );
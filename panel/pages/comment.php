<?php

define( 'samanehper', 'comment' );
define( "ajax_head", true );
require("ajax_head.php");
$lang = $lang['comment'];

function ptintpm( $msg = 'waccess', $type = 'error' )
{
    $html = new html();
    global $lang;
    $html->msg( $lang[$msg], $type );
    $html->printout( true );
}

$task = (!isset( $_POST['task'] )) ? ptintpm() : $_POST['task'];
switch ( $task )
{
    case "activate":
        $id = (!is_numeric( @$_POST['id'] )) ? ptintpm() : $_POST['id'];
        $d->uquery( "comments", array(
            'active' => 1,
                ), " c_id='$id'" );
        ptintpm( "activated", "success" );
        break;

    case "hide":
        $id = (!is_numeric( @$_POST['id'] )) ? ptintpm() : $_POST['id'];
        $q  = $d->uquery( "comments", array(
            'active' => '2',
                ), " c_id='$id'" );
        ptintpm( "hide", "success" );
        break;



    case "delete":
        $id = (!is_numeric( @$_POST['id'] )) ? ptintpm() : $_POST['id'];
        $q  = $d->getrowvalue( "p_id", "SELECT `p_id` FROM `comments`  WHERE `c_id`='$id' LIMIT 1", true );
        if ( empty( $q ) )
            ptintpm();
        else
        {
            $d->Query( "DELETE FROM `comments`  WHERE `c_id`='$id' LIMIT 1" );
            $d->Query( "UPDATE `data` SET `num`=`num`-1  WHERE `id`='$q' LIMIT 1" );
        }
        ptintpm( "deleted", "success" );
        break;
    case "move":
        $cid     = (!is_numeric( @$_POST['cid'] )) ? ptintpm( "allneed" ) : $_POST['cid'];
        $pid     = (!is_numeric( @$_POST['pid'] )) ? ptintpm( "allneed" ) : $_POST['pid'];
        $d->uquery( "comments", array(
            'p_id' => $pid,
                ), " c_id='$cid'" );
        ptintpm( "ok", "success" );
        break;
    case "edit":
        $name    = (!isset( $_POST['name'] )) ? ptintpm() : $_POST['name'];
        $email   = (!isset( $_POST['email'] )) ? ptintpm() : $_POST['email'];
        $website = (!isset( $_POST['website'] )) ? ptintpm() : $_POST['website'];
        $comment = (!isset( $_POST['comment'] )) ? ptintpm() : $_POST['comment'];
        $reply   = (!isset( $_POST['reply'] )) ? ptintpm() : $_POST['reply'];
        $id      = (!is_numeric( @$_POST['id'] )) ? ptintpm() : $_POST['id'];
        $ansid   = (!empty( $reply )) ? $info['u_id'] : 0;
        $d->uquery( "comments", array(
            'c_author' => $name,
            'email'    => $email,
            'url'      => $website,
            'text'     => $comment,
            'ans'      => $reply,
            'ansid'    => $ansid,
                ), " c_id='$id'" );
        ptintpm( "ok", "success" );
        break;
    case "method":
        $do      = (!isset( $_POST['do'] )) ? ptintpm() : $_POST['do'];
        $pid     = (!is_numeric( @$_POST['pid'] )) ? ptintpm() : $_POST['pid'];
        $do      = @$_POST['do'];
        $q       = ($permissions['editotherposts'] != 1) ? " AND `author`='$info[u_id]'" : " ";
        switch ( $do )
        {
            case "delete":
                $d->Query( "DELETE FROM `comments`  WHERE `p_id`='$pid' $q " );
                break;
            case "activate":
                $d->Query( "UPDATE `comments`  SET `active`='1' WHERE `p_id`='$pid' $q " );
                break;
            case "deactivate":
                $d->Query( "UPDATE `comments`  SET `active`='0' WHERE `p_id`='$pid' $q " );
                break;
            case "hide":
                $d->Query( "UPDATE `comments`  SET `active`='2' WHERE `p_id`='$pid' $q " );
                break;
            default:
                ptintpm();
        }
        break;
    default:
        ptintpm();
}
?>
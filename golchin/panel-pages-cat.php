<?php

define( 'samanehper', 'cat' );
define( "ajax_head", true );
require("ajax_head.php");

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
    case "addsubject":
        $subject   = (empty( $_POST['subject'] )) ? ptintpm( "allneed" ) : $_POST['subject'];
        $ensubject = (empty( $_POST['ensubject'] )) ? ptintpm( "allneed" ) : $_POST['ensubject'];
        $q         = $d->query( "SELECT * FROM `cat` WHERE (`name`='$subject' OR `enname`='$ensubject') AND `sub`='0'" );
        if ( $d->getrows( $q ) > 0 )
        {
            ptintpm( "cat_exists" );
        }
        $q             = $d->iquery( "cat", array(
            'sub'    => 0,
            'name'   => $subject,
            'enname' => $ensubject,
                ) );
        ptintpm( "cat_created", "success" );
        break;
    case "add_sub_subject":
        $sub_subject   = (empty( $_POST['sub_subject'] )) ? ptintpm( "allneed" ) : $_POST['sub_subject'];
        $sub_ensubject = (empty( $_POST['sub_ensubject'] )) ? ptintpm( "allneed" ) : $_POST['sub_ensubject'];
        $sub_core      = (!is_numeric( @$_POST['core'] )) ? ptintpm() : $_POST['core'];
        $q             = $d->query( "SELECT * FROM `cat` WHERE `id`='$sub_core'" );
        if ( $d->getrows( $q ) <= 0 )
        {
            ptintpm();
        }
        $q = $d->query( "SELECT * FROM `cat` WHERE (`name`='$sub_subject' OR `enname`='$sub_ensubject') AND `sub`='$sub_core'" );
        if ( $d->getrows( $q ) > 0 )
        {
            ptintpm( "cat_exists" );
        }
        $q          = $d->iquery( "cat", array(
            'sub'    => $sub_core,
            'name'   => $sub_subject,
            'enname' => $sub_ensubject,
                ) );
        ptintpm( "cat_created", "success" );
        break;
    case "edit_subject":
        $edit_sub   = (empty( $_POST['edit_sub'] )) ? ptintpm( "allneed" ) : $_POST['edit_sub'];
        $edit_ensub = (empty( $_POST['edit_ensub'] )) ? ptintpm( "allneed" ) : $_POST['edit_ensub'];
        $editing_id = (!is_numeric( @$_POST['editing_id'] )) ? ptintpm( "allneed" ) : $_POST['editing_id'];
        $core       = (!is_numeric( @$_POST['core'] )) ? ptintpm( "allneed" ) : $_POST['core'];
        $qu         = $d->Query( "SELECT `sub` FROM `cat` WHERE `id`='$editing_id'" );
        $qu         = $d->fetch( $qu );
        $sub        = $qu['sub'];
        if ( $sub == 0 && $core != 0 )
        {
            $qu = $d->Query( "UPDATE `cat` SET `sub`='0' WHERE `sub`='$editing_id'" );
            $d->uquery( "cat", array( "name" => $edit_sub, "enname" => $edit_ensub, "sub" => $core ), "id=" . $editing_id );
        }
        else
        {
            $d->uquery( "cat", array( "name" => $edit_sub, "enname" => $edit_ensub, "sub" => $core ), "id=" . $editing_id );
        }
        ptintpm( "cat_edited", "success" );
        break;
    case "delete_subject":
        $id  = (!is_numeric( @$_POST['id'] )) ? ptintpm() : $_POST['id'];
        $qu  = $d->Query( "SELECT `sub` FROM `cat` WHERE `id`='$id'" );
        $qu  = $d->fetch( $qu );
        $sub = $qu['sub'];
        if ( $sub == 0 )
        {
            $qu = $d->Query( "UPDATE `cat` SET `sub`='0' WHERE `sub`='$id'" );
            $qu = $d->Query( "DELETE FROM `cat`  WHERE `id`='$id' LIMIT 1" );
        }
        else
        {
            $qu = $d->Query( "DELETE FROM `cat`  WHERE `id`='$id' LIMIT 1" );
            $qu = $d->Query( "SELECT `id` FROM `data`  WHERE `cat_id`='$id'" );
            while ( $q  = $d->fetch( $qu ) )
                $qu = $d->Query( "DELETE FROM `catpost`  WHERE `pid`='$q[id]'" );
            $qu = $d->Query( "DELETE FROM `data` WHERE `cat_id`='$id'" );
        }
        ptintpm( "cat_deleted", "success" );
        break;
    case "listing":
        $type   = (@$_POST['type'] != 1 AND @$_POST['type'] != 2) ? ptintpm() : $_POST['type'];
        $type   = (@$_POST['type'] != 1 AND @$_POST['type'] != 2) ? ptintpm() : $_POST['type'];
        if ( $type == 1 )
            $result = '<select class="select" name="ecore" id="ecore" size="1">' . getSelectCats( 0, '' ) . '</select>';
        else
            $result = '<select class="select" name="core" id="core" size="1">' . getSelectCats( 0, '', 'cat_list_' ) . '</select>';
        die( $result );
        break;
    default:
        ptintpm();
}
?>
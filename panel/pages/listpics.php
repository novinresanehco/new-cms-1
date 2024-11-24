<?php

define( "samanehper", 'extra' );
define( "ajax_head", true );
define( 'theme', 'quickpics.htm' );
require("ajax_head.php");

$task = (!isset( $_POST['task'] )) ? ptintpm() : $_POST['task'];

$colors = array( 'green', 'black', 'red', 'blue', 'orange', 'purpule' );

function getTableGalleryCats( $parent = 0, $join = '' )
{
    
    global $d, $tpl, $colors, $lang;
    $users = array( '', $lang['members'], $lang['public'], $lang['guest'] );
    $menu_data = $d->Query( "SELECT * FROM `gallery_cat` WHERE `sub` = '$parent' ORDER BY `sub`,`id` ASC" );
    if ( $d->GetRows( $menu_data ) > 0 )
    {
        $p_sub = ( int ) $d->GetRowValue( "sub", "SELECT `sub` FROM `gallery_cat` WHERE `id`='$parent' LIMIT 1", true );
        $join .= '---';
        while ( $menu = $d->fetch( $menu_data ) )
        {
            if ( $p_sub == $parent )
            {
                $join = substr( $join, 0, -3 );
            }
            $color = (isset( $colors[floor( strlen( $join ) / 3 )] )) ? $colors[floor( strlen( $join ) / 3 )] : 'black';
            if ( $menu['sub'] == 0 )
            {
                $catMainId = 0;
                $font = 'bold';
            }
            else
            {
                $catMainId = $menu['sub'];
                $font = 'normal';
            }
            $tpl->block( "listpics", array(
                "text" => $menu['text'],
                "title" => $menu['title'],
                "title2" => $join.$menu['title'],
                "users_id" => $menu['users'],
                "id" => $menu['id'],
                "users" => $users[$menu['users']],
                "img" => $menu['img'],
                "thumb" => $menu['img'],
                "star" => $menu['star'],
                "cat_id" => $menu['sub'],
            ) );
            //$tpl->block( "listtr", array( "CatId" => $menu['id'], "font" => $font, "color" => $color, "Subject" => $join . ' ' . $menu['name'], "Ename" => $menu['enname'], "catId" => $menu['id'], "CatcoreId" => $catMainId ) );
            getTableGalleryCats( $menu['id'], $join );
        }
    }
}

//getTableGalleryCats( 0 );
switch ( $task )
{
    case 'listing':
        $tpl->assign( 'pics', 1 );
        $q = $d->query( "SELECT * FROM `gallery_images`" );
        $users = array( '', $lang['members'], $lang['public'], $lang['guest'] );
        while ( $data = mysql_fetch_array( $q ) )
        {

            $tpl->block( "listpics", array(
                "text" => $data['text'],
                "title" => $data['title'],
                "users_id" => $data['users'],
                "id" => $data['id'],
                "users" => $users[$data['users']],
                "img" => $data['img'],
                "thumb" => $data['thumb'],
                "cat_id" => $data['cat'],
                "star" => $data['star'],
            ) );
        }
        break;

    case 'listingcats':
        $tpl->assign( 'cats', 1 );
        getTableGalleryCats();
        /*
        $q = $d->Query( "SELECT * FROM `gallery_cat` ORDER BY `sub` ASC,`id` ASC" );

        while ( $data = $d->fetch( $q ) )
        {
            $tpl->block( "listpics", array(
                "text" => $data['text'],
                "title" => $data['title'],
                "title2" => $depth . $data['title'],
                "users_id" => $data['users'],
                "id" => $data['id'],
                "users" => $users[$data['users']],
                "img" => $data['img'],
                "thumb" => $data['img'],
                "star" => $data['star'],
                "cat_id" => $data['sub'],
            ) );
        }
         */

        break;

    default:
        printpm();
}
$tpl->showit();
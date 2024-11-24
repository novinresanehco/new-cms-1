<?php

// plugins name:     Site Map
// plugins Version:  1.0
// Designer Name:   Iman Moodi
// Website:         www.TopBlogger.ir
// License:         GPL/GNU (Free)
if ( !defined( 'plugins_admin_area' ) )
    die( 'invalid access' );
if ( !isset( $permissions['access_admin_area'] ) )
    die( 'invalid access' );
if ( $permissions['access_admin_area'] != '1' )
    die( 'invalid access' );
$information = array(
    'name'        => 'نقشه سايت',
    'provider'    => 'ايمان مودي',
    'providerurl' => 'http://www.topblogger.ir',
    'version'     => '1.0',
    'install'     => true,
    'uninstall'   => true,
    'activate'    => true,
    'inactivate'  => true,
    'data'        => array(
        'icon' => array( 'name' => 'آیکن', 'value' => '', 'type' => 'icon' ),
    )
);

$tpl->assign( 'first', '' );
if ( defined( 'methods' ) )
{

    function defaultop()
    {
        global $tpl, $d, $information;
        $itpl = new samaneh();
        $itpl->load( '../plugins/site-map/admin-theme.html' );


        $tpl->assign( 'plugins_name', $information['name'] );
        $tpl->assign( 'first', $itpl->dontshowit() );
        unset( $itpl );
    }

    function inactivateop()
    {
        global $d;
        $d->Query( "UPDATE `plugins` SET `stat`='0' WHERE `name`='site-map' LIMIT 1" );
        print_msg( 'ماژول با موفقيت غير فعال شد.', 'Success' );
    }

    function activateop()
    {
        global $d;
        $d->Query( "UPDATE `plugins` SET `stat`='9' WHERE `name`='site-map' LIMIT 1" );
        print_msg( 'ماژول با موفقيت فعال شد.', 'Success' );
    }

    function installop()
    {
        global $d;
        $q = $d->getrows( "SELECT `stat` FROM `plugins` WHERE `name`='site-map' LIMIT 1", true );
        if ( $q > 0 )
            print_msg( 'اين ماژول قبلا نصب شده است.', 'Info' );
        else
        {
            global $information;

            $oid = $d->getmax( 'oid', 'menus' );
            $q   = $d->Query( "INSERT INTO `menus` SET `oid`='$oid',`name`='site-map',`title`='$information[name]',`url`='plugins.php?plugin=site-map',`type`='0'" );
            $q   = $d->Query( "INSERT INTO `plugins` SET `name`='site-map',`title`='$information[name]',`stat`='0'" );
            print_msg( 'ماژول با موفقيت نصب شد.', 'Success' );
        }
    }

    function uninstallop()
    {
        global $d;
        $q = $d->getrows( "SELECT `stat` FROM `plugins` WHERE `name`='site-map' LIMIT 1", true );
        if ( $q <= 0 )
            print_msg( 'اين ماژول نصب نشده است يا استاندارد نيست.', 'Info' );
        else
        {
            $d->Query( "DELETE FROM `menus` WHERE `name`='site-map' LIMIT 1" );
            $d->Query( "DELETE FROM `plugins` WHERE `name`='site-map' LIMIT 1" );
            print_msg( 'ماژول با موفقيت حذف شد.', 'Success' );
        }
    }

    function print_msg( $msg, $type )
    {
        global $tpl, $information;
        $tpl->assign( array(
            'plugins_name' => $information['name'],
            $type          => 1,
            'msg'          => $msg,
        ) );
    }

}
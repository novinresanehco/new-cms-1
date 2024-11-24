<?php

if ( !defined( 'plugins_admin_area' ) )
    die( 'invalid access' );
if ( !isset( $permissions['access_admin_area'] ) )
    die( 'invalid access' );
if ( $permissions['access_admin_area'] != '1' )
    die( 'invalid access' );
$information = array(
    'name'        => 'خلاصه آمار',
    'provider'    => 'رضا شاهرخيان',
    'providerurl' => 'http://samaneh.com',
    'install'     => false,
    'uninstall'   => false,
    'activate'    => true,
    'inactivate'  => true,
    'data'        => array(
        'title'      => array( 'name' => 'عنوان', 'value' => 'آمار وب سایت' ),
        'Ctoday'     => array( 'name' => 'بازدید امروز', 'type' => 'checkbox' ),
        'Cyesterday' => array( 'name' => 'بازدید دیروز', 'type' => 'checkbox' ),
        'Cmonth'     => array( 'name' => 'بازدید ماه', 'type' => 'checkbox' ),
        'Clastmonth' => array( 'name' => 'بازدید ماه قبل', 'type' => 'checkbox' ),
        'Cyear'      => array( 'name' => 'بازدید سال', 'type' => 'checkbox' ),
        'Clastyear'  => array( 'name' => 'بازدید سال قبل', 'type' => 'checkbox' ),
        'Ctotal'     => array( 'name' => 'بازدید کل', 'type' => 'checkbox' ),
        'Cnews'      => array( 'name' => 'اخبار', 'type' => 'checkbox' ),
        'Ccomments'  => array( 'name' => 'نظرات', 'type' => 'checkbox' ),
        'Crss'       => array( 'name' => 'rss', 'type' => 'checkbox' ),
        'Cmembers'   => array( 'name' => 'تعداد اعضا', 'type' => 'checkbox' ),
        'Conlines'   => array( 'name' => 'کاربران آنلاین', 'type' => 'checkbox' ),
        'icon'       => array( 'name' => 'آیکن', 'value' => '', 'type' => 'icon' ),
    )
);
$tpl->assign( 'first', '' );
if ( defined( 'methods' ) )
{

    function defaultop()
    {
        print_msg( 'براي اين ماژول تنظيم خاصي در نظر گرفته نشده است.<br>براي فعال/غير فعال سازي ماژول مي توانيد از بخش "ماژول ها" اقدام كنيد.', 'Info' );
    }

    function inactivateop()
    {
        global $d;
        $d->Query( "UPDATE `plugins` SET `stat`='0' WHERE `name`='counter' LIMIT 1" );
        print_msg( 'ماژول با موفقيت غير فعال شد.', 'Success' );
    }

    function activateop()
    {
        global $d;
        $d->Query( "UPDATE `plugins` SET `stat`='1' WHERE `name`='counter' LIMIT 1" );
        print_msg( 'ماژول با موفقيت فعال شد.', 'Success' );
    }

    function print_msg( $msg, $type )
    {
        global $tpl, $information;
        $tpl->assign( array(
            'plugin_name' => $information['name'],
            $type         => 1,
            'msg'         => $msg,
        ) );
    }

}
?>
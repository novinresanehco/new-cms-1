<?php

if ( !defined( 'plugins_admin_area' ) )
    die( 'invalid access' );
if ( !isset( $permissions['access_admin_area'] ) )
    die( 'invalid access' );
if ( $permissions['access_admin_area'] != '1' )
    die( 'invalid access' );
$information = array(
    'name'        => 'آخرین تصاویر گالری تصاویر',
    'provider'    => 'امیرحسین عبیری',
    'providerurl' => 'http://www.roboteronic.com',
    'install'     => true,
    'uninstall'   => true,
    'activate'    => true,
    'inactivate'  => true,
    'data'        => array( //لیست پارامترهای اختصاصی ماژول
        'title' => array( 'name' => 'عنوان', 'value' => 'آخرین تصاویر گالری تصاویر' ),
        'count' => array( 'name' => 'تعداد', 'value' => '10', 'type' => 'number' ),
        'icon'  => array( 'name' => 'آیکن', 'value' => '', 'type' => 'icon' ),
    )
);

// define('mod_version', $information['version']);

$tpl->assign( 'first', '' );
if ( defined( 'methods' ) )
{

    function defaultop()
    {
        print_msg( 'براي اين پلاگین تنظيم خاصي در نظر گرفته نشده است.<br>', 'Info' );
    }

    function inactivateop()
    {
        global $d;
        $d->Query( "UPDATE `plugins` SET `stat`='0' WHERE `name`='gllastpic' LIMIT 1" );
        print_msg( 'پلاگین با موفقيت غير فعال شد.', 'Success' );
    }

    function activateop()
    {
        global $d;
        $d->Query( "UPDATE `plugins` SET `stat`='1',`sortable`=1 WHERE `name`='gllastpic' LIMIT 1" );
        print_msg( 'پلاگین با موفقيت فعال شد.', 'Success' );
    }

    function installop()
    {
        global $d;
        $q = $d->getrows( "SELECT `stat` FROM `plugins` WHERE `name`='gllastpic' LIMIT 1", true );
        if ( $q > 0 )
            print_msg( 'اين پلاگین قبلا نصب شده است.', 'Info' );
        else
        {
            global $information;
            $q = $d->getrows( "SELECT * FROM `plugins` WHERE `name`='gallery'", true );
            if ( $q == 0 )
                print_msg( 'برای نصب این پلاگین ابتدا باید پلاگین گالری تصاویر را دانلود نمایید', 'Success' );
            else
            {
                $q = $d->Query( "INSERT INTO `plugins` SET `name`='gllastpic',`title`='$information[name]',`stat`='0',`sortable`=1" );

                print_msg( 'پلاگین با موفقيت نصب شد.', 'Success' );
            }
        }
    }

    function uninstallop()
    {
        global $d;
        $q = $d->getrows( "SELECT `stat` FROM `plugins` WHERE `name`='gllastpic'  LIMIT 1", true );
        if ( $q <= 0 )
            print_msg( 'اين پلاگین نصب نشده است يا استاندارد نيست.', 'Info' );
        else
        {
            $q = $d->Query( "DELETE FROM `plugins` WHERE `name`='gllastpic' LIMIT 1" );
            print_msg( 'پلاگین با موفقيت حذف شد.', 'Success' );
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
?>

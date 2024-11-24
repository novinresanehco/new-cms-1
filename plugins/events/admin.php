<?php

if ( !defined( 'plugins_admin_area' ) OR !isset( $permissions['access_admin_area'] ) OR $permissions['access_admin_area'] != '1' )
{
    die( 'invalid access' );
}

$information = array(
    'name'        => 'رخدادها',
    'provider'    => 'رضا شاهرخيان',
    'providerurl' => 'http://shahrokhian.com',
    'install'     => true,
    'uninstall'   => true,
    'activate'    => true,
    'inactivate'  => true
);
$tpl->assign( 'first', '' );
if ( defined( 'methods' ) )
{

    function defaultop()
    {
        $theme = new samaneh();
        $theme->load( dirname( __FILE__ ) . '/events.html' );
        global $config;
        if ( isset( $_POST['events'] ) && is_array( $_POST['events'] ) )
        {
            $config['events'] = base64_encode( json_encode( $_POST['events'] ) );
            saveconfig( 'events', $config['events'] );

            echo '<div class="alert alert-success">تنظیمات با موفقیت ذخیره شدند.</div>';
        }

        $events = $config['events'];
        if ( !empty( $events ) )
        {
            $events = json_decode( base64_decode( $events ), true );
        }
        if ( isset( $events['today'] ) )
        {
            $theme->assign( 'events_today', 'checked="checked"' );
        }
        else
        {
            $theme->assign( 'events_today', '' );
        }

        if ( isset( $events['all'] ) )
        {
            $theme->assign( 'events_all', 'checked="checked"' );
        }
        else
        {
            $theme->assign( 'events_all', '' );
        }

        if ( isset( $events['unapprovedComments'] ) )
        {
            $theme->assign( 'events_unapprovedComments', 'checked="checked"' );
        }
        else
        {
            $theme->assign( 'events_unapprovedComments', '' );
        }

        if ( isset( $events['date'] ) )
        {
            $theme->assign( 'date_' . $events['date'], ' selected ' );
        }
        if ( isset( $events['numbers'] ) )
        {
            $theme->assign( 'numberslist', $events['numbers'] );
        }
        else
        {
            $theme->assign( 'numberslist', '' );
        }
        $theme->showit();
        exit;
    }

    function inactivateop()
    {
        global $d;
        $d->Query( "UPDATE `plugins` SET `stat`='0' WHERE `name`='events' LIMIT 1" );
        print_msg( 'ماژول با موفقيت غير فعال شد.', 'Success' );
    }

    function activateop()
    {
        global $d;
        $d->Query( "UPDATE `plugins` SET `stat`='1' WHERE `name`='events' LIMIT 1" );
        print_msg( 'ماژول با موفقيت فعال شد.', 'Success' );
    }

    function installop()
    {
        global $d;
        $q = $d->getrows( "SELECT `stat` FROM `plugins` WHERE `name`='events' LIMIT 1", true );
        if ( $q > 0 )
            print_msg( 'اين ماژول قبلا نصب شده است.', 'Info' );
        else
        {
            global $information;
            $d->Query( "INSERT INTO `plugins` SET `name`='events',`title`='$information[name]',`stat`='0'" );
            print_msg( 'ماژول با موفقيت نصب شد.', 'Success' );
            activateop();
        }
    }

    function uninstallop()
    {
        global $d;
        $q = $d->getrows( "SELECT `stat` FROM `plugins` WHERE `name`='events' LIMIT 1", true );
        if ( $q <= 0 )
        {
            print_msg( 'اين ماژول نصب نشده است يا استاندارد نيست.', 'Info' );
        }
        else
        {
            $d->Query( "DELETE FROM `plugins` WHERE `name`='events' LIMIT 1" );
            print_msg( 'ماژول با موفقيت حذف شد.', 'Success' );
        }
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
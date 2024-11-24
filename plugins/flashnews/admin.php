<?php

if ( !defined( 'plugins_admin_area' ) )
    die( 'invalid access' );
if ( !isset( $permissions['access_admin_area'] ) )
    die( 'invalid access' );
if ( $permissions['access_admin_area'] != '1' )
    die( 'invalid access' );
$information = array(
    'name'        => 'اخبار سريع',
    'provider'    => 'بهنام حسن پور',
    'providerurl' => 'http://www.rashcms.com',
    'install'     => true,
    'uninstall'   => true,
    'activate'    => true,
    'inactivate'  => true,
    'data'        => array(
        'title' => array( 'name' => 'عنوان', 'value' => 'اخبار سريع' ),
        'icon'  => array( 'name' => 'آیکن', 'value' => '', 'type' => 'icon' ),
    )
);
$tpl->assign( 'first', '' );
if ( defined( 'methods' ) )
{

    function defaultop()
    {
        print_msg( 'تنظيمات اين ماژول در يك زيرمنوي اختصاصي مي باشند.', 'Info' );
    }

    function inactivateop()
    {
        global $d;
        $d->Query( "UPDATE `plugins` SET `stat`='0' WHERE `name`='flashnews' LIMIT 1" );
        print_msg( 'ماژول با موفقيت غير فعال شد.', 'Success' );
    }

    function activateop()
    {
        global $d;
        $d->Query( "UPDATE `plugins` SET `stat`='1' WHERE `name`='flashnews' LIMIT 1" );
        print_msg( 'ماژول با موفقيت فعال شد.', 'Success' );
    }

    function installop()
    {
        global $d;
        $q = $d->getrows( "SELECT `stat` FROM `plugins` WHERE `name`='flashnews' LIMIT 1", true );
        if ( $q > 0 )
            print_msg( 'اين ماژول قبلا نصب شده است.', 'Info' );
        else
        {
            global $information;
            $q = $d->Query( "CREATE TABLE IF NOT EXISTS `news` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
    `link` varchar(255) CHARACTER SET utf8 NOT NULL,
	`title` varchar(255) CHARACTER SET utf8 NOT NULL,
	PRIMARY KEY (`id`)
	) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;" );

            $oid = $d->getmax( 'oid', 'menus' );
            $q   = $d->Query( "INSERT INTO `menus` SET `oid`='$oid',`name`='flashnews',`title`='$information[name]',`url`='flashnews.php',`type`='1'" );
            $q   = $d->Query( "INSERT INTO `plugins` SET `name`='flashnews',`title`='$information[name]',`stat`='0'" );
            print_msg( 'ماژول با موفقيت نصب شد.', 'Success' );
            activateop();
        }
    }

    function uninstallop()
    {
        global $d;
        $q = $d->getrows( "SELECT `stat` FROM `plugins` WHERE `name`='flashnews' LIMIT 1", true );
        if ( $q <= 0 )
            print_msg( 'اين ماژول نصب نشده است يا استاندارد نيست.', 'Info' );
        else
        {
            $q = $d->Query( "DROP TABLE `news`" );
            $q = $d->Query( "DELETE FROM `menus` WHERE `name`='flashnews' LIMIT 1" );
            $q = $d->Query( "DELETE FROM `plugins` WHERE `name`='flashnews' LIMIT 1" );
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
?>
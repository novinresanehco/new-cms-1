<?php

if ( !defined( 'plugins_admin_area' ) )
    die( 'invalid access' );
if ( !isset( $permissions['access_admin_area'] ) )
    die( 'invalid access' );
if ( $permissions['access_admin_area'] != '1' )
    die( 'invalid access' );
$information = array(
    'name'        => 'لينكدوني',
    'provider'    => 'رضا شاهرخيان',
    'providerurl' => 'http://samaneh.com',
    'install'     => true,
    'uninstall'   => true,
    'activate'    => true,
    'inactivate'  => true,
    'data'        => array(
        'title' => array( 'name' => 'عنوان', 'value' => 'لینکدونی' ),
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
        $d->Query( "UPDATE `plugins` SET `stat`='0' WHERE `name`='simplelink' LIMIT 1" );
        print_msg( 'ماژول با موفقيت غير فعال شد.', 'Success' );
    }

    function activateop()
    {
        global $d;
        $d->Query( "UPDATE `plugins` SET `stat`='1' WHERE `name`='simplelink' LIMIT 1" );
        print_msg( 'ماژول با موفقيت فعال شد.', 'Success' );
    }

    function installop()
    {
        global $d;
        $q = $d->getrows( "SELECT `stat` FROM `plugins` WHERE `name`='simplelink' LIMIT 1", true );
        if ( $q > 0 )
            print_msg( 'اين ماژول قبلا نصب شده است.', 'Info' );
        else
        {
            global $information;
            $q   = $d->Query( "CREATE TABLE IF NOT EXISTS `link` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`title` varchar(255) CHARACTER SET utf8 NOT NULL,
	`url` varchar(255) CHARACTER SET utf8 NOT NULL,
	`des` text CHARACTER SET utf8 NOT NULL,
	`hits` int(5) NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`)
	) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;" );
            $q   = $d->Query( "INSERT INTO `link` (`id`, `title`, `url`, `des`, `hits`) VALUES
(1, 'سيستم مديريت محتواي راش', 'http://samaneh.com/', 'سيستم مديريت محتواي راش', 0),
(2, 'سامانه نظر سنجي راش سي ام اس', 'http://samaneh.ir', 'سامانه نظر سنجي راش سي ام اس', 0),
(3, 'سيستم افزايش آمار هوشمند مجيك', 'http://dir.magictools.ir', 'سيستم افزايش آمار هوشمند مجيك', 0),
(4, 'شبكه آموزش پارسيان', 'http://educator.ir', 'شبكه آموزش پارسيان', 0),
(5, 'انجمن پشتيباني راش سي ام اس', 'http://forum.samaneh.com', 'انجمن پشتيباني راش سي ام اس', 0);" );
            $oid = $d->getmax( 'oid', 'menus' );
            $q   = $d->Query( "INSERT INTO `menus` SET `oid`='$oid',`name`='simplelink',`title`='$information[name]',`url`='simplelink.php',`type`='1'" );
            $q   = $d->Query( "INSERT INTO `plugins` SET `name`='simplelink',`title`='$information[name]',`stat`='0'" );
            print_msg( 'ماژول با موفقيت نصب شد.', 'Success' );
        }
    }

    function uninstallop()
    {
        global $d;
        $q = $d->getrows( "SELECT `stat` FROM `plugins` WHERE `name`='simplelink' LIMIT 1", true );
        if ( $q <= 0 )
            print_msg( 'اين ماژول نصب نشده است يا استاندارد نيست.', 'Info' );
        else
        {
            $q = $d->Query( "DROP TABLE `link`" );
            $q = $d->Query( "DELETE FROM `menus` WHERE `name`='simplelink' LIMIT 1" );
            $q = $d->Query( "DELETE FROM `plugins` WHERE `name`='simplelink' LIMIT 1" );
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
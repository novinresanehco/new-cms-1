<?php

if ( !defined( 'plugins_admin_area' ) )
    die( 'invalid access' );
if ( !isset( $permissions['access_admin_area'] ) )
    die( 'invalid access' );
if ( $permissions['access_admin_area'] != '1' )
    die( 'invalid access' );
$information = array(
    'name'        => 'خبر نامه',
    'provider'    => 'رضا شاهرخيان',
    'providerurl' => 'http://samaneh.com',
    'install'     => true,
    'uninstall'   => true,
    'activate'    => true,
    'inactivate'  => true,
    'data'        => array(
        'title' => array( 'name' => 'عنوان', 'value' => 'خبر نامه' ),
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
        $d->Query( "UPDATE `plugins` SET `stat`='0' WHERE `name`='newsletter' LIMIT 1" );
        print_msg( 'ماژول با موفقيت غير فعال شد.', 'Success' );
    }

    function activateop()
    {
        global $d;
        $d->Query( "UPDATE `plugins` SET `stat`='1' WHERE `name`='newsletter' LIMIT 1" );
        print_msg( 'ماژول با موفقيت فعال شد.', 'Success' );
    }

    function installop()
    {
        global $d;
        $q = $d->getrows( "SELECT `stat` FROM `plugins` WHERE `name`='newsletter' LIMIT 1", true );
        if ( $q > 0 )
            print_msg( 'اين ماژول قبلا نصب شده است.', 'Info' );
        else
        {
            global $information;
            $q   = $d->Query( "CREATE TABLE IF NOT EXISTS `nl` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`mail` varchar(255) NOT NULL,
	PRIMARY KEY (`id`)
	) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;" );
            $q   = $d->Query( "CREATE TABLE IF NOT EXISTS `nls` (
	`SmtpHost` varchar(255) NOT NULL,
	`SmtpUser` varchar(255) NOT NULL,
	`SmtpPassword` varchar(255) NOT NULL,
	`mailperpack` varchar(255) NOT NULL DEFAULT '20',
	`msperpack` varchar(255) NOT NULL DEFAULT '10'
	) ENGINE=MyISAM DEFAULT CHARSET=latin1;" );
            $q   = $d->Query( "INSERT INTO `nls` (`SmtpHost`, `SmtpUser`, `SmtpPassword`, `mailperpack`, `msperpack`) VALUES
	('', '', '', '', '');" );
            $oid = $d->getmax( 'oid', 'menus' );
            $q   = $d->Query( "INSERT INTO `menus` SET `oid`='$oid',`name`='newsletter',`title`='$information[name]',`url`='newsletter.php',`type`='1'" );
            $q   = $d->Query( "INSERT INTO `plugins` SET `name`='newsletter',`title`='$information[name]',`stat`='0'" );
            print_msg( 'ماژول با موفقيت نصب شد.', 'Success' );
        }
    }

    function uninstallop()
    {
        global $d;
        $q = $d->getrows( "SELECT `stat` FROM `plugins` WHERE `name`='newsletter' LIMIT 1", true );
        if ( $q <= 0 )
            print_msg( 'اين ماژول نصب نشده است يا استاندارد نيست.', 'Info' );
        else
        {
            $q = $d->Query( "DROP TABLE `nl`" );
            $q = $d->Query( "DROP TABLE `nls`" );
            $q = $d->Query( "DELETE FROM `menus` WHERE `name`='newsletter' LIMIT 1" );
            $q = $d->Query( "DELETE FROM `plugins` WHERE `name`='newsletter' LIMIT 1" );
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
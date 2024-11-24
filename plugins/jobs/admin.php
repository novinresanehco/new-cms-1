<?php

if ( !defined( 'plugins_admin_area' ) )
    die( 'invalid access' );
if ( !isset( $permissions['access_admin_area'] ) )
    die( 'invalid access' );
if ( $permissions['access_admin_area'] != '1' )
    die( 'invalid access' );
$information = array(
    'name'        => 'وظایف و کارها',
    'provider'    => 'رضا شاهرخيان',
    'providerurl' => 'http://shahrokhian.com',
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
        if ( !isset( $_GET['act'] ) )
        {
            exit; //silence is golden
        }
        global $d, $config;
        switch ( $_GET['act'] )
        {
            case 'new':
                $theme = new samaneh();
                $theme->load( dirname( __FILE__ ) . '/new.html' );
                $theme->assign( 'date', timeboxgen( 'job' ) );
                $theme->showit();
                exit;
                break;
            case 'edit':
                if ( !isset( $_GET['id'] ) OR !is_numeric( $_GET['id'] ) )
                {
                    exit;
                }
                $job = $d->Query( 'SELECT * FROM `jobs` WHERE `jobID`=\'' . $_GET['id'] . '\' LIMIT 1' );
                if ( $d->getRows( $job ) !== 1 )
                {
                    exit;
                }
                $job   = $d->fetch( $job );
                $theme = new samaneh();
                $theme->load( dirname( __FILE__ ) . '/edit.html' );
                $theme->assign( 'title', $job['title'] );
                $theme->assign( 'jobID', $job['jobID'] );
                $date  = mytime( "Y/m/d/H", $job['time'], $config['dzone'] );
                list($year, $month, $day, $hour) = explode( '/', $date );
                define( 'thisyear', $year );
                define( 'thismonth', $month );
                define( 'thisday', $day );
                define( 'thishour', $hour );
                $theme->assign( 'date', timeboxgen( 'job' ) );
                $theme->showit();
                exit;
                break;
            case 'remove':
                exit;
                break;

            default:
                exit;
                break;
        }
    }

    function inactivateop()
    {
        global $d;
        $d->Query( "UPDATE `plugins` SET `stat`='0' WHERE `name`='jobs' LIMIT 1" );
        print_msg( 'ماژول با موفقيت غير فعال شد.', 'Success' );
    }

    function activateop()
    {
        global $d;
        $d->Query( "UPDATE `plugins` SET `stat`='1' WHERE `name`='jobs' LIMIT 1" );
        print_msg( 'ماژول با موفقيت فعال شد.', 'Success' );
    }

    function installop()
    {
        global $d;
        $q = $d->getrows( "SELECT `stat` FROM `plugins` WHERE `name`='jobs' LIMIT 1", true );
        if ( $q > 0 )
            print_msg( 'اين ماژول قبلا نصب شده است.', 'Info' );
        else
        {
            global $information;
            $d->Query( "INSERT INTO `plugins` SET `name`='jobs',`title`='$information[name]',`stat`='0'" );
            $d->Query( "CREATE TABLE IF NOT EXISTS `jobs` (
            `jobID` INT(10) NOT NULL AUTO_INCREMENT,
            `title` VARCHAR(255) NOT NULL,
            `time` VARCHAR(50) NOT NULL,
            PRIMARY KEY (`jobID`)
            )
            COLLATE='utf8_general_ci'
            ENGINE=InnoDB;
            " );
            print_msg( 'ماژول با موفقيت نصب شد.', 'Success' );
            activateop();
        }
    }

    function uninstallop()
    {
        global $d;
        $q = $d->getrows( "SELECT `stat` FROM `plugins` WHERE `name`='jobs' LIMIT 1", true );
        if ( $q <= 0 )
            print_msg( 'اين ماژول نصب نشده است يا استاندارد نيست.', 'Info' );
        else
        {
            $d->Query( "DELETE FROM `plugins` WHERE `name`='jobs' LIMIT 1" );
            $d->Query( "DROP TABLE `jobs`" );
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
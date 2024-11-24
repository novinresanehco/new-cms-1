<?php

define( 'head', true );
if ( !empty( $_GET['plugin'] ) )
{
    define( 'page', $_GET['plugin'] );
}
else
{
    define( 'page', 'plugins' );
}
$pageTheme = 'plugins.htm';
$pagetitle = 'پلاگین ها';
define( 'tabs', true );
$tabs = array( 'نخست' );
include('header.php');
$error = '';
$plugin = (empty( $_GET['plugin'] )) ? die( 'Samaneh:: Invalid plugin name' ) : safeurl( $_GET['plugin'] );
$task = (empty( $_GET['task'] )) ? 'none' : $_GET['task'];
$information = array(
    'name' => $lang['unknown'],
    'provider' => $lang['unknown'],
    'providerurl' => $lang['unknown'],
    'install' => false,
    'uninstall' => false,
    'activate' => false,
    'inactivate' => false,
);
define( 'plugins_admin_area', true );
define( 'methods', true );
if ( !is_dir( '../plugins/' . $plugin ) )
    $error = $lang['plugin']['wplugin'];
else
if ( !is_file( '../plugins/' . $plugin . '/admin.php' ) )
    $error = $lang['plugin']['invalid'];
else
{
    require('../plugins/' . $plugin . '/admin.php');
}
if ( empty( $error ) )
    switch ( $task )
    {
        case 'install':
            if ( isset( $information['install'] ) )
            {
                if ( $information['install'] )
                {
                    if ( function_exists( 'installop' ) )
                        installop();
                    else
                        $error = $lang['plugin']['invalid'];
                }
                else
                    $error = $lang['plugin']['ioperation'];
            }
            else
                $error = $lang['plugin']['invalid'];
            break;
        case 'uninstall';
            if ( isset( $information['uninstall'] ) )
            {
                if ( $information['uninstall'] )
                {
                    if ( function_exists( 'uninstallop' ) )
                        uninstallop();
                    else
                        $error = $lang['plugin']['invalid'];
                }
                else
                    $error = $lang['plugin']['ioperation'];
            }
            else
                $error = $lang['plugin']['invalid'];
            break;
        case 'activate';
            if ( isset( $information['activate'] ) )
            {
                if ( $information['activate'] )
                {
                    if ( function_exists( 'activateop' ) )
                    {
                        activateop();
                    }
                    else
                        $error = $lang['plugin']['invalid'];
                }
                else
                    $error = $lang['plugin']['ioperation'];
            }
            else
                $error = $lang['plugin']['invalid'];
            break;
        case 'inactivate';
            if ( isset( $information['inactivate'] ) )
            {
                if ( $information['inactivate'] )
                {
                    if ( function_exists( 'inactivateop' ) )
                        inactivateop();
                    else
                        $error = $lang['plugin']['invalid'];
                }
                else
                    $error = $lang['plugin']['ioperation'];
            }
            else
                $error = $lang['plugin']['invalid'];
            break;
        case 'none':
            if ( function_exists( 'defaultop' ) )
            {
                //check whether module is enabled !
                $splugin = safe( $plugin );
                $q = $d->Query( "SELECT * FROM `plugins` WHERE `stat`!='0' AND `name`='$splugin' LIMIT 1" );
                if ( $d->getRows( $q ) !== 1 )
                {
                    $error = $lang['disabled'];
                }
                else
                {
                    defaultop();
                }
            }
            else
            {
                $error = $lang['plugin']['invalid'];
            }
            break;
        default :
            @HEADER( "LOCATION : plugin.php" );
            die( $lang['plugin']['invalid'] );
    }
if ( !empty( $error ) )
    $tpl->assign( array(
        'plugin_name' => $information['name'],
        'Error' => 1,
        'msg' => $error,
        'first' => '',
    ) );
$htpl->assign( "pagetitle", $information['name'] );
$htpl->assign( "sitetitle", $information['name'] );
$tpl->assign( "plugin_name", $information['name'] );
$htpl->showit();
$tpl->showit();
$ftpl->showit();
<?php

define( 'head', true );
define( 'page', 'plugins' );
$pageTheme = 'plugin.htm';
$pagetitle = 'پلاگین ها';
define( 'tabs', true );
$tabs = array( 'پلاگین ها' );
include('header.php');
$handle = opendir( '../plugins' );
define( 'plugins_admin_area', true );
while ( $file = readdir( $handle ) )
{
    if ( strpos( $file, '.' ) === false )
    {
        $file = safe( $file );

        if ( is_file( '../plugins/' . $file . '/admin.php' ) && !in_array( $file, $hide_plugins ) )
        {
            if ( isset( $information ) )
                unset( $information );
            $q = $d->getrows( "SELECT `stat` FROM `plugins` WHERE `name`='$file' LIMIT 1", true );
            if ( $q > 0 )
                $q = $d->getrowvalue( "stat", "SELECT `stat` FROM `plugins` WHERE `name`='$file' LIMIT 1", true );
            else
                $q = '-1';
            $stat = $lang['unknown'];
            if ( $q == '-1' )
                $stat = $lang['uninstalled'];
            elseif ( $q == 0 )
                $stat = $lang['inactive'];
            else
                $stat = $lang['active'];
            include('../plugins/' . safeurl( $file ) . '/admin.php');

            $name = isset( $information['name'] ) ? safe( $information['name'] ) : $lang['unknown'];
            $provider = isset( $information['provider'] ) ? safe( $information['provider'] ) : $lang['unknown'];
            $url = isset( $information['providerurl'] ) ? safe( $information['providerurl'] ) : 'http://samaneh.com/';
            $arr = array(
                'name' => $name,
                'provider' => $provider,
                'url' => $url,
                'stat' => $stat,
                'file' => $file,
            );
            if ( isset( $information['install'] ) && ($q == '-1') )
                if ( $information['install'] )
                {
                    $arr['Install'] = 1;
                }
            if ( isset( $information['activate'] ) && ($q == 0) && !(isset( $arr['Install'] )) )
                if ( $information['activate'] )
                    $arr['Activate'] = 1;
            if ( isset( $information['uninstall'] ) && ($q != '-1') )
                if ( $information['uninstall'] )
                    $arr['UInstall'] = 1;
            if ( isset( $information['inactivate'] ) && ($q != 0 && $q != '-1') )
                if ( $information['inactivate'] )
                    $arr['InActivate'] = 1;
            $tpl->block( 'pluginslist', $arr );
        }
    }
}
closedir( $handle );

$htpl->showit();
$tpl->showit();
$ftpl->showit();
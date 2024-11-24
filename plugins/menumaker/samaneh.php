<?php

if ( !defined( 'plugins-inc' ) OR !is_array( @$data ) )
{
    die( '<a href="http://samaneh.it" target=_blank>samaneh</a> :: Invalid calling of ' . basename( __FILE__ ) );
}
$sql = $d->query( 'SELECT * FROM `ns_menu_group`' );
$sql = $d->fetch( $sql );
require_once( 'styles.config.php' );
if ( isset( $nsmstyle[$sql['style']]['css'] ) && is_array( $nsmstyle[$sql['style']]['css'] ) )
{
    foreach ( $nsmstyle[$sql['style']]['css'] as $css )
    {
        $headercss[] = $config['site'] . 'plugins/menumaker/css/' . $css;
    }
}
if ( isset( $nsmstyle[$sql['style']]['js'] ) && is_array( $nsmstyle[$sql['style']]['js'] ) )
{
    foreach ( $nsmstyle[$sql['style']]['js'] as $js )
    {
        $headerjs[] = $config['site'] . 'plugins/menumaker/js/' . $js;
    }
}
var_dump( $headercss );
var_dump( $headerjs );
exit;
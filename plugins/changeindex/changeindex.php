<?php

if ( !defined( 'plugins-inc' ) OR !is_array( @$data ) )
{
    die( '<a href="http://help.mihanphp.com/plugins" target=_blank>samaneh</a> :: Invalid calling of ' . basename( __FILE__ ) );
}
//$pageTheme ='first.htm';
if ( !empty( $config['mainpagetheme'] ) && empty( $_SERVER['REQUEST_URI'] ) )
{
    $tpl->load( current_theme_dir . $config['mainpagetheme'] );
}
if ( !empty( $config['mainpagepost'] ) && is_numeric( $config['mainpagepost'] ) && !isset( $_GET['plugins'] ) )
{
    $ctimestamp = time();
    define( 'customized_post_query', true );
    $post_q = "select * FROM `data` WHERE  `date` <= '$ctimestamp' AND (`expire`='0' OR `expire`='' OR `expire`>$ctimestamp) AND `id`='$config[mainpagepost]' LIMIT 1";
    define( 'customized_post_query_value', $post_q );
    define( 'customized_post_query_value_t', 0 );
}
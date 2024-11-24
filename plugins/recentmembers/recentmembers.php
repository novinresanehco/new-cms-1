<?php

if ( !defined( 'plugins-inc' ) OR !is_array( @$data ) )
{
    die( '<a href="http://help.mihanphp.com/plugins" target=_blank>samaneh</a> :: Invalid calling of ' . basename( __FILE__ ) );
}

function recentmembers_output()
{
    global $tpl, $d, $config;
    $args = func_get_args();
    if ( !empty( $args[0] ) )
    {
        $tpl->assign( $args[0] );
    }
    $itpl = new samaneh();
    $itpl->assign( 'theme_url', '/theme/core/' . $config['theme'] . '/' );
    $itpl->load( 'plugins/recentmembers/block-theme.html' );
    if ( isset( $args[0]['count'] ) && is_numeric( $args[0]['count'] ) )
    {
        $count = $args[0]['count'];
    }
    else
    {
        $count = $config['nlast'];
    }
    if ( !is_numeric( $count ) OR $count <= 0 )
    {
        $count = 5;
    }
    $iq = $d->Query( "SELECT `showname`,`u_id`,`user` FROM `member` WHERE `stat`='1' ORDER BY `u_id` DESC LIMIT  0,$count" );
    while ( $idata = $d->fetch( $iq ) )
    {
        $itpl->block( 'RecentUsers', array(
            'user' => $idata['user'],
            'id' => $idata['u_id'],
            'showname' => $idata['showname'],
        ) );
    }
    return $itpl->dontshowit();
}

$tpl->assign( 'recentmembers', recentmembers_output() );
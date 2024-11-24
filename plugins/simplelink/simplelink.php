<?php

if ( !defined( 'plugins-inc' ) OR !is_array( @$data ) )
{
    die( '<a href="http://help.mihanphp.com/plugins" target=_blank>samaneh</a> :: Invalid calling of ' . basename( __FILE__ ) );
}
$tpl->assign( 'links', simplelink_output() );

function simplelink_output()
{
    global $tpl, $config, $d;
    $args = func_get_args();
    if ( !empty( $args[0] ) )
    {
        $tpl->assign( $args[0] );
    }
    $data = $d->Query( "SELECT * FROM `plugins` WHERE `name`='simplelink' LIMIT 1" );
    $data = $d->fetch( $data );
    $itpl = new samaneh();
    $itpl->load( 'plugins/simplelink/block-theme.html' );
    $itpl->assign( 'theme_url', core_theme_url );

    $iq = $d->Query( "SELECT * FROM `link` ORDER BY `id` LIMIT $config[nlast]" );
    if ( $data['stat'] == 2 )
    {
        $itpl->assign( 'Hits', 1 );
    }
    while ( $idata = $d->fetch( $iq ) )
    {
        $url = ($data['stat'] == 1) ? $idata['url'] : $config['site'] . 'plugins/simplelink/method/redirect/id-' . $idata['id'] . '.html';
        $url = ($config['seo'] != 1 && $data['stat'] != 1) ? $config['site'] . 'index.php?plugins=simplelink&method=redirect&n=' . $idata['id'] : $url;
        $itpl->block( 'links', array(
            'title' => $idata['title'],
            'desc' => $idata['des'],
            'url' => $url,
            'clicks' => $idata['hits'],
        ) );
    }
    return $itpl->dontshowit();
}

if ( isset( $_GET['plugins'] ) && $_GET['plugins'] == 'simplelink' )
{
    if ( @$_GET['method'] == 'redirect' && is_numeric( @$_GET['n'] ) )
    {
        $d->Query( "UPDATE `link` SET `hits`=`hits`+1 WHERE `id`='$_GET[n]' LIMIT 1" );
        if ( $d->affected() <= 0 )
        {
            HEADER( "LOCATION: index.php" );
            die();
        }
        $r = $d->getrowvalue( "url", "SELECT `url` FROM `link` WHERE `id`='$_GET[n]' LIMIT 1", true );
        if ( empty( $r ) )
        {
            HEADER( "LOCATION: index.php" );
            die();
        }
        HEADER( "LOCATION: $r" );
    }
}
<?php

if ( !defined( 'plugins-inc' ) OR !is_array( @$data ) )
{
    die( '<a href="http://help.samaneh.it/plugins" target=_blank>samaneh</a> :: Invalid calling of ' . basename( __FILE__ ) );
}
$flsahnewstpl = new samaneh();
$flsahnewstpl->load( 'plugins/flashnews/block-theme.html' );
$flsahnewstpl->assign( 'theme_url', 'theme/core/' . $config['theme'] . '/' );

$iq    = $d->Query( "SELECT * FROM `news` ORDER BY `id` DESC " );
while ( $idata = $d->fetch( $iq ) )
{
    $flsahnewstpl->block( 'flashnews', array(
        'title' => $idata['title'],
        'link'  => $idata['link'],
    ) );
}

function flashnews_output()
{
    global $flsahnewstpl;
    return $flsahnewstpl->dontshowit();
}

$tpl->assign( 'flashnews', $flsahnewstpl->dontshowit() );
//unset( $flsahnewstpl );
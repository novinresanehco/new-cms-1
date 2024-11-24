<?php

if ( !defined( 'plugins-inc' ) OR !is_array( @$data ) )
{
    die( '<a href="http://help.mihanphp.com/plugins" target=_blank>samaneh</a> :: Invalid calling of ' . basename( __FILE__ ) );
}

function gllastpic_output()
{
    global $tpl, $config, $d;
    $args = func_get_args();
    if ( !empty( $args[0] ) )
    {
        $args = $args[0];
    }

    $itpl = new samaneh();
    $itpl->load( 'plugins/gllastpic/theme.html' );
    $itpl->assign( 'siteurl', $config['site'] );
    $itpl->assign( 'theme', $config['theme'] );
    $itpl->assign( 'site', $config['site'] );
    $itpl->assign( $args );
    $total = 10;
    if ( isset( $args['count'] ) && is_numeric( $args['count'] ) && $args['count'] > 0 )
    {
        $total = $args['count'];
    }
    $q = $d->Query( "SELECT `id`,`title`,`thumb` FROM `gallery_images` ORDER BY `id` DESC LIMIT $total" );
    while ( $data = $d->fetch( $q ) )
    {
        $itpl->block( 'images', array(
            'id' => $data['id'],
            'title' => $data['title'],
            'thumb' => $data['thumb'],
        ) );
    }

    if ( isset( $_GET['plugins'] ) && $_GET['plugins'] == 'gllastpic' && isset( $_POST['js'] ) )
    {
        //die( $itpl->dontshowit() );
    }
    else
    {
        $itpl->assign( 'full', 1 );
        //$tpl->assign( 'gllastpic', $itpl->dontshowit() );
    }

    return $itpl->dontshowit();
}

$tpl->assign( 'glast', gllastpic_output() );
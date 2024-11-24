<?php

if ( !defined( 'plugins-inc' ) OR !is_array( @$data ) )
{
    die( '<a href="http://help.mihanphp.com/plugins" target=_blank>samaneh</a> :: Invalid calling of ' . basename( __FILE__ ) );
}

function recentcomments_output()
{
    global $tpl, $config, $d;
    $args = func_get_args();
    if ( !empty( $args[0] ) )
    {
        $args = $args[0];
    }
    else
    {
        $args = array();
    }
    $itpl = new samaneh();
    $itpl->load( 'plugins/recentcomments/block-theme.html' );
    $itpl->assign( 'theme_url', 'theme/core/' . $config['theme'] . '/' );
    if ( $config['comment'] != '4' )
    {
        $len   = ( isset( $args['length'] ) && is_numeric( $args['length'] ) ) ? $args['length'] : 50;
        $count = ( isset( $args['count'] ) && is_numeric( $args['count'] ) ) ? $args['count'] : 10;
        $end   = '';
        $iq    = $d->Query( "SELECT `c_author`,`url`,`text`,`p_id` FROM `comments` WHERE `active`!='2' AND `active`!='0' ORDER BY `c_id` DESC LIMIT  0,$count" );
        while ( $idata = $d->fetch( $iq ) )
        {
            $short = mb_substr( $idata['text'], 0, $len, 'UTF-8' );
            $itpl->block( 'RecentComments', array(
                'author' => $idata['c_author'],
                'url'    => quick_post_link( $idata['p_id'] ),
                'short'  => $short . $end,
            ) );
        }
    }
    if ( count( $args ) > 0 )
    {
        $itpl->assign( $args );
    }

    $out = $itpl->dontshowit();
    return $out;
}

$tpl->assign( 'RecentComments', recentcomments_output() );
//unset($itpl);
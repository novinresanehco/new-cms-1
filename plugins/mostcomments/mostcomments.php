<?php

if ( !defined( 'plugins-inc' ) OR !is_array( @$data ) )
{
    die( '<a href="http://help.mihanphp.com/plugins" target=_blank>samaneh</a> :: Invalid calling of ' . basename( __FILE__ ) );
}

function mostcomments_output()
{
    global $tpl, $config, $d;
    $args = func_get_args();
    if ( !empty( $args[0] ) )
    {
        $args = $args[0];
    }
    $itpl = new samaneh();
    $itpl->load( 'plugins/mostcomments/block-theme.html' );
    $itpl->assign( 'theme_url', 'theme/core/' . $config['theme'] . '/' );
    if ( $config['comment'] != '4' )
    {
        $len = ( isset( $args['length'] ) && is_numeric( $args['length'] ) ) ? $args['length'] : 50;
        $count = ( isset( $args['count'] ) && is_numeric( $args['count'] ) ) ? $args['count'] : 10;

        $iq = $d->Query( "SELECT `p_id`,COUNT(`p_id`) as `number` FROM `comments` WHERE `active`!='2' AND `active`!='0' GROUP BY p_id ORDER BY `number` DESC LIMIT  0,$count" );
        while ( $idata = $d->fetch( $iq ) )
        {
            $post = $d->Query( "SELECT `id`,`entitle`,`title`,`timage` FROM `data` WHERE `id`='$idata[p_id]' LIMIT 1 " );
            $post = $d->fetch( $post );
            if ( empty( $post['id'] ) )
            {
                continue;
            }

            $itpl->block( 'MostComments', array(
                'title' => $post['title'],
                'url' => quick_post_link( $idata['p_id'] ),
                'image' => $post['timage'],
            ) );
        }
    }
    $itpl->assign( $args );
    $out = $itpl->dontshowit();
    return $out;
}

$tpl->assign( 'MostComments', mostcomments_output() );
//unset($itpl);
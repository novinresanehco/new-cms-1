<?php

if ( !defined( 'plugins-inc' ) OR !is_array( @$data ) )
{
    die( '<a href="http://help.mihanphp.com/plugins" target=_blank>samaneh</a> :: Invalid calling of ' . basename( __FILE__ ) );
}

function random_output()
{
    global $d, $config;
    $args  = func_get_args();
    $count = $config['nlast'];
    if ( isset( $args[0]['count'] ) && is_numeric( $args[0]['count'] ) && $args[0]['count'] > 0 )
    {
        $count = $args[0]['count'];
    }
    $randomtpl  = new samaneh();
    $randomtpl->load( 'plugins/random/block-theme.html' );
    $randomtpl->assign( 'theme_url', core_theme_url );
    $ctimestamp = time();
    $iq         = $d->Query( "SELECT * FROM `data` WHERE `date` <= '$ctimestamp' AND (`expire`='0' OR `expire`='' OR `expire`>$ctimestamp) AND (`show`!='4' || `show`='2') ORDER BY RAND() DESC LIMIT  0,$count" );
    while ( $idata      = $d->fetch( $iq ) )
    {
        $date = mytime( "Y-m-d", $idata['date'], $config['dzone'] );
        $date = explode( "-", $date );
        $cat  = isset( $cats[$idata['cat_id']] ) ? $cats[$idata['cat_id']] : '';
        $url  = get_post_link( array( "%postid%" => $idata['id'], "%subjectid%" => $idata['cat_id'], "%sname%" => $cat, "%sslug%" => $cat, "%posttitle%" => $idata['title'], "%postslug%" => $idata['entitle'], "%postdday%" => $date[2], "%postdmonth%" => $date[1], "%postyear%" => $date[0] ) );
        $randomtpl->block( 'random', array(
            'title' => $idata['title'],
            'url'   => $url,
        ) );
    }
    return $randomtpl->dontshowit();
}

$tpl->assign( 'randoms', random_output() );
//unset( $randomtpl );

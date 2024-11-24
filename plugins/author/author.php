<?php

if ( !defined( 'plugins-inc' ) OR !is_array( @$data ) )
    die( '<a href="http://help.mihanphp.com/plugins" target=_blank>samaneh</a> :: Invalid calling of ' . basename( __FILE__ ) );
$iq    = $d->Query( "SELECT `u_id` FROM `permissions` WHERE  `access_admin_area` = '1' ORDER BY `u_id` ASC" );
$itpl  = new samaneh();
$itpl->load( 'plugins/author/block-theme.html' );
$itpl->assign( 'theme_url', core_theme_url );
while ( $idata = mysql_fetch_array( $iq ) )
{
    $iiq                     = $d->Query( "SELECT `name`,`showname`,`user` FROM `member` WHERE  `u_id` = '$idata[u_id]' LIMIT 1" );
    $iiq                     = $d->fetch( $iiq );
    $pinfo                   = $d->Query( "SELECT COUNT(*) as `num` FROM `data` WHERE  `author` = '$idata[u_id]'" );
    $pinfo                   = $d->fetch( $pinfo );
    $name                    = (empty( $iiq['showname'] )) ? $iiq['name'] : $iiq['showname'];
    $url                     = get_user_link( array(
        '%id%'   => $idata['u_id'],
        '%user%' => $iiq['user'],
            ) );
    $authors[$idata['u_id']] = $name;
    $itpl->block( 'Authors', array(
        'name' => $name,
        'num'  => $pinfo['num'],
        'url'  => $url,
    ) );
}
$tpl->assign( 'Authors', $itpl->dontshowit() );
unset( $itpl );

<?php

defined( 'news_security' ) or exit;
if ( !$login )
{
    $tpl->assign( 'Guest', 1 );
}
else
    $tpl->assign( 'Member', 1 );
//0:inactive|9:active - but need to be called
if ( !isset( $_GET['plugins'] ) )
{
    $q = $d->Query( "SELECT * FROM `plugins` WHERE `stat`!='0' AND `stat`!='9'" );
}
else
{
    $q = $d->Query( "SELECT * FROM `plugins` WHERE `stat`!='0' AND (`stat`!='9' OR `name`='$_GET[plugins]')" );
}
define( 'plugins-inc', true );

while ( $data = $d->fetch( $q ) )
{
    $qtmp = $q;
    $plugins[$data['name']] = true;
    $name = safe( safeurl( $data['name'], true ) );
    if ( ctype_alnum( $name ) )
    {
        if ( is_dir( 'plugins/' . $name ) )
        {
            if ( file_exists( 'plugins/' . $name . '/' . $name . '.php' ) )
            {
                include_once('plugins/' . $name . '/' . $name . '.php');

            }
        }
    }
    $q = $qtmp;
}

$q = $d->Query( "SELECT * FROM `block` ORDER BY `order`" );
$fpos = array( '1' => 'top', '2' => 'down', '3' => 'right', '4' => 'left' );
while ( $data = $d->fetch( $q ) )
{
    if ( $data['plugins'] != 'none' )
    {
        if ( !isset( $plugins[$data['plugins']] ) )
        {
            continue;
        }
    }
    //1 : top
    if ( ($data['users'] == 3 && !$login) || $data['users'] == 2 || ($data['users'] == 1 && $login) )
    {
        $tpl->block( 'CMS' . $fpos[$data['pos']], array(
            'title' => $data['name'],
            'content' => $data['text'],
        ) );
    }
}

$tpl->assign( array(
    'siteurl' => $config['site'],
) );
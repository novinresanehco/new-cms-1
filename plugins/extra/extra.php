<?php

if ( !defined( 'plugins-inc' ) OR !is_array( @$data ) )
{
    die( '<a href="http://samaneh.it/plugins" target=_blank>samaneh</a> :: Invalid calling of ' . basename( __FILE__ ) );
}
$itpl = new samaneh();
$itpl->load( 'plugins/extra/block-theme.html' );
$itpl->assign( 'theme_url', core_theme_url );
$nopost = false;
if ( $data['stat'] != 1 )
{
    $nopost = true;
    $tpl->block( 'mp', array(
        'subject' => $config['sitetitle'],
        'sub_id' => 1,
        'sub_link' => 'index.php',
        'link' => 'index.php',
        'title' => $lang['404'],
        'body' => '<div class=error>' . $lang['disabled'] . '</div>',
    ) );
}
else
{
    $q = $d->Query( "SELECT * FROM `extra` ORDER BY `id` LIMIT $config[nlast]" );
    while ( $data = $d->fetch( $q ) )
    {
        if ( !( ( $data['users'] == 3 && !$login ) || $data['users'] == 2 || ($data['users'] == 1 && $login)) )
        {
            continue;
        }
        $url = $config['seo'] != 1 ? $config['site'] . 'index.php?plugins=extra&id=' . $data['id'] : quick_extra_link( $data['id'] );
        $itpl->block( 'extra', array(
            'title' => $data['title'],
            'url' => $url,
        ) );
    }

    if ( isset( $_GET['plugins'] ) )
    {
        if ( $_GET['plugins'] == 'extra' )
        {
            if ( isset( $_GET['id'] ) && is_numeric( $_GET['id'] ) )
            {

                $Themedir = "theme/core/" . $config['theme'] . '/';

                if ( file_exists( $Themedir . 'extra_' . $_GET['id'] . '.htm' ) )
                {
                    $pageTheme = 'extra_' . $_GET['id'] . '.htm';
                }
                else
                {
                    if ( !file_exists( $Themedir . 'plugin_extra.htm' ) )
                    {
                        $pageTheme = 'single.htm';
                    }
                }

                $nopost = true;
                $config['nlast'] = intval( $config['nlast'] );
                $q = $d->Query( "SELECT * FROM `extra` WHERE `id`='$_GET[id]' LIMIT $config[nlast]" );

                if ( $d->getrows( $q ) > 0 )
                {
                    $data = $d->fetch( $q );
                    $url = $config['seo'] != 1 ? $config['site'] . 'index.php?plugins=extra&id=' . $data['id'] : quick_extra_link( $data['id'] );
                    $pgtitle = $data['title'];
                    $pgtext = $data['text'];
                    $tpl->assign( 'sitetitle', $config['sitetitle'] . ' - ' . $data['title'] );
                    if ( !(($data['users'] == 3 && !$login) || $data['users'] == 2 || ($data['users'] == 1 && $login)) )
                    {
                        $pgtext = '<div class=error>' . $lang['limited_area'] . '<div>';
                    }
                }
                else
                {
                    $url = 'index.php';
                    $pgtitle = $config['sitetitle'];
                    $pgtext = '<div class=error>' . $lang['404'] . '<div>';
                }

                $tpl->block( 'mp', array(
                    'subject' => $config['sitetitle'],
                    'sub_id' => 1,
                    'sub_link' => 'index.php',
                    'link' => $url,
                    'title' => $pgtitle,
                    'body' => $pgtext,
                ) );
            }
        }
    }
    $tpl->assign( 'Extra', $itpl->dontshowit() );
}
if ( $nopost )
{
    $show_posts = false;
}
unset( $itpl );
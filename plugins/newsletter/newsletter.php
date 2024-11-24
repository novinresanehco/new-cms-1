<?php

if ( !defined( 'plugins-inc' ) OR !is_array( @$data ) )
{
    die( '<a href="http://help.mihanphp.com/plugins" target=_blank>samaneh</a> :: Invalid calling of ' . basename( __FILE__ ) );
}

function newsletter_output()
{
    $args = func_get_args();
    global $tpl, $show_posts, $lang, $d;
    if ( !empty( $args[0] ) )
    {
        $tpl->assign( $args[0] );
    }
    $itpl = new samaneh();
    $itpl->load( 'plugins/newsletter/block-theme.html' );
    $itpl->assign( 'theme_url', core_theme_url );
    $message = null;
    if ( isset( $_GET['plugins'] ) && $_GET['plugins'] == 'newsletter' && isset( $_POST['email'] ) )
    {
        $show_posts = false;
        if ( empty( $_POST['email'] ) )
        {
            $error = $lang['allneed'];
        }
        elseif ( !email( $_POST['email'] ) )
        {
            $error = $lang['wmail'];
        }
        else
        {
            $ex = $d->getrows( "SELECT `mail` FROM `nl` WHERE `mail`='$_POST[email]'", true );
            if ( $ex != '0' )
            {
                $error = $lang['exit_mail'];
            }
        }
        if ( !empty( $error ) )
        {
            $message = $error;
        }
        else
        {
            $query = $d->Query( "INSERT INTO `nl` SET `mail`='$_POST[email]'" );
            $message = ($query) ? $lang['email_sub'] : $lang['Error'];
        }
    }
    $itpl->assign( 'result', $message );
    return $itpl->dontshowit();
}

$tpl->assign( 'newsletter', newsletter_output() );
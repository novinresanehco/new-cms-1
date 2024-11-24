<?php

if ( !defined( 'plugins-inc' ) OR !is_array( @$data ) )
    die( '<a href="http://help.mihanphp.com/plugins" target=_blank>samaneh</a> :: Invalid calling of ' . basename( __FILE__ ) );

function member_output()
{
    $args = func_get_args();
    global $tpl, $show_posts, $lang, $config, $user, $info;
    $tpl->assign( @$args[0] );
	$user = new user();
    $itpl = new samaneh();
    $itpl->load( 'plugins/member/block-theme.html' );
    $itpl->assign( 'theme_url', core_theme_url );
    if ( $config['member_area'] != '1' )
    {
        $itpl->assign( 'Guest', 1 );
    }
    else
    {
        if ( !defined( 'reg_plugins_samaneh' ) )
        {
            define( 'reg_plugins_samaneh', 1 );
        }
        $login = ($config['member_area'] == '1') ? $user->checklogin() : false;
        if ( $login )
        {
            $name = empty( $info['showname'] ) ? $info['name'] : $info['showname'];
            $avatar = (empty( $info['avatar'] )) ? 'samaneh/images/no_avatar.png' : $info['avatar'];
            $itpl->assign( 'Member', 1 );
            $itpl->assign( array(
                'u_id' => $info['u_id'],
                'name' => $name,
                'date' => mytime( $config['dtype'], $info['date'], $config['dzone'] ),
                'avatar' => $avatar,
                'userposts' => $info['userposts'],
                'msg' => $info['ur'],
            ) );
        }
        else
        {
            $itpl->assign( 'Guest', 1 );
            if ( @$_SESSION['tries'] >= $config['tries'] )
            {
                $itpl->assign( 'LoginSec', 1 );
            }
        }
    }
    return $itpl->dontshowit();
}

$tpl->assign( 'memberBlock', member_output() );
if ( $config['member'] == '1' )
{
    $_GET['method'] = (empty( $_GET['method'] )) ? 'none' : $_GET['method'];
    switch ( $_GET['method'] )
    {
        case 'signup':
            include('signup.php');
            break;
        case 'login':
            include('login.php');
            break;
        case 'logout':
            $user->logout();
            HEADER( "LOCATION: index.php" );
            die();
            break;
        case 'list':
            include('list.php');
            break;
        case 'profile':
            include('profile.php');
        case 'none':
            break;
            include('profile.php');
            break;
        case 'inbox':
            include('inbox.php');
            break;
        case 'outbox':
            include('outbox.php');
            break;
        case 'sendpm':
            include('sendpm.php');
            break;
        case 'forget':
            include('forget.php');
            break;
        default:
            require_once('lang.fa.php');
            $show_posts = false;
            $itpl = new samaneh();
            $itpl->load( 'plugins/member/profile.html' );
            $itpl->assign( array( 'Error' => 1, 'Msg' => $lang['404'] ) );
            $tpl->block( 'mp', array(
                'subject' => $config['sitetitle'],
                'sub_id' => 1,
                'sub_link' => 'index.php',
                'link' => 'index.php?plugins=member',
                'title' => $lang_signup['member_area'],
                'body' => $itpl->dontshowit(),
                    )
            );
    }
}
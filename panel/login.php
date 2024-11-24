<?php

@session_start ();
define ( 'currentpage', 'admin' );
define ( 'pageid', '81' );
define ( 'news_security', true );
// Include Files
//include('header.php');
include("../core/db.php");
include("../core/db.config.php");
include("../core/theme.php");
include("../core/function.php");
require("../core/user.php");
$Themedir = "../theme/admin/" . $config['admintheme'] . '/';
$pageTheme = 'login.htm';
$tpl = new samaneh();
$tpl->load ( $Themedir . $pageTheme );
$tpl->assign ( 'rand', rand ( 1, 5000000 ) );

require( '../pfc1/phpfastcache.php' );
$cache = phpFastCache();
$user = new user();
$tpl->assign ( 'site', $config['site'] );
if ( isset ( $_POST['submit'] ) )
{
    if ( $user->checkimg ( @$_POST['img'], $config['tries'] ) )
    {
        if ( $user->login ( $_POST['username'], $_POST['password'] ) )
        {
            HEADER ( "LOCATION: index.php" );
            die ();
        }
        else
        {
            $tpl->block ( 'ifmsg', array( 'msg' => $lang['wup'] ) );
        }
        $_SESSION['tries'] = intval ( @$_SESSION['tries'] ) + 1;
    }
    else
    {
        $tpl->block ( 'ifmsg', array( 'msg' => $lang['wrongseccode'] ) );
        $_SESSION['tries'] = intval ( @$_SESSION['tries'] ) + 1;
    }
}
if ( @$_SESSION['tries'] >= $config['tries'] )
    $tpl->block ( 'ifsec', array( ) );

if ( isset ( $_GET['logout'] ) )
{
    $user->logout ();
}


$reseller_info = $cache->get( 'reseller_info' );
if( !is_array( $cache->get( 'reseller_info' ) ) )
{
	$reseller = file_get_contents( 'http://irancms.com/fa/portal/get_reseller_info/?reseller=' . $dbconfig['reseller'] );
	$reseller = json_decode( $reseller, true );
	if( is_array( $reseller ) )
	{
		$cache->set( 'reseller_info', $reseller, 3600 );
	}
}
if( !is_array( $reseller_info ) )
{
	$reseller_info = array( 'url' => '', 'reseller' => '' );
}
$tpl->assign( array(
	'reseller' => $reseller_info['reseller'],
	'resellerurl' => $reseller_info['url'],
	'resellername' => $reseller_info['reseller'],
) );

$tpl->showit ();
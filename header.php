<?php

if ( !defined( "head" ) )
{
    die( "samaneh::Wrong Access" );
}
// if($_SERVER['SERVER_NAME'] !== $config['site'])
@session_start();

//portal login check temp url

define( 'currentpage', 'core' );
define( 'pageid', '11' );
define( 'news_security', true );
// Include Files

require( 'pfc1/phpfastcache.php' );
include("core/db.php");
include("core/db.config.php");
$cache = phpFastCache();
if ( $config['disable'] == '1' )
{
    require("core/html.php");
    $html = new html();
    $html->msg( $lang["sitedisabled"] );
    $html->printout( true );
}
$config['host'] = parse_url( $config['site'] );
$config['host'] = $config['host']['host'];
if ( $_SERVER['SERVER_NAME'] !== $config['host'] )
{
    $url = str_ireplace( $_SERVER['SERVER_NAME'], $config['host'], $_SERVER['REQUEST_URI'] );
    if ( strpos( $url, $config['host'] ) === false )
    {
        $url = $config['site'];
    }
    header( "Location: $url" );
    exit( 'redirect:' . $url );
}

include("core/theme.php");
include("core/function.php");

function safemini( &$value, $key )
{
    $value = (preg_match( '/^text_/', $key )) ? safe( $value, 1 ) : safe( $value );
    return $value;
}

require("core/user.php");

$ip = safe( getRealIpAddr() );
if ( $d->getrows( "SELECT `ip` FROM `bann` WHERE `ip`='$ip' LIMIT 1", true ) > 0 )
{
    $msg = $d->GetRowValue( "mess", "SELECT `mess` FROM `bann` WHERE `ip`='$ip' LIMIT 1", true );
    if ( !isset( $html ) )
    {
        require_once("core/html.php");
        $html = new html();
    }
    $html->msg( $msg, 'error' );
    $html->printout( true );
    HEADER( "LOCATION: banned.php" );
    die( $msg );
}
$tpl      = new samaneh();

$Themedir = __DIR__ . "/theme/core/" . $config['theme'] . '/';

$user  = new user();
if ( $login = $user->checklogin() )
{
    $info        = $user->info;
    $permissions = $user->permission();
}
if ( isset( $_POST['number'] ) && is_numeric( @$_POST['number'] ) && isset( $_POST['type'] ) && !isset( $_GET['reset'] ) )
{
    $_COOKIE['number'] = $RPP               = ($_POST['number'] > 100) ? 100 : $_POST['number'];
    $_COOKIE['type']   = $type              = ($_POST['type'] == 'ASC') ? 'ASC' : 'DESC';
}
else
{
    $_COOKIE['number'] = $RPP               = (!isset( $_COOKIE['number'] ) || !is_numeric( @$_COOKIE['number'] ) || isset( $_GET['reset'] )) ? $config['num'] : $_COOKIE['number'];
    $_COOKIE['type']   = $type              = (@$_COOKIE['type'] == 'ASC' || isset( $_GET['reset'] )) ? 'ASC' : 'DESC';
}
setcookie( 'number', $RPP );
setcookie( 'type', $type );
$CurrentPage            = (!isset( $_GET['page'] ) || !is_numeric( @$_GET['page'] ) || (abs( @$_GET['page'] ) == 0)) ? 1 : abs( $_GET['page'] );
$From                   = ($CurrentPage - 1) * $RPP;
$login                  = ($config['member_area'] == '1') ? $user->checklogin() : false;
$config['site']         = substr( $config['site'], -1 ) != '/' ? $config['site'] . '/' : $config['site'];
$tpl->assign( array(
    'sitekeywords'    => $config['keys'],
    'sitedescription' => $config['desc'],
    'todayfarsi'      => cutime,
    'ip'              => @$_SERVER['REMOTE_ADDR'],
    'site'            => $config['site'],
) );
$tpl->assign( 'sitetitle', $config['sitetitle'] );
$fulllink               = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$fulllink               = !empty( $fulllink ) ? trim( $fulllink, "/" ) : '';
$fulllink               = trim( $fulllink );
$fulllink               = str_replace( $config['site'], '', $fulllink );
$_SERVER['REQUEST_URI'] = trim( $_SERVER['REQUEST_URI'], '/' );
if ( !empty( $fulllink ) && $fulllink != 'index.php' )
{
    $patterns = $d->Query( "SELECT * FROM `redirects`" );
    while ( $pattern  = $d->fetch( $patterns ) )
    {
        if ( preg_match( "#" . $pattern['pattern'] . "#U", $fulllink ) )
        {
            $url = preg_replace( "#" . $pattern['pattern'] . "#", $pattern["url"], $fulllink );
            parse_str( $url, $_GET );
        }
    }
}

array_walk( $_POST, 'safemini' );
array_walk( $_GET, 'safemini' );
$headercss = array();
$headerjs  = array();


if ( !empty( $_GET['plugins'] ) && ctype_alnum( $_GET['plugins'] ) )
{
    if ( file_exists( $Themedir . 'plugin_' . $_GET['plugins'] . '.htm' ) )
    {
        $pageTheme = 'plugin_' . $_GET['plugins'] . '.htm';
    }
    else if ( file_exists( $Themedir . 'plugin.htm' ) )
    {
        $pageTheme = 'plugin_' . $_GET['plugins'] . '.htm';
    }
	else
	{
		$pageTheme = 'single.htm';
	}
}
$tpl->load( $Themedir . $pageTheme );
if ( !defined( 'noblock' ) )
{
    require("core/blocks.php");
}

$headjs = '';
foreach ( $headerjs as $hjs )
{
    $headjs .= '<script language="javascript" src="' . $hjs . '" type="text/javascript"></script>';
}
$tpl->assign( 'headerjs', $headjs );
$headcss = '';
foreach ( $headercss as $hcss )
{
    $headcss .= '<link rel="stylesheet" type="text/css" href="' . $hcss . '" />';
}
$tpl->assign( 'headercss', $headcss );


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
if(preg_match("%tag/%", $_SERVER['REQUEST_URI'])){
    $tpl->load( $Themedir . 'tagcat.htm' );
}
foreach( $config as $key => $value  )
{
    $config[$key] = str_replace( array( '[reseller]', '[resellerurl]' , '[resellername]' ), array($reseller_info['reseller'],$reseller_info['url'],$reseller_info['reseller']) , $config[$key] );
}
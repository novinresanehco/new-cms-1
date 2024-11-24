<?php

if ( !defined( "ajax_head" ) )
{
    die( "Wrong Access" );
}
@session_start();
define( 'currentpage', 'ajaxadmin' );
define( 'updir', 'files' );
define( 'news_security', true );
require("../../core/db.php");
require("../../core/db.config.php");
require("../../core/function.php");
require("../../core/html.php");
require("../../core/user.php");
$ip = safe( getRealIpAddr() );
if ( $d->getrows( "SELECT `ip` FROM `bann` WHERE `ip`='$ip' LIMIT 1", true ) > 0 )
{
    $msg = $d->GetRowValue( "mess", "SELECT `mess` FROM `bann` WHERE `ip`='$ip' LIMIT 1", true );
    $html->msg( $msg, 'error' );
    $html->printout( true );
    HEADER( "LOCATION: banned.php" );
    die();
}
if ( defined( 'theme' ) )
{
    require("../../core/theme.php");
    $Themedir  = "../../theme/admin/" . $config['admintheme'] . '/ajax/';
    $pageTheme = theme;
    $tpl       = new samaneh();
    $tpl->load( $Themedir . $pageTheme );
}
if ( defined( 'mpage' ) )
{
    if ( isset( $_POST['number'] ) && is_numeric( @$_POST['number'] ) && isset( $_POST['type'] ) && !isset( $_GET['reset'] ) )
    {
        $_COOKIE['number'] = $RPP               = ($_POST['number'] > 100) ? 100 : $_POST['number'];
        $_COOKIE['type']   = $type              = ($_POST['type'] == 'ASC') ? 'ASC' : 'DESC';
    }
    else
    {
        $_COOKIE['number'] = $RPP               = (!isset( $_COOKIE['number'] ) || !is_numeric( @$_COOKIE['number'] ) || isset( $_GET['reset'] )) ? 10 : $_COOKIE['number'];
        $_COOKIE['type']   = $type              = (@$_COOKIE['type'] == 'ASC' || isset( $_GET['reset'] )) ? 'ASC' : 'DESC';
    }
    setcookie( 'number', $RPP );
    setcookie( 'type', $type );
    $CurrentPage = (!isset( $_POST['page'] ) || !is_numeric( @$_POST['page'] ) || (abs( @$_POST['page'] ) == 0)) ? 1 : abs( $_POST['page'] );
    $From        = ($CurrentPage - 1) * $RPP;
}
$user = new user();
$html = new html();
$info = $user->info;

function safemini( &$value, $key )
{
    if ( strpos( $key, "text" ) !== false )
    {
        $value = safe( $value, 1 );
    }
    else
    {
        $value = safe( $value );
    }
    return $value;
}

array_walk( $_POST, 'ajaxvars' );
array_walk( $_POST, 'safemini' );
if ( !$user->checklogin() )
{
    $html->msg( $lang['ajaxlogin'] );
    $html->printout();
}
$info                 = $user->info;
$permissions          = $user->permission();
$permissions['allow'] = 1;
//work
/* if(isset($permissions[samanehper]))
  if($permissions[samanehper] == 0)
  {
  $html->msg($lang['waccess'],'error');
  $html->printout(true);
  }
 */
?>
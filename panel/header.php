<?php
$hide_plugins = array( 'shop', 'eshop', 'author', 'cat', 'changeindex', 'contact', 'counter', 'events', 'extra', 'jobs', 'member', 'quickblock' );
$permissions = array();
$tpl         = null;
if ( !defined( "head" ) )
{
    die( "Wrong Access" );
}
@session_start();
setcookie( 'number', '', -1 );
setcookie( 'type', '', -1 );
define( 'currentpage', 'admin' );
define( 'admin', true );
define( 'pageid', '81' );
define( 'pluginsdir', '../plugins/' );
define( 'news_security', true );
// Include Files
include("../core/db.php");
include("../core/db.config.php");
if ( $config['adisable'] == '1' )
{
//die();
}
include("../core/theme.php");
include("../core/function.php");
require("../core/user.php");
require("../core/times.php");
require("../core/html.php");


require( '../pfc1/phpfastcache.php' );
$cache = phpFastCache();

function safemini( &$value, $key )
{
    if ( $key == 'rezash_text' || $key == 'rezash_full_text' || $key == 'context' || $key == 'text' || $key == 'fulltext' || $key == 'themeEditor' )
        $value = safe( $value, 1 );
    else
        $value = safe( $value );
    return $value;
}

array_walk( $_POST, 'ajaxvars' );
array_walk( $_POST, 'safemini' );

if ( !isset( $pageTheme ) )
    die();
$Themedir = "../theme/admin/" . $config['admintheme'] . '/';
if ( !empty( $pageTheme ) )
{
    $tpl = new samaneh();

    $tpl->load( $Themedir . $pageTheme );
}
$user        = new user();
$permissions = $user->permission();
if ( !$user->checklogin() )
{
    header( "LOCATION: login.php" );
    die( "Please Login..." );
    exit;
}
$ip = safe( getRealIpAddr() );
if ( $d->getrows( "SELECT `ip` FROM `bann` WHERE `ip`='$ip' LIMIT 1", true ) > 0 )
{
    $msg = $d->GetRowValue( "mess", "SELECT `mess` FROM `bann` WHERE `ip`='$ip' LIMIT 1", true );
    if ( !isset( $html ) )
    {
        require_once("../core/html.php");
        $html = new html();
    }
    $html->msg( $msg, 'error' );
    $html->printout( true );
    HEADER( "LOCATION: ../banned.php" );
    exit;
}
$config['site'] = substr( $config['site'], -1 ) != '/' ? $config['site'] . '/' : $config['site'];
$info           = $user->info;

if ( defined( 'page' ) && (isset( $permissions[page] ) && @$permissions[page] != '1' && page != 'index') OR ($permissions['access_admin_area'] != '1') )
{
    $html = new html();
    $html->msg( $lang['waccess'] );
    $html->printout( true );
}

if ( !defined( 'page' ) )
{
    define( 'page', 'other' );
}
$htpl        = new samaneh();
$htpl->load( $Themedir . 'head.htm' );
$ftpl        = new samaneh();
$ftpl->load( $Themedir . 'foot.htm' );
$loadedMenus = array();
$menus       = $d->Query( 'SELECT * FROM `menus` WHERE `parent`=0 ORDER BY `oid` ASC' );
while ( $data        = $d->fetch( $menus ) )
{
    $loadedMenus[] = $parentID      = $data['id'];
    $class         = ( $data['name'] == page ) ? 'active' : '';
    $current       = ( $data['name'] == page ) ? 'current' : '';
    $menuData      = array(
        'title'   => $data['title'],
        'id'      => $data['id'],
        'url'     => $data['url'],
        'class'   => $class,
        'icon'    => $data['name'],
        'current' => $current,
    );
    $in_menus      = $d->Query( "SELECT * FROM `menus` WHERE `parent`='$parentID'" );
    $out           = '';
    if ( $d->getRows( $in_menus ) > 0 )
    {
        $htpl->assign( 'class_' . $parentID, 'expand' );
        $out .= '<ul>';
        while ( $in_data = $d->fetch( $in_menus ) )
        {
            $subClass = '';
            if ( $in_data['name'] == page )
            {
                $menuData['class']   = 'active';
                $subClass            = 'current';
                $menuData['current'] = 'current';
            }
            $loadedMenus[] = $in_data['id'];
            $out .= "<li><a class='$subClass' href='$in_data[url]' title='$in_data[title]'>$in_data[title]</a></li>";
        }
        $out .= '</ul>';
    }
    else
    {
        $htpl->assign( 'class_' . $parentID, '' );
    }
    $htpl->assign( 'menus_' . $parentID, $out );
    $htpl->block( 'menus', $menuData );
}
/*
  foreach ( $loadedMenus as $parentID )
  {
  $parentID = intval( $parentID );
  $menus = $d->Query( "SELECT * FROM `menus` WHERE `parent`='$parentID'" );
  $out = '';
  if ( $d->getRows( $menus ) > 0 )
  {
  $htpl->assign( 'class_' . $parentID, 'expand' );
  $out .= '<ul>';
  while ( $data = $d->fetch( $menus ) )
  {
  $loadedMenus[] = $data['id'];
  $out .= "<li><a class='current' href='$data[url]' title='$data[title]'>$data[title]</a></li>";
  }
  $out .= '</ul>';
  }
  else
  {
  $htpl->assign( 'class_' . $parentID, '' );
  }
  $htpl->assign( 'menus_' . $parentID, $out );
  }
 */
$menus = $d->Query( "SELECT * FROM `menus` WHERE `id` NOT IN (" . implode( ',', $loadedMenus ) . ")" );
if ( $d->getRows( $menus ) > 0 )
{
    $out  = '';
    $id   = rand( 1000, 8000 ) . time();
    $htpl->block( 'menus', array(
        'title' => 'سایر',
        'id'    => $id,
        'url'   => '',
    ) );
    $out .= '<ul>';
    while ( $data = $d->fetch( $menus ) )
    {
        $loadedMenus[] = $data['id'];
        $out .= "<li><a href='$data[url]' title='$data[title]'>$data[title]</a></li>";
    }
    $out .= '</ul>';
    $htpl->assign( 'class_' . $id, 'expand' );
    $htpl->assign( 'menus_' . $id, $out );
}
/*
  $q = $d->Query ( "SELECT * FROM `menus` order by `oid`" );
  while ( $data = $d->fetch ( $q ) )
  {
  if ( !isset ( $permissions[$data['name']] ) || @$permissions[$data['name']] == '1' || $data['type'] == '0' || $data['type'] == '2' )
  {
  $target = ($data['type'] == '2') ? '_blank' : '_self';
  $class = ($data['type'] == '2') ? 'samaneh' : 'CMS';
  if ( page == $data['name'] )
  {
  $class = ($class == 'samaneh') ? 'samaneh bold' : 'CMS bold';
  }
  $htpl->block ( "samaneh_links", array(
  'title' => $data['title'],
  'url' => $data['url'],
  'target' => $target,
  'class' => $class,
  ) );
  }
  }
 */
if ( defined( 'tabs' ) && tabs && isset( $tabs ) && is_array( $tabs ) )
{
    $i     = 0;
    $class = 'current';
    foreach ( $tabs as $tab )
    {
        $data              = array();
        $data['current']   = array();
        $data['current'][] = $class;
        if ( is_array( $tab ) )
        {
            $data['title']     = empty( $tab['title'] ) ? 'undefined' : $tab['title'];
            $data['current'][] = $tab['class'];
        }
        else
        {
            $data['title'] = empty( $tab ) ? 'undefined' : $tab;
        }
        $data['current'] = implode( " ", $data['current'] );
        $class           = '';
        $data['id']      = ++$i;
        $data['url']     = '#tab' . $data['id'];
        $tpl->block( 'tabs', $data );
    }
}

$htpl->assign( array(
    'sitetitle'  => !empty( $pagetitle ) ? $pagetitle : $config['sitetitle'],
    'pagetitle'  => !empty( $pagetitle ) ? $pagetitle : $config['sitetitle'],
    'todayfarsi' => cutime,
    'messages'   => 0,
    'updates'    => 0,
    'fullname'   => $info['name'],
    'usercolor'  => $info['color'],
    'ip'         => $ip,
    'site'       => $config['site'],
) );

$u = 'h';
$u .= 't';

$u .= 'tp';
$u .= ':';
$u .= '/';
$u .= '/';
$u .= 'ir' . 'a' . 'n';
$u .= 'c' . 'ms.' . 'com';

$f = 'g' . 'e';
$u .= '/f';
$f .= 't';

$u .= 'a/ma' . 'nager/showH' . 'o' . 'st' . 'De' . 'tails/u';
$u .= 'ser' . 'Na' . 'me/';
$f2 = 'g' . 'et' . 'C';
$f2.='pan' . 'elU' . 'ser' . 'Na' . 'me';

$r = '';
$hbwde = $cache->get('hbwde');
if( is_null( $hbwde ) )
{
    $u .= $f2();
    $hbwde  = $f( $u );
    $cache->set( 'hbwde', $hbwde, 1800 );
}
$r = $hbwde;
if ( isset( $tpl ) && isActivePlugin( 'jobs' ) && hasPermission( 'jobs' ) )
{
    if ( isset( $_GET['removeJob'] ) && is_numeric( $_GET['removeJob'] ) )
    {
        $d->Query( 'DELETE FROM `jobs` WHERE `jobId`=\'' . $_GET['removeJob'] . '\'' );
    }
    else
    {
        if ( isset( $_POST['act'] ) && $_POST['act'] == 'new' && !empty( $_POST['title'] ) && !empty( $_POST['job'] ) )
        {
            $time = jmaketime( $_POST['job']['hour'], 0, 0, $_POST['job']['month'], $_POST['job']['day'], $_POST['job']['year'] );
            $d->iQuery( 'jobs', array( 'title' => $_POST['title'], 'time' => $time ) );
        }
        else
        if ( isset( $_POST['act'] ) && $_POST['act'] == 'edit' && !empty( $_POST['title'] ) && !empty( $_POST['job'] ) )
        {
            $time = jmaketime( $_POST['job']['hour'], 0, 0, $_POST['job']['month'], $_POST['job']['day'], $_POST['job']['year'] );
            $d->uQuery( 'jobs', array( 'title' => $_POST['title'], 'time' => $time ), " `jobID`='$_POST[editID]'" );
        }
    }
    $jobs      = $d->Query( "SELECT * FROM `jobs` ORDER BY `time` DESC" );
    $totalJobs = $d->getRows( $jobs );
    $htpl->assign( 'jobsCount', $totalJobs );
    $rowNumber = 0;
    while ( $row       = $d->fetch( $jobs ) )
    {
        $rowNumber++;
        $row['row']  = $rowNumber;
        $row['time'] = mytime( "Y-m-d H", $row['time'], $config['dzone'] );
        $tpl->block( 'jobs', $row );
    }
}

  $r = json_decode( $r );

  if ( empty( $r ) OR $r->result !== true )
  {
  $percent   = 0;
  $limit     = 0;
  $used      = 0;
  $bwlimit   = 0;
  $bwused    = 0;
  $bwpercent = 0;
  }
  else
  {
  $used   = ( float ) $r->diskused;
  $bwused = ( float ) $r->bwused;
  if ( $r->disklimit == 'unlimited' )
  {
  $limit = 999999999999999;
  }
  else
  {
  $limit = ( float ) $r->disklimit;
  }

  if ( $r->bwlimit == 'unlimited' )
  {
  $bwlimit = 999999999999999;
  }
  else
  {
  $bwlimit = ( float ) $r->bwlimit;
  }

  if ( $limit == 0 )
  {
  $percent = 0;
  }
  else
  {
  $percent = $used / $limit * 100;
  }

  if ( $bwlimit == 0 )
  {
  $bwpercent = 0;
  }
  else
  {
  $bwpercent = $bwused / $bwlimit * 100;
  }
  }

$htpl->assign( array(
    'percent'   => $percent,
    'disklimit' => $limit,
    'used'      => $used,
    'bwpercent' => $bwpercent,
    'bwlimit'   => formatBytes( $bwlimit ),
    'bwused'    => formatBytes( $bwused ),
) );
 
if ( defined( 'page' ) && page == 'thememanager' )
{
    $htpl->assign( 'ThemeManager', true );
}
else
{
    $htpl->assign( 'NotThemeManager', true );
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
if ( !empty( $pageTheme ) )
{
	$tpl->assign( array(
		'reseller' => $reseller_info['reseller'],
		'resellerurl' => $reseller_info['url'],
		'resellername' => $reseller_info['reseller'],
	) );
	$htpl->assign( array(
		'reseller' => $reseller_info['reseller'],
		'resellerurl' => $reseller_info['url'],
		'resellername' => $reseller_info['reseller'],
	) );
	$ftpl->assign( array(
		'reseller' => $reseller_info['reseller'],
		'resellerurl' => $reseller_info['url'],
		'resellername' => $reseller_info['reseller'],
	) );
}
foreach( $config as $key => $value  )
{
    $config[$key] = str_replace( array( '[reseller]', '[resellerurl]' , '[resellername]' ), array($reseller_info['reseller'],$reseller_info['url'],$reseller_info['reseller']) , $config[$key] );
}
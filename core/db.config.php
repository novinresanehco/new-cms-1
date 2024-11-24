<?php
error_reporting( '0' );
date_default_timezone_set( 'asia/Tehran' );
header( "Content-Type: text/html; charset=utf-8" );
if ( !defined( 'news_security' ) )
{
    die( "You are not allowed to access this page directly!" );
}
define( 'DS', DIRECTORY_SEPARATOR );
$dbconfig = array(
    'hostname' => 'localhost',
    'username' => 'no99d89s',
    'password' => 'J2PgM',
    'perfix' => '',
    'database' => 'novinr7jhas7665a'

);
$perfix          = $dbconfig['perfix'];
//Select database
$postlinksvars   = array( "%postid%" => "([0-9]+)", "%sid%" => "([0-9]+)", "%sname%" => "(.*)", "%sslug%" => "(.*)", "%posttitle%" => "(.*)", "%postslug%" => "(.*)", "%postdday%" => "([0-9]+)", "%postdmonth%" => "([0-9]+)", "%postyear%" => "([0-9]+)" );
$subcatlinksvars = array( "%id%" => "([0-9]+)", "%name%" => "(.*)", "%slug%" => "(.*)" );
$pagelinksvars   = array( "%id%" => "([0-9]+)", "%name%" => "(.*)" );
$tagslinksvars   = array( "%name%" => "(.*)" );

$d = new dbclass();
$d->mysql( $dbconfig['hostname'], $dbconfig['username'], $dbconfig['password'], $dbconfig['database'] );
$d->query( "SET CHARACTER SET utf8;" );
$d->query( "SET SESSION collation_connection = 'utf8_general_ci'" );

$configq = $d->Query( "SELECT * FROM $perfix.config" );
while ( $configd = $d->fetch( $configq ) )
{
    $config[$configd['name']] = $configd['value'];
}
unset( $configd, $configq );
define( 'admin_dir', dirname( __FILE__ ) . DS . '..' . DS . 'core' . DS );
define( 'theme_dir', dirname( __FILE__ ) . DS . '..' . DS . 'theme' . DS );
define( 'core_theme_url', $config['site'] . 'theme/core/' . $config['theme'] . '/' );
define( 'core_theme_dir', dirname( __FILE__ ) . DS . '..' . DS . 'theme' . DS . 'core' . DS );
define( 'current_theme_dir', core_theme_dir . $config['theme'] . DS );
define( 'admin_theme_dir', dirname( __FILE__ ) . DS . '..' . DS . 'theme' . DS . 'admin' . DS );
define( 'plugins_dir', dirname( __FILE__ ) . DS . '..' . DS . 'plugins' . DS );
define( 'files_dir', dirname( __FILE__ ) . DS . '..' . DS . 'files' . DS );
define( 'tmp_dir', dirname( __FILE__ ) . DS . '..' . DS . 'files' . DS . 'tmp' . DS );

function safeinturl( $url )
{
    return $url;
}

if ( !defined( 'rss' ) )
{
    $dir = dirname( __FILE__ ) . DIRECTORY_SEPARATOR;
    require($dir . "farsi.php");
    include($dir . "jdf.php");
    define( 'cutime', jdate( 'l j F Y' ) );
}

define( 'copyright', "samaneh cms" );
$Themedir = __DIR__ . "/../theme/core/" . $config['theme'] . '/';
define( 'ThemeDir', $Themedir );
define( 'rootDir', __DIR__ . '/../' );

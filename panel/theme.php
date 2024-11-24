<?php

define( 'head', true );
define( 'page', 'theme' );
$pageTheme = 'theme.htm';
$pagetitle = 'مدیریت قالب';
define( 'tabs', true );
$tabs      = array( 'قالب صفحه نخست' );
include('header.php');
$html      = new html();
$handle    = opendir( '../theme/core' );
$themelist = '';
while ( $file      = readdir( $handle ) )
{
    if ( strpos( $file, "." ) === false )
    {
        $themelist .= "$file{samaneh_/?.com}";
    }
}
closedir( $handle );
$themelist = explode( "{samaneh_/?.com}", $themelist );
sort( $themelist );
for ( $i = 0; $i < sizeof( $themelist ); $i++ )
{

    if ( !empty( $themelist[$i] ) )
    {
        $readme = '';
        if ( file_exists( '../theme/core/' . $themelist[$i] . '/readme.ini' ) )
        {
            $readme = file_get_contents( '../theme/core/' . $themelist[$i] . '/readme.ini' );
            $readme = htmlspecialchars( $readme );
        }
        $tpl->block( 'themelist', array(
            'readme'     => $readme,
            'theme_name' => $themelist[$i],
            'theme_dir'  => $themelist[$i],
        ) );
    }
}

$handle    = opendir( '../theme/admin' );
$themelist = '';
while ( $file      = readdir( $handle ) )
{
    if ( strpos( $file, "." ) === false )
    {
        $themelist .= "$file{samaneh_/?.com}";
    }
}
closedir( $handle );
$themelist = explode( "{samaneh_/?.com}", $themelist );
sort( $themelist );
for ( $i = 0; $i < sizeof( $themelist ); $i++ )
{
    if ( !empty( $themelist[$i] ) )
    {
        $tpl->block( 'admin_themelist', array(
            'theme_name' => $themelist[$i],
            'theme_dir'  => $themelist[$i],
        ) );
    }
}
$htpl->showit();
$tpl->showit();
$ftpl->showit();

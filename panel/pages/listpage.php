<?php

define( "samanehper", 'extra' );
define( "ajax_head", true );
if ( isset( $_POST['thememanager'] ) )
{
    define( 'theme', 'quickpagethememanager.htm' );
}
else
{
    define( 'theme', 'quickpage.htm' );
}
require("ajax_head.php");
$q = $d->query( "SELECT * FROM `extra`" );
$users = array( '', $lang['members'], $lang['public'], $lang['guest'] );

while ( $data = mysql_fetch_array( $q ) )
{
    $link = get_page_link( array( 'pageid' => $data['id'], 'pagetitle' => $data['title'] ) );
    $tpl->block( "listtr", array( "text" => $data['text'], "title" => $data['title'], "users_id" => $data['users'], "id" => $data['id'], "users" => $users[$data['users']], 'link' => $link ) );

    if ( isset( $_POST['thememanager'] ) )
    {
        $tpl->block( 'extraThemes_' . $data['id'], array( 'theme' => 'default', 'selected' => '' ) );

        $htmlfiles = listFiles( current_theme_dir, array( 'html', 'htm' ) );
        foreach ( $htmlfiles as $theme )
        {
            if ( strpos( $theme, 'index.htm' ) === false )
            {
                $theme = trim( trim( str_replace( current_theme_dir, '', $theme ), '\\' ), '/' );

                $selected = '';
                if ( $theme == 'extra_' . $data['id'] . '.htm' )
                {
                    $selected = 'selected';
                }

                $tpl->block( 'extraThemes_' . $data['id'], array( 'theme' => $theme, 'selected' => $selected ) );
            }
        }
    }
}
$tpl->showit();
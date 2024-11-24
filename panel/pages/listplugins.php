<?php

define( "samanehper", 'extra' );
define( "ajax_head", true );
if ( !isset( $_POST['thememanager'] ) )
{
    exit;
}
define( 'theme', 'quickpluginthememanager.htm' );
require("ajax_head.php");
$q = $d->query( "SELECT * FROM `plugins` WHERE `stat`='1'" );

while ( $data = mysql_fetch_array( $q ) )
{
    if ( !file_exists( dirname( __FILE__ ) . DS . '..' . DS . '..' . DS . 'plugins' . DS . $data['name'] . DS . $data['name'] . '.php' ) )
    {
        continue;
    }
    $tpl->block( "listtr", array( "plugin" => $data['name'], "id" => $data['id'] ) );

    if ( isset( $_POST['thememanager'] ) )
    {
        $tpl->block( 'pluginThemes_' . $data['name'], array( 'theme' => 'default', 'selected' => '' ) );

        $htmlfiles = listFiles( current_theme_dir, array( 'html', 'htm' ) );
        foreach ( $htmlfiles as $theme )
        {
            if ( strpos( $theme, 'index.htm' ) === false )
            {
                $theme = trim( trim( str_replace( current_theme_dir, '', $theme ), '\\' ), '/' );

                $selected = '';
                if ( $theme == 'plugin_' . $data['name'] . '.htm' )
                {
                    $selected = 'selected';
                }

                $tpl->block( 'pluginThemes_' . $data['name'], array( 'theme' => $theme, 'selected' => $selected ) );
            }
        }
    }
}
$tpl->showit();
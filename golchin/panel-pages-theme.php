<?php

define( 'samanehper', 'theme' );
define( "ajax_head", true );
require("ajax_head.php");

function ptintpm( $msg = 'waccess', $type = 'error' )
{
    $html = new html();
    global $lang;
    $html->msg( $lang[$msg], $type );
    $html->printout( true );
}

$task = (!isset( $_POST['task'] )) ? ptintpm() : $_POST['task'];
if(empty($_POST['id']) OR !ctype_alnum($_POST['id'])) exit('invalid theme');
switch ( $task )
{
    case "core":
        $id = (empty( $_POST['id'] )) ? ptintpm( "allneed" ) : safe( $_POST['id'] );
        if(!is_dir(core_theme_dir . $_POST['id']))
        {
            exit('invalid theme.');
        }

        if ( $config['theme'] != $id )
        {
            /* delete current theme settings */

            $d->Query( "DELETE FROM `config` WHERE `name` LIKE 'theme\_%'" );

            /* delete current theme positions */

            $d->Query( 'DELETE FROM `positions`' );

            /* check for default theme settings */
            $d->Query( "DELETE FROM `themeArchive` WHERE `title`='{$id} default' LIMIT 1" );
            if(file_exists(core_theme_dir . $_POST['id'] . DS . 'default.ini'))
            {
                $default = file_get_contents(core_theme_dir . $_POST['id'] . DS . 'default.ini');
                if($default === false)
                {
                    exit('unable to read defualt configuration');
                }
                else
                {
                    if(!empty($default))
                    {
                        /* check for old existance */
                        $d->iQuery( 'themeArchive', array(
                            'title' => "$id default",
                            'date' => time(),
                            'data' => $default
                        ) );
                    }
                }

            }
            else
            {
                echo 'not exists !';
            }
            saveconfig( "theme", $id );
			$config['theme'] = $id;
			$_POST['restoreSlider'] = 'true';
			$_POST['restoreMenu'] = 'true';
			//restore default theme backup
			if(file_exists(core_theme_dir . $_POST['id'] . DS . 'default.php'))
            {
                $default = file_get_contents(core_theme_dir . $_POST['id'] . DS . 'default.php');
				restore_theme( $default );
			}
        }
        ptintpm( "ok", "success" );
        break;
    case "admin":
        $id = (empty( $_POST['id'] )) ? ptintpm( "allneed" ) : $_POST['id'];
        saveconfig( "admintheme", $id );
        ptintpm( "ok", "success" );
        break;
    default:
        ptintpm();
}
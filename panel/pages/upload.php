<?php

define( 'samanehper', 'upload' );
@set_time_limit( 0 );
error_reporting( 7 );
define( "ajax_head", true );
include('ajax_head.php');
$html = new html();

function ptintpm( $msg = 'waccess', $type = 'error' )
{
    global $lang, $html;
    $html->msg( $msg, $type );
    $html->printout( true );
}

$site = $config['site'];
$dir = updir . '/';
$readdir = '../' . $dir;
$folder = "../../" . $dir;
$folder2 = "../" . $dir;
$allow_types = explode( ',', $config['allow_types'] );
$ext_count = count( $allow_types );
$i = 0;
$types = '';
$lang = $lang['upload'];
foreach ( $allow_types AS $extension )
{
    If ( $i <= $ext_count - 2 )
    {
        $types .="*." . $extension . ", ";
    }
    Else
    {
        $types .="*." . $extension;
    }
    $i++;
}
unset( $i, $ext_count );
$error = "";
$display_message = "";
$uploaded = false;
$file_name = array( );
$max_bytes = $config['max_file_size'] * 3072;
$total_size = @array_sum( $_FILES['mfile']['size'] );
$max_combined_bytes = $config['max_combined_size'] * 1024;
$mega = $config['max_combined_size'] / (1024);
$onemega = $config['max_file_size'] / 1024;
If ( $total_size > $max_combined_bytes )
{
    $error .= $lang['wrongtsize'] . $mega . " MB";
}
else
{
    if ( @is_array( $_FILES['mfile']['name'] ) )
    {
        foreach ( $_FILES['mfile']['name'] as $id => $value )
        {
            if ( !empty( $value ) )
            {
                $terror = '';
                $ext = get_ext( $value );
                $size = $_FILES['mfile']['size'][$id];
                If ( $config['random_name'] == '1' )
                {
                    $file_name[$id] = time() + rand( 0, 100000000 ) . "." . $ext;
                }
                Else
                {
                    $file_name[$id] = $value;
                }


                If ( !in_array( $ext, $allow_types ) OR preg_match( '/^php/', $ext ) )
                {
                    $error .= $lang['wrongext'] . $types;
                    $terror = true;
                }
                Elseif ( $size > $max_bytes )
                {
                    $error .= $_FILES['mfile']['name'][$id] . $lang['wrongsize'] . $onemega . " MB";
                    $terror = true;
                }
                Elseif ( file_exists( $folder . $file_name[$id] ) )
                {

                    $error .= $value . $lang['file_exists'];
                    $terror = true;
                }

                If ( !$terror )
                {
                    If ( $_FILES['mfile']['name'][$id] )
                    {
                        If ( @move_uploaded_file( $_FILES['mfile']['tmp_name'][$id], $folder . $file_name[$id] ) )
                        {
                            $uploaded = true;
                            $ct = time();

                            $display_message .= $value . $lang['file_uploaded'] . $html->input( "file", "input", $site . $dir . $file_name[$id], 65, 'rinput', 'rtl', '' );
                        }
                        Else
                        {
                            $error .= $file_name[$id] . $lang['error'] . $folder;
                        }
                    }
                }
            }
        }
    }
    else
    {
        $error = $lang['wrong'];
    }
}
If ( $error )
{
    ptintpm( $error );
}
elseIf ( $display_message )
{
    ptintpm( $display_message, 'success' );
}
else
{
    ptintpm( $lang['wrong'] );
}
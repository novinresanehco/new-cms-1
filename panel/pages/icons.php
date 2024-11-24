<?php

define( 'samanehper', 'icon' );
define( "ajax_head", true );
require("ajax_head.php");
$icons16 = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'icons' . DIRECTORY_SEPARATOR . '16' . DIRECTORY_SEPARATOR;
$icons24 = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'icons' . DIRECTORY_SEPARATOR . '24' . DIRECTORY_SEPARATOR;
$result = array( );
$handle16png = getImageName( glob( $icons16 . "*.png" ), '16/' );
$handle16gif = getImageName( glob( $icons16 . '*.gif' ), '16/' );
$result['small'] = array_merge( $handle16png, $handle16gif );
$handle24png = getImageName( glob( $icons24 . '*.png' ), '24/' );
$handle24gif = getImageName( glob( $icons24 . '*.gif' ), '24/' );
$result['big'] = array_merge( $handle24png, $handle24gif );
if ( !defined( 'return_icon' ) )
{
    echo json_encode( $result );
}

function getImageName( $array, $prepend = '' )
{
    $result = array( );
    foreach ( $array as $key => $value )
    {
        $result[$key] = $prepend . pathinfo( $value, PATHINFO_BASENAME );
    }
    return $result;
}
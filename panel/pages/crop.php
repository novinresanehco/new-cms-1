<?php
define( 'samanehper', 'crop' );
define( "ajax_head", true );
require("ajax_head.php");

$allowedExts = array(
    'jpg', 'jpeg', 'gif', 'png', 'gif'
);
$allowedMimes = array(
    'image/gif',
    'image/jpeg',
    'image/png',
);
$image = $_GET['image'];
$image = str_replace( '\\', '/', $image );
$upload = '../files/';
$image = str_replace( $upload, '', $image );
$dir = $image;
$image = $upload . $image;
if ( !file_exists( '../' . $image ) )
{
    die( 'invalid image!' );
}
$ext = strtolower( substr( strrchr( $image, '.' ), 1 ) );
if ( !in_array( $ext, $allowedExts ) )
{
    die( 'invalid image!' );
}
//$finfo = finfo_open( FILEINFO_MIME_TYPE );
//$mime = finfo_file( $finfo, '../' . $image );
//finfo_close( $finfo );
$mime = getimagesize( '../' . $image );
$mime = $mime['mime'];
/*
  $ch = curl_init( $image );
  curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
  $imageSource = curl_exec( $ch );
  $mime = curl_getinfo( $ch, CURLINFO_CONTENT_TYPE );
 */
if ( !in_array( $mime, $allowedMimes ) )
{
    die( 'invalid image!' );
}
//$name = substr( strrchr( $image, '/' ), 1 );
$name = pathinfo( '../' . $image, PATHINFO_FILENAME );
$url = $config['site'] . 'files/' . $dir;
if ( isset( $_GET['action'] ) && $_GET['action'] == 'crop' )
{
    $output = array( );
    $output['success'] = false;
    if ( !isset( $_GET['x1'] ) OR !isset( $_GET['y1'] ) OR !isset( $_GET['x2'] ) OR !isset( $_GET['y2'] ) )
    {
        $output['error'] = 'تغییرات ذخیره نشد.';
    }
    else
    {
        if ( is_numeric( $_GET['x1'] ) && is_numeric( $_GET['y1'] ) && is_numeric( $_GET['x2'] ) && is_numeric( $_GET['y2'] ) )
        {
            switch ( $mime )
            {
                case 'image/jpeg':
                    $src_image = imagecreatefromjpeg( '../' . $image );
                    break;
                case 'image/gif':
                    $src_image = imagecreatefromgif( '../' . $image );
                    break;
                case 'image/png':
                    $src_image = imagecreatefrompng( '../' . $image );
                    break;
                default:
                    die( 'image does not support cropping' );
                    break;
            }

            $src_w = imagesx( $src_image );
            $src_h = imagesy( $src_image );
            $resized_width = (( int ) $_GET["x2"]) - (( int ) $_GET['x1']);
            $resized_height = (( int ) $_GET["y2"]) - (( int ) $_GET["y1"]);
            $resized_image = imagecreatetruecolor( $resized_width, $resized_height );
            imagecopyresampled( $resized_image, $src_image, 0, 0, ( int ) $_GET['x1'], ( int ) $_GET['y1'], $src_w, $src_h, $src_w, $src_h );
            $exits = file_exists( dirname( '../' . $image ) . '/' . $name . '_' . $resized_width . 'x' . $resized_height . '.' . $ext );
            $i = 0;
            if ( $exits )
            {
                $i = 1;
                do
                {
                    $exits = file_exists( dirname( '../' . $image ) . '/' . $name . '_' . $i . '_' . $resized_width . 'x' . $resized_height . '.' . $ext );
                    $i++;
                }
                while ( $exits == true );
            }
            if ( $i == 0 )
            {
                $newName = $name . '_' . $resized_width . 'x' . $resized_height . '.' . $ext;
            }
            else
            {
                $newName = $name . '_' . ($i - 1) . '_' . $resized_width . 'x' . $resized_height . '.' . $ext;
            }
            imagejpeg( $resized_image, dirname( '../' . $image ) . '/' . $newName );
        }
    }
    echo json_encode( $output );
    exit;
}
echo '<div id="cropHolder">';
echo "<img id='cropImage' src='$image' />";
echo '<div id="cropCover"></div>';
echo '</div>';
?>
<script>
    $( document ).ready( function()
    {
        $( '#cropImage' ).imgAreaSelect(
                {
                    handles: true,
                    onSelectEnd: function( img, selection )
                    {
                        $( '#x1' ).val( selection.x1 );
                        $( '#y1' ).val( selection.y1 );
                        $( '#x2' ).val( selection.x2 );
                        $( '#y2' ).val( selection.y2 );
                    }
                }
        );
    } );
</script>
<?php
define( 'samanehper', 'crop' );
define( "ajax_head", true );
require("ajax_head.php");

$allowedExts = array(
    'jpg', 'jpeg', 'gif', 'png', 'gif', 'bmp'
);
$allowedMimes = array(
    'image/gif',
    'image/jpeg',
    'image/png',
    'image/bmp',
);
$image = $_GET['image'];
$image = str_replace( '\\', '/', $image );
$upload = '../files/';
$image = str_replace( $upload, '', $image );
$dir = $image;
$image = '../' . $upload . $image;
if ( !file_exists( $image ) )
{
    die( 'invalid image!' );
}
$ext = strtolower( substr( strrchr( $image, '.' ), 1 ) );
if ( !in_array( $ext, $allowedExts ) )
{
    die( 'invalid image!' );
}
$finfo = finfo_open( FILEINFO_MIME_TYPE );
$mime = finfo_file( $finfo, $image );
finfo_close( $finfo );
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
$name = substr( strrchr( $image, '/' ), 1 );
$url = $config['site'] . 'files/' . $dir;
//initialize cropper
echo '<script type="text/javascript" src="../../theme/admin/pannonia/js/jquery.min.js"></script>';
echo '<script type="text/javascript" src="../../theme/admin/pannonia/js/imagearea/jquery.imgareaselect.pack.js"></script>';
echo '<link href="../../theme/admin/pannonia/js/imagearea/css/imgareaselect-default.css" rel="stylesheet" type="text/css" />';

echo '
<style>
#cropHolder
{
    /*position: relative;*/
}
#cropImage
{

}
#cropCover
{
    position:absolute;
    top: 0;
    left: 0;
    z-index: 1000;
    opacity: 0.8;
    width: 100%;
    height: 100%;
    background : #000000;
    display:none;
}
</style>
';
echo '<div id="cropHolder">';
echo "<img id='cropImage' src='$image' />";
echo '<div id="cropCover"></div>';
echo '</div>';
?>
<input type='hidden' name='x1' id='x1' value='' /> 
<input type='hidden' name='y1' id='y1' value='' /> 
<input type='hidden' name='x2' id='x2' value='' /> 
<input type='hidden' name='y2' id='y2' value='' /> 
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
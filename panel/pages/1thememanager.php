<?php

define( 'samanehper', 'thememanager' );
define( "ajax_head", true );
require("ajax_head.php");

function printpm( $msg = 'waccess', $type = 'error' )
{
    global $lang;
    $out            = array();
    $out['success'] = false;
    if ( $type == 'success' )
    {
        $out['success'] = true;
    }
    $out['value'] = "<div class='MsgHead $type'><div class='MsgBody'>$lang[$msg]</div></div>";
    echo json_encode( $out );
    exit;
}

$themesGLob = glob( current_theme_dir . '*.htm*' );
$themes     = array();
foreach ( $themesGLob as $value )
{
    $value    = str_replace( current_theme_dir, '', $value );
    $value    = str_replace( '.html', '', $value );
    $value    = str_replace( '.htm', '', $value );
    $value    = trim( $value, '/' );
    $value    = trim( $value, '\\' );
    $themes[] = $value;
}

if ( isset( $_POST['themeAction'] ) )
{
    switch ( $_POST['themeAction'] )
    {
        case 'saveCatThemes':
            if ( !isset( $_POST['catTheme'] ) OR !is_array( $_POST['catTheme'] ) )
            {
                exit( 'error' );
            }
            foreach ( $_POST['catTheme'] as $category => $theme )
            {
                if ( !is_numeric( $category ) )
                {
                    continue;
                }
                $rawName = str_replace( '.html', '', $theme );
                $rawName = str_replace( '.htm', '', $rawName );

                if ( $theme == 'default' && in_array( 'cat_' . $category, $themes ) )
                {
                    if ( !unlink( current_theme_dir . 'cat_' . $category . '.htm' ) )
                    {
                        printpm( 'خطا در سطح دسترسی ها' );
                    }
                }
                else
                {
                    if ( $theme != 'cat_' . $category . '.htm' && in_array( $rawName, $themes ) )
                    {

                        /* copy theme and rename */

                        $newTheme = file_get_contents( current_theme_dir . $theme );
                        $handle   = fopen( current_theme_dir . 'cat_' . $category . '.htm', 'w+' );
                        fwrite( $handle, $newTheme );
                        fclose( $handle );
                        chmod( current_theme_dir . 'cat_' . $category . '.htm', 0644 );
                    }
                }
            }

            if ( !isset( $_POST['subCatTheme'] ) OR !is_array( $_POST['subCatTheme'] ) )
            {
                exit( 'error' );
            }

            foreach ( $_POST['subCatTheme'] as $category => $theme )
            {
                if ( !is_numeric( $category ) )
                {
                    continue;
                }
                $rawName = str_replace( '.html', '', $theme );
                $rawName = str_replace( '.htm', '', $rawName );

                if ( $theme == 'default' && in_array( 'cat_' . $category . '_single', $themes ) )
                {
                    if ( !unlink( current_theme_dir . 'cat_' . $category . '_single' . '.htm' ) )
                    {
                        printpm( 'خطا در سطح دسترسی ها' );
                    }
                }
                else
                {
                    if ( $theme != 'cat_' . $category . '_single.htm' && in_array( $rawName, $themes ) )
                    {

                        /* copy theme and rename */

                        $newTheme = file_get_contents( current_theme_dir . $theme );
                        $handle   = fopen( current_theme_dir . 'cat_' . $category . '_single.htm', 'w+' );
                        fwrite( $handle, $newTheme );
                        fclose( $handle );
                        chmod( current_theme_dir . 'cat_' . $category . '_single.htm', 0644 );
                    }
                }
            }

            die( "<div class='alert margin'>تنظیمات با موفقیت ذخیره شد.</div>" );

            exit;
            break;

        case 'savePageThemes':

            if ( !isset( $_POST['extraTheme'] ) OR !is_array( $_POST['extraTheme'] ) )
            {
                exit( 'error' );
            }
            foreach ( $_POST['extraTheme'] as $pageID => $theme )
            {
                if ( !is_numeric( $pageID ) )
                {
                    continue;
                }
                $rawName = str_replace( '.html', '', $theme );
                $rawName = str_replace( '.htm', '', $rawName );

                if ( $theme == 'default' && in_array( 'extra_' . $pageID, $themes ) )
                {
                    if ( !unlink( current_theme_dir . 'extra_' . $pageID . '.htm' ) )
                    {
                        printpm( 'خطا در سطح دسترسی ها' );
                    }
                }
                else
                {
                    if ( $theme != 'extra_' . $pageID . '.htm' && in_array( $rawName, $themes ) )
                    {

                        /* copy theme and rename */

                        $newTheme = file_get_contents( current_theme_dir . $theme );
                        $handle   = fopen( current_theme_dir . 'extra_' . $pageID . '.htm', 'w+' );
                        fwrite( $handle, $newTheme );
                        fclose( $handle );
                        chmod( current_theme_dir . 'extra_' . $pageID . '.htm', 0644 );
                    }
                }
            }
            die( "<div class='alert margin'>تنظیمات با موفقیت ذخیره شد.</div>" );
            break;
        case 'savePluginThemes':
            if ( !isset( $_POST['pluginTheme'] ) OR !is_array( $_POST['pluginTheme'] ) )
            {
                exit( 'error' );
            }
            foreach ( $_POST['pluginTheme'] as $plugin => $theme )
            {
                if ( !ctype_alnum( $plugin ) OR !file_exists( dirname( __FILE__ ) . DS . '..' . DS . '..' . DS . 'plugins' . DS . $plugin . DS . $plugin . '.php' ) )
                {
                    continue;
                }
                $rawName = str_replace( '.html', '', $theme );
                $rawName = str_replace( '.htm', '', $rawName );

                if ( $theme == 'default' && in_array( 'plugin_' . $plugin, $themes ) )
                {
                    if ( !unlink( current_theme_dir . 'plugin_' . $plugin . '.htm' ) )
                    {
                        printpm( 'خطا در سطح دسترسی ها' );
                    }
                }
                else
                {
                    if ( $theme != 'plugin_' . $plugin . '.htm' && in_array( $rawName, $themes ) )
                    {

                        /* copy theme and rename */

                        $newTheme = file_get_contents( current_theme_dir . $theme );
                        $handle   = fopen( current_theme_dir . 'plugin_' . $plugin . '.htm', 'w+' );
                        fwrite( $handle, $newTheme );
                        fclose( $handle );
                    }
                }
            }
            die( "<div class='alert margin'>تنظیمات با موفقیت ذخیره شد.</div>" );
            break;
        case 'saveNewTheme':
            if ( empty( $_POST['myThemeName'] ) )
            {
                die( "<div class='alert margin'>نام قالب وارد نشده است.</div>" );
            }
            else
            {
                $myThemeName = safe( $_POST['myThemeName'] );
                $query       = $d->query( "SELECT * FROM  `themeArchive` WHERE `title`='$myThemeName' LIMIT 1" );
                if ( $d->getRows( $query ) > 0 )
                {
                    die( "<div class='alert margin'>نام قالب تکراری است.</div>" );
                }
                else
                {
                    $newTheme              = array();
                    $newTheme['positions'] = array();
                    $newTheme['config']    = array();
                    $newTheme['themes']    = array();
                    $newTheme['theme']     = $config['theme'];
                    $positions             = $d->query( "SELECT * FROM `positions`" );

                    while ( $data = $d->fetch( $positions ) )
                    {
                        $newTheme['positions'][] = $data;
                    }

                    foreach ( $config as $name => $value )
                    {
                        if ( substr( $name, 0, 6 ) == 'theme_' )
                        {
                            $newTheme['config'][$name] = $value;
                        }
                    }

                    $themesGLob = glob( current_theme_dir . '*.htm*' );
                    foreach ( $themesGLob as $value )
                    {
                        $name                      = str_replace( current_theme_dir, '', $value );
                        $name                      = trim( $name, '/' );
                        $name                      = trim( $name, '\\' );
                        $newTheme['themes'][$name] = file_get_contents( $value );
                        if ( $newTheme['themes'][$name] === false )
                        {
                            die( "<div class='alert margin'>خطا در خواندن فایل $name</div>" );
                        }
                    }
                    $newTheme = base64_encode( serialize( $newTheme ) );
                    $name     = 'back_' . rand( 1000, 200000 ) . '_' . time();
                    $h        = fopen( __DIR__ . DIRECTORY_SEPARATOR . '../themeBackUp/' . $name . '.php', 'w+' );
                    if ( $h )
                    {
                        fwrite( $h, '<?php exit; ?>' . "\r\n" . $newTheme );
                        fclose( $h );
                    }
                    else
                    {
                        die( "<div class='alert margin'>error, please try again !.</div>" );
                    }
                    $d->iQuery( 'themeArchive', array(
                        'date'  => time(),
                        'title' => $myThemeName,
                        'data'  => $name,
                    ) );

                    die( "<div class='alert margin'>تنظیمات ذخیره شد.</div>" );
                }
            }
            exit;
            break;
        case 'deleteMyTheme':
            if ( empty( $_POST['delete'] ) OR !is_numeric( $_POST['delete'] ) )
            {
                exit( 'wrong data' );
            }
            else
            {
                $id = ( int ) $_POST['delete'];

                $d->Query( "DELETE FROM `themeArchive` WHERE `id`='$id' LIMIT 1" );
                if ( $d->affected() > 0 )
                {
                    die( "<div class='alert margin'>قالب با موفقیت حذف شد.</div>" );
                }
            }
            exit;

            break;
        case 'saveMyTheme':
            if ( empty( $_POST['id'] ) OR !is_numeric( $_POST['id'] ) )
            {
                exit( 'wrong data' );
            }
            else
            {
                $id = ( int ) $_POST['id'];

                $theme = $d->query( "SELECT * FROM `themeArchive` WHERE `id`='$id' LIMIT 1" );

                if ( $d->getRows( $theme ) !== 1 )
                {
                    die( "<div class='alert margin'>اطلاعات ارسالی ناصحیح است.</div>" );
                }
                else
                {
                    $theme  = $d->fetch( $theme );
                    $name   = $theme['data'] . '.php';
                    $handle = fopen( __DIR__ . DIRECTORY_SEPARATOR . '../themeBackUp/' . $name, 'rt' );
                    if ( $handle )
                    {
                        $themeContent = '';
                        while ( !feof( $handle ) )
                        {
                            $themeContent .= fread( $handle, 10000 );
                        }
                    }

                    if ( empty( $themeContent ) )
                    {
                        die( "<div class='alert margin'>قالب ذخیره شده قابل استفاده نیست.</div>" );
                    }
                    $themeContent = nl2br( $themeContent );
                    $themeContent = str_replace( '<?php exit; ?>' . "<br />", '', $themeContent );
                    $themeContent = trim( $themeContent );
                    $themeContent = ( unserialize( base64_decode( $themeContent ) ) );
                    if ( !is_array( $themeContent ) )
                    {
                        die( "<div class='alert margin'>قالب ذخیره شده قابل استفاده نیست.</div>" );
                    }

                    if ( !is_dir( theme_dir . 'core' . DS . $themeContent['theme'] ) )
                    {
                        die( "<div class='alert margin'>قالب $themeContent[theme] باید نصب شده باشد.</div>" );
                    }

                    /* backup current theme */

                    /* creat backup directory */

                    if ( !is_dir( current_theme_dir . DS . 'backup' ) )
                    {
                        if ( !mkdir( current_theme_dir . DS . 'backup', 0777 ) )
                        {
                            die( "<div class='alert margin'>امکان ایجاد فولدر بک آپ پشتیبان وجود ندارد.</div>" );
                        }
                    }

                    $backupdir = date( 'Y_m_d_h_i_s' );

                    if ( !is_dir( current_theme_dir . DS . 'backup' . DS . $backupdir ) )
                    {
                        if ( !mkdir( current_theme_dir . DS . 'backup' . DS . $backupdir, 0777 ) )
                        {
                            die( "<div class='alert margin'>امکان ایجاد فولدر بک آپ پشتیبان وجود ندارد.</div>" );
                        }
                    }

                    $backupfulldir = current_theme_dir . DS . 'backup' . DS . $backupdir . DS;

                    $themesGLob = glob( current_theme_dir . '*.htm*' );

                    foreach ( $themesGLob as $value )
                    {
                        $name = str_replace( current_theme_dir, '', $value );
                        $name = trim( $name, '/' );
                        $name = trim( $name, '\\' );

                        $file = file_get_contents( $value );
                        if ( $file === false )
                        {
                            die( "<div class='alert margin'>خطا در خواندن فایل $name</div>" );
                        }
                        $backupName = '.' . $name . '.backup';
                        $handle     = fopen( $backupfulldir . $backupName, 'w+' );
                        if ( !$handle )
                        {
                            die( "<div class='alert margin'>امکان ایجاد فایل پشتیبان وجود ندارد.</div>" );
                        }
                        fwrite( $handle, $file );
                        fclose( $handle );
                    }

                    /* remove old theme configurations */


                    if ( substr( $name, 0, 4 ) == 'cat_' )
                    {
                        if ( !unlink( $value ) )
                        {
                            die( "<div class='alert margin'>خطا در حذف فایل :: پرمشین ها بررسی شوند. :: $name</div>" );
                        }
                    }
                    else
                    if ( substr( $name, 0, 6 ) == 'extra_' )
                    {
                        if ( !unlink( $value ) )
                        {
                            die( "<div class='alert margin'>خطا در حذف فایل :: پرمشین ها بررسی شوند. :: $name</div>" );
                        }
                    }
                    else
                    if ( substr( $name, 0, 7 ) == 'plugin_' )
                    {
                        if ( !unlink( $value ) )
                        {
                            die( "<div class='alert margin'>خطا در حذف فایل :: پرمشین ها بررسی شوند. :: $name</div>" );
                        }
                    }
                    else
                    if ( preg_match( "#cat_[0-9]+_single\.htm#i", $name ) )
                    {
                        if ( !unlink( $value ) )
                        {
                            die( "<div class='alert margin'>خطا در حذف فایل :: پرمشین ها بررسی شوند. :: $name</div>" );
                        }
                    }

                    /* change theme */

                    $d->Query( "UPDATE `config` SET `value`='$themeContent[theme]' WHERE `name`='theme' LIMIT 1" );

                    /* delete current theme settings */

                    $d->Query( "DELETE FROM `config` WHERE `name` LIKE 'theme\_%'" );

                    /* delete current theme positions */

                    $d->Query( 'DELETE FROM `positions`' );

                    /* insert new theme positions */

                    foreach ( $themeContent['positions'] as $position )
                    {
                        $d->iQuery( 'positions', $position );
                    }

                    /* insert new theme configs */

                    foreach ( $themeContent['config'] as $name => $value )
                    {
                        saveconfig( $name, $value );
                    }

                    /* creat new theme files */

                    foreach ( $themeContent['themes'] as $name => $file )
                    {
                        $handle = fopen( current_theme_dir . $name, 'w+' );
                        if ( !$handle )
                        {
                            print( "<div class='alert margin'>هشدار :: خطا در ایجاد فایل $name .</div>" );
                        }
                        fwrite( $handle, $file );
                        fclose( $handle );
                    }

                    exit( "ok<div class='alert margin'>تنظیمات با موفقیت بروز شد.</div>" );
                }
            }
            break;
        default:
            exit( 'invalid action' );
            break;
    }
}
$editingTheme = 'first';
if ( !isset( $_POST['theme'] ) OR !in_array( $_POST['theme'], $themes ) )
{
    $_POST['theme'] = 'first';
}
else
{
    $editingTheme = $_POST['theme'];
}
$themeSchema = 'theme_' . $_POST['theme'] . 'html';

$old_positions_q = $d->Query( "SELECT * FROM `positions` WHERE `theme`='$editingTheme'" );
$old_positions = array();
while( $data = $d->fetch( $old_positions_q ) )
{
	$old_positions[$data['id']] = $data['data'];
}	
$d->Query( "DELETE FROM `positions` WHERE `theme`='$editingTheme'" );
if ( isset( $_POST['posdata'] ) && is_array( $_POST['posdata'] ) )
{
    $pluginsq    = $d->Query( "SELECT * FROM `plugins` WHERE `sortable`='1'" );
    $listplugins = array();
    while ( $data        = $d->fetch( $pluginsq ) )
    {
        $listplugins[] = $data['name'];
    }
	
    // echo "<div style='direction:ltr;text-align:left'><pre>";
    // print_r($_POST['posdata']);exit('</pre></div>');
    foreach ( $_POST['posdata'] as $position => $plugins )
    {
        if ( !is_array( $plugins ) )
            continue;
        $id = str_replace( "position", "", $position );
        if ( !is_numeric( $id ) )
            continue;
        foreach ( $plugins as $plugin )
        {
            //print_r($plugin);
            if ( empty( $plugin["plugin"] ) )
                continue;
            $name = trim( $plugin["plugin"] );
			$old_id = '';
			if( is_numeric( $plugin["data"] ) )
			{
				$data = '';
				$old_id = $plugin["data"];
			}
			else
			{
				$data = base64_decode( trim( $plugin["data"] ) );
				if ( !empty( $data ) )
				{
					parse_str( $data, $data );
					$data = base64_encode( serialize( $data ) );
				}
			}
            
            if ( $name != 'remove' && in_array( $name, $listplugins ) )
            {
				if( empty( $data ) && is_numeric( $old_id ) )
				{
					$d->iquery( "positions", array( 'pid' => $id, 'value' => $name, 'data' => $old_positions[$old_id], 'theme' => $editingTheme ) );
				}
				else
				{
					$d->iquery( "positions", array( 'pid' => $id, 'value' => $name, 'data' => $data, 'theme' => $editingTheme ) );
				}
            }
        }
    }
    if ( isset( $_POST['siderBars'] ) && is_array( $_POST['siderBars'] ) )
    {
        saveconfig( 'theme_' . $config['theme'] . '_' . $_POST['theme'] . '_sidebars', json_encode( $_POST['siderBars'] ) );
    }
}
printpm( "ok", "success" );

function array_utf8_encode_recursive( $dat )
{
    if ( is_string( $dat ) )
    {
        return utf8_encode( $dat );
    }
    if ( is_object( $dat ) )
    {
        $ovs = get_object_vars( $dat );
        $new = $dat;
        foreach ( $ovs as $k => $v )
        {
            $new->$k = array_utf8_encode_recursive( $new->$k );
        }
        return $new;
    }

    if ( !is_array( $dat ) )
        return $dat;
    $ret     = array();
    foreach ( $dat as $i => $d )
        $ret[$i] = array_utf8_encode_recursive( $d );
    return $ret;
}

function array_utf8_decode_recursive( $dat )
{
    if ( is_string( $dat ) )
    {
        return utf8_decode( $dat );
    }
    if ( is_object( $dat ) )
    {
        $ovs = get_object_vars( $dat );
        $new = $dat;
        foreach ( $ovs as $k => $v )
        {
            $new->$k = array_utf8_decode_recursive( $new->$k );
        }
        return $new;
    }

    if ( !is_array( $dat ) )
    {
        return $dat;
    }
    $ret = array();
    foreach ( $dat as $i => $d )
    {
        $ret[$i] = array_utf8_decode_recursive( $d );
    }
    return $ret;
}

<?php

define( 'head', true );
define( 'page', 'thememanager' );
$pageTheme = 'thememanager.htm';
$pagetitle = 'قالب ساز';
define( 'tabs', true );
$tabs      = array( 'قالب ساز', 'تنظیمات قالب', 'ویرایشگر', 'انتخاب قالب', 'دسته ها', 'صفحات', 'پلاگین ها', 'آرشیو', 'مشاده قالب', 'راهنما' );
include('header.php');
if ( !defined( 'plugins_admin_area' ) )
{
    define( 'plugins_admin_area', true );
}
//list all possible theme files
$htmlfiles    = listFiles( current_theme_dir, array( 'html', 'htm' ) );
$tmphtmlfiles = array();
foreach ( $htmlfiles as $file )
{
    $file             = str_replace( current_theme_dir, '', $file );
    $file             = trim( $file, '/' );
    $file             = trim( $file, '\\' );
    $data             = array( 'file' => $file );
    $data['selected'] = '';
    if ( isset( $_POST['editor'] ) && !empty( $_POST['file'] ) && $_POST['file'] === $file )
    {
        $tpl->assign( 'mode', 'text/html' );
        $data['selected'] = 'selected';
    }
    //exclude index.html and index.htm from list
    if ( strpos( $file, 'index.htm' ) === FALSE )
    {
        $tmphtmlfiles[] = $file;
        $tpl->block( 'htmlfiles', $data );
    }
}
$htmlfiles   = $tmphtmlfiles;
unset( $tmphtmlfiles );
/* css files */
$cssfiles    = listFiles( current_theme_dir, 'css' );
$tmpcssfiles = array();
foreach ( $cssfiles as $file )
{
    $file             = str_replace( current_theme_dir, '', $file );
    $file             = trim( $file, '/' );
    $data             = array( 'file' => $file );
    $data['selected'] = '';
    if ( isset( $_POST['editor'] ) && !empty( $_POST['file'] ) && $_POST['file'] === $file )
    {
        $tpl->assign( 'mode', 'text/css' );
        $data['selected'] = 'selected';
    }
    $tmpcssfiles[] = $file;
    $tpl->block( 'cssfiles', $data );
}
$cssfiles = $tmpcssfiles;
unset( $tmpcssfiles );
/* javascript files */
/*
  $jsfiles = listFiles( current_theme_dir, 'js' );
  foreach ( $jsfiles as $file )
  {
  $file = str_replace( current_theme_dir, '', $file );
  $file = trim( $file, '/' );
  $data = array( 'file' => $file );
  $data['selected'] = '';
  if ( isset( $_POST['editor'] ) && !empty( $_POST['file'] ) && $_POST['file'] === $file )
  {
  $tpl->assign( 'mode', 'text/javascript' );
  $data['selected'] = 'selected';
  }
  $tpl->block( 'jsfiles', $data );
  }

 */
if ( isset( $_POST['editor'] ) && !empty( $_POST['file'] ) )
{
    $tpl->assign( 'editTab', true );
    if ( strpos( $_POST['file'], '..' ) !== false )
    {
        $tpl->block( 'EditorMessage', array( 'message' => 'فایل معتبر نیست.' ) );
    }
    else
    {
        //if ( !in_array( $_POST['file'], $htmlfiles ) && !in_array( $_POST['file'], $cssfiles ) && !!in_array( $_POST['file'], $jsfiles ) )
        if ( !in_array( $_POST['file'], $htmlfiles ) && !in_array( $_POST['file'], $cssfiles ) )
        {
            $tpl->block( 'EditorMessage', array( 'message' => 'فایل معتبر نیست.' ) );
        }
        else
        {
            //double check for file existence and extension
            $file = current_theme_dir . $_POST['file'];
            if ( !file_exists( $file ) OR !in_array( pathinfo( $file, PATHINFO_EXTENSION ), array( 'html', 'htm', 'js', 'css' ) ) )
            {
                $tpl->block( 'EditorMessage', array( 'message' => 'فایل معتبر نیست.' ) );
            }
            else
            {
                //initialize editor
                $tpl->assign( 'editor', true );
                //save request
                if ( isset( $_POST['themeEditor'] ) )
                {
                    $handle = fopen( $file, 'w' );
                    if ( $handle )
                    {
                        fwrite( $handle, stripslashes( $_POST['themeEditor'] ) );
                        fclose( $handle );
                        $tpl->block( 'EditorMessage', array( 'message' => 'تغییرات ذخیره شد.' ) );
                    }
                }
                $fileContent = @file_get_contents( $file );
                if ( $fileContent === false )
                {
                    $tpl->block( 'EditorMessage', array( 'message' => 'خطا در خواندن فایل.' ) );
                }
                else
                {
                    $tpl->assign( 'pagecode', $fileContent );
                    $tpl->assign( 'currentfile', $_POST['file'] );
                }
            }
        }
    }
}
if ( isset( $_POST['themesetting'] ) )
{
    $result       = array();
    $themeSetting = @file_get_contents( current_theme_dir . 'info.ini' );
    if ( $themeSetting === false )
    {
        $result['message'] = 'فایل تنظیمات قالب در دسترس نیست.';
    }
    else
    {
        if ( !isJson( $themeSetting ) )
        {
            $result['message'] = 'فایل تنظیمات مخدوش است.';
        }
        else
        {
            $themeSetting = json_decode( $themeSetting, true );
			$themeSetting['mailtpl'] = 'default.htm';
            foreach ( $themeSetting as $key => $value )
            {
                if ( isset( $_POST['themesetting_' . $key] ) )
                {
                    saveconfig( 'theme_' . $config['theme'] . '_' . $key, $_POST['themesetting_' . $key] );
                }
                $result['message'] = 'تنظیمات با موفقیت ذخیره شد.';
            }
        }
    }
    echo json_encode( $result );
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
    $tpl->block( 'listHtmlFiles', array(
        'theme'     => $value,
        'themeName' => $value,
        'selected'  => ( (isset( $_GET['theme'] )) && $_GET['theme'] == $value ) ? 'selected' : '',
    ) );
}
$editingTheme = 'first';
if ( !isset( $_GET['theme'] ) OR !in_array( $_GET['theme'], $themes ) )
{
    $_GET['theme'] = 'first';
}
else
{
    $editingTheme = $_GET['theme'];
}
$themeSchema = 'theme_' . $_GET['theme'] . 'html';
$tpl->assign( 'editingtheme', $editingTheme );
$tpl->assign( 'theme_' . $editingTheme, 'selected' );
if ( !file_exists( current_theme_dir . $themeSchema ) )
{
    $themeSchema = 'theme.htm';
}
if ( file_exists( current_theme_dir . $themeSchema ) )
{
    if ( isset( $_GET['task'] ) )
    {
        switch ( $_GET['task'] )
        {
            case 'pluginSetting':
                if ( empty( $_GET['plugin'] ) )
                {
                    echo 'Invalid plugin name';
                    exit;
                }
				/*
				if( preg_match( '#^tab\_([0-9]+)$#', $_GET['plugin'], $m ) )
				{
					$tab_id = $m[1];
					if( !is_numeric( $tab_id ) )
					{
						exit( 'invalid tab id' );
					}
					$group = $d->Query( "SELECT * FROM `tabs_groups` WHERE `id`='$tab_id'" );
					$information = array();
					$information['name'] = 'tab';
					$information['provider'] = 'tab';
					$information['providerurl'] = 'tab';
				}
				*/
				if( true )
				{
					if( !ctype_alnum( $_GET['plugin'] ) )
					{
						echo 'Invalid plugin name';
						exit;
					}
					$plugin = $d->Query( "SELECT * FROM `plugins` WHERE `sortable`='1' AND `name`='$_GET[plugin]' LIMIT 1" );
					if ( $d->getRows( $plugin ) !== 1 )
					{

						echo 'Invalid plugin name';
						exit;
					}

					$plugin   = $d->fetch( $plugin );
					
					defined( 'plugins_admin_area' ) or define( 'plugins_admin_area', true );
					require('../plugins/' . $plugin['name'] . '/admin.php' );
				}				
                $Themedir = "../theme/admin/" . $config['admintheme'] . '/';
                $tpl      = new samaneh();
                $tpl->load( $Themedir . 'thememanagerinfo.htm' );
                $tpl->assign( 'site', $config['site'] );
                $tpl->assign( array(
                    'name'        => $information['name'],
                    'provider'    => $information['provider'],
                    'providerurl' => $information['providerurl'],
                ) );
                $new_item = false;
                if ( isset( $_GET['id'] ) && is_numeric( $_GET['id'] ) )
                {
                    //module may have default values
                    $positionData = $d->Query( "SELECT * FROM `positions` WHERE `id`='$_GET[id]' LIMIT 1" );
                    if ( $d->getRows( $positionData ) !== 1 )
                    {
                        if ( substr( $_GET['id'], 0, 16 ) == '1000200030009000' )
                        {
                            $new_item = true;
                        }
                        else
                        {
                            die( 'invalid position data.' );
                        }
                    }
                    else
                    {
                        $positionData = $d->fetch( $positionData );
                        $positionData = trim( $positionData['data'] );
                        $positionData = unserialize( base64_decode( $positionData ) );
                    }
                }
                $tpl->assign( 'id', $_GET['id'] );
                if ( isset( $information['data'] ) )
                {
                    foreach ( $information['data'] as $name => $data )
                    {
                        if ( empty( $data['type'] ) )
                        {
                            $data['type'] = 'input';
                        }
                        if ( !$new_item && isset( $positionData['pluginData'][$name] ) )
                        {

                            $data['value'] = $positionData['pluginData'][$name];
                        }
                        if ( !empty( $_POST['data'] ) )
                        {

                            //parse_str( base64_decode( $_POST['data'] ), $positionData );

                            if ( !empty( $positionData['pluginData'][$name] ) )
                            {
                                $data['value'] = $positionData['pluginData'][$name];
                            }
                        }
                        if ( empty( $data['class'] ) )
                        {
                            $data['class'] = '';
                        }
                        $data['class'] .= ' input-xlarge';
                        $tpl->block( 'fields', array(
                            'title' => $data['name'],
                            'input' => generateField( $data['type'], "pluginData[$name]", "pluginData_$name" . rand( 1000, 2000 ), isset( $data['class'] ) ? $data['class'] : '', isset( $data['value'] ) ? $data['value'] : '', isset( $data['options'] ) ? $data['options'] : ''  ),
                        ) );
                    }
                }
				$temp = $tpl->dontshowit();
				//$temp = preg_replace( '#<sidebar_([0-9]+)_([0-9]+)>(.*)</sidebar_\\1_\\2>#iUs', "\\1", $temp );
				die( $temp );
                //$tpl->showit();
                break;
            default:
                die( 'invalid request' );
                break;
        }
        exit;
    }
    $thememanager = new samaneh();
    $thememanager->load( current_theme_dir . $themeSchema );
    $themecontent = $thememanager->dontshowit();


    /* sort sidebars */

    preg_match_all( "#<sidebar_([0-9]+)_([0-9]+)>(.*)</sidebar_\\1_\\2>#iUs", $themecontent, $sidebars );
    $sideBarOrders = array();
    $order         = 0;
	$orders = array();
    for ( $i = 0, $c = count( $sidebars[2] ); $i < $c; $i++ )
    {
        $groupID   = $sidebars[1][$i];
        $sidebarID = $sidebars[2][$i];
        $sideBar   = $sidebars[0][$i];
        $name      = 'theme_' . $config['theme'] . '_' . $editingTheme . '_sidebars';
        if ( isset( $config[$name] ) )
        {
            $sidebarsData = json_decode( $config[$name], true );
            if ( !isset( $sidebarsData[$groupID . '_' . $sidebarID] ) )
            {
                //mark sidebar as removed
                $sideBar      = str_replace( '[class]', 'removed', $sideBar );
                $themecontent = str_replace( $sidebars[0][$i], $sideBar, $themecontent );
            }
            else
            {
                $sideBar         = str_replace( '[class]', '', $sideBar );
                $sideBarOrders[] = $sideBar;
                $order           = $sidebarsData[$groupID . '_' . $sidebarID];
				if( in_array( $order, $orders ) )
				{
					do
					{
						$order++;
					}while( in_array( $order, $orders ) );
				}
				$orders[] = $order;
                $themecontent    = str_replace( $sidebars[0][$i], "[siderbarOrder_$order]", $themecontent );
            }
        }
    }
    foreach ( $sideBarOrders as $key => $value )
    {
        $themecontent = str_replace( "[siderbarOrder_$key]", $value, $themecontent );
    }
    //$themecontent = str_replace( $sidebars[0][$i], $sideBar, $themecontent );
    //end order sidebars
    preg_match_all( "#\[position([0-9]+)\]#U", $themecontent, $positions );
    $postionsData = '';
    $positions    = $positions[1];
    foreach ( $positions as $position )
    {
        $out       = "<ul class='sortable' id='position{$position}'>";
//loop
        $cposquery = $d->Query( "SELECT * FROM `positions` WHERE `pid` ='{$position}' AND `theme`='$editingTheme'" );
        $childs    = 0;
        while ( $data      = $d->fetch( $cposquery ) )
        {
            unset( $information );
            require('../plugins/' . $data['value'] . '/admin.php' );
            $out.= "<li class='ui-state-highlight modulec' id='plugin_$data[id]' rel='$data[value]'>{$information['name']}</li>";
            $data['data'] = unserialize( base64_decode( $data['data'] ) );
            $childs++;
            if ( isset( $data['data']['pluginData'] ) && is_array( $data['data']['pluginData'] ) )
            {
                $jquerydata = '';
                foreach ( $data['data']['pluginData'] as $index => $value )
                {
                    $jquerydata .= "&pluginData[$index]=$value";
                }
                $jquerydata = base64_encode( urlencode( ltrim( $jquerydata, "&" ) ) );
				$jquerydata = str_replace(
				array( '%3D', '%26' ),
				array( '=', '&' ),
				$jquerydata
				);
				$jquerydata = $data['id'];
                $postionsData .= "<input type='hidden' id='optionValue_$data[id]' value='$jquerydata'>";
            }
        }
        if ( $childs == 0 )
        {
            $out.= "<li class='notremove ui-state-highlight modulec' rel='remove'>موقعیت {$position}</li>";
        }
        $out .="</ul>";
        $themecontent = str_replace( "[position{$position}]", $out, $themecontent );
        $tpl->block( 'positions', array( 'id' => $position ) );
    }
	//$themecontent = preg_replace( '#<sidebar_([0-9]+)_([0-9]+)>(.*)</sidebar_\\1_\\2>#iUs', "\\3", $themecontent );
    $tpl->assign( "thememanager", $postionsData . $themecontent );
}
else
{
    $tpl->assign( "thememanager", '' );
}

/* list themes */
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
        $cats = '';

        if ( !empty( $readme ) )
        {
            $readme = explode( '<br />', nl2br( $readme ) );
            $cats   = $readme[0];
            unset( $readme[0] );
            $readme = implode( "<br />", $readme );
        }
        $tpl->block( 'themelist', array(
            'readme'     => $readme,
            'theme_name' => $themelist[$i],
            'theme_dir'  => $themelist[$i],
            'class'      => (( $themelist[$i] == $config['theme'] ) ? 'currentTheme' : '') . ' ' . $cats,
        ) );
    }
}

//list all sortable plugins
$plugins = $d->Query( "SELECT * FROM `plugins` WHERE `sortable`='1'" );
/*
function table_exist($table){
    global $d;
    $sql = "show tables like '".$table."'";
    $res = $d->query($sql);
    return ($d->getrows( $res ) > 0);
}
*/

while ( $data = $d->fetch( $plugins ) )
{

    require('../plugins/' . $data['name'] . '/admin.php' );
    $htpl->block( "plugins", array(
        'name'  => $data['name'],
        'title' => $information['name']
    ) );
}
/*
// tabs groups
if( table_exist( 'tabs_groups' ) )
{
	$group = $d->Query( "SELECT * FROM `tabs_groups`" );
	while ( $data = $d->fetch( $group ) )
	{
		$htpl->block( "plugins", array(
			'name'  => 'tab_'.$data['id'],
			'title' => $data['title']
		) );
	}
}
*/
//detect theme settings
if( !function_exists( 'get_email_templates' ) )
{
	function get_email_templates()
	{
		$themesGLob = glob( theme_dir . DS . 'mail' . DS . '*.htm' );
		$themes     = array();
		foreach ( $themesGLob as $value )
		{
			$value    = str_replace( theme_dir . DS . 'mail' . DS  , '', $value );
			$value 	  = explode( DS , $value );
			$value    = $value[(count( $value ) - 1)];
			$value    = str_replace( '.html', '', $value );
			$value    = str_replace( '.htm', '', $value );
			$value    = trim( $value, '/' );
			$value    = trim( $value, '\\' );
			$themes[$value] = $value;
		}
		return $themes;
	}
}	
//check for info.ini file
if ( !file_exists( current_theme_dir . 'info.ini' ) )
{
    $tpl->assign( 'themeSettings', '<div class="alert margin">این قالب شامل تنظیمات اضافی نمی باشد.</div>' );
}
else
{
    $themeSetting = @file_get_contents( current_theme_dir . 'info.ini' );
    if ( $themeSetting === false )
    {
        $tpl->assign( 'themeSettings', '<div class="alert margin">فایل تنظیمات قابل خواندن نبود.</div>' );
    }
    else
    {
        if ( !isJson( $themeSetting ) )
        {
            $tpl->assign( 'themeSettings', '<div class="alert margin">فایل تنظیمات خراب است.</div>' );
        }
        else
        {
            $themeSetting = json_decode( $themeSetting, true );
            //parse theme Settings
            foreach ( $themeSetting as $name => $data )
            {
                if ( empty( $data['type'] ) )
                {
                    $data['type'] = 'input';
                }
                /*
                  if ( isset( $positionData['pluginData'][$name] ) )
                  {
                  $data['value'] = $positionData['pluginData'][$name];
                  }
                 */
                if ( empty( $data['class'] ) )
                {
                    $data['class'] = '';
                }
                $data['class'] .= ' input-xlarge';
                if ( isset( $config['theme_' . $config['theme'] . '_' . $name] ) )
                {
                    $data['value'] = $config['theme_' . $config['theme'] . '_' . $name];
                }
                $tpl->block( 'Settingfields', array(
                    'title' => $data['name'],
                    'usage' => '[config_theme_' . $config['theme'] . '_' . $name . ']',
                    'input' => generateField( $data['type'], "themesetting_$name", "themesetting_$name" . rand( 1000, 2000 ), isset( $data['class'] ) ? $data['class'] : '', isset( $data['value'] ) ? $data['value'] : '', isset( $data['options'] ) ? $data['options'] : array()  ),
                ) );
				
                $tpl->assign( 'themeSettings', '' );
            }
			if ( isset( $config['theme_' . $config['theme'] . '_mailtpl'] ) )
            {
                  $data['value'] = $config['theme_' . $config['theme'] . '_mailtpl'];
            }
			$tpl->block( 'Settingfields', array(
                    'title' => 'قالب ارسال ایمیل',
                    'usage' => 'فاقد تگ',
                    'input' => generateField( 'select', "themesetting_mailtpl", "themesetting_mailtpl" . rand( 1000, 2000 ), 'ltr input-xlarge', isset( $data['value'] ) ? $data['value'] : '', get_email_templates()  ),
            ) );
			
        }
    }
}
//end detect theme settings
$tpl->assign( 'site', $config['site'] );
$htpl->showit();
$tpl->showit();
$ftpl->showit();

function generateField( $type, $name, $id, $class, $value, $options = array() )
{
    $out = '---';
    if ( $type == 'input' )
    {
        $type = 'text';
    }
    switch ( $type )
    {

		case 'select-cat':
            $out = "<select name='$name' id='$id' class='$class'>";
            $out .= getSelectCats( 0, '', '', $value );
            $out .= "</select>";
            return $out;
            break;
        case 'select':
            $out = "<select name='$name' id='$id' class='$class'>";
            if ( is_array( $options ) )
            {
                foreach ( $options as $key => $val )
                {
                    if ( $key == $value )
                    {
                        $out .= "<option selected value='$key'>$val</option>";
                    }
                    else
                    {
                        $out .= "<option value='$key'>$val</option>";
                    }
                }
            }
            $out .= "</select>";
            return $out;
            break;
        case 'checkbox':
            $checked = ($value == 1) ? 'checked' : '';
            $out     = "<input type='checkbox' name='$name' id='$id' $checked value='1' class='$class' />";
            return $out;
            break;
        case 'radio':
            return 'radio';
            break;
        case 'image':
            $out     = "<div class='autocomplete-append'>
			    		<ul class='search-options'>
			    			<li><a data-original-title='Settings' href=\"mfm.php?mode=standalone&amp;field=$id\" class='settings-option tip browsefile'></a></li>
			    		</ul>
						 <input name='$name' id='$id' type='text' value='$value' class='$class' />

						 					</div>
			";
            break;
        case 'icon':
            $out     = "<div class='autocomplete-append'>
			    		<ul class='search-options'>
			    			<li><a data-original-title='Settings' href=\"iconmanager.php?field=$id\" class='settings-option tip browseicon'></a></li>
			    		</ul>
						 <input name='$name' id='$id' type='text' value='$value' class='$class' />

						 					</div>
			";
            break;
        case 'textarea':
            $out     = "<textarea name='$name' id='$id' class='$class'>$value</textarea>";
            break;
        default:
            $out     = "<input name='$name' id='$id' type='$type' value='$value' class='$class' />";
            break;
    }
    return $out;
}

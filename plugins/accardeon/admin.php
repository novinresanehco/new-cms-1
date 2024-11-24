<?php

/*
 * Advanced accardeon plugin by Reza Shahrokhian
 * author : Reza Shahrokhian <shahrokhian.com>
 * email : reza@shahrokhian.com
 */

if ( !defined( 'plugins_admin_area' ) OR !isset( $permissions['access_admin_area'] ) OR $permissions['access_admin_area'] != '1' )
{
    die( 'invalid access' );
}
if( !function_exists( 'get_accardeon_list' ) )
{
	function get_accardeon_list()
	{
		global $d;
		$sql = "show tables like 'accardeon_groups'";
		$res = $d->query($sql);
		$options = array();
		if ( $d->getrows( $res ) > 0 )
		{
			$groups = $d->Query( "SELECT * FROM `accardeon_groups`" );
			while( $data = $d->fetch( $groups ) )
			{
				$options[ $data['id'] ] = $data['title'];
			}
		}
		return $options;
	}
}	
if( !function_exists( 'get_accardeon_templates' ) )
{
	function get_accardeon_templates()
	{
		$themesGLob = glob( current_theme_dir . 'plugins' . DS. 'accardeon' . DS . '*.html' );
		$themes     = array();
		foreach ( $themesGLob as $value )
		{
			$value    = str_replace( current_theme_dir. 'plugins' . DS . 'accardeon' . DS , '', $value );
			$value 	  = explode( '\\', $value );
			$value    = $value[(count( $value ) - 1)];
			$value    = str_replace( '.html', '', $value );
			$value    = str_replace( '.htm', '', $value );
			$value    = trim( $value, '/' );
			$value    = trim( $value, '\\' );
			if( ctype_alnum( $value ) )
			{
				$themes[$value] = $value;
			}
		}
		$themesGLob = glob( plugins_dir. DS . 'accardeon' . DS . 'themes' . DS . '*.html' );
		foreach ( $themesGLob as $value )
		{
			$value    = str_replace( plugins_dir. DS . 'accardeon' . DS . 'themes' , '', $value );
			$value 	  = explode( '\\', $value );
			$value    = $value[(count( $value ) - 1)];
			$value    = str_replace( '.html', '', $value );
			$value    = str_replace( '.htm', '', $value );
			$value    = trim( $value, '/' );
			$value    = trim( $value, '\\' );
			if( ctype_alnum( $value ) )
			{
				$themes[$value] = $value;
			}
		}
		//$themes['default'] = 'default';
		return $themes;
	}
}
$information = array(
    'name'        => 'مدیریت آکاردئون ها',
    'provider'    => 'رضا شاهرخيان',
    'providerurl' => 'http://shahrokhian.com',
    'install'     => true,
    'uninstall'   => true,
    'activate'    => true,
    'inactivate'  => true,
	'data'        => array(
        'tid' 	=> array( 'name' => 'آکاردئون', 'value' => 'default', 'type' => 'select', 'options' => get_accardeon_list()  ),
    )
);
$tpl->assign( 'first', '' );

if ( defined( 'methods' ) )
{
    function defaultop()
    {
        global $tpl, $d, $information, $config;
        $ac   = isset( $_GET['ac'] ) ? $_GET['ac'] : '';
        $itpl = new samaneh();
		$itpl->assign( 'site', $config['site'] );
        //accardeon
       
		$id = filter_input( INPUT_GET, 'accardeon', FILTER_VALIDATE_INT );
		$templates = get_accardeon_templates();
		if( is_numeric( $id ) && $id > 0 )
		{
			$group = $d->Query( "SELECT * FROM `accardeon_groups` WHERE `id`='$id'" );
			$group = $d->fetch( $group );
			if( empty( $group['id'] ) )
			{
				die( 'invalid group' );
			}
			//var_dump( $_POST );exit;
			if( isset( $_POST['saveaccardeon'] ) && isset( $_POST['accardeontitle'] )  && isset( $_POST['accardeonIcon'] )  && isset( $_POST['accardeontext'] )  && isset( $_POST['accardeonimage'] )  )
			{
				$d->Query( "DELETE FROM `accardeon` WHERE `gid`='$id'" );
				for( $i = 0, $c = count( $_POST['accardeontitle'] ); $i < $c; $i++ )
				{
					$title 	= $_POST['accardeontitle'][$i];
					$icon 	= $_POST['accardeonIcon'][$i];
					$text 	= $_POST['accardeontext'][$i];
					$image 	= $_POST['accardeonimage'][$i];
					if( !empty( $title ) && !empty( $text ) )
					{
						$d->iQuery( 'accardeon', array( 
						'title' => $title,
						'icon' 	=> $icon,
						'image' 	=> $image,
						'text' 	=> htmlspecialchars_decode( $text ),
						'gid'	=> $id,
					) );
					}
					
				}
				if( !empty( $_POST['eaccardeontitle'] ) && !empty( $_POST['eaccardeontemplate'] ) && isset( $templates[$_POST['eaccardeontemplate']]) )
				{
					$d->uQuery( "accardeon_groups", array(
						'title' => $_POST['eaccardeontitle'],
						'template' => $_POST['eaccardeontemplate'],
					), " `id`='$id'" );
				}
				die( '<div class="alert margin">اطلاعات با موفقیت ذخیره شد.</div>' );
			}
			else if( isset( $_GET['remove'] ) )
			{
				$d->Query( "DELETE FROM `accardeon` WHERE `gid`='$id'" );
				$d->Query( "DELETE FROM `accardeon_groups` WHERE `id`='$id'" );
				Header( "Location: plugins.php?plugin=accardeon" );
				exit('removed');
			}
			
			foreach( $templates as $name )
			{
				$select = '';
				if( $name == $group['template'] )
				{
					$select = 'selected';
				}
				$itpl->block( 'accardeonblock', array( 'name' => $name, 'select' => $select ) );
			}
			$itpl->assign( 'tab', $group['title'] );
			$itpl->assign( 'accardeon_id', $group['id'] );
			$itpl->load( '../plugins/accardeon/admin/accardeon.html' );
			$tab = $d->Query( "SELECT * FROM `accardeon` WHERE `gid`='$id'" );
			while( $data = $d->fetch( $tab ) )
			{
				$itpl->block( 'accardeon', array(
					'rand'	=>	$data['id'],
					'title'	=>	$data['title'],
					'icon'	=>	$data['icon'],
					'text'	=>	$data['text'],
					'image'	=>	$data['image'],
				) );
			}
		}
		else
		{
			
			$itpl->load( '../plugins/accardeon/admin/first.html' );
			foreach( $templates as $name )
			{
				$itpl->block( 'accardeonblock', array( 'name' => $name ) );
			}
			
			if( isset( $_POST['accardeontitle'] ) && !empty( $_POST['tabtemplate'] ) && isset( $templates[$_POST['tabtemplate']] ) )
			{
				$d->iQuery( "accardeon_groups", array( 'title' => mysql_real_escape_string( $_POST['accardeontitle'] ), 'template' => mysql_real_escape_string( $_POST['tabtemplate'] ) ));
			}
		}
		$groups = $d->Query( "SELECT * FROM `accardeon_groups`" );
		while( $data = $d->fetch( $groups ) )
		{
			$tpl->block( 'tabs', array(
				'title' => $data['title'],
				'id'    => 1,
				'url'   => 'plugins.php?plugin=accardeon&accardeon=' . $data['id'],
			) );
		}
        $tpl->assign( 'plugins_name', $information['name'] );
        $tpl->assign( 'first', $itpl->dontshowit() );
    }

    function activateop()
    {
        global $d;
        $d->Query( "UPDATE `plugins` SET `stat`='1' WHERE `name`='accardeon'" );
        print_msg( 'ماژول با موفقيت فعال شد.', 'Success' );
    }

    function installop()
    {
        global $d;
        $q = $d->getrows( "SELECT `stat` FROM `plugins` WHERE `name`='accardeon' LIMIT 1", true );
        if ( $q > 0 )
        {
            print_msg( 'اين ماژول قبلا نصب شده است.', 'Info' );
        }
        else
        {
            global $information;
            $d->Query( "CREATE TABLE IF NOT EXISTS `accardeon` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `gid` int(10) NOT NULL,
  `title` varchar(255) NOT NULL,
  `icon` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `text` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;" );
            $d->Query( "CREATE TABLE IF NOT EXISTS `accardeon_groups` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `template` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;" );
            $oid = $d->getmax( 'oid', 'menus' );
            $oid++;
            $d->Query( "INSERT INTO `plugins` SET `name`='accardeon',`title`='$information[name]',`stat`='0',`sortable`=1" );
            $d->Query( "INSERT INTO `menus` SET `oid`='$oid',`name`='accardeon',`title`='$information[name]',`url`='plugins.php?plugin=accardeon',`type`='1'" );
            print_msg( 'ماژول با موفقيت نصب شد.', 'Success' );
            activateop();
        }
	}

        function inactivateop()
        {
            global $d;
            $d->Query( "UPDATE `plugins` SET `stat`='0' WHERE `name`='accardeon'" );
            print_msg( 'ماژول با موفقيت غير فعال شد.', 'Success' );
        }

        function uninstallop()
        {
            global $d;
            $q = $d->getrows( "SELECT `stat` FROM `plugins` WHERE `name`='accardeon' ", true );
            if ( $q <= 0 )
            {
                print_msg( 'اين ماژول نصب نشده است يا استاندارد نيست.', 'Info' );
            }
            else
            {
                $q = $d->Query( "DELETE FROM `plugins` WHERE `name`='accardeon' " );
                $q = $d->Query( "DELETE FROM `menus` WHERE `name`='accardeon' " );
                $d->Query( 'DROP TABLE IF EXISTS `accardeon`' );
                $d->Query( 'DROP TABLE IF EXISTS `accardeon_groups`' );
                print_msg( 'ماژول با موفقيت حذف شد.', 'Success' );
            }
        }

    

    function print_msg( $msg, $type )
    {
        global $tpl, $information;
        $tpl->assign( array(
            'plugin_name' => $information['name'],
            $type         => 1,
            'msg'         => $msg,
        ) );
    }
}
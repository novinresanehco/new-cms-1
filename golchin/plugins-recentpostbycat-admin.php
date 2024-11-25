<?php

if ( !defined( 'plugins_admin_area' ) OR !isset( $permissions['access_admin_area'] ) OR $permissions['access_admin_area'] != '1' )
    die( 'invalid access' );
if( !function_exists( 'get_recentpostbycat_templates' ) )
{
	function get_recentpostbycat_templates()
	{
		$themesGLob = glob( current_theme_dir . 'plugins//recentpostbycat//' . '*.htm*' );
		$themes     = array();
		foreach ( $themesGLob as $value )
		{
			$value    = str_replace( current_theme_dir. 'plugins//recentpostbycat//' , '', $value );
			$value 	  = explode( '\\', $value );
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
$information = array(
    'name'        => 'آخرین ارسال های دسته بندی',
    'provider'    => 'رضا شاهرخيان',
    'providerurl' => 'http://rashcms.com',
    'install'     => true,
    'uninstall'   => true,
    'activate'    => true,
    'inactivate'  => true,
    'data'        => array( //لیست پارامترهای اختصاصی ماژول
        'title'      => array( 'name' => 'عنوان', 'value' => 'آخرین ارسال های دسته بندی' ),
        'count'      => array( 'name' => 'تعداد', 'value' => '10', 'type' => 'number' ),
        'categories' => array( 'name' => 'دسته', 'value' => '', 'type' => 'select', 'options' => getSelectCats( 0, '', '', false, true ) ),
		'template' => array( 'name' => 'قالب', 'value' => 'default', 'type' => 'select', 'options' => get_recentpostbycat_templates()  ),
        'icon'       => array( 'name' => 'آیکن', 'value' => '', 'type' => 'icon' ),
    )
);
$tpl->assign( 'first', '' );
if ( defined( 'methods' ) )
{

    function defaultop()
    {
        print_msg( 'اين ماژول شامل تنظيمات خاصي نمي باشد.', 'Info' );
    }

    function inactivateop()
    {
        global $d;
        $d->Query( "UPDATE `plugins` SET `stat`='0' WHERE `name`='recentpostbycat' LIMIT 1" );
        print_msg( 'ماژول با موفقيت غير فعال شد.', 'Success' );
    }

    function activateop()
    {
        global $d;
        $d->Query( "UPDATE `plugins` SET `stat`='1' WHERE `name`='recentpostbycat' LIMIT 1" );
        print_msg( 'ماژول با موفقيت فعال شد.', 'Success' );
    }

    function installop()
    {
        global $d;
        $q = $d->getrows( "SELECT `stat` FROM `plugins` WHERE `name`='recentpostbycat' LIMIT 1", true );
        if ( $q > 0 )
            print_msg( 'اين ماژول قبلا نصب شده است.', 'Info' );
        else
        {
            global $information;
            $options          = array();
            $options['theme'] = true;
            $options          = base64_encode( serialize( $options ) );
            $q                = $d->Query( "INSERT INTO `plugins` SET `name`='recentpostbycat',`title`='$information[name]',`stat`='0',`options`='$options'" );
            print_msg( 'ماژول با موفقيت نصب شد.', 'Success' );
            activateop();
        }
    }

    function uninstallop()
    {
        global $d;
        $q = $d->getrows( "SELECT `stat` FROM `plugins` WHERE `name`='recentpostbycat' LIMIT 1", true );
        if ( $q <= 0 )
            print_msg( 'اين ماژول نصب نشده است يا استاندارد نيست.', 'Info' );
        else
        {
            $q = $d->Query( "DELETE FROM `plugins` WHERE `name`='recentpostbycat' LIMIT 1" );
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
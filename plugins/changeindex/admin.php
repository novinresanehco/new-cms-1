<?php

if ( !defined( 'plugins_admin_area' ) )
    die( 'invalid access' );
if ( !isset( $permissions['access_admin_area'] ) )
    die( 'invalid access' );
if ( $permissions['access_admin_area'] != '1' )
    die( 'invalid access' );
$information = array(
    'name'        => 'تغییر صفحه اصلی',
    'provider'    => 'رضا شاهرخيان',
    'providerurl' => 'http://shahrokhian.com',
    'install'     => true,
    'uninstall'   => true,
    'activate'    => true,
    'inactivate'  => true,
    'data'        => array(
        'icon' => array( 'name' => 'آیکن', 'value' => '', 'type' => 'icon' ),
    )
);
$tpl->assign( 'first', '' );
$tpl->assign( 'plugin_name', $information['name'] );
if ( defined( 'methods' ) )
{

    function defaultop()
    {
        global $tpl, $config, $d, $lang;
        $itpl     = new samaneh();
        $itpl->load( plugins_dir . 'changeindex' . DS . 'first.htm' );
        $affected = false;
        if ( isset( $_POST['pagetheme'] ) )
        {
            if ( strpos( $_POST['pagetheme'], '..' ) === false && strpos( $_POST['pagetheme'], '/' ) === false && strpos( $_POST['pagetheme'], '\\' ) === false && is_file( current_theme_dir . $_POST['pagetheme'] ) && ( get_ext( $_POST['pagetheme'] ) == 'htm' OR get_ext( $_POST['pagetheme'] ) == 'html') )
            {
                saveconfig( 'mainpagetheme', $_POST['pagetheme'] );
                $config['mainpagetheme'] = $_POST['pagetheme'];
                if ( isset( $_POST['postid'] ) && is_numeric( $_POST['postid'] ) && $_POST['postid'] >= 0 )
                {
                    saveconfig( 'mainpagepost', $_POST['postid'] );
                    $config['mainpagepost'] = $_POST['postid'];
                }
                $tpl->block( 'Success', array( 'msg' => $lang['ok'] ) );
            }
            else
            {
                $tpl->block( 'Error', array( 'msg' => $lang['error'] ) );
            }
        }
        $handle     = opendir( current_theme_dir );
        $themesfile = '';
        while ( false !== ($file       = readdir( $handle )) )
        {
            if ( !empty( $file ) && $file != '.' && $file != '..' && is_file( current_theme_dir . $file ) && ( get_ext( $file ) == 'htm' OR get_ext( $file ) == 'html') )
            {
                $data = array( 'name' => $file, 'selected' => '' );
                if ( $file == @$config['mainpagetheme'] )
                {
                    $data['selected'] = 'selected';
                }
                $itpl->block( 'themesfile', $data );
            }
        }
        closedir( $handle );
        $themesfile = explode( "{samaneh_/?.com}", $themesfile );
        sort( $themesfile );
        for ( $i = 0; $i < sizeof( $themesfile ); $i++ )
        {
            if ( !empty( $themesfile[$i] ) )
            {
                $itpl->block( 'themesfile', array(
                    'name' => $themesfile[$i],
                ) );
            }
        }
        $tpldata             = array(
            'postid' => 0,
            'title'  => $lang['none'],
        );
        $tpldata['selected'] = '';
        if ( empty( $config['mainpagepost'] ) OR $config['mainpagepost'] == 0 )
        {
            $tpldata['selected'] = 'selected';
        }
        $itpl->block( 'posts', $tpldata );
        $ctimestamp = time();
        $posts      = $d->Query( "select `id`,`title` FROM `data` WHERE `date` <= '$ctimestamp' AND (`expire`='0' OR `expire`='' OR `expire`>'$ctimestamp') AND (`show`!='4' || `show`='2')  order by `id` desc" );
        while ( $data       = $d->fetch( $posts ) )
        {
            $tpldata             = array(
                'postid' => $data['id'],
                'title'  => $data['title'],
            );
            $tpldata['selected'] = '';
            if ( @$config['mainpagepost'] == $data['id'] )
            {
                $tpldata['selected'] = 'selected';
            }
            $itpl->block( 'posts', $tpldata );
        }

        $tpl->assign( 'first', $itpl->dontshowit() );
    }

    function inactivateop()
    {
        global $d;
        $d->Query( "UPDATE `plugins` SET `stat`='0' WHERE `name`='changeindex' LIMIT 1" );
        print_msg( 'ماژول با موفقيت غير فعال شد.', 'Success' );
    }

    function activateop()
    {
        global $d;
        $d->Query( "UPDATE `plugins` SET `stat`='1' WHERE `name`='changeindex' LIMIT 1" );
        print_msg( 'ماژول با موفقيت فعال شد.', 'Success' );
    }

    function installop()
    {
        global $d, $info;
        $q = $d->getrows( "SELECT `stat` FROM `plugins` WHERE `name`='changeindex' LIMIT 1", true );
        if ( $q > 0 )
        {
            print_msg( 'اين ماژول قبلا نصب شده است.', 'Info' );
        }
        else
        {
            global $information;
            $oid = $d->getmax( 'oid', 'menus' );
            $oid++;
            $d->Query( "ALTER TABLE `permissions` ADD `changeindex` INT( 1 ) NOT NULL DEFAULT '0'" );
            $q   = $d->Query( "UPDATE `permissions` SET `changeindex`='1' WHERE `u_id`='$info[u_id]'" );
            $q   = $d->Query( "INSERT INTO `menus` SET `oid`='$oid',`name`='changeindex',`title`='$information[name]',`url`='plugins.php?plugin=changeindex',`type`='1'" );
            $q   = $d->Query( "INSERT INTO `plugins` SET `name`='changeindex',`title`='$information[name]',`stat`='0'" );
            print_msg( 'ماژول با موفقيت نصب شد.', 'Success' );
        }
    }

    function uninstallop()
    {
        global $d;
        $q = $d->getrows( "SELECT `stat` FROM `plugins` WHERE `name`='changeindex' LIMIT 1", true );
        if ( $q <= 0 )
            print_msg( 'اين ماژول نصب نشده است يا استاندارد نيست.', 'Info' );
        else
        {
            $d->Query( "ALTER TABLE `permissions` DROP `changeindex`" );
            $d->Query( "DELETE FROM `menus` WHERE `name`='changeindex' LIMIT 1" );
            $d->Query( "DELETE FROM `plugins` WHERE `name`='changeindex' LIMIT 1" );
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
?>
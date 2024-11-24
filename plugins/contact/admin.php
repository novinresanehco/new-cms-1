<?php

if ( !defined( 'plugins_admin_area' ) )
    die( 'invalid access' );
if ( !isset( $permissions['access_admin_area'] ) )
    die( 'invalid access' );
if ( $permissions['access_admin_area'] != '1' )
    die( 'invalid access' );
$information = array(
    'name'        => 'تماس با ما',
    'provider'    => 'رضا شاهرخيان',
    'providerurl' => 'http://samaneh.com',
    'install'     => false,
    'uninstall'   => false,
    'activate'    => true,
    'inactivate'  => true,
    'data'        => array(
        'icon' => array( 'name' => 'آیکن', 'value' => 'dashboard', 'type' => 'icon' ),
        'title'      => array( 'name' => 'عنوان', 'value' => 'تماس با ما' ),
    )
);

$tpl->assign( 'first', '' );
if ( defined( 'methods' ) )
{

    function defaultop()
    {
        global $config, $tpl, $lang, $htpl, $d;
        $user     = new user();
        $info     = $user->info();
        $itpl     = new samaneh();
        $inboxtpl = new samaneh();
        $itpl->load( plugins_dir . 'contact' . DS . 'first.html' );
        $inboxtpl->load( plugins_dir . 'contact' . DS . 'inbox.html' );
        if ( isset( $_GET['id'] ) && is_numeric( $_GET['id'] ) && isset( $_GET['delete'] ) )
        {
            $id = intval( $_GET['id'] );
            $d->Query( "DELETE FROM `contact` WHERE `u_id`='$info[u_id]' AND `id`='$id' LIMIT 1" );
            if ( $d->affected() > 0 )
            {
                $tpl->block( 'Success', array( 'msg' => $lang['ok'] ) );
            }
        }
        if ( isset( $_POST['id'] ) && is_numeric( $_POST['id'] ) )
        {
            $id      = intval( $_POST['id'] );
            $contact = $d->Query( "SELECT * FROM `contact` WHERE `u_id`='$info[u_id]' AND `id`='$id' LIMIT 1" );
            if ( count( $contact ) !== 1 )
            {
                die( 'error' );
            }
            else
            {
                $contact = $d->fetch( $contact );
                echo $contact['name'] . "<samaneh/>" . $contact['email'] . "<samaneh/>" . $contact['tell'] . "<samaneh/>" . $contact['site'] . "<samaneh/>" . $contact['text'];
                exit;
            }
        }

        $tabs = array( 'تنظیمات' );

        if ( isset( $_POST['text'] ) )
        {
            saveconfig( 'contacttxt', $_POST['text'] );
            $config['contacttxt'] = stripcslashes( $_POST['text'] );
        }

        $contacts = $d->Query( "SELECT * FROM `contact` WHERE `u_id`='$info[u_id]' ORDER BY `id` DESC" );
        while ( $data     = $d->fetch( $contacts ) )
        {
            $inboxtpl->block( "listtr", array(
                'name'  => $data['name'],
                'email' => $data['email'],
                'id'    => $data['id'],
            ) );
        }
        $itpl->assign( 'contacttxt', @$config['contacttxt'] );
        $tpl->assign( 'first', $inboxtpl->dontshowit() );
        for ( $i = 0, $c = count( $tabs ); $i < $c; $i++ )
        {
            $tpl->block( 'tabs', array( 'title' => $tabs[$i], 'id' => $i + 2, 'current' => '', 'url' => '#tab' . ( $i + 2 ) ) );
            $tpl->block( "extra_div", array( 'id' => $i + 2, 'inside' => $itpl->dontshowit() ) );
        }
    }

    function inactivateop()
    {
        global $d;
        $d->Query( "UPDATE `plugins` SET `stat`='0' WHERE `name`='contact' LIMIT 1" );
        $d->Query( "DELETE FROM `menus` WHERE `name`='contact' LIMIT 1" );
        print_msg( 'ماژول با موفقيت غير فعال شد.', 'Success' );
    }

    function activateop()
    {
        global $d, $information;
        $d->Query( "UPDATE `plugins` SET `stat`='1' WHERE `name`='contact' LIMIT 1" );
        $oid = $d->getmax( 'oid', 'menus' );
        $oid++;
        $q   = $d->Query( "INSERT INTO `menus` SET `oid`='$oid',`name`='contact',`title`='$information[name]',`url`='plugins.php?plugin=contact',`type`='1'" );
        print_msg( 'ماژول با موفقيت فعال شد.', 'Success' );
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
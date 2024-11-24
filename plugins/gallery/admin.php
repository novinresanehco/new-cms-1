<?php

if ( !defined( 'plugins_admin_area' ) )
    die( 'invalid access' );
if ( !isset( $permissions['access_admin_area'] ) )
    die( 'invalid access' );
if ( $permissions['access_admin_area'] != '1' )
    die( 'invalid access' );
$information = array(
    'name'        => 'ماژول گالری تصاویر',
    'provider'    => 'گروه طراحی نت ساز',
    'providerurl' => 'http://www.netsaz.ir',
    'version'     => '3.0.0 final',
    'install'     => true,
    'uninstall'   => true,
    'activate'    => true,
    'inactivate'  => true,
    'data'        => array(
        'icon' => array( 'name' => 'آیکن', 'value' => '', 'type' => 'icon' ),
    )
);

define( 'gmod_version', $information['version'] );

$tpl->assign( 'first', '' );
if ( defined( 'methods' ) )
{

    function getSelectGalleryCats( $parent = 0, $join = '', $id = '', $selected_id = false )
    {
        global $d, $colors;
        $menu_data = $d->Query( "SELECT * FROM `gallery_cat` WHERE sub = '$parent' ORDER BY `sub`,`id` ASC" );
        $out       = '';
        if ( $d->GetRows( $menu_data ) > 0 )
        {
            $p_sub = ( int ) $d->GetRowValue( "sub", "SELECT `sub` FROM `gallery_cat` WHERE `id`='$parent' LIMIT 1", true );
            $join .= '---';
            while ( $menu  = $d->fetch( $menu_data ) )
            {
                //$font = (strlen($join) == 3) ? 'bold' : 'normal';
                $font  = '';
                if ( $p_sub == $parent )
                    $join  = substr( $join, 0, -3 );
                $color = (isset( $colors[floor( strlen( $join ) / 3 )] )) ? $colors[floor( strlen( $join ) / 3 )] : 'black';
                if ( $menu['sub'] == 0 )
                {
                    $catMainId = 0;
                    $font      = 'bold';
                }
                else
                {
                    $catMainId = $menu['id'];
                    $font      = 'notmal';
                }
                $vid      = (!empty( $id )) ? "id='" . $id . $menu['id'] . "'" : '';
                $selected = '';
                if ( $selected_id !== false && $selected_id == $menu['id'] )
                    $selected = " selected ";
                $out .= "<option style='font-weight:$font' value='$menu[id]' $selected $vid >" . $join . ' ' . $menu['title'] . "</option>";
                $out .= getSelectGalleryCats( $menu['id'], $join, $id, $selected_id );
            }
        }
        return $out;
    }

    function defaultop()
    {
        global $tpl, $d, $information;
        $itpl = new samaneh();
        $itpl->load( '../plugins/gallery/admin-theme.html' );

        $itpl->assign( 'gmod_version', gmod_version );

        /*
          $q = $d->Query( "SELECT * FROM `gallery_cat` WHERE `sub`='0'" );

          while ( $f = $d->fetch( $q ) )
          {
          $itpl->block( 'cats', array( 'id' => $f['id'], 'title' => $f['title'] ) );

          $q2 = $d->Query( "SELECT * FROM `gallery_cat` WHERE `sub`='$f[id]'" );
          while ( $f2 = $d->fetch( $q2 ) )
          {
          $itpl->block( 'cats', array( 'id' => $f2['id'], 'title' => $f['title'] . ' --> ' . $f2['title'] ) );
          }
          }
         */
        $itpl->assign( 'cats', getSelectGalleryCats() );
        $q = $d->Query( "SELECT * FROM `gallery_config` LIMIT 1" );
        $f = $d->fetch( $q );

        $itpl->assign( array(
            'num_rows'    => $f['numrows'],
            'num_columns' => $f['numcolumns'],
        ) );
        $tabs = array(
            'افزودن',
            'مدیریت تصاویر',
            'ویرایش',
            'افزودن دسته',
            'مدیریت دسته',
            'ویرایش دسته',
            'تنظیمات'
        );
        $tpl->resetBlock( 'tabs' );
        foreach ( $tabs as $id => $tab )
        {
            $tpl->block( 'tabs', array( 'current' => '', 'id' => $id + 2, 'title' => $tab, 'url' => '#tab' . ($id + 2) ) );
        }
        $tpl->assign( 'plugins_name', $information['name'] );
        $tpl->assign( 'first', $itpl->dontshowit() );
    }

    function inactivateop()
    {
        global $d;
        $d->Query( "UPDATE `plugins` SET `stat`='0' WHERE `name`='gallery' LIMIT 1" );
        print_msg( 'ماژول با موفقيت غير فعال شد.', 'Success' );
    }

    function activateop()
    {
        global $d;
        $d->Query( "UPDATE `plugins` SET `stat`='1' WHERE `name`='gallery' LIMIT 1" );
        print_msg( 'ماژول با موفقيت فعال شد.', 'Success' );
    }

    function installop()
    {
        global $d;
        $q = $d->getrows( "SELECT `stat` FROM `plugins` WHERE `name`='gallery' LIMIT 1", true );
        if ( $q > 0 )
            print_msg( 'اين ماژول قبلا نصب شده است.', 'Info' );
        else
        {
            global $information;
            $q = $d->Query( "INSERT INTO `plugins` SET `name`='gallery',`title`='$information[name]',`stat`='9'" );
            $q = $d->Query( 'CREATE TABLE `gallery_cat` (
						`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
						`title` VARCHAR( 100 ) NOT NULL ,
						`text` VARCHAR( 1000 ) NOT NULL ,
						`img` VARCHAR( 1000 ) NOT NULL ,
						`users` INT NOT NULL ,
						`sub` INT NOT NULL ,
						`star` INT( 1 ) NOT NULL ,
						`tvote` INT ( 10 ) NOT NULL ,
						`ajax` TINYINT ( 1 ) NOT NULL ,
						`nov` INT ( 5 ) NOT NULL
					) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci;' );

            $q = $d->Query( 'CREATE TABLE `gallery_images` (
						`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
						`title` VARCHAR( 100 ) NOT NULL ,
						`text` VARCHAR( 1000 ) NOT NULL ,
						`img` VARCHAR( 1000 ) NOT NULL ,
						`thumb` VARCHAR( 1000 ) NOT NULL ,
						`users` INT NOT NULL ,
						`cat` INT NOT NULL ,
						`hits` INT NOT NULL ,
						`star` INT( 1 ) NOT NULL ,
						`tvote` INT ( 10 ) NOT NULL ,
						`nov` INT ( 5 ) NOT NULL
					) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci;' );


            $q = $d->Query( 'CREATE TABLE `gallery_config` (
						`numcolumns` INT NOT NULL ,
						`numrows` INT NOT NULL
					) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci;' );

            $q = $d->Query( "INSERT INTO `gallery_config` (`numcolumns`, `numrows`) VALUES ('4', '10')" );




            $oid = $d->getmax( 'oid', 'menus' );
            $q   = $d->Query( "INSERT INTO `menus` SET `oid`='$oid',`name`='gallery',`title`='$information[name]',`url`='plugins.php?plugin=gallery',`type`='0'" );

            print_msg( 'ماژول با موفقيت نصب شد.', 'Success' );
        }
    }

    function uninstallop()
    {
        global $d;
        $q = $d->getrows( "SELECT `stat` FROM `plugins` WHERE `name`='gallery' LIMIT 1", true );
        if ( $q <= 0 )
            print_msg( 'اين ماژول نصب نشده است يا استاندارد نيست.', 'Info' );
        else
        {
            $d->Query( "DELETE FROM `menus` WHERE `name`='gallery' LIMIT 1" );
            $q = $d->Query( "DELETE FROM `plugins` WHERE `name`='gallery' LIMIT 1" );
            $q = $d->Query( "DROP TABLE `gallery_cat`, `gallery_images`, `gallery_config`;" );
            print_msg( 'ماژول با موفقيت حذف شد.', 'Success' );
        }
    }

    function print_msg( $msg, $type )
    {
        global $tpl, $information;
        $tpl->assign( array(
            'plugins_name' => $information['name'],
            $type          => 1,
            'msg'          => $msg,
        ) );
    }

}
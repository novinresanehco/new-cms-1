<?php

if ( !defined( 'plugins_admin_area' ) )
    die( 'invalid access' );
if ( !isset( $permissions['access_admin_area'] ) )
    die( 'invalid access' );
if ( $permissions['access_admin_area'] != '1' )
    die( 'invalid access' );
$information = array(
    'name'        => 'اسلایدر',
    'provider'    => 'رضا شاهرخيان',
    'providerurl' => 'http://shahrokhian.com',
    'install'     => true,
    'uninstall'   => true,
    'activate'    => true,
    'inactivate'  => true,
    'data'        => array(
        'title' => array( 'name' => 'عنوان', 'value' => 'اسلایدر' ),
        'icon'  => array( 'name' => 'آیکن', 'value' => '', 'type' => 'icon' ),
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
        $itpl->load( plugins_dir . 'slider' . DS . 'first.htm' );
        $itpl->assign( array(
            'thnwidth'           => ( int ) @$config['slider_thn_width'],
            'thnheight'          => ( int ) @$config['slider_thn_height'],
            'slider_group_style' => @$config['slider_group_style'],
            'moduleurl'          => $config['site'] . 'panel/plugins.php?plugin=slider',
            'pluginurl'          => $config['site'] . 'plugins/slider/',
        ) );
        $response = array();
        if ( isset( $_GET['do'] ) )
        {
            switch ( $_GET['do'] )
            {
                case 'update':
                    saveconfig( 'slider_thn_width', ( int ) @$_POST['width'] );
                    saveconfig( 'slider_thn_height', ( int ) @$_POST['height'] );
                    saveconfig( 'slider_group_style', @$_POST['style'] );
                    $response            = array();
                    $response['success'] = true;
                    header( 'Content-type: application/json' );
                    echo json_encode( $response );
                    exit;
                    break;
                case 'sort':
                    if ( !isset( $_POST['posdata'] ) OR !is_array( $_POST['posdata'] ) )
                        exit;
                    $_POST['posdata']    = array_reverse( $_POST['posdata'] );
                    $oid                 = 0;
                    foreach ( $_POST['posdata'] as $id )
                    {
                        if ( is_numeric( $id ) )
                        {
                            $oid++;
                            $d->Query( "UPDATE `mp_slider` SET `oid`='$oid' WHERE `id`='$id' LIMIT 1" );
                        }
                    }
                    $response            = array();
                    $response['success'] = true;
                    header( 'Content-type: application/json' );
                    echo json_encode( $response );
                    exit;
                    break;
                case 'delete':
                    if ( !isset( $_POST['id'] ) OR !is_numeric( $_POST['id'] ) )
                        exit;
                    $response            = array();
                    $response['success'] = true;
                    $d->Query( "DELETE FROM `mp_slider` WHERE `id`='$_POST[id]' LIMIT 1" );
                    header( 'Content-type: application/json' );
                    echo json_encode( $response );
                    exit;
                    break;
                case 'insert':
                    if ( empty( $_POST['url'] ) )
                    {
                        $response            = array();
                        $response['success'] = false;
                        $response['error']   = 'آدرس تصویر به درستی وارد نشده است.';
                        header( 'Content-type: application/json' );
                        echo json_encode( $response );
                        exit;
                    }
                    $ext = get_ext( $_POST['url'] );
                    if ( !in_array( $ext, array( 'gif', 'jpeg', 'png', 'jpg' ) ) )
                    {
                        $response            = array();
                        $response['success'] = false;
                        $response['error']   = 'فرمت تصویر نامعتبر است.';
                        header( 'Content-type: application/json' );
                        echo json_encode( $response );
                        exit;
                    }
                    if ( isset( $_POST['thumbnailckbx'] ) )
                    {
                        //auto generate thumb from image
                        require_once(plugins_dir . 'slider/ThumbnailGenerator.class.php');
                        if ( !( isset( $config['slider_thn_width'] ) ) || !is_numeric( $config['slider_thn_width'] ) OR !(isset( $config['slider_thn_height'] )) || !is_numeric( $config['slider_thn_height'] ) )
                        {
                            $response            = array();
                            $response['success'] = false;
                            $response['error']   = 'گالری بدرستی تنظیم نشده است.';
                            header( 'Content-type: application/json' );
                            echo json_encode( $response );
                            exit;
                        }
                        $thumbs = files_dir . 'thumbs\\';
                        $file   = file_get_contents( $_POST['url'] );
                        if ( !$file )
                        {
                            $response            = array();
                            $response['success'] = false;
                            $response['error']   = 'امکان بارگزاری تصویر وجود ندارد.';
                            header( 'Content-type: application/json' );
                            echo json_encode( $response );
                            exit;
                        }
                        $tmpname            = '.' . rand( 5000, 1000 ) . time() . rand( 9000, 15000 );
                        $handle             = fopen( tmp_dir . $tmpname, 'w+' );
                        fwrite( $handle, $file );
                        unset( $file );
                        fclose( $handle );
                        $tg                 = new ThumbnailGenerator( tmp_dir . $tmpname, $config['slider_thn_width'], $config['slider_thn_height'], $thumbs, $ext );
                        $thumb              = $tg->showimage();
                        $thumb              = explode( '\\', $thumb );
                        $thumb              = $thumb[count( $thumb ) - 1];
                        unlink( tmp_dir . $tmpname );
                        $_POST['thumbnail'] = $config['site'] . 'files/thumbs/' . $thumb;
                    }
                    $oid                     = $d->getMax( 'oid', 'mp_slider' );
                    $d->iQuery( 'mp_slider', array(
                        'oid'         => ($oid + 1),
                        'title'       => @$_POST['title'],
                        'link'        => @$_POST['link'],
                        'description' => @$_POST['description'],
                        'url'         => @$_POST['url'],
                        'thumbnail'   => @$_POST['thumbnail'],
                    ) );
                    $response                = array();
                    $response['success']     = true;
                    $response['id']          = $d->last();
                    $response['url']         = $_POST['url'];
                    $response['title']       = @$_POST['title'];
                    $response['description'] = @$_POST['description'];
                    $response['thumbnail']   = empty( $_POST['thumbnail'] ) ? $_POST['url'] : $_POST['thumbnail'];
                    header( 'Content-type: application/json' );
                    echo json_encode( $response );
                    exit;
                    break;
                case 'edit':
                    if ( empty( $_POST['editid'] ) OR !is_numeric( $_POST['editid'] ) )
                    {
                        $response            = array();
                        $response['success'] = false;
                        $response['error']   = 'شناسه ویرایش معتبر نیست.';
                        header( 'Content-type: application/json' );
                        echo json_encode( $response );
                        exit;
                    }
                    if ( empty( $_POST['editurl'] ) )
                    {
                        $response            = array();
                        $response['success'] = false;
                        $response['error']   = 'آدرس تصویر به درستی وارد نشده است.';
                        header( 'Content-type: application/json' );
                        echo json_encode( $response );
                        exit;
                    }
                    $ext = get_ext( $_POST['editurl'] );
                    if ( !in_array( $ext, array( 'gif', 'jpeg', 'png', 'jpg' ) ) )
                    {
                        $response            = array();
                        $response['success'] = false;
                        $response['error']   = 'فرمت تصویر نامعتبر است.';
                        header( 'Content-type: application/json' );
                        echo json_encode( $response );
                        exit;
                    }
                    if ( isset( $_POST['editthumbnailckbx'] ) )
                    {
                        //auto generate thumb from image
                        require_once(plugins_dir . 'slider/ThumbnailGenerator.class.php');
                        if ( !( isset( $config['slider_thn_width'] ) ) || !is_numeric( $config['slider_thn_width'] ) OR !(isset( $config['slider_thn_height'] )) || !is_numeric( $config['slider_thn_height'] ) )
                        {
                            $response            = array();
                            $response['success'] = false;
                            $response['error']   = 'گالری بدرستی تنظیم نشده است.';
                            header( 'Content-type: application/json' );
                            echo json_encode( $response );
                            exit;
                        }
                        $thumbs = files_dir . 'thumbs/';
                        $file   = file_get_contents( $_POST['editurl'] );
                        if ( !$file )
                        {
                            $response            = array();
                            $response['success'] = false;
                            $response['error']   = 'امکان بارگزاری تصویر وجود ندارد.';
                            header( 'Content-type: application/json' );
                            echo json_encode( $response );
                            exit;
                        }
                        $tmpname                = '.' . rand( 5000, 1000 ) . time() . rand( 9000, 15000 );
                        $handle                 = fopen( tmp_dir . $tmpname, 'w+' );
                        fwrite( $handle, $file );
                        unset( $file );
                        fclose( $handle );
                        $tg                     = new ThumbnailGenerator( tmp_dir . $tmpname, $config['slider_thn_width'], $config['slider_thn_height'], $thumbs, $ext );
                        $thumb                  = $tg->showimage();
                        $thumb                  = explode( '\\', $thumb );
                        $thumb                  = $thumb[count( $thumb ) - 1];
                        unlink( tmp_dir . $tmpname );
                        $_POST['editthumbnail'] = $config['site'] . 'files/thumbs/' . $thumb;
                    }
                    $d->uQuery( 'mp_slider', array(
                        'title'       => @$_POST['edittitle'],
                        'link'        => @$_POST['editlink'],
                        'description' => @$_POST['editdescription'],
                        'url'         => @$_POST['editurl'],
                        'thumbnail'   => @$_POST['editthumbnail'],
                            ), " `id`='$_POST[editid]' LIMIT 1" );
                    $response                = array();
                    $response['success']     = true;
                    $response['id']          = $_POST['editid'];
                    $response['link']        = @$_POST['editlink'];
                    $response['title']       = @$_POST['edittitle'];
                    $response['description'] = @$_POST['editdescription'];
                    $response['url']         = $_POST['editurl'];
                    $response['thumbnail']   = empty( $_POST['editthumbnail'] ) ? $_POST['editurl'] : $_POST['editthumbnail'];
                    header( 'Content-type: application/json' );
                    echo json_encode( $response );
                    exit;
                    break;
                default:
                    # code...
                    break;
            }
        }
        $images = $d->Query( "SELECT * FROM `mp_slider` ORDER BY `oid` DESC" );
        while ( $image  = $d->fetch( $images ) )
        {
            $itpl->block( 'images', array(
                'id'          => $image['id'],
                'title'       => $image['title'],
                'link'        => $image['link'],
                'description' => $image['description'],
                'url'         => $image['url'],
                'thumbnail'   => empty( $image['thumbnail'] ) ? $image['url'] : $image['thumbnail'],
            ) );
        }
        require_once(plugins_dir . 'slider' . DS . 'styles.config.php');
        if ( count( $mpstyle ) > 1 )
        {
            $slidertypes                = array();
            $persian                    = array( 'ض', 'ص', 'ث', 'ق', 'ف', 'غ', 'ع', 'ه', 'خ', 'ح', 'ج', 'ج', 'چ', 'پ', 'ش', 'س', 'ی', 'ب', 'ل', 'ا', 'ت', 'ن', 'م', 'ک', 'گ', 'ظ', 'ط', 'ز', 'ر', 'ذ', 'د', 'ئ', 'و' );
            $english                    = array( 'q', 'w', 'e', 'r', 't', 'u', 'i', 'o', 'p', 'pp', 'ppp', 'pppp', 'a', 's', 'd', 'f', 'g', 'h', 'j', 'k', 'l', 'l', 'l', 'z', 'x', 'c', 'v', 'b', 'n', 'm', 'mm' );
            $selectType                 = '';
            $data                       = array();
            $data['slider_group_style'] = isset( $config['slider_group_style'] ) ? $config['slider_group_style'] : '';
            foreach ( $mpstyle as $nsstyleid => $nsstylec )
            {
                if ( $data['slider_group_style'] == $nsstyleid )
                {
                    $type       = str_ireplace( $persian, $english, $nsstylec['type'] );
                    $type       = preg_replace( "/[^a-zA-Z0-9\-]/", "_", $type );
                    $selectType = $type;
                }
            }
            foreach ( $mpstyle as $nsstyleid => $nsstylec )
            {
                $type = str_ireplace( $persian, $english, $nsstylec['type'] );
                $type = preg_replace( "/[^a-zA-Z0-9\-]/", "_", $type );
                if ( !in_array( $nsstylec['type'], $slidertypes ) )
                {
                    $slidertypes[] = $nsstylec['type'];
                    $selected      = '';
                    $itpl->block( 'themetypes', array(
                        'title'    => $nsstylec['type'],
                        'selected' => $selected,
                        'checked'  => ($selectType == $type) ? 'checked' : '',
                        'value'    => $type,
                    ) );
                }
                $itpl->block( 'sliderscreenshots', array(
                    'type'    => $type,
                    'display' => ($selectType == $type) ? 'block' : 'none',
                    'style'   => $nsstyleid,
                    'border'  => ($data['slider_group_style'] == $nsstyleid) ? "border:2px red solid;" : "",
                    'image'   => $config['site'] . 'plugins/slider/screenshots/' . $nsstylec['image'],
                ) );
                $itpl->assign( 'selectedclass', $data['slider_group_style'] );
                //
                //$itpl -> block('sliderstyles', array('value' => $nsstyleid, 'title' => $nsstylec['title'], 'selected' => ($data['slider_group_style'] == $nsstyleid ? 'selected="selected"' : '' )));
            }
        }
        $tpl->assign( 'first', $itpl->dontshowit() );
    }

    function inactivateop()
    {
        global $d;
        $d->Query( "UPDATE `plugins` SET `stat`='0' WHERE `name`='slider' LIMIT 1" );
        print_msg( 'ماژول با موفقيت غير فعال شد.', 'Success' );
    }

    function activateop()
    {
        global $d;
        $d->Query( "UPDATE `plugins` SET `stat`='1' WHERE `name`='slider' LIMIT 1" );
        print_msg( 'ماژول با موفقيت فعال شد.', 'Success' );
    }

    function installop()
    {
        global $d, $info;
        $q = $d->getrows( "SELECT `stat` FROM `plugins` WHERE `name`='slider' LIMIT 1", true );
        if ( $q > 0 )
        {
            print_msg( 'اين ماژول قبلا نصب شده است.', 'Info' );
        }
        else
        {
            global $information;
            $oid = $d->getmax( 'oid', 'menus' );
            $oid++;
            $d->Query( "ALTER TABLE `permissions` ADD `slider` INT( 1 ) NOT NULL DEFAULT '0'" );
            $q   = $d->Query( "UPDATE `permissions` SET `slider`='1' WHERE `u_id`='$info[u_id]'" );
            $q   = $d->Query( "INSERT INTO `menus` SET `oid`='$oid',`name`='slider',`title`='$information[name]',`url`='plugins.php?plugin=slider',`type`='1'" );
            $q   = $d->Query( "INSERT INTO `plugins` SET `name`='slider',`title`='$information[name]',`stat`='0'" );
            $d->Query( "
			CREATE TABLE IF NOT EXISTS `mp_slider` (
			  `id` int(10) NOT NULL AUTO_INCREMENT,
			  `oid` int(10) NOT NULL DEFAULT '0',
			  `title` varchar(255) NOT NULL DEFAULT '',
			  `description` varchar(255) NOT NULL DEFAULT '',
			  `url` varchar(255) NOT NULL,
			  `link` varchar(255) NOT NULL,
			  `thumbnail` varchar(255) NOT NULL DEFAULT '',
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1
			" );
            activateop();
            print_msg( 'ماژول با موفقيت نصب شد.', 'Success' );
        }
    }

    function uninstallop()
    {
        global $d;
        $q = $d->getrows( "SELECT `stat` FROM `plugins` WHERE `name`='slider' LIMIT 1", true );
        if ( $q <= 0 )
        {
            print_msg( 'اين ماژول نصب نشده است يا استاندارد نيست.', 'Info' );
        }
        else
        {
            $d->Query( "ALTER TABLE `permissions` DROP `slider`" );
            $d->Query( "DELETE FROM `plugins` WHERE `name`='slider' LIMIT 1" );
            $d->Query( "DELETE FROM `menus` WHERE `name`='slider' LIMIT 1" );
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
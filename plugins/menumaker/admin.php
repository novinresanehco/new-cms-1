<?php

if ( !defined( 'plugins_admin_area' ) )
    die( 'invalid access' );
if ( !isset( $permissions['access_admin_area'] ) )
    die( 'invalid access' );
if ( $permissions['access_admin_area'] != '1' )
    die( 'invalid access' );

$information = array(
    'name'        => 'ماژول فهرست سامانه',
    'provider'    => 'گروه طراحی سامانه',
    'providerurl' => 'http://www.samaneh.it',
    'version'     => '2.0 Final',
    'install'     => true,
    'uninstall'   => true,
    'activate'    => false,
    'inactivate'  => false,
    'data'        => array(
        'icon' => array( 'name' => 'آیکن', 'value' => '', 'type' => 'icon' ),
    )
);

require_once dirname( __FILE__ ) . '/includes/config.php';

define( 'mod_version', $information['version'] );

$tpl->assign( 'first', '' );
if ( defined( 'methods' ) )
{

    function defaultop()
    {
        global $tpl, $d, $information, $config;
        $command  = @$_GET['do'];
        $itpl     = new samaneh();
        $itpl->assign( 'siteurl', $config['site'] );
        $itpl->assign( 'moduleurl', $config['site'] . 'panel/plugins.php?plugin=menumaker' );
        $response = array();
        switch ( $command )
        {
            default:

                $itpl->load( '../plugins/menumaker/admin.html' );
                $itpl->assign( 'site', $config['site'] );
                $data = array();

                require_once dirname( __FILE__ ) . '/styles.config.php';

                $group_id = 1;
                if ( isset( $_GET['group_id'] ) )
                {
                    $group_id = ( int ) $_GET['group_id'];
                }

                $sql = $d->Query( sprintf( 'SELECT * FROM %s WHERE %s = %s ORDER BY %s, %s', MENU_TABLE, MENU_GROUP, $group_id, MENU_PARENT, MENU_POSITION ) );

                $menu            = $d->getrows( $sql );
                $data['menu_ul'] = '<ul id="easymm"></ul>';
                if ( $menu )
                {

                    require_once dirname( __FILE__ ) . '/includes/tree.php';
                    $tree = new NSMenuTree;

                    while ( $row = $d->fetch( $sql ) )
                    {
                        $row[MENU_URL] = str_replace( "[site]", $config['site'], $row[MENU_URL] );
                        $tree->add_row(
                                $row[MENU_ID], $row[MENU_PARENT], ' id="menu-' . $row[MENU_ID] . '" class="sortable"', '<div class="ns-row">' .
                                '<div class="ns-title">' . $row[MENU_TITLE] . '</div>' .
                                '<div class="ns-url">' . $row[MENU_URL] . '</div>' .
                                '<div class="ns-class">' . $row[MENU_CLASS] . '</div>' .
                                '<div class="ns-actions">' .
                                '<a href="#" class="edit-menu" title="Edit Menu">' .
                                '<img src="' . $config['site'] . 'plugins/menumaker/images/edit.png" alt="ويرايش">' .
                                '</a>' .
                                '<a href="#" class="delete-menu">' .
                                '<img src="' . $config['site'] . 'plugins/menumaker/images/cross.png" alt="حذف">' .
                                '</a>' .
                                '<input type="hidden" name="menu_id" value="' . $row[MENU_ID] . '">' .
                                '</div>' .
                                '</div>'
                        );
                    }

                    $data['menu_ul'] = $tree->generate_list( 'id="easymm"' );
                }
                $data['group_id']    = $group_id;
                $sql                 = $d->query( sprintf( 'SELECT * FROM %s WHERE %s = %s', MENUGROUP_TABLE, MENUGROUP_ID, $group_id ) );
                $menu_group          = $d->fetch( $sql );
                $data['group_title'] = $menu_group[MENUGROUP_TITLE];
                $data['group_style'] = $menu_group[MENUGROUP_STYLE];
                $sql                 = $d->query( sprintf( 'SELECT %s, %s FROM %s', MENUGROUP_ID, MENUGROUP_TITLE, MENUGROUP_TABLE ) );

                while ( $row = $d->fetch( $sql ) )
                {
                    $itpl->block( 'groups', array( 'id' => $row[MENUGROUP_ID], 'title' => $row[MENUGROUP_TITLE] ) );
                }

                if ( ($group_id >= 2 ? true : false ) )
                    $itpl->assign( 'groupl1', 1 );

                foreach ( $data as $dc => $dv )
                {
                    $itpl->assign( $dc, $dv );
                }
                $menutypes = array();
                if ( count( $nsmstyle ) > 1 )
                {
                    $persian    = array( 'ض', 'ص', 'ث', 'ق', 'ف', 'غ', 'ع', 'ه', 'خ', 'ح', 'ج', 'ج', 'چ', 'پ', 'ش', 'س', 'ی', 'ب', 'ل', 'ا', 'ت', 'ن', 'م', 'ک', 'گ', 'ظ', 'ط', 'ز', 'ر', 'ذ', 'د', 'ئ', 'و' );
                    $english    = array( 'q', 'w', 'e', 'r', 't', 'u', 'i', 'o', 'p', 'pp', 'ppp', 'pppp', 'a', 's', 'd', 'f', 'g', 'h', 'j', 'k', 'l', 'l', 'l', 'z', 'x', 'c', 'v', 'b', 'n', 'm', 'mm' );
                    $selectType = '';
                    foreach ( $nsmstyle as $nsstyleid => $nsstylec )
                    {
                        if ( $data['group_style'] == $nsstyleid )
                        {
                            $type       = str_ireplace( $persian, $english, $nsstylec['type'] );
                            $type       = preg_replace( "/[^a-zA-Z0-9\-]/", "_", $type );
                            $selectType = $type;
                        }
                    }
                    foreach ( $nsmstyle as $nsstyleid => $nsstylec )
                    {
                        $type = str_ireplace( $persian, $english, $nsstylec['type'] );
                        $type = preg_replace( "/[^a-zA-Z0-9\-]/", "_", $type );
                        if ( !in_array( $nsstylec['type'], $menutypes ) )
                        {
                            $menutypes[] = $nsstylec['type'];
                            $selected    = '';
                            $itpl->block( 'themetypes', array(
                                'title'    => trim( $nsstylec['type'] ),
                                'selected' => $selected,
                                'checked'  => ($selectType == $type) ? 'checked' : '',
                                'value'    => $type,
                            ) );
                        }
                        $itpl->block( 'menuscreenshots', array(
                            'type'    => $type,
                            'display' => ($selectType == $type) ? 'block' : 'none',
                            'style'   => $nsstyleid,
                            'border'  => ($data['group_style'] == $nsstyleid) ? "border:2px red solid;" : "",
                            'image'   => $config['site'] . 'plugins/menumaker/screenshots/' . $nsstylec['image'],
                        ) );
                        $itpl->assign( 'selectedclass', $data['group_style'] );
                        //
                        //$itpl -> block('menustyles', array('value' => $nsstyleid, 'title' => $nsstylec['title'], 'selected' => ($data['group_style'] == $nsstyleid ? 'selected="selected"' : '' )));
                    }
                }

                $sql    = $d->query( "SELECT * FROM extra order by id desc" );
                $extras = null;
                while ( $row    = $d->fetch( $sql ) )
                {
                    $extras .= "\r\n<label><input type=\"checkbox\" class=\"extracbx\" value=\"$row[id]\"> <span id=\"extracbt_$row[id]\">$row[title]</span></label><br />";
                }
                $itpl->assign( 'pages', $extras );
                $sql     = $d->query( "SELECT * FROM plugins WHERE `stat`=1 order by id desc" );
                $plugins = null;
                while ( $row     = $d->fetch( $sql ) )
                {
                    $plugins .= "\r\n<label><input type=\"checkbox\" class=\"plugincbx\" value=\"$row[id]\"> <span id=\"plugincbt_$row[id]\">$row[title]</span></label><br />";
                }
                $itpl->assign( 'plugins', $plugins );

                //$sql = $d->query( "SELECT * FROM cat where `sub` != '0' order by id desc" );
                //while ( $row = $d->fetch( $sql ) )
                //{
                //$sql2 = $d->query( "SELECT * FROM `data` where `cat_id` = '" . $row['id'] . "' order by `pos` asc" );
                $sql2 = $d->query( "SELECT * FROM `data`  order by `pos` asc" );

                if ( $d->getrows( $sql2 ) )
                {
                    $dcoptions = null;
                    while ( $dc        = $d->fetch( $sql2 ) )
                    {
                        $dcoptions .= "\r\n<label><input type=\"checkbox\" class=\"datacbx\" value=\"$dc[id]\"> <span id=\"datacbt_$dc[id]\">$dc[title]</span></label><br />";
                    }
                    $itpl->block( 'datas', array( 'title' => $row['name'], 'options' => $dcoptions ) );
                }
                //}



                $tpl->assign( 'module_name', $information['name'] );
                $tpl->assign( 'first', $itpl->dontshowit() );


                $tpl->block( 'extra_div', array( 'id' => 2, 'title' => 'درباره ماژول', 'inside' => 'طراحي ماژول:گروه طراحی سامانه' ) );

                break;
            case 'addgroup':
                die();
                if ( isset( $_POST['title'] ) )
                {
                    $data[MENUGROUP_TITLE] = trim( $_POST['title'] );
                    if ( !empty( $data[MENUGROUP_TITLE] ) )
                    {
                        if ( $d->query( sprintf( "INSERT INTO %s (`%s`, `%s`) VALUES (NULL, '%s');", MENUGROUP_TABLE, MENUGROUP_ID, MENUGROUP_TITLE, mysql_real_escape_string( $data[MENUGROUP_TITLE] ) ) ) )
                        {
                            $response['status'] = 1;
                            $response['id']     = mysql_insert_id();
                        }
                        else
                        {
                            $response['status'] = 2;
                            $response['msg']    = 'خطا در اضافه منو.';
                        }
                    }
                    else
                    {
                        $response['status'] = 3;
                    }
                    header( 'Content-type: application/json' );
                    echo json_encode( $response );
                }
                else
                {
                    $itpl->load( '../plugins/menumaker/admin.menu_group.add.html' );
                    echo $itpl->dontshowit();
                }

                exit;
                break;

            case 'updategroup' :
                if ( isset( $_POST['title'] ) && isset( $_POST['style'] ) )
                {
                    $id                    = ( int ) $_POST['id'];
                    $data[MENUGROUP_TITLE] = trim( $_POST['title'] );
                    $data[MENUGROUP_STYLE] = trim( $_POST['style'] );
                    $response['success']   = false;
                    if ( $d->query( sprintf( "UPDATE %s SET `%s` = '%s', `%s` = '%s'  WHERE `%s` = '%d';", MENUGROUP_TABLE, MENUGROUP_TITLE, mysql_real_escape_string( $data[MENUGROUP_TITLE] ), MENUGROUP_STYLE, mysql_real_escape_string( $data[MENUGROUP_STYLE] ), MENUGROUP_ID, $id ) ) )
                    {
                        $response['success'] = true;
                    }
                    header( 'Content-type: application/json' );
                    echo json_encode( $response );
                }
                exit;
                break;

            case 'deletegroup' :
                if ( isset( $_POST['id'] ) )
                {
                    $id = ( int ) $_POST['id'];
                    if ( $id == 1 )
                    {
                        $response['success'] = false;
                        $response['msg']     = 'شما نمي توانيد فهرست اول را حذف نماييد';
                    }
                    else
                    {
                        $sql    = sprintf( 'DELETE FROM %s WHERE %s = %s', MENUGROUP_TABLE, MENUGROUP_ID, $id );
                        $delete = $d->query( $sql );
                        if ( $delete )
                        {
                            $sql                 = sprintf( 'DELETE FROM %s WHERE %s IN (%s)', MENU_TABLE, MENU_GROUP, $id );
                            $d->query( $sql );
                            $response['success'] = true;
                        }
                        else
                        {
                            $response['success'] = false;
                        }
                    }
                    header( 'Content-type: application/json' );
                    echo json_encode( $response );
                }
                exit;
                break;

            case 'addmenu':
                if ( isset( $_POST['title'] ) )
                {
                    $data[MENU_TITLE] = trim( $_POST['title'] );
                    if ( !empty( $data[MENU_TITLE] ) )
                    {
                        $data[MENU_URL]   = $_POST['url'];
                        $data[MENU_CLASS] = $_POST['class'];
                        $data[MENU_GROUP] = $_POST['group_id'];
                        $sql              = sprintf(
                                'SELECT MAX(%s) as max FROM %s WHERE %s = %s', MENU_POSITION, MENU_TABLE, MENU_GROUP, $data[MENU_GROUP]
                        );
                        $last_position    = $d->fetch( $d->query( $sql ) );

                        $data[MENU_POSITION] = $last_position['max'] + 1;
                        if ( $d->query( "INSERT INTO `" . MENU_TABLE . "` (`" . MENU_ID . "`, `" . MENU_PARENT . "`, `" . MENU_TITLE . "`, `" . MENU_URL . "`, `" . MENU_CLASS . "`, `" . MENU_POSITION . "`, `" . MENU_GROUP . "`) VALUES (NULL, '0', '" . mysql_real_escape_string( $data[MENU_TITLE] ) . "', '" . mysql_real_escape_string( $data[MENU_URL] ) . "', '" . mysql_real_escape_string( $data[MENU_CLASS] ) . "', '" . $data[MENU_POSITION] . "', '" . $data[MENU_GROUP] . "');" ) )
                        {
                            $data[MENU_ID]      = mysql_insert_id();
                            $response['status'] = 1;
                            $li_id              = 'menu-' . $data[MENU_ID];
                            $response['li']     = '<li id="' . $li_id . '" class="sortable"><div class="ns-row">' .
                                    '<div class="ns-title">' . $data[MENU_TITLE] . '</div>' .
                                    '<div class="ns-url">' . $data[MENU_URL] . '</div>' .
                                    '<div class="ns-class">' . $data[MENU_CLASS] . '</div>' .
                                    '<div class="ns-actions">' .
                                    '<a href="#" class="edit-menu" title="Edit Menu">' .
                                    '<img src="' . $config['site'] . 'plugins/menumaker/images/edit.png" alt="ويرايش">' .
                                    '</a>' .
                                    '<a href="#" class="delete-menu">' .
                                    '<img src="' . $config['site'] . 'plugins/menumaker/images/cross.png" alt="حذف">' .
                                    '</a>' .
                                    '<input type="hidden" name="menu_id" value="' . $data[MENU_ID] . '">' .
                                    '</div>' .
                                    '</div></li>';
                            $response['li_id']  = $li_id;
                        }
                        else
                        {
                            $response['status'] = 2;
                            $response['msg']    = 'خطا در اضافه منو جديد .';
                        }
                    }
                    else
                    {
                        $response['status'] = 3;
                    }
                }

                if ( isset( $_POST['bulk1'] ) OR isset( $_POST['bulk2'] ) OR isset( $_POST['bulk3'] ) )
                {
                    if ( count( $_POST['bt'] ) > 0 && count( $_POST['bid'] ) > 0 )
                    {
                        $response = array();
                        $bii      = 0;
                        foreach ( $_POST['bt'] as $bi => $bt )
                        {
                            $data[MENU_TITLE] = trim( $_POST['bt'][$bi] );
                            if ( !empty( $data[MENU_TITLE] ) )
                            {
                                if ( isset( $_POST['bulk1'] ) )
                                {
                                    $data[MENU_URL] = quick_extra_link( $_POST['bid'][$bi] );
                                }
                                else if ( isset( $_POST['bulk2'] ) )
                                {
                                    $data[MENU_URL] = quick_post_link( $_POST['bid'][$bi] );
                                }
                                else if ( isset( $_POST['bulk3'] ) )
                                {
                                    $sql            = sprintf(
                                            'SELECT `name` FROM `plugins` WHERE `id` = %s', intval( $_POST['bid'][$bi] )
                                    );
                                    $sql            = $d->fetch( $d->query( $sql ) );
                                    $sql            = $sql['name'];
                                    $data[MENU_URL] = 'index.php?plugins=' . $sql;
                                }
                                $data[MENU_CLASS] = '';
                                $data[MENU_GROUP] = $_POST['group_id'];
                                $sql              = sprintf(
                                        'SELECT MAX(%s) as max FROM %s WHERE %s = %s', MENU_POSITION, MENU_TABLE, MENU_GROUP, $data[MENU_GROUP]
                                );
                                $last_position    = $d->fetch( $d->query( $sql ) );

                                $data[MENU_POSITION] = $last_position['max'] + 1;
                                if ( $d->query( "INSERT INTO `" . MENU_TABLE . "` (`" . MENU_ID . "`, `" . MENU_PARENT . "`, `" . MENU_TITLE . "`, `" . MENU_URL . "`, `" . MENU_CLASS . "`, `" . MENU_POSITION . "`, `" . MENU_GROUP . "`) VALUES (NULL, '0', '" . mysql_real_escape_string( $data[MENU_TITLE] ) . "', '" . mysql_real_escape_string( $data[MENU_URL] ) . "', '" . mysql_real_escape_string( $data[MENU_CLASS] ) . "', '" . $data[MENU_POSITION] . "', '" . $data[MENU_GROUP] . "');" ) )
                                {
                                    $data[MENU_ID]                 = mysql_insert_id();
                                    $li_id                         = 'menu-' . $data[MENU_ID];
                                    $response['i' . $bii]['li']    = '<li id="' . $li_id . '" class="sortable"><div class="ns-row">' .
                                            '<div class="ns-title">' . $data[MENU_TITLE] . '</div>' .
                                            '<div class="ns-url">' . $data[MENU_URL] . '</div>' .
                                            '<div class="ns-class">' . $data[MENU_CLASS] . '</div>' .
                                            '<div class="ns-actions">' .
                                            '<a href="#" class="edit-menu" title="Edit Menu">' .
                                            '<img src="' . $config['site'] . 'plugins/menumaker/images/edit.png" alt="ويرايش">' .
                                            '</a>' .
                                            '<a href="#" class="delete-menu">' .
                                            '<img src="' . $config['site'] . 'plugins/menumaker/images/cross.png" alt="حذف">' .
                                            '</a>' .
                                            '<input type="hidden" name="menu_id" value="' . $data[MENU_ID] . '">' .
                                            '</div>' .
                                            '</div></li>';
                                    $response['i' . $bii]['li_id'] = $li_id;
                                }
                                else
                                {
                                    continue;
                                }
                            }
                            else
                            {
                                continue;
                            }
                            $bii++;
                        }

                        if ( !$bii )
                        {
                            $response['status'] = 2;
                            $response['msg']    = 'خطا در اضافه منو جديد .';
                        }
                        else
                        {
                            $response['status'] = 1;
                            $response['count']  = $bii;
                        }
                    }
                }

                header( 'Content-type: application/json' );
                echo json_encode( $response );

                exit;
                break;

            case 'savemenuposition':
                if ( isset( $_POST['easymm'] ) )
                {
                    $easymm = $_POST['easymm'];
                    menu_update_position( 0, $easymm );
                }
                exit;
                break;

            case 'editmenu' :
                if ( isset( $_GET['id'] ) )
                {
                    $id    = ( int ) $_GET['id'];
                    $sql   = sprintf(
                            'SELECT * FROM %s WHERE %s = %s', MENU_TABLE, MENU_ID, $id
                    );
                    $query = $d->query( $sql );
                    $menu  = $d->fetch( $query );
                    $itpl->load( '../plugins/menumaker/admin.menu.edit.html' );
                    $itpl->assign( 'id', $id );
                    $itpl->assign( 'title', $menu[MENU_TITLE] );
                    $itpl->assign( 'url', $menu[MENU_URL] );
                    $itpl->assign( 'class', $menu[MENU_CLASS] );
                    $itpl->assign( 'site', $config['site'] );
                    echo $itpl->dontshowit();
                }
                exit;
                break;

            case 'savemenu':
                if ( isset( $_POST['title'] ) )
                {
                    $data[MENU_TITLE] = trim( $_POST['title'] );
                    if ( !empty( $data[MENU_TITLE] ) )
                    {
                        $data[MENU_ID]    = $_POST['menu_id'];
                        $data[MENU_URL]   = $_POST['url'];
                        $data[MENU_CLASS] = $_POST['class'];
                        if ( $d->query( sprintf( "UPDATE %s SET `%s` = '%s', `%s` = '%s', `%s` = '%s' WHERE `%s` = '%d';", MENU_TABLE, MENU_TITLE, mysql_real_escape_string( $data[MENU_TITLE] ), MENU_URL, mysql_real_escape_string( $data[MENU_URL] ), MENU_CLASS, mysql_real_escape_string( $data[MENU_CLASS] ), MENU_ID, $data[MENU_ID] ) ) )
                        {
                            $response['status'] = 1;
                            $da['title']        = $data[MENU_TITLE];
                            $da['url']          = $data[MENU_URL];
                            $da['klass']        = $data[MENU_CLASS]; //klass instead of class because of an error in js
                            $response['menu']   = $da;
                        }
                        else
                        {
                            $response['status'] = 2;
                            $response['msg']    = 'خطا در ويرايش منو .';
                        }
                    }
                    else
                    {
                        $response['status'] = 3;
                    }
                    header( 'Content-type: application/json' );
                    echo json_encode( $response );
                }
                exit;
                break;

            case 'deletemenu':
                if ( isset( $_POST['id'] ) )
                {
                    global $menuids;
                    $id = ( int ) $_POST['id'];
                    menu_getchilds( $id );
                    if ( !empty( $menuids ) )
                    {
                        $ids = implode( ', ', $menuids );
                        $id  = "$id, $ids";
                    }
                    $sql    = sprintf( 'DELETE FROM %s WHERE %s IN (%s)', MENU_TABLE, MENU_ID, $id );
                    $delete = $d->query( $sql );
                    if ( $delete )
                    {
                        $response['success'] = true;
                    }
                    else
                    {
                        $response['success'] = false;
                    }
                    header( 'Content-type: application/json' );
                    echo json_encode( $response );
                }
                exit;
                break;
        }
    }

    function menu_getchilds( $id )
    {
        global $d, $menuids;
        $sql   = sprintf(
                'SELECT %s FROM %s WHERE %s = %s', MENU_ID, MENU_TABLE, MENU_PARENT, $id
        );
        $query = $d->query( $sql );

        if ( $d->getrows( $query ) )
        {
            while ( $v = $d->fetch( $query ) )
            {
                $menuids[] = $v[MENU_ID];
                menu_getchilds( $v[MENU_ID] );
            }
        }
    }

    function menu_update_position( $parent, $children )
    {
        global $d;
        $i = 1;
        foreach ( $children as $k => $v )
        {
            $id                  = ( int ) $children[$k]['id'];
            $data[MENU_PARENT]   = $parent;
            $data[MENU_POSITION] = $i;
            $d->query( sprintf( "UPDATE `%s` SET `%s` = '%d', `%s` = '%d' WHERE `%s` = '%d'", MENU_TABLE, MENU_PARENT, $data[MENU_PARENT], MENU_POSITION, $data[MENU_POSITION], MENU_ID, $id ) );
            if ( isset( $children[$k]['children'][0] ) )
            {
                menu_update_position( $id, $children[$k]['children'] );
            }
            $i++;
        }
    }

    function installop()
    {
        global $d;
        $q = $d->getrows( "SELECT `stat` FROM `plugins` WHERE `name`='menumaker' LIMIT 1", true );
        if ( $q > 0 )
            print_msg( 'اين ماژول قبلا نصب شده است.', 'Info' );
        else
        {
            global $information;
            $q = $d->Query( "INSERT INTO `plugins` SET `name`='menumaker',`title`='$information[name]',`stat`='1'" );

            $q = $d->Query( "CREATE TABLE IF NOT EXISTS `ns_menu` (
		  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
		  `parent_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
		  `title` varchar(255) NOT NULL DEFAULT '',
		  `url` varchar(255) NOT NULL DEFAULT '',
		  `class` varchar(255) NOT NULL DEFAULT '',
		  `position` tinyint(3) unsigned NOT NULL DEFAULT '0',
		  `group_id` tinyint(3) unsigned NOT NULL DEFAULT '1',
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;" );

            $q = $d->Query( 'CREATE TABLE IF NOT EXISTS `ns_menu_group` (
		  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
		  `title` varchar(255) NOT NULL,
		  `style` varchar(32) NOT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;' );

            $menus   = array();
            $menus[] = array(
                'title' => 'صفحه اصلی',
                'url'   => '[site]',
            );
            $menus[] = array(
                'title' => 'لیست اعضا',
                'url'   => '[site]members.html',
            );
            $menus[] = array(
                'title' => 'تماس با ما',
                'url'   => '[site]contact.html',
            );
            for ( $i = 0, $c = count( $menus ); $i < $c; $i++ )
            {
                $d->iQuery( "ns_menu", array(
                    'parent_id' => 0,
                    'title'     => $menus[$i]['title'],
                    'url'       => $menus[$i]['url'],
                    'class'     => '',
                    'position'  => ($i + 1),
                    'group_id'  => 1,
                ) );
            }
            $q   = $d->Query( "INSERT INTO `ns_menu_group` (`id`, `title`, `style`) VALUES (1, 'صفحه اصلي', 'h-white-rtl');" );
            $oid = $d->getmax( 'oid', 'menus' );
            $q   = $d->Query( "INSERT INTO `menus` SET `oid`='$oid',`name`='menumaker',`title`='<b>$information[name]</b>',`url`='plugins.php?plugin=menumaker',`type`='0'" );

            print_msg( 'ماژول با موفقيت نصب شد.', 'Success' );
        }
    }

    function uninstallop()
    {
        global $d;
        $q = $d->getrows( "SELECT `stat` FROM `plugins` WHERE `name`='menumaker' LIMIT 1", true );
        if ( $q <= 0 )
            print_msg( 'اين ماژول نصب نشده است يا استاندارد نيست.', 'Info' );
        else
        {
            $d->Query( "DELETE FROM `menus` WHERE `name`='menumaker' LIMIT 1" );
            $q = $d->Query( "DELETE FROM `plugins` WHERE `name`='menumaker' LIMIT 1" );
            $q = $d->Query( "DROP TABLE `ns_menu_group`, `ns_menu`;" );
            print_msg( 'ماژول با موفقيت حذف شد.', 'Success' );
        }
    }

    function print_msg( $msg, $type )
    {
        global $tpl, $information;
        $tpl->assign( array(
            'module_name' => $information['name'],
            $type         => 1,
            'msg'         => $msg,
        ) );
    }

}
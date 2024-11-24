<?php

if ( !defined( 'IN_NSMENU' ) )
{
    die( 'Access Denied !' );
}
if ( !( defined( 'tabs' ) OR defined( 'ajax_head' ) ) )
{
    $menuinit        = array();
    $menuinitcode    = null;
    $menuinitcodecss = null;


    if ( $nsmenu = preg_match_all( '/\[menu-([0-9]+)(_static)?\]/s', $this->tpl, $nsmatches ) )
    {
        if ( !class_exists( 'NSMenuTree' ) )
        {
            require_once( dirname( __FILE__ ) . '/includes/tree.php');
        }
        if ( !defined( 'NSMENU_INIT' ) )
        {
            require_once( dirname( __FILE__ ) . '/includes/config.php');
        }
        if ( !isset( $config ) )
        {
            global $config;
        }

        $tree = new NSMenuTree;

        $i           = 0;
        $chosedTheme = '';
        while ( $i < $nsmenu )
        {
            $group_id = ( int ) $nsmatches[1][$i];
            $sql      = $d->query( sprintf(
                            'SELECT * FROM %s WHERE %s = %s', MENUGROUP_TABLE, MENUGROUP_ID, $group_id ) );
            $menu     = $d->fetch( $sql );
            $sql      = $d->query( sprintf(
                            'SELECT * FROM %s WHERE %s = %s AND %s = 0 ORDER BY %s', MENU_TABLE, MENU_GROUP, $group_id, MENU_PARENT, MENU_POSITION ) );

            $style    = $menu[MENUGROUP_STYLE];
            $theme    = __DIR__ . '/../../theme/core/' . $config['theme'] . '/plugins/' . $style . '.html';
            $tplTheme = __DIR__ . '/themes/' . $style . '.html';
            if ( file_exists( $theme ) )
            {
                $chosedTheme = $theme;
            }
            else if ( file_exists( $tplTheme ) )
            {
                $chosedTheme = $tplTheme;
            }
            else
            {
                //$chosedTheme = __DIR__ . '/themes/menumaker_default.html';
            }
            if ( !empty( $chosedTheme ) )
            {
                $menutpl = new samaneh();
                $menutpl->load( $chosedTheme );
                while ( $cmenu   = $d->fetch( $sql ) )
                {
                    $menu1 = array(
                        'label1' => $cmenu['title'],
                        'id'     => $cmenu['id'],
                        'link1'  => $cmenu['url'],
                        'icon1'  => $cmenu['class'],
                    );
                    //check if menu have 2send level
                    $sql2  = $d->query( sprintf(
                                    'SELECT * FROM %s WHERE %s = %s AND %s = %s ORDER BY %s', MENU_TABLE, MENU_GROUP, $group_id, MENU_PARENT, $cmenu['id'], MENU_POSITION ) );
                    if ( $d->getRows( $sql2 ) > 0 )
                    {
                        $menu1['hasMenu2' . $cmenu['id']] = true;
                    }
                    $menutpl->block( 'menu1', $menu1 );
                    while ( $cmenu2 = $d->fetch( $sql2 ) )
                    {
                        $menu2 = array(
                            'label2' => $cmenu2['title'],
                            'id2'    => $cmenu2['id'],
                            'link2'  => $cmenu2['url'],
							'icon2'  => $cmenu['class'],
                        );
                        $sql3  = $d->query( sprintf(
                                        'SELECT * FROM %s WHERE %s = %s AND %s = %s ORDER BY %s', MENU_TABLE, MENU_GROUP, $group_id, MENU_PARENT, $cmenu2['id'], MENU_POSITION ) );
                        if ( $d->getRows( $sql3 ) > 0 )
                        {
                            $menu1['hasMenu3' . $cmenu2['id']] = true;
                        }

                        $menutpl->block( 'menu2' . $cmenu['id'], $menu2 );
                        while ( $cmenu3 = $d->fetch( $sql3 ) )
                        {
                            $menu3 = array(
                                'label3' => $cmenu3['title'],
                                'id3'    => $cmenu3['id'],
                                'link3'  => $cmenu3['url'],
								'icon3'  => $cmenu['class'],
                            );
                            $sql4  = $d->query( sprintf(
                                            'SELECT * FROM %s WHERE %s = %s AND %s = %s ORDER BY %s', MENU_TABLE, MENU_GROUP, $group_id, MENU_PARENT, $cmenu3['id'], MENU_POSITION ) );
                            if ( $d->getRows( $sql4 ) > 0 )
                            {
                                $menu3['hasMenu4' . $cmenu3['id']] = true;
                            }

                            $menutpl->block( 'menu3' . $cmenu2['id'], $menu3 );
                            while ( $cmenu4 = $d->fetch( $sql4 ) )
                            {
                                $menu4 = array(
                                    'label4' => $cmenu4['title'],
                                    'id4'    => $cmenu4['id'],
                                    'link4'  => $cmenu4['url'],
									'icon4'  => $cmenu['class'],
                                );
                                $sql5  = $d->query( sprintf(
                                                'SELECT * FROM %s WHERE %s = %s AND %s = %s ORDER BY %s', MENU_TABLE, MENU_GROUP, $group_id, MENU_PARENT, $cmenu4['id'], MENU_POSITION ) );
                                if ( $d->getRows( $sql5 ) > 0 )
                                {
                                    $menu4['hasMenu5' . $cmenu4['id']] = true;
                                }
                                $menutpl->block( 'menu4' . $cmenu3['id'], $menu4 );
                                while ( $cmenu5 = $d->fetch( $sql5 ) )
                                {
                                    $menu5 = array(
                                        'label5' => $cmenu5['title'],
                                        'id5'    => $cmenu5['id'],
                                        'link5'  => $cmenu5['url'],
										'icon5'  => $cmenu['class'],
                                    );
                                    $sql6  = $d->query( sprintf(
                                                    'SELECT * FROM %s WHERE %s = %s AND %s = %s ORDER BY %s', MENU_TABLE, MENU_GROUP, $group_id, MENU_PARENT, $cmenu5['id'], MENU_POSITION ) );
                                    if ( $d->getRows( $sql6 ) > 0 )
                                    {
                                        $menu5['hasMenu6' . $cmenu5['id']] = true;
                                    }
                                    $menutpl->block( 'menu5' . $cmenu4['id'], $menu5 );
                                    while ( $cmenu6 = $d->fetch( $sql6 ) )
                                    {
                                        $menu6 = array(
                                            'label6' => $cmenu6['title'],
                                            'id6'    => $cmenu6['id'],
                                            'link6'  => $cmenu6['url'],
											'icon6'  => $cmenu['class'],
                                        );
                                        $sql7  = $d->query( sprintf(
                                                        'SELECT * FROM %s WHERE %s = %s AND %s = %s ORDER BY %s', MENU_TABLE, MENU_GROUP, $group_id, MENU_PARENT, $cmenu6['id'], MENU_POSITION ) );
                                        if ( $d->getRows( $sql7 ) > 0 )
                                        {
                                            $menu6['hasMenu7' . $cmenu6['id']] = true;
                                        }
                                        $menutpl->block( 'menu6' . $cmenu5['id'], $menu6 );
                                        while ( $cmenu7 = $d->fetch( $sql7 ) )
                                        {
                                            $menu7 = array(
                                                'label7' => $cmenu7['title'],
                                                'id7'    => $cmenu7['id'],
                                                'link7'  => $cmenu7['url'],
												'icon7'  => $cmenu['class'],
                                            );
                                            $sql8  = $d->query( sprintf(
                                                            'SELECT * FROM %s WHERE %s = %s AND %s = %s ORDER BY %s', MENU_TABLE, MENU_GROUP, $group_id, MENU_PARENT, $cmenu7['id'], MENU_POSITION ) );
                                            if ( $d->getRows( $sql8 ) > 0 )
                                            {
                                                $menu6['hasMenu8' . $cmenu7['id']] = true;
                                            }
                                            $menutpl->block( 'menu7' . $cmenu6['id'], $menu7 );
                                            while ( $cmenu8 = $d->fetch( $sql8 ) )
                                            {
                                                $menu8 = array(
                                                    'label8' => $cmenu8['title'],
                                                    'id8'    => $cmenu8['id'],
                                                    'link8'  => $cmenu8['url'],
													'icon8'  => $cmenu['class'],
                                                );
                                                $sql9  = $d->query( sprintf(
                                                                'SELECT * FROM %s WHERE %s = %s AND %s = %s ORDER BY %s', MENU_TABLE, MENU_GROUP, $group_id, MENU_PARENT, $cmenu8['id'], MENU_POSITION ) );
                                                if ( $d->getRows( $sql9 ) > 0 )
                                                {
                                                    $menu6['hasMenu9' . $cmenu8['id']] = true;
                                                }
                                                $menutpl->block( 'menu8' . $cmenu7['id'], $menu8 );
                                                while ( $cmenu9 = $d->fetch( $sql9 ) )
                                                {
                                                    $menu9 = array(
                                                        'label9' => $cmenu9['title'],
                                                        'id9'    => $cmenu9['id'],
                                                        'link9'  => $cmenu9['url'],
														'icon9'  => $cmenu['class'],
                                                    );
                                                    $sql10 = $d->query( sprintf(
                                                                    'SELECT * FROM %s WHERE %s = %s AND %s = %s ORDER BY %s', MENU_TABLE, MENU_GROUP, $group_id, MENU_PARENT, $cmenu9['id'], MENU_POSITION ) );
                                                    if ( $d->getRows( $sql10 ) > 0 )
                                                    {
                                                        $menu6['hasMenu10' . $cmenu9['id']] = true;
                                                    }
                                                    $menutpl->block( 'menu9' . $cmenu8['id'], $menu9 );
                                                    while ( $cmenu10 = $d->fetch( $sql10 ) )
                                                    {
                                                        $menu9 = array(
                                                            'label10' => $cmenu10['title'],
                                                            'id10'    => $cmenu10['id'],
                                                            'link10'  => $cmenu10['url'],
															'icon10'  => $cmenu['class'],
                                                        );
                                                        $menutpl->block( 'menu10' . $cmenu9['id'], $menu10 );
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                $menucode  = $menutpl->dontshowit();
                $this->tpl = str_replace( '[menu-' . $group_id . ']', $menucode, $this->tpl );
            }
            $i++;
        }
        if ( empty( $chosedTheme ) )
        {
            $i = 0;
            while ( $i < $nsmenu )
            {
                $group_id = ( int ) $nsmatches[1][$i];
                $sql      = $d->query( sprintf(
                                'SELECT * FROM %s WHERE %s = %s', MENUGROUP_TABLE, MENUGROUP_ID, $group_id ) );
                $menu     = $d->fetch( $sql );

                $sql = $d->query( sprintf(
                                'SELECT * FROM %s WHERE %s = %s ORDER BY %s, %s', MENU_TABLE, MENU_GROUP, $group_id, MENU_PARENT, MENU_POSITION ) );

                while ( $cmenu = $d->fetch( $sql ) )
                {
                    $cmenu[MENU_URL]   = str_replace( '[site]', $config['site'], $cmenu[MENU_URL] );
                    $cmenu[MENU_TITLE] = str_replace( '[site]', $config['site'], $cmenu[MENU_TITLE] );
                    $label             = '<a href="' . $cmenu[MENU_URL] . '"><img src="' . $cmenu[MENU_CLASS] . '"/>';
                    $label .= $cmenu[MENU_TITLE];
                    $label .= '</a>';

                    $li_attr = '';
                    if ( $cmenu[MENU_CLASS] )
                    {
                        $li_attr = '';
                    }
                    $tree->add_row( $cmenu[MENU_ID], $cmenu[MENU_PARENT], $li_attr, $label );
                    //static menus

                    if ( $cmenu[MENU_PARENT] == 0 )
                    {
                        $tag   = 'menus';
                        $array = array(
                            'title' => $cmenu[MENU_TITLE],
                            'url'   => $cmenu[MENU_URL],
                            'id'    => $cmenu[MENU_ID]
                        );
                        $this->block( 'subMenu_' . $cmenu[MENU_ID], array() );
                    }
                    else
                    {
                        $this->block( 'subMenu_' . $cmenu[MENU_PARENT], array() );
                        $tag   = 'subMenuItems_' . $cmenu[MENU_PARENT];
                        $array = array(
                            'subMenuTitle' => $cmenu[MENU_TITLE],
                            'subUrl'       => $cmenu[MENU_URL],
                            'subID'        => $cmenu[MENU_ID]
                        );
                    }

                    $this->block( $tag, $array );
                }


                $menuinit[$menu[MENUGROUP_STYLE]] = $menu[MENUGROUP_STYLE];
                $menuStyle                        = $d->Query( "SELECT `style` FROM `" . MENUGROUP_TABLE . "` WHERE `id`='$group_id' LIMIT 1" );
                $menuStyle                        = $d->fetch( $menuStyle );
                $menuStyle                        = $menuStyle['style'];
                require_once dirname( __FILE__ ) . '/styles.config.php';
                $ulattr                           = '';
				
                if ( !empty( $nsmstyle[$menuStyle]['ul'][0] ) )
                {
                    $ulattr .= ' id="' . $nsmstyle[$menuStyle]['ul'][0] . '"';
                }
                if ( !empty( $nsmstyle[$menuStyle]['ul'][0] ) )
                {
                    $ulattr .= ' class="' . $nsmstyle[$menuStyle]['ul'][1] . '"';
                }
                $menucode = $tree->generate_list( $ulattr, $menuStyle, $nsmstyle[$menuStyle] );

                $this->tpl = str_replace( '[menu-' . $group_id . ']', $menucode, $this->tpl );
                $this->tpl = str_replace( '[menu-' . $group_id . '_static]', '', $this->tpl );
                $i++;
            }
        }
    }

    if ( count( $menuinit ) )
    {
        $menuinitcode .= '<script src="' . $config['site'] . 'plugins/menumaker/js/jquery.1.4.1.min.js"></script> <script> $(function() {';
        foreach ( $menuinit as $menuinitc => $menuinitv )
        {
            $menuinitcodecss .= '<link rel="stylesheet" type="text/css" href="' . $config['site'] . 'plugins/menumaker/css/' . $menuinitc . '.css">';
            $menuinitcode .= '$(document).ready(function(){
		$(".' . $menuinitc . ' li").hover(
			function(){ $("> ul", this).fadeIn("fast"); }, 
			function(){ $("> ul", this).fadeOut("fast"); } 		
		);

	$(\'.' . $menuinitc . ' li:has(ul)\').addClass(\'parent\');});';
        }

        $menuinitcode .= '});</script>';
        $menuinitcode .= $menuinitcodecss;
    }

    $this->tpl = str_replace( '[menu-init]', $menuinitcode, $this->tpl );
}
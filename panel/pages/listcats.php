<?php

define( 'samanehper', 'cat' );
define( "ajax_head", true );
require("ajax_head.php");
require("../../core/theme.php");
$Themedir = "../../theme/admin/" . $config['admintheme'] . '/ajax/';
if ( isset( $_POST['thememanager'] ) )
{
    $pageTheme = 'quickcatthememanager.htm';
}
else
{
    $pageTheme = 'quickcat.htm';
}
$tpl = new samaneh();
$tpl->load( $Themedir . $pageTheme );
$colors = array( 'green', 'black', 'red', 'blue', 'orange', 'purpule' );
$categories = array( );

function getTableCats( $parent = 0, $join = '' )
{
    global $d, $tpl, $colors, $categories;
    $menu_data = $d->Query( "SELECT * FROM `cat` WHERE sub = '$parent' ORDER BY `sub`,`id` ASC" );
    if ( $d->GetRows( $menu_data ) > 0 )
    {
        $p_sub = ( int ) $d->GetRowValue( "sub", "SELECT `sub` FROM `cat` WHERE `id`='$parent' LIMIT 1", true );
        $join .= '---';
        while ( $menu = $d->fetch( $menu_data ) )
        {
            if ( $p_sub == $parent )
            {
                $join = substr( $join, 0, -3 );
            }
            //$font = (strlen($join) == 3) ? 'bold' : 'normal';
            $color = (isset( $colors[floor( strlen( $join ) / 3 )] )) ? $colors[floor( strlen( $join ) / 3 )] : 'black';
            if ( $menu['sub'] == 0 )
            {
                $catMainId = 0;
                $font = 'bold';
            }
            else
            {
                $catMainId = $menu['sub'];
                $font = 'normal';
            }
            $link = get_subcat_link( array( "%id%" => $menu['id'], "%name%" => $menu['name'], "%slug%" => $menu['enname'] ) );
            $tpl->block( "listtr", array( "CatId" => $menu['id'], "font" => $font, "color" => $color, "Subject" => $join . ' ' . $menu['name'], "Ename" => $menu['enname'], "catId" => $menu['id'], "CatcoreId" => $catMainId, "link" => $link ) );
            $categories[] = $menu['id'];
            getTableCats( $menu['id'], $join );
        }
    }
}

getTableCats( 0 );

$htmlfiles = listFiles( current_theme_dir, array( 'html', 'htm' ) );



if ( isset( $_POST['thememanager'] ) )
{
    $once = array( );
    foreach ( $htmlfiles as $theme )
    {
        if ( strpos( $theme, 'index.htm' ) !== false )
        {
            continue;
        }
        $theme = str_replace( current_theme_dir, '', $theme );
        $theme = trim( $theme, '/' );
        $theme = trim( $theme, '\\' );
        foreach ( $categories as $category )
        {
            $selected = '';
            if ( $theme == 'cat_' . $category . '.htm' )
            {
                $selected = 'selected';
            }
            $subSelected = '';
            if ( $theme == 'cat_' . $category . '_single' . '.htm' )
            {
                $subSelected = 'selected';
            }

            if ( !in_array( $category, $once ) )
            {
                $once[] = $category;
                $tpl->block( 'catThemes_' . $category, array( 'theme' => 'default', 'selected' => '' ) );
                $tpl->block( 'subCatthemes_' . $category, array( 'theme' => 'default', 'selected' => '' ) );
            }

            $tpl->block( 'catThemes_' . $category, array( 'theme' => $theme, 'selected' => $selected ) );
            $tpl->block( 'subCatthemes_' . $category, array( 'theme' => $theme, 'selected' => $subSelected ) );
        }
    }
}
/*
  $q = $d->query("SELECT * FROM `cat` WHERE `sub`='0'");
  while ($data = $d->fetch($q)){
  $tpl->block("listtr",array("font"=>"bold","color"=>"green","Subject"=>$data['name'],"Ename"=>$data['enname'],"catId"=>$data['id'],"catMainId"=>"0"));
  $id = $data['id'];
  $qu = $d->query("SELECT * FROM `cat` WHERE `sub`='$id'");
  while ($datau = $d->fetch($qu)){
  $color =
  $tpl->block("listtr",array("font"=>"normal","color"=>"black","Subject"=>$datau['name'],"Ename"=>$datau['enname'],"catId"=>$datau['id'],"catMainId"=>$data['id']));
  $last = $datau["enname"];
  }

  }
 */
$tpl->showit();
<?php

define( 'head', true );
define( 'page', 'cat' );
$pageTheme = 'cat.htm';
define( 'tabs', true );
$pagetitle = 'موضوعات';
$tabs = array( 'درج موضوع', 'درج زیر موضوع', 'دسته بندی ها' );
include('header.php');
$q = $d->query( "SELECT id,name FROM `cat` WHERE `sub`='0'" );
$core = '';
$core2 = '<option id="cat_list_0" value="0">' . $lang['coresub'] . '</option>';
while ( $data = mysql_fetch_array( $q ) )
{
    $core .= '<option value="' . $data["id"] . '">' . $data["name"] . '</option>';
    $core2 .= '<option id="cat_list_' . $data["id"] . '" value="' . $data["id"] . '">' . $data["name"] . '</option>';
}
$core = '<select data-placeholder="انتخاب موضوع" id="clear-results" class="select2 rtl" name="ecore" id="ecore" size="1">' . $core . '</select>';
$core2 = '<select data-placeholder="انتخاب موضوع" id="clear-results" class="select2 rtl" id="core" name="core" size="1">' . $core2 . '</select>';
$tpl->assign( array( "core" => $core, "core2" => $core2 ) );
$htpl->showit();
$tpl->showit();
$ftpl->showit();
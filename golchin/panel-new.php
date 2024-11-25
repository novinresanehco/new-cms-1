<?php

define( "head", true );
define( 'page', 'newpost' );
$pageTheme = 'new.htm';
define( 'tabs', true );
$tabs = array( 'نخست', 'تنظیمات مطلب', 'فیلدهای اضافی' );
include('header.php');
if ( isset( $permissions['newpost'] ) )
{
    if ( $permissions['newpost'] != '1' )
    {
        require('../include/html.php');
        $html = new html();
        $html->msg( $lang['limited'] );
        $html->printout( 'info' );
    }
}
$coree = '<select name="core" id="core" class="select2">' . getSelectCats( 0, '' ) . '</select>';
$expiredate = timeboxgen( 'expiredate' );
$posttime = timeboxgen( 'posttime' );
$info = $user->info;
$tpl->assign( array(
    'fullname' => $info['name'],
    'core' => $coree,
    'subject' => getListCat(),
    'expiredate' => $expiredate,
    'posttime' => $posttime,
) );
$feilds = $d->Query( "SELECT * FROM `postfields` ORDER BY `orderid`" );
while ( $data = $d->fetch( $feilds ) )
{
    $field = '';
    switch ( $data['type'] )
    {
        case 'image':
            $field = "
						<div class='autocomplete-append'>
			    		<ul class='search-options'>
			    			<li><a data-original-title='Settings' href=\"mfm.php?mode=standalone&amp;field=custom_$data[id]\" class='settings-option tip browsefile'></a></li>
			    		</ul>
						 <input type='text' class='input-xlarge' id='custom_$data[id]' name='custom_$data[name]' value='' />
						 					</div>

						";
            break;
        case 'input':
            $field = "
						<input type='text' class='input-xlarge' id='custom_$data[id]' name='custom_$data[name]' value='' />";
            break;
        case 'textarea':
            $field = "<textarea class='input-xlarge editor' id='custom_$data[id]' name='custom_$data[name]'></textarea>";
            break;
    }
    $tpl->block( 'postfields', array(
        'title' => $data['title'],
        'field' => $field,
        'name' => $data['name'],
    ) );
}
$htpl->showit();
$tpl->showit();
$ftpl->showit();
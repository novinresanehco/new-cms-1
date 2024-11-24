<?php

define( 'head', true );
$pageTheme = 'edit.htm';
define( 'page', 'postmgr' );
$pagetitle = 'ویرایش مطلب';
define( 'tabs', true );
$tabs = array( 'نخست', 'تنظیمات مطلب', 'فیلدهای اضافی' );
include('header.php');

function check()
{
    $html = new html();
    global $lang;
    $html->msg( $lang['waccess'] );
    $html->printout( true );
}

$pid = (!isset( $_GET['id'] ) || !is_numeric( @$_GET['id'] )) ? check() : $_GET['id'];

$qu = $d->Query( "SELECT * FROM `data` WHERE `id`='$pid'" );
$data = $d->fetch( $qu );
$qu = $d->Query( "SELECT `catid` FROM `catpost` WHERE `pid`='$pid'" );
$subcats = array( );
while ( $scdata = $d->fetch( $qu ) )
{
    $subcats[] = $scdata['catid'];
}
$expiredate = timeboxgen( 'expiredate' );
$posttime = timeboxgen( 'posttime' );
$author = new user();
$author = $author->info( false, $data['author'] );
if ( !empty( $data['full'] ) )
{
    $tpl->block( "hasfull", array( ) );
}
else
{
    $tpl->block( "nofull", array( ) );
}
if ( !empty( $data['pass1'] ) )
{
    $data['pass1'] = '**hidden**';
    $tpl->assign( "postpwch", "checked" );
    $tpl->assign( "postpassword", "" );
    $tpl->block( "hasfull", array( ) );
}
else
{
    $tpl->assign( "postpwch", "" );
    $tpl->assign( "postpassword", "none" );
}
if ( !empty( $data['pass2'] ) )
{
    $data['pass2'] = '';
    $tpl->assign( "fpostpwch", "checked" );
    $tpl->assign( "postfullpassword", "" );
    $tpl->block( "hasfull", array( ) );
}
else
{
    $tpl->assign( "fpostpwch", "" );
    $tpl->assign( "postfullpassword", "none" );
}
/*
  $qu = $d->Query("SELECT * FROM `voteq` WHERE `pid`='$pid'");
  $qu = $d->fetch($qu);
  if(is_numeric($qu['id'])){
  $tpl->assign("quest",$qu['title']);
  $tmp = ($qu['ipvote'] == 1) ? $tpl->assign("ipvote","checked"): '';
  $tmp = ($qu['multic'] == 1) ? $tpl->assign("multic","checked"): '';
  $tmp = $qu['id'];
  $qu = $d->Query("SELECT * FROM `voteans` WHERE `voteid`='$tmp'");
  $ans = '';
  while($tmp = $d->fetch($qu)){
  $ans .= trim($tmp['title'])."\n";
  }
  $tpl->assign("voteans",trim($ans));

  }else{
  $tpl->assign(array('quest'=>'','ipvote'=>'','multic'=>'','voteans'=>''));
  }
 */
for ( $i = 1; $i < 5; $i++ )
{
    if ( $data['show'] == $i )
    {
        $tpl->assign( "show" . $i, "selected" );
    }
    else
    {
        $tpl->assign( "show" . $i, "" );
    }
}
$keys = '';
$def = '';
$tmp = ($qu['star'] == 1) ? $tpl->assign( "star", "checked" ) : '';
$qu = $d->Query( "SELECT `key_id` FROM `keys_join` WHERE `post_id`='$pid'" );
while ( $tmp = $d->fetch( $qu ) )
{
    if ( !empty( $tmp['key_id'] ) )
    {
        $qus = $d->Query( "SELECT `title` FROM `keys` WHERE `id`='" . $tmp['key_id'] . "' LIMIT 1" );
        $tmps = $d->fetch( $qus );
        $keys .= $def . $tmps['title'];
        $def = ', ';
    }
}
$def = empty( $def ) ? $tpl->assign( "keys", "" ) : $tpl->assign( "keys", $keys );


for ( $i = 1; $i < 4; $i++ )
{
    if ( $data['scomments'] == $i )
    {
        $tpl->assign( "scomments" . $i, "selected" );
    }
    else
    {
        $tpl->assign( "scomments" . $i, "" );
    }
}
if ( $data['reg'] == 1 )
{
    $tpl->assign( "reg1", "selected" );
    $tpl->assign( "reg2", "" );
}
else
{
    $tpl->assign( "reg1", "" );
    $tpl->assign( "reg2", "selected" );
}


$tpl->assign( array(
    'core' => '<select name="core" id="core" class="select2">' . getSelectCats( 0, '', '', $data['cat_id'] ) . '</select>',
    'subject' => getListCat( 0, $subcats ),
    'expiredate' => $expiredate,
    'posttime' => $posttime,
    'title' => $data['title'],
    'entitle' => engconv( $data['entitle'] ),
    'timage' => $data['timage'],
    'author' => $author['name'],
    'context' => $data['context'],
    'hits' => intval( $data['hits'] ),
    'show' => $data['show'],
    'scomments' => $data['scomments'],
    'star' => $data['star'],
    'pass1' => $data['pass1'],
    'pass2' => $data['pass2'],
    'reg' => $data['reg'],
    'text' => $data['text'],
    'fulltext' => $data['full'],
    'keywords' => $data['keywords'],
    'description' => $data['description'],
    'pos' => 0,
    'pid' => $pid,
) );
if ( !empty( $data['full'] ) )
{
    $tpl->assign( array(
        'delclass' => '',
        'addclass' => 'class="hideclass"',
    ) );
}
else
{
    $tpl->assign( array(
        'delclass' => 'class="hideclass"',
        'addclass' => '',
    ) );
}
$feilds = $d->Query( "SELECT * FROM `postfields` ORDER BY `orderid`" );
while ( $fdata = $d->fetch( $feilds ) )
{
    $value = !empty( $data[$fdata['name']] ) ? $data[$fdata['name']] : '';
    $field = '';
    switch ( $fdata['type'] )
    {
        case 'image':
            $field = "
						<div class='autocomplete-append'>
			    		<ul class='search-options'>
			    			<li><a data-original-title='Settings' href=\"mfm.php?mode=standalone&amp;field=custom_$fdata[id]\" class='settings-option tip browsefile'></a></li>
			    		</ul>
						 <input type='text' class='input-xlarge ltr' id='custom_$fdata[id]' name='custom_$fdata[name]' value='$value' />
						 					</div>
						";
            break;
        case 'input':
            $field = "
						<input type='text' class='input-xlarge' id='custom_$fdata[id]' name='custom_$fdata[name]' value='$value' />";
            break;
        case 'textarea':
            $value = htmlspecialchars( $value );
            $field = "<textarea class='input-xlarge editor' id='custom_$fdata[id]' name='custom_$fdata[name]'>$value</textarea>";
            break;
    }
    $tpl->block( 'postfields', array(
        'title' => $fdata['title'],
        'field' => $field,
        'name' => $fdata['name'],
    ) );
}
$htpl->showit();
$tpl->showit();
$ftpl->showit();
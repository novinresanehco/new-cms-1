<?php

@session_start();
define( 'head', true );
$pageTheme   = 'list.htm';
define( 'page', 'postmgr' );
define( 'tabs', true );
$tabs        = array( 'لیست مطالب', 'جستجو پیشرفته' );
$pagetitle   = 'مدیریت مطالب';
include('header.php');
$permissions = $user->permission();
$html        = new html();
$q           = $d->query( "SELECT `id`,`name`,`sub` FROM `cat`" );
$core        = '<option value="0">' . $lang["all"] . '</option>';
$subject     = '<option value="0">' . $lang["all"] . '</option>';
while ( $data        = $d->fetch( $q ) )
{
    if ( $data['sub'] == 0 )
    {
        $core .= '<option value="' . $data["id"] . '">' . $data["name"] . '</option>';
    }
    else
    {
        $subject .= '<option value="' . $data["id"] . '">' . $data["name"] . '</option>';
    }
}
$core    = '<select class="select2" name="search[core]" size="1">' . $core . '</select>';
$subject = '<select class="select2" name="search[subject]" size="1">' . $subject . '</select>';
if ( isset( $_GET['first'] ) && is_numeric( $_GET['first'] ) )
{
    saveconfig( 'mainpagepost', $_GET['first'] );
}
if ( isset( $_POST['method'] ) && !empty( $_POST['listids'] ))
{
    $_POST['list'] = explode( ',', $_POST['listids'] );
    if ( $_POST['method'] == 'delete' )
    {

        foreach ( $_POST['list'] as $id )
        {
            if ( !is_numeric( $id ) )
            {
                continue;
            }
            $id = (is_numeric( $id )) ? $id : header( "LOCATION: login.php?logout=true" );
            $id = (is_numeric( $id )) ? $id : die();
            $d->Query( "DELETE FROM `data` WHERE `id`='$id' LIMIT 1" );
            $d->Query( "DELETE FROM `comments` WHERE `p_id`='$id'" );
            $d->Query( "DELETE FROM `catpost` WHERE `pid`='$id'" );
            $d->Query( "DELETE FROM `keys_join` WHERE `post_id`='$id'" );
        }
    }
    if ( $_POST['method'] == 'draft' )
    {
        foreach ( $_POST['list'] as $id )
        {
            $d->Query( "UPDATE `data` SET `show`='4' WHERE `id`='$id' LIMIT 1" );
        }
    }
}
if ( isset( $_GET['deletepost'] ) )
{
    $every = ($permissions['editotherposts'] == 1) ? ' ' : " AND `author`='{" . $info->userid . "}' ";
    $id    = (!is_numeric( @$_GET['id'] )) ? die( "access denied" ) : $_GET['id'];
    $d->Query( "DELETE FROM `data` WHERE id='$id' " . $every . " LIMIT 1" );
}
if ( is_numeric( @$_GET['movedown'] ) )
{
    $wqu     = ($permissions['editotherposts'] == 1) ? " " : " AND `author`='" . $info->userid . "' ";
    $qu      = $d->Query( "SELECT `pos`,`id` FROM `data` WHERE `pos`<'{$_GET['movedown']}' $wqu ORDER BY `pos` DESC LIMIT 1" );
    $qu      = $d->fetch( $qu );
    $lowerid = $qu['id'];
    $qu      = $qu['pos'];
    if ( !empty( $qu ) )
    {
        $d->Query( "UPDATE `data` SET `pos`='$qu' WHERE `pos`='{$_GET['movedown']}' $wqu LIMIT 1" );
        $d->Query( "UPDATE `data` SET `pos`='{$_GET['movedown']}' WHERE `id`='$lowerid' $wqu LIMIT 1" );
    }
    header( 'Location: postmgr.php' );
    exit( 'redirect' );
}
elseif ( is_numeric( @$_GET['moveup'] ) )
{
    $wqu    = ($permissions['editotherposts'] == 1) ? " " : " AND `author`='" . $info->userid . "' ";
    $qu     = $d->Query( "SELECT `pos`,`id` FROM `data` WHERE `pos`>'{$_GET['moveup']}' $wqu ORDER BY `pos` ASC LIMIT 1" );
    $qu     = $d->fetch( $qu );
    $uperid = $qu['id'];
    $qu     = $qu['pos'];
    if ( !empty( $qu ) )
    {
        $d->Query( "UPDATE `data` SET `pos`='$qu' WHERE `pos`='{$_GET['moveup']}' $wqu LIMIT 1" );
        $d->Query( "UPDATE `data` SET `pos`='{$_GET['moveup']}' WHERE `id`='$uperid' $wqu LIMIT 1" );
    }
    header( 'Location: postmgr.php' );
    exit( 'redirect' );
}
//search process
$search = false;
if ( isset( $_GET['reset'] ) )
{
    $search = false;
    setcookie( 'search', '', time() - 60 * 60 );
    //remove default post
    saveconfig( 'mainpagepost', 0 );
    saveconfig( 'mainpagetheme', 'first.htm' );
}
if ( isset( $_COOKIE['search'] ) && !isset( $_GET['reset'] ) )
    $search = true;

if ( isset( $_POST['searching'] ) && isset( $_POST['search'] ) )
{
    setcookie( 'search', '', time() - 60 * 60 );
    $search            = serialize( $_POST['search'] );
    setcookie( 'search', $search, time() + 60 * 60 );
    $_COOKIE['search'] = $search;
    $search            = true;
}

if ( $search )
{
    $search = str_replace( '\\"', '"', $_COOKIE['search'] );
    $search = unserialize( $search );
    $sq     = ' ';
    $sq .= (is_numeric( @$search['show'] ) && @$search['show'] != 0) ? 'AND `show`=' . $search['show'] : ' ';
    $sq .= (is_numeric( @$search['core'] ) && @$search['core'] != 0) ? ' AND `cat_id`=' . $search['core'] : ' ';
    if ( is_numeric( @$search['subject'] ) && @$search['subject'] != 0 )
    {
        $t = $d->Query( "SELECT * FROM `catpost` WHERE `catid`='{$search['subject']}'" );
        if ( $d->getrows( $t ) > 0 )
        {
            $sq .=' AND ( `id`=';
            $first = true;
            while ( $tdata = $d->fetch( $t ) )
            {
                $sq .= ($first) ? '' : " OR `id`=";
                $sq .= $tdata['pid'];
                $first = false;
            }
            $sq .=') ';
        }
    }

    //date and time limitation
    $from = jmaketime( $search['timebox1']['hour'], 0, 0, $search['timebox1']['month'], $search['timebox1']['day'], $search['timebox1']['year'] );
    $upto = jmaketime( $search['timebox2']['hour'], 0, 0, $search['timebox2']['month'], $search['timebox2']['day'], $search['timebox2']['year'] );
    $sq .= " AND (`date` BETWEEN " . $from . " AND " . $upto . ") ";
    //end date and time limitation
    if ( !empty( $search['text'] ) )
    {
        $sq .= " AND ";
        $text         = safe( $search['text'], 1 );
        $text         = trim( htmlspecialchars( $text ) );
        $text         = str_replace( "&amp;", "&", $text );
        $text         = str_replace( "&#1740;", "&#1610;", $text );
        $star         = '%';
        $and          = '';
        $split_search = array();
        $split_search = explode( " ", $text );
        for ( $i = 0, $max = count( $split_search ); $i < $max; $i++ )
        {
            $sq .= $and . "(`title` LIKE '" . $star . $split_search[$i] . $star . "'  or `text` LIKE '" . $star . $split_search[$i] . $star . "' or `full` LIKE '" . $star . $split_search[$i] . $star . "')";
            $and = " AND ";
        }
    }
    if ( !empty( $search['title'] ) )
    {
        $sq .= " AND ";
        $title        = safe( $search['title'], 1 );
        $title        = trim( htmlspecialchars( $title ) );
        $title        = str_replace( "&amp;", "&", $title );
        $title        = str_replace( "&#1740;", "&#1610;", $title );
        $star         = '%';
        $and          = '';
        $split_search = array();
        $split_search = explode( " ", $title );
        for ( $i = 0, $max = count( $split_search ); $i < $max; $i++ )
        {
            $sq .= $and . "(`title` LIKE '" . $star . $split_search[$i] . $star . "')";
            $and = " AND ";
        }
    }

    if ( !empty( $search['username'] ) && $permissions['editotherposts'] == 1 )
    {
        $UsernameId = $user->GetId( safe( $search['username'], 1 ) );
        $sq .= ' AND `author`=' . $UsernameId;
    }
}
else
    $sq = ' ';

//end search process
if ( isset( $_POST['number'] ) && is_numeric( @$_POST['number'] ) && isset( $_POST['type'] ) && !isset( $_GET['reset'] ) )
{
    $_COOKIE['number'] = $RPP               = ($_POST['number'] > 100) ? 100 : $_POST['number'];
    $_COOKIE['type']   = $type              = ($_POST['type'] == 'ASC') ? 'ASC' : 'DESC';
}
else
{
    $_COOKIE['number'] = $RPP               = (!isset( $_COOKIE['number'] ) || !is_numeric( @$_COOKIE['number'] ) || isset( $_GET['reset'] )) ? 10 : $_COOKIE['number'];
    $_COOKIE['type']   = $type              = (@$_COOKIE['type'] == 'ASC' || isset( $_GET['reset'] )) ? 'ASC' : 'DESC';
}
setcookie( 'number', $RPP );
setcookie( 'type', $type );
$CurrentPage = (!isset( $_GET['page'] ) || !is_numeric( @$_GET['page'] ) || (abs( @$_GET['page'] ) == 0)) ? 1 : abs( $_GET['page'] );


$From = ($CurrentPage - 1) * $RPP;
$q    = ($permissions['editotherposts'] == 1) ? " OR `author`!='$info[u_id]'" : " ";
$q    = $d->query( "SELECT * FROM `data`  WHERE (`author`='$info[u_id]' $q) $sq ORDER BY `pos` $type,`id` DESC LIMIT $From,$RPP" );
while ( $data = $d->fetch( $q ) )
{
    $ainfo = $user->info( $data['author'] );
    $stat  = $data['show'];
    $stats = array( '', $lang['usuall'], $lang['attached'], $lang['archive'], $lang['draft'], $lang['hiddenlist'] );
    $stat  = $stats[$stat];
    $tpl->block( 'listtr', array( 'NewsTitle' => $data['title'], 'NewsCom' => $data['num'], 'NewsAuthor' => $ainfo['showname'], 'NewsDate' => mytime( $config['dtype'], $data['date'], $config['dzone'] ), 'Newsposid' => $data['pos'], 'NewsId' => $data['id'], 'NewsStat' => $stat ) );
}
if ( $permissions['editotherposts'] == 1 )
{
    $q = $d->getrows( "SELECT `id` FROM `data` WHERE 1=1 $sq ", true );
}
else
{
    $q = $d->getrows( "SELECT `id` as `num` FROM `data`  WHERE `author`='$info[u_id]' $sq ", true );
}

CMSpage( $q, $RPP, 5, $CurrentPage, $tpl, 'pages', 'postmgr.php?' );

$tpl->assign( array(
    'sitetitle'  => $config['sitetitle'],
    'todayfarsi' => cutime,
    'fullname'   => $info['name'],
    'usercolor'  => $info['color'],
    'ip'         => @$_SERVER['REMOTE_ADDR'],
    'timebox1'   => timeboxgen( 'search[timebox1]', 13, 10, (jdate( 'Y' ) - 5 ) ),
    'timebox2'   => timeboxgen( 'search[timebox2]' ),
    'core'       => $core,
    'subject'    => $subject,
) );
$htpl->showit();
$tpl->showit();
$ftpl->showit();

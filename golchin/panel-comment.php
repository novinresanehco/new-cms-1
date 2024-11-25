<?php

define( 'head', true );
define( 'page', 'comment' );
$pageTheme = 'comment.htm';
define( 'tabs', true );
$pagetitle = 'مديريت نظرات';
$tabs = array( 'نظرات', 'ویژه', array( 'title' => 'مدیریت نظرات', 'class' => 'disabled' ) );
include('header.php');
$html = new html();
$pq = ($permissions['editotherposts'] != 1) ? " WHERE `author`='$info[u_id]'" : "";
$q = $d->Query( "SELECT `id`,`title` FROM `data` $pq ORDER by `pos`" );
while ( $data = $d->fetch( $q ) )
{
    $pdata[$data['id']] = '';
    $tpl->block( "listposts", array( "pid" => $data['id'], "postname" => $data['title'] ) );
}
$qs = '';
$type = 3;
if ( isset( $_GET['type'] ) )
{
    $type = $_GET['type'];
    $qs = ($type == '0' || $type == '1' || $type == '2') ? " WHERE `active`='$type' " : "";
}
if ( is_numeric( @$_GET['pid'] ) )
{
    $pid = $_GET['pid'];
    $qs = (empty( $qs )) ? " WHERE `p_id`='$pid' " : " AND `p_id`='$pid' ";
}
$html->ts( "ctype", $type, 3, 0 );
$RPP = $config['nlast'];
$CurrentPage = (!isset( $_GET['page'] ) || !is_numeric( @$_GET['page'] ) || (abs( @$_GET['page'] ) == 0)) ? 1 : abs( $_GET['page'] );
$From = ($CurrentPage - 1) * $RPP;
$q = $d->Query( "SELECT * FROM `comments` $qs ORDER by `date` LIMIT $From,$RPP " );
while ( $data = $d->fetch( $q ) )
{
    if ( !isset( $pdata[$data['p_id']] ) )
        continue;
    $qu = $d->Query( "SELECT `title` FROM `data` WHERE `id`='{$data['p_id']}'" );
    $qu = $d->fetch( $qu );
    if ( $data['memberid'] != '-1' )
    {
        $sender_info = $user->info( false, $data['memberid'] );
        $sender_user = $sender_info['user'];
        $sender_name = $lang['users']['member'];
    }
    else
    {
        $sender_user = 'guest';
        $sender_name = $lang['users']['guest'];
    }
    $ans = '';
    if ( !empty( $data['ans'] ) )
    {
        $replier_info = $user->info( false, $data['ansid'] );
        $replier_name = $replier_info['name'];
        $ans = '
<div class=reply style="display: block;"><b>' . $replier_name . ' : </b> <br>
<font color="#FF0000"><b>»</b></font> ' . $data['ans'] . '</div>';
        $tpl->assign( 'reply', $ans );
    }
    $tpl->block( "comment", array(
        "id" => $data['c_id'],
        "pnum" => $data['p_id'],
        "posttitle" => $qu['title'],
        "email" => $data['email'],
        "name" => $data['c_author'],
        "website" => $data['url'],
        "member_name" => $sender_name,
        "member_user" => $sender_user,
        "reply" => $ans,
        "reply_txt" => $data['ans'],
        "comment_date" => mytime( $config['dtype'], $data['date'], $config['dzone'] ),
        "comment" => $data['text'],
    ) );
}
$q = $d->getrows( "SELECT `c_id` FROM `comments` $qs", true );
$con = "comment.php?do&";
$con = (!empty( $qs )) ? $con . "type=" . $type . "&" : $con;
$con = (isset( $pid )) ? $con . "&pid=" . $pid . "&" : $con;
CMSpage( $q, $RPP, 5, $CurrentPage, $tpl, 'pages', $con );
$htpl->showit();
$tpl->showit();
$ftpl->showit();
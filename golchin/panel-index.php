<?php

$pageTheme = 'admin.htm';
define( 'crrentver', '3.0.0.0' );
define( "head", true );
define( 'page', 'dashboard' );
$pagetitle = 'مدیریت سیستم';
define( 'tabs', true );
$tabs = array( 'نخست', 'آخرین نظرات', 'آمار بازدید' );
include('header.php');
$q = $d->Query( "SELECT * FROM `comments` ORDER by `date` LIMIT 10" );
while ( $data = $d->fetch( $q ) )
{
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
    $tpl->block( "comment", array(
        "id" => $data['c_id'],
        "pid" => $data['p_id'],
        "post" => $qu['title'],
        "email" => $data['email'],
        "name" => $data['c_author'],
        "site" => $data['url'],
        "member_name" => $sender_name,
        "member_user" => $sender_user,
        "comment_date" => mytime( $config['dtype'], $data['date'], $config['dzone'] ),
        "comment" => nl2br( $data['text'] ),
    ) );
}
$q = $d->Query( "SELECT `stat` FROM `plugins` WHERE `name`='counter' lIMIT 1" );
if ( $d->getrows( $q ) <= 0 )
{
    $tpl->assign( 'DisabledCounter', true );
}
else
{
    $data = $d->fetch( $q );
    if ( $data['stat'] == '3' )
    {
        $tpl->assign( 'DisabledCounter', true );
    }
    else
    {
        require_once(pluginsdir . 'counter/count.php');
    }
    if ( $data['stat'] == 1 )
    {
        $counter = CMScounter();
        $tpl->assign( array(
            'total_news' => $counter['totalpost'],
            'today' => $counter['todayv'],
            'yes' => $counter['yesterdayv'],
            'total' => $counter['totalv'],
            'ons' => $counter['onlines'],
            'month' => $counter['monthv'],
            'pmonth' => $counter['lastmonthv'],
            'year' => $counter['yearv'],
            'pyear' => $counter['lastyearv'],
            'tmem' => $counter['member'],
            'ncom' => $counter['totalcom'],
                )
        );
    }
}
//تعداد مطالب منتظر تایید کاربران
if ( isActivePlugin( 'sendpost' ) )
{
    $sendpostPending = $d->Query( 'SELECT COUNT(*) as `total` FROM `data` WHERE `sendpost`=1 AND `show`=4' );
    $sendpostPending = $d->fetch( $sendpostPending );
    $sendpostPending = $sendpostPending['total'];
    $tpl->assign( 'sendpostPending', $sendpostPending );
}
//تعداد کل لینک ها
if ( isActivePlugin( 'simplelink' ) )
{
    $countLinks = $d->Query( 'SELECT COUNT(*) as `total` FROM `link`' );
    $countLinks = $d->fetch( $countLinks );
    $countLinks = $countLinks['total'];
    $tpl->assign( 'countLinks', $countLinks );
}
//نظر سنجی های فعال
if ( isActivePlugin( 'poll' ) )
{
    $activePolls = $d->Query( 'SELECT COUNT(*) as `total` FROM `polls` WHERE `status`=\'active\'' );
    $activePolls = $d->fetch( $activePolls );
    $activePolls = $activePolls['total'];
    $tpl->assign( 'activePolls', $activePolls );
}
//پیام های خوانده نشده
$unreadMessages = $d->Query( 'SELECT COUNT(*) as `total` FROM `msg` WHERE `reade`=0' );
$unreadMessages = $d->fetch( $unreadMessages );
$unreadMessages = $unreadMessages['total'];
$tpl->assign( 'unreadMessages', $unreadMessages );
//تعداد نظرات تایید نشده
if ( hasPermission( 'comment' ) )
{
    $pendingComments = $d->Query( 'SELECT COUNT(*) as `total` FROM `comments` WHERE `active`=0' );
    $pendingComments = $d->fetch( $pendingComments );
    $pendingComments = $pendingComments['total'];
    $tpl->assign( 'pendingComments', $pendingComments );
}
//تعدا کل پست ها
$totalPosts = $d->Query( 'SELECT COUNT(*) as `total` FROM `data`' );
$totalPosts = $d->fetch( $totalPosts );
$totalPosts = $totalPosts['total'];
$tpl->assign( 'totalPosts', $totalPosts );
//تعدا کل دسته ها
$totalCategories = $d->Query( 'SELECT COUNT(*) as `total` FROM `cat`' );
$totalCategories = $d->fetch( $totalCategories );
$totalCategories = $totalCategories['total'];
$tpl->assign( 'totalCategories', $totalCategories );
//تعداد مدیران
$totalAdmins = $d->Query( 'SELECT COUNT(*) as `total` FROM `member` WHERE `u_id` IN ( SELECT `u_id` FROM `permissions` WHERE `access_admin_area`=1)' );
$totalAdmins = $d->fetch( $totalAdmins );
$totalAdmins = $totalAdmins['total'];
$tpl->assign( 'totalAdmins', $totalAdmins );
//تعداد ایمیلهای ثبت شده در خبرنامه
$mailingListMembers = $d->Query( 'SELECT COUNT(*) as `total` FROM `nl`' );
$mailingListMembers = $d->fetch( $mailingListMembers );
$mailingListMembers = $mailingListMembers['total'];
$tpl->assign( 'mailingListMembers', $mailingListMembers );

$htpl->showit();
$tpl->showit();
$ftpl->showit();
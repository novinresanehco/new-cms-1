<?php

/////////////////////////////////////////////////////////////////////
// module name:     Autolinker
// module Version:  1.1.0
// Designer Name:   Sheikh Ali Shahi (BestTools.ir)
// Website:         www.besttools.ir
// Blog:            www.blog.besttools.ir
// Contact:         http://www.besttools.ir/contact/?t=autolinker1
// Designer SMS:    +98 935 935 9833
// License:         GPL/GNU (Free)
// Support:         http://rashcms.com/forum/forum-14.html
/////////////////////////////////////////////////////////////////////
define( 'head', true );
define( 'autolinker', 'block' );
define( 'page', 'autolinker' );
$pageTheme = 'autolinker.htm';
$pagetitle = 'تبادل لینک هوشمند';
define( 'tabs', true );
$tabs = array( 'مدیریت لینک ها', 'تنظیمات' );
include('header.php');
$ver = '1.1.0';
$html = new html();
global $lang;
include('../plugins/autolinker/fa.php');
$msgs = "";
if ( isset( $_GET['del'] ) )
{
    if ( is_numeric( $_GET['del'] ) and $d->getrows( "SELECT `id` FROM `autolinker` WHERE `id`='$_GET[del]' LIMIT 1", true ) > 0 )
    {
        $q = $d->Query( "DELETE FROM `autolinker` WHERE `id`='$_GET[del]' LIMIT 1" );
        $msgs .='<div align="center" dir="rtl" style="border: 6px solid #00FF00"><font color="black" size="2">' . $language['dellinker'] . '</font></div>';
    }
}
if ( isset( $_GET['ban'] ) )
{
    if ( is_numeric( $_GET['ban'] ) and $d->getrows( "SELECT `id` FROM `autolinker` WHERE `id`='$_GET[ban]' and `stats`!='0' LIMIT 1", true ) > 0 )
    {
        $d->Query( "UPDATE `autolinker` SET `stats`='0' WHERE `id`='$_GET[ban]' LIMIT 1" );
        $msgs .= '<div align="center" dir="rtl" style="border: 6px solid #00FF00"><font color="black" size="2">' . $language['banlinker'] . '</font></div>';
    }
}
if ( isset( $_GET['dis'] ) )
{
    if ( is_numeric( $_GET['dis'] ) and $d->getrows( "SELECT `id` FROM `autolinker` WHERE `id`='$_GET[dis]' and `stats`!='3' LIMIT 1", true ) > 0 )
    {
        $d->Query( "UPDATE `autolinker` SET `stats`='3' WHERE `id`='$_GET[dis]' LIMIT 1" );
        $msgs .= '<div align="center" dir="rtl" style="border: 6px solid #00FF00"><font color="black" size="2">' . $language['dislinker'] . '</font></div>';
    }
}
if ( isset( $_GET['hid'] ) )
{
    if ( is_numeric( $_GET['hid'] ) and $d->getrows( "SELECT `id` FROM `autolinker` WHERE `id`='$_GET[hid]' and `stats`!='4' LIMIT 1", true ) > 0 )
    {
        $d->Query( "UPDATE `autolinker` SET `stats`='4' WHERE `id`='$_GET[hid]' LIMIT 1" );
        $msgs .= '<div align="center" dir="rtl" style="border: 6px solid #00FF00"><font color="black" size="2">' . $language['hidlinker'] . '</font></div>';
    }
}
if ( isset( $_GET['sho'] ) )
{
    if ( is_numeric( $_GET['sho'] ) and $d->getrows( "SELECT `id` FROM `autolinker` WHERE `id`='$_GET[sho]' LIMIT 1", true ) > 0 )
    {
        $tim = time();
        $d->Query( "UPDATE `autolinker` SET `update`='$tim',`stats`='1' WHERE `id`='$_GET[sho]' LIMIT 1" );
        $msgs .= '<div align="center" dir="rtl" style="border: 6px solid #00FF00"><font color="black" size="2">' . $language['showlinker'] . '</font></div>';
    }
}

$tpl->assign( 'msgs', $msgs, 1 );

//$tpl->assign( 'ver', $ver, 1 );
$q = $d->Query( "SELECT * FROM `autolinkerset`" );
while ( $data = $d->fetch( $q ) )
{
    $ch = 'selected';
    $chlist1 = ($data['list'] == 1) ? $ch : '';
    $chlist0 = ($data['list'] == 1) ? '' : $ch;
    $chf1 = ($data['fstats'] == 1) ? $ch : '';
    $chf0 = ($data['fstats'] == 1) ? '' : $ch;
    $tpl->block( "setlinker", array(
        'title' => $data['title'],
        'url' => $data['url'],
        'time2' => $data['time2'],
        'time3' => $data['time3'],
        'timeban' => $data['bantime'],
        'list' => $data['list'],
        'sform' => $data['fstats'],
        'sformch1' => $chf1,
        'sformch0' => $chf0,
        'listch1' => $chlist1,
        'listch0' => $chlist0,
    ) );

    $q = $d->Query( "SELECT * FROM `autolinker`" );
    while ( $ldata = $d->fetch( $q ) )
    {

        $tim2 = $data['time2'] * 86400; //*24 huor
        $tim3 = $data['time3'] * 86400; //*24 huor
        $st1 = '';

        $st1 = $st2 = '';
        if ( $ldata['stats'] == 0 )
        {
            $st1 = '<span style="background-color: #FF8C8C" lang="fa">&nbsp;مسدود &nbsp;&nbsp;&nbsp;</span>';
        }
        if ( $ldata['stats'] == 1 )
        {
            $s = 1;
            if ( $ldata['update'] + $tim2 < time() )
            {
                $st1 = '<span style="background-color: #C0C0C0" lang="fa">&nbsp;غیر فعال </span>';
                $s = 0;
            }
            if ( $ldata['update'] + $tim3 < time() )
            {
                $st1 = '<span style="background-color: #FFCC66" lang="fa">&nbsp;مخفی </span>';
                $s = 0;
            }
            if ( $s == 1 )
            {
                $st1 = '<span style="background-color: #00FF00" lang="fa">&nbsp;فعال &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>';
            }
        }
        if ( $ldata['stats'] == 3 )
        {
            $st1 = '<span style="background-color: #C0C0C0" lang="fa">&nbsp;غیر فعال &nbsp;</span>';
            $s = 0;
        }
        if ( $ldata['stats'] == 4 )
        {
            $st1 = '<span style="background-color: #00FFFF" lang="fa">&nbsp;در انتظار &nbsp;&nbsp;</span>';
        }

        $updat = empty( $ldata['update'] ) ? '<span style="background-color: #00FFFF" lang="fa">&nbsp;در انتظار &nbsp;&nbsp;</span>' : jdate( "Y/m/d | H:m", $ldata['update'] );

        $tpl->block( "linkerS", array(
            'id' => $ldata['id'],
            'user' => $ldata['user'],
            'cdate' => jdate( "Y/m/d | H:m", $ldata['cdate'] ),
            'update' => $updat,
            'title' => $ldata['title'],
            'url' => $ldata['url'],
            'desc' => $ldata['desc'],
            'ref' => $ldata['ref'],
            'stats1' => $st1,
            'stats2' => $st2,
            'site' => $config['site'],
            'lin' => @$ida,
        ) );
    }
}

$htpl->showit();
$tpl->showit();
$ftpl->showit();
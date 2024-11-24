<?php

if ( !defined( 'plugins-inc' ) OR !is_array( @$data ) )
{
    die( '<a href="http://help.mihanphp.com/plugins" target=_blank>samaneh</a> :: Invalid calling of ' . basename( __FILE__ ) );
}
$all = false;

function counter_output()
{
    global $all;
    $itpl    = new samaneh();
    $itpl->load( 'plugins/counter/block-theme.html' );
    $itpl->assign( 'theme_url', core_theme_url );
    require_once(dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'count.php');
    $counter = CMScounter();

    $args = func_get_args();
    $args = @$args[0];
    global $tpl;
    $tpl->assign( $args );
    if ( @$args['Ctoday'] == 1 OR $all )
    {
        $itpl->assign( 'todayArea', true );
    }
    if ( @$args['Cyesterday'] == 1 OR $all )
    {
        $itpl->assign( 'yesterdayArea', true );
    }
    if ( @$args['Cmonth'] == 1 OR $all )
    {
        $itpl->assign( 'monthArea', true );
    }
    if ( @$args['Clastmonth'] == 1 OR $all )
    {
        $itpl->assign( 'pmonthArea', true );
    }
    if ( @$args['Cyear'] == 1 OR $all )
    {
        $itpl->assign( 'yearArea', true );
    }
    if ( @$args['Clastyear'] == 1 OR $all )
    {
        $itpl->assign( 'pyearArea', true );
    }
    if ( @$args['Ctotal'] == 1 OR $all )
    {
        $itpl->assign( 'totalArea', true );
    }
    if ( @$args['Cnews'] == 1 || @$args['Ccomments'] == 1 || @$args['Crss'] == 1 OR $all )
    {
        $itpl->assign( 'siteInfo', true );
        if ( @$args['Cnews'] == 1 OR $all )
        {
            $itpl->assign( 'newsArea', true );
        }
        if ( @$args['Ccomments'] == 1 OR $all )
        {
            $itpl->assign( 'commentsArea', true );
        }
        if ( @$args['Crss'] == 1 OR $all )
        {
            $itpl->assign( 'rssArea', true );
        }
    }
    if ( @$args['Cmembers'] == 1 || @$args['Conlines'] == 1 OR $all )
    {
        $itpl->assign( 'memberInfo', true );
        if ( @$args['Cmembers'] == 1 OR $all )
        {
            $itpl->assign( 'totalmembersArea', true );
        }
        if ( @$args['Conlines'] == 1 OR $all )
        {
            $itpl->assign( 'onlinesArea', true );
        }
    }
    $itpl->assign( array(
        'total_news' => $counter['totalpost'],
        'today'      => $counter['todayv'],
        'yes'        => $counter['yesterdayv'],
        'total'      => $counter['totalv'],
        'ons'        => $counter['onlines'],
        'month'      => $counter['monthv'],
        'pmonth'     => $counter['lastmonthv'],
        'year'       => $counter['yearv'],
        'pyear'      => $counter['lastyearv'],
        'tmem'       => $counter['member'],
        'ncom'       => $counter['totalcom'],
            )
    );
    return $itpl->dontshowit();
}

$all = true;
$tpl->assign( 'counter', counter_output() );
$all = false;

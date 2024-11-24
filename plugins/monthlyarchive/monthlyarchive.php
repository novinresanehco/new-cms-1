<?php

if ( !defined( 'plugins-inc' ) OR !is_array( @$data ) )
{
    die( '<a href="http://help.mihanphp.com/plugins" target=_blank>samaneh</a> :: Invalid calling of ' . basename( __FILE__ ) );
}

function monthlyarchive_output()
{
    global $tpl, $d, $config;
    $itpl = new samaneh();
    $itpl->load( 'plugins/monthlyarchive/block-theme.html' );
    $itpl->assign( 'theme_url', 'theme/core/' . $config['theme'] . '/' );
    $ctimestamp = time();
    $years = $d->Query( "SELECT `year` FROM `data` WHERE `date` <= '$ctimestamp' AND (`expire`='0' OR `expire`='' OR `expire`>$ctimestamp) AND (`show`!='4' || `show`='2') GROUP BY `year`" );
    while ( $year = $d->fetch( $years ) )
    {
        $year = $year['year'];
        $months = $d->Query( "SELECT `month` FROM `data` WHERE `date` <= '$ctimestamp' AND (`expire`='0' OR `expire`='' OR `expire`>$ctimestamp) AND (`show`!='4' || `show`='2') AND `year`='$year' GROUP BY `month`" );
        while ( $month = $d->fetch( $months ) )
        {
            $month = $month['month'];
            $itpl->block( 'monthlyarchive', array( 'url' => $config['site'] . "archive/$year/$month.php", 'title' => get_month( $month ) . ' ' . $year ) );
        }
    }
    return $itpl->dontshowit();
}

$tpl->assign( 'monthlyarchive', monthlyarchive_output() );
if ( isset( $_GET['plugins'] ) && $_GET['plugins'] == 'monthlyarchive' && isset( $_GET['year'] ) && isset( $_GET['month'] ) && is_numeric( $_GET['year'] ) && is_numeric( $_GET['month'] ) )
{
    @$type = ($type != 'DESC' && $type != 'ASC') ? 'ASC' : $type;
    @$RPP = (!is_numeric( $RPP )) ? 10 : abs( $RPP );
    $CurrentPage = (!isset( $_GET['page'] ) || !is_numeric( @$_GET['page'] ) || (abs( @$_GET['page'] ) == 0)) ? 1 : abs( $_GET['page'] );
    $From = ($CurrentPage - 1) * $RPP;
    @$From = (!is_numeric( $From )) ? 1 : abs( $From );
    if ( !defined( 'custom_p_url' ) )
    {
        define( 'custom_p_url', true );
    }
    $ctimestamp = time();
    define( 'customized_post_query', true );
    $From = 0;
    $post_q = "select * FROM `data` WHERE  `date` <= '$ctimestamp' AND (`expire`='0' OR `expire`='' OR `expire`>'$ctimestamp') AND `year`='$_GET[year]' AND `month`='$_GET[month]' order by `id` $type LIMIT $From,$RPP";
    define( 'customized_post_query_value', $post_q );
    $t_pr = $d->getrows( "select `id` FROM `data` WHERE  `date` <= '$ctimestamp' AND (`expire`='0' OR `expire`='' OR `expire`>'$ctimestamp') AND `year`='$_GET[year]' AND `month`='$_GET[month]' order by `id` $type", true );
    define( 'customized_post_query_value_t', $t_pr );
}

function get_month( $i )
{
    $month = array( '', 'فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور', 'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسفند' );
    if ( empty( $month[$i] ) )
    {
        return '---';
    }
    return $month[$i];
}
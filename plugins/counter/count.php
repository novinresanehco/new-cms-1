<?php

/* * *************************************************************************
 *                                  CMS CMS
 *                          -------------------
 *   copyright            : (C) 2009 The samaneh  $Team = "www.samaneh.com";
 *   email                : info@samaneh.com
 *   email                : samaneh@gmail.com
 *   programmer           : Reza Shahrokhian
 * ************************************************************************* */
//         Security
if ( !defined( 'news_security' ) )
{
    die( "You are not allowed to access this page directly!" );
}

//
function CMSnewCount( $total, $todaycounts, $yescounts, $month, $year, $lastyear, $cudate, $lastmonth )
{
    global $d;
    $cdata = array( 'total' => $total, 'lastmonth' => $lastmonth, 'todaycounts' => $todaycounts, 'yescounts' => $yescounts, 'month' => $month, 'year' => $year, 'lastyear' => $lastyear, 'cdate' => $cudate );
    $d->uquery( "counter", $cdata );
}

function CMScounter()
{
    global $d;
    $query = $d->Query( "SELECT * FROM counter" );
    $row = $d->fetch( $query );
    List( $year, $month, $day ) = explode( "-", $row['cdate'] );
    list( $CurrentYear, $CurrentMonth, $CurrentDay ) = explode( "-", date( "Y-m-d" ) );
    $cudate = date( "Y-m-d" );
    $total = empty( $row['total'] ) ? 0 : $row['total'];
    $todaycounts = empty( $row['todaycounts'] ) ? 0 : $row['todaycounts'];
    $yescounts = empty( $row['yescounts'] ) ? 0 : $row['yescounts'];
    $monthcounts = empty( $row['month'] ) ? 0 : $row['month'];
    $yearcounts = empty( $row['year'] ) ? 0 : $row['year'];
    $lastyearcounts = empty( $row['lastyear'] ) ? 0 : $row['lastyear'];
    $lastmonthcounts = empty( $row['lastmonth'] ) ? 0 : $row['lastmonth'];

    if ( !defined( 'admin' ) && (!defined( "ajax_head" )) && !isset( $_SESSION['counted'] ) )
    {
        ++$total;
        $_SESSION['counted'] = true;
        if ( $CurrentYear == $year )
        {
            if ( $CurrentMonth == $month )
            {
                if ( $CurrentDay == $day )
                {
                    define( 'counted', true );
                    ++$todaycounts;
                    ++$monthcounts;
                    ++$yearcounts;
                    CMSnewCount( $total, $todaycounts, $yescounts, $monthcounts, $yearcounts, $lastyearcounts, $cudate, $lastmonthcounts );
                }//End of $CurrentDay == $day
                if ( $CurrentDay == ++$day )
                {
                    define( 'counted', true );
                    $yescounts = $todaycounts;
                    $todaycounts = 1;
                    ++$monthcounts;
                    ++$yearcounts;
                    CMSnewCount( $total, $todaycounts, $yescounts, $monthcounts, $yearcounts, $lastyearcounts, $cudate, $lastmonthcounts );
                }
            }//End of $CurrentMonth == $month
            if ( $CurrentMonth == ++$month )
            {
                define( 'counted', true );
                $lastmonthcounts = $monthcounts;
                $monthcounts = 1;
                ++$todaycounts;
                ++$yearcounts;
                CMSnewCount( $total, $todaycounts, $yescounts, $monthcounts, $yearcounts, $lastyearcounts, $cudate, $lastmonthcounts );
            }
        }//END OF Year...
        if ( $CurrentYear == ++$year )
        {
            define( 'counted', true );
            $lastyearcounts = $yearcounts;
            $yearcounts = 1;
            ++$monthcounts;
            ++$todaycounts;
            CMSnewCount( $total, $todaycounts, $yescounts, $monthcounts, $yearcounts, $lastyearcounts, $cudate, $lastmonthcounts );
        }
    }//END of !defined('admin')
    if ( !defined( 'counted' ) && !defined( 'admin' ) )
    {
        CMSnewCount( $total, $todaycounts, $yescounts, $monthcounts, $yearcounts, $lastyearcounts, $cudate, $lastmonthcounts );
    }

    $session = safe( $_SERVER['REMOTE_ADDR'] );
    $time = time();
    $time_c = $time - 600;
    $d->Query( "DELETE FROM `onlines` WHERE time < $time_c" );
    $q = $d->Query( "SELECT COUNT(*) as issess FROM onlines  WHERE session='$session'" );
    $q = $d->fetch( $q );
    if ( $q['issess'] == 0 )
    {
        $d->Query( "INSERT INTO `onlines` (session, time) VALUES ('$session', '$time')" );
    }
    $onlines = $d->getrows( "SELECT session FROM `onlines`", true );
    $t_mem = $d->getrows( "SELECT `u_id` FROM member", true );
    $t_new = $d->getrows( "SELECT `id` as num FROM data", true );
    $Com = $d->GetRowValue( 'total', "SELECT SUM(`num`) as `total` FROM `data` WHERE `num` > 0", true );
    $Com = (empty( $Com )) ? 0 : $Com;
    return array( "onlines" => $onlines, "todayv" => $todaycounts, "yesterdayv" => $yescounts, "totalv" => $total, "lastmonthv" => $lastmonthcounts, "lastyearv" => $lastyearcounts, "monthv" => $monthcounts, "yearv" => $yearcounts, "member" => $t_mem, "totalpost" => $t_new, "totalcom" => $Com );
}
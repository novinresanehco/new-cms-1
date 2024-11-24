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

function timeboxgen( $id = '', $yearp = 13, $yearn = 0, $defaultYear = null )
{
    $dfun = defined( 'notfarsi' ) ? 'date' : 'jdate';
    if ( !defined( 'thishour' ) )
    {
        $thishour = $dfun( 'G' );
    }
    else
    {
        $thishour = thishour;
    }
    if ( !defined( 'thismonth' ) )
    {
        $thismonth = $dfun( 'm' );
    }
    else
    {
        $thismonth = thismonth;
    }
    if ( !defined( 'thisyear' ) )
    {
        $thisyear = $dfun( 'Y' );
    }
    else
    {
        $thisyear = thisyear;
    }
    if ( !empty( $defaultYear ) )
    {
        $thisyear = $defaultYear;
    }
    if ( !defined( 'thisday' ) )
    {
        $thisday = $dfun( 'd' );
    }
    else
    {
        $thisday = thisday;
    }
    $Hourb = "<select name='" . $id . "[hour]'  id='" . $id . "_hour' class=\"select2\">";
    for ( $i = 1; $i < 25; $i++ )
    {
        $Hourb.="<option ";
        if ( $thishour == $i )
        {
            $Hourb .=" selected ";
        } $Hourb .="value=$i>$i</option>";
    }
    $Hourb .="</select>";
    $Monthb = "<select name='" . $id . "[month]'  id='" . $id . "_month' class=\"select2\">";
    for ( $i = 1; $i <= 12; $i++ )
    {
        $Monthb.="<option ";
        if ( $thismonth == $i )
        {
            $Monthb .=" selected ";
        } $Monthb .="value=$i>$i</option>";
    }
    $Monthb .="</select>";
    $Yearb = "<select name='" . $id . "[year]'  id='" . $id . "_year' class=\"select2\">";
    for ( $i = $thisyear - $yearn; $i <= $thisyear + $yearp; $i++ )
    {
        $Yearb.="<option ";
        if ( $thisyear == $i )
        {
            $Yearb .=" selected ";
        } $Yearb .="value=$i>$i</option>";
    }
    $Yearb .="</select>";
    $Dayb = "<select name='" . $id . "[day]' id='" . $id . "_day' class=\"select2\">";
    for ( $i = 1; $i <= 31; $i++ )
    {
        $Dayb.="<option ";
        if ( $thisday == $i )
        {
            $Dayb .=" selected ";
        } $Dayb .="value=$i>$i</option>";
    }
    $Dayb .="</select>";
    return $Hourb . ' - ' . $Dayb . ' / ' . $Monthb . ' / ' . $Yearb;
}

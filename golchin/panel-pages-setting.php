<?php

define( 'samanehper', 'setting' );
define( "ajax_head", true );
require("ajax_head.php");

function printpm( $msg = 'waccess', $type = 'error' )
{
    $html = new html();
    global $lang;
    $html->msg( isset( $lang[$msg] ) ? $lang[$msg] : $msg, $type );
    $html->printout( true );
}

$task = (!isset( $_POST['task'] )) ? printpm() : $_POST['task'];
switch ( $task )
{
    case "general":

        $email = (empty( $_POST['email'] )) ? printpm( "allneed" ) : $_POST['email'];
        $site = (empty( $_POST['site'] )) ? printpm( "allneed" ) : $_POST['site'];
        $tries = (!is_numeric( $_POST['tries'] )) ? printpm() : $_POST['tries'];
        $num = (!is_numeric( $_POST['num'] )) ? printpm() : $_POST['num'];
        $num = ($num > 15) ? 15 : $num;
        $nlast = (!is_numeric( $_POST['nlast'] )) ? printpm() : $_POST['nlast'];
        $nlast = ($nlast > 25) ? 25 : $nlast;
        $member = (!is_numeric( $_POST['member_area'] )) ? printpm() : $_POST['member_area'];
        $member = ($member != 1 && $member != 2) ? printpm() : $member;
        $comment = (!is_numeric( $_POST['comment'] )) ? printpm() : $_POST['comment'];
        $comment = ($comment != 1 && $comment != 2 && $comment != 3 && $comment != 4) ? printpm() : $comment;
        $data = array(
            'site' => $site,
            'email' => $email,
            'num' => $num,
            'nlast' => $nlast,
            'tries' => $tries,
            'member' => $member,
            'comment' => $comment,
        );
        foreach ( $data as $name => $value )
            $q = $d->uquery( "config", array( "value" => $value ), " `name`='$name'" );
        printpm( "ok", "success" );
        break;

    case "time":
        $dzone = (empty( $_POST['dzone'] )) ? printpm( "allneed" ) : $_POST['dzone'];
        $dtype = (empty( $_POST['dtype'] )) ? printpm( "allneed" ) : $_POST['dtype'];
        $data = array( );
        $data['dzone'] = $dzone;
        $data['dtype'] = $dtype;
        foreach ( $data as $name => $value )
            $q = $d->uquery( "config", array( "value" => $value ), " `name`='$name'" );
        printpm( "ok", "success" );
        break;
    case "seo":
        $title = (empty( $_POST['title'] )) ? printpm( "allneed" ) : $_POST['title'];
        $keys = (empty( $_POST['keys'] )) ? printpm( "allneed" ) : $_POST['keys'];
        $desc = (empty( $_POST['desc'] )) ? printpm( "allneed" ) : $_POST['desc'];
        $seo = ($_POST['seo'] != '1' && $_POST['seo'] != '2' ) ? printpm() : $_POST['seo'];
        $postlinks = (empty( $_POST['postlinks'] )) ? printpm( "allneed" ) : $_POST['postlinks'];
        $userlink = (empty( $_POST['userlink'] )) ? printpm( "allneed" ) : $_POST['userlink'];
        $subcatlinks = (empty( $_POST['subcatlinks'] )) ? printpm( "allneed" ) : $_POST['subcatlinks'];
        $catlinks = (empty( $_POST['catlinks'] )) ? printpm( "allneed" ) : $_POST['catlinks'];
        $pagelinks = (empty( $_POST['pagelinks'] )) ? printpm( "allneed" ) : $_POST['pagelinks'];
        $taglinks = (empty( $_POST['taglinks'] )) ? printpm( "allneed" ) : $_POST['taglinks'];
        if ( strpos( $postlinks, "%postid%" ) === false )
        {
            printpm( "لینک پست ها می بایست شامل متغیر %postid% باشد." );
        }
        else
        {
            $postlinks = trim( $postlinks, " /" );
            saveconfig( 'postlinks', $postlinks );
            $postlinks = str_replace( array( ".", "(", ")" ), array( "\\.", "", "" ), $postlinks );
            preg_match_all( "#%[a-z]+%#i", $postlinks, $result );
            $result = $result[0];
            $id = 0;
            if ( is_array( $result ) )
            {
                for ( $i = 0, $c = count( $result ); $i < $c; $i++ )
                {
                    if ( !isset( $postlinksvars[$result[$i]] ) )
                    {
                        $postlinksvars[$result[$i]] = "(.*)";
                    }
                    if ( $result[$i] == "%postid%" )
                    {
                        $id = $i + 1;
                    }
                    $postlinks = str_replace( $result[$i], $postlinksvars[$result[$i]], $postlinks );
                }
            }
            $url = 'plugins=cat&pid=\\\\' . $id;
            $d->Query( "UPDATE `redirects` SET `pattern`='$postlinks',`url`='$url' WHERE `name`='post'" );
        }
        if ( strpos( $userlink, "%username%" ) === false )
        {
            printpm( "لینک پروفایل بایستی شامل متغیر %username% باشد." );
        }
        else
        {
            $userlink = trim( $userlink, " /" );
            saveconfig( 'userlink', $userlink );
            $userlink = str_replace( array( ".", "(", ")" ), array( "\\.", "", "" ), $userlink );
            $userlink = str_replace( "%username%", "([a-zA-z0-9]+)", $userlink );
            $url = 'plugins=member&method=profile&user=\\\\1';
            $d->Query( "UPDATE `redirects` SET `pattern`='$userlink',`url`='$url' WHERE `name`='profile'" );
        }

        if ( strpos( $pagelinks, "%pageid%" ) === false )
        {
            printpm( "لینک صفحات باید شامل متغیر %pageid% باشد." );
        }
        else
        {
            $pagelinks = trim( $pagelinks, " /" );
            saveconfig( 'pagelinks', $pagelinks );
            $pagelinks = str_replace( array( ".", "(", ")" ), array( "\\.", "", "" ), $pagelinks );
            $pagelinks = str_replace( "%pagetitle%", "(.+)", $pagelinks );
            $pagelinks = str_replace( "%pageid%", "([0-9]+)", $pagelinks );
            $url = 'plugins=extra&id=\\\\1';
            $d->Query( "UPDATE `redirects` SET `pattern`='$pagelinks',`url`='$url' WHERE `name`='pageslinks'" );
        }

        if ( strpos( $taglinks, "%name%" ) === false )
        {
            printpm( "لینک تگ ها باید شامل متغیر %name% باشد." );
        }
        else
        {
            $taglinks = trim( $taglinks, " /" );
            saveconfig( 'taglinks', $taglinks );
            $taglinks = str_replace( array( ".", "(", ")" ), array( "\\.", "", "" ), $taglinks );
            $taglinks = str_replace( "%name%", "(.+)", $taglinks );
            $url = 'plugins=cat&tag=\\\\1';
            $d->Query( "UPDATE `redirects` SET `pattern`='$taglinks',`url`='$url' WHERE `name`='tags'" );
        }

        if ( strpos( $subcatlinks, "%id%" ) === false )
        {
            printpm( "لینک زیر دسته های می بایست شامل %id% باشد." );
        }
        else
        {
            $subcatlinks = trim( $subcatlinks, " /" );
            saveconfig( 'subcatlinks', $subcatlinks );
            $subcatlinks = str_replace( array( ".", "(", ")" ), array( "\\.", "", "" ), $subcatlinks );
            preg_match_all( "#%[a-z]+%#i", $subcatlinks, $result );
            $result = $result[0];
            $id = 0;
            if ( is_array( $result ) )
            {
                for ( $i = 0, $c = count( $result ); $i < $c; $i++ )
                {
                    if ( !isset( $subcatlinksvars[$result[$i]] ) )
                    {
                        $subcatlinksvars[$result[$i]] = "(.*)";
                    }
                    if ( $result[$i] == "%id%" )
                    {
                        $id = $i + 1;
                    }
                    $subcatlinks = str_replace( $result[$i], $subcatlinksvars[$result[$i]], $subcatlinks );
                }
            }
            $url = 'plugins=cat&catid=\\\\' . $id;
            $d->Query( "UPDATE `redirects` SET `pattern`='$subcatlinks',`url`='$url' WHERE `name`='subcat'" );
        }
        $data = array( );
        $data['sitetitle'] = $title;
        $data['keys'] = $keys;
        $data['desc'] = $desc;
        $data['seo'] = $seo;
        foreach ( $data as $name => $value )
            $q = $d->uquery( "config", array( "value" => $value ), " `name`='$name'" );
        saveconfig( 'catlinks', $catlinks );
        //saveconfig( 'pagelinks', $pagelinks );
        printpm( "ok", "success" );
        break;
    case "menus":
        $list = (empty( $_POST['list'] )) ? printpm() : explode( ",", $_POST['list'] );
        $c = 0;
        foreach ( $list as $i )
        {
            if ( is_numeric( $i ) )
                $d->Query( "UPDATE `menus` SET `oid`='$c' WHERE `id`='$i' LIMIT 1" );
            $c++;
        }
        printpm( "ok", "success" );
        break;
    default:
        printpm();
}
?>
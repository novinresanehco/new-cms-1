<?php
if ( !defined( 'lsdkfskdnfsdf' ) )
{
    die();
}
if ( !is_numeric( @$post_data['id'] ) )
{
    die();
}
$itpl       = new samaneh();
$itpl->load( "theme/core/" . $config['theme'] . '/comment.htm' );
$_GET['id'] = $post_data['id'];
$sq         = '';
If ( !isset( $_GET['id'] ) or (!is_numeric( @$_GET['id'] )) )
{
    header( 'location: index.php' );
    die();
}
$id    = $_GET['id'];
$itpl->assign( 'poid', $id );
$error = '';
$inf   = $d->Query( "SELECT `scomments`,`title`,`show` FROM `data` WHERE `id`='$id'  LIMIT 1", true );
$inf   = $d->fetch( $inf );
if ( isset( $permissions['comment'] ) && $permissions['comment'] != '1' )
{
    $error = $lang['limited_area'];
}
if ( $config['comment'] == '4' )
{
    $error = $lang['disabled'];
}
elseif ( empty( $error ) )
{
    if ( $inf['scomments'] == '2' && $config['comment'] != '2' && $config['comment'] != '3' )
    {
        $error = $lang['disabled'];
    }

    if ( ($inf['scomments'] == '3' && $config['comment'] != '2') OR $config['comment'] == '3' )
    {
        $sq = " AND `active`!='0' ";
        $itpl->assign( 'activation', 1 );
    }
    $email = isset( $_POST['email'] ) ? $_POST['email'] : '';
    $name  = isset( $_POST['name'] ) ? $_POST['name'] : '';
    if ( !empty( $_SESSION['comment'] ) && $_SESSION['comment'] == @$_POST['comment'] )
    {
        $commbody = '';
    }
    else
        @$commbody = $_POST['comment'];
    $itpl->assign( array(
        'Guest'    => 1,
        'fullname' => $name,
        'email'    => $email,
        'url'      => @$_POST['url'],
        'comment'  => $commbody,
    ) );

    if ( isset( $_POST['sendcmt'] ) )
    {
        $_GET['task'] = 'new';
    }
    $itpl->assign( 'CommentForm', 1 );
    $itpl->assign( 'CommentList', 1 );
    if ( empty( $error ) )
    {
        // if(!isset($_GET['task'])){

        @$type        = ($type != 'DESC' && $type != 'ASC') ? 'ASC' : $type;
        @$From        = (!is_numeric( $From )) ? 1 : abs( $From );
        @$RPP         = (!is_numeric( $RPP )) ? 10 : abs( $RPP );
        @$CurrentPage = (!is_numeric( $RPP )) ? 1 : abs( $CurrentPage );
        //$comm = $d->Query("SELECT * FROM `comments` WHERE `p_id`='$id' AND `active`!='2' $sq  ORDER BY `c_id` $type LIMIT $From ,$RPP");
        $comm        = $d->Query( "SELECT * FROM `comments` WHERE `p_id`='$id' AND `active`!='2' AND `active`!='0' $sq  ORDER BY `c_id` $type" );
        if ( $d->getrows( $comm ) > 0 )
        {
            $itpl->assign( 'havecomment', 1 );
        }
        while ( $cs = $d->fetch( $comm ) )
        {
            if ( $cs['memberid'] != '-1' )
            {
                $sender_info   = $user->info( false, $cs['memberid'] );
                $sender_user   = $sender_info['user'];
                $sender_mail   = $sender_info['email'];
                $sender_avatar = $sender_info['avatar'];
                $sender_level  = $lang['users']['member'];
                $sender_name   = (empty( $sender_info['showname'] )) ? $sender_info['name'] : $sender_info['showname'];
            }
            else
            {
                $sender_avatar = '';
                $sender_user   = 'guest';
                $sender_level  = $lang['users']['guest'];
                $sender_name   = $cs['c_author'];
                $sender_mail   = $cs['email'];
            }
            $re                   = $reu                  = $ret                  = $reta                 = 'a';
            $replier_info['user'] = $replier_name         = '';
            if ( !empty( $cs['ans'] ) )
            {
                $replier_info = $user->info( false, $cs['ansid'] );
                $replier_name = $replier_info['name'];
                $re           = 'reply_name';
                $reu          = 'reply_user';
                $ret          = 'answer';
                $reta         = 'Ans';
            }

            $tpldata = array(
                'id'     => $cs['c_id'],
                'level'  => $sender_level,
                'name'   => $sender_name,
                'name'   => $sender_name,
                'avatar' => '',
                'text'   => nl2br( smile( $cs['text'] ) ),
                'date'   => mytime( $config['dtype'], $cs['date'], $config['dzone'] ),
                'url'    => $cs['url'],
                'body'   => smile( nl2br( $cs['text'] ) ),
                $re      => $replier_name,
                $reu     => $replier_info['user'],
                $ret     => smile( nl2br( $cs['ans'] ) ),
                $reta    => 1,
            );
            if ( !empty( $sender_avatar ) )
            {
                $tpldata['avatar']    = true;
                $tpldata['avatarurl'] = $config['site'] . 'files/avatars/' . $sender_avatar;
            }
            $itpl->block( 'comments', $tpldata );
        }
        // $t_com = $d->getrows("SELECT `c_id` FROM `comments` WHERE `p_id`='$id'",true);
        // CMSpage($t_com,$RPP,5,$CurrentPage,$itpl,'pages','index.php?module=member&action=list&');
        // }
        // else
        if ( isset( $_POST['task'] ) && $_POST['task'] == 'new' )
        {
            $required = array( 'comment', 'samaneh' );
            if ( false && !$login )
            {
                $required[] = 'name';
                $required[] = 'email';
            }
            if ( empty( $_POST['name'] ) )
            {
                $_POST['name'] = 'ناشناس';
            }
            if ( empty( $_POST['name'] ) )
            {
                $_POST['email'] = 'noperson@shahrokhian.com';
            }
            for ( $i = 0, $t = count( $required ), $c = true; $i < $t && $c; $i++ )
            {
                if ( empty( $_POST[$required[$i]] ) )
                {
                    $c     = false;
                    $error = $lang['allneed'];
                }
            }

            if ( $c )
            {
                if ( false && $login )
                {
                    $author   = (empty( $user->info['showname'] )) ? $user->info['name'] : $user->info['showname'];
                    $email    = $user->info['email'];
                    $memberid = $user->info['u_id'];
                }
                else
                {
                    $author   = $_POST['name'];
                    $email    = $_POST['email'];
                    $memberid = '-1';
                }
                if ( $_SESSION['CMS_secimg'] !== $_POST['samaneh'] )
                {
                    $c     = false;
                    $error = $lang['wrongseccode'];
                }
                else
                if ( !empty( $_SESSION['comment'] ) && $_SESSION['comment'] == $_POST['comment'] )
                {
                    $c                = false;
                    $error            = smile( 'این نظر قبلا ثبت شده است' );
                    $_POST['comment'] = '';
                }
                else
                if ( !email( $email ) )
                {
                    $c     = false;
                    $error = $lang['wmail'];
                }
            }
            if ( $c )
            {
                $randcode               = rand( 1000, 100000 );
                $_SESSION['CMS_secimg'] = $randcode;
                $_POST['url']           = empty( $_POST['url'] ) ? '' : $_POST['url'];
                $_POST['url']           = str_replace( array( 'http://', 'www', 'https://' ), '', $_POST['url'] );
                $_POST['url']           = 'http://' . $_POST['url'];
                $d->iquery( "comments", array(
                    "p_id"     => $id,
                    "c_author" => $author,
                    "text"     => $_POST['comment'],
                    "date"     => time(),
                    "ip"       => getRealIpAddr(),
                    "url"      => $_POST['url'],
                    "email"    => $email,
                    "ans"      => '',
                    "memberid" => $memberid,
                    "active"   => 0,
                    "ansid"    => 0,
                ) );
                $_SESSION['comment']    = $_POST['comment'];
                $itpl->assign( array(
                    'Guest'    => 1,
                    'fullname' => '',
                    'email'    => '',
                    'url'      => '',
                    'comment'  => '',
                ) );
                $d->Query( "UPDATE `data` SET `num`=`num`+1 WHERE `id`='$id' LIMIT 1" );
                $itpl->assign( array( 'Success' => 1, 'msg' => $lang['comsent'] ) );
            }
        }
    }
}

if ( !empty( $error ) )
{
    $itpl->assign( array( 'Error' => 1, 'msg' => $error ) );
}
$itpl->assign( 'site', $config['site'] );
$tpl->assign( array( 'CommentArea' => 1, 'comments' => $itpl->dontshowit() ) );

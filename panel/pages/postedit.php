<?php

define( 'samanehper', 'postmgr' );
define( 'ajax_head', true );
include('ajax_head.php');
$info  = $user->info;
$error = array();
if ( isset( $_POST['title'] ) )
{
    if ( !is_numeric( @$_POST['pid'] ) )
    {
        $html->msg( $lang['waccess'] );
        $html->printout( true );
    }
    $id = $_POST['pid'];
    if ( $permissions['editotherposts'] == 0 )
    {
        $q = $d->Query( "SELECT `author` FROM `data` WHERE `id`='$id' LIMIT 1" );
        $q = $d->fetch( $q );
        if ( $q['author'] != $info['u_id'] )
            $html->msg( $lang['waccess'] );
        $html->printout( true );
    }
    if ( empty( $_POST['title'] ) || empty( $_POST['entitle'] ) || empty( $_POST['text'] ) )
    {
        $error[] = $lang['fillpostn'];
    }

    if ( $error )
    {
        $error[] = '<a href="#reload" onclick="showfrm();"><center>[ ' . $lang["reloadfrm"] . ']</center></a>';
        $html->msg( $error );
        $html->printout();
    }



    $expire = (isset( $_POST['expirech'] ) && $_POST['expirech'] == 1) ? gmmktime( $_POST['expiredate_hour'], 0, 0, $_POST['expiredate_month'], $_POST['expiredate_day'], $_POST['expiredate_year'] ) : 0;
    $ctime  = (isset( $_POST['postch'] ) && $_POST['postch'] == 1) ? gmmktime( $_POST['posttime_hour'], 0, 0, $_POST['posttime_month'], $_POST['posttime_day'], $_POST['posttime_year'] ) : time();
    $jdate  = jdate( "Y/m/d" );
    $month  = jdate( "m" );
    $year   = jdate( "y" );

    $arr = array(
        'title'       => $_POST['title'],
        'entitle'     => engconv( $_POST['entitle'] ),
        'timage'      => $_POST['timage'],
        'cat_id'      => intval( $_POST['core'] ),
        'author'      => $info['u_id'],
        'context'     => $_POST['context'],
        'hits'        => intval( $_POST['hits'] ),
        'show'        => $_POST['show'],
        'scomments'   => $_POST['scomments'],
        'star'        => @$_POST['star'],
        'expire'      => $expire,
        //'date'        => $ctime,
        'year'        => $year,
        'month'       => $month,
        'reg'         => $_POST['reg'],
        'text'        => stripcslashes( $_POST['text'] ),
        'keywords'    => @$_POST['keywords'],
        'description' => @$_POST['description'],
        'full'        => stripcslashes( $_POST['fulltext'] ),
        'pos'         => 0,
    );

    $sendpost = $d->Query( "SELECT `sendpost`,`acceptmsg`,`author` FROM `data` WHERE `id`='$id' LIMIT 1" );
    if ( $d->getRows( $sendpost ) !== 1 )
    {
        die( 'invalid post' );
    }
    $sendpost  = $d->fetch( $sendpost );
    $author    = $sendpost['author'];
    $acceptmsg = $sendpost['acceptmsg'];
    $sendpost  = $sendpost['sendpost'];
    if ( ($_POST['show'] == 1 OR $_POST['show'] == 2) && $sendpost == 1 && $acceptmsg == 0 )
    {
        $arr['acceptmsg'] = 1;
        $msgtext          = "با سلام و احترام
			مطلب '$_POST[title]' توسط مدیریت تایید و ثبت شد.
			لینک مطلب :
			" . quick_post_link( $id ) . '
			سپاس
			';
        $q                = $d->iquery( "msg", array(
            'title'   => 'مطلب ارسالی شما مورد تایید واقع شد.',
            'text'    => safe( $msgtext ),
            'send_id' => $info['u_id'],
            're_id'   => $author,
                ) );
    }
    if ( !empty( $_POST['postpassword'] ) )
    {
        if ( @$_POST['postpassword'] != '**hidden**' )
        {
            $arr['pass1'] = md5( sha1( $_POST['postpassword'] ) );
        }
    }
    else
        $arr['pass1'] = '';
    if ( !empty( $_POST['fullpasswordi'] ) )
    {
        if ( @$_POST['fullpasswordi'] != '**hidden**' )
        {
            $arr['pass2'] = md5( sha1( $_POST['fullpasswordi'] ) );
        }
    }
    else
    {
        $arr['pass2'] = '';
    }

    $feilds = $d->Query( "SELECT * FROM `postfields` ORDER BY `orderid`" );

    while ( $data = $d->fetch( $feilds ) )
    {
        if ( !empty( $_POST['custom_' . $data['name']] ) )
        {
            $arr[$data['name']] = stripcslashes( $_POST['custom_' . $data['name']] );
        }
    }
    $query = $d->uquery( "data", $arr, "id= '" . $id . "' " );

    //cats
    if ( $query )
    {
        $d->Query( "DELETE FROM `catpost` WHERE `pid`='$id'" );
        if ( isset( $_POST['cats'] ) && is_array( $_POST['cats'] ) )
        {
            if ( isset( $_POST['cats'] ) && is_array( $_POST['cats'] ) )
            {
                foreach ( $_POST['cats'] as $cat )
                {
                    $d->Query( "INSERT INTO `catpost` SET `catid`='$cat',`pid`='$id'" );
                }
            }
        }
        //end cats
        //keywords
        $d->Query( "DELETE FROM `keys_join` WHERE `post_id`='$id'" );
        if ( !empty( $_POST['keys'] ) )
        {
            $_POST['keys'] = str_replace( '،', ',', $_POST['keys'] );
            $keys          = explode( ',', $_POST['keys'] );
            for ( $i = 0, $max = count( $keys ); $i < $max; $i++ )
            {
                $keys[$i] = trim( $keys[$i] );
                if ( strlen( $keys[$i] ) > 2 )
                {
                    $qu = $d->Query( "SELECT * FROM `keys` WHERE `title`='" . $keys[$i] . "'" );
                    if ( $d->getrows( $qu ) == 0 )
                    {
                        $d->Query( "INSERT INTO `keys` SET `title`='" . $keys[$i] . "'" );
                        $keyid = $d->getmax( 'id', 'keys' );
                        $d->iquery( "keys_join", array( "key_id" => $keyid, "post_id" => $id ) );
                    }
                    else
                    {
                        $qu = $d->fetch( $qu );
                        $d->iquery( "keys_join", array( "key_id" => $qu['id'], "post_id" => $id ) );
                    }
                }
            }
        }
        //end keywords
        //start sitemap update
        $Map        = '<xml>
		<urlset>
		<url>
		<loc>' . $config['site'] . '</loc>
		<changefreq>weekly</changefreq>
		<priority>1.0</priority>
		</url>
		';
        $ctimestamp = time();
        $Post_Map   = $d->Query( "select * FROM `data` WHERE `date` <= '$ctimestamp' AND (`expire`='0' OR `expire`='' OR `expire`>$ctimestamp) AND (`show`!='4' || `show`='2')  order by `id` ASC LIMIT 1500" );
        while ( $Row        = $d->fetch( $Post_Map ) )
        {
            $Map .= '
			<url>
			<loc>' . $config['site'] . 'post-' . $Row['id'] . '.html</loc>
			<changefreq>monthly</changefreq>
			<priority>0.8</priority>
			</url>
			';
        };
        $Map .= '
		</urlset>
		</xml>';
        unset( $Row );
        $filename = '../../sitemap.xml';
        $fp       = fopen( $filename, "w" ) or die( "Couldn't open $filename" );
        fwrite( $fp, $Map );
        fclose( $fp );
        //end sitemap update
        $html->msg( $lang['news_edited'], 'success' );
    }
    else
    {
        $error   = array();
        $error[] = $lang['error'];
        $error[] = "<a href='#reload' onclick='showfrm();'><center>[ " . $lang["reloadfrm"] . "]</center></a>";
        $html->msg( $error );
    }
    $html->printout();
}
<?php

define( 'samanehper', 'newpost' );
define( "ajax_head", true );
include('ajax_head.php');
$error = array();

if ( !isset( $_POST['title'] ) )
{
    $html->msg( $lang["waccess"] );
    $html->printout();
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
if ( isset( $_POST['postch'] ) && $_POST['postch'] == 1 )
{
    $jdate = $_POST['posttime_year'] . "/" . $_POST['posttime_month'] . "/" . $_POST['posttime_day'];
    if ( !validate_date( $jdate ) )
    {
        $ctime = time();
        $jdate = jdate( "Y/m/d" );
    }
    else
    {
        $month = $_POST['posttime_year'];
        $year  = $_POST['posttime_month'];
    }
}
$_POST['postpassword']  = (!empty( $_POST['postpassword'] )) ? md5( sha1( $_POST['postpassword'] ) ) : '';
$_POST['fullpasswordi'] = (!empty( $_POST['fullpasswordi'] )) ? md5( sha1( $_POST['fullpasswordi'] ) ) : '';
$insertData             = array(
    'title'       => $_POST['title'],
    'entitle'     => engconv( $_POST['entitle'] ),
    'timage'      => $_POST['timage'],
    'cat_id'      => intval( $_POST['core'] ),
    'author'      => $info['u_id'],
    'context'     => @$_POST['context'],
    'hits'        => intval( @$_POST['hits'] ),
    'show'        => $_POST['show'],
    'scomments'   => $_POST['scomments'],
    'star'        => $_POST['star'],
    'expire'      => $expire,
    'date'        => $ctime,
    'year'        => $year,
    'month'       => $month,
    'pass1'       => $_POST['postpassword'],
    'pass2'       => $_POST['fullpasswordi'],
    'reg'         => $_POST['reg'],
    'text'        => stripcslashes( $_POST['text'] ),
    'keywords'    => @$_POST['keywords'],
    'description' => @$_POST['description'],
    'full'        => stripcslashes( $_POST['fulltext'] ),
    'pos'         => 0,
);
$feilds                 = $d->Query( "SELECT * FROM `postfields` ORDER BY `orderid`" );

while ( $data = $d->fetch( $feilds ) )
{
    if ( !empty( $_POST['custom_' . $data['name']] ) )
    {
        $insertData[$data['name']] = stripcslashes( $_POST['custom_' . $data['name']] );
    }
}
$query = $d->iquery( "data", $insertData );
$id    = $d->getmax( 'id', 'data' );
$d->Query( "UPDATE `data` SET `pos`='$id' WHERE `id`='$id' LIMIT 1" );
//cats
if ( $query )
{
    //$cats = explode(',',$_POST['cats']);
    if ( isset( $_POST['cats'] ) && is_array( $_POST['cats'] ) )
    {
        foreach ( $_POST['cats'] as $cat )
        {
            $d->Query( "INSERT INTO `catpost` SET `catid`='$cat',`pid`='$id'" );
        }
    }
    //end cats
    //keywords
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
    /*
      //vote
      if(!empty($_POST['quest']) AND !empty($_POST['voteans'])){
      $quest	= $_POST['quest'];
      $voteans= $_POST['voteans'];
      $voteans = explode('\n',$voteans);
      $ipvote = $_POST['ipvote'];
      $multic=$_POST['multic'];
      $qu = mysql_query("INSERT INTO `voteq` SET `title`='$quest',`pid`='$id',`ipvote`='$ipvote',`multic`='$multic'");
      $vid = $d->getmax('id','voteq');
      for($i=0;$i<count($voteans);$i++){
      $qu = $d->iquery("voteans",array("voteid"=>$vid,"title"=>$voteans[$i],"count"=>0));
      }
      }
      //end vote
     */
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

    $html->msg( $lang['news_submited'], 'success' );
}
else
{
    $error   = array();
    $error[] = $lang['error'];
    $error[] = "<a href='#reload' onclick='showfrm();'><center>[ " . $lang["reloadfrm"] . "]</center></a>";
    $html->msg( $error );
}

if ( $_POST['webdir'] == 'on' )
{
    $data                = array();
    $data['domain']      = getDomain( $_SERVER['HTTP_HOST'] );
    $data['title']       = $insertData['title'];
    $data['entitle']     = $insertData['entitle'];
    $data['image']       = $insertData['timage'];
    $data['description'] = $insertData['description'];
    $data['keywords']    = $insertData['keywords'];
    $data['text']        = $insertData['text'];
    $data['fulltext']    = $insertData['full'];
    $cat                 = isset( $cats[$insertData['cat_id']] ) ? $cats[$insertData['cat_id']] : '';
    $date                = mytime( "Y-m-d", $insertData['date'], $config['dzone'] );
    $date                = explode( "-", $date );
    $data['link']        = get_post_link( array( "%postid%" => $id, "%subjectid%" => $insertData['cat_id'], "%sname%" => $cat, "%sslug%" => $cat, "%posttitle%" => $data['title'], "%postslug%" => $data['entitle'], "%postdday%" => $date[2], "%postdmonth%" => $date[1], "%postyear%" => $date[0] ) );
    $result              = get( 'http://samaneh.it/fa/directory/newPostDirectory', null, $data );
    $result              = json_decode( $result, true );
    if ( $result['status'] == true && isset( $result['id'] ) && is_numeric( $result['id'] ) )
    {
        $d->uQuery( 'data', array( 'directoryid' => $result['id'] ), " `id`='$id' LIMIT 1" );
    }
}
$html->printout();

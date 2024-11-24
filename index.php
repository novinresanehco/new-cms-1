<?php
$show_posts  = true;
$pageTheme   = 'first.htm';
$Admin_Paras = array();
define( 'crrentver', '3.0.0.0' );
define( "head", true );
define( 'page', 'index' );
$script      = '';
$single_post = false;

include('header.php');

if ( $show_posts )
{

    @$type        = ($type != 'DESC' && $type != 'ASC') ? 'ASC' : $type;
    @$From        = (!is_numeric( $From )) ? 1 : abs( $From );
    @$RPP         = (!is_numeric( $RPP )) ? 24 : abs( $RPP );
    @$CurrentPage = (!is_numeric( $CurrentPage )) ? 1 : abs( $CurrentPage );
    $tpl->assign( 'Page', 1 );
    $ctimestamp  = time();
    if ( !defined( 'customized_post_query' ) )
    {
        $post_q = $d->Query( "select * FROM `data` WHERE `date` <= '$ctimestamp' AND (`expire`='0' OR `expire`='' OR `expire`>'$ctimestamp') AND (`show`!='4' || `show`='2')  order by `id` desc LIMIT $From,$RPP" );
        $t_pr   = $d->getrows( "SELECT `id` FROM `data` WHERE `date` <= '$ctimestamp' AND (`expire`='0' OR `expire`='' OR `expire`>'$ctimestamp') AND (`show`!='4' || `show`='2')  order by `id` desc", true );
    }
    elseif ( customized_post_query_value == '' )
    {
        die( "samaneh :: Wrong Query...!" );
    }
    else
    {
        $post_q = $d->Query( customized_post_query_value );
        $t_pr   = customized_post_query_value_t;
    }

    if ( $d->getrows( $post_q ) > 0 )
    {
        $tpl->assign( 'samaneh', 1 );
        while ( $post_data = $d->fetch( $post_q ) )
        {
            if ( ($post_data['scomments'] != '2' && $config['comment'] != '4') OR $config['comment'] == '2' OR $config['comment'] != '3' )
            {
                $tpl->assign( 'Comment', 1 );
            }
            $cat      = isset( $cats[$post_data['cat_id']] ) ? $cats[$post_data['cat_id']] : '';
            //$link		= ($config['seo'] == '1') ?	$config['site'].'post/'.$post_data['id'].'-' . $post_data['entitle'].'.php' : 'index.php?plugins=cat&pid='.$post_data['id'];
            //$sub_link	= ($config['seo'] == '1') ?	$config['site'].'category/'.$post_data['cat_id'].'-'.$cat.'.php' : 'index.php?plugins=cat&catid='.$post_data['cat_id'];
            $sub_link = get_subcat_link( array( "%id%" => $post_data['cat_id'], "%name%" => $cats[$post_data['cat_id']], "%slug%" => $cats[$post_data['cat_id']] ) );
            if ( isset( $authors[$post_data['author']] ) )
            {
                $name = $authors[$post_data['author']];
            }
            else
            {
                $iq                            = $d->Query( "SELECT `name`,`showname` FROM `member` WHERE  `u_id` = '$post_data[author]' LIMIT 1" );
                $iiq                           = $d->fetch( $iq );
                $name                          = (empty( $iiq['showname'] )) ? $iiq['name'] : $iiq['showname'];
                $authors[$post_data['author']] = $name;
            }
            if ( !empty( $post_data['pass1'] ) )
            {
                $p = $post_data['text'];
                if ( isset( $_POST['post_password'] ) )
                {
                    if ( md5( sha1( $_POST['post_password'] ) ) != $post_data['pass1'] )
                    {
                        $p = $lang['wpass'] . str_replace( '%pid%', $post_data['id'], $lang['protected'] );
                    }
                    else
                    {
                        $_SESSION['post_sess_us_'] = $post_data['id'];
                        $p                         = $post_data['text'];
                    }
                }
                elseif ( @$_SESSION['post_sess_us_'] != $post_data['id'] )
                {
                    $p = str_replace( '%pid%', $post_data['id'], $lang['protected'] );
                }
                $post_data['text'] = $p;
            }
            $context = '';
            if ( !empty( $post_data['pass2'] ) && ($single_post) )
            {
                $p = $post_data['full'];

                if ( isset( $_POST['fpost_password'] ) )
                {
                    if ( md5( sha1( $_POST['fpost_password'] ) ) != $post_data['pass2'] )
                    {
                        $p = $lang['wpass'] . str_replace( '%pid%', $post_data['id'], $lang['fprotected'] );
                    }
                    else
                    {
                        $_SESSION['post_sess_us_f'] = $post_data['id'];
                        $p                          = $post_data['full'];
                    }
                }
                elseif ( @$_SESSION['post_sess_us_f'] != $post_data['id'] )
                {
                    $p = str_replace( '%pid%', $post_data['id'], $lang['fprotected'] );
                }
                $post_data['full'] = $p;
            }

            if ( $single_post && !empty( $post_data['full'] ) )
            {
                $text = $post_data['text'] . '<br>' . $post_data['full'];
            }
            else
            {
                $text = $post_data['text'];
            }
            //star rating
            $star = '';
            $id   = $post_data['id'];
            if ( $post_data['star'] == '1' )
            {
                $star = '<span id="rate_' . $id . '" dir="rtl"><a href="javascript:rate_send(\'' . $id . '\',\'1\')"><img src="/' . $Themedir . 'img/rate_off.gif" id="my_rate_' . $id . '_1" onmouseover="show_rate_on( \'' . $id . '\',\'1\')" onmouseout="show_rate_off( \'' . $id . '\',\'1\' )" alt="one" border="0"></a><a href="javascript:rate_send(\'' . $id . '\',\'2\')"><img src="/' . $Themedir . 'img/rate_off.gif" id="my_rate_' . $id . '_2" onmouseover="show_rate_on( \'' . $id . '\',\'2\')" onmouseout="show_rate_off( \'' . $id . '\',\'2\' )" alt="two" border="0"></a><a href="javascript:rate_send(\'' . $id . '\',\'3\')"><img src="/' . $Themedir . 'img/rate_off.gif" id="my_rate_' . $id . '_3" onmouseover="show_rate_on( \'' . $id . '\',\'3\')" onmouseout="show_rate_off( \'' . $id . '\',\'3\' )" alt="three" border="0"></a><a href="javascript:rate_send(\'' . $id . '\',\'4\')"><img src="/' . $Themedir . 'img/rate_off.gif" id="my_rate_' . $id . '_4" onmouseover="show_rate_on( \'' . $id . '\',\'4\')" onmouseout="show_rate_off( \'' . $id . '\',\'4\' )" alt="four" border="0"></a><a href="javascript:rate_send(\'' . $id . '\',\'5\')"><img src="/' . $Themedir . 'img/rate_off.gif" id="my_rate_' . $id . '_5" onmouseover="show_rate_on( \'' . $id . '\',\'5\')" onmouseout="show_rate_off( \'' . $id . '\',\'5\' )" alt="five" border="0"></a></span>';
            }
            $post_data['context'] = empty( $post_data['context'] ) ? $lang['context'] : $post_data['context'];
            if ( $post_data['reg'] == '2' && !$login )
            {
                $text = reglink( $text );
            }
            $date = mytime( "Y-m-d", $post_data['date'], $config['dzone'] );
            $date = explode( "-", $date );
            $link = get_post_link( array( "%postid%" => $post_data['id'], "%subjectid%" => $post_data['cat_id'], "%sname%" => $cat, "%sslug%" => $cat, "%posttitle%" => $post_data['title'], "%postslug%" => $post_data['entitle'], "%postdday%" => $date[2], "%postdmonth%" => $date[1], "%postyear%" => $date[0] ) );
            $arr  = array(
                'title'      => $post_data['title'],
                'subject'    => $cat,
                'id'         => $post_data['id'],
                'link'       => $link,
                'sub_link'   => $sub_link,
                'sub_id'     => $post_data['cat_id'],
                'num'        => $post_data['num'],
                'body'       => smile( $text ),
                'date'       => mytime( $config['dtype'], $post_data['date'], $config['dzone'] ),
                'author'     => $name,
				'text'       => reglink( $post_data['text'] ),
                'fulltext'   => reglink( $post_data['full'] ),
                'entitle'    => $post_data['entitle'],
                'timage'     => $post_data['timage'],
                'numhits'    => $post_data['hits'],
                'starrating' => $star );


            if ( !empty( $post_data['timage'] ) )
            {
                $arr['hasImage'] = true;
            }

            //cache tags and reset every 24 hours :: 86400 = ttl
            //$tags = array();

            $tags = $cache->get("tags_".$post_data['id']);
            if ( is_null( $tags ) )
            {

                    $tagsq    = $d->Query( "SELECT `title` FROM `keys` WHERE `id` IN (SELECT `key_id` FROM `keys_join` WHERE `post_id`='$post_data[id]') GROUP BY `title`" );
                    $tags     = array();
                    while ( $tagsdata = $d->fetch( $tagsq ) )
                    {
                        $tags[] = "<a href='" . get_tag_link( array( '%name%' => $tagsdata['title'] ) ) . "' class='".tag."'>$tagsdata[title]</a>";
                    }
               $cache->set("tags_".$post_data['id'], $tags, 86400 ); //cache for 3 ours

            }
            else
            {
                    //$tags = apc_fetch( 'tags' );
            }
          /*
              foreach ( $tags as $tag )
              {

              $tpl->block( 'tags_' . $post_data['id'], array(
              ''
              ) );


              }
             */
            $arr['tags'] = implode( ',', $tags );

            $feilds      = $d->Query( "SELECT * FROM `postfields` ORDER BY `orderid`" );
            while ( $fdata       = $d->fetch( $feilds ) )
            {
                $arr[$fdata['name']] = $post_data[$fdata['name']];
                $arr['body']         = str_replace( "[" . $fdata['name'] . "]", $post_data[$fdata['name']], $arr['body'] );
            }

            //if ( !empty( $post_data['full'] ) )
            //{
            if ( $single_post )
            {
                $d->Query( "UPDATE `data` SET `hits`=`hits`+1 WHERE `id`='$post_data[id]' LIMIT 1" );
            }
            $arr['Hits'] = 1;
            //}
            if ( !empty( $post_data['full'] ) && !($single_post) )
            {
                $arr['Fulltpl'] = 1;
                $arr['context'] = $post_data['context'];
            }
            if ( $single_post )
            {
                if ( !empty( $post_data['keywords'] ) )
                {
                    $tpl->assign( 'sitekeywords', $post_data['keywords'] );
                }
                if ( !empty( $post_data['description'] ) )
                {
                    $tpl->assign( 'sitedescription', $post_data['description'] );
                }
                define( 'lsdkfskdnfsdf', true );
                require('comment.php');
                $tpl->assign( 'sitetitle', $config['sitetitle'] . ' - ' . $post_data['title'] );
            }
//var_dump($arr);exit;
            $tpl->block( 'mp', $arr );
        }
    }
    else
        $tpl->block( 'mp', array(
            'subject'  => $config['sitetitle'],
            'sub_id'   => 1,
            'sub_link' => 'index.php',
            'link'     => 'index.php?plugins=member',
            'title'    => $config['sitetitle'],
            'body'     => '<div class=error>' . $lang['404s'] . '</div>',
                )
        );
    if ( defined( 'custom_p_url' ) )
    {
        $pages_url = (isset( $pages_url )) ? $pages_url : 'index.php?';
        $pages_url = (defined( 'pages_url' )) ? pages_url : 'index.php?';
    }
    else
    {
        $pages_url = 'index.php?';
    }

    CMSpage( $t_pr, $RPP, 5, $CurrentPage, $tpl, 'pages', $pages_url, false );
}
if ( count( $Admin_Paras ) > 0 )
{
    $t = '';
    foreach ( $Admin_Paras as $value )
        $t .= $value;
    $tpl->assign( 'Admin_Paras', $t );
}
else
{
    $tpl->assign( 'Admin_Paras', '' );
}
$tpl->assign( 'scripts', $script );
//sort and remove sortable sidebars


preg_match_all( "#<sidebar_([0-9]+)_([0-9]+)>(.*)</sidebar_\\1_\\2>#iUs", $tpl->tpl, $sidebars );
$sideBarOrders = array();
$order         = 0;
$orders		   = array();

for ( $i = 0, $c = count( $sidebars[2] ); $i < $c; $i++ )
{

    $groupID   = $sidebars[1][$i];
    $sidebarID = $sidebars[2][$i];
    $sideBar   = $sidebars[3][$i];
    $name      = 'theme_' . $config['theme'] . '_' . str_replace( array( '.html', '.htm' ), '', $pageTheme ) . '_sidebars';

    if ( isset( $config[$name] ) )
    {
        $sidebarsData = json_decode( $config[$name], true );
        if ( !isset( $sidebarsData[$groupID . '_' . $sidebarID] ) )
        {
            $tpl->tpl = str_replace( $sidebars[0][$i], '', $tpl->tpl );
        }
        else
         {
            
            $order           = $sidebarsData[$groupID . '_' . $sidebarID];
			if( in_array( $order, $orders ) )
			{
				do
				{
					$order++;
				}while( in_array( $order, $orders ) );
			}
            $sideBarOrders[$order] = $sideBar;
			$orders[] = $order;
            $tpl->tpl        = str_replace( $sidebars[0][$i], "[siderbarOrder_$order]", $tpl->tpl );
        }
    }
    else
    {
        $tpl->tpl = str_replace( $sidebars[0][$i], '', $tpl->tpl );
    }
}

//$sideBarOrders = array_reverse( $sideBarOrders );

foreach ( $sideBarOrders as $key => $value )
{
    $tpl->tpl = str_replace( "[siderbarOrder_$key]", $value, $tpl->tpl );
}
$ctimestamp = time();
foreach( $config as $key => $value )
{
	if( substr( $key, -10 ) == 'show_posts' )
	{
		if( preg_match ('#^theme\_'.$config['theme'].'\_([a-zA-Z0-9]+)\_show\_posts$#iU', $key, $m ) )
		{
			//get list of sub categories of $value
			$s_cats = getCats( $value );
			foreach( $s_cats as $s_cat )
			{
				$tpl->block( 'show_posts_' . $m[1] . '_cats', array(
					'id' => $s_cat['id'],
					'name' => $s_cat['name'],
					'link' => $s_cat['link'],
					'slug' => $s_cat['slug'],
				) );
				$catId = $s_cat['id'];
				$iq         = $d->Query( "SELECT * FROM `data` WHERE 
				(`cat_id`='$catId'
					OR `data`.`id` IN (SELECT `pid` FROM `catpost` WHERE `catid` = '$catId' )
				)
				AND `date` <= '$ctimestamp' AND (`expire`='0' OR `expire`='' OR `expire`>$ctimestamp) AND (`show`!='4' || `show`='2') ORDER BY `id` DESC" );
				while ( $idata      = $d->fetch( $iq ) )
				{
					$date = mytime( "Y-m-d", $idata['date'], $config['dzone'] );
					$cat  = isset( $cats[$idata['cat_id']] ) ? $cats[$idata['cat_id']] : '';
					$url  = get_post_link( array( "%postid%" => $idata['id'], "%subjectid%" => $idata['cat_id'], "%sname%" => $cat, "%sslug%" => $cat, "%posttitle%" => $idata['title'], "%postslug%" => $idata['entitle'], "%postdday%" => $date[2], "%postdmonth%" => $date[1], "%postyear%" => $date[0] ) );
					$tpl->block( 'show_posts_' . $m[1], array(
						'title' => $idata['title'],
						'cat_id' => $catId,
						'cat_slug' => $s_cat['slug'],
						'url'   => $url,
						'date'  => $date,
						'cat'   => $cat,
						'image' => $idata['timage'],
					) );
				}
			}
			
		}
	}
}

$tpl->tpl = preg_replace( '#\[siderbarOrder_[0-9]+\]#iU','', $tpl->tpl );
$tpl->assign( 'copyright', copyright );
loadPositions( $tpl, $pageTheme );
$tpl->showit();
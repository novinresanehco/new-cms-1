<?php

if ( !defined( 'plugins-inc' ) OR !is_array( @$data ) )
{
    die( '<a href="http://help.mihanphp.com/plugins" target=_blank>samaneh</a> :: Invalid calling of ' . basename( __FILE__ ) );
}

function recentpostbycatParser( $tpl )
{
    if ( preg_match_all( '#\[recentPostByCat\_([0-9]+)(\_([a-z0-9\-]+))?\]#Ui', $tpl, $matches ) )
    {
        for ( $i = 0, $c = count( $matches[0] ); $i < $c; $i++ )
        {
            $category           = $matches[1][$i];
            $template           = $matches[3][$i];
            $data               = array();
            $data['categories'] = $category;
            $data['template'] 	= $template;
            $tpl                = str_replace( $matches[0][$i], recentpostbycat_output( $data ), $tpl );
        }
    }
    return $tpl;
}

function recentpostbycat_output()
{
    global $config, $tpl, $d, $cats;
    $args = func_get_args();
    if ( !empty( $args[0] ) )
    {
        $args = $args[0];
    }
    $cache = md5( implode( 'mihanphp.com', $args ) );
	/*
    if( file_exists( __DIR__ . '/cache/.mp' . $cache . '.mihanphp.cache' ) )
    {
        $filelastmodified = filemtime( __DIR__ . '/cache/.mp' . $cache . '.mihanphp.cache' );
        if( ( time() - $filelastmodified ) > 3600 * 2 )
        {
                unlink( __DIR__ . '/cache/.mp' . $cache . '.mihanphp.cache' );
        }
        else
        {
              return file_get_contents( __DIR__ . '/cache/.mp' . $cache . '.mihanphp.cache' );
        }
    }
	*/
    if ( empty( $args['categories'] ) OR !is_numeric( $args['categories'] ) OR $args['categories'] < 0 )
    {
        return '';
    }
    $catId = $args['categories'];
    $count = ( isset( $args['count'] ) && is_numeric( $args['count'] ) && $args['count'] > 0 ) ? $args['count'] : 10;
    $itpl  = new samaneh();
	if( !empty( $args['template'] ) && ctype_alnum( $args['template'] ) && file_exists( current_theme_dir. 'plugins\\recentpostbycat\\' . $args['template'] . '.html' ) )
	{
		$itpl->load( current_theme_dir. 'plugins\\recentpostbycat\\' . $args['template'] . '.html' );
		
	}	
	else if( file_exists( current_theme_dir . 'plugins\\recentpostbycat_block-theme.html' ) )
	{
		$itpl->load( current_theme_dir. 'plugins\\recentpostbycat_block-theme.html' );
	}
	else
	{
		$itpl->load( 'plugins/recentpostbycat/block-theme.html' );
	}
    $itpl->assign( 'theme_url', 'theme/core/' . $config['theme'] . '/' );
    $ctimestamp = time();
    $iq         = $d->Query( "SELECT * FROM `data` WHERE 
        (`cat_id`='$catId'
            OR `data`.`id` IN (SELECT `pid` FROM `catpost` WHERE `catid` = '$catId' )
        )
            AND `date` <= '$ctimestamp' AND (`expire`='0' OR `expire`='' OR `expire`>$ctimestamp) AND (`show`!='4' || `show`='2') ORDER BY `id` DESC LIMIT $count" );
    $first      = true;
    $itpl->assign( 'categoryTitle', @$cats[$catId] );
    while ( $idata      = $d->fetch( $iq ) )
    {
        $date = mytime( "Y-m-d", $idata['date'], $config['dzone'] );
        $cat  = isset( $cats[$idata['cat_id']] ) ? $cats[$idata['cat_id']] : '';
        $url  = get_post_link( array( "%postid%" => $idata['id'], "%subjectid%" => $idata['cat_id'], "%sname%" => $cat, "%sslug%" => $cat, "%posttitle%" => $idata['title'], "%postslug%" => $idata['entitle'], "%postdday%" => $date[2], "%postdmonth%" => $date[1], "%postyear%" => $date[0] ) );
        if ( $first )
        {
            $itpl->assign( array(
                'firstTitle' => $idata['title'],
                'firstUrl'   => $url,
                'firstText'  => $idata['text'],
                'firstDate'  => $date,
                'firstCat'   => $cat,
                'firstImage' => $idata['timage'],
            ) );
            $first = false;
            //continue;
        }

        /* $date = explode( "-", $date ); */

        $itpl->block( 'recent', array(
            'title' => $idata['title'],
            'url'   => $url,
            'date'  => $date,
            'cat'   => $cat,
            'image' => $idata['timage'],
        ) );
    }
    $out = $itpl->dontshowit();
    file_put_contents( __DIR__ . '/cache/.mp' . $cache . '.mihanphp.cache' , $out );
    return $out;
}

$tpl->assign( 'recentpostbycat', recentpostbycat_output() );
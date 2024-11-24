<?php

if ( !defined( 'plugins-inc' ) OR !is_array( @$data ) )
{
    die( '<a href="http://help.mihanphp.com/plugins" target=_blank>samaneh</a> :: Invalid calling of ' . basename( __FILE__ ) );
}

$tpl->assign( 'ifrelated', true );

function related_output()
{
    global $config, $d;
    $count = $config['nlast'];
    $args  = func_get_args();
    if ( isset( $args[0]['count'] ) && is_numeric( $args[0]['count'] ) && $args[0]['count'] > 0 )
    {
        $count = $args[0]['count'];
    }
    $relatedtpl = new samaneh();
    $relatedtpl->load( 'plugins/related/block-theme.html' );
    $relatedtpl->assign( 'theme_url', core_theme_url );
    if ( isset( $_GET['plugins'] ) && $_GET['plugins'] == 'cat' && isset( $_GET['pid'] ) && is_numeric( $_GET['pid'] ) )
    {

        $ctimestamp = time();
        $title      = $d->Query( "SELECT `title` FROM `data` WHERE `date` <= '$ctimestamp' AND (`expire`='0' OR `expire`='' OR `expire`>$ctimestamp) AND (`show`!='4' || `show`='2') 
	AND `id`='$_GET[pid]' LIMIT 1" );
        $title      = $d->fetch( $title );
        $title      = $title['title'];
        if ( !empty( $title ) )
        {
            /*
              $query = "SELECT *, MATCH(title, text, full) AGAINST('$title') AS score
              FROM data
              WHERE `date` <= '$ctimestamp'
              AND `id`!='$_GET[pid]'
              AND (`expire`='0' OR `expire`='' OR `expire`>$ctimestamp)
              AND (`show`!='4' || `show`='2')
              AND MATCH(title, text, full) AGAINST('$title')
              ORDER BY score DESC LIMIT $config[nlast]";
             */
            $titles = explode( ' ', $title );
            $sq     = '';
            $stitle = safe( $title );
            $sq .= " `title` LIKE '%$stitle%' OR  `text` LIKE '%$stitle%' OR  `full` LIKE '%$stitle%' OR ";
            foreach ( $titles as $t )
            {
                $t = safe( $t );
                $sq .= " `title` LIKE '%$t%' OR  `text` LIKE '%$t%' OR  `full` LIKE '%$t%' OR ";
            }
            $sq    = substr( $sq, 0, -3 );
            $query = "SELECT *
		  FROM data
		  WHERE `date` <= '$ctimestamp'
		  AND `id`!='$_GET[pid]'
		  AND (`expire`='0' OR `expire`='' OR `expire`>$ctimestamp)
		  AND (`show`!='4' || `show`='2')
		  AND ($sq)
		  ORDER BY `date` DESC LIMIT $count";
            $iq    = $d->Query( $query );
            while ( $idata = $d->fetch( $iq ) )
            {
                $date = mytime( "Y-m-d", $idata['date'], $config['dzone'] );
                $date = explode( "-", $date );
                $cat  = isset( $cats[$idata['cat_id']] ) ? $cats[$idata['cat_id']] : '';
                $url  = get_post_link( array( "%postid%" => $idata['id'], "%subjectid%" => $idata['cat_id'], "%sname%" => $cat, "%sslug%" => $cat, "%posttitle%" => $idata['title'], "%postslug%" => $idata['entitle'], "%postdday%" => $date[2], "%postdmonth%" => $date[1], "%postyear%" => $date[0] ) );
                $relatedtpl->block( 'related', array(
                    'title'  => $idata['title'],
                    'url'    => $url,
                    'timage' => $idata['timage'],
                ) );
            }
        }
    }
    return $relatedtpl->dontshowit();
}

$tpl->assign( 'related', related_output() );
//unset( $relatedtpl );

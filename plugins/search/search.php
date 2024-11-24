<?php

if ( !defined( 'plugins-inc' ) OR !is_array( @$data ) )
{
    die( '<a href="http://help.samaneh.com/pluginss" target=_blank>samaneh</a> :: Invalid calling of ' . basename( __FILE__ ) );
}

$tpl->assign( 'search', search_output() );

function search_output()
{
    global $tpl, $show_posts, $cat_post, $d, $config, $lang;
    $args = func_get_args();
    if ( !empty( $args[0] ) )
    {
        $tpl->assign( $args[0] );
    }
    $itpl = new samaneh();
    $itpl->load( 'plugins/search/block-theme.html' );
    $itpl->assign( 'theme_url', core_theme_url );
    $q    = $d->Query( "SELECT * FROM `cat` WHERE `sub`!='0'" );
    while ( $data = $d->fetch( $q ) )
    {
        $itpl->block( "scats", array(
            'name' => $data['name'],
            'id'   => $data['id'],
        ) );
    }


    return $itpl->dontshowit();
}

if ( isset( $_GET['q'] ) && isset( $_GET['cat'] ) && is_numeric( $_GET['cat'] ) )
{
    $star         = '%';
    $query        = '';
    $and          = '';
    $split_search = array();
    $split_search = explode( " ", $_GET['q'] );
    for ( $i = 0; $i < count( $split_search ); $i++ )
    {
        $query .= $and . "(`title` LIKE '" . $star . $split_search[$i] . $star . "' or `text` LIKE '" . $star . $split_search[$i] . $star . "' or `full` LIKE '" . $star . $split_search[$i] . $star . "')";
        $and = " AND ";
    }
    if ( $_GET['cat'] > 0 )
    {
        $query .= " AND `cat_id`=" . $_GET['cat'];
    }
    @$type        = ($type != 'DESC' && $type != 'ASC') ? 'ASC' : $type;
    @$RPP         = (!is_numeric( $RPP )) ? 10 : abs( $RPP );
    $CurrentPage = (!isset( $_GET['page'] ) || !is_numeric( @$_GET['page'] ) || (abs( @$_GET['page'] ) == 0)) ? 1 : abs( $_GET['page'] );
    $From        = ($RPP != 1) ? ($CurrentPage - 1) * $RPP : $CurrentPage;
    @$From        = (!is_numeric( $From )) ? 1 : abs( $From );
    $ctimestamp  = time();
    $post_q      = "select * FROM `data` WHERE " . $query . "  AND `date` <= '$ctimestamp' AND (`expire`='0' OR `expire`='' OR `expire`>$ctimestamp) AND (`show`!='4' || `show`='2')  order by `id` $type LIMIT $From,$RPP";
    $post_q      = $d->Query( $post_q );
    $iitpl       = new samaneh();
    $iitpl->load( 'plugins/search/result.html' );
    $iitpl->assign( 'theme_url', core_theme_url );
    if ( $d->getRows( $post_q ) > 0 )
    {
        while ( $post_data = $d->fetch( $post_q ) )
        {
            $cat  = isset( $cats[$post_data['cat_id']] ) ? $cats[$post_data['cat_id']] : '';
            $date = mytime( "Y-m-d", $post_data['date'], $config['dzone'] );
            $date = explode( "-", $date );
            $link = get_post_link( array( "%postid%" => $post_data['id'], "%subjectid%" => $post_data['cat_id'], "%sname%" => $cat, "%sslug%" => $cat, "%posttitle%" => $post_data['title'], "%postslug%" => $post_data['entitle'], "%postdday%" => $date[2], "%postdmonth%" => $date[1], "%postyear%" => $date[0] ) );
            $iitpl->block( "searchresult", array( "url" => $link, "title" => $post_data['title'] ) );
        }
        $out = $iitpl->dontshowit();
    }
    else
    {
        $out = $lang['404s'];
    }
    $tpl->block( 'mp', array(
        'title'    => 'نتایج جستجو',
        'subject'  => 'جستجو',
        'body'     => $out,
        'link'     => $config['site'],
        'sub_link' => $config['site'],
    ) );
    $show_posts = false;
    $cat_post   = true;
}
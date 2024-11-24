<?php

if ( !defined( 'plugins-inc' ) OR !is_array( @$data ) )
{
    die( '<a href="http://help.mihanphp.com/plugins" target=_blank>samaneh</a> :: Invalid calling of ' . basename( __FILE__ ) );
}
$q    = $d->Query( "SELECT * FROM `cat`" );
while ( $data = $d->fetch( $q ) )
{
    $cats[$data['id']] = $data['name'];
}

$tpl->assign( 'Cats', getShowCats( 0, 'cats' ) );

function cat_output()
{
    global $tpl;
    $args = func_get_args();
    $tpl->assign( $args[0] );
    return getShowCats( 0, 'cats' );
}

if ( isset( $_GET['plugins'] ) )
{
    if ( $_GET['plugins'] == 'cat' )
    {
        @$type        = ($type != 'DESC' && $type != 'ASC') ? 'ASC' : $type;
        @$RPP         = (!is_numeric( $RPP )) ? 12 : abs( $RPP );
        $CurrentPage = (!isset( $_GET['page'] ) || !is_numeric( @$_GET['page'] ) || (abs( @$_GET['page'] ) == 0)) ? 1 : abs( $_GET['page'] );
        $From        = ($CurrentPage - 1) * $RPP;
        @$From        = (!is_numeric( $From )) ? 1 : abs( $From );
        if ( isset( $_GET['catid'] ) && is_numeric( $_GET['catid'] ) )
        {
            $pageTheme = 'cat.htm';
            $Themedir = "theme/core/" . $config['theme'] . '/';
            if ( file_exists( $Themedir . 'cat_' . $_GET['catid'] . '.htm' ) )
            {
                $pageTheme = 'cat_' . $_GET['catid'] . '.htm';
            }
$tpl->load( $Themedir . $pageTheme );
            if ( !defined( 'custom_p_url' ) )
            {
                define( 'custom_p_url', true );
            }
            $ctimestamp = time();
            define( 'customized_post_query', true );
            //$From       = 0;
            $post_q     = "select * FROM `data` WHERE  `date` <= '$ctimestamp' AND (`expire`='0' OR `expire`='' OR `expire`>'$ctimestamp') AND `cat_id`='$_GET[catid]' OR cat_id IN (SELECT `id` FROM `cat` WHERE `sub`='$_GET[catid]')  order by `id` $type LIMIT $From,$RPP";

            define( 'customized_post_query_value', $post_q );
            $t_pr       = $d->getrows( "select `id` FROM `data` WHERE  `date` <= '$ctimestamp' AND (`expire`='0' OR `expire`='' OR `expire`>'$ctimestamp') AND `cat_id`='$_GET[catid]' OR cat_id IN (SELECT `id` FROM `cat` WHERE `sub`='$_GET[catid]')", true );
            define( 'customized_post_query_value_t', $t_pr );
            $pages_url  = ($config['seo'] == '1') ? $config['site'] . 'cat-' . $_GET['catid'] . '-' : $config['site'] . 'index.php?module=cat&catid=' . $_GET['catid'] . '&';
$pages_url = '/cat/'.$_GET['catid']. '-' . urlencode( $cats[$_GET['catid']] ) .'.php?';
define('pages_url', $pages_url);
        }
        else if ( isset( $_GET['pid'] ) && is_numeric( $_GET['pid'] ) )
        {
            $_GET['pid'] = intval( $_GET['pid'] );
            $category    = $d->Query( "SELECT `cat_id` FROM `data` WHERE `id`='$_GET[pid]' LIMIT 1" );
            $category    = $d->fetch( $category );
            $category    = $category['cat_id'];
            $Themedir    = "theme/core/" . $config['theme'] . '/';
            if ( file_exists( $Themedir . 'single.htm' ) )
            {
                $pageTheme = 'single.htm';
            }
            if ( file_exists( $Themedir . 'cat_' . $category . '_single.htm' ) )
            {
                $pageTheme = 'cat_' . $category . '_single.htm';
            }

            $single_post = true;

            $tpl->load( $Themedir . $pageTheme );
            $ctimestamp = time();
            define( 'customized_post_query', true );
            $post_q     = "select * FROM `data` WHERE  `date` <= '$ctimestamp' AND (`expire`='0' OR `expire`='' OR `expire`>$ctimestamp) AND `id`='$_GET[pid]' LIMIT 1";
            define( 'customized_post_query_value', $post_q );
            define( 'customized_post_query_value_t', 0 );
        }
        elseif ( @is_numeric( $_GET['userid'] ) )
        {
            if ( !defined( 'custom_p_url' ) )
            {
                define( 'custom_p_url', true );
            }
            $single_post = true;
            $ctimestamp  = time();
            define( 'customized_post_query', true );
            $post_q      = "select * FROM `data` WHERE  `date` <= '$ctimestamp' AND (`expire`='0' OR `expire`='' OR `expire`>$ctimestamp) AND `author`='$_GET[userid]' LIMIT $From,$RPP";
            $t_pr        = $d->getrows( "select * FROM `data` WHERE  `date` <= '$ctimestamp' AND (`expire`='0' OR `expire`='' OR `expire`>$ctimestamp) AND `author`='$_GET[userid]'", true );
            define( 'customized_post_query_value_t', $t_pr );
            define( 'customized_post_query_value', $post_q );
            $pages_url   = ($config['seo'] == '1') ? $config['site'] . 'user-' . $_GET['userid'] . '-' : $config['site'] . 'index.php?plugins=cat&userid=' . $_GET['userid'] . '&';
        }
        else if ( !empty( $_GET['tag'] ) )
        {
            if ( !defined( 'custom_p_url' ) )
                define( 'custom_p_url', true );
            $ctimestamp = time();
            define( 'customized_post_query', true );
            $From       = 0;
            $post_q     = "select * FROM `data` WHERE  `date` <= '$ctimestamp' AND (`expire`='0' OR `expire`='' OR `expire`>'$ctimestamp') AND `id` IN (SELECT `post_id` FROM `keys_join` WHERE `key_id` IN (SELECT `id` FROM `keys` WHERE `title`='$_GET[tag]') )  order by `data`.`id` $type LIMIT $From,$RPP";
            define( 'customized_post_query_value', $post_q );
            $t_pr       = $d->getrows( "select * FROM `data` WHERE  `date` <= '$ctimestamp' AND (`expire`='0' OR `expire`='' OR `expire`>'$ctimestamp') AND `id` IN (SELECT `post_id` FROM `keys_join` WHERE `key_id` IN (SELECT `id` FROM `keys` WHERE `title`='$_GET[tag]') )", true );
            define( 'customized_post_query_value_t', $t_pr );
        }
    }
}
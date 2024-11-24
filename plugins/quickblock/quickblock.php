<?php

if ( !defined( 'plugins-inc' ) OR !is_array( @$data ) )
{
    die( '<a href="http://help.mihanphp.com/plugins" target=_blank>samaneh</a> :: Invalid calling of ' . basename( __FILE__ ) );
}

function quickblock_output()
{
    global $tpl, $d, $config;
    $args = func_get_args();
    if ( !empty( $args[0] ) )
    {
        $tpl->assign( $args[0] );
    }
    $itpl       = new samaneh();
	if( !empty( $args[0]['template'] ) && ctype_alnum( $args[0]['template'] ) && file_exists( current_theme_dir. 'plugins/blocks/' . $args[0]['template'] . '.html' ) )
	{
		$itpl->load( current_theme_dir. 'plugins/blocks/' . $args[0]['template'] . '.html' );
		
	}	
	else
	{
		$itpl->load( 'plugins/quickblock/block-theme.html' );
	}
    
    $itpl->assign( 'theme_url', core_theme_url );
    $ctimestamp = time();
    $itpl->block( 'quickblock', array(
        'title' => @$args[0]['title'],
        'text'  => @$args[0]['text'],
    ) );
    return $itpl->dontshowit();
}

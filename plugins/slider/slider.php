<?php

if ( !defined( 'plugins-inc' ) OR !is_array( @$data ) )
{
    die( '<a href="http://help.mihanphp.com/plugins" target=_blank>samaneh</a> :: Invalid calling of ' . basename( __FILE__ ) );
}
require_once( 'styles.config.php' );
if ( !empty( $config['slider_group_style'] ) && isset( $mpstyle[$config['slider_group_style']] ) )
{
    if ( isset( $mpstyle[$config['slider_group_style']]['css'] ) && is_array( $mpstyle[$config['slider_group_style']]['css'] ) )
    {
        foreach ( $mpstyle[$config['slider_group_style']]['css'] as $css )
        {
            $headercss[] = $config['site'] . 'plugins/slider/css/' . $css;
        }
    }
    if ( isset( $mpstyle[$config['slider_group_style']]['js'] ) && is_array( $mpstyle[$config['slider_group_style']]['js'] ) )
    {
        foreach ( $mpstyle[$config['slider_group_style']]['js'] as $js )
        {
            $headerjs[] = $config['site'] . 'plugins/slider/js/' . $js;
        }
    }
    $images   = $d->Query( "SELECT * FROM `mp_slider` ORDER BY `oid` DESC" );
    $slidetpl = new samaneh();
    if( file_exists( current_theme_dir . 'plugins' . DS . 'slider' . DS . $config['slider_group_style'] . '.html' ) )
    {
        $slidetpl->load( current_theme_dir . 'plugins' . DS . 'slider' . DS . $config['slider_group_style'] . '.html' );
    }
    else
    {
        $slidetpl->load( plugins_dir . 'slider' . DS . 'tpl' . DS . $mpstyle[$config['slider_group_style']]['tpl'] );
    }
	$i = 1;
	$class = 'default';
    while ( $image    = $d->fetch( $images ) )
    {
        $slidetpl->block( 'images', array(
			'class' 	  => $class,
			'row'		  => $i++,
            'id'          => $image['id'],
            'title'       => $image['title'],
            'description' => $image['description'],
            'url'         => $image['url'],
            'link'        => $image['link'],
            'thumbnail'   => empty( $image['thumbnail'] ) ? $image['url'] : $image['thumbnail'],
        ) );
		$class = '';
    }

    function slider_output()
    {
        global $slidetpl;
        return $slidetpl->dontshowit();
    }

    $tpl->assign( 'slider', $slidetpl->dontshowit() );
    //unset( $slidetpl );
}
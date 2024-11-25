<?php

if ( !defined( 'plugins-inc' ) OR !is_array( @$data ) )
{
    die( '<a href="http://help.mihanphp.com/plugins" target=_blank>samaneh</a> :: Invalid calling of ' . basename( __FILE__ ) );
}
$headercss[] = $config['site'] . 'plugins/form/style.css';
$headercss[] = $config['site'] . 'plugins/form/validationEngine.jquery.css';
$headerjs['validate'] = $config['site'] . 'plugins/form/js/jquery.validationEngine.js';
$headerjs['validatefa'] = $config['site'] . 'plugins/form/js/jquery.validationEngine-fa.js';
<?php

define( 'return_icon', true );
require( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'icons.php' );
require( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'theme.php' );
$tpl = new samaneh();
$tpl->load( admin_theme_dir . DIRECTORY_SEPARATOR . $config['admintheme'] . DIRECTORY_SEPARATOR . 'iconmanager.html' );
$tpl->showit();
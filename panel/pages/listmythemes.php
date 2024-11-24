<?php

define( "samanehper", 'themeArchive' );
define( "ajax_head", true );
define( 'theme', 'quickMyThemes.html' );
require("ajax_head.php");
$q = $d->query( "SELECT * FROM `themeArchive`" );
$row = 1;
while ( $data = mysql_fetch_array( $q ) )
{
    $tpl->block( "mythemes", array(
        'row' => $row++,
        'id' => $data['id'],
        'title' => $data['title'],
        'date' => mytime( $config['dtype'], $data['date'], $config['dzone'] ),
    ) );
}
$tpl->showit();
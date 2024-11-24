<?php
define("rashcmsper",'extra');
define("ajax_head",true);
require("ajax_head.php");

function printpm($msg = 'waccess',$type = 'error'){
	$html = new html();
	global $lang;
	$html->msg($lang[$msg],$type);
//	$html->msg($msg,$type);
	$html->printout(true);
}

$task = (!isset($_POST['task'])) ? printpm() : $_POST['task'];

switch ($task){
case "update_map":


$Map = '<xml>
<urlset>

<url>
<loc>'.$config['site'].'</loc>
<changefreq>weekly</changefreq>
<priority>1.0</priority>
</url>
';

$ctimestamp = time();
$Post_Map = $d->Query("select * FROM `data` WHERE `date` <= '$ctimestamp' AND (`expire`='0' OR `expire`='' OR `expire`>$ctimestamp) AND (`show`!='4' || `show`='2')  order by `id` ASC LIMIT 1500");
while($Row = $d->fetch($Post_Map))
{
$Map .= '
<url>
<loc>'.$config['site'].'post-'.$Row['id'].'.html</loc>
<changefreq>monthly</changefreq>
<priority>0.8</priority>
</url>
';
};

$Map .= '
</urlset>
</xml>';

unset($Row);

$filename = '../../sitemap.xml';
$fp = fopen( $filename, "w" ) or die("Couldn't open $filename");
fwrite( $fp, $Map );
fclose( $fp );
if(!isset($_POST['nomsg']))
{
	$html_pam = new html();
	$html_pam->msg("نق شه با موفقيت ساخته/به روز شد ! <br><a href='$config[site]/sitemap.xml' target='_blank'>$config[site]/sitemap.xml</a></center>",'success');
	$html_pam->printout(true);
}
break;

default:
printpm();
}
?>
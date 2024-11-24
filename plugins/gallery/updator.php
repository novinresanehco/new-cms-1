<?php
echo 'بروز است.';
/*
$tpl = "
<b>نسخه فعلی:</b> [mod_version] <br />
<b>جدیدترین نسخه:</b> [new_version] <br />
<b>نیاز به بروزرسانی:</b> [islast] <br />
<b>فایل های تغییر یافته:</b> <br />
[edited_files]
";

$url = "http://www.roboteronic.com/projects/rashcms/modules/gallery/";

$mod_files = array(
					'gallery-config'	=> 'gallery-cpnfig.php',
					'admin-config'		=> 'admin-config.php',
					'gallery-ajax'		=> '../../manager/pages/gallery.php',
					'listpics'			=> '../../manager/pages/listpics.php',
					'theme'				=> 'theme.html',
					'admin-theme'		=> 'admin-theme.html',
					'quickpics'			=> '../../template/admin/rashcms/ajax/quickpics.htm',
					);
//
//

$mod_version = (!empty($_GET['version']))? $_GET['version'] : '2.0.0';
$tpl = str_replace('[mod_version]', $mod_version, $tpl);

$f = file_get_content($url . 'update.php?version=' . $mod_version);
$f = base64_decode($f);

$info = explode('|', $f);

if(trim($info[0]) == '1')
{
	$tpl = str_replace('[islast]', '<font color="green"> ندارد </font>', $tpl);
	$tpl = str_replace('[mod_version]', $mod_version, $tpl);
	$tpl = str_replace('[edited_files]', 'موجود نیست', $tpl);
	echo $tpl;
} else {
	
}


*/
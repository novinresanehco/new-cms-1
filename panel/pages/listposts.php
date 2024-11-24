<?php
define('samanehper','postmgr');
define("ajax_head",true);
require("ajax_head.php");
$q = ($permissions['editotherposts'] == 1) ? " " : " WHERE author='{$info['u_id']}'";
$q = $d->Query("SELECT `title`,`id` FROM `data` $q");
$html = '<select name="listposts" id="listposts" class="select" size="1">';
while($data = $d->fetch($q)){$html .= '<option value="'.$data['id'].'">'.$data['title'].'</option>';
}
$html .= '</select>';
die($html);
?>
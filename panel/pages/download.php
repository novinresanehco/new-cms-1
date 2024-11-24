<?php
define('samanehper','download');
define("ajax_head",true);
require("ajax_head.php");
$lang = $lang['backup'];
function printpm($msg = 'waccess',$type = 'error'){
$html = new html();
global $lang;
$html->msg($lang[$msg],$type);
$html->printout(true);
}
if(!isset($_POST['id']))
printpm();
$file = $fname = $_POST['id'];
if(str_replace('samanehBackup_','',$file) == $file)
printpm();
if(str_replace('.php','',$file) == $file)
printpm();
$file_path = '../backup/'.$fname;
if (!is_file($file_path))
printpm('notfound');

$fsize = filesize($file_path);
$fext = strtolower(substr(strrchr($fname,"."),1));
// get mime type
// set headers
@header("Pragma: public");
@header("Expires: 0");
@header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
@header("Cache-Control: public");
@header("Content-Description: File Transfer");
@header("Content-Type: application/php");
@header("Content-Disposition: attachment; filename=\"$fname\"");
@header("Content-Transfer-Encoding: binary");
@header("Content-Length: " . $fsize);
// download
// @readfile($file_path);
$file = @fopen($file_path,"rb");
if ($file) {
  while(!feof($file)) {
    print(fread($file, 1024*8));
    flush();
    if (connection_status()!=0) {
      @fclose($file);
      die();
    }
  }
  @fclose($file);
}


?>
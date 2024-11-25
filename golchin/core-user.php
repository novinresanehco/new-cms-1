<?php
class user{
	var $info;
	var $loginstate = false;
	function user($id = 0){	$this->loginstate =  $this->checklogin();
		if(!$id){
			if($this->loginstate)			$this->info = $this->info(true);
		}

		else{		$this->info = $this->info(true,$id);
		}
	}

	function login($username='',$password=''){
	$stat = !(empty($username) || empty($password)) ? true : false;
    	global $d;
    	if($stat){
		$username = $this->safe($username);
		$password = md5(sha1($password));
    	$q = $d->query("SELECT `pass`,`u_id` FROM `member` WHERE `user`='$username' AND `stat`='1' LIMIT 1");
        $q = $d->fetch($q);
    	$pass = $q['pass'];
    	$u_id = $q['u_id'];
        $stat = ($pass == $password) ? true : false;
    		if(currentpage == 'admin')
    		{    		$prmn = $d->Query("SELECT `access_admin_area` FROM `permissions` WHERE `u_id`='$u_id' LIMIT 1");
    		$prmn = $d->fetch($prmn);
    		$prmn = $prmn['access_admin_area'];
    		$stat = ($prmn == '1' && $stat) ? true : false;     		}
    	if($stat){    	$this->logingen($username,$password);
    	$_SESSION['tries'] = 0;
    	$_SESSION['CMSimg'] = '';
    	return true;
    	}
    	else{return false;}
    	}
		else{
		return false;
		}
	}

    function checkimg($img='',$tries=3){
    $_SESSION['tries'] = (!isset($_SESSION['tries'])) ? 0 :$_SESSION['tries'];
       	if($_SESSION['tries'] >= $tries){
        $simg = @$_SESSION['CMS_secimg'];
        if(($simg == $img) AND (!empty($img))){return true;}else{return false;}
    	}else{return true;}
    }
	function safe($value,$html=true)
	{
	$value = @stripslashes( trim($value) );
	$value = @mysql_real_escape_string($value);
	$value = (!$html) ? $value :  str_replace(array("<",">","'","&#1740;","&amp;","&#1756;"),array("&lt;","&gt;","&#39;","&#1610;","&","&#1610;"),$value);
	return $value;
	}
    function logingen($username,$password)
    {
    global $d;
    //$rnd = rand(50);
    //$mix = md5($username.sha1($password));
    $prv =  md5($_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT'].$_SERVER['SERVER_NAME']);
    $_SESSION['username'] = $username;
    $_SESSION['prv'] = $prv;
    $d->query("UPDATE `member` SET `prv`='$prv' WHERE `user`='$username' LIMIT 1 ");

    }

    function checklogin(){
    global $d;
    $stat = ((isset($_SESSION['username']))  AND (!empty($_SESSION['username']))  AND (isset($_SESSION['prv'])) AND (!empty($_SESSION['prv']))) ? true : false;
    if($stat){
    $user = $this->safe($_SESSION['username'],false);
    $q = $d->query("SELECT `prv`,`u_id` FROM `member` WHERE `user`='$user' LIMIT 1");
    $q = $d->fetch($q);
    $u_id = $q['u_id'];
    $q = $q['prv'];
    $prv = md5($_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT'].$_SERVER['SERVER_NAME']);
    $stat = (($prv == $_SESSION['prv']) AND ($prv == $q)) ? true : false;
	    if(currentpage == 'admin' || currentpage == 'ajaxadmin')
    	{    	$prmn = $d->Query("SELECT `access_admin_area` FROM `permissions` WHERE `u_id`='$u_id' LIMIT 1");
    	$prmn = $d->fetch($prmn);
    	$prmn = $prmn['access_admin_area'];
    	$stat = ($prmn == '1' && $stat) ? true : false;
    	}
    if($stat)
    {    return true;}else{return false;}
    }else{
    return false;
    }
    }
    function info($postinf = false,$id = 0)
    {    global $d;
    	if(!$id){
    	$username = $this->safe($_SESSION['username']);
    	$q = $d->query("SELECT * FROM `member` WHERE `user`='$username'");
    	}else{
    	$id = intval($id);
    	$q = $d->query("SELECT * FROM `member` WHERE `u_id`='$id'");
    	}
        $q = $d->fetch($q);
		$u_id = $q['u_id'];
         if($postinf){
         $qe = $d->query("SELECT COUNT(*) as `num` FROM `data` WHERE `author`='$u_id'");
         $qe = $d->fetch($qe);
         $qe['num'] = empty($qe['num']) ? 0 : $qe['num'];
         $q['userposts'] = $qe['num'];
         }
         $qe = $d->query("SELECT COUNT(*) as `ur` FROM `msg` WHERE `re_id`='$u_id' AND `reade`='0'");
         $qe = $d->fetch($qe);
         $qe['ur'] = empty($qe['ur']) ? 0 : $qe['ur'];
         $q['ur'] = $qe['ur'];
    return $q;
    }
    function GetId($username)
    {    $q = empty($username) ? 0 : 1;
    if(!$q)
    return 0;
	$username = $this->safe($username);    global $d;
    $q = $d->query("SELECT `u_id` FROM `member` WHERE `user`='$username'");
    $q = $d->fetch($q);
    $q['u_id'] = (empty($q['u_id'])) ? 0 : $q['u_id'];
    return $q['u_id'];
    }
    function logout()
    {
    global $d;
    if(isset($_SESSION['username'])){
    $username = $this->safe($_SESSION['username']);
    $d->query("UPDATE `member` SET `prv`='' WHERE user='$username'");
    }
    $_SESSION['username'] = '';
    $_SESSION['prv'] = '';
	session_unset();
    session_destroy();
    }


 	function permission($id = 0){    global $d;
    $permissions = array("newpost"=>0,"editotherposts"=>0,"access_admin_area"=>0);
    	if(!$this->loginstate){    	return $permissions;
    	}
    $id = ($id) ? intval($id) : $this->info['u_id'];
    $data = $d->Query("SELECT * FROM `permissions` WHERE `u_id`='$id'");
    $data = $d->fetch($data);
    return $data;
    }
    function exists($username,$id = 0)
    {    Global $d;    	if(!$id){    	$is = $this->GetId($username) ? 1 : 0;
    	return $is;
    	}
    	else
    	{    	$q = intval($id) ? 1 : 0;
    	if(!$q)
    	return 0;        $q = $d->query("SELECT `u_id` FROM `member` WHERE `u_id`='$id'");
    	$q = $d->fetch($q);
    	$q['u_id'] = (empty($q['u_id'])) ? 0 : $q['u_id'];
    	return $q['u_id'];
    	}
    }

}
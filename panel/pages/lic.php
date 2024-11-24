<?php
define('samanehper','allow');
define("ajax_head",true);
include('ajax_head.php');
function Client_IP()
{
//function source:http://forum.iranphp.org/Thread-%D8%AA%D8%A7%D8%A8%D8%B9-%D9%82%D8%AF%D8%B1%D8%AA%D9%85%D9%86%D8%AF-Client-IP
    $_Ary_List= array('REMOTE_ADDR', 'CLIENT_IP', 'HTTP_CLIENT_IP', 'HTTP_PROXY_CONNECTION', 'HTTP_FORWARDED', 'HTTP_X_FORWARDED', 'FORWARDED_FOR_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED_FOR_IP', 'HTTP_X_FORWARDED_FOR', 'FORWARDED', 'X_FORWARDED_FOR', 'FORWARDED_FOR', 'X_FORWARDED', 'HTTP_VIA', 'VIA');

    foreach($_Ary_List as &$_Value)
    {
        if(isset($_SERVER[$_Value])): return($_SERVER[$_Value]);
        else: continue;
        endif;
    }

    return(false);
}
function Proxy_Detection()
{
//function source:http://forum.iranphp.org/
    $_Ary_List= array('HTTP_PROXY_CONNECTION', 'HTTP_FORWARDED', 'HTTP_X_FORWARDED', 'FORWARDED_FOR_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED_FOR_IP', 'HTTP_X_FORWARDED_FOR', 'FORWARDED', 'X_FORWARDED_FOR', 'FORWARDED_FOR', 'X_FORWARDED', 'HTTP_VIA', 'VIA');

    foreach($_Ary_List as &$_Value)
    {
        if(isset($_SERVER[$_Value])): return(true);
        else: continue;
        endif;
    }

    return(false);
}
$proxy = Proxy_Detection() ? 'yes' : 'no';
@$file = file_get_contents("http://sv2.mihanphp.ir/lic.php?current=".crrentver."&ip=".Client_IP()."&server=".$_SERVER['HTTP_HOST']."&serveraddr=".$_SERVER['SERVER_ADDR']."&self=".$_SERVER['PHP_SELF']."&proxy=".$proxy);
if($file)
{
}
?>
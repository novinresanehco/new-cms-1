<?php

/* * *************************************************************************
 *                                  CMS CMS
 *                          -------------------
 *   copyright            : (C) 2009 The samaneh  $Team = "www.samaneh.com";
 *   email                : info@samaneh.com
 *   email                : samaneh@gmail.com
 *   programmer           : Reza Shahrokhian
 * ************************************************************************* */
//         Security
if ( !defined( 'news_security' ) )
{
    die( "You are not allowed to access this page directly!" );
}

function mytime( $type, $TheTime, $TimeZone )
{
    $mtime = $TheTime;
    $mtime += $TimeZone * 3600;
    return jdate( $type, $mtime );
}

function GEN( $num )
{
    $CMSlist = 'ABDEFGHJKMNPRSTZ23456789';
    $CMSg    = '';
    $i       = 0;
    while ( $i < $num )
    {
        $CMSg .= substr( $CMSlist, mt_rand( 0, strlen( $CMSlist ) - 1 ), 1 );
        $i++;
    }
    return $CMSg;
}

function newpass()
{
    return GEN( 8 );
}

function email( $email )
{
    if ( preg_match( "/([\w\-]+\@[\w\-]+\.[\w\-]+)/", $email ) )
        return true;
    return false;
}

// safe function
function safe( $value, $type = '0' )
{
    if ( is_array( $value ) )
    {
        foreach ( $value as $k => $v )
        {
            $value[safe( $k )] = safe( $v );
        }
    }
    else
    {
        $value = trim( $value );
        $value = str_replace( "|nline|", "", $value );
        $value = str_replace( "|rnline|", "", $value );
        $value = str_replace( "\r\n", "|rnline|", $value );
        $value = str_replace( "\n", "|nline|", $value );
        $value = mysql_real_escape_string( $value );
        $value = str_replace( "|nline|", "\n", $value );
        $value = str_replace( "|rnline|", "\r\n", $value );
        if ( $type != '1' )
        {
            $value = htmlspecialchars( $value );
            $value = strip_tags( $value );
            $value = str_replace( array( "<", ">", "'", "&#1740;", "&amp;", "&#1756;" ), array( "&lt;", "&gt;", "&#39;", "&#1610;", "&", "&#1610;" ), $value );
        }
    }
    return $value;
}

function safeurl( $url, $strict = true )
{
    $replace = ($strict) ? array( '/', '\\', '.', 'http', 'ftp', 'www', "'", '"' ) : array( '/', '\\', 'http://', 'ftp', 'www', "'", '"' );
    $url     = safe( str_ireplace( $replace, '', $url ) );
    return $url;
}

function engconv( $text )
{
    $text = safe( $text, 1 );
    $text = str_replace( array( '!', '@', '#', '$', '%', '^', '*', '(', ')', '_', '=', '+', '|', '/', '\\', '~', '`', '\'', '"', '&', '?', '>', '<' ), '-', $text );
    return $text;
}

function send_mail( $mail_to, $mail_from, $body, $mail_subject = "Password recovery" )
{

    global $config, $d;


    if ( currentpage == 'admin' )
        $dir        = '../';
    elseif ( currentpage == 'ajaxadmin' )
        $dir        = '../../';
    else
        $dir        = '';
    if ( !class_exists( 'samaneh' ) )
        require_once($dir . 'core/theme.php');
    $tpl        = new samaneh();
    $tpl->load( $dir . 'theme/core/' . $config['theme'] . '/mail.htm' );
    $MailHeader = 'MIME-Version: 1.0' . "\r\n";
    $MailHeader .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
    $MailHeader .= 'From: ' . $mail_from . '' . "\r\n"; // Sender's Email Address
    $MailHeader .= 'Return-Path: ' . $mail_from . ' <' . $mail_from . '> /n'; // Indicates Return-path
    $MailHeader .= 'Reply-To: ' . $mail_from . ' <' . $mail_from . '> /n'; // Reply-to Address
    $MailHeader .= 'X-Mailer: PHP/' . phpversion(); // For X-Mailer
    $cutime     = mytime( $config['dtype'], time(), $config['dzone'] );
    $tpl->assign( array(
        'samanehtopic' => $mail_subject,
        'samanehdate'  => $cutime,
        'samanehweb'   => $config['site'],
        'samanehmail'  => $config['email'],
        'samanehbody'  => $body,
    ) );
    $nls        = $d->Query( "SELECT * FROM nls" );
    $nls        = $d->fetch( $nls );
    if ( !empty( $nls['SmtpHost'] ) && !empty( $nls['SmtpUser'] ) && !empty( $nls['SmtpPassword'] ) )
    {
        $SmtpHost     = $nls['SmtpHost'];
        $SmtpUser     = $nls['SmtpUser'];
        $SmtpPassword = $nls['SmtpPassword'];
        require('smtp.php');
        return smtpmail( $mail_to, $mail_from, $mail_subject, $tpl->dontshowit(), $MailHeader );
        //uselsmtp
    }
    else
    {
        if ( @mail( $mail_to, $mail_subject, $tpl->dontshowit(), $MailHeader ) )
            return true;
        return false;
    }
}

function ajaxvars( &$value, $key )
{
    $value = str_ireplace( array( '**mp**', '**reza**', '**sh**' ), array( '&', '=', '+' ), $value );
    return $value;
}

function reglink( $text )
{
    global $lang;
    $text = preg_replace( "#<a(.*?)href=\"(.*?)\"(.*?)>.*?</a>#i", "<!-- CMS CMS|Programmed By Reza Shahrokhian -->" . $lang['reglink'] . "<!-- CMS CMS|Programmed By Reza Shahrokhian -->", $text );
    $text = preg_replace( "#<a href=\"mailto:(.*?)\">.*?</a>#i", "<!-- CMS CMS|Programmed By Reza Shahrokhian -->" . $lang['reglink'] . "<!-- CMS CMS|Programmed By Reza Shahrokhian -->", $text );
    return($text);
}

function CMSpage( $TR, $RPP, $NumPageList, $CurrentPage, $tpl, $pagetag, $ref, $seo = 0 )
{
    global $tpl, $lang;
//$CurrentPage--;
//TP = total Records
//Rpp = Results Per Page
//NumPageList = number of pages to be shown in the list (except first and last and next_pointer page)
    $CurrentPage = (empty( $CurrentPage ) || ($CurrentPage == 0)) ? 1 : abs( $CurrentPage );
    $Pages       = ceil( $TR / $RPP );
    $P           = array();
    for ( $i = 0; $i < $Pages; $i++ )
    {
        $P[$i] = $i;
    }
    $prv = 'page=';
    $ext = '';
    if ( $seo )
    {
        $prv = 'n-';
        $ext = '.html';
    }

    if ( $CurrentPage != 1 && $Pages >= $CurrentPage )
    {
        $tpl->block( $pagetag . '_prev', array( 'pagelink' => $ref . $prv . ($CurrentPage - 1) . $ext, 'page' => $lang['prpage'] ) );
    }
    for ( $i = 0; $i < $Pages; $i++ )
    {
        $tpl->block( $pagetag, array( 'pagelink' => $ref . $prv . ($P[$i] + 1) . $ext, 'page' => ($P[$i] + 1) ) );
    }
    if ( $CurrentPage < $Pages )
    {
        $tpl->block( $pagetag . '_next', array( 'pagelink' => $ref . $prv . ($CurrentPage + 1) . $ext, 'page' => $lang['nextpage'] ) );
    }
}
function curPageURL()
{
        $pageURL = 'http';
        if ( isset( $_SERVER["HTTPS"] ) && $_SERVER["HTTPS"] == "on" )
        {
            $pageURL .= "s";
        }
        $pageURL .= "://";
        if ( $_SERVER["SERVER_PORT"] != "80" )
        {
            $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
        }
        else
        {
            $pageURL .= $_SERVER["SERVER_NAME"] . '/' . $_SERVER["REQUEST_URI"];
        }
        return $pageURL;
}
function appendQS( $url, $name, $value )
{
    $url       = preg_replace( "#&?$name=[^&]+#", '', $url );
    $separator = (parse_url( $url, PHP_URL_QUERY ) == NULL) ? '?' : '&';
    $url .= $separator . $name . '=' . $value;
    return $url;
}
function get_ext( $key )
{
    $key = pathinfo( $key, PATHINFO_EXTENSION );
    $key = strtolower( $key );
    return $key;
}

//getRealIpAddr by ali_sed
function getRealIpAddr()
{
    if ( !empty( $_SERVER['HTTP_CLIENT_IP'] ) )   //check ip from share internet
    {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    elseif ( !empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) )   //to check ip is pass from proxy
    {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    else
    {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

function smile( $text )
{
    return $text;
}

function saveconfig( $name, $value )
{
    global $d, $config;
    if ( isset( $config[$name] ) )
    {
        $d->uquery( "config", array( "value" => $value ), " `name`='$name'" );
    }
    else
    {
        $d->iquery( "config", array( "value" => $value, "name" => $name ) );
    }
}

$patterns = array();

function get_user_link( $data = array() )
{
    global $d;
    if ( empty( $config['userlink'] ) )
    {
        return false;
    }
    $p = $config['userlink'];
    foreach ( $data as $key => $value )
    {
        $p = str_replace( $key, str_replace( ' ', '+', $value ), $p );
    }
    return $p;
}

function get_post_link( $data = array() )
{
    global $d, $config;
    if ( empty( $config['postlinks'] ) OR !is_array( $data ) )
    {
        return false;
    }
    $p = $config['postlinks'];
    foreach ( $data as $key => $value )
    {
        $p = str_replace( $key, str_replace( ' ', '+', $value ), $p );
    }
    return $config['site'] . $p;
}

function quick_post_link( $id )
{
    if ( !is_numeric( $id ) )
        return false;
    global $d, $config;
    $iq    = $d->Query( "SELECT * FROM `data` WHERE  `id`='$id' LIMIT  1" );
    $idata = $d->fetch( $iq );
    $date  = mytime( "Y-m-d", $idata['date'], $config['dzone'] );
    $date  = explode( "-", $date );
    $iq    = $d->Query( "SELECT `name` FROM `cat` WHERE  `id`='$id' LIMIT  1" );
    $cat   = $d->fetch( $iq );
    $cat   = $cat['name'];
    return get_post_link( array( "%postid%" => $idata['id'], "%subjectid%" => $idata['cat_id'], "%sname%" => $cat, "%sslug%" => $cat, "%posttitle%" => $idata['title'], "%postslug%" => $idata['entitle'], "%postdday%" => $date[2], "%postdmonth%" => $date[1], "%postyear%" => $date[0] ) );
}

function get_subcat_link( $data = array() )
{
    global $d, $config;
    if ( empty( $config['subcatlinks'] ) )
    {
        return false;
    }
    $p = $config['subcatlinks'];
    foreach ( $data as $key => $value )
    {
        $p = str_replace( $key, str_replace( ' ', '+', $value ), $p );
    }
    return $config['site'] . $p;
}

function get_page_link( $data = array() )
{
    global $d, $config;
    if ( empty( $config['pagelinks'] ) )
    {
        return false;
    }
    $p = $config['pagelinks'];
    foreach ( $data as $key => $value )
    {
        $p = str_replace( '%' . $key . '%', str_replace( ' ', '+', $value ), $p );
    }
    return $config['site'] . ltrim( $p, '/' );
}

function quick_extra_link( $id )
{
    if ( !is_numeric( $id ) )
        return false;
    global $d;
    $data = $d->Query( "select `title`,`id` FROM `extra` WHERE `id`='$id' LIMIT 1" );
    $data = $d->fetch( $data );
    if ( empty( $data['id'] ) )
    {
        return null;
    }
    return get_page_link( array( 'pageid' => $data['id'], 'pagetitle' => $data['title'] ) );
}

function get_tag_link( $data = array() )
{
    global $d, $config;
    if ( empty( $config['taglinks'] ) )
    {
        return false;
    }
    $p = $config['taglinks'];
    foreach ( $data as $key => $value )
    {
        $p = str_replace( $key, str_replace( ' ', '+', $value ), $p );
    }
    return $config['site'] . $p;
}

function getSelectCats( $parent = 0, $join = '', $id = '', $selected_id = false, $justArray = false )
{
    global $d, $colors;
    $menu_data = $d->Query( "SELECT * FROM `cat` WHERE sub = '$parent' ORDER BY `sub`,`id` ASC" );
    if ( $justArray )
    {
        $out = array();
    }
    else
    {
        $out = '';
    }
    if ( $d->GetRows( $menu_data ) > 0 )
    {
        $p_sub = ( int ) $d->GetRowValue( "sub", "SELECT `sub` FROM `cat` WHERE `id`='$parent' LIMIT 1", true );
        $join .= '---';
        while ( $menu  = $d->fetch( $menu_data ) )
        {
            //$font = (strlen($join) == 3) ? 'bold' : 'normal';
            $font = '';
            if ( $p_sub == $parent )
            {
                $join = substr( $join, 0, -3 );
            }
            //$color = (isset( $colors[floor( strlen( $join ) / 3 )] )) ? $colors[floor( strlen( $join ) / 3 )] : 'black';
            if ( $menu['sub'] == 0 )
            {
                $catMainId = 0;
                $font      = 'bold';
            }
            else
            {
                $catMainId = $menu['id'];
                $font      = 'notmal';
            }
            $vid      = (!empty( $id )) ? "id='" . $id . $menu['id'] . "'" : '';
            $selected = '';
            if ( $selected_id !== false && $selected_id == $menu['id'] )
            {
                $selected = " selected ";
            }
            if ( $justArray )
            {
                $out[$menu['id']] = $join . ' ' . $menu['name'];
                $tmp              = getSelectCats( $menu['id'], $join, $id, $selected_id, $justArray );
                foreach ( $tmp as $key => $value )
                {
                    $out[$key] = $value;
                }
            }
            else
            {
                $out .= "<option style='font-weight:$font' value='$menu[id]' $selected $vid >" . $join . ' ' . $menu['name'] . "</option>";
                $out .= getSelectCats( $menu['id'], $join, $id, $selected_id, $justArray );
            }
        }
    }
    return $out;
}

/*
  function getShowCats( $parent = 0, $class = '' )
  {
  global $d;
  $menu_data = $d->Query( "SELECT * FROM `cat` WHERE sub = '$parent' ORDER BY `sub`,`id` ASC" );
  $out       = '';
  if ( $d->GetRows( $menu_data ) > 0 )
  {
  $out  = !empty( $class ) ? "<ul class='$class'>" : "<ul>";
  while ( $menu = $d->fetch( $menu_data ) )
  {
  $out .= "<li>";
  $link = get_subcat_link( array( "%id%" => $menu['id'], "%name%" => $menu['name'], "%slug%" => $menu['enname'] ) );
  $out .= "<a href='$link'>$menu[name]</a>";
  $out .= getShowCats( $menu['id'], '' );
  $out .= "</li>";
  }
  $out .= "</ul>";
  }
  return $out;
  }

 */

function getShowCats( $parent = 0, $class = '' )
{
    global $d;
    $theme     = __DIR__ . '/../plugins/cat/block-theme.html';
    $tpl       = new samaneh;
    $tpl->load( $theme );
    $menu_data = $d->Query( "SELECT * FROM `cat` WHERE sub = '$parent' ORDER BY `sub`,`id` ASC" );
    if ( $parent === 0 )
    {
        $tpl->assign( 'first', true );
    }
    if ( $d->GetRows( $menu_data ) > 0 )
    {
        $tpl->assign( array( 'hasSubCategories' => true, 'class' => $class ) );
        while ( $menu = $d->fetch( $menu_data ) )
        {
            $link = get_subcat_link( array( "%id%" => $menu['id'], "%name%" => $menu['name'], "%slug%" => $menu['enname'] ) );
            if ( $parent == 0 )
            {
                $menu['name'] = $menu['name'] . '<span class="fa plus-minus">&nbsp;</span>';
            }
            $data = array(
                'link'    => $link,
                'title'   => $menu['name'],
                'subcats' => getShowCats( $menu['id'], '' )
            );
            $tpl->block( 'categories', $data );
        }
    }
    return $tpl->dontshowit();
}

function getListCat( $parent = 0, $data = array() )
{
    $cats = getCats( $parent );
    return Catarray2ul( $cats, $data );
}

function Catarray2ul( $array, $selection = array() )
{
    if ( count( $array ) > 0 )
    {
        $out = '<ul>';
        foreach ( $array as $key => $value )
        {
            $selected = '';
            if ( in_array( $value['id'], $selection ) )
            {
                $selected = ' checked ';
            }

            $out .= '<li>';
            $out .= '<input ' . $selected . ' type="checkbox" name="cats[]" value="' . $value['id'] . '" id="chkbox_' . $value['id'] . '" />';
            $out .= '<label for="chkbox_' . $value['id'] . '">' . $value['name'] . '</label>';
            if ( isset( $value['childs'] ) && is_array( $value['childs'] ) )
            {
                $out .= Catarray2ul( $value['childs'], $selection );
            }
            $out .= '</li>';
        }
        $out .= '</ul>';
        return $out;
    }
}

function getCats( $parent = 0 )
{
    global $d;
    $result    = array();
    $menu_data = $d->Query( "SELECT * FROM `cat` WHERE sub = '$parent' ORDER BY `sub`,`id` ASC" );
    if ( $d->GetRows( $menu_data ) > 0 )
    {
        while ( $menu = $d->fetch( $menu_data ) )
        {
            $link     = get_subcat_link( array( "%id%" => $menu['id'], "%name%" => $menu['name'], "%slug%" => $menu['enname'] ) );
            $result[] = array(
                'id'     => $menu['id'],
                'name'   => $menu['name'],
                'link'   => $link,
                'slug'   => $menu['enname'],
                'childs' => getCats( $menu['id'], $result )
            );
        }
    }
    return $result;
}

function validate_date( $date )
{
    $date  = explode( "/", $date );
    if ( count( $date ) !== 3 )
        return false;
    $year  = $date[0];
    $month = $date[1];
    $day   = $date[2];
    if ( !is_numeric( $year ) OR !is_numeric( $month ) OR !is_numeric( $day ) )
        return false;
    if ( $year < 1300 )
        return false;
    if ( $year > 1500 )
        return false;
    if ( $month < 1 OR $month > 12 )
        return false;
    if ( $day < 1 OR $day > 31 )
        return false;
    if ( $month > 6 )
    {
        if ( $day > 30 )
            return false;
    }
    return true;
}

//cache repeated requests for plugins status detection
$activePlugins   = array();
$inActivePlugins = array();

function isActivePlugin( $name, $force = false )
{
    if ( empty( $name ) OR !ctype_alnum( $name ) )
    {
        return false;
    }
    global $activePlugins, $inActivePlugins, $d;
    if ( !$force )
    {
        if ( in_array( $name, $activePlugins ) )
        {
            return true;
        }
        if ( in_array( $name, $inActivePlugins ) )
        {
            return false;
        }
    }
    $plugin = $d->Query( "SELECT `stat` FROM `plugins` WHERE `name`='$name' LIMIT 1" );
    if ( $d->getRows( $plugin ) === 1 )
    {
        $plugin = $d->fetch( $plugin );
        if ( $plugin['stat'] == 9 OR $plugin['stat'] == 1 )
        {
            $activePlugins[] = $name;
            return true;
        }
        else
        {
            $inActivePlugins[] = $name;
            return false;
        }
    }
    $inActivePlugins[] = $name;
    return false;
}

function hasPermission( $permission, $userID = null )
{
    if ( empty( $permission ) OR (!is_numeric( $userID ) && !is_null( $userID ) ) )
    {
        return false;
    }
    if ( !is_numeric( $userID ) )
    {
        $user = new user();
        if ( !$user->checklogin() )
        {
            return false;
        }
        $permissions = $user->permission();
    }
    else
    {
        $permissions = $user->permission( $userID );
    }
    if ( !isset( $permissions[$permission] ) )
    {
        if ( isset( $permissions['access_admin_area'] ) && $permissions['access_admin_area'] == 1 )
        {
            //no permission is required
            return true;
        }
        else
        {
            return false;
        }
    }
    else
    {
        return ( $permissions[$permission] == 1 );
    }
}

//detect if given string is a valid json string
function isJson( $string )
{
    @json_decode( $string );
    return (json_last_error() == JSON_ERROR_NONE);
}

/* list files recuresively */

function listFiles( $directory, $extension = false )
{
    $out = array();
    if ( is_dir( $directory ) )
    {
        if ( $extension !== false )
        {
            if ( is_string( $extension ) )
            {
                $extension = array( 0 => $extension );
            }
            if ( !is_array( $extension ) )
            {
                return $out;
            }
        }
        $handle = opendir( $directory );
        if ( $handle )
        {
            while ( false !== ($entry = readdir( $handle )) )
            {
                if ( $entry != "." && $entry != ".." )
                {
                    $entry = rtrim( $entry, '/' );
                    $entry = $directory . DIRECTORY_SEPARATOR . $entry;
                    if ( is_dir( $entry ) )
                    {
                        $out = array_merge( $out, listFiles( $entry, $extension ) );
                    }
                    elseif ( is_file( $entry ) )
                    {
                        if ( $extension !== false )
                        {
                            $ext = pathinfo( $entry, PATHINFO_EXTENSION );
                            if ( in_array( $ext, $extension ) )
                            {
                                $out[] = $entry;
                            }
                        }
                        else
                        {
                            if ( strpos( $entry, 'php' ) === false && strpos( $entry, 'htaccess' ) === false )
                            {
                                $out[] = $entry;
                            }
                        }
                    }
                }
            }
            closedir( $handle );
        }
    }
    return $out;
}

function getDomain( $url )
{
    $url    = addScheme( $url );
    $url    = preg_replace( '#^(https?://)?www\.#', '\\1', $url );
    $pieces = parse_url( $url );
    $domain = isset( $pieces['host'] ) ? $pieces['host'] : '';
    return $domain;
}

function addScheme( $url, $scheme = 'http://' )
{
    if ( parse_url( $url, PHP_URL_SCHEME ) === null )
    {
        return $scheme . $url;
    }
    return $url;
}

function get( $url, $cookie = null, $data = array() )
{
    $agent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)';
    $ch    = curl_init();
    curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
    curl_setopt( $ch, CURLOPT_VERBOSE, true );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
    if ( !is_null( $cookie ) )
    {
        curl_setopt( $ch, CURLOPT_COOKIEJAR, $cookie );
        curl_setopt( $ch, CURLOPT_COOKIEFILE, $cookie );
    }
    if ( is_array( $data ) && count( $data ) > 0 )
    {
        curl_setopt( $ch, CURLOPT_POST, 1 );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $data ) );
    }
    curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
    curl_setopt( $ch, CURLOPT_USERAGENT, $agent );
    curl_setopt( $ch, CURLOPT_REFERER, $url );
    curl_setopt( $ch, CURLOPT_URL, $url );
    $result = curl_exec( $ch );
    return $result;
}

function closeHtmlTags( $html )
{
    $arr_single_tags = array( 'meta', 'img', 'br', 'link', 'area', 'hr', 'input', '!' );
    $at              = 0;
    $end             = strlen( $html );
    $isInQuote1      = false;
    $isInQuote2      = false;
    $isInTag         = false;
    $isInOpeningTag  = false;
    $isReadingTag    = false;
    $tagClosing      = array();
    $tagClosingCount = 0;
    while ( $at < $end )
    {
        $char = $html{$at};
        if ( $char == '<' )
        {
            if ( $isInQuote1 )
            {
                // Pass
            }
            else if ( $isInQuote2 )
            {
                // Pass
            }
            else if ( $isInTag )
            {
                // Pass
            }
            else
            {
                if ( $at == $end - 1 )
                {
                    if ( $tagClosingCount )
                    {
                        $html .= "/";
                        $isInTag        = true;
                        $isInOpeningTag = false;
                        $isReadingTag   = true;
                        $tagCurr        = '';
                    }
                    else
                    {
                        $html .= " />";
                    }
                    break;
                }
                else
                {
                    $charNext = $html{ ++$at};
                    if ( ($charNext >= 'a' && $charNext <= 'z') || ($charNext >= 'A' && $charNext <= 'Z') || ($charNext == '!') )
                    {
                        $isInTag        = true;
                        $isInOpeningTag = true;
                        $isReadingTag   = $charNext != '!';
                        $tagCurr        = $charNext;
                    }
                    else if ( $charNext == '/' )
                    {
                        if ( $at == $end - 1 )
                        {
                            $isInTag        = true;
                            $isInOpeningTag = false;
                            $isReadingTag   = true;
                            $tagCurr        = '';
                            break;
                        }
                        else
                        {
                            $charNext = $html{ ++$at};
                            if ( ($charNext >= 'a' && $charNext <= 'z') || ($charNext >= 'A' && $charNext <= 'Z') )
                            {
                                $isInTag        = true;
                                $isInOpeningTag = false;
                                $isReadingTag   = true;
                                $tagCurr        = $charNext;
                            }
                            else
                            {
                                // Pass
                            }
                        }
                    }
                    else
                    {
                        // Pass
                    }
                }
            }
        }
        else if ( $char == '>' )
        {
            if ( $isInQuote1 )
            {
                // Pass
            }
            else if ( $isInQuote2 )
            {
                // Pass
            }
            else if ( !$isInTag )
            {
                // Pass
            }
            else
            {
                $isInTag      = false;
                $isReadingTag = false;
                $tagCurr      = strtolower( $tagCurr );
                if ( $isInOpeningTag )
                {
                    if ( $tagCurr === "script" )
                    {
                        $pos = stripos( $html, "</script", $at );
                        if ( $pos === false )
                        {
                            $len = strlen( $html );
                            if ( !strcmp( strtolower( substr( $html, $len - 1 ) ), "<" ) )
                            {
                                $html .= "/script>";
                            }
                            else if ( !strcmp( strtolower( substr( $html, $len - 2 ) ), "</" ) )
                            {
                                $html .= "script>";
                            }
                            else if ( !strcmp( strtolower( substr( $html, $len - 3 ) ), "</s" ) )
                            {
                                $html .= "cript>";
                            }
                            else if ( !strcmp( strtolower( substr( $html, $len - 4 ) ), "</sc" ) )
                            {
                                $html .= "ript>";
                            }
                            else if ( !strcmp( strtolower( substr( $html, $len - 5 ) ), "</scr" ) )
                            {
                                $html .= "ipt>";
                            }
                            else if ( !strcmp( strtolower( substr( $html, $len - 6 ) ), "</scri" ) )
                            {
                                $html .= "pt>";
                            }
                            else if ( !strcmp( strtolower( substr( $html, $len - 7 ) ), "</scrip" ) )
                            {
                                $html .= "t>";
                            }
                            else if ( !strcmp( strtolower( substr( $html, $len - 8 ) ), "</script" ) )
                            {
                                $html .= ">";
                            }
                            else
                            {
                                $html .= "</script>";
                            }
                            break;
                        }
                        else
                        {
                            $at             = $pos + 8;
                            array_push( $tagClosing, "script" );
                            $tagClosingCount++;
                            $isInTag        = true;
                            $isInOpeningTag = false;
                            $isReadingTag   = false;
                            $tagCurr        = "script";
                        }
                    }
                    else if ( in_array( $tagCurr, $arr_single_tags, true ) )
                    {
                        // Pass
                    }
                    else
                    {
                        array_push( $tagClosing, $tagCurr );
                        $tagClosingCount++;
                    }
                }
                else
                {
                    if ( $tagClosingCount && $tagClosing[$tagClosingCount - 1] === $tagCurr )
                    {
                        array_pop( $tagClosing );
                        $tagClosingCount--;
                    }
                    else
                    {
                        $tagAt = $tagClosingCount - 2;
                        while ( $tagAt >= 0 )
                        {
                            if ( $tagClosing[$tagAt] === $tagCurr )
                            {
                                break;
                            }
                            $tagAt--;
                        }
                        if ( $tagAt >= 0 )
                        {
                            $tagClosingCount--;
                            while ( $tagAt < $tagClosingCount )
                            {
                                $tagAt2             = $tagAt + 1;
                                $tagClosing[$tagAt] = $tagClosing[$tagAt2];
                                $tagAt              = $tagAt2;
                            }
                            array_pop( $tagClosing );
                        }
                        else
                        {
                            // Pass
                        }
                    }
                }
            }
        }
        else if ( $char == '"' )
        {
            if ( $isInQuote1 )
            {
                $isInQuote1 = false;
            }
            else if ( $isInQuote2 )
            {
                // Pass
            }
            else if ( $isInTag )
            {
                $isReadingTag = false;
                $isInQuote1   = true;
            }
            else
            {
                // Pass
            }
        }
        else if ( $char == "'" )
        {
            if ( $isInQuote1 )
            {
                // Pass
            }
            else if ( $isInQuote2 )
            {
                $isInQuote2 = false;
            }
            else if ( $isInTag )
            {
                $isReadingTag = false;
                $isInQuote2   = true;
            }
            else
            {
                // Pass
            }
        }
        else if ( ($char >= 'a' && $char <= 'z') || ($char >= 'A' && $char <= 'Z') || ($char == "_") || ($char >= '0' && $char <= '9') )
        {
            if ( $isInQuote1 )
            {
                // Pass
            }
            else if ( $isInQuote2 )
            {
                // Pass
            }
            else if ( $isInTag )
            {
                if ( $isReadingTag )
                {
                    $tagCurr .= $char;
                }
                else
                {
                    // Pass
                }
            }
            else
            {
                // Pass
            }
        }
        else
        {
            if ( $isInQuote1 )
            {
                // Pass
            }
            else if ( $isInQuote2 )
            {
                // Pass
            }
            else if ( $isInTag )
            {
                $isReadingTag = false;
            }
            else
            {
                // Pass
            }
        }
        $at++;
    }
    if ( $isInQuote1 )
    {
        $html .= '"';
    }
    if ( $isInQuote2 )
    {
        $html .= "'";
    }
    if ( $isInTag )
    {
        if ( $isInOpeningTag )
        {
            $html .= "/>";
        }
        else
        {
            $tagCurr    = strtolower( $tagCurr );
            $tagCurrLen = strlen( $tagCurr );
            if ( $tagClosingCount && !strncmp( $tagClosing[$tagClosingCount - 1], $tagCurr, $tagCurrLen ) )
            {
                if ( strlen( $tagClosing[$tagClosingCount - 1] ) != $tagCurrLen )
                {
                    $html .= substr( $tagClosing[$tagClosingCount - 1], $tagCurrLen );
                }
                $html .= ">";
                array_pop( $tagClosing );
                $tagClosingCount--;
            }
            else
            {
                $tagAt = $tagClosingCount - 2;
                while ( $tagAt >= 0 )
                {
                    if ( !strncmp( $tagClosing[$tagAt], $tagCurr, $tagCurrLen ) )
                    {
                        break;
                    }
                    $tagAt--;
                }
                if ( $tagAt >= 0 )
                {
                    if ( strlen( $tagClosing[$tagAt] ) != $tagCurrLen )
                    {
                        $html .= substr( $tagClosing[$tagAt], $tagCurrLen );
                    }
                    $html .= ">";
                    $tagClosingCount--;
                    while ( $tagAt < $tagClosingCount )
                    {
                        $tagAt2             = $tagAt + 1;
                        $tagClosing[$tagAt] = $tagClosing[$tagAt2];
                        $tagAt              = $tagAt2;
                    }
                    array_pop( $tagClosing );
                }
                else
                {
                    // Pass
                }
            }
        }
    }
    while ( --$tagClosingCount >= 0 )
    {
        $html .= "</{$tagClosing[$tagClosingCount]}>";
    }
    return $html;
}

function loadPositions( $tpl, $pageTheme = 'first' )
{
    global $d;
    //positions
    $pageTheme = explode( ".", $pageTheme );
    $pageTheme = $pageTheme[0];
    $positions = $d->Query( "SELECT * FROM `positions` WHERE `theme`='$pageTheme' GROUP BY `pid` ASC" );
    while ( $position  = $d->fetch( $positions ) )
    {
        $plugins = $d->Query( "SELECT * FROM `positions` WHERE `theme`='$pageTheme' AND `pid`='$position[pid]' ORDER BY `id` ASC" );
        while ( $plugin  = $d->fetch( $plugins ) )
        {
            $data = array();
            if ( !empty( $plugin['data'] ) )
            {
                $plugin['data'] = unserialize( base64_decode( $plugin['data'] ) );
                if ( isset( $plugin['data']['pluginData'] ) && is_array( $plugin['data']['pluginData'] ) )
                {
                    foreach ( $plugin['data']['pluginData'] as $key => $value )
                    {
                        $data[$key] = $value;
                    }
                }
            }
            $data['content'] = ( function_exists( $plugin['value'] . '_output' ) ) ? call_user_func( $plugin['value'] . '_output', $data ) : $plugin['value'];

            $config = array
                (
                'indent'       => true,
                'output-xhtml' => true,
                'wrap'         => 200
            );

            foreach ( $data as $key => $value )
            {
                $data[$key] = closeHtmlTags( $data[$key] );
            }
            $tpl->block( 'position' . $position['pid'], $data );
        }
    }
}

function getCpanelUserName()
{
    if ( preg_match( '#\/home[0-9]*\/([a-zA-Z0-9]+)\/public\_html#', __DIR__, $username ) )
    {
        return $username[1];
    }
    else
    {
        return false;
    }
}

/* format Bytes with suffixes
 * http://stackoverflow.com/a/2510540/877320
 */

function formatBytes( $size, $precision = 2 )
{
    $base     = log( $size ) / log( 1024 );
    $suffixes = array( 'B', 'k', 'M', 'G', 'T' );

    return round( pow( 1024, $base - floor( $base ) ), $precision ) . ' ' . @$suffixes[floor( $base )];
}

function getCurrentUrl( $full = true )
{
    if ( isset( $_SERVER['REQUEST_URI'] ) )
    {
        $parse         = parse_url(
                (isset( $_SERVER['HTTPS'] ) && strcasecmp( $_SERVER['HTTPS'], 'off' ) ? 'https://' : 'http://') .
                (isset( $_SERVER['HTTP_HOST'] ) ? $_SERVER['HTTP_HOST'] : (isset( $_SERVER['SERVER_NAME'] ) ? $_SERVER['SERVER_NAME'] : '')) . (($full) ? $_SERVER['REQUEST_URI'] : null)
        );
        $parse['port'] = $_SERVER["SERVER_PORT"]; // Setup protocol for sure (80 is default)
        return http_build_url( '', $parse );
    }
}

defined( 'HTTP_URL_REPLACE' ) or define( 'HTTP_URL_REPLACE', 0 );
defined( 'HTTP_URL_JOIN_PATH' ) or define( 'HTTP_URL_JOIN_PATH', 1 );
defined( 'HTTP_URL_JOIN_QUERY' ) or define( 'HTTP_URL_JOIN_QUERY', 2 );
defined( 'HTTP_URL_STRIP_USER' ) or define( 'HTTP_URL_STRIP_USER', 4 );
defined( 'HTTP_URL_STRIP_PASS' ) or define( 'HTTP_URL_STRIP_PASS', 8 );
defined( 'HTTP_URL_STRIP_AUTH' ) or define( 'HTTP_URL_STRIP_AUTH', 12 );
defined( 'HTTP_URL_STRIP_PORT' ) or define( 'HTTP_URL_STRIP_PORT', 32 );
defined( 'HTTP_URL_STRIP_PATH' ) or define( 'HTTP_URL_STRIP_PATH', 64 );
defined( 'HTTP_URL_STRIP_QUERY' ) or define( 'HTTP_URL_STRIP_QUERY', 128 );
defined( 'HTTP_URL_STRIP_FRAGMENT' ) or define( 'HTTP_URL_STRIP_FRAGMENT', 256 );
defined( 'HTTP_URL_STRIP_ALL' ) or define( 'HTTP_URL_STRIP_ALL', 492 );
if ( !function_exists( 'http_build_url' ) )
{

    /**
     * Build a URL
     * @link http://php.net/manual/en/function.http-build-url.php
     * @param mixed $url (part(s) of) an URL in form of a string or associative array like parse_url() returns
     * @param mixed $parts same as the first argument
     * @param integer $flags a bitmask of binary or'ed HTTP_URL constants; HTTP_URL_REPLACE is the default
     * @param array $new_url if set, it will be filled with the parts of the composed url like parse_url() would return
     * @return string Returns the new URL as string on success or FALSE on failure.
     */
    function http_build_url( $url = array(), $parts = array(), $flags = HTTP_URL_REPLACE, &$new_url = null )
    {
        $defaults = array(
            'scheme'   => (empty( $_SERVER['HTTPS'] ) || strtolower( $_SERVER['HTTPS'] ) == 'off' ? 'http' : 'https'),
            'host'     => $_SERVER['HTTP_HOST'],
            'port'     => '',
            'user'     => '', 'pass'     => '',
            'path'     => preg_replace( '`^([^\?]*).*$`', '$1', $_SERVER['REQUEST_URI'] ),
            'query'    => '', 'fragment' => '',
        );
        is_array( $url ) or $url      = parse_url( $url );
        is_array( $parts ) or $parts    = parse_url( $parts );
        $new_url  = $parts + $url + $defaults;

        $flags or $flags = (HTTP_URL_JOIN_PATH); // Default flags ?


        $JOIN_PATH      = (($flags | HTTP_URL_JOIN_PATH) == $flags);
        $JOIN_QUERY     = (($flags | HTTP_URL_JOIN_QUERY) == $flags);
        $STRIP_USER     = (($flags | HTTP_URL_STRIP_USER) == $flags);
        $STRIP_PASS     = (($flags | HTTP_URL_STRIP_PASS) == $flags);
        $STRIP_PATH     = (($flags | HTTP_URL_STRIP_PATH) == $flags);
        $STRIP_QUERY    = (($flags | HTTP_URL_STRIP_QUERY) == $flags);
        $STRIP_FRAGMENT = (($flags | HTTP_URL_STRIP_FRAGMENT) == $flags);


        // User
        if ( $STRIP_USER )
            $new_url['user'] = '';

        // Pass
        if ( !$new_url['user'] || ($new_url['pass'] && $STRIP_PASS) )
            $new_url['pass'] = '';

        // Port
        if ( $new_url['port'] && ($flags | HTTP_URL_STRIP_PORT) == $flags )
            $new_url['port'] = '';

        // Path
        if ( $STRIP_PATH )
            $new_url['path'] = '';
        else
        {
            $d_path = $defaults['path'];
            $u_path = (isset( $url['path'] ) ? $url['path'] : '');
            $p_path = (isset( $parts['path'] ) ? $parts['path'] : '');

            if ( $p_path )
                $u_path = '';

            $path = $d_path;

            if ( isset( $url['host'] ) && !$p_path )
                $path = '/' . ltrim( $u_path, '/' );
            elseif ( strpos( $u_path, '/' ) === 0 )
                $path = $u_path;
            elseif ( $u_path )
                $path = pathinfo( $path . 'x', PATHINFO_DIRNAME ) . '/' . $u_path;

            if ( isset( $parts['host'] ) )
                $path = '/' . ltrim( $p_path, '/' );
            elseif ( strpos( $p_path, '/' ) === 0 )
                $path = $p_path;
            elseif ( $p_path )
                $path = pathinfo( $path . 'x', PATHINFO_DIRNAME ) . '/' . $p_path;

            $path    = explode( '/', $path );
            $k_stack = array();
            foreach ( $path as $k => $v )
            {
                if ( $v == '..' ) // /../
                {
                    if ( $k_stack )
                    {
                        $k_parent = array_pop( $k_stack );
                        unset( $path[$k_parent] );
                    }
                    unset( $path[$k] );
                }
                elseif ( $v == '.' ) // /./
                    unset( $path[$k] );
                else
                    $k_stack[] = $k;
            }
            $path = implode( '/', $path );

            $new_url['path'] = $path;
        }
        $new_url['path'] = '/' . ltrim( $new_url['path'], '/' );

        // Query
        if ( $STRIP_QUERY )
            $new_url['query'] = '';
        else
        {
            $u_query = isset( $url['query'] ) ? $url['query'] : '';
            $p_query = isset( $parts['query'] ) ? $parts['query'] : '';

            $query = $new_url['query'];

            if ( is_array( $p_query ) )
                $query = $u_query;
            elseif ( $JOIN_QUERY )
            {
                if ( !is_array( $u_query ) )
                    parse_str( $u_query, $u_query );
                if ( !is_array( $p_query ) )
                    parse_str( $p_query, $p_query );

                $u_query = http_build_str( $u_query );
                $p_query = http_build_str( $p_query );

                $u_query = str_replace( array( '[', '%5B' ), '{{{', $u_query );
                $u_query = str_replace( array( ']', '%5D' ), '}}}', $u_query );

                $p_query = str_replace( array( '[', '%5B' ), '{{{', $p_query );
                $p_query = str_replace( array( ']', '%5D' ), '}}}', $p_query );

                parse_str( $u_query, $u_query );
                parse_str( $p_query, $p_query );

                $query = http_build_str( array_merge( $u_query, $p_query ) );
                $query = str_replace( array( '{{{', '%7B%7B%7B' ), '%5B', $query );
                $query = str_replace( array( '}}}', '%7D%7D%7D' ), '%5D', $query );

                parse_str( $query, $query );
            }

            if ( is_array( $query ) )
                $query = http_build_str( $query );

            $new_url['query'] = $query;
        }

        // Fragment
        if ( $STRIP_FRAGMENT )
            $new_url['fragment'] = '';


        // Scheme
        $out = $new_url['scheme'] . '://';

        // User
        if ( $new_url['user'] )
            $out .= $new_url['user']
                    . ($new_url['pass'] ? ':' . $new_url['pass'] : '')
                    . '@';

        // Host
        $out .= $new_url['host'];

        // Port
        if ( $new_url['port'] )
            $out .= ':' . $new_url['port'];

        // Path
        $out .= $new_url['path'];

        // Query
        if ( $new_url['query'] )
            $out .= '?' . $new_url['query'];

        // Fragment
        if ( $new_url['fragment'] )
            $out .= '#' . $new_url['fragment'];


        $new_url = array_filter( $new_url );
        return $out;
    }

}

function restore_theme( $themeContent )
{
	global $d, $config;
    //$themeContent = nl2br( $themeContent );
    $themeContent = str_replace( '<?php exit; ?>', '', $themeContent );
	$themeContent = trim( $themeContent );
    $themeContent = trim( $themeContent );
    $themeContent = ( unserialize( base64_decode( $themeContent ) ) );
    if ( !is_array( $themeContent ) )
    {
        die( "<div class='alert margin'>قالب ذخیره شده قابل استفاده نیست.</div>" );
    }
					
    if ( !is_dir( theme_dir . 'core' . DS . $themeContent['theme'] ) )
    {
        die( "<div class='alert margin'>قالب $themeContent[theme] باید نصب شده باشد.</div>" );
    }
					
					if( $config['theme'] != $themeContent['theme'] )
					{
						die( "<div class='alert margin'>قالب $themeContent[theme] می بایست قالب فعلی سیستم باشد.</div>" );
					}
    /* backup current theme */

    /* creat backup directory */

    if ( !is_dir( theme_dir . 'core' . DS . $themeContent['theme'] . DS . 'backup' ) )
    {
        if ( !mkdir( theme_dir . 'core' . DS . $themeContent['theme'] . DS . 'backup', 0777 ) )
        {
            die( "<div class='alert margin'>امکان ایجاد فولدر بک آپ پشتیبان وجود ندارد.</div>" );
        }
    }

    $backupdir = date( 'Y_m_d_h_i_s' );

    if ( !is_dir( theme_dir . 'core' . DS . $themeContent['theme'] . DS . 'backup' . DS . $backupdir ) )
    {
        if ( !mkdir( theme_dir . 'core' . DS . $themeContent['theme'] . DS . 'backup' . DS . $backupdir, 0777 ) )
        {
            die( "<div class='alert margin'>امکان ایجاد فولدر بک آپ پشتیبان وجود ندارد.</div>" );
        }
    }

    $backupfulldir = theme_dir . 'core' . DS . $themeContent['theme'] . DS . 'backup' . DS . $backupdir . DS;
    $themesGLob = glob( theme_dir . 'core' . DS . $themeContent['theme'] . '*.htm*' );

    foreach ( $themesGLob as $value )
    {
        $name = str_replace( theme_dir . 'core' . DS . $themeContent['theme'], '', $value );
        $name = trim( $name, '/' );
        $name = trim( $name, '\\' );

        $file = file_get_contents( $value );
        if ( $file === false )
        {
            die( "<div class='alert margin'>خطا در خواندن فایل $name</div>" );
        }
        $backupName = '.' . $name . '.backup';
        $handle     = fopen( $backupfulldir . $backupName, 'w+' );
        if ( !$handle )
        {
            die( "<div class='alert margin'>امکان ایجاد فایل پشتیبان وجود ندارد.</div>" );
        }
        fwrite( $handle, $file );
        fclose( $handle );
    

		/* remove old theme configurations */


		if ( substr( $name, 0, 4 ) == 'cat_' )
		{
			if ( !unlink( $value ) )
			{
				die( "<div class='alert margin'>خطا در حذف فایل :: پرمشین ها بررسی شوند. :: $name</div>" );
			}
		}
		else
		if ( substr( $name, 0, 6 ) == 'extra_' )
		{
			if ( !unlink( $value ) )
			{
				die( "<div class='alert margin'>خطا در حذف فایل :: پرمشین ها بررسی شوند. :: $name</div>" );
			}
		}
		else
		if ( substr( $name, 0, 7 ) == 'plugin_' )
		{
			if ( !unlink( $value ) )
			{
				die( "<div class='alert margin'>خطا در حذف فایل :: پرمشین ها بررسی شوند. :: $name</div>" );
			}
		}
		else
		if ( preg_match( "#cat_[0-9]+_single\.htm#i", $name ) )
		{
			if ( !unlink( $value ) )
			{
				die( "<div class='alert margin'>خطا در حذف فایل :: پرمشین ها بررسی شوند. :: $name</div>" );
			}
		}
	}
				
    /* change theme */

    $d->Query( "UPDATE `config` SET `value`='$themeContent[theme]' WHERE `name`='theme' LIMIT 1" );
					
    /* change slider */
					if( $_POST['restoreSlider'] == 'true' && !empty( $themeContent['slider_group_style'] ) )
					{
						$d->Query( "UPDATE `config` SET `value`='$themeContent[slider_group_style]' WHERE `name`='slider_group_style' LIMIT 1" );
						$d->Query( "UPDATE `config` SET `value`='$themeContent[slider_thn_height]' WHERE `name`='slider_thn_height' LIMIT 1" );
						$d->Query( "UPDATE `config` SET `value`='$themeContent[slider_thn_width]' WHERE `name`='slider_thn_width' LIMIT 1" );
						$d->Query( "DELETE FROM `mp_slider`" );
						
						foreach( $themeContent['slider_items'] as $item )
						{
							$d->iQUery( 'mp_slider', $item );
						}
					}
					/* change menu */
					if( $_POST['restoreMenu'] == 'true' && !empty( $themeContent['menu'] ) )
					{
						$d->Query( "UPDATE `ns_menu_group` SET `style`='$themeContent[menu]' WHERE `id`='1' LIMIT 1" );
						$d->Query( "DELETE FROM `ns_menu`" );
						foreach( $themeContent['menu_items'] as $item )
						{
							$d->iQUery( 'ns_menu', $item );
						}
					}
    /* delete current theme settings */

    $d->Query( "DELETE FROM `config` WHERE `name` LIKE 'theme\_%'" );

    /* delete current theme positions */

    $d->Query( 'DELETE FROM `positions`' );

    /* insert new theme positions */

    foreach ( $themeContent['positions'] as $position )
    {
        $d->iQuery( 'positions', $position );
    }

    /* insert new theme configs */

    foreach ( $themeContent['config'] as $name => $value )
    {
        saveconfig( $name, $value );
    }

    /* creat new theme files */
    foreach ( $themeContent['themes'] as $name => $file )
    {
        $handle = fopen( theme_dir . 'core' . DS . $themeContent['theme'] . DS . $name, 'w+' );
        if ( !$handle )
        {
            print( "<div class='alert margin'>هشدار :: خطا در ایجاد فایل $name .</div>" );
        }
        fwrite( $handle, $file );
        fclose( $handle );
    }
	return true;
    

}
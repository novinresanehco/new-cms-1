<?php

define( 'samanehper', 'member' );
define( "ajax_head", true );
require("ajax_head.php");

function printpm( $msg = 'waccess', $type = 'error' )
{
    $html = new html();
    global $lang;
    $html->msg( isset( $lang[$msg] ) ? $lang[$msg] : $msg, $type );
    $html->printout( true );
}

$task = (!isset( $_POST['task'] )) ? printpm() : $_POST['task'];
switch ( $task )
{
    case "adduser":
        $username   = (!empty( $_POST['username'] )) ? safe( $_POST['username'], 1 ) : printpm( 'fillnd' );
        $email      = (!empty( $_POST['email'] )) ? safe( $_POST['email'], 1 ) : printpm( 'fillnd' );
        $password   = (!empty( $_POST['password'] )) ? safe( $_POST['password'], 1 ) : printpm( 'fillnd' );
        $repassword = (!empty( $_POST['repassword'] )) ? safe( $_POST['repassword'], 1 ) : printpm( 'fillnd' );
        if($password != $repassword)
        {
            printpm('رمز عبور با تکرار آن مطابقت ندارد');
        }
        $q = $d->iquery( "member", array(
            'user'     => $username,
            'email'    => $email,
            'pass' => md5( sha1( $password ) ),
                ) );
        printpm( 'کاربر با موفقیت ایجاد شد. برای تعیین سطوح دسترسی و تکمیل پروفایل به بخش اختیارات و لیست کاربران مراجعه نمایید.', "success" );
        break;
    default:
        printpm();
}
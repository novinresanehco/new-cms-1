<?php
if ( !defined( 'plugins-inc' ) OR !is_array( @$data ) )
{
    die( '<a href="http://help.mihanphp.com/plugins" target=_blank>samaneh</a> :: Invalid calling of ' . basename( __FILE__ ) );
}
function contact_output()
{
    global $data, $contact_lang, $tpl, $config, $lang, $d,$show_posts;
    $itpl = new samaneh();
	if( file_exists( current_theme_dir . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . 'contact.html' ) )
	{
		$itpl->load( current_theme_dir . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . 'contact.html' );
	}
	else
	{
		$itpl->load('plugins/contact/contact.html');
	}
    $itpl->assign('theme_url', core_theme_url);
    $itpl->assign('contacttxt', @$config['contacttxt']);
    require('lang.fa.php');
//config
    $min_text_length = 10;

    // $required = array(
        // 'name' => $contact_lang['name'],
        // 'email' => $contact_lang['email'],
        // 'text' => $contact_lang['text'],
        // 'reciver' => $contact_lang['reciver'],
    // );
    $optional = array('site', 'tell');
	$required = array(
    'email' => $contact_lang['email'],
    'text' => $contact_lang['text'],

	);
	// $optional = array( 'site', 'tell', 'name', 'reciver' );
    
            $error = array();
            $q = $d->Query("SELECT `u_id` FROM `permissions` WHERE `access_admin_area`='1'");
            while ($data = $d->fetch($q)) {
                $iq = $d->Query("SELECT `name`,`showname`,`email` FROM `member` WHERE  `u_id` = '$data[u_id]' LIMIT 1");
                $iq = $d->fetch($iq);
                $name = (empty($iq['showname'])) ? $iq['name'] : $iq['showname'];
				if( empty( $rec ) ) $rec = $data[u_id];
                $itpl->block('users', array('id' => $data['u_id'], 'name' => $name));
            }

            if (isset($_POST['submit'])) {
                foreach ($required as $key => $value) {
                    if (empty($_POST[$key]))
                        $error[] = str_replace('%name%', $value, $contact_lang['required']);
                }
				if( empty( $_POST['reciver'] ) ) $_POST['reciver'] = $rec;
                foreach ($optional as $key => $value)
                    if (!isset($_POST[$key]))
                        $_POST[$key] = '';
                if (count($error) == 0) {
                    if ($_SESSION['CMS_secimg'] !== $_POST['samaneh'])
                        $error[] = $contact_lang['wrongseccode'];
                    else
                        $_SESSION['CMS_secimg'] = md5(rand(1000, 100000));
                    if (!email($_POST['email']))
                        $error[] = $contact_lang['wmail'];
                    if (strlen($_POST['text']) < $min_text_length)
                        $error[] = str_replace(array('%name%', '%least%'), array($contact_lang['text'], $min_text_length), $contact_lang['short']);
                    $_POST['reciver'] = (is_numeric($_POST['reciver'])) ? $_POST['reciver'] : die('Hacking attemp');
                    $q = $d->getrows("SELECT `u_id` FROM `permissions` WHERE `access_admin_area`='1' AND `u_id`='$_POST[reciver]'", true);
                    if ($q <= 0)
                        $error[] = $contact_lang['wreciver'];
                }
                if (count($error) != 0) {
                    foreach ($optional as $key)
                        if (isset($_POST[$key]))
                            $itpl->assign('contact_' . $key, $_POST[$key]);
                    foreach ($required as $key => $value)
                        if (isset($_POST[$key]))
                            $itpl->assign('contact_' . $key, $_POST[$key]);
                    $itpl->assign('Contact_Form', 1);
                    $itpl->assign('Contact_Error', 1);
                    $msg = '';
                    foreach ($error as $err)
                        $msg .= $err . $contact_lang['seprator'];
                    $itpl->assign('ErrorMsg', $msg);
                    $tpl->block('mp', array(
                            'subject' => $config['sitetitle'],
                            'sub_id' => 1,
                            'sub_link' => 'index.php',
                            'link' => 'index.php?plugins=contact',
                            'title' => $contact_lang['contact'],
                            'body' => $itpl->dontshowit(),
                        )
                    );
                } else {
                    $itpl->assign('Contact_success', 1);
                    $itpl->assign('Msg', $contact_lang['success']);

                    $query = $d->iquery("contact", array(
                        'u_id' => $_POST['reciver'],
                        'name' => $_POST['name'],
                        'email' => $_POST['email'],
                        'text' => $_POST['text'],
                        'tell' => $_POST['tell'],
                        'site' => $_POST['site'],
                    ));
                    $msg = str_replace(array('%user%', '%fullname%', '%email%', '%site%', '%tell%', '%text%'), array($name, $_POST['name'], $_POST['email'], $_POST['site'], $_POST['tell'], $_POST['text']), $contact_lang['body']);
                    send_mail($iq['email'], $_POST['email'], $msg, $contact_lang['body']);
                    $msg = ($query) ? $lang['email_sub'] : $contact_lang['Error'];
                    $tpl->block('mp', array(
                            'subject' => $config['sitetitle'],
                            'sub_id' => 1,
                            'sub_link' => 'index.php',
                            'link' => 'index.php?plugins=contact',
                            'title' => $contact_lang['contact'],
                            'body' => $itpl->dontshowit(),
                        )
                    );
                }
            } else {
                $itpl->assign('Contact_Form', 1);
                foreach ($optional as $key)
                    $itpl->assign('contact_' . $key, '');
                foreach ($required as $key => $value)
                    $itpl->assign('contact_' . $key, '');
				if ( !empty( $_GET['plugins'] ) && $_GET['plugins'] == 'contact' )
				{
					$show_posts = false;
					$tpl->block('mp', array(
                        'subject' => $config['sitetitle'],
                        'sub_id' => 1,
                        'sub_link' => 'index.php',
                        'link' => 'index.php?plugins=contact',
                        'title' => $contact_lang['contact'],
                        'body' => $itpl->dontshowit(),
                    )
					);
				}
                
				
            }
    return $itpl->dontshowit();
}
$tpl->assign( 'contact',contact_output() );
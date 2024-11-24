<?php

if ( !defined( 'plugins-inc' ) OR !is_array( @$data ) )
{
    die( '<a href="http://help.samaneh.com/pluginss" target=_blank>samaneh</a> :: Invalid calling of ' . basename( __FILE__ ) );
}

$itpl = new samaneh();
$itpl->load( 'plugins/gallery/theme.html' );
$itpl->assign( 'siteurl', $config['site'] );
$itpl->assign( 'theme', $config['theme'] );


//$bg1 = '#EBEBEB';
$bg1 = '#FFFFFF';
$bg2 = '#FFFFFF';

$nopost = false;

if ( isset( $_GET['plugins'] ) && $_GET['plugins'] == 'gallery' )
{
    if ( isset( $_GET['task'] ) && $_GET['task'] == 'showimage' )
    {

        if ( isset( $_GET['img'] ) && is_numeric( $_GET['img'] ) )
        {
            $q = $d->Query( "SELECT `id`,`img`,`title`,`text`,`hits`,`users` FROM `gallery_images` WHERE `id` = '$_GET[img]' LIMIT 1" );
            if ( $d->getrows( $q ) != 0 )
            {
                $q = $d->fetch( $q );

                if ( ($q['users'] == '1' && !$user->checklogin()) || ($q['users'] == '3' && $user->checklogin()) )
                    $pgtext = '<div class=error>' . $lang['limited_area'] . '<div>';
                else
                {
                    $d->Query( "UPDATE `gallery_images` SET `hits` = `hits` + 1 WHERE `id` = '$_GET[img]'" );

                    $intpl = new samaneh();
                    $intpl->load( 'plugins/gallery/imgtheme.html' );

                    $intpl->assign( $q );

                    $pgtext = $intpl->dontshowit();
                }

                if ( isset( $_GET['js'] ) )
                {
                    die( $pgtext );
                }
                else
                {
                    $tpl->block( 'mp', array(
                        'subject' => 'گالری تصاویر',
                        'sub_id' => 1,
                        'sub_link' => '1index.php?plugins=gallery',
                        'link' => '1index.php?plugins=gallery&task=showimage&img=' . $q['id'],
                        'title' => $q['title'],
                        'body' => $pgtext,
                            )
                    );

                    $nopost = true;
                }

                unset( $intpl );
            }
        }
    }
    else
    {

        //$itpl -> assign('title', $ttitle);

        $cat = (isset( $_GET['cat'] ) && is_numeric( $_GET['cat'] )) ? $_GET['cat'] : 0;


        $gallery_config = $d->Query( "SELECT * FROM `gallery_config`" );
        $gallery_config = $d->fetch( $gallery_config );



        $gallery_q = $d->Query( "SELECT * FROM `gallery_cat` WHERE `sub` = '$cat'" );

        if ( $d->getrows( $gallery_q ) )
        {
            $itpl->assign( 'showcats', 1 );

            $bgcolor = $bg2;

            $counter = 0;

            $width = round( 100 / $gallery_config['numcolumns'] );

            while ( $f = $d->fetch( $gallery_q ) )
            {
                if ( !(($f['users'] == 3 && !$login) || $f['users'] == 2 || ($f['users'] == 1 && $login)) )
                    continue;


                $bgcolor = ($bgcolor == $bg1) ? $bg2 : $bg1;

                $counter++;

                $id = $f['id'];

                $star = '';
                if ( $f['star'] == '1' )
                    $star = '<span id="rate_' . $id . '" dir="rtl"><a href="javascript:rate_gallery_send(\'' . $id . '\',\'1\')"><img src="' . $Themedir . 'img/rate_off.gif" id="my_rate_' . $id . '_1" onmouseover="show_rate_on( \'' . $id . '\',\'1\')" onmouseout="show_rate_off( \'' . $id . '\',\'1\' )" alt="one" border="0"></a><a href="javascript:rate_gallery_send(\'' . $id . '\',\'2\')"><img src="' . $Themedir . 'img/rate_off.gif" id="my_rate_' . $id . '_2" onmouseover="show_rate_on( \'' . $id . '\',\'2\')" onmouseout="show_rate_off( \'' . $id . '\',\'2\' )" alt="two" border="0"></a><a href="javascript:rate_gallery_send(\'' . $id . '\',\'3\')"><img src="' . $Themedir . 'img/rate_off.gif" id="my_rate_' . $id . '_3" onmouseover="show_rate_on( \'' . $id . '\',\'3\')" onmouseout="show_rate_off( \'' . $id . '\',\'3\' )" alt="three" border="0"></a><a href="javascript:rate_gallery_send(\'' . $id . '\',\'4\')"><img src="' . $Themedir . 'img/rate_off.gif" id="my_rate_' . $id . '_4" onmouseover="show_rate_on( \'' . $id . '\',\'4\')" onmouseout="show_rate_off( \'' . $id . '\',\'4\' )" alt="four" border="0"></a><a href="javascript:rate_gallery_send(\'' . $id . '\',\'5\')"><img src="' . $Themedir . 'img/rate_off.gif" id="my_rate_' . $id . '_5" onmouseover="show_rate_on( \'' . $id . '\',\'5\')" onmouseout="show_rate_off( \'' . $id . '\',\'5\' )" alt="five" border="0"></a></span>';

                $arr = array( );
                $arr = array(
                    'id' => $f['id'],
                    'title' => $f['title'],
                    'text' => $f['text'],
                    'img' => ($f['img'] == 'none') ? $config['site'] . 'plugins/gallery/open.png' : $f['img'],
                    'starrating' => $star,
                    'bgcolor' => $bgcolor,
                    'width' => $width,
                    'isdir' => 1,
                );

                if ( $counter == 1 )
                    $arr['beginoftr'] = 1;
                elseif ( $counter == $gallery_config['numcolumns'] )
                    $arr['endoftr'] = 1;

                $itpl->block( 'dirs', $arr );

                if ( $counter == $gallery_config['numcolumns'] )
                    $counter = 0;
            }

            if ( $counter > 0 && $counter < $gallery_config['numcolumns'] )
            {
                $extra = str_repeat( '<td width="' . $width . '%"><table width="100%"><tr><td align="center"> &nbsp; </td></tr><tr><td align="center"> &nbsp; </td></tr><tr><td align="center"> &nbsp; </td></tr><tr><td align="center"> &nbsp; </td></tr>
				</table></td>', ($gallery_config['numcolumns'] - $counter ) ) . '</tr>';
                $itpl->assign( 'catsextra', 1 );
                $itpl->assign( 'catsextracontent', $extra );
            }
        }

        $gallery_cat = $d->Query( "SELECT * FROM `gallery_cat` WHERE `id` = '$cat'" );
        $gallery_cat = $d->fetch( $gallery_cat );

        $gallery_q = $d->Query( "SELECT * FROM `gallery_images` WHERE `cat` = '$cat'" );

        if ( $d->getrows( $gallery_q ) )
        {
            if ( $gallery_cat['ajax'] == 1 )
            {
                $itpl->assign( 'galleryinit', 1 );
            }
            else
            {
                $itpl->assign( 'simpleinit', 1 );
                $counter = 0;
                $width = round( 100 / $gallery_config['numcolumns'] );
            }

            while ( $f = $d->fetch( $gallery_q ) )
            {
                if ( !(($f['users'] == 3 && !$login) || $f['users'] == 2 || ($f['users'] == 1 && $login)) )
                    continue;

                @$bgcolor = ($bgcolor == $bg1) ? $bg2 : $bg1;

                $counter++;

                $id = $f['id'];

                $star = '';
                if ( $f['star'] == '1' )
                    $star = '<span id="rateimg_' . $id . '" dir="rtl"><a href="javascript:rate_galleryimg_send(\'' . $id . '\',\'1\')"><img src="' . $Themedir . 'img/rate_off.gif" id="my_rate_' . $id . '_1" onmouseover="show_rate_on( \'' . $id . '\',\'1\')" onmouseout="show_rate_off( \'' . $id . '\',\'1\' )" alt="one" border="0"></a><a href="javascript:rate_galleryimg_send(\'' . $id . '\',\'2\')"><img src="' . $Themedir . 'img/rate_off.gif" id="my_rate_' . $id . '_2" onmouseover="show_rate_on( \'' . $id . '\',\'2\')" onmouseout="show_rate_off( \'' . $id . '\',\'2\' )" alt="two" border="0"></a><a href="javascript:rate_galleryimg_send(\'' . $id . '\',\'3\')"><img src="' . $Themedir . 'img/rate_off.gif" id="my_rate_' . $id . '_3" onmouseover="show_rate_on( \'' . $id . '\',\'3\')" onmouseout="show_rate_off( \'' . $id . '\',\'3\' )" alt="three" border="0"></a><a href="javascript:rate_galleryimg_send(\'' . $id . '\',\'4\')"><img src="' . $Themedir . 'img/rate_off.gif" id="my_rate_' . $id . '_4" onmouseover="show_rate_on( \'' . $id . '\',\'4\')" onmouseout="show_rate_off( \'' . $id . '\',\'4\' )" alt="four" border="0"></a><a href="javascript:rate_galleryimg_send(\'' . $id . '\',\'5\')"><img src="' . $Themedir . 'img/rate_off.gif" id="my_rate_' . $id . '_5" onmouseover="show_rate_on( \'' . $id . '\',\'5\')" onmouseout="show_rate_off( \'' . $id . '\',\'5\' )" alt="five" border="0"></a></span>';

                if ( $gallery_cat['ajax'] == 1 )
                {

                    $itpl->block( 'images', array(
                        'id' => $f['id'],
                        'title' => $f['title'],
                        'desc' => nl2br( $f['text'] ),
                        'thumb' => ($f['thumb'] == 'none' ? $config['site'] . 'plugins/gallery/picture.png' : $f['thumb']),
                        'image' => ($f['img'] == 'none' ? $config['site'] . 'plugins/gallery/picture.png' : $f['img']),
                        'link' => '#',
                            )
                    );
                }
                else
                {

                    $arr = array( );
                    $arr = array(
                        'id' => $f['id'],
                        'title' => $f['title'],
                        'text' => $f['text'],
                        'img' => ($f['thumb'] == 'none') ? $config['site'] . 'plugins/gallery/picture.png' : $f['thumb'],
                        'img2' => $config['site'] . 'index.php?plugins=gallery&task=showimage&img=' . $f['id'],
                        'img2-img' => $f['img'],
                        'starrating' => $star,
                        'bgcolor' => $bgcolor,
                        'width' => $width,
                        'isimage' => 1,
                    );

                    if ( $counter == 1 )
                        $arr['beginoftr'] = 1;
                    elseif ( $counter == $gallery_config['numcolumns'] )
                        $arr['endoftr'] = 1;

                    $itpl->block( 'images', $arr );


                    if ( $counter == $gallery_config['numcolumns'] )
                        $counter = 0;
                }
            }

            if ( $gallery_cat['ajax'] == 2 )
            {
                if ( $counter > 0 && $counter < $gallery_config['numcolumns'] )
                {
                    $extra = str_repeat( '<td width="' . $width . '%">&nbsp;</td>', ($gallery_config['numcolumns'] - $counter ) ) . '</tr>';
                    $itpl->assign( 'simpleextra', 1 );
                    $itpl->assign( 'simpleextracontent', $extra );
                }
            }
        }

        $data = $d->Query( "SELECT * FROM `gallery_cat` WHERE `id` = '$cat' LIMIT 1" );
        $data = $d->fetch( $data );

        if ( !(($data['users'] == 3 && !$login) || $data['users'] == 2 || ($data['users'] == 1 && $login)) && $cat !== 0 )
            $pgtext = '<div class=error>' . $lang['limited_area'] . '<div>';



        if ( isset( $_POST['ajax'] ) )
        {
            die( $itpl->dontshowit() );
        }
        else
        {
            $itpl->assign( 'noajax', 1 );
            $pgtext = $itpl->dontshowit();
            $tpl->block( 'mp', array(
                'subject' => $config['sitetitle'],
                'sub_id' => 1,
                'sub_link' => '1index.php',
                'link' => '1index.php?plugins=gallery',
                'title' => 'گالری تصاویر',
                'body' => $pgtext,
                    )
            );
        }


        $nopost = true;
    }
}
if ( $nopost )
    $show_posts = false;

unset( $itpl );
unset( $gallery_q );
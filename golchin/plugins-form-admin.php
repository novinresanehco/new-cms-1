<?php

if ( !defined( 'plugins_admin_area' ) )
    die( 'invalid access' );
if ( !isset( $permissions['access_admin_area'] ) )
    die( 'invalid access' );
if ( $permissions['access_admin_area'] != '1' )
    die( 'invalid access' );
$information = array(
    'name'        => 'فرم ساز',
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
        global $tpl, $config, $d, $lang, $htpl;
        $itpl = new samaneh();
        $itpl->assign( 'site', $config['site'] );
        //reset defaults tabs
        $htpl->resetBlock( 'tabs' );
        if ( !isset( $_GET['data'] ) OR !is_numeric( $_GET['data'] ) )
        {
            //show form generator && forms
            $itpl->load( plugins_dir . 'form' . DS . 'first.html' );
            $response = array();
            if ( isset( $_GET['do'] ) )
            {
                switch ( $_GET['do'] )
                {
                    case 'save':
                        $response            = array();
                        $response['success'] = false;
                        if ( empty( $_POST['formTitle'] ) || !isset( $_POST['formStatus'] ) || empty( $_POST['submitMSG'] ) || !is_array( $_POST['fields'] ) || count( $_POST['fields'] ) <= 0 )
                        {
                            $response['success'] = false;
                            $response['error']   = 'اطلاعات ارسالی ناقص است.';
                            header( 'Content-type: application/json' );
                            echo json_encode( $response );
                            exit;
                        }
                        $frmID = null;
                        if ( isset( $_GET['frmID'] ) && is_numeric( $_GET['frmID'] ) && $_GET['frmID'] > 0 )
                        {
                            //update form
                            $form = $d->Query( "SELECT * FROM `form` WHERE `formID`='$_GET[frmID]' LIMIT 1" );
                            if ( $d->getRows( $form ) === 1 )
                            {
                                $form                = $d->fetch( $form );
                                $d->uQuery( 'form', array(
                                    'name'      => @$_POST['formTitle'],
                                    'status'    => (@$_POST['formStatus'] == 1) ? 1 : 0,
                                    'mailto'    => @$_POST['formEmail'],
                                    'submitMSG' => @$_POST['submitMSG'],
                                    'fields'    => base64_encode( serialize( $_POST['fields'] ) ),
                                        ), " `formID`='$_GET[frmID]' LIMIT 1" );
                                $response['success'] = true;
                                $frmID               = $_GET['frmID'];
                                $response['frmID']   = $frmID;
                            }
                            else
                            {
                                //invalid form id
                                $response['success'] = false;
                                $response['error']   = 'اطلاعات ارسالی ناقص است.';
                            }
                        }
                        else
                        {
                            //new form
                            $d->iQuery( 'form', array(
                                'name'      => @$_POST['formTitle'],
                                'status'    => (@$_POST['formStatus'] == 1) ? 1 : 0,
                                'mailto'    => @$_POST['formEmail'],
                                'submitMSG' => @$_POST['submitMSG'],
                                'fields'    => base64_encode( serialize( $_POST['fields'] ) ),
                            ) );
                            $frmID               = $d->last();
                            $oid                 = $d->getmax( 'oid', 'menus' );
                            $oid++;
                            $q                   = $d->Query( "INSERT INTO `menus` SET `oid`='$oid',`name`='form_$frmID',`title`='فرم $_POST[formTitle]',`url`='plugins.php?plugin=form&data=$frmID',`type`='1'" );
                            $response['success'] = true;
                            $response['frmID']   = $frmID;
                        }
                        header( 'Content-type: application/json' );
                        echo json_encode( $response );
                        exit;
                        break;
                    case 'delete':
                        if ( !empty( $_GET['did'] ) && is_numeric( $_GET['did'] ) )
                        {
                            $id = safe( $_GET['did'] );
                            $d->Query( "DELETE FROM `form` WHERE `formID`='$id' LIMIT 1" );
                            $d->Query( "DELETE FROM `formdata` WHERE `formID`='$id'" );
                            $d->Query( "DELETE FROM `menus` WHERE `name`='form_$id' LIMIT 1" );
                            $tpl->block( 'Success', array( 'msg' => $lang['ok'] ) );
                        }
                        break;
                    default:
                        $response            = array();
                        $response['success'] = false;
                        $response['error']   = 'invalid action';
                        header( 'Content-type: application/json' );
                        echo json_encode( $response );
                        exit;
                        break;
                }
                if ( $_GET['do'] != 'delete' )
                {
                    exit;
                }
            }
            $form   = null;
            $formID = 1;
            if ( isset( $_GET['id'] ) && is_numeric( $_GET['id'] ) )
            {
                $form = $d->Query( "SELECT * FROM `form` WHERE `formID`='$_GET[id]' LIMIT 1" );
                if ( $d->getRows( $form ) === 1 )
                {
                    $form   = $d->fetch( $form );
                    $formID = $form['formID'];
                    $data   = unserialize( base64_decode( $form['fields'] ) );
                    foreach ( $data as $inputID => $input )
                    {
                        switch ( $input['fieldtype'] )
                        {
                            case 'input' :
                                $fieldTpl = new samaneh();
                                $fieldTpl->load( plugins_dir . 'form' . DS . 'input.html' );
                                $fieldTpl->block( 'field', array(
                                    'id'                                        => $inputID,
                                    'name'                                      => $input['fieldname'],
                                    'fieldValue'                                => $input['value'],
                                    'required_' . $input['required']            => 'selected',
                                    'required_' . abs( $input['required'] - 1 ) => '',
                                    'fieldMin'                                  => $input['min'],
                                    'fieldMax'                                  => $input['max'],
                                    'type'                                      => 'متن',
                                ) );
                                $fieldTpl->assign( 'site', $config['site'] );
                                $itpl->block( 'fields', array( 'field' => $fieldTpl->dontshowit() ) );
                                break;
                            case 'password' :
                                $fieldTpl = new samaneh();
                                $fieldTpl->load( plugins_dir . 'form' . DS . 'password.html' );
                                $fieldTpl->block( 'field', array(
                                    'id'                                        => $inputID,
                                    'name'                                      => $input['fieldname'],
                                    'fieldValue'                                => $input['value'],
                                    'required_' . $input['required']            => 'selected',
                                    'required_' . abs( $input['required'] - 1 ) => '',
                                    'fieldMin'                                  => $input['min'],
                                    'fieldMax'                                  => $input['max'],
                                    'type'                                      => 'کلمه عبور',
                                ) );
                                $fieldTpl->assign( 'site', $config['site'] );
                                $itpl->block( 'fields', array( 'field' => $fieldTpl->dontshowit() ) );
                                break;
                            case 'textarea' :
                                $fieldTpl = new samaneh();
                                $fieldTpl->load( plugins_dir . 'form' . DS . 'textarea.html' );
                                $fieldTpl->block( 'field', array(
                                    'id'                                        => $inputID,
                                    'name'                                      => $input['fieldname'],
                                    'fieldValue'                                => $input['value'],
                                    'required_' . $input['required']            => 'selected',
                                    'required_' . abs( $input['required'] - 1 ) => '',
                                    'fieldMin'                                  => $input['min'],
                                    'fieldMax'                                  => $input['max'],
                                    'type'                                      => 'متن گسترده',
                                ) );
                                $fieldTpl->assign( 'site', $config['site'] );
                                $itpl->block( 'fields', array( 'field' => $fieldTpl->dontshowit() ) );
                                break;
                            case 'selectbox' :
                                $fieldTpl = new samaneh();
                                $fieldTpl->load( plugins_dir . 'form' . DS . 'selectbox.html' );
                                $fieldTpl->block( 'field', array(
                                    'id'                                        => $inputID,
                                    'name'                                      => $input['fieldname'],
                                    'fieldValue'                                => $input['pvalue'],
                                    'required_' . $input['required']            => 'selected',
                                    'required_' . abs( $input['required'] - 1 ) => '',
                                    'type'                                      => 'لیست کشویی',
                                ) );
                                $values   = explode( "\n", $input['pvalue'] );
                                foreach ( $values as $value )
                                {
                                    $value    = trim( $value );
                                    $selected = ( $value == $input['value'] ) ? 'selected' : '';
                                    $fieldTpl->block( 'options', array( 'value' => $value, 'selected' => $selected ) );
                                }
                                $fieldTpl->assign( 'site', $config['site'] );
                                $itpl->block( 'fields', array( 'field' => $fieldTpl->dontshowit() ) );
                                break;

                            case 'checkbox' :
                                $fieldTpl = new samaneh();
                                $fieldTpl->load( plugins_dir . 'form' . DS . 'checkbox.html' );
                                $fieldTpl->block( 'field', array(
                                    'id'         => $inputID,
                                    'name'       => $input['fieldname'],
                                    'fieldValue' => $input['pvalue'],
                                    'type'       => 'چند گزینه ای',
                                ) );
                                $fieldTpl->assign( 'site', $config['site'] );
                                $itpl->block( 'fields', array( 'field' => $fieldTpl->dontshowit() ) );
                                break;

                            case 'radiobox' :
                                $fieldTpl = new samaneh();
                                $fieldTpl->load( plugins_dir . 'form' . DS . 'radiobox.html' );
                                $fieldTpl->block( 'field', array(
                                    'id'                                        => $inputID,
                                    'name'                                      => $input['fieldname'],
                                    'fieldValue'                                => $input['pvalue'],
                                    'required_' . $input['required']            => 'selected',
                                    'required_' . abs( $input['required'] - 1 ) => '',
                                    'type'                                      => 'دکمه رادیویی',
                                ) );
                                $values   = explode( "\n", $input['pvalue'] );
                                foreach ( $values as $value )
                                {
                                    $value    = trim( $value );
                                    $selected = ( $value == $input['value'] ) ? 'selected' : '';
                                    $fieldTpl->block( 'options', array( 'value' => $value, 'selected' => $selected ) );
                                }
                                $fieldTpl->assign( 'site', $config['site'] );
                                $itpl->block( 'fields', array( 'field' => $fieldTpl->dontshowit() ) );
                                break;
                            default:
                                //wrong field type
                                break;
                        }
                    }
                }
                else
                {
                    $form = null;
                }
            }
            if ( is_null( $form ) )
            {
                //new form
                $fields = array( 'frmCode' => '', 'frmID' => '', 'formTitle' => 'فرم', 'submitMSG' => 'اطلاعات با موفیت ارسال شد.', 'formEmail' => '', 'formStatus_0' => '', 'formStatus_1' => 'selected' );
                $tpl->assign( $fields );
            }
            else
            {
                //existing form
                $fields = array(
                    'frmCode'                                      => '[form_' . $form['formID'] . ']',
                    'frmID'                                        => $form['formID'],
                    'formTitle'                                    => $form['name'],
                    'submitMSG'                                    => $form['submitMSG'],
                    'formEmail'                                    => $form['mailto'],
                    'formStatus_' . $form['status']                => 'selected',
                    'formStatus_' . ( abs( 1 - $form['status'] ) ) => '',
                );
                $tpl->assign( $fields );
            }
            /* tabs */
            $class    = ( $formID == 1 ) ? 'current' : '';
            $htpl->block( 'tabs', array( 'current' => $class, 'id' => 1, 'title' => 'فرم جدید', 'url' => 'plugins.php?plugin=form' ) );
            $forms    = $d->Query( 'SELECT * FROM `form`' );
            while ( $formData = $d->fetch( $forms ) )
            {
                $class = ( $formID == $formData['formID'] ) ? 'current' : '';
                $tpl->block( 'tabs', array( 'current' => $class, 'id' => $formData['formID'], 'title' => $formData['name'], 'url' => 'plugins.php?plugin=form&id=' . $formData['formID'] ) );
                $tpl->block( 'extra_div', array( 'inside' => '', 'id' => $formData['formID'] ) );
            }
            /* End tabs */
        }
        else
        {
            //show form data
            $form = $d->Query( "SELECT * FROM `form` WHERE `formID`='$_GET[data]' LIMIT 1" );
            if ( $d->getRows( $form ) != 1 )
            {
                $tpl->block( 'Error', array( 'msg' => 'اطلاعات موجود نیست.' ) );
                $htpl->block( 'tabs', array( 'current' => 'current', 'id' => 1, 'title' => 'فرم', 'url' => 'plugins.php?plugin=form' ) );
            }
            else
            {
                $form = $d->fetch( $form );
                //remove
                if ( isset( $_GET['del'] ) && is_numeric( $_GET['del'] ) )
                {
                    $id = intval( $_GET['del'] );
                    $d->Query( "DELETE FROM `formdata` WHERE `formID`='$form[formID]' AND `id`='$id'" );
                    $tpl->block( 'Success', array( 'msg' => $lang['ok'] ) );
                }
                if ( isset( $_GET['view'] ) && is_numeric( $_GET['view'] ) )
                {
                    $itpl->assign( 'view', true );
                    $view     = intval( $_GET['view'] );
                    $formData = $d->Query( "SELECT * FROM `formdata` WHERE `formID`='$form[formID]' AND `id`='$view' LIMIT 1 " );
                    if ( $d->getRows( $formData ) > 0 )
                    {
                        $formData = $d->fetch( $formData );
                        $data     = unserialize( base64_decode( $formData['value'] ) );
                        foreach ( $data as $key => $value )
                        {
                            $input = '';
                            if ( is_array( $value[0] ) )
                            {
                                foreach ( $value[0] as $key => $cvalue )
                                {
                                    $input .= $key . '<br />';
                                }
                            }
                            else
                            {
                                $input = $value[0];
                            }
                            $itpl->block( 'values', array(
                                'name'  => ( string ) @$value[1],
                                'value' => $input,
                            ) );
                        }
                    }
                }
                $itpl->load( plugins_dir . 'form' . DS . 'data.html' );

                $formData = $d->Query( "SELECT * FROM `formdata` WHERE `formID`='$form[formID]'" );
                $i        = 0;
                while ( $data     = $d->fetch( $formData ) )
                {
                    $i++;
                    $itpl->block( 'list', array(
                        'row'  => $i,
                        'id'   => $data['id'],
                        'ip'   => $data['ip'],
                        'date' => mytime( $config['dtype'], $data['time'], $config['dzone'] ),
                    ) );
                }
                $itpl->assign( 'formID', $form['formID'] );
                $htpl->block( 'tabs', array( 'current' => 'current', 'id' => 1, 'title' => 'فرم ' . $form['name'], 'url' => '#' ) );
            }
        }
        $tpl->assign( 'first', $itpl->dontshowit() );
    }

    function inactivateop()
    {
        global $d;
        $d->Query( "UPDATE `plugins` SET `stat`='0' WHERE `name`='form' LIMIT 1" );
        print_msg( 'ماژول با موفقيت غير فعال شد.', 'Success' );
    }

    function activateop()
    {
        global $d;
        $d->Query( "UPDATE `plugins` SET `stat`='1' WHERE `name`='form' LIMIT 1" );
        print_msg( 'ماژول با موفقيت فعال شد.', 'Success' );
    }

    function installop()
    {
        global $d, $info;
        $q = $d->getrows( "SELECT `stat` FROM `plugins` WHERE `name`='form' LIMIT 1", true );
        if ( $q > 0 )
        {
            print_msg( 'اين ماژول قبلا نصب شده است.', 'Info' );
        }
        else
        {
            global $information;
            $oid = $d->getmax( 'oid', 'menus' );
            $oid++;
            $d->Query( "ALTER TABLE `permissions` ADD `form` INT( 1 ) NOT NULL DEFAULT '0'" );
            $q   = $d->Query( "UPDATE `permissions` SET `form`='1' WHERE `u_id`='$info[u_id]'" );
            $q   = $d->Query( "INSERT INTO `menus` SET `oid`='$oid',`name`='form',`title`='$information[name]',`url`='plugins.php?plugin=form',`type`='1'" );
            $q   = $d->Query( "INSERT INTO `plugins` SET `name`='form',`title`='$information[name]',`stat`='0'" );
            $d->Query( "
			CREATE TABLE IF NOT EXISTS `mp_form` (
			  `id` int(10) NOT NULL AUTO_INCREMENT,
			  `oid` int(10) NOT NULL DEFAULT '0',
			  `title` varchar(255) NOT NULL DEFAULT '',
			  `description` varchar(255) NOT NULL DEFAULT '',
			  `url` varchar(255) NOT NULL,
			  `thumbnail` varchar(255) NOT NULL DEFAULT '',
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1
			" );
            activateop();
            print_msg( 'ماژول با موفقيت نصب شد.', 'Success' );
        }
    }

    function uninstallop()
    {
        global $d;
        $q = $d->getrows( "SELECT `stat` FROM `plugins` WHERE `name`='form' LIMIT 1", true );
        if ( $q <= 0 )
            print_msg( 'اين ماژول نصب نشده است يا استاندارد نيست.', 'Info' );
        else
        {
            $d->Query( "ALTER TABLE `permissions` DROP `form`" );
            $d->Query( "DELETE FROM `plugins` WHERE `name`='form' LIMIT 1" );
            $d->Query( "DELETE FROM `menus` WHERE `name`='form' LIMIT 1" );
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
?>
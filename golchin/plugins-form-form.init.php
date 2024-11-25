<?php
if( !defined('IN_Form') )
die('<a href="http://help.mihanphp.com/plugins" target=_blank>samaneh</a> :: Invalid calling of '.basename (__FILE__));
function detectForm( $tpl )
{
	if(defined('admin'))
	{
		return $tpl;
	}
	global $d, $config;
	if ( preg_match_all( '#\[form_([0-9]+)\]#i', $tpl, $matches ) )
	{
		$matches = $matches[1];
		foreach( $matches as $formID )
		{
			$formID = intval( $formID );
			$form = $d->Query( "SELECT * FROM `form` WHERE `formID`='$formID' LIMIT 1" );
			if( $d->getRows( $form ) === 1 )
			{
				$form 		= $d->fetch( $form );
				$formError 	= array();
				$hideForm  	= false;
				$values		= array();
				if( isset( $_POST['submit_form_' . $formID ] ) )
				{
					$fields 	= unserialize( base64_decode( $form['fields'] ) );
					foreach( $fields as $fieldID => $field )
					{
						switch( $field['fieldtype'] )
						{
							case 'input' :
								if( $field['required'] == 1 )
								{
									if( empty( $_POST['form'][$formID][$fieldID] ) )
									{
										$formError[] = "وارد کردن فیلد $field[fieldname] ضروری است.";
									}
								}
								if( !empty( $_POST['form'][$formID][$fieldID] ) )
								{
									if( isset( $field['max'] ) && is_numeric( $field['max'] ) && mb_strlen( $_POST['form'][$formID][$fieldID] ) > $field['max'] )
									{
										$formError[] = "حداکثر طول مجاز برای $field[fieldname] $field[max] کاراکتر است.";
									}
									else
									if( isset( $field['min'] ) && is_numeric( $field['min'] ) && mb_strlen( $_POST['form'][$formID][$fieldID] ) < $field['min'] )
									{
										$formError[] = "حداقل طول مجاز برای $field[fieldname] $field[min] کاراکتر است.";
									}
									else
									{
										$values[$fieldID] = array( $_POST['form'][$formID][$fieldID], $field['fieldname'] );
									}
								}
							break;
							case 'password' :
								if( $field['required'] == 1 )
								{
									if( empty( $_POST['form'][$formID][$fieldID] ) )
									{
										$formError[] = "وارد کردن فیلد $field[fieldname] ضروری است.";
									}
								}
								if( !empty( $_POST['form'][$formID][$fieldID] ) )
								{
									if( isset( $field['max'] ) && is_numeric( $field['max'] ) && mb_strlen( $_POST['form'][$formID][$fieldID] ) > $field['max'] )
									{
										$formError[] = "حداکثر طول مجاز برای $field[fieldname] $field[max] کاراکتر است.";
									}
									else
									if( isset( $field['min'] ) && is_numeric( $field['min'] ) && mb_strlen( $_POST['form'][$formID][$fieldID] ) < $field['min'] )
									{
										$formError[] = "حداقل طول مجاز برای $field[fieldname] $field[min] کاراکتر است.";
									}
									else
									{
										$values[$fieldID] = array( $_POST['form'][$formID][$fieldID], $field['fieldname'] );
									}
								}
							break;
							case 'textarea' :
								if( $field['required'] == 1 )
								{
									if( empty( $_POST['form'][$formID][$fieldID] ) )
									{
										$formError[] = "وارد کردن فیلد $field[fieldname] ضروری است.";
									}
								}
								if( !empty( $_POST['form'][$formID][$fieldID] ) )
								{
									if( isset( $field['max'] ) && is_numeric( $field['max'] ) && mb_strlen( $_POST['form'][$formID][$fieldID] ) > $field['max'] )
									{
										$formError[] = "حداکثر طول مجاز برای $field[fieldname] $field[max] کاراکتر است.";
									}
									else
									if( isset( $field['min'] ) && is_numeric( $field['min'] ) && mb_strlen( $_POST['form'][$formID][$fieldID] ) < $field['min'] )
									{
										$formError[] = "حداقل طول مجاز برای $field[fieldname] $field[min] کاراکتر است.";
									}
									else
									{
										$values[$fieldID] = array( $_POST['form'][$formID][$fieldID], $field['fieldname'] );
									}
								}
							break;
							case 'selectbox' :
								if( $field['required'] == 1 )
								{
									if( empty( $_POST['form'][$formID][$fieldID] ) )
									{
										$formError[] = "وارد کردن فیلد $field[fieldname] ضروری است.";
									}
								}
								if( !empty( $_POST['form'][$formID][$fieldID] ) )
								{
									$pvalues = explode( "\n", $field['pvalue'] );
									foreach( $pvalues as $id => $value )
									{
										$pvalues[$id] = trim( $value );
									}
									if( !in_array( $_POST['form'][$formID][$fieldID], $pvalues ) )
									{
										$formError[] = "$field[fieldname] بدرستی انتخاب نشده است.";
									}
									else
									{
										$values[$fieldID] = array( $_POST['form'][$formID][$fieldID], $field['fieldname'] );
									}
								}
							break;
							case 'checkbox' :
								if( isset( $_POST['form'][$formID][$fieldID] ) && is_array( $_POST['form'][$formID][$fieldID] ) )
								{
									$pvalues = explode( "\n", $field['pvalue'] );
									foreach( $pvalues as $id => $value )
									{
										$pvalues[$id] = trim( $value );
									}
									foreach( $_POST['form'][$formID][$fieldID] as $id => $value )
									{
										if( !in_array( $value, $pvalues ) )
										{
											//unset( $_POST['form'][$formID][$fieldID][$id] );
										}
									}
									$values[$fieldID] = array( $_POST['form'][$formID][$fieldID], $field['fieldname'] );
									//echo '<pre>';print_r( $values[$fieldID] );exit;
								}
							break;
						}
					}
					//check submit
					if( count( $formError ) === 0 )
					{
						$hideForm = true;
						//insert data into db
						$lastMD5 = md5( base64_encode( serialize( $values ) ) );
						if( isset( $_SESSION[ 'form_' . $formID ] ) && $_SESSION[ 'form_' . $formID ] == $lastMD5 )
						{
							$rid = $_SESSION[ 'form_insert_' . $formID ];
							$form['submitMSG'] = str_replace( '[id]', $rid, $form['submitMSG'] );
						}
						else
						{
							$_SESSION[ 'form_' . $formID ] = $lastMD5;
							$d->iQuery( 'formdata', array(
							'formID'			=>			$formID,
							'time'				=>			time(),
							'ip'				=>			getRealIpAddr(),
							'value'				=>			base64_encode( serialize( $values ) ),
							));
							$rid = $d->last();
							$_SESSION[ 'form_insert_' . $formID ] = $rid;
							$form['submitMSG'] = str_replace( '[id]', $rid, $form['submitMSG'] );
						}
						$form['submitMSG'] = nl2br( $form['submitMSG'] );
						$tpl = str_replace( '[form_' . $formID . ']', "<div class='success'>$form[submitMSG]</div>", $tpl );
						//send mail to $form[mailto]
					}
				}
				if( !$hideForm )
				{
					//show form
					$fields 	= unserialize( base64_decode( $form['fields'] ) );
					$result 	= '';
					$formTpl 	= new samaneh();
					$formTpl->load(plugins_dir . 'form' . DS . 'site' . DS . 'form.html');
					if( count( $formError ) !== 0 )
					{
						$formTpl->block( 'error', array( 'msg' => implode( '<br />', $formError ) ) );
					}
					foreach( $fields as $fieldID => $field )
					{
						switch( $field['fieldtype'] )
						{
							case 'input' :
								$fieldTpl = new samaneh();
								$fieldTpl->load(plugins_dir . 'form' . DS . 'site' . DS . 'input.html');
								$class = array();
								if( $field['required'] == 1 )
								{
									$class[] = 'required';
								}
								if( !empty( $field['min'] ) )
								{
									$class[] = "minSize[$field[min]]";
								}
								if( !empty( $field['max'] ) )
								{
									$class[] = "maxSize[$field[max]]";
								}
								if( count( $class ) > 0 )
								{
									$class = implode( ',', $class );
									$class = 'validate[' . $class . ']';
								}
								$fieldTpl->assign( array(
								'value'		=>	isset( $values[$fieldID] ) ? $values[$fieldID][0] : $field['value'],
								'required'	=>	$field['required'],
								'ID'		=>	$fieldID,
								'formID'	=>	$formID,
								'title'		=>	$field['fieldname'],
								'class'		=>	$class,
								) );
								$result .= $fieldTpl->dontshowit();
							break;
							case 'password' :
								$fieldTpl = new samaneh();
								$fieldTpl->load(plugins_dir . 'form' . DS . 'site' . DS . 'password.html');
								$class = array();
								if( $field['required'] == 1 )
								{
									$class[] = 'required';
								}
								if( !empty( $field['min'] ) )
								{
									$class[] = "minSize[$field[min]]";
								}
								if( !empty( $field['max'] ) )
								{
									$class[] = "maxSize[$field[max]]";
								}
								if( count( $class ) > 0 )
								{
									$class = implode( ',', $class );
									$class = 'validate[' . $class . ']';
								}
								$fieldTpl->assign( array(
								// 'value'		=>	isset( $values[$fieldID] ) ? $values[$fieldID][0] : $field['value'],
								'value'		=>	'', //for security reasons,
								'required'	=>	$field['required'],
								'ID'		=>	$fieldID,
								'formID'	=>	$formID,
								'title'		=>	$field['fieldname'],
								'class'		=>	$class,
								) );
								$result .= $fieldTpl->dontshowit();
							break;
							case 'textarea' :
								$fieldTpl = new samaneh();
								$fieldTpl->load(plugins_dir . 'form' . DS . 'site' . DS . 'textarea.html');
								$class = '';
								$class = array();
								if( $field['required'] == 1 )
								{
									$class[] = 'required';
								}
								if( !empty( $field['min'] ) )
								{
									$class[] = "minSize[$field[min]]";
								}
								if( !empty( $field['max'] ) )
								{
									$class[] = "maxSize[$field[max]]";
								}
								if( count( $class ) > 0 )
								{
									$class = implode( ',', $class );
									$class = 'validate[' . $class . ']';
								}
								$fieldTpl->assign( array(
								'value'		=>	isset( $values[$fieldID] ) ? $values[$fieldID][0] : $field['value'],
								'required'	=>	$field['required'],
								'ID'		=>	$fieldID,
								'formID'	=>	$formID,
								'title'		=>	$field['fieldname'],
								'class'		=>	$class,
								) );
								$result .= $fieldTpl->dontshowit();
							break;
							case 'selectbox' :
								$fieldTpl = new samaneh();
								$fieldTpl->load(plugins_dir . 'form' . DS . 'site' . DS . 'selectbox.html');
								$class = '';
								if( $field['required'] == 1 )
								{
									$class .= 'required';
								}
								$fieldTpl->assign( array(
								'required'	=>	$field['required'],
								'ID'		=>	$fieldID,
								'formID'	=>	$formID,
								'title'		=>	$field['fieldname'],
								'class'		=>	$class,
								) );
								$pvalues = explode( "\n", $field['pvalue'] );
								$defaultValue = isset( $values[$fieldID]) ? $values[$fieldID][0] : $field['value'];
								foreach( $pvalues as $value )
								{
									$value = trim( $value );
									$selected = ( $value == $defaultValue ) ? 'selected' : '';
									$fieldTpl->block( 'options', array( 'value' => $value, 'selected' => $selected ) );
								}
								$result .= $fieldTpl->dontshowit();
							break;
							
							case 'checkbox' :
								$fieldTpl = new samaneh();
								$fieldTpl->load(plugins_dir . 'form' . DS . 'site' . DS . 'checkbox.html');
								$fieldTpl->assign( array(
								'ID'		=>	$fieldID,
								'formID'	=>	$formID,
								'title'		=>	$field['fieldname'],
								) );
								$pvalues = explode( "\n", $field['pvalue'] );
								foreach( $pvalues as $value )
								{
									$value = trim( $value );
									$fieldTpl->block( 'checkboxes', array( 'randID' => md5( rand( 1000,2000).time() ), 'name' => $value ) );
								}
								$result .= $fieldTpl->dontshowit();
							break;
							case 'radiobox' :
								$fieldTpl = new samaneh();
								$fieldTpl->load(plugins_dir . 'form' . DS . 'site' . DS . 'radiobox.html');
								$class = '';
								if( $field['required'] == 1 )
								{
									$class .= 'required';
								}
								$fieldTpl->assign( array(
								'required'	=>	$field['required'],
								'ID'		=>	$fieldID,
								'formID'	=>	$formID,
								'title'		=>	$field['fieldname'],
								'class'		=>	$class,
								) );
								$pvalues = explode( "\n", $field['pvalue'] );
								$defaultValue = isset( $values[$fieldID])  ? $values[$fieldID][0] : $field['value'];
								foreach( $pvalues as $value )
								{
									$value = trim( $value );
									$selected = ( $value == $defaultValue ) ? 'selected' : '';
									$fieldTpl->block( 'options', array( 'value' => $value, 'selected' => $selected ) );
								}
								$result .= $fieldTpl->dontshowit();
							break;
						}
					}
					$formTpl->assign( 'fields', $result );
					$formTpl->assign( 'formID', $formID );
					$tpl = str_ireplace( '[form_' . $formID . ']', $formTpl->dontshowit(), $tpl );
				}
			}
		}
	}
	$tpl = preg_replace( '#\[form_[0-9]+\]#i', '', $tpl );
	return $tpl;
}
<?php
class html{
var $output = '';
var $style = "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'><style>.MsgHead{font-size: 8pt; font-family: Tahoma;border-width: 1px;border-style: solid;padding:7px;text-align:right;margin: 5px 0px;}.error {direction: rtl;background: #F4DE64;color: #000;border-color: #D49E34;}.MsgBody{padding:10px 50px 10px 10px;}.error .MsgBody{background: #FFF}</style>";
var $errortpl = "<div class='alert margin'>[msg]</div>";
var $successtpl = "<div class='alert margin'>[msg]</div>";
var $infotpl = "<div class='alert margin'>[msg]</div>";
	function msg($input,$type = 'error'){
	$temp = '';
		if($type == 'error')
	    {
	 	$tpl = $this->errortpl;
	 	}elseif($type == 'success')

	 	{
	 	$tpl = $this->successtpl;
	 	}else

	 	{
		$tpl = $this->infotpl;
		}
		if(is_array($input))
	    {
        	foreach($input as $value)
            {
            $temp .= $value.'<br>';
            }
	    }
	    else
	    {
        $temp = $input;
	    }
	$this->output = str_replace('[msg]',$temp,$tpl);
	}


    function ts($id,$selected_id,$total,$start = 0)
	{
	$total = ($start == 0) ? $total: $total+$start;
	global $tpl;
    	for($i=$start; $i<=$total; $i++)
    	{
     	$stat = ($i == $selected_id) ? 'selected' : '';
     	$tpl->assign($id.'_'.$i,$stat);
    	}
	}
	function cbox($id,$checked)
	{
	global $tpl;
	$stat = ($checked)  ? 'checked' : ' ';
   	$tpl->assign($id.'_stat',$stat);
	}

	function href($text,$url,$title = '',$target='',$onclick = '00'){	$onclick = ($onclick == '00') ? '' : 'onclick='.$onclick.';';
    $href = '<a title="'.$title.'" target="'.$target.'" '.$onclick.' href="'.$url.'">'.$text.'</a>';
	return $href;
	}

	function input($name,$type,$value,$size,$class='',$dir='',$taborder=''){    $class 		= (empty($class)) 		? 	'' : " class=".$class." ";
    $dir 		= (empty($dir)) 	 	? 	'' : " dir=".$dir." ";
    $taborder 	= (empty($taborder)) 	? 	'' : " tabindex=".$taborder." ";
    $input = '<input type="'.$type.'" size="'.$size.'" value="'.$value.'" name="'.$name.'" '.$class.' '.$dir.' '.$taborder.' ></td>';
	return $input;
	}
	function printout($style = false)
	{
	$style = (!$style) ? die($this->output) : die($this->style.$this->output);
	}

}
<?php
/*************************************************
 * SOFT64 PhpAjaxWhois
 *
 * Version: 1.1
 * Date: 2/22/2008
 *
 ****************************************************/

class AjaxWhois{

    var $serverList;
    var $tr = 0;

function CheckWhois($domain){
		
		return "
		<a href=\"#\" onclick=\"javascript:makeRequest('get.php', '?domain=$domain');\">Whois</a>
		";

}

function tldList(){
            	$i = 0;
                foreach ($this->serverList as $value) {
                    if ($value['check'] == true) $checked=" checked='checked' ";
            		else $checked = " ";
            		
            		echo '<td><input type="checkbox" name="tld_'.$value['tld'].'"'.$checked.' />.'.$value['tld'].'</td>';
                    $i++;            		
            		if ($i > 4) {
            		    $i = 0;
            		    echo '</tr><tr>';
            		}
            	}
            	
}
function processAjaxWhois(){
        $domainName = (isset($_POST['domain'])) ? $_POST['domain'] : '';
        
       	for ($i = 0; $i < sizeof($this->serverList); $i++) {
       		$actTop = "tld_".$this->serverList[$i]['tld'];
			$check = str_replace(".", "_", $actTop);
       		$this->serverList[$i]['check'] = isset($_POST[$check]) ? true : false;
       	}

        if (strlen($domainName)>2){
			echo '<fieldset><legend class="green">Whois results</legend>';
            echo '<table class="tabel">';
            echo '<tr><th colspan="2"></th></tr>';
		
           	for ($i = 0; $i < sizeof($this->serverList); $i++) {
	       		if ($this->serverList[$i]['check']){
			     	$this->showDomainResult($domainName.".".$this->serverList[$i]['tld'],
			     	                        $this->serverList[$i]['server'],
			     	                        $this->serverList[$i]['response']);
			    }
		    }
        
		    echo '</table></fieldset>';
        }

}
function showDomainResult($domain,$server,$findText){
   if ($this->tr == 0){
       $this->tr = 1;
       $class = " class='alt'";
   } else {
       $this->tr = 0;
       $class = "";
   }
   if ($this->checkDomain($domain,$server,$findText)){
      echo "<tr $class><td><span class='td'>$domain</span></td><td class='disponibil'><img src='images/available.png' width='16' height='16' align='absmiddle'/>&nbsp; AVAILABLE</td></tr>";
   }
   else echo "<tr $class><td><span class='ta'>$domain</span></td><td class='ocupat'><img src='images/taken.png' width='16' height='16' align='absmiddle'/>&nbsp; 
TAKEN <a href='http://www.$domain/' target='_blank'> WWW </a>  ".$this->CheckWhois($domain)."</td></tr>";
}

function checkDomain($domain,$server,$findText){
    $con = fsockopen($server, 43);
    if (!$con) return false;
        
    fputs($con, $domain."\r\n");
    $response = ' :';
    while(!feof($con)) {
        $response .= fgets($con,128); 
    }
    fclose($con);
    if (strpos($response, $findText)){
        return true;
    }
    else {
        return false;   
    }
}
//whois servers and extensions
function AjaxWhois(){   
    $this->serverList[0]['tld']      = 'com';
	$this->serverList[0]['server']   = 'whois.crsnic.net';
	$this->serverList[0]['response'] = 'No match for';
	$this->serverList[0]['check']    = true;
	
	$this->serverList[1]['tld']      = 'net';
	$this->serverList[1]['server']   = 'whois.crsnic.net';
	$this->serverList[1]['response'] = 'No match for';
	$this->serverList[1]['check']    = false;

	$this->serverList[2]['tld']      = 'org';
	$this->serverList[2]['server']   = 'whois.publicinterestregistry.net';
	$this->serverList[2]['response'] = 'NOT FOUND';
	$this->serverList[2]['check']    = false;
	
	$this->serverList[3]['tld']      = 'info';
	$this->serverList[3]['server']   = 'whois.afilias.net';
	$this->serverList[3]['response'] = 'NOT FOUND';
	$this->serverList[3]['check']    = false;
	
	$this->serverList[4]['tld']      = 'name';
	$this->serverList[4]['server']   = 'whois.nic.name';
	$this->serverList[4]['response'] = 'No match';
	$this->serverList[4]['check']    = false;
	
	$this->serverList[5]['tld']      = 'us';
	$this->serverList[5]['server']   = 'whois.nic.us';
	$this->serverList[5]['response'] = 'Not found:';
	$this->serverList[5]['check']    = false;

	$this->serverList[6]['tld']      = 'biz';
	$this->serverList[6]['server']   = 'whois.nic.biz';
	$this->serverList[6]['response'] = 'Not found';
	$this->serverList[6]['check']    = false;
	
	$this->serverList[7]['tld']      = 'ca';
	$this->serverList[7]['server']   = 'whois.cira.ca';
	$this->serverList[7]['response'] = 'AVAIL';
	$this->serverList[7]['check']    = false;

	$this->serverList[8]['tld']      = 'tv';
	$this->serverList[8]['server']   = 'whois.internic.net';
	$this->serverList[8]['response'] = 'No match for';
	$this->serverList[8]['check']    = false;

	$this->serverList[9]['tld']      = 'eu';
	$this->serverList[9]['server']   = 'whois.eu';
	$this->serverList[9]['response'] = 'FREE';
	$this->serverList[9]['check']    = false;

	$this->serverList[10]['tld']      = 'ro';
	$this->serverList[10]['server']   = 'whois.rotld.ro';
	$this->serverList[10]['response'] = 'No entries found for the selected source';
	$this->serverList[10]['check']    = false;

	$this->serverList[11]['tld']      = 'ws';
	$this->serverList[11]['server']   = 'whois.nic.ws';
	$this->serverList[11]['response'] = 'No match for';
	$this->serverList[11]['check']    = false;
	
	$this->serverList[12]['tld']      = 'co.uk';
	$this->serverList[12]['server']   = 'whois.nic.uk';
	$this->serverList[12]['response'] = 'No match for';
	$this->serverList[12]['check']    = false;

	$this->serverList[13]['tld']      = 'de';
	$this->serverList[13]['server']   = 'whois.denic.de';
	$this->serverList[13]['response'] = 'not found in database';
	$this->serverList[13]['check']    = false;
	
	$this->serverList[13]['tld']      = 'ir';
	$this->serverList[13]['server']   = 'whois.nic.ir';
	$this->serverList[13]['response'] = 'not found in database';
	$this->serverList[13]['check']    = false;

}
}
?>
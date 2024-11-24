<?php 
/*************************************************
 * SOFT64 PhpAjaxWhois
 *
 * Version: 1.1
 * Date: 1/29/2008
 *
 ****************************************************/

require_once("AjaxWhois.php"); 
$whois = new AjaxWhois();
?>

<?php 
			echo '<fieldset><legend class="green">Whois results</legend><div align="left">';
// always scour user input
$domain=escapeshellcmd($_GET['domain']);
// path to whois
$cmd="/usr/bin/whois $domain";

exec($cmd,$data);
$max=count($data);
$count=0;
if($max):
    while($count <= $max):
        echo "$data[$count]<br>";
        // check each line returned for the whois server for the domain        
        if(ereg("Whois Server: ", $data[$count])):
                $whoserver=ereg_replace("Whois Server: ", "", $data[$count]);
        endif;
        $count++;
    endwhile;
endif;
if(isset($whoserver)):
    $cmd="/usr/local/php/bin/whois -h$whoserver $domain";
    unset($data);
    exec($cmd,$data);
    $max=count($data);
    $count=0;
    if($max):
        while($count <= $max):
            echo "$data[$count]<br>";
            $count++;
        endwhile;
    endif;
endif;
		    echo '</div></fieldset>';

?>

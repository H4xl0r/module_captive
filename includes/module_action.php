<? 
/*
    Copyright (C) 2013-2020 xtr4nge [_AT_] gmail.com

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/ 
?>
<?
include "../../../config/config.php";
include "../_info_.php";
include "../../../login_check.php";
include "../../../functions.php";
include "options_config.php";

// Checking POST & GET variables...
if ($regex == 1) {
    regex_standard($_GET["service"], "../msg.php", $regex_extra);
    regex_standard($_GET["action"], "../msg.php", $regex_extra);
    regex_standard($_GET["page"], "../msg.php", $regex_extra);
    regex_standard($io_action, "../msg.php", $regex_extra);
    regex_standard($_GET["mac"], "../msg.php", $regex_extra);
    regex_standard($_GET["install"], "../msg.php", $regex_extra);
	regex_standard($_GET["db_id"], "../msg.php", $regex_extra);
}

$service = $_GET['service'];
$action = $_GET['action'];
$page = $_GET['page'];
$mac =  strtoupper($_GET['mac']);
$install = $_GET['install'];
$db_id = $_GET['db_id'];

if (strlen($db_id) == 13) {
	$exec = "sed -i '/$db_id/d' $json_file";
	exec_fruitywifi($exec);
	header("Location: ../index.php?tab=4");
	exit;
}

if($service == "station" and $mac != "") {
		if($action == "allow") {
			$exec = "iptables -t nat -A PREROUTING -p tcp -m mac --mac-source $mac -j MARK --set-mark 99";
		} else {
			$exec = "iptables -t nat -D PREROUTING -p tcp -m mac --mac-source $mac -j MARK --set-mark 99";
		}
		exec_fruitywifi($exec);
    header("Location: ../index.php?tab=1");
	exit;
}

if($service == "captive") {
    
    if ($action == "start") {
        // START MODULE
        
        // COPY LOG
        if ( 0 < filesize( $mod_logs ) ) {
            $exec = "cp $mod_logs $mod_logs_history/".gmdate("Ymd-H-i-s").".log";
			exec_fruitywifi($exec);
            
            $exec = "echo '' > $mod_logs";
			exec_fruitywifi($exec);
        }	
			# Redirect all non marked traffic to the portal http / https 80 / Block
			$exec = "$bin_iptables -t nat -A PREROUTING -i $io_in_iface -p tcp -m mark ! --mark 99 -m tcp -m multiport --dports 80,443 -j DNAT --to-destination $io_in_ip:80";
			exec_fruitywifi($exec);
			
			# FORWARD
			$exec = "echo '1' > /proc/sys/net/ipv4/ip_forward";
			exec_fruitywifi($exec);
        
            // INCLUDE INDEX
		  if (!file_exists("/var/www/index.php")) {
            $exec = "$bin_echo '.' >> /var/www/index.php";
            exec_fruitywifi($exec);
            }
	
            $exec = "grep 'FruityWifi-Phishing' /var/www/index.php";
            $isphishingup = exec($exec);
            if ($isphishingup  != "") {
                $exec = "sed -i '/FruityWifi-Phishing/d' /var/www/index.php";
			     exec_fruitywifi($exec);
	    
                $exec = "sed -i 1i'<? include \\\"site\/index.php\\\"; \/\* FruityWifi-Phishing \*\/ ?>' /var/www/index.php";
			     exec_fruitywifi($exec);
            
                $exec = "sed -i 1i'<? header(\\\"Location: captive\/index.php\\\"); exit; \/\* FruityWifi-Captive \*\/ ?>' /var/www/index.php";
			     exec_fruitywifi($exec); 
            } else {
            $exec = "sed -i 1i'<? header(\\\"Location: captive\/index.php\\\"); exit; \/\* FruityWifi-Captive \*\/ ?>' /var/www/index.php";
			exec_fruitywifi($exec);
            }
		  # SET ISUP
			$exec = "$bin_sed -i 's/^\\\$mod_captive_block=.*/\\\$mod_captive_block= \\\"close\\\";/g' ../_info_.php";
			exec_fruitywifi($exec);
        
    } else if($action == "stop") {
        
        // STOP MODULE

        // REMOVE INCLUDE
        $exec = "sed -i '/FruityWifi-Captive/d' /var/www/index.php";
		exec_fruitywifi($exec);

			# Redirect all marked traffic to the portal		
			$exec = "$bin_iptables -t nat -D PREROUTING -i $io_in_iface -p tcp -m mark ! --mark 99 -m tcp -m multiport --dports 80,443 -j DNAT --to-destination $io_in_ip:80";
			exec_fruitywifi($exec);
			
			$exec = "$bin_iptables -t nat -L | grep -iEe 'MARK.+MAC' | awk '{print \\\$7}'";
			$output = exec_fruitywifi($exec);
			
			for ($i=0; $i < sizeof($output); $i++) {
				$mac = $output[$i];
				$exec = "$bin_iptables -t nat -D PREROUTING -p tcp -m mac --mac-source $mac -j MARK --set-mark 99";
				exec_fruitywifi($exec);
			}            
        // CLEAN USERS FILE
        $exec = "echo '-' > $file_users";
		exec_fruitywifi($exec);
        
        // COPY LOG
        if ( 0 < filesize( $mod_logs ) ) {
            $exec = "cp $mod_logs $mod_logs_history/".gmdate("Ymd-H-i-s").".log";
			exec_fruitywifi($exec);
            
            $exec = "echo '' > $mod_logs";
			exec_fruitywifi($exec);
        }
        
        # SET ISUP
        $exec = "$bin_sed -i 's/^\\\$mod_captive_block=.*/\\\$mod_captive_block= \"open\";/g' ../_info_.php";
        exec_fruitywifi($exec);
   }
}

if($service == "install_portal") {
	$exec = "/bin/ln -s $mod_path/www.captive /var/www/captive";
    exec_fruitywifi($exec);
}

$filename = $file_users;

if ($service == "users" and $mac != "") {
    $mac = trim($mac,'*');

    if ($action == "delete") {
    
		$exec = "$bin_sed -i '/$mac/d' $filename";
		exec_fruitywifi($exec);
		
		$exec = "$bin_iptables -D internet -t mangle -m mac --mac-source $mac -j RETURN";
		exec_fruitywifi($exec);
		
		// ADD TO LOGS
		$exec = "$bin_echo 'DELETE: $mac|".date("Y-m-d h:i:s")."' >> $mod_logs ";
		exec_fruitywifi($exec);
	
    } 
    
    header('Location: ../index.php?tab=2');
    exit;
}

if ($install == "install_captive") {

    $exec = "$bin_chmod 755 install.sh";
    exec_fruitywifi($exec);

    $exec = "$bin_sudo ./install.sh > $log_path/install.txt &";
    exec_fruitywifi($exec);

    header('Location: ../../install.php?module='.$mod_name);
    exit;
}

if ($page == "status") {
    header('Location: ../../../action.php');
} else {
    header('Location: ../../action.php?page='.$mod_name);
}

?>

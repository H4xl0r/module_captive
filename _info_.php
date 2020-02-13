<?
$mod_name="captive";
$mod_version="2.b";
$mod_path="/usr/share/fruitywifi/www/modules/$mod_name";
$mod_logs="$log_path/captive.log"; 
$mod_logs_history="$mod_path/includes/logs/";
$mod_panel="show";
$mod_alias="Captive";
$mod_isup = "iptables -t nat -L | grep -iEe 'mark match ! 0x63'";
$mod_co= "Nginx";
$mod_coisup = "ps auxww | grep -E 'nginx.+/modules/nginx/.+nginx.conf' | grep -v -e grep";

# OPTIONS
$mod_captive_block= open;
$mod_captive_portal_name="Free WiFi Zone";
$mod_captive_policy="0";
$mod_captive_welcome="1";
$mod_captive_recon="1";
$mod_captive_inject="0";
$mod_captive_validate_user="0";
$mod_captive_validate_pass="0";
$mod_captive_portal_user="demo";
$mod_captive_portal_pass="demo";
$mod_captive_template="portal_default";
$mod_captive_site="0";
$mod_captive_site_value="captive.home";
$mod_captive_redirect_change_http="1";
$mod_captive_redirect_change_www="0";
$mod_captive_redirect_timestamp="0";
$mod_captive_redirect="1";
$mod_captive_redirect_value="google.com";

# OTHER
$mod_dnsmasq_dhcp_script_path="/usr/share/fruitywifi/www/modules/ap/includes/dnsmasq-dhcp-script.sh";

# EXEC
$bin_sudo = "/usr/bin/sudo";
$bin_iptables = "/sbin/iptables";
$bin_awk = "/usr/bin/awk";
$bin_grep = "/bin/grep";
$bin_sed = "/bin/sed";
$bin_conntrack = "/usr/sbin/conntrack";
$bin_cat = "/bin/cat";
$bin_echo = "/bin/echo";
$bin_ln = "/bin/ln";
$bin_arp = "/usr/sbin/arp";

# PORTAL
$mod_portal_name="Hotel";
$mod_portal_redirect="2";
$mod_portal_redirect_url="";
$mod_portal_policy="1";
$mod_portal_thanks="1";

# DB
$json_file = "$mod_path/includes/captive_db.json";

# FILE
$file_users = "/var/www/captive/admin/users";
$portal_name="Free WiFi Zone";

?>

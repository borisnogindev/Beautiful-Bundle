<?php
require_once("config.php");

$domain = preg_replace('/^www\./', '', $_GET['shop']);
if (trim($domain) == "") {
	exit;
}

$check_app_installed = "SELECT `id`, `app_status`, `payment_status`,`token` FROM `app` WHERE `shop` = '".$domain."' OR `shop_domain` = '".$domain."' LIMIT 1";
$qry = mysql_query($check_app_installed);                                                                                                             
if (mysql_num_rows($qry) > 0) {                                                                 
	$res_data = mysql_fetch_assoc($qry);  
	if ($res_data['app_status'] != 'installed' || $res_data['payment_status'] != 'accepted') {
		exit;
	}
	else
	{
		$js = '$js';
		echo "document.write(\"<img src='//".DOMAIN_NAME."/loading.gif' class='ty_main_loader' style='display:block;padding-left: 157px;width:15%'>\");";
		echo "document.write(\"<script type='text/javascript' src='//".DOMAIN_NAME."/ty_inc.js'></script>\");";
	}
} 
?>
<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_WARNING);

$server = "localhost";
$db_user = "shopiapp_upsell";
$db_pwd = "upsell121*1";
$db_name = "shopiapp_beautifulimage";

$db_obj = mysql_connect($server,$db_user, $db_pwd);
mysql_select_db($db_name);
mysql_set_charset('utf-8');

$sql_details = array(
    'user' => $db_user,
    'pass' => $db_pwd,
    'db' => $db_name,
    'host' => $server
);

define('DOMAIN_NAME', 'shopiapps.io/beautifull_bundle_images');
define('SITE_URL', 'https://shopiapps.io/beautifull_bundle_images');

session_start();
  
if (!$db_obj) {
	echo "Failed to connect to MySQL: " . mysql_error();
}

define('SHOPIFY_API_KEY', '25bc5021e93cf1df868e5c139b0aa438');
define('SHOPIFY_SECRET', '33bb74af7e3c7e1b8559568f51e8d80c');
define('SHOPIFY_SCOPE', 'write_products,write_content,write_themes');
define('PLAN', 'free'); /* free/paid */
define('PLAN_PRICE', 27.99); /* 2.99 */
define('PLAN_TRIAL', 7); /* 7 */
define('PLAN_MODE', true); /* true/false */

define('APP_NAME',"Beautifull Bundle Images");
function loopAndFind($array, $index, $search){
	$returnArray = array();
	foreach($array as $k=>$v){
		if($v[$index] == $search){   
			$returnArray[] = $v;
		} 
	}
	return $returnArray;
}
?>
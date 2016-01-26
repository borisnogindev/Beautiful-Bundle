<?php
require_once("config.php");

$to      = 'jaron.smith2006@gmail.com';
$subject = APP_NAME.' : Webbook Called';
$message = APP_NAME.' : Webbook Called';
$headers = 'From: jaron.smith2006@gmail.com' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();
    
$entityBody = file_get_contents('php://input');
$arr = json_decode($entityBody);
$store_domain = $arr->myshopify_domain;
$update_sql = "UPDATE `app` SET `app_status` = 'uninstalled', status_change_date = '".date('Y-m-d H:i:s')."' WHERE `shop` = '".$store_domain."'";
mysql_query($update_sql);
mysql_query("UPDATE `related_settings` SET `install_status` = '0' where `shop` = '".$store_domain."'");

#mail($to, $subject, $entityBody, $headers);
exit;

?>
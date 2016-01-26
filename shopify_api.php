<?php
require '../shopifyclient.php';
require '../vendor/autoload.php';
use sandeepshetty\shopify_api;

$shop = $_REQUEST['shop'];
$_SESSION['shop'] = $shop;

$select_sql = "SELECT `id`,`token` FROM `app` WHERE `shop` = '" . $shop . "' ORDER BY `id` DESC LIMIT 1";
$res = mysql_query($select_sql);
if (mysql_num_rows($res) > 0) {
    $res_arr = mysql_fetch_assoc($res);
    $token = $res_arr['token'];
}

$sc = new ShopifyClient($shop, $token, SHOPIFY_API_KEY, SHOPIFY_SECRET);
?>
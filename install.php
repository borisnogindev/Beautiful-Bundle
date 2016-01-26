<?php
session_start();

require_once("config.php");
require 'shopifyclient.php';
//require 'shopify.php';
require 'vendor/autoload.php';

use sandeepshetty\shopify_api;

$shop = ($_REQUEST['shop'] != '') ? $_REQUEST['shop'] : $_SESSION['shop'];
$date = date("Y-m-d H:i:s");
$select_sql = "SELECT `id`,`token` FROM `app` WHERE `shop` = '" . $shop . "' ORDER BY `id` DESC LIMIT 1";
$res = mysql_query($select_sql);
if (mysql_num_rows($res) > 0) {
    $res_arr = mysql_fetch_assoc($res);
    $token = $res_arr['token'];
}

$sc = new ShopifyClient($shop, $token, SHOPIFY_API_KEY, SHOPIFY_SECRET);
session_unset();

if (PLAN == 'paid') {
    $charge_id = $_GET['charge_id'];
    if ($shop != '' && $charge_id != '') {
        /* Recurring Charge Code Start */
        $charge_stat = $sc->call('GET', "/admin/recurring_application_charges/{$charge_id}.json");

        if ($charge_stat['status'] == 'accepted') {
            $billing_on = date("Y-m-d") . "T00:00:00+00:00";
            $create_at = date("Y-m-d\TH:i:s") . "+05:30";
            $updated_at = date("Y-m-d\TH:i:s") . "+05:30";
            $trial_ends_on = date("Y-m-d\TH:i:s", strtotime("+7 days")) . "+05:30";

            $act_recurring_charge_arr = array("recurring_application_charge" =>
                array("activated_on" => null,
                    "billing_on" => $billing_on,
                    "cancelled_on" => null,
                    "created_at" => $create_at,
                    "id" => $charge_id,
                    "name" => "Standard Plan",
                    "price" => PLAN_PRICE,
                    "return_url" => SITE_URL . "/index.php",
                    "status" => "accepted",
                    "test" => PLAN_MODE,
                    "trial_days" => PLAN_TRIAL,
                    "trial_ends_on" => $trial_ends_on,
                    "updated_at" => $updated_at
            ));
            try {
                $act_recurring_charge_call = $sc->call('POST', "/admin/recurring_application_charges/{$charge_id}/activate.json", $act_recurring_charge_arr);
                $update_sql = "UPDATE `app` SET `payment_status` = 'accepted' WHERE `shop` = '" . $shop . "'";
                mysql_query($update_sql);


                /* Webhook for App Uninstall Track */
                $themes = $sc->call('GET', '/admin/themes.json');
                $active_theme_arr = loopAndFind($themes, 'role', 'main');
                $active_theme_id = $active_theme_arr[0]['id'];


                /* Webhook for App Uninstall Track */
                try {
                    $webhooks_arr = array("webhook" =>
                        array(
                            "topic" => "app/uninstalled",
                            "address" => SITE_URL . "/hook.php",
                            "format" => "json"
                    ));
                    $resp = $sc->call('POST', "/admin/webhooks.json", $webhooks_arr);
                } catch (exception $e) {
                    
                }

                /* Add Liquid File */
                try {
                    $assets_arr = array("asset" =>
                        array(
                            "key" => "snippets/beautifull_bundle_image_ad.liquid",
                            "src" => SITE_URL . "/beautifull_bundle_image_ad.liquid"
                    ));
                
                    $resp = $sc->call('PUT', "/admin/themes/{$active_theme_id}/assets.json", $assets_arr);
                } catch (exception $e) {
                    
                }
				
				/* Add Template Liquid File */
                try {
                    $assets_arr = array("asset" =>
                        array(
                            "key" => "templates/product.bundles.liquid",
                            "src" => SITE_URL . "/product.bundles.liquid"
                    ));
                
                    $resp = $sc->call('PUT', "/admin/themes/{$active_theme_id}/assets.json", $assets_arr);
                } catch (exception $e) {
                }
				
				
				
            } catch (exception $e) {
                echo $e;
                exit;
            }
        } else {
            $update_sql = "UPDATE `app` SET `payment_status` = 'declined',`app_status` = 'uninstalled' WHERE `shop` = '" . $shop . "'";
            if (mysql_query($update_sql)) {
                header("Location: index.php");
                exit;
            }
        }
    }
} else {
    try {
        $update_sql = "UPDATE `app` SET `payment_status` = 'accepted' WHERE `shop` = '" . $shop . "'";
        mysql_query($update_sql);

        /* Webhook for App Uninstall Track */
        $themes = $sc->call('GET', '/admin/themes.json');
        $active_theme_arr = loopAndFind($themes, 'role', 'main');
        $active_theme_id = $active_theme_arr[0]['id'];

        /* Webhook for App Uninstall Track */
        try {
            $webhooks_arr = array("webhook" =>
                array(
                    "topic" => "app/uninstalled",
                    "address" => SITE_URL . "/hook.php",
                    "format" => "json"
            ));
            $resp = $sc->call('POST', "/admin/webhooks.json", $webhooks_arr);
        } catch (exception $e) {
            
        }

        /* Add Liquid File */
        try {
            $assets_arr = array("asset" =>
                array(
                    "key" => "snippets/beautifull_bundle_image_ad.liquid",
                    "src" => SITE_URL . "/beautifull_bundle_image_ad.liquid"
            ));
        
            $resp = $sc->call('PUT', "/admin/themes/{$active_theme_id}/assets.json", $assets_arr);
        } catch (exception $e) {
            
        }
		
						/* Add Template Liquid File */
                try {
                    $assets_arr = array("asset" =>
                        array(
                            "key" => "templates/product.bundles.liquid",
                            "src" => SITE_URL . "/product.bundles.liquid"
                    ));
                
                    $resp = $sc->call('PUT', "/admin/themes/{$active_theme_id}/assets.json", $assets_arr);
                } catch (exception $e) {
                }


        /* Add Loader SVG File */
        try {
            $cu_loader = '<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="50px" height="50px" viewBox="0 0 50 50" enable-background="new 0 0 50 50" xml:space="preserve">
                            <g id="spinner">
                              <animateTransform attributeName="transform" type="rotate" values="0,25,25; 359,25,25;" dur="0.8s" repeatCount="indefinite"/>
                              <path fill="none" stroke="#FFFFFF" stroke-width="1.8977" stroke-miterlimit="10" d="M40.088,6.696  c5.272,4.35,8.633,10.935,8.633,18.304c0,13.101-10.62,23.721-23.721,23.721S1.279,38.101,1.279,25S11.899,1.279,25,1.279"/>
                            </g>
                        </svg>';
            $cu_loader = base64_encode($cu_loader);

            $assets_arr = array("asset" =>
                array(
                    "key" => "assets/cu_loader.svg",
                    "attachment" => $cu_loader
            ));

            $resp = $sc->call('PUT', "/admin/themes/{$active_theme_id}/assets.json", $assets_arr);
            $cu_loader_public_url = mysql_real_escape_string($resp['public_url']);
        } catch (exception $e) {
            
        }
    } catch (exception $e) {
        echo $e;
        exit;
    }
}
$_SESSION['shop'] = $shop;
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <title>Installing..</title>
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <meta http-equiv="content-language" content="en" />
        <link href="style.css" rel="stylesheet" type="text/css" />
    </head>
    <body>

        <div class="span24">
            <div id="app-install">
                <p style="font-size: 15px; margin-top: 21px;" class="app_status">Please Wait.. the app is being installed..</p>
            </div>
        </div>
        <script>
                setTimeout('redirectToAdmin()', 3000);
            function redirectToAdmin() {
                //window.location.href = "<?= SITE_URL ?>/admin/add_image.php?shop=<?= $shop; ?>";
                window.location.href = "//<?= $shop ?>/admin/apps/<?= SHOPIFY_API_KEY ?>";
            }
        </script>

    </body>
</html>

<?php
require_once("config.php");

require 'shopifyclient.php';
//require 'shopify.php';
require 'vendor/autoload.php';
use sandeepshetty\shopify_api;

if ($_SESSION) {
#	echo "<pre>";
#	print_R($_SESSION);
#	echo "</pre>";
	#exit;
}

if (isset($_GET['logout']) && $_GET['logout'] == 'true') {
	session_unset();
}

if((isset($_POST['addShop']) && $_POST['shop'] != "") || isset($_GET['code']) || $_SESSION['token'] != '' || isset($_GET['mode'])  || (isset($_GET['shop']) && $_GET['shop'] != '' ) ) {
	
	$shop = ($_REQUEST['shop'] != '')?$_REQUEST['shop']:$_SESSION['shop'];
       
	$select_sql = "SELECT `id`,`token`,`payment_status`, `app_status` FROM `app` WHERE `shop` = '".$shop."' AND (`app_status` = 'installed') AND (`payment_status` = 'free' OR `payment_status` = 'accepted') ORDER BY `id` DESC LIMIT 1";
	$res = mysql_query($select_sql);
	if (mysql_num_rows($res) > 0) {
		header("Location: admin/add_image.php?shop=".$shop);
		exit;
	}
	
	if (isset($_GET['code'])) {
	
		$select_sql = "SELECT `id`, `payment_status` FROM `app` WHERE `shop` = '".$_GET['shop']."' ORDER BY `id` DESC LIMIT 1";
		$res = mysql_query($select_sql);
		
		if (mysql_num_rows($res) > 0) {
			$result = mysql_fetch_assoc($res);
			/*if ($result['payment_status'] == 'accepted' || $result['payment_status'] == 'free') {
				header("Location: widget.php?shop=".$_GET['shop']);
				exit;
			}*/
			
			$shopifyClient = new ShopifyClient($_GET['shop'], "", SHOPIFY_API_KEY, SHOPIFY_SECRET);
			$access_token = $shopifyClient->getAccessToken($_GET['code']);
			        
			session_unset();
			
			$_SESSION['token'] = $access_token;
			if ($_SESSION['token'] != '') {
				$_SESSION['shop'] = $_GET['shop'];
				$update_sql = "UPDATE `app` SET `code` = '".$_GET['code']."', `token` = '".$_SESSION['token']."', `payment_status` = 'pending', created_date = '".date('Y-m-d H:i:s')."' WHERE `id` = '".$result['id']."'";
				mysql_query($update_sql);    
				
				$delete_all_other_entries = "DELETE FROM `app` WHERE `id` != '".$result['id']."' AND `shop` = '".$_GET['shop']."'";
				mysql_query($delete_all_other_entries);
			}
		} else {
			$error_message = "Something went wrong, Please try after sometime.";
		}
		
		header("Location: index.php");
		exit;
		     
	} elseif (isset($_POST['shop']) || (isset($_GET['shop']) && !isset($_GET['c_id']))) {
	
		$shop = isset($_POST['shop']) ? $_POST['shop'] : $_GET['shop'];
	     $check_sql = "SELECT `id`, `payment_status`, `app_status` FROM `app` WHERE `shop` = '".$shop."' AND `code` != '' AND `token` != ''";
	     #echo $check_sql; exit;
		$chk_res = mysql_query($check_sql);
		if (mysql_num_rows($chk_res) > 0) {
			$result = mysql_fetch_assoc($chk_res);
			if (($result['payment_status'] == 'accepted' || $result['payment_status'] == 'free')  && $result['app_status'] == 'installed') {
				header("Location: widget.php?shop=".$_GET['shop']);
				exit;
			}/* elseif ($result['payment_status'] == 'declined') {
				header("Location: index.php?p=0");
				exit;
			}*/
		}
	     
		$ins_sql = "INSERT INTO app (shop) VALUES ('".$_REQUEST['shop']."')";
		$shop_id = mysql_query($ins_sql); 

		$shopifyClient = new ShopifyClient($shop, "", SHOPIFY_API_KEY, SHOPIFY_SECRET);
		
		$pageURL = 'http';
		if ($_SERVER["HTTPS"] == "on") { $pageURL .= "s"; }
		$pageURL .= "://";
		if ($_SERVER["SERVER_PORT"] != "80") {
		    $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		} else {
		    $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		}

		$pageURL = str_replace("?", "&", urldecode($pageURL)); 
		
		header("Location: " . $shopifyClient->getAuthorizeUrl(SHOPIFY_SCOPE, $pageURL));
		exit;
	}
	
	if ($_REQUEST['shop'] != '' || $_SESSION['shop'] != '') {
	
		$shop = ($_REQUEST['shop'] != '')?$_REQUEST['shop']:$_SESSION['shop'];
		$select_sql = "SELECT `id`,`token`,`payment_status`, `app_status` FROM `app` WHERE `shop` = '".$shop."' ORDER BY `id` DESC LIMIT 1";
		$res = mysql_query($select_sql);
		if (mysql_num_rows($res) > 0) {
			$res_arr = mysql_fetch_assoc($res);
			$token = $res_arr['token'];
			$payment_status = $res_arr['payment_status'];
			$app_status = $res_arr['app_status'];
			$id = $res_arr['id'];
		}

		if (($payment_status == 'accepted' || $payment_status == 'free') && $app_status == 'installed') {
			header("Location: admin/add_image.php?shop=".$shop);
			exit;
		}
	
		if ($_SESSION['token'] != '') {
			$token = $_SESSION['token']; 
		}	
		
		$sc = new ShopifyClient($shop, $token, SHOPIFY_API_KEY, SHOPIFY_SECRET);
		
		$fields_arr = array("fields" => "myshopify_domain,domain");
		$shop_resp = $sc->call('GET', "/admin/shop.json", $fields_arr);
		$domain = preg_replace('/^www\./', '', $shop_resp['domain']);
		$app_update_sql = "UPDATE `app` SET `shop_domain` = '".$domain."' WHERE `id` = '".$id."'";
		mysql_query($app_update_sql); 
		
		$status_update_sql = "UPDATE `app` SET `app_status` = 'installed' WHERE `id` = '".$id."'";
		mysql_query($status_update_sql);
			
		if (PLAN == 'paid') {
			/*Recurring Charge Code Start*/
	
			$recurring_charge_arr = array("recurring_application_charge" => 
									array("name" => "Standard Plan",
										"price" => PLAN_PRICE,
										"return_url" => SITE_URL."/install.php?shop={$shop}",
										"trial_days" => PLAN_TRIAL,
										"test" => PLAN_MODE
									));
			$recurring_charge_call = $sc->call('POST', "/admin/recurring_application_charges.json", $recurring_charge_arr);
			
			session_unset();
			if (count($recurring_charge_call) > 0) {
				if ($recurring_charge_call['confirmation_url'] != '') {
					header("Location: ".$recurring_charge_call['confirmation_url']);
				}
			}
		} else {
			#header("location: ".SITE_URL."/install.php?shop={$shop}");
                        header("location: ".SITE_URL."/install.php?shop={$shop}");
		}
	}
} else {
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
     <head>
          <title>Add Your Shop</title>
          <meta http-equiv="content-type" content="text/html; charset=utf-8" />
          <meta http-equiv="content-language" content="en" />
          <link href="style.css" rel="stylesheet" type="text/css" />
	</head>
	<body>

<?php
      if(isset($_POST['addShop']) && ($_POST['shop'] == "" && $_GET['shop'] == "")){
            echo "<center style='color:red;'>Please enter shop name.</center>";
      }
      if(isset($_GET['p']) && $_GET['p'] == 0){
            echo "<center style='color:red;'>Please contact us at jaron.smith2006@gmail.com to get this app installed.</center>";
      }
      if(isset($_GET['a']) && $_GET['a'] == 0){
            echo "<center style='color:red;'>You already have installed this app.</center>";
      }
?>
          <div class="span24">
            <div id="app-install">

                <div class="clearfix" id="visual-install-details">
                    <div class="oauth-app-icon">
                        <img id="app-logo" src="image/shopify-app-logo.png" style="height: 63px;"/>
                        <p><?=APP_NAME;?></p>
                    </div>

                    <div class="connect">
                        <p class="readwrite"></p>
                    </div>

                    <div class="shopify-icon">
                        <img width="60" height="60" id="shopify-logo" src="image/shopify-app-logo.png" />
                        <p>Your Store</p>
                    </div>
                </div>

                <h2>You're about to install <span><?=APP_NAME;?></span></h2>

                <p class="app_status">This application will be able to access and modify your store data.</p>

                <form method="POST">
                    <input type="text" id="shop" name="shop" value="" placeholder="Enter your shop name"/>
                    <input type="submit" id="addShop" name="addShop" value="Install" class="btn primary"/>
                </form>
            </div>
        </div>
      
</body>
</html>
<?php
}
exit;
?>

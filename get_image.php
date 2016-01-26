<?php
header('Access-Control-Allow-Origin: *');
include("config.php");

$current_date = date("Y-m-d H:i:s");

$shop = "";
if(isset($_REQUEST['shop']) && $_REQUEST['shop'] != "") {
    $shop = $_REQUEST["shop"];
    $shop = trim($shop);
}

$bid = base64_decode(urldecode($_REQUEST["bn_id"]));

$checl_pro_sql = "SELECT * FROM bundle_images WHERE bid='".$bid."' and shop='".$shop."'";
$res_pro = mysql_query($checl_pro_sql);
$total_row = mysql_num_rows($res_pro);
#echo $total_row.">>";
if($total_row > 0) { 
   $image_data = mysql_fetch_array($res_pro);
   $cordinates = explode("||",trim($image_data["b_cordinates"], '||'));
   $handles = explode("|||",$image_data["b_target_list_handle"]);
   ?>
   <img id="img_bundle" src="<?php echo SITE_URL."/admin/upload_images/$shop/" . $image_data["b_image"]; ?>?<?php echo time(); ?>" usemap="#bundleimagecords" alt="" />
	<map name="bundleimagecords">
		<? foreach($cordinates as $key => $cord) { ?>
		<area shape="rect" coords="<?php echo $cord; ?>" href="#" data-product-handle="<?php echo $handles[$key]; ?>" />
		<?php } ?>
	</map>
   <?php
}
?>
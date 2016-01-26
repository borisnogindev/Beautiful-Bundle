<?php
header("Access-Control-Allow-Origin: *");
include("../config.php");
include("../shopify_api.php");
include("shopify_function.php");

$product_list = array();

$collection_list = array();
$All_coll_array = getCollection($shop, $token);
if(count($All_coll_array["result"]) > 0 && $All_coll_array["status"] == "1"){
    $collection_list = $All_coll_array["result"];
    $collection_list = array_values($collection_list);
}

if(isset($_REQUEST["upload"]))
{
		if( !empty($_FILES['bundle_image']['name']) && !empty($shop) && !empty($_POST["bTitle"]) && !empty($_POST["bTitleInternal"]))
		{
			$copyPath = "upload_images/".$shop;
			if(!file_exists($copyPath)) {
				mkdir($copyPath,0777,true);
			}
			$imageSize = getimagesize($_FILES['bundle_image']['tmp_name']);
			$imageWidth = $imageSize[0];
			$imageHeight = $imageSize[1];
			
			if($imageWidth >= 500 && $imageHeight >= 500) {
				if(($imageWidth == $imageHeight) || ($imageWidth == 1200 && $imageHeight == 628)) {
					$CurrentImg_Q="select * from bundle_images where shop='".addslashes($shop)."' and b_image='".addslashes($_FILES['bundle_image']['name'])."' limit 1";
					$CurrentImg_R=mysql_query($CurrentImg_Q);
					
					if(mysql_num_rows($CurrentImg_R) == 0)
					{
						$current_date = date("Y-m-d H:i:s");
						mysql_query("insert into bundle_images (shop,b_image,b_title,b_title_internal,created_date,updated_time) values ('".addslashes($shop)."','".addslashes($_FILES['bundle_image']['name'])."','".addslashes($_POST["bTitle"])."','".addslashes($_POST["bTitleInternal"])."', '$current_date','$current_date')");
						$CurrentImg_Q="select * from bundle_images where shop='".addslashes($shop)."' and b_image='".addslashes($_FILES['bundle_image']['name'])."' limit 1";
						$CurrentImg_R=mysql_query($CurrentImg_Q);
					} else {
						$CurrentImg_R=mysql_query($CurrentImg_Q);
					}
					$imageId = mysql_fetch_array($CurrentImg_R);
					$imagePath=$copyPath."/".$_FILES['bundle_image']['name'];
					copy($_FILES['bundle_image']['tmp_name'], $imagePath ) or die( "Could not copy file!");
					header("location: edit_image.php?id=".base64_encode($imageId["bid"]). "&shop=". $shop);
				} else {
					header("location:".$_SERVER["PHP_SELF"]."?err=Please upload square image or 1200x628". "&shop=". $shop);
				}
			} else {
				header("location:".$_SERVER["PHP_SELF"]."?err=Please upload square image having width and height more then 500". "&shop=". $shop);
			}
		} else {
			header("location:".$_SERVER["PHP_SELF"]."?err=Sorry please fill all fields correctly". "&shop=". $shop);
		}
}

?>    
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<?php include 'header.php'; ?>
<script type="text/javascript">
    ShopifyApp.init({
        apiKey: '<?= SHOPIFY_API_KEY ?>',
        shopOrigin: 'https://<?= $shop ?>'
    });
</script>
<title><?php echo APP_NAME; ?></title>
</head>
<body>
<div class="section">
    <div class="section-content">   
        <?php if ($install_status == '0') { ?>
            <div class="section-row install_status">
                <div class="section-cell" style="box-shadow: none;text-align: center;">
                    <label>Please wait... the app is completing its setup.</label>
                </div>
            </div>
        <?php } ?>
        <div class="section-row">
            <div class="section-listing">
                <div class="section-options">
                    <?php include 'menu.php'; ?>
                    <div class="section-content tab-content" >
                        <div class="alert alert-success" style="display: none;"><b>Success!</b> Your Bundle Image has saved successfully.</div>
                        <div class="alert alert-danger" style="display: <?php echo (isset($_REQUEST["err"]) ? 'block' : "none"); ?>;"><?php echo (isset($_REQUEST["err"]) ? $_REQUEST["err"] : ""); ?></div>
                        <div id="tab_1">
						<form id="os-form" class="contact-form" name="os-form" method="post" enctype="multipart/form-data">
                            <div class="section-row" style="width:420px">
                                <div class="section-cell"  style="box-shadow: none;">
                                    <div class="cell-container" style="border-bottom: 1px solid #ebeef0;">
                                        <div class="cell-column">
                                            <a class="btn primary" href="bundle_images.php?shop=<?= $shop; ?>"><< Back to list</a><br /><br />
                                            <label style="font-size:17px;">Add New Bundle Image</label>
                                        </div>
                                    </div>

                                    <div class="cell-container">
                                        <div class="cell-column text_right row_head">
                                            <label>Image Title * :</label>
                                        </div>
                                        <div class="cell-column row_field">
                                            <input type="text" class="demo input_field" name="bTitle" id="bTitle" placeholder="Image Title" value="" /> 
                                        </div>
                                    </div>
									
									<div class="cell-container">
                                        <div class="cell-column text_right row_head">
                                            <label>Internal name (will not be visible to users) * :</label>
                                        </div>
                                        <div class="cell-column row_field">
                                            <input type="text" class="demo input_field" name="bTitleInternal" id="bTitleInternal" placeholder="Image Internal Title" value="" /> 
                                        </div>
                                    </div>
									
									<div class="cell-container">
                                        <div class="cell-column text_right row_head">
											<label>Upload File *</label>
                                        </div>
                                        <div class="cell-column row_field">
                                            <input type="file"  data-filename-placement="inside" class="input-block-level" placeholder="Bundle Image" name="bundle_image">
                                        </div>
                                    </div>
                
                                    <div class="cell-container">
                                        <div class="cell-column text_right row_head">
                                            <label></label>
                                        </div>
                                        <div class="cell-column row_field">
                                            <br />
                                            <input class="btn primary btnsave" name="upload" value="Save" type="submit" />
                                        </div>
                                    </div>
                                </div>
                            </div>
						</form>
                        </div>
                    </div>                    
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>               
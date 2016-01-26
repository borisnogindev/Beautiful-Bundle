<?php
header("Access-Control-Allow-Origin: *");
include("../config.php");
include("../shopify_api.php");

/*$sql_added_in_cart = "SELECT bt.bid, bt.cart_id, bt.pid, COUNT( bt.add_to_cart ) AS tot_added, bm.b_title ".
			"FROM banner_transacrion AS bt LEFT JOIN banner_mst AS bm ON bt.bid = bm.bid ".
			"where bt.add_to_cart=1 and bt.shop='".$shop."' ".
			"Group by bt.bid, bt.cart_id ORDER BY tot_added DESC, bt.bid ASC, bt.cart_id ASC";

$sql_purchased = "SELECT bt.bid, bt.cart_id, bt.pid, COUNT( bt.is_purchased ) AS tot_purchased, bm.b_title ".
			"FROM banner_transacrion AS bt LEFT JOIN banner_mst AS bm ON bt.bid = bm.bid ".
			"where bt.is_purchased=1 and bt.shop='".$shop."' ".
			"Group by bt.bid, bt.cart_id ORDER BY tot_purchased DESC, bt.bid ASC, bt.cart_id ASC";
*/

$date_filter_condition = "";
if (!empty($_GET["start"]) && !empty($_GET["end"])){
	$date_filter_condition = " and created_date between '".date("Y-n-d", strtotime($_GET["start"]))." 00:00:00 ' and '".date("Y-n-d", strtotime($_GET["end"]))." 23:59:59' ";
}
$sql_added_in_cart = "SELECT COUNT( bt.add_to_cart ) AS total_item FROM banner_transacrion AS bt where bt.add_to_cart=1 and bt.shop='".$shop."' $date_filter_condition";
$res_added_in_cart = mysql_query($sql_added_in_cart);
$data_added_in_cart = mysql_fetch_assoc($res_added_in_cart);
$total_added_in_cart = $data_added_in_cart["total_item"];

$sql_purchased = "SELECT COUNT( bt.add_to_cart ) AS total_item FROM banner_transacrion AS bt where bt.is_purchased=1 and bt.shop='".$shop."' $date_filter_condition";
$res_purchased = mysql_query($sql_purchased);
$data_purchased = mysql_fetch_assoc($res_purchased);
$total_purchased = $data_purchased["total_item"];

$sql_prod_max_transaction = "SELECT COUNT( bt.add_to_cart ) AS total_item FROM banner_transacrion AS bt where bt.is_purchased=1 and bt.shop='".$shop."' $date_filter_condition";
$red_prod_max_transaction = mysql_query($sql_prod_max_transaction);
$data_prod_max_transaction = mysql_fetch_assoc($red_prod_max_transaction);
$total_prod_max_transaction = $data_prod_max_transaction["total_item"];

?>    
<html>
<head>
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
						<div style="display:inline-block;width:100%">
							<div class="section-row">   
								<div class="section-cell"  style="box-shadow: none;">
									<div class="cell-container" style="border-bottom: 1px solid #ebeef0;">
										<div class="cell-column">
											<label class="title_heading">Dashboard</label> 
										</div>
									</div>
								</div>
							</div>
							<div class="section-row">   
								<div class="section-cell"  style="box-shadow: none;">
									<div class="cell-container" style="border-bottom: 1px solid #ebeef0;">
										<div class="cell-column">
			<form method="get" id="stats_filter">				
 <input id="e2" name="e2">
 <input type="hidden" id="start" name="start">
 <input type="hidden" id="end" name="end">
 
 <?php if (!empty($_GET["start"]) && !empty($_GET["end"])){ ?>
	Date From: <?php echo $_GET["start"]; ?> To: <?php echo $_GET["end"]; ?>	
<?php } ?>
 </form>
 	</div>
									</div>
								</div>
							</div>
 							<div class="col-4">
								<div class="panel panel-primary">
									<div class="panel-heading">
										<div class="col-xs-3">
											<img src="images/cart-icon.png" />
										</div>
										<div class="col-xs-9 text_right">
											<div class="huge"><?=$total_added_in_cart?></div>
											<div>Add to cart</div>
										</div>
										<hr class="hr">
									</div>
									<a href="javascript:void(0)">
										<div class="panel-footer open_result_box" target_div="list_addtocart">
											<span style="font-size:15px;float:left;">View Details</span>
											<span style="float:right"> <img src="images/blue-arrow.png" /></span>
											<div class="clearfix"></div>										
										</div>
									</a>
								</div>
							</div>

							<div class="col-4">
								<div class="panel panel-cust">
									<div class="panel-heading">
										<div class="col-xs-3">
											<img src="images/user-icon.png" />
										</div>
										<div class="col-xs-9 text_right">
											<div class="huge"><?=$total_purchased?></div>
											<div>Purchased</div>
										</div>
										<hr class="hr">
									</div>
									<a href="javascript:void(0)">
										<div class="panel-footer open_result_box" target_div="list_purchased">										
											<span style="font-size:15px;float:left;">View Details</span>
											<span style="float:right;"><img src="images/black-arrow.png" /></span>
											<div class="clearfix"></div>
										</div>
									</a>
								</div>
							</div>

							<div class="col-4">
								<div class="panel panel-green">
									<div class="panel-heading">
										<div class="col-xs-3">
											<img src="images/tags-icon.png" />
										</div>
										<div class="col-xs-9 text_right">
											<div class="huge"><?=$total_prod_max_transaction?></div>
											<div>Top Product Transaction</div>
										</div>
										<hr class="hr">
									</div>
									<a href="javascript:void(0)">
										<div class="panel-footer open_result_box" target_div="list_maxprotransaction">										
											<span style="font-size:15px;float:left">View Details</span>
											<span style="float:right;"><img src="images/green-arrow.png"></span>
											<div class="clearfix"></div>										
										</div>
									</a>
								</div>
							</div>
						</div>
						<hr class="hr_bottom">
						<div class="result_box_main">
							<div class="option home-border text-capitalize result_data" style="display:none;">
								<?php
									$sql_added_in_cart = "SELECT bt.bid, bt.cart_id, bt.pid, COUNT( bt.add_to_cart ) AS tot_added, bm.b_title ".
												"FROM banner_transacrion AS bt LEFT JOIN banner_mst AS bm ON bt.bid = bm.bid ".
												"where bt.add_to_cart=1 and bt.shop='".$shop."' $date_filter_condition ".
												"Group by bt.bid, bt.cart_id ORDER BY tot_added DESC, bt.bid ASC, bt.cart_id ASC";
									$res_added_in_cart = mysql_query($sql_added_in_cart);
									if(count(mysql_num_rows($res_added_in_cart)) > 0){
								?>
								<div class="list_addtocart result_list" style="display:none;">
									<label class="title_heading">Product Added in Cart</label>
									<table>
										<tr>
											<th>Banner Name</th>
											<th>Product</th>
											<th>Cart Token</th>
											<th>Total Count</th>
										</tr>
										<?php 
											while($row_addtocart = mysql_fetch_assoc($res_added_in_cart)){ 
												$p_name = " - ";
												$p_id = $row_addtocart["pid"];
												$bid = base64_encode($row_addtocart["bid"]);
												$cart_id = $row_addtocart["cart_id"];
												$product_data = array();
												try{
													$product_data = $sc->call('GET', "/admin/products/".$p_id.".json");
												} catch (exception $e) {

												}
												if(count($product_data) > 0){
													$p_name = $product_data["title"];
												}
										?>										
										<tr>
											<td>												
												<a href="#preview" id="<?=$bid?>" class="preview_banner"><?=$row_addtocart["b_title"]?></a>
											</td>
											<td><?=$p_name?></td>
											<td><?=$cart_id?></td>
											<td><?=$row_addtocart["tot_added"]?></td>
										</tr>
										<?php } ?>
									</table>
								</div>
								<?php 
									}

									$sql_purchased = "SELECT bt.bid, bt.cart_id, bt.pid, COUNT( bt.is_purchased ) AS tot_purchased, bm.b_title ".
												"FROM banner_transacrion AS bt LEFT JOIN banner_mst AS bm ON bt.bid = bm.bid ".
												"where bt.is_purchased=1 and bt.shop='".$shop."' $date_filter_condition ".
												"Group by bt.bid, bt.cart_id ORDER BY tot_purchased DESC, bt.bid ASC, bt.cart_id ASC";
									$res_purchased = mysql_query($sql_purchased);
									if(count(mysql_num_rows($res_purchased)) > 0){
								?>
								<div class="list_purchased result_list" style="display:none;">
									<label class="title_heading">Product Purchased</label>
									<table>
										<tr>
											<th>Banner Name</th>
											<th>Product</th>
											<th>Cart Token</th>
											<th>Total Count</th>
										</tr>
										<?php 
											while($row_purchased = mysql_fetch_assoc($res_purchased)){ 
												$p_name = " - ";
												$p_id = $row_purchased["pid"];
												$bid = base64_encode($row_purchased["bid"]);
												$cart_id = $row_purchased["cart_id"];
												$product_data = array();
												try{
													$product_data = $sc->call('GET', "/admin/products/".$p_id.".json");
												} catch (exception $e) {

												}
												if(count($product_data) > 0){
													$p_name = $product_data["title"];
												}
										?>										
										<tr>
											<td>
												<a href="#preview" id="<?=$bid?>" class="preview_banner"><?=$row_purchased["b_title"]?></a>
											</td>
											<td><?=$p_name?></td>
											<td><?=$cart_id?></td>
											<td><?=$row_purchased["tot_purchased"]?></td>
										</tr>
										<?php } ?>
									</table>
								</div>								
								<?php 
									}

									$sql_max_pro_tran = "SELECT bt.bid, bt.cart_id, bt.pid, COUNT( bt.is_purchased ) AS tot_purchased, bm.b_title ".
												"FROM banner_transacrion AS bt LEFT JOIN banner_mst AS bm ON bt.bid = bm.bid ".
												"where bt.is_purchased=1 and bt.shop='".$shop."' $date_filter_condition ".
												"Group by bt.bid, bt.cart_id ORDER BY tot_purchased DESC, bt.bid ASC, bt.cart_id ASC";
									$res_max_pro_tran = mysql_query($sql_max_pro_tran);
									if(count(mysql_num_rows($res_max_pro_tran)) > 0){
								?>
								<div class="list_maxprotransaction result_list" style="display:none;">
									<label class="title_heading">Top Product Transaction</label>
									<table>
										<tr>
											<th>Banner Name</th>
											<th>Product</th>
											<th>Cart Token</th>
											<th>Total Count</th>
										</tr>
										<?php 
											while($row_max_pro_tran = mysql_fetch_assoc($res_max_pro_tran)){ 
												$p_name = " - ";
												$p_id = $row_max_pro_tran["pid"];
												$bid = base64_encode($row_max_pro_tran["bid"]);
												$cart_id = $row_max_pro_tran["cart_id"];
												$product_data = array();
												try{
													$product_data = $sc->call('GET', "/admin/products/".$p_id.".json");
												} catch (exception $e) {

												}
												if(count($product_data) > 0){
													$p_name = $product_data["title"];
												}
										?>
										<tr>
											<td>
												<a href="#preview" id="<?=$bid?>" class="preview_banner"><?=$row_max_pro_tran["b_title"]?></a>
											</td>
											<td><?=$p_name?></td>
											<td><?=$cart_id?></td>
											<td><?=$row_max_pro_tran["tot_purchased"]?></td>
										</tr>
										<?php } ?>
									</table>
								</div>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
    </div>
</div>

<div id="preview" style="display:none">
	<div id="preview_popup" style="height:250px;">
		<img class="loader_popup" src="assets/css/ajax-loader.gif">
	</div>
	<a class="fancy-close" style="text-decoration:none;cursor: pointer;;">Close</a>
</div>
<div id="loader" style="display:none"></div> 
<link rel="stylesheet" href="assets/css/jquery.minicolors.css">
<script src="assets/js/jquery.fancybox.js"></script>
<script src="assets/js/jquery.fancybox.pack.js"></script>
<style>
	.title_heading{ font-size: 17px; }
	.result_list .title_heading {
		margin-left: 8px;
		font-weight: normal;
		border-bottom: 1px solid #bbb;
		padding-bottom: 11px;
	}
	.col-xs-3{
		width:25%;
		float: left;
	}
	.col-xs-9 div{
		width: 75%;
		float: right;
		font-size: 16px;
	}
	.hr{
		margin-top: 75px;
		border-style: solid none none;
		color: #fff;
		width: 100%
	}
	.hr_bottom{
		margin: 20px 0;
		border-style: solid none none;
		color: #cccccc;
	}
	.option{
		padding: 10px;
		border: 1px solid #cccccc;
	}
	.col-4{
		width:30.3333%;
		float:left;
		padding-left:15px;
		padding-right:15px;
	}
	.panel{
		margin-bottom: 12px;
		border-radius:4px;
		border:1px solid transparent;
		height: auto;
	}
	.panel a{text-decoration: none;cursor: pointer; }
	.panel.panel-cust a:hover,.panel.panel-cust a:focus{color:#4C4C4C;}
	.panel a:hover,.panel a:focus{color:#23527c;}
	.panel.panel-yellow a:hover,.panel.panel-yellow a:focus{color:#da8f13;}
	.panel-heading{
		padding: 10px 15px;
		height: auto;
		border-bottom:1px solid transparent;
	}
	.panel-footer{
		padding: 10px 15px;
		border-top: 1px solid #ddd;
		height: 23px;
	}
	.panel-primary .panel-heading{
		background-color: #337ab7;
		color: #fff;
	}
	.panel-cust .panel-heading{
		background-color: #222;
		color: #fff;
	}
	.panel-yellow .panel-heading{
		background-color: #f0ad4e;
		color: #fff;
	}
	.panel-primary{border-color: #337ab7;}
	.panel-cust{border-color: #222;}
	.panel-yellow{border-color: #f0ad4e;}
	.fa {
		display: inline-block;
		font-family: FontAwesome;
		font-feature-settings: normal;
		font-kerning: auto;
		font-language-override: normal;
		font-size: inherit;
		font-size-adjust: none;
		font-stretch: normal;
		font-style: normal;
		font-synthesis: weight style;
		font-variant: normal;
		font-weight: normal;
		line-height: 1;
		text-rendering: auto;
	}
	.huge {
	  	font-size: 35px !important;
	  	margin: 10px 0;
	}
	.panel-green {
  		border-color: #5cb85c;
	}
	.panel-green .panel-heading {
		border-color: #5cb85c;
		color: #fff;
		background-color: #5cb85c;
	}	
	.panel-primary a{ color: #337ab7; }
	.panel-primary a:hover { color: #23527c; }
	.panel-cust a{ color: #4C4C4C; }
	.panel-cust a:hover { color: #222; }
	.panel-green a{ color: #5cb85c; }
	.panel-green  a:hover { color: #3d8b3d; }
	.lis_addtocart table{ width: 100%; }
	.result_box_main{ display:inline-block; width:100%; }
	.col-4 .active_box{ box-shadow: 3px 3px 3px 3px #000; }
	
	.banner_list{
	    	width:100%;border-top: 1px solid #ebeef0;clear: both;
	}
	.alert-error {
		background: #FFCDC9;
		color: red;
	}
	#cust_data_wrapper{width:100%;}
	#preview_popup h1{font-size: 25px;color:#479ccf;}
	#preview .loader_popup{ padding:125px 275px;}
	ul.pr_images{padding:0;margin:0;list-style-type:none;display:inline-block;}
	ul.pr_images li.up_pr_img{float:left;width:140px;height: auto;padding: 14px 7px;}
	ul.pr_images li.up_pr_img img{max-height: 100%; max-width:135px;}
	#cust_data tr td:nth-child(1){padding-left:19px;}
	#cust_data tr td:nth-child(2){padding-left:30px;}
	#cust_data tr td:nth-child(3){padding-left:20px;}
	#cust_data tr td:nth-child(4){padding-left:21px;}
	#cust_data tr td:nth-child(5){padding-left:28px;}
</style>
<script src="assets/js/jquery.minicolors.min.js"></script>
<script>
    	$(document).ready(function () {
		ShopifyApp.Bar.loadingOff();
		var shop = "<?php echo $shop; ?>";
		// for success message ShopifyApp.flashNotice("Settings have been saved successfully!");
		var ranger = $("#e2").daterangepicker({
         datepickerOptions : {
             numberOfMonths : 2
        }});
    	});
		$(document).on('change', '#e2', function () {
			$("#start").val();
			$("#end").val();
			var date_value=$.trim($(this).val());
			if (date_value != "") {
				date_selected = JSON.parse(date_value);
				$("#start").val(date_selected.start);
				$("#end").val(date_selected.end);
			}
			$("#stats_filter").submit();
		});
		

    	$(document).on('click', '.open_result_box', function () {
		var curr_btn_obj = $(this);
		var target_div = "."+$(curr_btn_obj).attr("target_div");
		$(".col-4 .panel").removeClass("active_box");
		$(".result_data").show();
		$(".result_list").hide();
		$(target_div).show();
		$(curr_btn_obj).parents(".col-4 .panel").addClass("active_box");
	});

    	$(document).on('click', '.preview_banner', function () {
		var curr_obj = $(this);
		var id_str = $.trim($(curr_obj).attr("id"));
		var hr_str = $.trim($(curr_obj).attr("href"));

        	$(".loader_popup").show();
        	$.ajax({
       		type: "GET",
            	url: "ajax.php",
            	data: {
               	type: "get_preview", 
                	shop: "<?= $shop ?>", 
                	id: id_str
            	},
            	success: function (data) {
                	$("#preview_popup").html(data);
                	$(".loader_popup").hide();
            	}
        	});
        
        	$.fancybox({
            	'autoSize':false,
            	'closeBtn': false,
            	'width':625,
            	'height':290,
            	'href': hr_str
        	});        
    	});

    	$(document).on('click', '.fancy-close', function () { 
        	$("#preview_popup").html("<img class='loader_popup' src='assets/css/ajax-loader.gif'>");
        	$.fancybox.close();
    	});
    	
    	$(function () {
        	var colpick = $('.demo').each(function () {
            	$(this).minicolors({
				control: $(this).attr('data-control') || 'hue',
				inline: $(this).attr('data-inline') === 'true',
				letterCase: 'lowercase',
				opacity: false,
                	change: function (hex, opacity) {
	                    if (!hex)
	                        return;
	                    if (opacity)
	                        hex += ', ' + opacity;
	                    try {
	                        //console.log(hex);
	                    } catch (e) {
	                    }
	                    $(this).select();
           		},
           		theme: 'bootstrap'
       		});
   		});
    	});
		

</script>
</body>
</html>               
<?php
header('Access-Control-Allow-Origin: *');
include("../config.php");
include("../shopify_api.php");
$p_id = array();
$c_id = array();

$order = $sc->call('GET', "/admin/orders/" . $_REQUEST['o_id'] . ".json");
$shop_data = $sc->call('GET', "/admin/shop.json");
$money = split("{{amount}}", $shop_data['money_format']);

$domain = $shop_data['domain'];

for ($i = 0; $i < count($order['line_items']); $i++) {
array_push($p_id, $order['line_items'][$i]['product_id']);   /* product id in order*/
}
for ($i = 0; $i < count($p_id); $i++) {
$collection = $sc->call('GET', "/admin/custom_collections.json?product_id=" . $p_id[$i]);
array_push($c_id, $collection[0]['id']); /* collection id of the product in order*/
}
$c_id = array_unique($c_id);

$setting_query = mysql_query("select * from general_settings where shop = '" . $shop . "'");
$setting_data = mysql_fetch_assoc($setting_query);

if($setting_data['display_related_product'] == 1){
?>
<div class="ty_related_pr">
<div class="ty_heading"><?php echo $setting_data['heading_text']; ?></div>
<ul class="ty_product">
<?php
$count=0;

for ($i = 0; $i < count($c_id); $i++) {
      $col = $sc->call('GET', "/admin/products.json?collection_id=" . $c_id[$i]); /* related product */
      foreach ($col as $product) {
          if (!in_array($product['id'], $p_id)) {
if($count < $setting_data['product_number'] ){
              ?>
             <li>
              <div>
              <div class="ty_image">
                  <a href="<?php echo "//".$domain."/products/".$product['handle']; ?>">
                  <?php if($product['image']['src'] == '') { ?>
                     <img src="//<?=DOMAIN_NAME?>/no-image.gif">
                 <?php } else{
                    ?>
                  <img src="<?php echo $product['image']['src']; ?>">
                  <?php } ?>
                  </a>
                  </div>
                 
                  <p><?php
                  if(strlen($product['title']) > 13){
                  echo substr($product['title'], 0, 13) . '..';
                   }else{
                   echo $product['title'];
                    } ?></p>
                  <p class="ty_price"><?php echo $money[0] . " " . $product['variants'][0]['price'] . " " . $money[1] ?></p>
                  <?php if($setting_data['display_button'] == 1){?>
                  <a target="_blank" href="<?php echo "//".$domain."/products/".$product['handle']; ?>"class="ty_btn"><?php echo $setting_data['button_text']; ?></a>
               <?php   } ?>
              </div>
             </li>
          <?php $count++;
}

}
      }
} ?>
</ul>
</div>
<?php } 
?>
<script>
  $(document).ready(function(){
    $(".ty_main_loader").hide();
  });
</script>
<style >
.ty_related_pr .ty_image{display: table;width: 100%;}
ul.ty_product li .ty_image a{display: table-cell;height: 104px;vertical-align: middle;}
.ty_btn{padding:6px;color:<?php echo $setting_data['button_text_color']; ?>;background-color: <?php echo $setting_data['button_color']; ?>;display:inline-block;margin-top: 5px;} 
<?php echo $setting_data['cust_css'];  ?>
ul.ty_product{padding:0;margin:0;list-style-type:none;display:inline-block;}
ul.ty_product li{float:left;width:120px;text-align: center;padding-right: 15px;}
ul.ty_product li img{max-height: 100%;}
ul.ty_product li p{margin:6px 0px;}
ul.ty_product:after, ul.ty_product:before{content: "";display: table;}
.ty_related_pr .ty_heading{padding:9px 0;}
.ty_related_pr .ty_heading,.ty_related_pr .ty_heading h1,.ty_related_pr .ty_heading h2,.ty_related_pr .ty_heading h3,.ty_related_pr .ty_heading h4,.ty_related_pr .ty_heading h5,.ty_related_pr .ty_heading h6,.ty_related_pr .ty_heading p,.ty_related_pr .ty_heading span{color:<?php echo $setting_data['heading_color'] ?>;font-style:<?php echo $setting_data['heading_style'] ?>}
.ty_related_pr p{color:<?php echo $setting_data['product_title_color'] ?>;font-style:<?php echo $setting_data['product_title_style'] ?>}
.ty_related_pr .ty_price{color:<?php echo $setting_data['product_price_color'] ?>;font-style:<?php echo $setting_data['product_price_style'] ?>}
</style>
<?php
header('Access-Control-Allow-Origin: *');
include("../config.php");
include("../shopify_api.php");

$current_date = date("Y-m-d H:i:s");

$type = "";
if(isset($_REQUEST['type']) && $_REQUEST['type'] != "") {
    $type = $_REQUEST["type"];
    $type = trim($type);
}

if($type == 'b_assign_bundle_to_product') {
    
    $b_id = $_REQUEST['id'];
    $b_target_pro =$_REQUEST['b_target_pro'];
    $b_target_pro_handle = $_REQUEST['b_target_pro_h'];
	
	$array_target_pro = explode(",",$b_target_pro);

	/* Add Metafield for Upsell List */        
	
	foreach($array_target_pro as $product_id) {
		$api_call_str = "";        
		$MetaData = array("metafield" => array("namespace" => "p_bundle_target", "key" => "p_bundle_image", "value" => $b_id, "value_type" => "string"));
		$api_call_str = "/admin/products/".$product_id."/metafields.json";
		
		try{
			$add_meta = $sc->call('POST', $api_call_str, $MetaData);
		} catch (exception $e) {

		}       

	}	

	echo "success";
}

if($type == 'b_update') {
    $bTitle= $_REQUEST['bTitle'];
	$bTitleInternal= $_REQUEST['bTitleInternal'];
    $b_target_pro =$_REQUEST['b_target_pro'];
    $b_target_pro_handle = $_REQUEST['b_target_pro_h'];
	$b_cordinates = $_REQUEST['b_cordinates'];
	
    $bid = base64_decode($_REQUEST['id']);
    
    $sql_update = "Update bundle_images set b_cordinates = '$b_cordinates', b_target_list='".$b_target_pro."', b_target_list_handle='".mysql_real_escape_string($b_target_pro_handle)."',updated_time='".$current_date."' where bid='".$bid."' and shop='".mysql_real_escape_string($shop)."'";
    $result_sql = mysql_query($sql_update);
    if (!$result_sql) {
        echo 'Invalid query: '.mysql_error();
    } else {
        $banner_id = $bid;
        echo "success";
    }
}

if($type == 'b_delete') {
    $bid = base64_decode($_REQUEST['id']);
    
    $sql_sel = "select * from bundle_images where bid='".$bid."' and shop='".mysql_real_escape_string($shop)."' limit 1";
    #echo $sql_sel;
    $sel_result_sql = mysql_query($sql_sel);
    if(mysql_num_rows($sel_result_sql) > 0){
		$sel_result_rs = mysql_fetch_array($sel_result_sql);
        $sql_delete = "delete from bundle_images where bid='".$bid."' and shop='".mysql_real_escape_string($shop)."'";
        #echo $sql_update;            
        $result_sql = mysql_query($sql_delete);
        if (!$result_sql) {
            echo 'Invalid query: '.mysql_error();
        } else {
			unlink("upload_images/$shop/". $sel_result_rs["b_image"]);
            echo "success";
        }        
    } else {
        echo 'Invalid query: '.mysql_error();
    }
}
if($type == 'get_bundle_list') {
    $table = 'bundle_images';    
    $primaryKey = 'bid';
    
    $columns = array(        
        array('db' => 'b_title', 'dt' => 'b_title'),
        array('db' => 'bid', 'dt' => 'bid')
    );

    $extraWhere = "shop='".mysql_real_escape_string($shop)."' ";
    require('ssp.class.php');

    $arr = SSP::complex($_POST, $sql_details, $table, $primaryKey, $columns, null, $extraWhere);
    $json = $arr;
    
    print_r(json_encode($json));
}

if($type == 'search_p') {
    $search_text= trim($_REQUEST['ss']);  
    $search_collection_id = trim($_REQUEST['sc']);  
    $search_type= trim($_REQUEST['st']);  
    $page = trim($_REQUEST['page']);
    $limit_str = 10;

    if($search_type == "all_coll"){
        $search_peram = "?limit=".$limit_str."&page=".$page;
        $search_products_data = $sc->call('GET', '/admin/products.json?collection_id='.$search_collection_id.$search_peram);  

        $total_prod_count = $sc->call('GET', '/admin/products.json?collection_id='.$search_collection_id); 
        $total_prod_count = intval($total_prod_count); 
    } else {
        if($search_text == ""){
            $search_peram = "?limit=".$limit_str."&page=".$page;
        } else {
            $search_peram = "?page=".$page;
        }
        if($search_text != ""){
            $search_peram .= "&title=".$search_text;
        }
        $search_products_data = $sc->call('GET', '/admin/products.json'.$search_peram);

        if($search_text != ""){
            $total_prod_count = count($search_products_data);
        } else {
            $total_prod_count = $sc->call('GET', '/admin/products/count.json');
        }
        $total_prod_count = intval($total_prod_count);
    }

    #echo "<pre>"; print_r($search_products_data);
    #echo $total_prod_count;

    if($total_prod_count > 0){        
        $p_str = "";
        for($i=0;$i<$limit_str;$i++){
            if($search_products_data[$i]["id"] != ""){
                $p_str_temp = '<div class="p_box" id="'.$search_products_data[$i]["id"].'"><span class="p_name">'.$search_products_data[$i]["title"].'</span><input type="button" class="btn primary p_add" value="Add" p_id="'.$search_products_data[$i]["id"].'" handle="'.$search_products_data[$i]["handle"].'" /></div>';
                $p_str .= $p_str_temp;
            }
        }

        $pagignation_str = "";        
        $total_page = $total_prod_count / $limit_str;
        if($total_page > 1){
            $pagignation_str = "Page: ";
            for($i=0;$i<$total_page;$i++){            
                $active_class="";
                if(($i+1) == $page){
                    $active_class=" active_page";
                }
                $pagignation_str .= "<a class='page_no".$active_class."' st='".$search_type."' sc='".$search_collection_id."' ss='".$search_text."'>".($i+1)."</a>";
            }
        }

        if($p_str != ""){
            echo $pagignation_str."|||".$p_str;
        } else {
            echo "no_products";
        }
    }
}
if ($type == 'get_preview') {
    $bid = base64_decode($_REQUEST['id']);

    $products_data = array();    
    $sql_sel = "select * from banner_mst where bid='".$bid."' limit 1";
    #echo $sql_sel;
    $result_sql = mysql_query($sql_sel);
    if (!$result_sql) {
        echo 'Invalid query: '.mysql_error();
    } else {
        $preview = "";
        $result_data= mysql_fetch_assoc($result_sql);
        $target_list_temp = $result_data['b_target_list']; 
        $preview .= "<div><h1>".$result_data['b_title']."</h1>";
        $preview .= "<ul class='pr_images'>";
        $target_data_temp = array();
        $target_data_temp = $sc->call('GET', "/admin/products.json?fields=image&ids=".$target_list_temp);

        if(count($target_data_temp) > 0){
            for($i =0;$i<count($target_data_temp);$i++){
                $image_str = $target_data_temp[$i]['image']['src'];
                if($image_str != ""){
                    $image_str = "<img src='".$image_str."' alt='' />";
                }                
                $preview .= "<li class='up_pr_img'>
                                <div class='table' style='width:100%;display: table;'>
                                    <div class='table-cell' style='width:100%;vertical-align: middle;;height:140px;display: table-cell;text-align:center;border: 1px solid #bbb;'>".$image_str."</div>
                                </div>
                            </li>";
            }
        } 
        $preview .="</ul></div>";
        echo $preview;
    }
}
?>
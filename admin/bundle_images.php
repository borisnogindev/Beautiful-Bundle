<?php
header("Access-Control-Allow-Origin: *");
include("../config.php");
include("../shopify_api.php");
include("shopify_function.php");
$collection_list = array();
$All_coll_array = getCollection($shop, $token);
if(count($All_coll_array["result"]) > 0 && $All_coll_array["status"] == "1"){
    $collection_list = $All_coll_array["result"];
    $collection_list = array_values($collection_list);
}


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
<script src="//cdn.datatables.net/1.10.3/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="//cdn.datatables.net/1.10.3/css/jquery.dataTables.css" />
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
                        <div class="alert alert-success" style="display: none;"><b>Success!</b> Your image has saved successfully.</div>       
                        <div class="alert alert-error" style="display: none;"></div>                 
                        <div class="banner_list section-row">
                            <div class="section-cell"  style="box-shadow: none;">
                                <div class="cell-container" style="border-bottom: 1px solid #ebeef0;">
                                    <div class="cell-column">
                                        <a class="btn primary" href="add_image.php?shop=<?= $shop; ?>">Add Image</a><br /><br />
                                        <label style="font-size:17px;">Bundle Image List</label>                                        
                                    </div>
                                </div>

                                <div class="cell-container">
                                    <table id="cust_data" class="display" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th width="1%" style="text-align:left;display:none;">Id</th>
                                                <th width="29%" style="text-align:left">Title</th>
                                                <th width="50%" style="text-align:left">Use Code (Copy/Paste Anywhere)</th>
                                                <th width="10%" style="text-align:left">Edit</th>
                                                <th width="10%" style="text-align:left">Delete</th>
                                            </tr>
                                        </thead>
                                    </table>
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
        </div>
    </div>
</div>
<div id="target_pro_view" style="display:none">    
    <div class="product_selection_box">
        <div class="product_selection_option">
            <h2>Search Products:</h2>
            <div class="select_opt_row">
                <input type="radio" class="rd_all_product rd_ps_opt" target_div="all_pro_list_box" name="rd_ps_opt1" value="all_pro" checked /> 
                <div class="op_head">All Products</div>
                <input type="text" class="txt_search_products" target_div="all_pro_list_box" placeholder="Search by product name" value="" />
            </div>
            <div class="select_opt_row">    
                <input type="radio" class="rd_all_collection rd_ps_opt" target_div="all_pro_list_box" name="rd_ps_opt1" value="all_coll">
                <div class="op_head">From Collection</div>
                <select class="store_col_list">
                    <option value="-1">Select</option>
                    <?php
                    if(count($collection_list) > 0){
                        for($i=0;$i<count($collection_list);$i++){
                    ?>
                        <option value="<?=$collection_list[$i]["id"]?>"><?=$collection_list[$i]["title"]?></option>
                    <?php
                        }
                    }
                    ?>
                </select>
            </div>          
            <input type="button" class="btn primary btnSearchProducts" value="Search Products" />
            <span class="error_search_opt"></span>
        </div>
        <div class="product_selection_data">
            <div class="pro_list_box">
                <div class="all_pro_list_box product_data_list"> </div>                           
            </div>
            <div class="pro_target_list"></div>
        </div>    
        <div class="footer_data">
            <div class="pagignation_data"> </div>   
            <div class="product_selection_buttonbox">
                <input style="display: none" type="button" class="btn primary btnContProducts" value="Continute with Selected Products"  data_for="b_target_products" max-limit="1" />
				<input style="display: none" type="hidden" id="hd_id" />
				
				
                <span class="error_cont_btn"></span>
            </div>
        </div>
    </div>
</div>

<div id="loader" style="display:none"></div> 
<link rel="stylesheet" href="assets/css/jquery.minicolors.css">
<script src="assets/js/jquery.fancybox.js"></script>
<script src="assets/js/jquery.fancybox.pack.js"></script>
<style>
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

.select_opt_row{ display: block; }
.rd_all_product{
    display: inline-block;
    float: none;
    vertical-align: top;
}
.product_selection_box{ width: 608px; }
.product_selection_option{ padding-bottom: 10px; border-bottom: 1px solid #ddd; }
.product_selection_data{ margin: 10px 0; }
.pro_list_box, .pro_target_list{ display: inline-block; vertical-align: top; padding: 5px 10px 5px 5px; }
.product_data_list .p_box, .pro_target_list .p_box { padding: 5px 0; display: block; block; clear: both; }
.pro_list_box { border: 1px solid #ddd; width: 285px; height: 300px; overflow-y: scroll; overflow-x: hidden; }
.pro_target_list{ border: 1px solid #ddd; width: 285px; height: 300px; }
.product_selection_buttonbox{ text-align: left; }
span.p_name { float: left; }
.p_box .p_add, .p_box .p_remove { float: right; }
.p_box .p_add.marked { background-color: green; color: #fff; border: 1px solid green; }
#b_target_products, #b_product_list{ background: #eee; }
.footer_data .pagignation_data, .footer_data .product_selection_buttonbox{ display: inline-block; vertical-align: top; padding: 5px 10px 5px 5px; width: 47%; }
.pagignation_data .page_no { margin-right: 5px; text-decoration: underline; cursor: pointer; }
.pagignation_data .page_no.active_page { text-decoration: none; cursor: text; }
.select_opt_row .rd_ps_opt, .select_opt_row .op_head, .txt_search_products, .store_col_list{ display: inline-block; vertical-align: top; }
.txt_search_products, .store_col_list{ width: 250px !important }
.op_head{ width: 95px; margin-top: 7px; }
.select_opt_row .rd_ps_opt{ margin-top: 7px !important; }
.product_selection_option h2{ font-size: 19px; }
span.error_search_opt, span.error_cont_btn  { color: red; margin-left: 5px; }


</style>
<script src="assets/js/jquery.minicolors.min.js"></script>
<script>
    $(document).ready(function () {
        ShopifyApp.Bar.loadingOff();
        var shop = "<?php echo $shop; ?>";

         oTable = load_data(shop);
         $("#loader").hide();
    });

    $(document).on('click', '.fancy-close', function () { 
        $("#preview_popup").html("<img class='loader_popup' src='assets/css/ajax-loader.gif'>");
        $.fancybox.close();
    });

    $(document).on('click', '.b_delete', function () {     
        $(".alert-error").hide();          
        $(".alert-error").html("");       
        var curr_obj = $(this);
        var id_str = $.trim($(curr_obj).attr("id"));

        $("#loader").show();
        $.ajax({
            type: "POST",
            url: "ajax.php",
            data: {
                type: "b_delete", 
                shop: "<?= $shop ?>", 
                id: id_str
            },
            success: function (data) {
                $("#loader").hide();
                if(data == "success"){          
                    $(curr_obj).parents("tr").hide();
                } else {
                    $(".alert-error").html(data); 
                    $(".alert-error").show();
                }
            }
        }); 
        return false;
    });

    function load_data(shop_str) {
        $("#loader").show();
        var table = $('#cust_data').DataTable({
            responsive: true,
            "processing": true,
            "serverSide": true,
            "order": [[1, "desc"]],
            "columns": [                
                {"data": "bid", "visible": false},
                {"data": "b_title"},
				{"data": "bid", "bSortable": false, "targets": 'no-sort', "render": function (data, type, row) {
                        var b_action_str = '<input type="text" class="code" readonly"=readonly" value="{% assign bundle_image=\''+base64_encode(data)+'\' %}{% include \'beautifull_bundle_image_ad\' %}"><br> Or<br> <a href="#" class="assign_to_product" data-image="'+base64_encode(data)+'" >Assign To Product</a>';
                        return b_action_str;
                    }
                },
                {"data": "bid", "bSortable": false, "targets": 'no-sort', "render": function (data, type, row) {
                        var bid_str_temp = base64_encode(data);                        
                        var b_action_str = '<a href="edit_image.php?id='+bid_str_temp+'&shop='+shop_str+'" class="b_edit"><img src="images/edit.png" alt="" style="max-width: 20px;" /></a>';
                        return b_action_str;
                    }
                },
                {"data": "bid", "bSortable": false, "targets": 'no-sort', "render": function (data, type, row) {
                        var bid_str_temp = base64_encode(data);
                        var b_action_str = '<a href="javascript:void(0)" id="'+bid_str_temp+'" class="b_delete"><img src="images/delete.png" alt="" style="max-width: 20px;" /></a>';
                        return b_action_str;
                    }
                }
            ],
            "ajax": {
                "url": "ajax.php?shop="+shop_str+"&type=get_bundle_list",
                "type": "POST"
            }
        });
        return table;
    }
	
	$(document).on('click', '.assign_to_product', function () {
		$("#hd_id").val($(this).data("image"));
		$.fancybox({
            'content' : $("#target_pro_view").html()
        });
	});

	$(document).on('click', '.rd_ps_opt', function () {
        var curr_btn_obj = $(this);     
        var main_div_obj = $(curr_btn_obj).parents(".product_selection_box");
        //alert($(main_div_obj).find('.rd_ps_opt:checked').val());
        
        var target_div = $(curr_btn_obj).attr("target_div");
        if(target_div == "all_coll_list_box"){
          $(main_div_obj).find(".btnSearchProducts").hide();
        } else {
          $(main_div_obj).find(".btnSearchProducts").show();
        }
        $(main_div_obj).find(".all_pro_list_box").html("");        
        $(main_div_obj).find(".product_data_list").hide();        
        $(main_div_obj).find(".p_add").removeClass("marked");
        $("."+target_div).show();
    });

    $(document).on('click', '.p_add', function () {     
        var curr_btn_obj = $(this);
        var main_div_obj = $(curr_btn_obj).parents(".product_selection_box");
        var limit_for_add = parseInt($(main_div_obj).find(".btnContProducts").attr("max-limit"));
        var current_added_box = $(main_div_obj).find(".pro_target_list .p_box").length;
		

        var object_id = $(curr_btn_obj).attr("p_id");
        var target_list_arr = Array();
        $(main_div_obj).find(".pro_target_list .p_box .p_remove").each( function(ii){
            var p_id_temp = $(this).attr("p_id");
            target_list_arr.push(p_id_temp);
        });
        //console.log(target_list_arr);
        //console.log($.inArray(object_id, target_list_arr));
        
        if($.inArray(object_id, target_list_arr) == "-1"){        
            var copy_html = $(curr_btn_obj).parent(".p_box").html();
            copy_html = copy_html.replace("p_add", "p_remove");
            copy_html = copy_html.replace("Add", "Remove");
            var new_html = "<div class='p_box' id='"+object_id+"'>"+copy_html+"</div>";
            //console.log(new_html);
            $(main_div_obj).find(".pro_target_list").append(new_html);
        }
        $(curr_btn_obj).addClass("marked");
		$(".btnContProducts").trigger('click');
		$.fancybox.close(); 
    });

    $(document).on('click', '.p_remove', function () {
        var curr_btn_obj = $(this);   
        var main_div_obj = $(curr_btn_obj).parents(".product_selection_box");     
        var object_id = $(curr_btn_obj).attr("p_id");
        $(main_div_obj).find(".product_data_list").find("#"+object_id).find(".p_add").removeClass("marked");
        $(curr_btn_obj).parent(".p_box").remove();
    });  

    $(document).on('click', '.btnContProducts', function () {        
        var curr_btn_obj = $(this);
        var main_div_obj = $(curr_btn_obj).parents(".product_selection_box");
        var current_added_box = $(main_div_obj).find(".pro_target_list .p_box").length;
        
        var data_for = $(curr_btn_obj).attr("data_for");
        var prefix = "";
        if(data_for == "b_product_list"){
            var selected_opt = $(main_div_obj).find('.rd_ps_opt:checked').val();
            if(selected_opt == "all_pro"){
                prefix = "p_";  
            } else {
                prefix = "c_";  
            }
        }

        var final_selected_pro_list = "";
        var final_selected_prohandle_list = "";
        var final_selected_proname_str = "";
        $(main_div_obj).find(".pro_target_list .p_box").each( function(ii){
            var p_id_temp = prefix + $(this).find(".p_remove").attr("p_id");
            var p_handle_temp = prefix + $(this).find(".p_remove").attr("handle");
            var p_name_temp = $(this).find(".p_name").text();
            if(final_selected_pro_list == ""){
                final_selected_pro_list = p_id_temp; 
                final_selected_prohandle_list = p_handle_temp;
            } else {
                final_selected_pro_list += ","+p_id_temp; 
                final_selected_prohandle_list += "|||"+p_handle_temp;
            }
            final_selected_proname_str += "<label>"+p_name_temp+"</label>";
        });

        if(data_for == "b_product_list"){
            $(".selected_pro_upsell_list").html(final_selected_proname_str);
        } else {
            $(".selected_pro_target_list").html(final_selected_proname_str);
        }

        $(main_div_obj).find(".pro_target_list").html("");
        $(main_div_obj).find(".p_add").removeClass("marked");
		
		$(".alert-error").hide();          
		$(".alert-error").html("");
		var bid_val = $.trim($("#hd_id").val());
		
		var b_target_products_val = $.trim(final_selected_pro_list);
		var b_product_list_handle_val = $.trim(final_selected_prohandle_list);
		
		$("#loader").show();

		$.ajax({
			type: "POST",
			url: "ajax.php",
			data: {
				type: "b_assign_bundle_to_product", 
				shop: "<?= $shop ?>", 
				id: bid_val,
				b_target_pro: b_target_products_val,
				b_target_pro_h: b_product_list_handle_val
			},
			success: function (data) {
				$("#loader").hide();
				if(data == "success"){
					
				} else {
					$(".alert-error").html(data);  
					$(".alert-error").show();
				}
			}
		});
		
    });

    $(document).on('click', '.btnSearchProducts', function () {        
        var curr_btn_obj = $(this);
        var main_div_obj = $(curr_btn_obj).parents(".product_selection_box");
        var selected_opt = $(main_div_obj).find('.rd_ps_opt:checked').val();
        var search_text = $(main_div_obj).find(".txt_search_products").val();
        search_text = $.trim(search_text);
        var selected_coll = $(".store_col_list").val();

        if(selected_opt == "all_coll" && selected_coll == "-1"){
            alert("Please select collection");
            return false;
        }

        $("#loader").show();
        $.ajax({
            type: "POST",
            url: "ajax.php",
            data: {
                type: "search_p", 
                shop: "<?= $shop ?>",
                ss: search_text,
                st: selected_opt,
                sc: selected_coll,
                page: 1
            },
            success: function (data) {
                $("#loader").hide();
                if(data == "no_products"){
                    $(main_div_obj).find(".all_pro_list_box").html("Sorry! No search found.");
                } else {
                    var data_arr = data.split("|||");
                    var list_str = data_arr[1];
                    $(main_div_obj).find(".pagignation_data").html(data_arr[0]);      
                    $(main_div_obj).find(".all_pro_list_box").html(list_str);                 
                }
            },
            error: function (error) {
                $("#loader").hide();
                console.log('error; ' + eval(error));
                alert("Process fail. Please try again.")
            }
        });
    });

    $(document).on('click', '.page_no', function () {        
        var curr_btn_obj = $(this);
        var main_div_obj = $(curr_btn_obj).parents(".product_selection_box");
        var curr_page_no = $(curr_btn_obj).text();
        var search_text = $(curr_btn_obj).attr("ss");
        search_text = $.trim(search_text);

        $("#loader").show();
        $.ajax({
            type: "POST",
            url: "ajax.php",
            data: {
                type: "search_p", 
                shop: "<?= $shop ?>",
                ss: search_text,
                page: curr_page_no
            },
            success: function (data) {
                $("#loader").hide();
                if(data == "no_products"){
                    $(main_div_obj).find(".all_pro_list_box").html("Sorry! No search found.");
                } else {
                    var data_arr = data.split("|||");
                    var list_str = data_arr[1];
                    $(main_div_obj).find(".pagignation_data").html(data_arr[0]);      
                    $(main_div_obj).find(".all_pro_list_box").html(list_str);               
                }
            },
            error: function (error) {
                $("#loader").hide();
                console.log('error; ' + eval(error));
                alert("Process fail. Please try again.")
            }
        });
    }); 
    $("#btnSelect_target_pro").fancybox();	
	
    function base64_encode(data) {
          var b64 = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=';
          var o1, o2, o3, h1, h2, h3, h4, bits, i = 0,
            ac = 0,
            enc = '',
            tmp_arr = [];

          if (!data) {
            return data;
          }

          data = unescape(encodeURIComponent(data));

          do {
            // pack three octets into four hexets
            o1 = data.charCodeAt(i++);
            o2 = data.charCodeAt(i++);
            o3 = data.charCodeAt(i++);

            bits = o1 << 16 | o2 << 8 | o3;

            h1 = bits >> 18 & 0x3f;
            h2 = bits >> 12 & 0x3f;
            h3 = bits >> 6 & 0x3f;
            h4 = bits & 0x3f;

            // use hexets to index into b64, and append result to encoded string
            tmp_arr[ac++] = b64.charAt(h1) + b64.charAt(h2) + b64.charAt(h3) + b64.charAt(h4);
          } while (i < data.length);

          enc = tmp_arr.join('');

          var r = data.length % 3;

          return (r ? enc.slice(0, r - 3) : enc) + '==='.slice(r || 3);
    }
</script>
</body>
</html>               
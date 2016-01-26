<?php
    
function getAllProuctData($shop_str, $Access_Token){
    $i = 1;
    $products = array();
    for($i=1;$i<2;$i++){
        $product_Url = 'https://' .SHOPIFY_API_KEY. ':' .SHOPIFY_SECRET. '@' .$shop_str. '/admin/products.json?limit=100&page='.$i;
        $header = array('Accept: application/json', 'Content-Type: application/json','X-Shopify-Access-Token: '.$Access_Token);                
        $response = http_Curl($product_Url, null, $header);
        if($response['status']){
            if(empty($response['result']['products'])){
                break;
            }
            else{
                $products = array_merge($products, $response['result']['products']);
            }
        }
        else{
          return array('status'=>0,'error'=>$response['error']);
        }
    }
    return array('status'=>1,'products'=> $products);
}

function getCollection($shop_str, $Access_Token){
    $collection_Url = 'https://' .SHOPIFY_API_KEY. ':' .SHOPIFY_SECRET. '@' .$shop_str. '/admin/custom_collections.json';
    $header = array('Accept: application/json', 'Content-Type: application/json','X-Shopify-Access-Token: '.$Access_Token);
    
    $response = http_Curl($collection_Url, NULL, $header);
  
    if($response['status']){
        $collections = (array)$response['result']['custom_collections'];
        $collection_ID = array();
        $product_collections = array();
        foreach($collections as $collection){
            array_push($collection_ID, $collection['id']);
            $product_collections[$collection['id']] = array('id'=>(string)$collection['id'],'title'=>(string)$collection['title'],'desc'=>(string)$collection['body_html']);
        }
        if(!empty($product_collections)){
            return array('status'=>1,'result'=>$product_collections,'IDs'=>$collection_ID);
        }
        else{
            return array('status'=>0,'result'=>"No Collection are there!");
        }
    }
    else{
        return array('status'=>0,'result'=>$response['error']);
    }
}

function http_Curl($url ,$post_field, $header){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    if($header){
        curl_setopt($ch, CURLOPT_HTTPHEADER , $header);
    }
        if(!empty($post_field)){  
            curl_setopt($ch, CURLOPT_POST, true);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_field);
    } 
    $y = curl_exec($ch);
    $info = curl_getinfo($ch);
    if($y !== false){
        curl_close($ch);
        if($info['http_code'] == 200 || $info['http_code'] == 201){
            return array('status'=>1, 'result' => json_decode($y, true));
        }
        else{
            return array('status'=>0, 'error' => "There is some error having error code ". $info['http_code']);
        }
    }
    else{
        $error = 'Error: "' .curl_error($ch).'"';
        curl_close($ch);
        return array('status'=>0, 'error'=> $error);
    }
}    

?>
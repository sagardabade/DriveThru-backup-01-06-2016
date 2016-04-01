<?php
 
   header('Content-Type: application/json; charset=utf-8');
   
   include("DB_config.php");
   connectDB();
   
   
   class Products {
    public $productID = "";
    public $product_name = "";
    public $product_image = "";
    public $merchant_id = "";
    public $product_price = "";
    public $product_created_at = "";
    public $product_updated_at = "";
    public $customization_availability = "";
    public $product_customization_details = "";
    public $product_category = "";
}

   
   $merchant_id = $_GET['merchant_id'];
   
   $qry = "SELECT p.id_dt_ot_product, p.product_name, p.product_image, p.merchant_id, p.product_price, p.product_created_at, p.product_updated_at,a.customization_availability 

		FROM DT_OT_PRODUCTS p,DT_OT_PRODUCT_CUSTOMIZATION_AVAILABILITY a
		WHERE
		p.id_dt_ot_product = a.id_dt_ot_product
		and 
		p.merchant_id = ".$merchant_id." ";
 
	//$json = array();
	$result = mysql_query($qry); 
	
	$json = array();
	$productsArray = array();
	$products = array();
	
	while($row = mysql_fetch_array($result,MYSQL_ASSOC)) 
	{
		$products = new Products();
		
   		$product_id = $row['id_dt_ot_product']; //product id to get customization details 
   		$products->productID = $row['id_dt_ot_product'];
   		$products->product_name = $row['product_name'];
   		$products->product_image = $row['product_image'];
   		$products->merchant_id = $row['merchant_id'];
   		$products->product_price = $row['product_price'];
   		$products->product_created_at = $row['product_created_at'];
   		$products->product_updated_at = $row['product_updated_at'];
   		
   		$products->customization_availability =filter_var($row['customization_availability'],FILTER_VALIDATE_BOOLEAN);
   		
   		array_push($productsArray, $products);
   		
   		if($row['customization_availability'] == "yes") //if a product has a customization
   		{
   		
   		$sql_catagories = select distinct cc.category_name from DT_RT_CUSTOMIZATION_CATEGORY cc, DT_OT_PRODUCT_CUSTOMIZATION pc,DT_OT_PRODUCTS p where 
   		p.id_dt_ot_product = ".$product_id." and
	       cc.id_customization_category = pc.category_code and
	       p.id_dt_ot_product = pc.id_dt_ot_product;
   		
   			$result_product_customization_category = mysql_query($sql_catagories); 
			
			while($row_product_customization_category = mysql_fetch_array($result_product_customization_category,MYSQL_ASSOC)) 
			{
			
			
			
				$sql_product_customization = "select 								cc.category_name,cc.category_image_url,cv.category_value_name,sa.store_alias_name,sa.id_customization_value_alias,sa.customization_price,sa.id_merchant
	
	from DT_OT_PRODUCT_CUSTOMIZATION pc,DT_OT_PRODUCTS p,DT_RT_CUSTOMIZATION_CATEGORY cc,DT_RT_CUTOMIZATION_CATEGORY_VALUES cv,DT_RT_STORE_CUSTOMIZATION_VALUE_ALIAS sa
	
	where 
	p.id_dt_ot_product = ".$product_id."
	and
	p.id_dt_ot_product = pc.id_dt_ot_product
	and
	cc.id_customization_category = pc.category_code
	and 
	cc.category_name = " . $row_product_customization['category_name'] . "
	and
	cv.id_customization_category = cc.id_customization_category
	and
	cv.id_customization_category_value = sa.id_customization_category_value";
	
			$result_product_customization = mysql_query($sql_product_customization); 
			
			while($row_product_customization = mysql_fetch_array($result_product_customization,MYSQL_ASSOC)) 
			{
			
				/* creating temp array of product customization details */
				$product_custom_details_ARY = array(				
				"category_name" => $row_product_customization['category_name'],
				//'category_image_url' => $row_product_customization['category_image_url'],
				'category_value_name' => $row_product_customization['category_value_name'], 
				'store_alias_name' => $row_product_customization['store_alias_name'],
				'id_customization_value_alias' => $row_product_customization['id_customization_value_alias'], 
				'customization_price' => $row_product_customization['customization_price'],
				'id_merchant' => $row_product_customization['id_merchant']);
				
				array_push($json, $product_custom_details_ARY);
			
			}//end $result_product_customization  while
			
		//$groups = _group_by($json,'category_name', $json_scalar, 'category_name') ;
		
			$product_customization_details = "Category";
   		}// end if
   		/* adding product customization details to object array */
			
		$products->product_customization_details = $json; 	
			
			
			
			
	   	
   	}	
   		
	} //end while



deliver_response($products); 
mysql_close();

function _group_by($array, $key,$array_scalar, $grouped_key) {
    $return = array();
    $return_scalar = array();
    foreach($array_scalar as $sval){
    $return [$sval[$key]] = $sval ;
    }
    foreach($array as $val) {
    	$return[[$key][$val[$key]]][] = $val;
   	}
     //return  str_replace([$val[$key]],[$key],$return);
    return $return; 
   
}
	 
	 
function deliver_response($arr)
{
$response['products']=$arr;
$json_response=json_encode($response, JSON_PRETTY_PRINT|JSON_NUMERIC_CHECK);
echo $json_response;
} 
?>
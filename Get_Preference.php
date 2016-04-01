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
	public $preference =FALSE;
    public $product_customization_details = "";
    
}

class Category
{
	public $category_name ="";
	public $category_value_name ="";
	public $id_merchant ="";

}

class subCategory
{
	public $category_value_name="";
	public $store_alias_name = "";
	public $id_customization_value_alias ="";
	public $customization_price ="";
	public $customization_category_name =""; 
	public $category_ID ="";
	public $preference_alias_id = "";
	public $selected = FALSE;

}
   
   $merchant_id = $_REQUEST['merchant_id'];
   $consumer_id = $_REQUEST['consumer_id'];
   
   //query to
   $qry = "SELECT p.id_dt_ot_product, p.product_name, p.product_image, p.merchant_id, p.product_price, p.product_created_at, p.product_updated_at,a.customization_availability 

		   FROM DT_OT_PRODUCTS p,DT_OT_PRODUCT_CUSTOMIZATION_AVAILABILITY a,
		   DT_OT_USER_PRODUCT_PREFERENCES pp,DT_RT_USER_PREFERENCES up
		   
		   WHERE
		   p.id_dt_ot_product = a.id_dt_ot_product
		   and 
		   p.merchant_id = ".$merchant_id." 
		   and
		   up.id_user = ".$consumer_id."
		   and
		   up.id_user_preferences = pp.id_user_preferences
		   and
		   pp.id_dt_ot_product = p.id_dt_ot_product";
 
	//echo $qry;
	$result = mysql_query($qry); 
	
	$json = array();
	$json2 = array();
	$productsArray = array();
	$products = array();
	$category_arry['categories'] = array();
	
	
	//$products_subcategory_Arry['subcat'] = array();
	
	while($row = mysql_fetch_array($result,MYSQL_ASSOC)) //to get product details
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
   		
   		//FILTER_VALIDATE_BOOLEAN filter validates value as a boolean option.
   		/* 
		 * Returns TRUE for "1", "true", "on" and "yes"
			Returns FALSE for "0", "false", "off" and "no"
			Returns NULL otherwise.
		 * */
   		$products->customization_availability =filter_var($row['customization_availability'],FILTER_VALIDATE_BOOLEAN);
   		
   		array_push($productsArray, $products);
   		
   		if($row['customization_availability'] == "yes") //if a product has a customization
   		{
   		
	   		
			$sql_product_category = "select cc.id_customization_category,cc.category_name,cc.category_image_url 
									from 
									DT_OT_PRODUCT_CUSTOMIZATION pc,
									DT_OT_PRODUCTS p,
									DT_RT_CUSTOMIZATION_CATEGORY cc
	
									where 
									p.id_dt_ot_product = ".$product_id."
									and
									cc.id_customization_category = pc.category_code
									and
									pc.id_dt_ot_product = p.id_dt_ot_product";
			/*$sql_product_category = "select cc.id_customization_category,cc.category_name,cc.category_image_url 
									from DT_OT_PRODUCT_CUSTOMIZATION pc,DT_OT_PRODUCTS p,DT_RT_CUSTOMIZATION_CATEGORY cc,DT_OT_USER_PREFERENCE_PRODUCT_CUSTOMIZATION ppc
									where 
									p.id_dt_ot_product = ".$product_id."
									and
									cc.id_customization_category = pc.category_code
									and
									ppc.id_customization_category_value = cc.id_customization_category";*/						
			//echo $sql_product_category;						
	
			$result_product_category = mysql_query($sql_product_category); 
			$json = array();
			while($row_product_category = mysql_fetch_array($result_product_category,MYSQL_ASSOC)) 
			{
				
				$Category = new Category();
				
				/*$sql_product_customization = "select cc.id_customization_category,cc.category_name,cc.category_image_url,cv.category_value_name,sa.store_alias_name,
												sa.id_customization_value_alias,sa.customization_price,sa.id_merchant,up.id_customization_vlaue_alias as preference_alias_id
	
												from DT_OT_PRODUCT_CUSTOMIZATION pc,DT_OT_PRODUCTS p,DT_RT_CUSTOMIZATION_CATEGORY cc,
												DT_RT_CUTOMIZATION_CATEGORY_VALUES cv,DT_RT_STORE_CUSTOMIZATION_VALUE_ALIAS sa
												
												LEFT JOIN DT_OT_USER_PREFERENCE_PRODUCT_CUSTOMIZATION up ON
												up.id_customization_vlaue_alias = sa.id_customization_value_alias 
												
												where 
												p.id_dt_ot_product = ".$product_id."
												and
												cc.id_customization_category = pc.category_code
												and
												cv.id_customization_category = cc.id_customization_category
												and
												cv.id_customization_category_value = sa.id_customization_category_value";*/
												
				/*$sql_product_customization = "select  cc.category_name,cc.category_image_url,cv.id_customization_category,cv.category_value_name,sa.store_alias_name,sa.id_customization_value_alias,sa.customization_price,sa.id_merchant,up.id_customization_vlaue_alias as preference_alias_id
	
												from DT_OT_PRODUCT_CUSTOMIZATION pc,
												DT_OT_PRODUCTS p,DT_RT_CUSTOMIZATION_CATEGORY cc,
												DT_RT_CUTOMIZATION_CATEGORY_VALUES cv,
												DT_RT_STORE_CUSTOMIZATION_VALUE_ALIAS sa
												
												LEFT JOIN DT_OT_USER_PREFERENCE_PRODUCT_CUSTOMIZATION up ON
												up.id_customization_vlaue_alias = sa.id_customization_value_alias

								
				
								where 
												p.id_dt_ot_product = ".$product_id."
												and
												cv.id_customization_category_value = sa.id_customization_category_value
												and
												cc.id_customization_category = pc.category_code
												and
												cv.id_customization_category = cc.id_customization_category
												and 
												p.id_dt_ot_product = pc.id_dt_ot_product
												 ";
		*/										
												
								$sql_product_customization = "select t.*,  (select v.id_customization_vlaue_alias  from vDT_OT_USER_PRODUCT_PREFERENCES_CUSTOMIZATION v where v.id_customization_vlaue_alias = t.id_customization_value_alias and v.id_dt_ot_product = t.id_dt_ot_product and v.id_user_preferences = t.id_user_preferences
															 ) as preference_alias_id 
															from (
															SELECT cc.category_name,cc.category_image_url,cv.id_customization_category,cv.category_value_name,sa.store_alias_name,
															sa.id_customization_value_alias,sa.customization_price, sa.id_merchant, pc.id_dt_ot_product, rup.id_user_preferences
															FROM DT_RT_CUSTOMIZATION_CATEGORY cc, DT_RT_CUTOMIZATION_CATEGORY_VALUES cv, DT_OT_PRODUCT_CUSTOMIZATION pc,  DT_OT_USER_PRODUCT_PREFERENCES upp, DT_RT_USER_PREFERENCES rup, DT_RT_STORE_CUSTOMIZATION_VALUE_ALIAS sa
															WHERE cc.id_customization_category = pc.category_code
															AND cv.id_customization_category = cc.id_customization_category
															and  rup.id_user_preferences = upp.id_user_preferences
															AND pc.id_dt_ot_product = upp.id_dt_ot_product
															and pc.id_dt_ot_product = ".$product_id."
															and cv.id_customization_category_value = sa.id_customization_category_value
															and rup.id_user = ".$consumer_id.") t";												
												
												
				/*$sql_product_customization = " select  cc.category_name,cc.category_image_url,cv.id_customization_category,cv.category_value_name,sa.store_alias_name,sa.id_customization_value_alias,sa.customization_price,sa.id_merchant,up.id_customization_vlaue_alias as preference_alias_id
	
												from DT_OT_PRODUCT_CUSTOMIZATION pc,
												DT_OT_PRODUCTS p,DT_RT_CUSTOMIZATION_CATEGORY cc,
												DT_RT_CUTOMIZATION_CATEGORY_VALUES cv,
												
												DT_OT_USER_PRODUCT_PREFERENCES upp,
												DT_RT_USER_PREFERENCES rup,
												DT_RT_STORE_CUSTOMIZATION_VALUE_ALIAS sa
																								
												LEFT JOIN DT_OT_USER_PREFERENCE_PRODUCT_CUSTOMIZATION up ON
												up.id_customization_vlaue_alias = sa.id_customization_value_alias
												
												where 
												p.id_dt_ot_product = ".$product_id."
												and
												cv.id_customization_category_value = sa.id_customization_category_value
												AND
												up.id_customization_vlaue_alias = sa.id_customization_value_alias
												and
												up.id_user_product_preferences = upp.id_user_product_preferences
												and
												rup.id_user_preferences = upp.id_user_preferences
												and
												upp.id_dt_ot_product = p.id_dt_ot_product
												and
												cc.id_customization_category = pc.category_code
												and
												cv.id_customization_category = cc.id_customization_category
												and 
												p.id_dt_ot_product = pc.id_dt_ot_product";*/
												
				//echo $sql_product_customization;
				$result_product_customization = mysql_query($sql_product_customization);
				
				
				
				
				$json2 = array(); //reinitializing array
				while($row_product_customization = mysql_fetch_array($result_product_customization,MYSQL_ASSOC)) //loop to get category values 
				{
					$subCategoryobj = new subCategory(); //creating object for values array
					
					
						if($row_product_category['category_name'] == $row_product_customization['category_name'])
						{
						
						//echo $row_product_customization['store_alias_name']."\n"; 
						$subCategoryobj->category_value_name =$row_product_customization['category_value_name'];
						$subCategoryobj->customization_category_name =$row_product_customization['category_name'];
						$subCategoryobj->category_ID=$row_product_customization['id_customization_category'];
						$subCategoryobj->store_alias_name = $row_product_customization['store_alias_name'];
						$subCategoryobj->id_customization_value_alias = $row_product_customization['id_customization_value_alias'];
						$subCategoryobj->customization_price = $row_product_customization['customization_price'];
						if($row_product_customization['preference_alias_id'] != null)
						{
							$subCategoryobj->selected =  TRUE;
							$subCategoryobj->preference_alias_id = $row_product_customization['preference_alias_id'];
						}
						
						array_push($json2, $subCategoryobj);
						
						$product_custom_details_ARY = array(				
						'Category_Name' => $row_product_category['category_name'],
						'Values' => $json2
						);	
						
					}// end of if	
						
							
				}//end of while
	
				array_push($json, $product_custom_details_ARY);	
				
			$category_arry['categories'] = $json;
				
			}//end $result_product_customization  while
		
		
		//$tempvar = $category_arry['categories'];
		$products->product_customization_details = $category_arry;
		//$products->$tempvar = $category_arry;	
		/*$merged = array();
		foreach($json as $item)
		{
		   $categoryname = $item['category_name'];
		    if (!isset($merged[$categoryname]))
		    {
		        $merged[$categoryname] = array();
		    }
		    $merged[$categoryname] = array_merge($merged[$categoryname], $item);
		}
		//$groups['category'] = array();
		//$groups['category'] = _group_by($json['category'],'category_name', $json_scalar, 'category_name') ;
		*/	
   		}// end if
   		/* adding product customization details to object array */
else {
	$category_arry['categories'] = array();	
	$products->product_customization_details = $category_arry;
	
}
   		
	 }//end while


deliver_response($productsArray); 
mysql_close();

function _group_by($array, $key,$array_scalar, $grouped_key) {
    $return = array();
    $return_scalar = array();
    foreach($array_scalar as $sval){
    $return [$sval[$key]] = $sval ;
    }
    foreach($array as $val) {
    	$return[$val[$key]][] = $val;
   	}
    return $return; 
   
}
	
	
	 
function deliver_response($arr)
{
$response['products_preferences']=$arr;
$json_response=json_encode($response, JSON_PRETTY_PRINT|JSON_NUMERIC_CHECK);
echo $json_response;
} 
?>
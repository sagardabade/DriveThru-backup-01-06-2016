<?php 


header('Content-Type: application/json; charset=utf-8');
   
   include("DB_config.php");
   connectDB();
   
class OrderArray {
    public $Order_ID = "";
    public $Token = "";
    public $Order_Status = "";
    public $sequence = "";
	public $Status_id = "";
	public $user_profile_image = "";
	public $consumerid = "";
	public $Products = "";
	
}

  
class Products {
    public $Product_ID = "";
    public $Product_Name = "";
    public $Quantity = "";
    public $Customization_Required = "";
    public $Customization = "";
    
}

class Customization{
	public $category;
	public $category_value;
}

$merchant_id = $_REQUEST['merchant_id'];
$pickedup = $_REQUEST['pickedup'];
$recent_oreders = $_REQUEST['recent'];
$delivery = $_REQUEST['delivery'];
$others = $_REQUEST['others'];

	if ($pickedup == "true") {
		
		$qry = "SELECT oc.order_id, os.status, oc.order_status_id, ot.order_token,os.status_id,oc.consumer_id,
				(select ud.user_profile_image_url from DT_OT_USER_DETAILS ud,DT_OT_USERS u,DT_OT_ORDER_CUSTOMER oc1  where oc1.consumer_id = u.id_user and ud.id_user = u.id_user and oc1.consumer_id = oc.consumer_id and oc1.order_id = oc.order_id ) 
				as user_profile_image
				FROM DT_OT_ORDER_TOKENS ot, DT_RT_ORDER_STATUS os, DT_OT_ORDER_CUSTOMER oc
				WHERE oc.order_id = ot.order_id
				AND oc.order_status_id = os.status_id
				AND oc.merchant_id =".$merchant_id."
				AND oc.order_status_id = 5
				AND  DATE(created_at) = CURDATE()
				ORDER BY  `oc`.`order_id` ASC ";
				
	} 
	elseif ($recent_oreders == "true") {
	
		$qry = "SELECT oc.order_id, os.status, oc.order_status_id, ot.order_token,os.status_id,oc.created_at,oc.consumer_id,
				(select ud.user_profile_image_url from DT_OT_USER_DETAILS ud,DT_OT_USERS u,DT_OT_ORDER_CUSTOMER oc1  where oc1.consumer_id = u.id_user and ud.id_user = u.id_user and oc1.consumer_id = oc.consumer_id and oc1.order_id = oc.order_id ) 
				as user_profile_image
				FROM DT_OT_ORDER_TOKENS ot, DT_RT_ORDER_STATUS os, DT_OT_ORDER_CUSTOMER oc
				WHERE oc.order_id = ot.order_id
				AND oc.order_status_id = os.status_id
				AND oc.merchant_id =".$merchant_id."
				AND  DATE(created_at) = CURDATE()
				AND oc.order_status_id IN (1,2,3)
				ORDER BY  `oc`.`order_id` ASC ";	
	}
	elseif ($others == "true") {
	
		$qry = "SELECT oc.order_id, os.status, oc.order_status_id, ot.order_token,os.status_id,oc.created_at,oc.consumer_id,
				(select ud.user_profile_image_url from DT_OT_USER_DETAILS ud,DT_OT_USERS u,DT_OT_ORDER_CUSTOMER oc1  where oc1.consumer_id = u.id_user and ud.id_user = u.id_user and oc1.consumer_id = oc.consumer_id and oc1.order_id = oc.order_id ) 
				as user_profile_image
				FROM DT_OT_ORDER_TOKENS ot, DT_RT_ORDER_STATUS os, DT_OT_ORDER_CUSTOMER oc
				WHERE oc.order_id = ot.order_id
				AND oc.order_status_id = os.status_id
				AND oc.merchant_id =".$merchant_id."
				AND  DATE(created_at) = CURDATE()
				AND oc.order_status_id IN (1,2)
				ORDER BY  `oc`.`order_id` ASC ";	
	}
	elseif ($delivery == "true") {
		
			$qry = "SELECT oc.order_id, os.status, oc.order_status_id, ot.order_token,os.status_id,oc.created_at,oc.consumer_id,
				(select ud.user_profile_image_url from DT_OT_USER_DETAILS ud,DT_OT_USERS u,DT_OT_ORDER_CUSTOMER oc1  where oc1.consumer_id = u.id_user and ud.id_user = u.id_user and oc1.consumer_id = oc.consumer_id and oc1.order_id = oc.order_id ) 
				as user_profile_image
				FROM DT_OT_ORDER_TOKENS ot, DT_RT_ORDER_STATUS os, DT_OT_ORDER_CUSTOMER oc
				WHERE oc.order_id = ot.order_id
				AND oc.order_status_id = os.status_id
				AND oc.merchant_id =".$merchant_id."
				AND oc.order_status_id IN (3,4)
				AND  DATE(created_at) = CURDATE()
				ORDER BY  `oc`.`order_id` ASC ";
	}
	else {
		
		$qry = "SELECT oc.order_id, os.status, oc.order_status_id, ot.order_token,os.status_id,oc.consumer_id,
				(select ud.user_profile_image_url from DT_OT_USER_DETAILS ud,DT_OT_USERS u,DT_OT_ORDER_CUSTOMER oc1  where oc1.consumer_id = u.id_user and ud.id_user = u.id_user and oc1.consumer_id = oc.consumer_id and oc1.order_id = oc.order_id ) 
				as user_profile_image
				FROM DT_OT_ORDER_TOKENS ot, DT_RT_ORDER_STATUS os, DT_OT_ORDER_CUSTOMER oc
				WHERE oc.order_id = ot.order_id
				AND oc.order_status_id = os.status_id
				AND oc.merchant_id =".$merchant_id."
				
				ORDER BY  `oc`.`order_id` ASC ";
	}
	

		
 //echo $qry;
	$OrderArray_temp = array();
	$json = array();
	$customization_tempArry = array();
	
	$result = mysql_query($qry);
	while($row = mysql_fetch_array($result,MYSQL_ASSOC)) //to get product details
	{
		$OrderArray = new OrderArray();
		
		$order_id = $row['order_id']; //order id to get orderdetails details
		$OrderArray->Order_ID = $row['order_id'];
   		$OrderArray->Token = $row['order_token'];
   		$OrderArray->Order_Status = $row['status'];
		$OrderArray->Status_id = $row['status_id'];
		$OrderArray->consumerid = $row['consumer_id'];
   		$OrderArray->sequence = $row['merchant_id'];
   		$OrderArray->user_profile_image = $row['user_profile_image'];
   		
		
		array_push($OrderArray_temp, $OrderArray);
		
		//get the order details
		$qry_orderdetails = "SELECT  od.order_id,od.id, od.item_name, od.item_quantity, od.item_price 

			from DT_OT_ORDER_DETAILS od, DT_OT_ORDER_CUSTOMER oc 

			where od.order_id =  ".$order_id."	
			and
			od.order_id = oc.order_id ";
			
		$result_orderdetails = mysql_query($qry_orderdetails);	
		$json = array();
		while($row_orderdetails = mysql_fetch_array($result_orderdetails,MYSQL_ASSOC)) 
		{
			
			$Products = new Products();  
			$order_details_ID = $row_orderdetails['id'];
			//$Products->Product_ID = $row_orderdetails['id_dt_ot_product'];
			$Products->Product_Name = $row_orderdetails['item_name'];
    		$Products->Quantity = $row_orderdetails['item_quantity'];
			
			$qry_order_customizationDetails = "SELECT cc.category_name, ca.store_alias_name

												FROM DT_RT_CUSTOMIZATION_CATEGORY cc ,DT_RT_STORE_CUSTOMIZATION_VALUE_ALIAS ca,
												DT_OT_ORDER_CUSTOMIZATION oc
												WHERE 
												oc.order_details_id = ".$order_details_ID." 
												and
												oc.id_customization_category_value = cc.id_customization_category
												and
												oc.id_customization_value_alias = ca.id_customization_value_alias";
												
			//echo $qry_order_customizationDetails;
			$result_order_customizationDetails = mysql_query($qry_order_customizationDetails);	
			
			
			$customization_tempArry = array();
			while($row_order_customizationDetails = mysql_fetch_array($result_order_customizationDetails,MYSQL_ASSOC)) 
			{
				$Customization = new Customization();
				$Customization->category = $row_order_customizationDetails['category_name'];
				$Customization->category_value = $row_order_customizationDetails['store_alias_name'];
				array_push($customization_tempArry,$Customization);
				
				
			}// End WHILE $result_order_customizationDetails	
			if(!empty($customization_tempArry))	
			{
				$Products->Customization_Required = "yes";
			}
			else
			{
				$Products->Customization_Required = "no";	
			} 
			$Products->Customization = $customization_tempArry;
 			array_push($json, $Products);
			
		}//end WHILE $result_orderdetails
		
		$OrderArray->Products = $json;
	}//end WHILE ORDERS
	
		
	deliver_response($OrderArray_temp); 
	mysql_close();
	
	function deliver_response($arr)
	{
		$response2['Merchant_ID'] = $_REQUEST['merchant_id'];
	$response2['Orders'] = array(
	"OrderArray" =>$arr
	);	
	//$response2['Orders'] = $response['OrderArray'];
	//$response['OrderArray']= $arr;// $arr;
	$json_response=json_encode($response2, JSON_PRETTY_PRINT|JSON_NUMERIC_CHECK);
	echo $json_response;
	}

?>
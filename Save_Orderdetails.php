<?php

   /*
   **************pusher block****************
   include required files
   */
   	require('Pusher.php');

	$app_id = '164557';
	$app_key = '1f006f9bd40000fbe5e8';
	$app_secret = '118ce01e86e2ff6aa374';
	
	$pusher = new Pusher(
	  $app_key,
	  $app_secret,
	  $app_id,
	  array('encrypted' => true)
	);
	
   /* 
   *************end of pusher block**********
   */	
   
   //print_r($_GET);
   
   header('Content-Type: application/json; charset=utf-8');
   
   include("DB_config.php");
   connectDB();
   
   	$json = file_get_contents('php://input'); //test_set_preference.json
   	//$json = file_get_contents('http://sqweezy.com/DriveThru/set_orderdetails.json'); //
   	 
	$obj = json_decode($json);
	
	//print_r($obj);
	$consumer_id = $obj->order->Consumer_ID;
   	$merchant_id = $obj->order->Merchant_ID;
   global $inserted_orderID;
   
   	$qry = "INSERT INTO DT_OT_ORDER_CUSTOMER (order_status_id,consumer_id, merchant_id, created_at, updated_at) VALUES(1, " . $consumer_id . ", " . $merchant_id . ", NOW(), NOW())";
	
	$productsarray = $obj->order->products;
	//$customizationArray_count = $obj->orders->products->customization;
	//$json = array();
	$result = mysql_query($qry); 
	
	$json = array();
	if($result == TRUE) 
	{ 
		$inserted_orderID = mysql_insert_id();
		
		// begin the sql statement
    		//$sql = "INSERT INTO DT_OT_ORDER_DETAILS (order_id, item_name, item_price, item_quantity, created_at, updated_at ) VALUES ";
    		//$numItems = count($productsarray);
    		//$i = 0;
    		// loop over the array(more than one array)
    		foreach($productsarray as $key) 
    		{
				//if product name is empty
				if(!isset($key->item_name))
				{
					$qry_product_details = "SELECT product_name FROM DT_OT_PRODUCTS WHERE id_dt_ot_product = ".$key->product_id." ";
					$result_product_details = mysql_query($qry_product_details);
					while($row_product_details = mysql_fetch_array($result_product_details,MYSQL_ASSOC)) //to get product details
					{
							$key->item_name = 	$row_product_details['product_name'];
					}
								
				}
				
        	// add to the query
        	$sql = "INSERT INTO DT_OT_ORDER_DETAILS (order_id, id_dt_ot_product,item_name, item_price, item_quantity, created_at, updated_at ) 
        			VALUES (".$inserted_orderID.",".$key->product_id.",'" .$key->item_name."'," .$key->item_price."," .$key->item_quantity.",NOW(),NOW())";
        	// if there is anot	her array member, add a comma
        	
        	/*if(!(++$i === $numItems)) {
		    $sql .= ",";
		    
		  	}*/ 
		 	$result_order_details = mysql_query($sql);
			if($result_order_details == TRUE) 
			{
				$inserted_orderDetailsID = mysql_insert_id();
			//query string for order customization INSERT
    		//$qry_order_customization = "INSERT INTO DT_OT_ORDER_CUSTOMIZATION (order_details_id, id_customization_category_value, id_customization_value_alias, customization_price) VALUES ";
    		
			
			//if customization is present for above inserted item/product then loop through and insert into order cutomization table
			if($key->customization_present == TRUE)
			{
				
				$customizationArry  = $key->customization;
				foreach($customizationArry as $customizationkey) 
    			{
    				// add to the query
		        	$qry_order_customization = "INSERT INTO DT_OT_ORDER_CUSTOMIZATION (order_details_id, id_customization_category_value, id_customization_value_alias, customization_price) 
		        								VALUES (".$inserted_orderDetailsID."," .$customizationkey->category_ID."
		        								," .$customizationkey->id_customization_value_alias."
		        								," .$customizationkey->customization_price.")";
		        	// if there is another array member, add a comma
		        	
		    	  	$result_order_customization = mysql_query($qry_order_customization);
					if($result_order_customization != TRUE)
					{
							$bus = array(
					    'error' => true,
					    'message' => "error customization save===".mysql_error());
					    array_push($json, $bus);
					    deliver_response($json);
					}// end IF $result_order_customization
				
				}// end of foreach $customizationArry
			} //end IF	customization_present
			
			
			}//end if 	$result_order_details
			
    		} //end of foreach $productsarray
    		 
    		
			
			
			//call a procedure to get a token
			$result_fn_next_token = mysql_query("SELECT fn_next_token(".$merchant_id.")");
			if($result_fn_next_token == TRUE)
			{
			
			while($row = mysql_fetch_row($result_fn_next_token)) 
			{ 
			$next_token = $row[0]; 
			} 
			//get the token and insert into "DT_OT_ORDER_TOKENS" table
			 $qry2 = "INSERT INTO DT_OT_ORDER_TOKENS (order_id,order_token, craeted_at, updated_at, ID_STORE) VALUES(".$inserted_orderID.", " . $next_token. ",  NOW(), NOW()," . $merchant_id . ")";
			
			$result_tokeninsert = mysql_query($qry2);
			if($result_tokeninsert != TRUE)
			{
				$bus = array(
					    'error' => true,
					    'message' => "error token===".mysql_error() );
					    array_push($json, $bus);
					    deliver_response($json);
			}
			
					
						$bus = array(
					    'error' => false,
					    'inserted_orderID' => $inserted_orderID,
					    'result_fn_next_token' => $next_token );
					    array_push($json, $bus);
					    deliver_response_order_success($json,$inserted_orderID);
						
					
					
					$qry_orderstatus = "SELECT status FROM DT_RT_ORDER_STATUS s,DT_OT_ORDER_CUSTOMER o WHERE o.order_id = ".$inserted_orderID." and o.order_status_id = s.status_id";
	 
					$result_orderstatus = mysql_query($qry_orderstatus);
					
					while($row = mysql_fetch_array($result_orderstatus,MYSQL_ASSOC)) 
					
					{
						$order_status = $row['status'];
						
					}
					//echo $order_status;
	    
					//$data['message'] = 'hello SANTOSH, you pushed a push notification using pusher !!..seriously???? :P';
					//$channel_consumer = $consumer_id;
					$message = "your order has been placed successfully";
					$data_consumer_orderdetails = array('message' => $message,
					'order_status' => $order_status,
					'token' => $next_token,
					'order_id' => $inserted_orderID);
					
					$pusher->trigger('consumer_'.$consumer_id.'', 'consumer_event', $data_consumer_orderdetails);
					//$pusher->trigger('consumer_10','consumer_event', $data_consumer_orderdetails);
					
					$message_new_order = "New Order!!";
					$data_order_recived = array('message' => $message_new_order,
					'order_id' =>$inserted_orderID,
					'token' => $next_token,
					'order_status' => $order_status);
					$pusher->trigger('md_channel', 'md_event', $data_order_recived);
					
					$message_new_order = "New Order!!";
					$data_order_recived = array('message' => $message_new_order,
					'order_id' =>$inserted_orderID,
					'token' => $next_token,
					'consumer_id' => $consumer_id,
					'order_status' => $order_status);
					$pusher->trigger('k_channel', 'k_event', $data_order_recived);
					
			
			}
			else //if token generation fails 
			{
				$bus = array(
		    'error' => true,
		    'message' => "error2==".mysql_error());
		    array_push($json, $bus);
		    deliver_response($json);
			}
	
		/*else
		{
			
			$bus = array(
		    'error' => true,
		    'message' => "error3===".mysql_error());
		    array_push($json, $bus);
		    deliver_response($json);
		}*/
	}
	
	else
	{
		    $bus = array(
		    'error' => true,
		    'message' => "INSERT INTO DT_OT_ORDER_CUSTOMER error=>".mysql_error()
		    );
		    array_push($json, $bus);
		    deliver_response($json);
		
	}
	
	mysql_close();
	function deliver_response($arr)
	{
	$response['result']=$arr;
	$json_response=json_encode($response, JSON_PRETTY_PRINT);
	echo $json_response;
	}
	
	function deliver_response_order_success($arr,$inserted_orderID)
	{
	$response['result']=$arr;
	//call get order details
	$result_orderdetails = get_order_details($inserted_orderID);
	$response['order_details'] = $result_orderdetails;
	$json_response=json_encode($response, JSON_PRETTY_PRINT);
	echo $json_response;
	}

   
   
   //get order details for an order
   function get_order_details($inserted_orderID)
   {
		   	class OrderArray {
		    public $Order_ID = "";
		    public $Token = "";
		    public $Order_Status = "";
		    public $sequence = "";
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
		
		//$order_id = $_REQUEST['order_id'];
		$order_id =  $inserted_orderID;
				$qry = "SELECT oc.order_id, os.status, oc.order_status_id, ot.order_token
						FROM DT_OT_ORDER_TOKENS ot, DT_RT_ORDER_STATUS os, DT_OT_ORDER_CUSTOMER oc
						WHERE oc.order_id = ot.order_id
						AND oc.order_status_id = os.status_id
						
						and oc.order_id = ".$order_id."
						ORDER BY  oc.order_id ASC ";
		 
			
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
		   		$OrderArray->sequence = $row['merchant_id'];
		   		
				
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
			
				
			return $OrderArray_temp; 
			mysql_close();
	
	
   }// END get_order_details
   
 ?>
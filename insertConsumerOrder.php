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
   
   $consumer_id = $_GET['consumer_id'];
   $merchant_id = $_GET['merchant_id'];
   
   $qry = "INSERT INTO DT_OT_ORDER_CUSTOMER (order_status_id,consumer_id, merchant_id, created_at, updated_at) VALUES(1, " . $consumer_id . ", " . $merchant_id . ", NOW(), NOW())";
	
	
	
	//$json = array();
	$result = mysql_query($qry); 
	
	$json = array();
	if($result == TRUE) 
	{ 
		$inserted_orderID = mysql_insert_id();
		
		// begin the sql statement
    		$sql = "INSERT INTO DT_OT_ORDER_DETAILS (order_id, item_name, item_price, item_quantity, created_at, updated_at ) VALUES ";
    		$numItems = count($_GET['item_name']);
    		
    		$i = 0;
    		// loop over the array(more than one array)
    		foreach(array_keys($_GET['item_name']) as $key) 
    		{
        	// add to the query
        	
        	
        	$sql .= "(".$inserted_orderID.",'" .$_GET['item_name'][$key]."'," .$_GET['item_price'][$key]."," .$_GET['item_quantity'][$key].",NOW(),NOW())";
        	// if there is another array member, add a comma
        	
        	if(!(++$i === $numItems)) {
		    $sql .= ",";
		    
		  }
		 
    		}
    		
    		$result_order_details = mysql_query($sql); 
    		if($result_order_details == TRUE) 
		{ 
			//call a procedure to get a token
			$result_fn_next_token = mysql_query("SELECT fn_next_token(".$merchant_id.")");
			if($result_fn_next_token == TRUE)
			{
			
			while($row = mysql_fetch_row($result_fn_next_token)) 
			{ 
			$next_token = $row[0]; 
			} 
			//get the token and insert into "DT_OT_ORDER_TOKENS" table
			 $qry2 = "INSERT INTO DT_OT_ORDER_TOKENS (order_id,order_token, craeted_at, updated_at, merchant_id) VALUES(".$inserted_orderID.", " . $next_token. ",  NOW(), NOW()," . $merchant_id . ")";
			
			$result_tokeninsert = mysql_query($qry2);
			if($result_order_details == TRUE) 
			{
					
						$bus = array(
					    'error' => false,
					    'inserted_orderID' => $inserted_orderID,
					    'result_fn_next_token' => $next_token );
					    array_push($json, $bus);
					    deliver_response($json);
					
					
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
					
					$pusher->trigger('consumer_10', 'consumer_event', $data_consumer_orderdetails);
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
			else
			{
				$bus = array(
		    'error' => true,
		    'message' => mysql_error());
		    array_push($json, $bus);
		    deliver_response($json);
			} 
			
			}
			else
			{
				$bus = array(
		    'error' => true,
		    'message' => "dd".mysql_error());
		    array_push($json, $bus);
		    deliver_response($json);
			}
	
		}
		else
		{
			
			$bus = array(
		    'error' => true,
		    'message' => mysql_error());
		    array_push($json, $bus);
		    deliver_response($json);
		}
	
	
	}
	
	else
	{
		    $bus = array(
		    'error' => true,
		    'message' => mysql_error()
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

   
   
 ?>
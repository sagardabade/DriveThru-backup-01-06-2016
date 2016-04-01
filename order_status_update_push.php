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
   
   
    header('Content-Type: application/json; charset=utf-8');
   
   include("DB_config.php");
   connectDB();
   
   $order_id = $_GET['order_id'];
   $status = $_GET['status'];
   
   $qry_order_status_update = "UPDATE DT_OT_ORDER_CUSTOMER SET order_status_id=".$status.",updated_at=NOW() WHERE order_id=".$order_id." ";
	 
  $result_status = mysql_query($qry_order_status_update);
   //echo $result_status;
  $json = array(); 	 
  if(mysql_affected_rows() > 0)
  {
  	
	
	$result = "SELECT status FROM DT_RT_ORDER_STATUS s,DT_OT_ORDER_CUSTOMER o WHERE o.order_id = ".$order_id." and o.order_status_id = s.status_id";
	 
	$result1 = mysql_query($result);
	if($result1 == TRUE)
	{
	 //echo $result1['status']; 
	while($row = mysql_fetch_array($result1,MYSQL_ASSOC)) 
	
	{
		$order_status = $row['status'];
		
	}
	//echo $order_status;
	$bus = array(
		    'error' => flase,
		    'order_status' => $order_status,
		    'message' => "update success!");
		    array_push($json, $bus);
		    deliver_response($json);
		    	    
	$message ="your order status is ".$order_status." ";
	$data_consumer = array('message' => $message,
	'orderID' => $order_id,
	'order_status' => $order_status);
	$pusher->trigger('consumer_10', 'consumer_event', $data_consumer);
	
	$message_md ="order status for orderID-".$order_id." is ".$order_status." ";
	$data = array('message' => $message_md,
	'orderID' => $order_id,
	'order_status' => $order_status);
	$pusher->trigger('md_channel', 'md_event', $data);
	
	}
	
	else
	{
		$bus = array(
		    'error' => true,
		    'message' => "problem in fetching order status",
		    'error_message' => mysql_error());
		    array_push($json, $bus);
		    deliver_response($json);
	
	}
	
  }
 else
  {
  	$bus = array(
		    'error' => true,
		    'message' => "update failed!");
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
<?php


header('Content-Type: application/json; charset=utf-8');
   
include("DB_config.php");
connectDB();
   
//registering a new user


//user basic details
$merchant_id 	= $_REQUEST['merchant_id'];
$merchant_status 	= $_REQUEST['merchant_status'];
$checkOpen 	= $_REQUEST['checkOpen'];
$json = array();
// First check if user already existed in db
if($checkOpen == 1)
{
 $qry_merchant_status_update = "UPDATE DT_OT_MERCHANT SET merchant_status='".$merchant_status."' WHERE id_merchant=".$merchant_id." ";
	 
  $result_status = mysql_query($qry_merchant_status_update);
  
  
  if($result_status == TRUE)
	{
	 //echo $result1['status']; 
	
	$result = "SELECT merchant_status FROM DT_OT_MERCHANT WHERE id_merchant = ".$merchant_id." ";
	 
	$result1 = mysql_query($result);
	//echo $result1; 
	if($result1 == TRUE)
	{
	 //echo $result1['status']; 
	while($row = mysql_fetch_array($result1,MYSQL_ASSOC)) 
	
	{
		$merchant_status = $row['merchant_status'];
		
	}
	//echo $order_status;
	$bus = array(
		    'error' => FALSE,
		    'message' => "Updated",
		    'merchant_status' => $merchant_status
		    );
		      array_push($json, $bus);
		      }
		      
  deliver_response($json);
  
  }
  }
  else{
  //echo $result_status; 
  $result = "SELECT merchant_status FROM DT_OT_MERCHANT WHERE id_merchant = ".$merchant_id." ";
	 
	$result1 = mysql_query($result);
	//echo $result1; 
	if($result1 == TRUE)
	{
	 //echo $result1['status']; 
	while($row = mysql_fetch_array($result1,MYSQL_ASSOC)) 
	
	{
		$merchant_status = $row['merchant_status'];
		
	}
	//echo $order_status;
	$bus = array(
		    'error' => FALSE,
		    'message' => "Select",
		    'merchant_status' => $merchant_status
		    );
		    array_push($json, $bus);
		    deliver_response($json);
}
}
	//deliver_response($bus);
	
	/* json response of the file tobe displayed */
	function deliver_response($arr)
	{
	$response['result']=$arr;
	$json_response=json_encode($response, JSON_PRETTY_PRINT);
	echo $json_response;
	
	}
	
    
?>
<?php


header('Content-Type: application/json; charset=utf-8');
   
include("DB_config.php");
connectDB();
   
//registering a new user


//user basic details
$merchant_id 	= $_REQUEST['merchant_id'];
$merchant_pincode 	= $_REQUEST['merchant_pincode'];
$checkUpdate 	= $_REQUEST['checkUpdate'];
$json = array();
// First check if user already existed in db
if($checkUpdate == 1)
{
 $qry_merchant_status_update = "UPDATE DT_OT_MERCHANT SET merchant_pincode='".$merchant_pincode."' WHERE id_merchant='".$merchant_id."' ";
	 
  $result_status = mysql_query($qry_merchant_status_update);
  
  
  if($result_status == TRUE)
	{
	
	//echo $order_status;
	$bus = array(
		    'error' => FALSE,
		    'message' => "Updated",
		      );
		      array_push($json, $bus);
		      }
		      
  deliver_response($json);
  
  }
  
  else
  {
  
  
  
	$result = "SELECT merchant_pincode FROM DT_OT_MERCHANT WHERE id_merchant = ".$merchant_id." ";
	 
	$result1 = mysql_query($result);
	//echo $result1; 
	if($result1 == TRUE)
	{
	 //echo $result1['status']; 
	while($row = mysql_fetch_array($result1,MYSQL_ASSOC)) 
	
	{
		$merchant_pincode = $row['merchant_pincode'];
		
	}
	//echo $order_status;
	$bus = array(
		    'error' => FALSE,
		    'message' => "Exits",
		    'merchant_pincode' => $merchant_pincode
		    );
		      array_push($json, $bus);
		      }
		      
  deliver_response($json);
  
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
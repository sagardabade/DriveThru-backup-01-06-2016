<?php
 

 
	header('Content-Type: application/json; charset=utf-8');
	

	include("DB_config.php");
	 
	connectDB();

	
	$consumer_id = $_REQUEST['consumer_id'];
	$basetype = $_REQUEST['basetype'];
   	$base_location_home_lat = $_REQUEST['base_location_home_latitude'];
   	$base_location_home_long = $_REQUEST['base_location_home_longitude'];
   	$base_location_work_lat = $_REQUEST['base_location_work_latitude'];
   	$base_location_work_long = $_REQUEST['base_location_work_longitude'];
   	$json = array();
   	
   	if($basetype == 'HOME'){
   
   	$sql_USER_BASE_LOCAITON = "INSERT INTO DT_OT_USER_LOCATION (id_user, user_home_latitude, user_home_longitude, user_location_updated_at)
    VALUES (" . $consumer_id . ", " . $base_location_home_lat . "," . $base_location_home_long . ", " . "NOW()) ON DUPLICATE KEY UPDATE
        user_home_latitude = " . $base_location_home_lat . ",
        user_home_longitude = " . $base_location_home_long . ",
        user_location_updated_at = NOW())";
   	
	    		}
	  elseif ($basetype == 'WORK'){
	  
	  $sql_USER_BASE_LOCAITON = "INSERT INTO DT_OT_USER_LOCATION (id_user, user_work_latitude, user_work_longitude, user_location_updated_at)
    VALUES (" . $consumer_id . ", " . $base_location_work_lat . "," . $base_location_work_long . ", " . "NOW()) ON DUPLICATE KEY UPDATE
        user_work_latitude = " . $base_location_work_lat . ",
        user_work_longitude = " . $base_location_work_long . ",
        user_location_updated_at = NOW()";
        
	    										 }
	    		$result_USER_BASE_LOCAITON = mysql_query($sql_USER_BASE_LOCAITON); 
	    		
	    		
   	if($result_USER_BASE_LOCAITON == TRUE)//if insert is successfull
	    			{
	    				$tempArry = array(
					    'error' => false,
					    'message' => "success");
					    array_push($json, $tempArry);
					    deliver_response($json);
					   
	    			}
	    			else//insert fails
	    			{
	    				$tempArry = array(
					    'error' => true,
					    'message' => "error 03 -".mysql_error()."");
					    array_push($json, $tempArry);
					    deliver_response($json);
	    			};
	    			
	    			
	mysql_close();
	
	function deliver_response($arr)
	{
	$response['result']=$arr;
	$json_response=json_encode($response, JSON_PRETTY_PRINT);
	echo $json_response;
	}

?>
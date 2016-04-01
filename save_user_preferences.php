<?php
 
	header('Content-Type: application/json; charset=utf-8');
   
	include("DB_config.php");
	connectDB();
    
   	$json = file_get_contents('php://input'); //test_set_preference.json
   	//$json = file_get_contents('http://sqweezy.com/DriveThru/test_set_preference.json'); //
   	 
	$obj = json_decode($json);
	
	//$result = json_encode($obj);
	global $consumer_id;
	global $merchant_id;
	global $productsarray;
	$consumer_id = $obj->Consumer_ID;
   	$merchant_id = $obj->Merchant_ID;
   	$product_id = $_REQUEST['product_id'];
   	$product_alias_id = $_REQUEST['product_alias_id'];
	global $inserted_USER_PREFERENCESID;
	
	//$products_Arry = $obj->products;
	
   	/* saving user preferences 
   	*tables used(3)
   	DT_RT_USER_PREFERENCES
   	DT_OT_USER_PRODUCT_PREFERENCES
   	DT_OT_USER_PREFERENCE_PRODUCT_CUSTOMIZATION
   	
   	insert1- insert merchantID,userID into DT_RT_USER_PREFERENCES
   	insert2- insert productID and preferenceID(DT_RT_USER_PREFERENCES table) into DT_OT_USER_PRODUCT_PREFERENCES
   	insert3- insert preferenceID(DT_OT_USER_PRODUCT_PREFERENCES table),storealiasID into  DT_OT_USER_PREFERENCE_PRODUCT_CUSTOMIZATION
   	*/
	$productsarray = $obj->preferences->products;
	//echo is_array($obj->preferences->products);
	/* ######################## DELETE BEFORE INSERT ########################## */
	//before insert into DT_RT_USER_PREFERENCES check for duplicate entries 
	//select from DT_RT_USER_PREFERENCES
	$qry_select_user_preferences = "SELECT id_user_preferences from DT_RT_USER_PREFERENCES where id_merchant =".$merchant_id."  and id_user = ".$consumer_id." "; 
	
	$result_select_user_preferences = mysql_query($qry_select_user_preferences);
	
	if(mysql_num_rows($result_select_user_preferences) > 0) //if user already has preference
	{
		$json = array();
		while($row_select_user_preferences = mysql_fetch_array($result_select_user_preferences,MYSQL_ASSOC)) //to get product details
		{
			$inserted_USER_PREFERENCESID = $row_select_user_preferences['id_user_preferences'];
			
			/*$qry_delete_preferences  = "DELETE DT_OT_USER_PRODUCT_PREFERENCES , DT_OT_USER_PREFERENCE_PRODUCT_CUSTOMIZATION  FROM DT_OT_USER_PRODUCT_PREFERENCES  INNER JOIN DT_OT_USER_PREFERENCE_PRODUCT_CUSTOMIZATION  
										WHERE DT_OT_USER_PREFERENCE_PRODUCT_CUSTOMIZATION.id_user_product_preferences= DT_OT_USER_PRODUCT_PREFERENCES.id_user_product_preferences 
										and DT_OT_USER_PRODUCT_PREFERENCES.id_user_preferences = ".$inserted_USER_PREFERENCESID." ";
			*/
			$qry_delete_preferences  = "DELETE DT_OT_USER_PRODUCT_PREFERENCES , DT_OT_USER_PREFERENCE_PRODUCT_CUSTOMIZATION  FROM DT_OT_USER_PRODUCT_PREFERENCES  INNER JOIN DT_OT_USER_PREFERENCE_PRODUCT_CUSTOMIZATION  ON
										DT_OT_USER_PRODUCT_PREFERENCES.id_user_product_preferences=
										DT_OT_USER_PREFERENCE_PRODUCT_CUSTOMIZATION.id_user_product_preferences 
										where	 
										DT_OT_USER_PRODUCT_PREFERENCES.id_user_preferences = ".$inserted_USER_PREFERENCESID." ";
			
			$result_delete_preferences = mysql_query($qry_delete_preferences);
										
			$qry_delete_preferences_without_cutomization  = "DELETE   FROM DT_OT_USER_PRODUCT_PREFERENCES  	where	 
										DT_OT_USER_PRODUCT_PREFERENCES.id_user_preferences = ".$inserted_USER_PREFERENCESID." ";							
						 				
			$result_delete_preferences_without_cutomization = mysql_query($qry_delete_preferences_without_cutomization);	
			
			//if user deletes all preferences 
			if (mysql_affected_rows() > 0) 
			{
					$tempArry = array(
						    'error' => FALSE,
						    'message' => "user preferences empty");
						    array_push($json, $tempArry);
						    deliver_response($json);
				
			} //END IF	
								
		}//END WHILE 
		
		//re-insert all product preferences
			foreach ($productsarray as  $value) // loop for all products
				{
				//insert2
				// begin the sql statement
		    		$sql_USER_PRODUCT_PREFERENCES = "INSERT INTO DT_OT_USER_PRODUCT_PREFERENCES (id_user_preferences,id_dt_ot_product, product_preferences_created_at, product_preferences_updated_at	) 
		    										 VALUES( " . $inserted_USER_PREFERENCESID . ", " . $value->product_id . ", NOW(), NOW())";
		    		
		    		$result_USER_PRODUCT_PREFERENCES = mysql_query($sql_USER_PRODUCT_PREFERENCES); 
		    		
		    		if($result_USER_PRODUCT_PREFERENCES == TRUE) //if  $result_USER_PRODUCT_PREFERENCES is successful
					{
					$inserted_USER_PRODUCT_PREFERENCESID = mysql_insert_id();
					
			 		foreach ($value->customization as  $value2) // loop for all customization
					{
						//insert3
						// begin the sql statement
		    			$sql_USER_PREFERENCE_PRODUCT_CUSTOMIZATION = "INSERT INTO DT_OT_USER_PREFERENCE_PRODUCT_CUSTOMIZATION (id_user_product_preferences,id_customization_vlaue_alias,id_customization_category_value, user_cutomization_created_at,user_cutomization_updated_at) 
		    														  VALUES( " . $inserted_USER_PRODUCT_PREFERENCESID. ", " . $value2->id_customization_value_alias. ",".$value2->category_ID.", NOW(), NOW())";
		    			
		    			$result_USER_PREFERENCE_PRODUCT_CUSTOMIZATION = mysql_query($sql_USER_PREFERENCE_PRODUCT_CUSTOMIZATION); 
		    			
		    			if($result_USER_PREFERENCE_PRODUCT_CUSTOMIZATION == TRUE)//if insert is successfull
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
		    			}
					
					}//end of customize loop
				}//end if(userProductpreference)
				
				else//if insert fails
				{
					$tempArry = array(
						    'error' => true,
						    'message' => "error 02".mysql_error());
						    array_push($json, $tempArry);
						    deliver_response($json);	
				}
				
				
			}//end of products loop
		
	}
	
	//when user has preferences first time
	else 
	{
		$qry_USER_PREFERENCES = "INSERT INTO DT_RT_USER_PREFERENCES (id_merchant,id_user, user_preferences_created_at, user_preferences_updated_at) VALUES( " . $merchant_id . ", " . $consumer_id . ", NOW(), NOW())";
		$result_USER_PREFERENCES = mysql_query($qry_USER_PREFERENCES); 
		
		$json = array();
		if($result_USER_PREFERENCES == TRUE) //if  $result_USER_PREFERENCES is successful
		{ 
			$inserted_USER_PREFERENCESID = mysql_insert_id();
			foreach ($productsarray as  $value) // loop for all products
			{
			//insert2
			// begin the sql statement
	    		$sql_USER_PRODUCT_PREFERENCES = "INSERT INTO DT_OT_USER_PRODUCT_PREFERENCES (id_user_preferences,id_dt_ot_product, product_preferences_created_at, product_preferences_updated_at	) 
	    										 VALUES( " . $inserted_USER_PREFERENCESID . ", " . $value->product_id . ", NOW(), NOW())";
	    		
	    		$result_USER_PRODUCT_PREFERENCES = mysql_query($sql_USER_PRODUCT_PREFERENCES); 
	    		
	    		if($result_USER_PRODUCT_PREFERENCES == TRUE) //if  $result_USER_PRODUCT_PREFERENCES is successful
				{
				$inserted_USER_PRODUCT_PREFERENCESID = mysql_insert_id();
		 		foreach ($value->customization as  $value2) // loop for all customization
				{
					//insert3
					// begin the sql statement
	    			$sql_USER_PREFERENCE_PRODUCT_CUSTOMIZATION = "INSERT INTO DT_OT_USER_PREFERENCE_PRODUCT_CUSTOMIZATION (id_user_product_preferences,id_customization_vlaue_alias,id_customization_category_value, user_cutomization_created_at,user_cutomization_updated_at) 
	    														  VALUES( " . $inserted_USER_PRODUCT_PREFERENCESID. ", " . $value2->id_customization_value_alias. ",".$value2->category_ID.", NOW(), NOW())";
	    			
	    			$result_USER_PREFERENCE_PRODUCT_CUSTOMIZATION = mysql_query($sql_USER_PREFERENCE_PRODUCT_CUSTOMIZATION); 
	    			
	    			if($result_USER_PREFERENCE_PRODUCT_CUSTOMIZATION == TRUE)//if insert is successfull
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
	    			}
				
				}//end of customize loop
			}//end if(userProductpreference)
			
			else//if insert fails
			{
				$tempArry = array(
					    'error' => true,
					    'message' => "error 02".mysql_error());
					    array_push($json, $tempArry);
					    deliver_response($json);	
			}
			
			
		}//end of products loop
		
		}// end if(userpreference insert check)	
		 
		else//if insert fails
		{
			$tempArry = array(
				    'error' => true,
				    'message' => "error 01".mysql_error());
				    array_push($json, $tempArry);
				    deliver_response($json);
		}	
	}
	/* ####################### END OF DELETE BLOCK ############################ */
	
	

	mysql_close();
	
	function deliver_response($arr)
	{
	$response['result']=$arr;
	$json_response=json_encode($response, JSON_PRETTY_PRINT);
	echo $json_response;
	}

?>
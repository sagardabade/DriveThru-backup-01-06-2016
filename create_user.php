<?php


header('Content-Type: application/json; charset=utf-8');
   
include("DB_config.php");
connectDB();
   
//registering a new user


//user basic details
$first_name 	= $_REQUEST['first_name'];
$last_name 	= $_REQUEST['last_name'];
$email		= $_REQUEST['email'];
$mobile		= $_REQUEST['mobile'];
$password	= $_REQUEST['password'];
global $fbid;
global $gplus_id;
$fbid = $_REQUEST["fid"];
global $FB_URL; 
$FB_URL = "https://graph.facebook.com/".$fbid."/picture?type=large";

$json = array();
// First check if user already existed in db

if (!isUserExists($email)) 
{
	require_once 'PassHash.php';
	
	// Generating password hash
        $password_hash = PassHash::hash($password);

	$qry_insert_user = "INSERT INTO DT_OT_USERS (first_name,last_name, email, mobile, password,created_at,updated_at) VALUES('".$first_name."', '" .$last_name. "','" . $email. "','".$mobile."','".$password_hash."', NOW(), NOW())";
	
	//echo $qry_insert_user;
	$result_insert_user = mysql_query($qry_insert_user);
	
	if($result_insert_user == TRUE) 
	{
		$last_insted_userID = mysql_insert_id();
		
		//commented on 09-03-2016(to change image URL data flow according to the requirment change)
		/*if(isset($_REQUEST['image_url']) && !empty($_REQUEST['image_url']))
		{
			$image_url = $_REQUEST['image_url'];
			//insert user details into DT_OT_USER_DETAILS 
			$qry_insert_userdetails = "INSERT INTO DT_OT_USER_DETAILS (user_profile_image_url,user_details_created_at, user_details_updated_at, id_user) VALUES('".$image_url."', NOW(),NOW(),".$last_insted_userID.")";
			$result_insert_userdetails = mysql_query($qry_insert_userdetails);
		}*/
		
		
		/* 
		**three condition check
		**1-if facebook id is provided (insert facebook details in DT_OT_FB_DETAILS table and return succcess)
		**2-if google id is provided (insert google details in DT_OT_GPLUS_DETAILS table and return succcess)
		**3-if no facebook or google ID is provided (return succcess)
		*/
		//facebook ID
		if(isset($_REQUEST["fid"]))
		{
			$facebook_id = $_REQUEST["fid"];
			
			$qry_insert_user_fbdetails = "INSERT INTO DT_OT_FB_DETAILS (user_FID,id_user, fb_details_created_at,fb_details_updated_at) VALUES('".$facebook_id."', " .$last_insted_userID. ", NOW(), NOW())";
			
			$image_url = $FB_URL;
			//insert user details into DT_OT_USER_DETAILS 
			$qry_insert_userdetails = "INSERT INTO DT_OT_USER_DETAILS (user_profile_image_url,user_details_created_at, user_details_updated_at, id_user) VALUES('".$image_url."', NOW(),NOW(),".$last_insted_userID.")";
			$result_insert_userdetails = mysql_query($qry_insert_userdetails);
			
			
			$result_insert_user_fbdetails = mysql_query($qry_insert_user_fbdetails);
			if($result_insert_user_fbdetails) //on successfull insert of facebook details 
			{
				    $bus = array(
				    'error' 		=> false,
				    'message' 		=> "user registration successfull",
				    'last_insted_userID'=> $last_insted_userID );
				    
				    array_push($json, $bus);
				    deliver_response($json);
			} // facebook details insert condition check
			else
			{
				$bus = array(
				    'error' 		=> true,
				    'message' 		=> mysql_error());
				    
				    array_push($json, $bus);
				    deliver_response($json);
			}	
		} //facebook ID condition check
		
		//google plus ID
		else if(isset($_REQUEST["gid"]))
		{
			$google_plus_id = $_REQUEST["gid"];	
			
			$qry_insert_user_googledetails = "INSERT INTO DT_OT_GPLUS_DETAILS (user_GID,id_user, gplus_details_created_at,gplus_details_updated_at) VALUES('".$google_plus_id."', " .$last_insted_userID. ", NOW(), NOW())";
		
			$result_insert_user_googledetails = mysql_query($qry_insert_user_googledetails);
			
			$image_url = $_REQUEST['image_url'];
			//insert user details into DT_OT_USER_DETAILS 
			$qry_insert_userdetails = "INSERT INTO DT_OT_USER_DETAILS (user_profile_image_url,user_details_created_at, user_details_updated_at, id_user) VALUES('".$image_url."', NOW(),NOW(),".$last_insted_userID.")";
			$result_insert_userdetails = mysql_query($qry_insert_userdetails);
			
			
			if($result_insert_user_googledetails) //on successfull insert of google details 
			{
				    $bus = array(
				    'error' 		=> false,
				    'message' 		=> "user registration successfull",
				    'last_insted_userID'=> $last_insted_userID );
				    
				    array_push($json, $bus);
				    deliver_response($json);
			} // google details insert condition check
			else
			{
				$bus = array(
				    'error' 		=> true,
				    'message' 		=> mysql_error());
				    
				    array_push($json, $bus);
				    deliver_response($json);
			}
			
		}// google ID condition check
		
		else //no facebook ID or google ID provided
		{
			$bus = array(
				'error' 		=> false,
				'message' 		=> "user registration successfull",
				'last_insted_userID'=> $last_insted_userID);
				    
				array_push($json, $bus);
				deliver_response($json);	
		}

	}// user insert condition check 
	
	else
	{
		$bus = array(
			'error' 	=> true,
			'message' 	=> mysql_error());
			   
			array_push($json, $bus);
			deliver_response($json);		
	}		

} // duplicate user condition check

else
{
	//for existing user get email and userID
	$qry_userID = "SELECT id_user,email from DT_OT_USERS WHERE email = '".$email."' ";
    $result_qry_userID = mysql_query($qry_userID); 
	while($row = mysql_fetch_array($result_qry_userID,MYSQL_ASSOC)) //to get product details
	{
		$bus = array(
		'error' 	=> false,
		'message' 	=> "email already exists!",
		'last_insted_userID'=> $row['id_user'],
		
		);
	}
	
		array_push($json, $bus);
		deliver_response($json);

}//end else

/**
     * Checking for duplicate user by email address
     * @param String $email email to check in db
     * @return boolean
     */
    function isUserExists($email) {
         
            
        $stmt = "SELECT id_user from DT_OT_USERS WHERE email = '".$email."' ";
        $result = mysql_query($stmt); 
        if(mysql_num_rows($result) > 0)
        {
        	return true;
        }
        else
        {
        	return false;
        }
    }

	/* json response of the file tobe displayed */
	function deliver_response($arr)
	{
	$response['result']=$arr;
	$json_response=json_encode($response, JSON_PRETTY_PRINT);
	echo $json_response;
	
	}
	
    
?>
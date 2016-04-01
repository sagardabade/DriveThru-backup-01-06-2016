<?php


header('Content-Type: application/json; charset=utf-8');
   
include("DB_config.php");
connectDB();

$merchant_email = $_REQUEST['merchant_email'];
$merchant_password = $_REQUEST['merchant_password'];

 $json = array();
$select = mysql_query("SELECT id_merchant,merchant_password FROM `DT_OT_MERCHANT` WHERE `merchant_email` = '".$merchant_email."' and `merchant_password` = '".$merchant_password."'") or exit(mysql_error());
if(mysql_num_rows($select))

{
	while($row = mysql_fetch_array($select,MYSQL_ASSOC)) 
	{
		$bus = array(
		'error' => false,
		'id_merchant' => $row['id_merchant'],
		'merchant_password' => $row['merchant_password'],
		'record' => "exists"
			);
			array_push($json, $bus);
	}
		
   // exit("This email is already being used");
   deliver_response($json);
    
   }
   else
   {
   
   $bus = array(
		'error' => true,
		
		'record' => "No Record"
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
<?php
 
   header('Content-Type: application/json; charset=utf-8');
   
   include("DB_config.php");
   connectDB();
   
   $lat = $_REQUEST['lat'];
   $lng = $_REQUEST['lng'];
  
 $qry = "select DISTINCT id_merchant,merchant_name,merchant_logo from(SELECT s.id_store,s.store_location_lat,s.store_location_lng,s.store_created_at,s.store_updated_at,m.id_merchant,m.merchant_name,m.merchant_logo,
(((acos(sin((".$lat."*pi()/180)) * 
						            sin((s.store_location_lat*pi()/180))+cos((".$lat."*pi()/180)) * 
						            cos((s.store_location_lat*pi()/180)) * cos(((".$lng."-s.store_location_lng)* 
						            pi()/180))))*180/pi())*60*1.1515
						        ) as distance

FROM DT_OT_STORE s,DT_OT_MERCHANT m where s.id_merchant = m.id_merchant) as temp where distance <=2";

//echo $qry;
//$qry="SELECT id_merchant, merchant_name, merchant_logo  FROM DT_OT_MERCHANT"

//$json = array();
	$result = mysql_query($qry); 
	
	$json = array();
	$json2 = array();
	$json_temp = array();
	$Merchant_Array['Merchant_Array'] = array();
	$storeArray['Store_Array'] = array();
	while($row = mysql_fetch_array($result,MYSQL_ASSOC)) 
	{
	
		
		
		
		$qry_get_store = "SELECT * from(SELECT id_store,id_merchant,store_location_lat,store_location_lng,store_created_at,store_updated_at,
(((acos(sin((".$lat."*pi()/180)) * 
						            sin((store_location_lat*pi()/180))+cos((".$lat."*pi()/180)) * 
						            cos((store_location_lat*pi()/180)) * cos(((".$lng."-store_location_lng)* 
						            pi()/180))))*180/pi())*60*1.1515
						        ) as distance

FROM DT_OT_STORE  where id_merchant = ".$row['id_merchant'].") as temp where distance <=2"; 
		
		
		$result_qry_get_store = mysql_query($qry_get_store); 
		while($row_store= mysql_fetch_array($result_qry_get_store,MYSQL_ASSOC)) //get images loop
		{
		
		$bus['Store'] = array(
		'Store_ID' => $row_store['id_store'],
		'Store_Lat' => $row_store['store_location_lat'], 
		'Store_Lng' => $row_store['store_location_lng'], 
		'Store_Address' =>getAddress($row_store['store_location_lat'],$row_store['store_location_lng']),
		'created_at' => $row_store['store_created_at'],
		'updated_at' => $row_store['store_updated_at']
		 );
		array_push($json, $bus);
		
		}//end of store loop
		$storeArray['Store_Array'] = $json;
		$storeObject = $json;
	$tempArry['Merchant'] = array(
		'Merchant_Name' => $row['merchant_name'],
		'Merchant_ID' => $row['id_merchant'],
		'Merchant_Image' => $row['merchant_logo'],
		//$storeArray
		'STORE_ARRAY' => $storeObject
		);
		
		array_push($json2,$tempArry);	
	//array_merge($storeArray, $bus);
	
	}//end of while(stores)
	//array_push($storeArray,$bus);
	
	
	$Merchant_Array['Merchant_Array'] = $json2;
	//$resultArry = array_merge($Merchant_Array, $storeArray);
	
deliver_response($Merchant_Array); 
mysql_close();


/* rever geocoding */

function getAddress($lat, $lon){
   $url  = "http://maps.googleapis.com/maps/api/geocode/json?latlng=".
            $lat.",".$lon."&sensor=false";
   $json = @file_get_contents($url);
   $data = json_decode($json);
   $status = $data->status;
   $address = '';
   if($status == "OK"){
      $address = $data->results[0]->formatted_address;
    }
   return $address;
  }


/* end of reverse geocode */	 
	 
function deliver_response($arr)
{
$response['Stores']=$arr;
$json_response=json_encode($response, JSON_PRETTY_PRINT|JSON_NUMERIC_CHECK);
echo $json_response;
} 
?>
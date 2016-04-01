<?php
 
   header('Content-Type: application/json; charset=utf-8');
   
   include("DB_config.php");
   connectDB();
   
   $consumer_id = $_GET['consumer_id'];
   $merchant_id = $_GET['merchant_id'];
   $order_id = $_GET['order_id'];   
   
if(!empty($merchant_id) && isset($merchant_id) && empty($order_id))
{
	$qry ="SELECT DISTINCT od.order_id,os.status,oc.order_status_id, ot.order_token, od.item_name, od.item_quantity, od.item_price 

			from DT_OT_ORDER_DETAILS od, DT_OT_ORDER_TOKENS ot,DT_RT_ORDER_STATUS os,DT_OT_ORDER_CUSTOMER oc 

			where od.order_id = ot.order_id 
			and oc.order_status_id = os.status_id
			and oc.merchant_id =".$merchant_id." ";	
}
   
if (empty($order_id) && empty($merchant_id)){
 //$qry = "SELECT  od.order_id, ot.order_token, od.item_name, od.item_quantity, od.item_price, (select count(1) from DT_OT_ORDER_DETAILS od1 where od1.order_id = od.order_id) as order_detail_count   from DT_OT_ORDER_DETAILS od, DT_OT_ORDER_TOKENS ot where od.order_id = ot.order_id ";

$qry = "SELECT  od.order_id,os.status,oc.order_status_id, ot.order_token, od.item_name, od.item_quantity, od.item_price ,(select count(1) from DT_OT_ORDER_DETAILS od1 where od1.order_id = od.order_id) as order_detail_count  from DT_OT_ORDER_DETAILS od, DT_OT_ORDER_TOKENS ot,DT_RT_ORDER_STATUS os,DT_OT_ORDER_CUSTOMER oc 


where od.order_id = ot.order_id 

and 

oc.order_status_id = os.status_id
and oc.order_id = od.order_id";
}
 if(!empty($order_id) && empty($merchant_id)){
//$qry = "SELECT  od.order_id, ot.order_token, od.item_name, od.item_quantity, od.item_price, (select count(1) from DT_OT_ORDER_DETAILS od1 where od1.order_id = od.order_id) as order_detail_count  from DT_OT_ORDER_DETAILS od, DT_OT_ORDER_TOKENS ot  where od.order_id = ". $order_id ." and od.order_id = ot.order_id";
 
 $qry = "SELECT  od.order_id,os.status,oc.order_status_id, ot.order_token, od.item_name, od.item_quantity, od.item_price, (select count(1) from DT_OT_ORDER_DETAILS od1 where od1.order_id = od.order_id) as order_detail_count  from DT_OT_ORDER_DETAILS od, DT_OT_ORDER_TOKENS ot,DT_OT_ORDER_CUSTOMER oc,DT_RT_ORDER_STATUS os
where od.order_id = ". $order_id ." 
and od.order_id = ot.order_id
and
oc.order_status_id = os.status_id
and oc.order_id = od.order_id";
 
 };
 $json = array();
 $json_scalar = array();
	$result = mysql_query($qry); 
	
	$json = array();
	while($row = mysql_fetch_array($result,MYSQL_ASSOC)) 
	{
	$bus = array(
'ORDER_ID' => $row['order_id'],
//'ORDER_TOKEN' => $row['order_token'],
'ITEM_NAME' => $row['item_name'],
'ITEM_QUANTITY' => $row['item_quantity'], 
'ITEM_PRICE' => $row['item_price']
);
 $bus_scalar = array(
 'ORDER_ID' => $row['order_id'],
'ORDER_TOKEN' => $row['order_token'],
'STATUS' => $row['status'],
'ORDER_DETAIL_COUNT' => $row['order_detail_count']
);
   array_push($json, $bus);
   array_push($json_scalar, $bus_scalar);
  
}
//print_r(array_unique($json_scalar));
$groups = _group_by($json,'ORDER_ID', $json_scalar, 'ORDER_ID') ;

deliver_response($groups); 
mysql_close();

function _group_by($array, $key,$array_scalar, $grouped_key) {
    $return = array();
    $return_scalar = array();
    foreach($array_scalar as $sval){
    $return [$sval[$key]] = $sval ;
    }
    foreach($array as $val) {
    	$return[$val[$key]][] = $val;
   	}
    return $return; 
   
}	 
	 
function deliver_response($arr)
{
$response['OrderDetails']=$arr;
$json_response=json_encode($response, JSON_PRETTY_PRINT|JSON_NUMERIC_CHECK);
echo $json_response;
} 
?>
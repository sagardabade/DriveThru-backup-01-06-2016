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
// $qry = "SELECT  od.order_id, ot.order_token, od.item_name, od.item_quantity, od.item_price from DT_OT_ORDER_DETAILS od, DT_OT_ORDER_TOKENS ot where od.order_id = ot.order_id ";
 $qry ="SELECT DISTINCT od.order_id,os.status,oc.order_status_id, ot.order_token, od.item_name, od.item_quantity, od.item_price from DT_OT_ORDER_DETAILS od, DT_OT_ORDER_TOKENS ot,DT_RT_ORDER_STATUS os,DT_OT_ORDER_CUSTOMER oc where od.order_id = ot.order_id and oc.order_status_id = os.status_id";
 
}
 if(!empty($order_id) && empty($merchant_id)){
 $qry = "SELECT DISTINCT od.order_id,os.status,oc.order_status_id, ot.order_token, od.item_name, od.item_quantity, od.item_price from DT_OT_ORDER_DETAILS od, DT_OT_ORDER_TOKENS ot,DT_RT_ORDER_STATUS os,DT_OT_ORDER_CUSTOMER oc  where od.order_id = ". $order_id ." and od.order_id = ot.order_id and oc.order_status_id = os.status_id";
 };
 $json = array();
$result = mysql_query($qry); 
$json = array();
while($row = mysql_fetch_array($result,MYSQL_ASSOC)) 
{
$bus = array(
'ORDER_ID' => $row['order_id'],
'ORDER_TOKEN' => $row['order_token'],
'ITEM_NAME' => $row['item_name'],
'ITEM_QUANTITY' => $row['item_quantity'], 
'ITEM_PRICE' => $row['item_price'],
'STATUS' => $row['status']
);
 
   array_push($json, $bus);
}
$groups = _group_by($json,'ORDER_ID','ORDER_ID' ) ;


//$jsonstring = deliver_response($json);
deliver_response($groups); 
mysql_close();

function _group_by($array, $key, $grouped_key) {
    $return = array();
    foreach($array as $val) {
    $return[ $grouped_key .":". $val[$val]][] = $val;
       // $return[$val[$key]][] = $val;
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
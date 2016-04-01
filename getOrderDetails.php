<?php
 
   header('Content-Type: application/json; charset=utf-8');
   
   include("DB_config.php");
   connectDB();
   
   $consumer_id = $_GET['consumer_id'];
   $merchant_id = $_GET['merchant_id'];
   $order_id = $_GET['order_id'];   
    //$qry = "SELECT order_id, item_name, item_quantity from DT_OT_ORDER_DETAIL";
if (empty($order_id)){
 $qry = "SELECT  od.order_id, ot.order_token, od.item_name, od.item_quantity, od.item_price from DT_OT_ORDER_DETAILS od, DT_OT_ORDER_TOKENS ot where od.order_id = ot.order_id ";	
 }
 else{
 $qry = "SELECT  od.order_id, ot.order_token, od.item_name, od.item_quantity, od.item_price from DT_OT_ORDER_DETAILS od, DT_OT_ORDER_TOKENS ot  where od.order_id = ". $order_id ." and od.order_id = ot.order_id";
 }
	//$json = array();
	$result = mysql_query($qry); 
	
	$json = array();
	$json1 = array();
	$idvar = -1;
	$first = true;
	while($row = mysql_fetch_array($result,MYSQL_ASSOC)) 
	{
	
	$bus = array(
		//'ORDER_ID' => $row['order_id'],
		'ORDER_ID' => $row['order_id'],
		'ORDER_TOKEN' => $row['order_token'],
		//array(
		'ITEM_NAME' => $row['item_name'], //$row['ietm_name'],
		'ITEM_QUANTITY' => $row['item_quantity'], // $row['item_quantity']
		'ITEM_PRICE' => $row['item_price']
		//)
 );
 if($first)
 {
 $first = false;
 array_push($json, $bus);
 $idvar = intval($row['order_id']);
 continue;
 }
 if($idvar == $row['order_id'])
 {
    array_push($json, $bus);
    }
    else
    {
   // $b = array(intval($idvar) => $json);
   // array_push($json1, $b);
  //  array_push($json1, $json);
    $json1[intval($idvar)] = $json;
   // $json1=json_encode($json1, JSON_PRETTY_PRINT|JSON_NUMERIC_CHECK);
    $json = array();
    }
    $idvar = intval($row['order_id']);
}
$json1[intval($idvar)] = $json;
//array_push($json1, $json);
//$jsonstring = deliver_response($json);
deliver_response($json1); 
mysql_close();

	 
	 
function deliver_response($arr)
{
$response['OrderDetails']=$arr;
$json_response=json_encode($response, JSON_PRETTY_PRINT|JSON_NUMERIC_CHECK);
echo $json_response;
} 
?>
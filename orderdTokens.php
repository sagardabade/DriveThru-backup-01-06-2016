<?php
header("Access-Control-Allow-Origin: *");
//header("Content-Type:application/json");
   
//connect to database
  $connection = mysqli_connect('localhost', 'sqweezy3_tru', 'Nanite12#', 'sqweezy3_DriveThru');
    
function deliver_response($arr)
{
header("HTTP/1.1 ");

$t['drive_thru'] = $arr;

$json_response=json_encode($t);

echo $json_response;

}
 
 ?>
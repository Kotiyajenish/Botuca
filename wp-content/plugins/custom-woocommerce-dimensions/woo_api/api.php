<?php
header("Content-Type:application/json");
if (isset($_GET['id']) && $_GET['id']!="") {
	include('db.php');
	$id = $_GET['id'];
	$result = mysqli_query(
	$con,
	"SELECT * FROM 'wp_api_data' WHERE id=$id");
	if(mysqli_num_rows($result)>0){
	$row = mysqli_fetch_array($result);
	$key = $row['woo_key'];
	$sec_key = $row['secret_key'];
	$url = $row['url'];
	response($id, $key, $sec_key,$url);
	mysqli_close($con);
	}else{
		response(NULL, NULL, 200,"No Record Found");
		}
}else{
	response(NULL, NULL, 400,"Invalid Request");
	}

function response($id,$key,$sec_key,$url){
	$response['id'] = $id;
	$response['key'] = $key;
	$response['secret_key'] = $sec_key;
	$response['url'] = $url;
	
	$json_response = json_encode($response);
	echo $json_response;
}
?>
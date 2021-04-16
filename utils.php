<?php
$_PUT = [];
/**
 * Used to parse put request parameters
 */
function parse_put(){
	global $_PUT;
	$_PUT = [];
	$body = file_get_contents("php://input");
	$tmp = explode("&", $body);
	foreach($tmp as $field){
		$tmp2 = explode("=", $field);
		$_PUT[$tmp2[0]] = $tmp2[1];
	}
}

function parse_body(): array| false{
	$body = file_get_contents("php://input");
	try{
		return json_decode($body, true, flags: JSON_THROW_ON_ERROR);
	}catch(JsonException){
		return false;
	}
}

/**
 * Create a JSON format response with HTTP Code, message and eventually content
 * @param int $status HTTP Code to return
 * @param string $message Message to join with status code
 * @param array|null $return Content to return
 */
function response(int $status, string $message, array|null $return = null){
	header("Content-Type: application/json");
	$response = [
		'status' => [
			'code' => $status,
			'message' => $message
		]
	];
	if($return !== null){
		$response['content'] = $return;
	}
	http_response_code($status);
	echo json_encode($response);
	exit();
}
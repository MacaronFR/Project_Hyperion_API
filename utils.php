<?php
$_PUT = [];
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
<?php
/*
Mock key-value store for extra data
*/
	const 
		hash_algo = 'sha256',
		data_dir = 'data';
	
	if (!isset($_GET['key'])) {
		header('400 Bad request', true, 400);
		echo 'A key parameter needed';
		exit;
	}
	else
		$key = $_GET['key'];

	
	switch ($_SERVER["REQUEST_METHOD"]) {
		case 'GET' :
			try {
				$v = getValue($key);
				$vDecoded = json_decode($v);
				if ( json_last_error() === JSON_ERROR_NONE )
					header('Content-Type: application/json; charset=utf-8');
				echo $v;
			}
			catch (Exception $e) {
				header('204 No Content', true, 204);
				exit;
			}
			
			break;
		case 'POST' :
			$value = file_get_contents('php://input');
			echo saveValue($key, $value);
			break;
		default :
			header('405 Method Not Allowed', true, 405);
			echo 'The store supports GET and POST methods only';
			exit;
	};

function getValue($key){
	$fileName = data_dir.'/'.hash(hash_algo, $key);
	if ( file_exists($fileName) )
		$value = file_get_contents($fileName);
	else 
		throw new Exception('Key "'.$key.'" not found');
	
	return $value;
}

function saveValue($key, $value){
	$fileName = hash(hash_algo, $key);
	$fullFileName = data_dir.'/'.$fileName;
	
	file_put_contents($fullFileName, $value);
	return $fileName;
}
?>
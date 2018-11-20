<?php

$config = require_once(__DIR__ . '/../config/params.php');




if(!is_array($config) || !isset($config['SoapApi']) || !isset($config['SoapApi']['api_credentials']) || !is_array($config['SoapApi']['api_credentials'])){
	header('HTTP/1.1 403 Forbidden');
	exit;
}else{
	$api_credentials = $config['SoapApi']['api_credentials'];
}

if (!isset($_SERVER['PHP_AUTH_USER'])) {
	
	header('WWW-Authenticate: Basic realm="Soap API"');
	header('HTTP/1.1 401 Unauthorized');
	exit;

} else {

	$username = $_SERVER['PHP_AUTH_USER'];
	$password = $_SERVER['PHP_AUTH_PW'];
	

	if (!array_key_exists($username, $api_credentials)) {
		header('HTTP/1.1 403 Forbidden');
		exit;
	}

	if ($password != $api_credentials[$username]) {
		header('HTTP/1.1 403 Forbidden');
		exit;
	}

	header("Content-type: text/xml; charset='utf-8'");
	require_once("wsdl.xml");
	exit;
}

?>
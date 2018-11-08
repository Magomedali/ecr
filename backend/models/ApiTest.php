<?php

namespace backend\models;

use SoapClient;

class ApiTest{


	public function sendBrigades(){
		print_r("sendBrigades");

		$option = ['trace'=>1];
		header('Cache-Control: no-store, no-cache');
		ini_set("soap.wsdl_cache_enabled", "0");
		$wsdl_url = "http://localhost:8082/ecr/api/soap/web/wsdl.xml";
		$client = new SoapClient($wsdl_url);
		

		exit;
	}

}
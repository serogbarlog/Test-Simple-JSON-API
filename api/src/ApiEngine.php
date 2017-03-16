<?php

class ApiEngine
{
	private $_jsonBuilder;
	private $_method;
	private $_methodName;
	private $_params;

	function __construct($method, $params) {
		$this->_methodName = $method;
		$this->_params = $params;
	}

	public function processApi() 
	{
		$this->_loadClasses();
		$this->_jsonBuilder = new JsonBuilder();
		try 
		{
			if(!is_file(API_ROOT_DIR."src/method/".$this->_methodName.".php")) 
			{
				throw new Exception("Unsupported method ".API_ROOT_DIR."src/method/".$this->_methodName.".php");
			}
			$this->_method = require_once(API_ROOT_DIR."src/method/".$this->_methodName.".php");

			$res = $this->_method->process($this->_params);

			echo( $this->_jsonBuilder->buildJson($res) );
			die();
		} catch(Exception $e) {
			$this->responseError($e->getMessage());
		}
	}

	private function _loadClasses() {
		require_once(API_ROOT_DIR."src/lib/JsonBuilder.php");
	}

	private function responseError($message = "Unexpected error") 
	{
		echo( $this->_jsonBuilder->buildJson( array( "status" => "error", "message" => $message ) ) );
		die();
	}
}

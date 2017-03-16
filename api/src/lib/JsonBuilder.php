<?php

class JsonBuilder
{
	private $jsonConfig;
	private $data = [];

	function __construct()
	{
		$this->jsonConfig = require_once(API_ROOT_DIR."src/config/jsonConfig.php");
		$this->data = $this->jsonConfig["data"];
	}

	public function buildJson(Array $opt_array) 
	{
		foreach ($opt_array as $key => $value) 
		{
			if( array_key_exists($key, $this->jsonConfig["data"]) && !empty($value) ) 
			{
				$this->data[$key] = $value;
			}
		}
		
		return json_encode($this->data, $this->jsonConfig["opts"]);
	}

}

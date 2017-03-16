<?php

abstract class AbstractMethod
{
	protected $_db;

	public abstract function process($params);

	protected function init() 
	{
		$config = require(API_ROOT_DIR."src/config/dbConfig.php");
		$this->_db = mysqli_connect($config['host'], $config['user'], $config['pass'], $config['db'], $config['port'], $config['socket']);
		
		if ($this->_db->connect_errno) 
		{
			throw new Exception("Database connection error");
		}
	}

	protected function _query($query) {
		$res = $this->_db->query($query);
		// if(!$res) return null;
		return mysqli_fetch_assoc($res);
	}

	protected function _queryAll($query) {
		$res = $this->_db->query($query);
		// if(!$res) return null;
		return mysqli_fetch_all($res, MYSQLI_ASSOC);
	}

}

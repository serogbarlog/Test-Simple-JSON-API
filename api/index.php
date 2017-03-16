<?php

header('Content-type: text/html; charset=UTF-8');

if($_SERVER['REQUEST_METHOD'] != 'POST') die("Incompatible request method");

if(!isset($_REQUEST['route']) || empty($_REQUEST['route'])) die("Unexpected error occured");

define('API_ROOT_DIR', dirname(__FILE__)."/");

require_once(API_ROOT_DIR."src/ApiEngine.php");

$api = new ApiEngine($_REQUEST['route'], $_POST);
$api->processApi();

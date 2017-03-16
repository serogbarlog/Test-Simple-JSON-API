<?php

require_once(API_ROOT_DIR."src/method/AbstractMethod.php");

class PostNews extends AbstractMethod
{
	public function process($params)
	{
		if(!isset($params["userEmail"]) || !isset($params["newsTitle"]) || !isset($params["newsMessage"]) || 
			empty($params["userEmail"]) || empty($params["newsTitle"]) || empty($params["newsMessage"])) 
		{
			throw new Exception("Insufficient parameters");
		}

		$this->init();

		$userEmail = $this->_db->real_escape_string($params["userEmail"]);
		$newsTitle = $this->_db->real_escape_string($params["newsTitle"]);
		$newsMessage = $this->_db->real_escape_string($params["newsMessage"]);

		$user = $this->_query("SELECT `ID` FROM `participant` WHERE `Email`='".$userEmail."'");
		if(!$user) 
		{
			throw new Exception("User not found");
		}

		$result = $this->_query("SELECT * FROM `news` WHERE `ParticipantId`='".$user['ID']."' AND `NewsTitle`='".
			$newsTitle."' AND `NewsMessage`='".$newsMessage."'");
		if($result) 
		{
			throw new Exception("News already posted");
		}

		try 
		{
			$result = $this->_db->query("INSERT INTO `news` (`ParticipantId`,`NewsTitle`,`NewsMessage`) VALUES ('".
				$user['ID']."','".$newsTitle."','".$newsMessage."')");
			if (!$result) 
			{
				throw new Exception("Database error");
			}
			
			return array("status" => "ok", "payload" => [], "message" => "Спасибо, ваша новость сохранена!");
		} 
		catch(Exception $e) 
		{
			throw new Exception("Database error");
		}
	}
}

return new PostNews();

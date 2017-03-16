<?php

require_once(API_ROOT_DIR."src/method/AbstractMethod.php");

class SessionSubscribe extends AbstractMethod
{
	public function process($params)
	{
		if(!isset($params["sessionId"]) || !isset($params["userEmail"]) || empty($params["sessionId"])  || empty($params["userEmail"])) 
		{
			throw new Exception("Insufficient parameters");
		}

		$this->init();

		$sessionId = $this->_db->real_escape_string($params["sessionId"]);
		$userEmail = $this->_db->real_escape_string($params["userEmail"]);

		$user = $this->_query("SELECT `ID` FROM `participant` WHERE `Email`='".$userEmail."'");
		if(!$user) 
		{
			throw new Exception("User not found");
		}

		$session = $this->_query("SELECT * FROM `session` WHERE `ID`='".$sessionId."' AND `TimeOfEvent` > NOW()");
		if(!$session) 
		{
			throw new Exception("Session expired or incorrect");
		}

		$registered = $this->_query("SELECT * FROM `sessionsubscribe` WHERE `SessionID`=".$sessionId." AND `ParticipantId`=".$user['ID']);
		if($registered)
		{
			return array("status" => "ok", "payload" => [], "message" => "Вы уже зарегистрированы");
		}

		$sessionCapacity = $this->_query("SELECT `SessionCapacity` FROM `sessioncapacity` AS `sc` INNER JOIN `session` AS `s` ON s.`ID`=sc.`SessionID` WHERE `SessionID`='".$sessionId."' AND s.`TimeOfEvent` > NOW()");

		$sessionOccupancy = $this->_query("SELECT COUNT(*) AS `occupancy` FROM `sessionsubscribe` AS `ss` INNER JOIN `session` AS `s` ON s.`ID`=ss.`SessionID` WHERE ss.`SessionID`='".$sessionId."' AND s.`TimeOfEvent` > NOW()");
		
		if($sessionCapacity <= $sessionOccupancy)
		{
			return array("status" => "ok", "payload" => [], "message" => "Извините, все места заняты");
		}

		try 
		{
			$result = $this->_db->query("INSERT INTO `sessionsubscribe` (`SessionID`,`ParticipantId`) VALUES ('".$sessionId."','".$user['ID']."')");
			if (!$result) 
			{
				throw new Exception("Database error");
			}
			
			return array("status" => "ok", "payload" => [], "message" => "Спасибо, вы успешно записаны!");
		} 
		catch(Exception $e) 
		{
			throw new Exception("Database error");
		}
	}
}

return new SessionSubscribe();

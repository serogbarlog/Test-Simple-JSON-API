<?php

require_once(API_ROOT_DIR."src/method/AbstractMethod.php");

class Table extends AbstractMethod
{
	public function process($params)
	{
		if(!isset($params["table"]) || empty(($params["table"]))) 
		{
			throw new Exception("Insufficient parameters");
		}

		$this->init();

		$table = $this->_db->real_escape_string($params["table"]);

		$isSetID = isset($params["id"]) && !empty($params["id"]);
		$isSetIDCond = ($isSetID) ? " AND `ID`=".intval($params['id']) : "";

		try 
		{
			$query = "SELECT * FROM `".$table."`";
			$query .= " WHERE 1";
			$query .= $isSetIDCond;
			$query .= ($table == "Session") ? " AND TimeOfEvent > NOW()" : "";
			$query .= " ORDER BY `ID` ASC";

			// $res = $this->_db->query($query);
			// $result = mysqli_fetch_all($res, MYSQLI_ASSOC);
			$result = $this->_queryAll($query);

			if($result && $table == "Session") 
			{
				$sesQuery = "SELECT s.ID, s.Name FROM speaker s";
				$sesQuery .= " LEFT JOIN sessionspeaker ss ON ss.SpeakerID = s.ID";
				$sesQuery .= " WHERE ss.SessionID = %ID%";
				$sesQuery .= " ORDER BY s.ID ASC";

				foreach ($result as $key => $value) 
				{
					// $sesRes = $this->_db->query( str_replace("%ID%", $result[$key]['ID'], $sesQuery) );
					// $sesResult = mysqli_fetch_all($sesRes, MYSQLI_ASSOC);
					$sesResult = $this->_queryAll( str_replace("%ID%", $result[$key]['ID'], $sesQuery) );
					$result[$key]['Speakers'] = $sesResult;
				}
			}

			return array("status" => "ok", "payload" => $result);

		} catch(Exception $e) 
		{
			throw new Exception("Database error");
		}
	}
}

return new Table();

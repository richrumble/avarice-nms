<?php

header("Content-Type: application/json");

// Always use UTC
date_default_timezone_set('UTC');

// Get the run time and calculalte the minute of the day
$runTimeEpoch = date('U');

// Load config
$config = json_decode(file_get_contents('../config.json'), TRUE);

// Consolidate request data
$formData = array_merge($_GET, $_POST);

// Error function
function ajaxError($type, $message)
{
	print "{";
	print "	\"result\": \"Error\",";
	print "	\"errorType\": \"" . $type . "\",";
	print "	\"errorMessage\": \"" . $message . "\"";
	print "}";
	exit();
}

// Data return function
function dataReturn($section, $action, $data)
{
	print "{";
	print "	\"result\": \"Success\",";
	print " \"section\": \"" . $section . "\",";
	print " \"action\": \"" . $action . "\",";
	print " \"data\": " . json_encode($data);
	print "}";
}

// Connect to avariceDB
try
{
	$avarice_dbh = new PDO("sqlite:" . $config['avariceDataFile']['path']);
}
catch(PDOException $e)
{
	ajaxError("Database", $e->getMessage());
}

// Creates module DB connection based on 'section' parametor
if (empty($formData['section']))
{
	ajaxError("Syntax", "'section' is a required parameter");
}
else if ($formData['section'] == "eventLog")
{
	try
	{
		$dbh = new PDO("sqlite:" . $config['module']['eventLog']['path'] . "/eventLog.sqlite3");
	}
	catch(PDOException $e)
	{
		ajaxError("Database", $e->getMessage());
	};
}

// Actions
$return = array();
if ($formData['section'] == "eventLog")
{
	if ($formData['action'] == "getUser")
	{
		$query = "
			select
				UserID,
				User
			from user
			order by
				User;";
		$sth = $dbh->prepare($query);
		$sth->execute();
		$result = $sth->fetchAll(PDO::FETCH_ASSOC);
		$return[1410065407] = "All";
		foreach($result as $row)
		{
			$return[$row['UserID']] = $row['User'];
		}
		dataReturn($formData['section'], $formData['action'], $return);
	}
	else if ($formData['action'] == "getEventLog")
	{
		$query = "
			select
				eventLogID,
				eventLog
			from eventLog
			order by
				eventLog;";
		$sth = $dbh->prepare($query);
		$sth->execute();
		$result = $sth->fetchAll(PDO::FETCH_ASSOC);
		$return[1410065407] = "All";
		foreach($result as $row)
		{
			$return[$row['eventLogID']] = $row['eventLog'];
		}
		dataReturn($formData['section'], $formData['action'], $return);
	}
	else if ($formData['action'] == "getSource")
	{
		$query = "
			select
				sourceID,
				source
			from source
			order by
				source;";
		$sth = $dbh->prepare($query);
		$sth->execute();
		$result = $sth->fetchAll(PDO::FETCH_ASSOC);
		$return[1410065407] = "All";
		foreach($result as $row)
		{
			$return[$row['sourceID']] = $row['source'];
		}
		dataReturn($formData['section'], $formData['action'], $return);
	}
	else if ($formData['action'] == "logSearch")
	{
		$query = "
			select
				e.ComputerName,
				el.eventLog,
				s.source,
				u.user,
				e.TimeWritten,
				e.Type,
				i.InsertionString,
				m.messageTemplate
			from events e
			join eventLog el
				on el.eventLogID = e.eventLogID
			join source s
				on s.sourceID = e.sourceID
			join user u
				on u.userID = e.userID
			join InsertionString i
				on i.InsertionStringID = e.InsertionStringID
			join eventLogSourceFile elsf
				on elsf.eventLogID = e.eventLogID
				and elsf.sourceID = e.sourceID
			join message m
				on m.messageFileID = elsf.messageFileID
				and m.identifier = e.EventIdentifier
			where 1=1";
		if (!empty($formData['userID']) and !in_array("1410065407", $formData['userID']))
		{
			$query .= "
				and e.userID in
				(";
			foreach($formData['userID'] as $userID)
			{
				$query .= "
					" . $userID . ",";
			}
			$query = substr($query, 0, -1) . "
				)";
		}
		if (!empty($formData['eventLogID']) and !in_array("1410065407", $formData['eventLogID']))
		{
			$query .= "
				and e.eventLogID in
				(";
			foreach($formData['eventLogID'] as $eventLogID)
			{
				$query .= "
					" . $eventLogID . ",";
			}
			$query = substr($query, 0, -1) . "
				)";
		}
		if (!empty($formData['sourceID']) and !in_array("1410065407", $formData['sourceID']))
		{
			$query .= "
				and e.sourceID in
				(";
			foreach($formData['sourceID'] as $sourceID)
			{
				$query .= "
					" . $sourceID . ",";
			}
			$query = substr($query, 0, -1) . "
				)";
		}
		if (!empty($formData['searchString']))
		{
			$query .= "
				and
				(
					i.InsertionString like '%" . $formData['searchString'] . "%'
					or m.messageTemplate like '%" . $formData['searchString'] . "%'
				)";
		}
		$query .= "
			limit 2000";
		if (!empty($formData['offset']))
		{
			$query .="
			offset " . ($formData['offset'] * 2000);
		}
		$sth = $dbh->prepare($query);
		$sth->execute();
		$result = $sth->fetchAll(PDO::FETCH_ASSOC);
		foreach($result as $row)
		{
			$iStrings = explode(",", $row['InsertionString']);
			$keys = array();
			for ($i = 1; $i <= count($iStrings); $i++)
			{
				$keys[] = "%" . $i;
			}
			$message = str_replace($keys, $iStrings, $row['messageTemplate']);
			$delimiters = array
			(
				'%n' => "<br />",
				'%t' => "&nbsp;&nbsp;&nbsp;&nbsp;"
			);
			$message = str_replace(array_keys($delimiters), $delimiters, $message);
			$return[] = array(
				$row['ComputerName'],
				$row['eventLog'],
				$row['source'],
				$row['User'],
				$row['TimeWritten'],
				$row['Type'],
				$message
			);
		}
		dataReturn($formData['section'], $formData['action'], $return);
	}
}

?>
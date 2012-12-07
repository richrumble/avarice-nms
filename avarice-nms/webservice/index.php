<?php
# Merge all GET and POST variables into request variable
$req = array_merge($_GET, $_POST);
# Sets default output format
if (!isset($req['format']))
{
	$req['format'] = 'json';
}
# Sets header depending on format
if (strtolower($req['format']) == 'xml')
{
	header("Content-type: text/xml; charset=utf-8");
}

# include datasource connection functions
include_once('./datasource.php');

# function to convert PHP array to XML
function array_to_xml(array $arr, SimpleXMLElement $xml)
{
    foreach ($arr as $k => $v) {
        is_array($v)
            ? array_to_xml($v, $xml->addChild($k))
            : $xml->addChild($k, $v);
    }
    return $xml;
}

# function to verify required parameters are passed
function requiredParams ($array)
{
	global $req;
	$return = array
	(
		"paramsPassed"  => TRUE,
		"missingParams" => array()
	);
	foreach ($array as $requiredParam)
	{
		if (!isset($req[$requiredParam]))
		{
			$return['paramsPassed']    = FALSE;
			$return['missingParams'][] = $requiredParam;
		}
	}
	if ($return['paramsPassed'] == TRUE)
	{
		return TRUE;
	}
	else
	{
		wsResponse($req['format'], "Error", $return);
		exit;
	}
}

# output depending on format requested
function wsResponse ($format, $status, $return)
{
	$response = array
	(
		"status"   => $status,
		"response" => $return
	);
	if (strtolower($format) == 'json')
	{
		print json_encode($response);
	}
	else if (strtolower($format) == 'xml')
	{
		print array_to_xml($response, new SimpleXMLElement('<root />'))->asXML();
	}
}

# defaults to error
if (!isset($req['action']))
{
	$return = array
	(
		"fault"       => "input error",
		"description" => "no action provided"
	);
	wsResponse($req['format'], "Error", $return);
}
# used to queue data file for consumption
else if ($req['action'] == 'fileQueue')
{
	# data being pushed is Windows Event Logs
	if ($req['dataType'] == 'winEventLogs')
	{
		$requiredParamsPresent = requiredParams(array("assetID", "fileName"));
		$query = "
			INSERT INTO `queue.windowsevent`
				(assetID, fileName, createdDate)
			VALUES
				(:assetID, :fileName, NOW())";
		$sqlParams = array
		(
			':assetID'  => $req['assetID'],
			':fileName' => $req['fileName']
		);
		$dbh = avariceDBConnect();
		$sth = $dbh->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$sth->execute($sqlParams);
		$insertedID = $dbh->lastInsertID();
		$return = array
		(
			"action" => $req['action'],
			"dataType" => $req['dataType'],
			"assetID" => $req['assetID'],
			"fileName" => $req['fileName'],
			"queueID" => $insertedID
		);
		wsResponse($req['format'], "Success", $return);
	}
}

?>
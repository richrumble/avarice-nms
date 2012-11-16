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

# defaults to error
if (!isset($req['action']))
{
	$return = array
	(
		"response" => array
		(
			"fault"       => "input error",
			"description" => "no action provided"
		)
	);
}
# used to push data to avarice DB
else if ($req['action'] == 'dataPush')
{
	# data being pushed is Windows Event Logs
	if ($req['dataType'] == 'winEventLogs')
	{
		
	}
}

# output depending on format requested
if (strtolower($req['format']) == 'json')
{
	print json_encode($return);
}
else if (strtolower($req['format']) == 'xml')
{
	print array_to_xml($return, new SimpleXMLElement('<root />'))->asXML();
}

?>
<?php

header("Content-Type: text/plain");
include_once("../../include/config.php");

function getID(
	$details, 
	$type,
	$types = array(
		"asset" => array(
			"table"      => "log__asset_data",
			"identifier" => "assetID"
		),
		"category" => array(
			"table"      => "log__event_categories",
			"identifier" => "categoryID"
		),
		"event template" => array(
			"table"      => "log__event_templates",
			"identifier" => "templateID"
		),
		"local group" => array(
			"table"      => "log__local_groups",
			"idnetifier" => "groupID"
		),
		"local user" => array(
			"table"      => "log__local_users",
			"identifier" => "userID"
		),
		"log file" => array(
			"table"      => "log__logfiles",
			"identifier" => "logFileID"
		),
		"source" => array(
			"table"      => "log__sources",
			"identifier" => "sourceID"
		),
		"time zone" => array(
			"table"      => "log__time_zones",
			"identifier" => "timeZoneID"
		),
		"type" => array(
			"table"      => "log__types",
			"identifier" => "typeID"
		)
	)
) {
	if (!empty($details) and in_array($type, $types)) {
		
	};
};


/*
    $query['query'] = "
                       SELECT hash_ID
                            , template
                         FROM inv__config_templates
                        WHERE inv__config_templates.os = ?
                          AND inv__config_templates.release = ?
                          AND inv__config_templates.version = ?";
    $query['params'] = array("sss",
                             $form_data['os'],
                             $form_data['release'],
                             $form_data['version']);
    $result = dbquery_func($avarice_user_connection, $query);
*/


if (!empty($form_data['action'])) {
	
};

?>
<?php
header("Content-Type: text/plain");
include_once("../../include/config.php");
include_once("inv_functions.php");
include_once("inv_config.php");

if (!empty($form_data['action'])) {
  $query = array();
  if ($form_data['action'] == "templatecheck") {
    $given_hash_id = getHashID($form_data['hash']);
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
    if ($given_hash_id != $result[0]['hash_ID']) {
      print $result[0]['template'];
    };
  } else if ($form_data['action'] == "submit_results") {
    $query['query'] = "
                       INSERT INTO inv__dataprocessing
                                   (createdDate, assetName, data)
                            VALUES (?, ?, ?);";
    $query['params'] = array("ssb",
                             substr($form_data['filename'], 0, 4) . "-" . substr($form_data['filename'], 4, 2) . "-" . substr($form_data['filename'], 6, 2),
                             substr($form_data['filename'], 8, -4),
                             $form_data['xml_result']);
    dbquery_func($avarice_admin_connection, $query);
  };
};
?>
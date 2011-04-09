<?php
header("Content-Type: text/plain");
include_once("../../include/config.php");
include_once("inv_functions.php");
include_once("inv_config.php");

if (!empty($form_data['action'])) {
  if ($form_data['action'] == "templatecheck") {
    $given_hash_id = getHashID($form_data['hash']);
    $query = "
              SELECT hash_ID
                   , template
                FROM inv__config_templates
               WHERE inv__config_templates.os = '" . $form_data['os'] . "'
                 AND inv__config_templates.release = '" . $form_data['release'] . "'
                 AND inv__config_templates.version = '" . $form_data['version'] . "'";
    $result = mysql_fetch_assoc(dbquery_func($avarice_user_connection, $query));
    if ($given_hash_id != $result['hash_ID']) {
      print $result['template'];
    };
  } else if ($form_data['action'] == "submit_results") {
    $query = "
              INSERT INTO inv__dataprocessing
                          (createdDate, assetName, data)
                   VALUES ('" . substr($form_data['filename'], 0, 4) . "-" . substr($form_data['filename'], 4, 2) . "-" . substr($form_data['filename'], 6, 2) . "', '" . substr($form_data['filename'], 8, -4) . "', '" . $form_data['xml_result'] . "');";
    dbquery_func($avarice_admin_connection, $query);
  };
};
?>
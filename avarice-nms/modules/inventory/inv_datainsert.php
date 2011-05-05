<?php

include_once("../../include/config.php");
include_once("inv_functions.php");
include_once("inv_config.php");

$query = "
          SELECT template
            FROM inv__config_templates";
$result = dbquery_func($avarice_user_connection, $query);
$tables_expected = array();
while ($row = mysql_fetch_assoc($result)) {
  $template_xml = simplexml_load_string($row['template']);
  $tables_expected['asset'] = array("type" => "single", "columns" => array());
  foreach($template_xml->asset->property as $property) {
    $tables_expected['asset']['columns'][] = (string)$property['name'];
  };
  foreach($template_xml->asset->category as $category) {
    $tables_expected[(string)$category['name']] = array("type" => (string)$category['type'], "columns" => array());
    if ((string)$category['type'] == "single") {
      foreach($category->property as $property) {
        if (!isset($property['type']) or (string)$property['type'] == "individual") {
          $tables_expected[(string)$category['name']]['columns'][] = (string)$property['name'];
        } else if ((string)$property['type'] == "general") {
          if (!in_array((string)$category['name'] . "_general", array_keys($tables_expected))) {
            $tables_expected[(string)$category['name'] . "_general"] = array("type" => "general", "columns" => array());
          };
          $tables_expected[(string)$category['name'] . "_general"]['columns'][] = (string)$property['name'];
        };
      };
    } else if ((string)$category['type'] == "multiple") {
      foreach($category->instance->property as $property) {
        if (!isset($property['type']) or (string)$property['type'] == "individual") {
          $tables_expected[(string)$category['name']]['columns'][] = (string)$property['name'];
        } else if ((string)$property['type'] == "general") {
          if(!in_array((string)$category['name'] . "_general", array_keys($tables_expected))) {
            $tables_expected[(string)$category['name'] . "_general"] = array("type" => "general", "columns" => array());
          };
          $tables_expected[(string)$category['name'] . "_general"]['columns'][] = (string)$property['name'];
        };
      };
    }
  };
};

print_r($tables_expected);

//$query = "
//            SELECT pkID
//                 , createdDate
//                 , assetName
//                 , data
//              FROM inv__dataprocessing
//             WHERE statusID = 1
//          ORDER BY pkID ASC
//             LIMIT 10";
//$result = dbquery_func($avarice_user_connection, $query);
//$data
//while ($row = mysql_fetch_assoc($result)) {
//
//};

?>
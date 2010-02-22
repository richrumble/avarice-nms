<?php
error_reporting(E_ALL);
define("BASE_DIR", substr(dirname(__FILE__), 0, strrpos(dirname(__FILE__), "\\")));
include_once(BASE_DIR . "/include/db_config.php");
$form_data = $_GET;
$form_data = array_merge($form_data, $_POST);
$config_results = dbquery_func($avarice_user_connection, "SELECT parameter, value FROM config");
while ($row = mysql_fetch_assoc($config_results)) {
  define("CONF_" . $row['parameter'], $row['value']);
};
function microtime_float() {
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
};
?>
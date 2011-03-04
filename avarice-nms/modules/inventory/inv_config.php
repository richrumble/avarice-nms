<?php

$query = "
          SELECT *
            FROM inv__config_server";
$result = dbquery_func($avarice_user_connection, $query);
while ($row = mysql_fetch_assoc($result)) {
  define("INV_CONF_" . strtoupper($row['parameter']), $row['value']);
};

?>
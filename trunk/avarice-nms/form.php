<?php
$form_data = $_POST;
function find ($string, $array = array ()) {
  foreach ($array as $key => $value) {
    unset ($array[$key]);
    if (strpos($value, $string) !== false) {
      $array[$key] = $value;
    };
  };
  return $array;
};
print "
       <form method=\"popst\" action=\"form.php\">
        Data Block:<br />
        <textarea name=\"datadump\">"; if (isset($form_data['datadump'])) { print $form_dta['datadump']; }; print "</textarea><br />
        Search for:<br />
        <input type=\"text\" name=\"item\""; if (isset($form_data['item'])) { print " value=\"" . $form_data['item'] . "\""; }; print " /><br />
        <input type=\"submit\" value=\"Search\" />
       </form>
       <p />
";
if (isset($form_data['datadump'], $form_data['item'])) {
  $datadump_array = explode("\n", $form_data['datadump']);
  $result_array = find($form_data['item'], $datadump_array);
  if (empty($result_array)) {
    print $form_data['item'] . " not found";
  } else {
    foreach ($result_array as $value) {
      print $value . "<br />";
    };
  };
};
?>
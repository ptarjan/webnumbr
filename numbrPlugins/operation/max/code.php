<?php
if (is_array($data)) {
  $num = NULL;
  if (isset($params['num']))
    $num = (int) $params['num'];
  else if (isset($params[0]))
    $num = (int) $params[0];

  // only do this is the num is numeric
  if (is_numeric($num)) {
    $newData = array();
    foreach ($data as $row) {
      $time = (float) $row[0];
      $val = (float) $row[1];
      if ($val <= $num) {
        $newData[] = (array($time, $val));
      }
    }

    $data = $newData;
  }
}

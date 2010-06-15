<?php
if (is_array($data)) {
  $points = NULL;
  if (isset($params['points']))
    $points = (int) $params['points'];
  else if (isset($params[0]))
    $points = (int) $params[0];
  if (!is_numeric($points))
    $points = 24;
  $points = (int) $points;

  $newData = array();
  $i = 0;
  $total = 0;
  foreach ($data as $row) {
    $time = (float) $row[0];
    $val = (float) $row[1];
    $total += $val;
    $i += 1;
    if ($i >= $points) {
      $newData[] = (array($time, $total));
      $i = 0;
      $total = 0;
    }
  }
  $data = $newData;
}

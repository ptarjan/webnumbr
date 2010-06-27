<?php
if (is_array($data)) {
  $range = NULL;
  if (isset($params['range']))
    $range = (int) $params['range'];
  else if (isset($params[0]))
    $range = (int) $params[0];
  if (!is_numeric($range))
    $range = 1;

  $max = 0;
  foreach ($data as $row) {
    $val = (float) $row[1];
    $max = max($max, $val);
  }

  $newData = array();
  foreach ($data as $row) {
    $time = (float) $row[0];
    $val = (float) $row[1];
    $val /= $max;
    $val *= $range;
    $newData[] = (array($time, $val));
  }

  $data = $newData;
}

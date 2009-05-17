<?php
if (is_array($data)) {
    $sum = NULL;
    if (isset($params['numtimes']))
        $sum = (int) $params['numtimes'];
    else if (isset($params[0]))
        $sum = (int) $params[0];
    if (!is_numeric($sum))
        $sum = 1;
    $sum = (int) $sum;

    for ($d = 0; $d < $sum; $d ++) {
        $newData = array();
        $last = 0;
        foreach ($data as $row) {
            $time = (float) $row[0];
            $val = (float) $row[1];
            $val = $val + $last;
            $last = $val;
            $newData[] = (array($time, $val));
        }
        $data = $newData;
    }
}

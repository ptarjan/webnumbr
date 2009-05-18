<?php
if (is_array($data)) {
    $derivative = NULL;
    if (isset($params['numtimes']))
        $derivative = (int) $params['numtimes'];
    else if (isset($params[0]))
        $derivative = (int) $params[0];
    if (!is_numeric($derivative))
        $derivative = 1;
    $derivative = (int) $derivative;
    for ($d = 0; $d < $derivative; $d ++) {
        $newData = array();
        $last = NULL;
        foreach ($data as $row) {
            $time = (float) $row[0];
            $val = (float) $row[1];
            $old = array($time, $val);
            if ($last === NULL) {
                $last = $old;
                continue;
            }
            // Discrete derivative (by the hours)
            $val = - ($val - $last[1]) / ($time - $last[0]) * 60 * 60;
            // Put the point directly in between the two values
            // $time -= ($time - $last[0]) / 2;
            $last = $old;
            $newData[] = (array($time, $val));
        }
        $data = $newData;
    }
}

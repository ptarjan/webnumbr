<?php
$first = TRUE;
foreach ($params as $param) {
    $numbr = new Numbr($param . ".json");
    $newData = $numbr->run();
    if ($newData !== null) {
        if ($first) {
            $first = FALSE;
            if ($c['name'] && $data)
                $data = array($c['name'] => $data);
            else
                $data = array();
        }
        $data[$numbr->c['name']] = json_decode($newData);
    }
}
?>

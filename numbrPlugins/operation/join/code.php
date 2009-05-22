<?php
$first = TRUE;
foreach ($params as $param) {
    $numbr = new Numbr($param . ".json");
    $newData = $numbr->run();
    if ($newData !== null) {
        if ($first) {
            $first = FALSE;
            $data = array($c['name'] => $data);
        }
        $data[$numbr->c['name']] = json_decode($newData);
    }
}
?>

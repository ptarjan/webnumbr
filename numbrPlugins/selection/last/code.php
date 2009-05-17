<?php
if (!isset($params['count'])) {
    if (count($params) == 1 && isset($params[0]))
        $count = (int) $params[0];
    else
        $count =1;
} else
    $count = (int) $params['count'];
$c['limit'] = $count;
$c['singleValue'] = FALSE;
?>

<?php
if (count($params) == 1 && isset($params[0]))
    $params['date'] = $params[0];

if (!is_numeric($params['date']) && strtotime($params['date']) !== FALSE)
    $params['date'] = strtotime($params['date']);
$c['sql']['where'][] = "UNIX_TIMESTAMP(timestamp) <= :todate";
$c['sql']['params']['todate'] = ($params['date']);
require("numbrPlugins/selection/all/code.php");

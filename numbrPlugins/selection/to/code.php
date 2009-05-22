<?php
if (count($params) == 1 && isset($params[0]))
    $params['date'] = $params[0];

if (!is_numeric($params['date']) && strtotime($params['date']) !== FALSE)
    $params['date'] = strtotime($params['date']);

if (!isset($params['date'])) $params['date'] = pow(2,31)-1;

$c['sql']['where'][] = "UNIX_TIMESTAMP(timestamp) <= :todate";
$c['sql']['params']['todate'] = ($params['date']);

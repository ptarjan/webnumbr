<?php
if (count($params) == 1 && isset($params[0]))
    $params['date'] = $params[0];

if (!is_numeric($params['date']) && strtotime($params['date']) !== FALSE)
    $params['date'] = strtotime($params['date']);

if (!isset($params['date'])) $params['date'] = 0;

$c['sql']['where'][] = "UNIX_TIMESTAMP(timestamp) = :exactdate";
$c['sql']['params']['exactdate'] = ($params['date']);
$c['sql']['params']['limit'] = 1;
$c['singleValue'] = TRUE;

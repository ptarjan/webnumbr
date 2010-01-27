<?php
if (count($params) == 1 && isset($params[0]))
    $params['number'] = $params[0];

if (!isset($params['number'])) $params['number'] = 0;

$c['sql']['where'][] = "data < :upperbound";
$c['sql']['params']['upperbound'] = ($params['number']);

<?php
// header("Content-Type: application/json");

//default
if (count($params) == 1 && isset($params[0]))
    $params = array('callback' => $params[0]);

$cb = $params['callback'];
if ($cb) {
    print $cb . "(" . json_encode($data) . ")";
} else {
    print json_encode($data);
}

<?php
if (!is_array($data) || !isset($data['data'])) {
    $data = array("data" => $data);
}
$data['debug'] = $c;

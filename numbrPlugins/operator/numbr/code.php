<?php
if (!is_array($data) || !isset($data['data'])) {
    $data = array("data" => $data);
}

$s = $PDO->prepare("SELECT * FROM numbrs WHERE name = :name");
$s->execute(array("name" => $c['name']));
$numbr = $s->fetchAll(PDO::FETCH_ASSOC);
$data['numbr'] = $numbr[0];

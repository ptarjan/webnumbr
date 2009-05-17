<?php
if (!isset($data['data'])) {
    $data = array("data" => $data);
}

$s = $PDO->prepare("SELECT * FROM numbrs WHERE name = :name");
$s->execute(array("name" => $c['name']));
$data['numbr'] = $s->fetchAll(PDO::FETCH_ASSOC);

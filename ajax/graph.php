<?php
require ("../db.inc");
header("Content-type: application/json");

function dieError($pdo) {
    die(json_encode(array("error" => array("msg" => "Prepare error", "errorInfo" => $pdo->errorInfo(), "errorCode" => $pdo->errorCode()))));
}

$stmt = $PDO->prepare("SELECT UNIX_TIMESTAMP(timestamp) AS ts, data FROM graph_data WHERE graph_id = :id ORDER BY timestamp");
if (!$stmt) dieError($PDO);
$ret = $stmt->execute(array("id" => $_REQUEST['id']));
if (!$ret) dieError($stmt);

$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
$data = array();
foreach ($result as $row) {
    $data[] = array($row['ts'], $row['data']);
}

$stmt = $PDO->prepare("SELECT * FROM graphs WHERE id = :id");
if (!$stmt) dieError($PDO);
$ret = $stmt->execute(array("id" => $_REQUEST['id']));
if (!$ret) dieError($stmt);
$graph = $stmt->fetchAll(PDO::FETCH_ASSOC);
$graph = $graph[0];

$ret = array(
    "request" => $_REQUEST,
    "graph" => $graph,
    "data" => $data,
);

if (isset($_REQUEST['callback'])) 
    print $_REQUEST['callback'] . "(" . json_encode($ret) . ")";
else
    print json_encode($ret);

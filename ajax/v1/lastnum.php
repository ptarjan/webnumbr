<?php
require ("../../db.inc");

// Don't let them be whitespace
foreach ($_REQUEST as $var => $_) 
    if (strlen(trim($_REQUEST[$var])) == 0) unset($_REQUEST[$var]);

function dieEncode($output) {
    switch ($_REQUEST['format']) {
        case "json":
            header("Content-type: application/json");
            if (isset($_REQUEST['callback'])) 
                print $_REQUEST['callback'] . "(" . json_encode($output) . ")";
            else
                print json_encode($output);
            break;
        case "text":
        case "txt" :
        case "raw" :
        default :
            print_r($output);
            break;
    }
    die();
}

function dieError($err) {
    if ($err instanceof PDO) {
        dieEncode(array("error" => 
        array(
            "msg" => "Prepare error", 
            "errorInfo" => $pdo->errorInfo(), 
            "errorCode" => $pdo->errorCode()
        )));
    } else {
        dieEncode(array("error" => $err));
    }
}

function isGood($str) {
    return isset($_REQUEST[$str]) && is_numeric($_REQUEST[$str]) && ((int) $_REQUEST[$str]) >= 0;
}

if (!isGood('id')) {
    dieError("Bad 'id' parameter");
}

if (!isset($_REQUEST['format'])) 
    $_REQUEST['format'] = "txt";

$stmt = $PDO->prepare("
SELECT
data

FROM graph_data WHERE

graph_id = :id

ORDER BY timestamp DESC LIMIT 1

");
if (!$stmt) dieError($PDO);
$ret = $stmt->execute(array("id" => $_REQUEST['id']));
if (!$ret) dieError($stmt);

$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (count($result) != 1) dieError("Didn't get 1 row returned");
if (! isset($result[0]['data'])) dieError("'data' not present");
dieEncode($result[0]['data']);


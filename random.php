<?php
require("db.inc");
$stmt = $PDO->prepare("
SELECT 
    id, 
    badFetches < 100 
    OR 
    (goodFetches / (goodFetches + badFetches)) > 0.25
    AS fetching 

FROM graphs HAVING fetching = TRUE ORDER BY rand() LIMIT 1"
);
$ret = $stmt->execute();
if (!$ret) {
    print_r($stmt->errorInfo());
}
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (count ($data) == 1) {
    $id = $data[0]['id'];
} else {
    $PDO->prepare("
SELECT 
    MAX(id) AS maxid,
    badFetches < 100 
    OR 
    (goodFetches / (goodFetches + badFetches)) > 0.25
    AS fetching 
FROM graphs HAVING fetching = TRUE
");
$ret = $stmt->execute();
if (!$ret) {
    print_r($ret->errorInfo());
}
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (count ($data) == 1) {
    $id = my_rand(1, $data['maxid']);
} else {
    print "Some error happending finding a random graph. Please contact me and I'll have it working in a jiffy";
}
}

header("Location: graph?id=$id");

?>

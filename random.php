<?php
require("db.inc");
$stmt = $PDO->prepare("
SELECT name FROM numbrs WHERE is_fetching = TRUE ORDER BY rand() LIMIT 1
");
$ret = $stmt->execute();
if (!$ret) {
    print_r($stmt->errorInfo());
}
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (count ($data) == 1) {
    $name = $data[0]['name'];
} else {
    print "Some error is happending finding a random graph. Please contact me and I'll have it working in a jiffy";
}

header("Location: $name");

?>

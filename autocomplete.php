<?php
$name = $_REQUEST['q'];
require ("db.inc");
$s = $PDO->prepare("SELECT name FROM numbrs WHERE name LIKE CONCAT(:name,'%') LIMIT 10");
$s->execute(array("name"=>$_REQUEST['q']));
$results = $s->fetchAll(PDO::FETCH_NUM);
foreach ($results as $r) {
    print "{$r[0]}\n";
}

<?php
require_once("db.inc");

$url = $_REQUEST['url'];
$s = $PDO->prepare("SELECT name, url, title FROM numbrs WHERE url LIKE :url ORDER BY createdTime DESC");
$s->execute(array('url' => '%'.$url.'%'));
$r = $s->fetchAll(PDO::FETCH_ASSOC);
print_r(json_encode($r));

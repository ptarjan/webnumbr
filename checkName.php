<?php
require_once("db.inc");

$name = $_REQUEST['name'];

if (trim($name) == "")
    print("Your name is kind of empty.");
else if (! preg_match("/^[a-z0-9-]*$/", $name)) {
    print("Only a-z and 0-9 and '-' are allowed");
} else if (strlen($name) > 63) {
    print("Up to 63 characters are allowed");
} else {

$s = $PDO->prepare("SELECT COUNT(*) as count FROM numbrs WHERE name=:name");
$s->execute(array("name" => $name));
$r = $s->fetchAll();
if ($r[0]['count'] != 0) {
    print("Name is already taken");
}
}

<?php
require_once("db.inc");
$reserved = scandir(".");
foreach ($reserved as $r) {
    list($n, $ext) = explode(".", $r, 2);
    $reserved[] = $n;
    $reserved[] = strtolower($n);
    $reserved[] = strtolower($r);
}
$reserved = array_merge($reserved, array(
"login", "register", "edit", "create", "make", "delete", "update", "save",
));

$name = $_REQUEST['name'];

if (trim($name) == "")
    print("Your name is kind of empty.");
else if (! preg_match("/^[a-z0-9-]*$/", $name)) {
    print("Only a-z and 0-9 and '-' are allowed");
} else if (strlen($name) > 63) {
    print("Up to 63 characters are allowed");
} else if (strlen($name) < 4) {
    print("Must be >= 4 chars. Get creative");
} else if (in_array($name, $reserved)) {
    print("That is a reserved name. Hands off");
} else {

$s = $PDO->prepare("SELECT COUNT(*) as count FROM numbrs WHERE name=:name");
$s->execute(array("name" => $name));
$r = $s->fetchAll();
if ($r[0]['count'] != 0) {
    print("Name is already taken");
}
}

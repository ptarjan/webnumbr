<?php

list($name, $op) = explode(".", $_REQUEST["name"]);

require "db.inc";

if (!isset($_REQUEST["format"])) $_REQUEST["format"] = "html";

switch ($_REQUEST["format"]) {
    case "json" :
    case "raw" :
    case "xml" :
    default :
        switch ($op) {
            case "all" :
                $s = $PDO->prepare("SELECT data FROM numbr_data WHERE numbr = :name ORDER BY timestamp DESC");
                break;
            case "" :
            default :
                $s = $PDO->prepare("SELECT data FROM numbr_data WHERE numbr = :name ORDER BY timestamp DESC LIMIT 1");
                break;
        }
        $s->execute(array("name" => $name));
        $data = $s->fetchAll(PDO::FETCH_ASSOC);
        print_r($data);
        break;
         
    case "html" :

        $s = $PDO->prepare("SELECT * FROM numbrs WHERE name = :name");
        $s->execute(array("name" => $name));
        $numbr = $s->fetchAll(PDO::FETCH_ASSOC);
        print_r($numbr);
?>
<?php
        break;
};
?>

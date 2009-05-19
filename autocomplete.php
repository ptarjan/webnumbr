<?php
$name = $_REQUEST['q'];

if (strpos($name, ".") === FALSE) {
    require ("db.inc");
    $s = $PDO->prepare("SELECT name FROM numbrs WHERE name LIKE CONCAT(:name,'%') LIMIT 10");
    $s->execute(array("name"=>$_REQUEST['q']));
    $results = $s->fetchAll(PDO::FETCH_NUM);
    foreach ($results as $r) {
        print "{$r[0]}\n";
    }
} else {
    $ops = explode(".", $_REQUEST['q']);
    $last = array_pop($ops);
    $plugins = array();
    $base = "numbrPlugins";
    foreach (scandir($base) as $dir) {
        if (strpos($dir, ".") === 0) continue;
        if (!is_dir("$base/$dir")) continue;
        foreach (scandir("$base/$dir") as $name) {
            if (strpos($name, ".") === 0) continue;
            if ($last == "" || strpos($name, $last) === 0) {
                print implode(".", $ops) . ".$name\n";
            }
        }
    }
}

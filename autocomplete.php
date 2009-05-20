<?php
$name = $_REQUEST['q'];

$r = array();

if (strpos($name, ".") === FALSE) {
    require ("db.inc");
    $s = $PDO->prepare("SELECT name FROM numbrs WHERE name LIKE CONCAT(:name,'%') LIMIT 10");
    $s->execute(array("name"=>$_REQUEST['q']));
    $results = $s->fetchAll(PDO::FETCH_NUM);
    foreach ($results as $result) {
        $r[] = "{$result[0]}";
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
            if ($name == "default") continue;
            if ($last == "" || strpos($name, $last) === 0) {
                $r[] =  implode(".", $ops) . ".$name";
                $params = @file_get_contents("$base/$dir/$name/params.txt");
                if (trim($params) != "")
                    $r[] =  implode(".", $ops) . ".$name(" . trim($params) . ")";
            }
        }
    }
}

sort($r);
if ($_REQUEST['format'] == "json") {
    if (isset($_REQUEST['callback'])) {
        print $_REQUEST['callback'] . "(" . json_encode($r) . ")";
    } else {
        print json_encode($r);
    }
} else {
    print implode("\n", $r);
}

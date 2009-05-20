<?php
preg_match("/[a-z0-9-]+/", $_REQUEST['name'], $matches);
$name = $matches[0];

$c = array();
$c['name'] = $name;
$c['ops'] = array();
$c['formats'] = array();
$c['selections'] = array();
$c['code'] = $name;
$c['sql'] = array("where" => array('numbr = :name'), "orderby" => "timestamp DESC", "params" => array("name" => $name, "limit" => array(1, PDO::PARAM_INT)));
/* Reserved
$c['numbr'] ;
$c['singleValue'];
*/

preg_match_all("/[.]([a-z0-9-]+)(\((.*?)\))?/", $_REQUEST['name'], $matches, PREG_SET_ORDER);
foreach ($matches as $match) {
    $op = $match[1];
    $params = $match[3];
    if ($params) {
        $p = array();
        foreach( explode(",", $params) as $row ) {
            $boom = explode("=", $row, 2);
            if (!$boom[1]) {
                $p[] = $boom[0];
            } else {
                $p[$boom[0]] = $boom[1];
            }
        }
        if (count($p) > 0) {
            $params = $p;
        }
    }
    $op = strtolower($op);
    $c['ops'][] = array($op, $params);
}

require "db.inc";

function makeOrig($row) {
    list($op, $params) = $row;
    if ($op == "default") return "";

    $r = ".$op";
    if (!$params) return $r;
    $p = array();
    foreach ($params as $key => $value) {
        $p[] = "$key=$value";
    }
    $r .= "(" . implode(",", $p) . ")";
    return $r;
}

$formats = scandir("numbrPlugins/format");
$selections = scandir("numbrPlugins/selection");
$operators = scandir("numbrPlugins/operator");

// The single value plugins (format, selection)
foreach ($c['ops'] as $key => $row) {
    list($op, $params) = $row;
    if (in_array($op, $formats))
        $c['formats'][] = $row;

    if (in_array($op, $selections))
        $c['selections'][] = $row;
}
if (count($c['selections']) == 0)
    $c['selections'] = array(array("default", ""));
if (count($c['formats']) == 0)
    $c['formats'] = array(array("default", ""));

foreach (array("selections", "ops", "formats") as $type) {
    foreach ($c[$type] as $key => $row) {
        list($op, $params) = $row;
        if (in_array($op, $operators)) {
            $c['code'] .= makeOrig($row);
        }
    }
}

foreach ($c['selections'] as $key => $row) {
    list($op, $params) = $row;
    if (in_array($op, $selections)) {
        require("numbrPlugins/selection/$op/code.php");
    }
}

$where = implode(" AND ", $c['sql']['where']);
$orderby = $c['sql']['orderby'];
$s = $PDO->prepare("SELECT UNIX_TIMESTAMP(timestamp) as timestamp, data FROM numbr_data WHERE $where ORDER BY $orderby LIMIT :limit");
foreach ($c['sql']['params'] as $key => $value) {
    if (is_string($value))
        $s->bindValue($key, $value);
    else if (is_int($value))
        $s->bindValue($key, $value, PDO::PARAM_INT);
    else if (is_array($value) && count($value) == 2) {
        $s->bindValue($key, $value[0], $value[1]);
    }
}
$r = $s->execute();
if (!$r) 
    $data = array("error" => array("PDO" => $s->errorInfo()));
else  {
    $result = $s->fetchAll(PDO::FETCH_ASSOC);

    if (count($result) == 0) {
        $data = NULL;
    } else if ($c['singleValue']) {
        $data = (float) $result[0]['data'];
    } else {
        // Its an array
        $data = array();
        foreach ($result as $ind => $row) {
            $data[] = array((int) $row['timestamp'], (float) $row['data']);
        }
        sort($data);
    }
}

$s = $PDO->prepare("SELECT * FROM numbrs WHERE name = :name LIMIT 1");
$r = $s->execute(array("name" => $c['name']));
if (!$r) 
    $c['numbr'] = array("error" => array("PDO" => $s->errorInfo()));
else  {
    $result = $s->fetchAll(PDO::FETCH_ASSOC);

    if (count($result) == 0) {
        $c['numbr'] = NULL;
    } else {
        $c['numbr'] = $result[0];
    }
}

// operator plugins
foreach ($c['ops'] as $key => $row) {
    list($op, $params) = $row;
    if (in_array($op, $operators)) {
        require("numbrPlugins/operator/$op/code.php");
    }
}

foreach ($c['formats'] as $key => $row) {
    list($op, $params) = $row;
    if (in_array($op, $formats)) {
        ob_start();
        require("numbrPlugins/format/$op/code.php");
        $data = ob_get_contents();
        ob_end_clean();
    }
}

print $data;
?>

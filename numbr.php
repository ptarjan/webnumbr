<?php

$regexlist = <<<END
NAME    [a-z0-9-]+
OP      \.[a-z0-9-]+
KEY     [^=]+
VALUE   [^,)]+
OPTKEY  {KEY}=|
PARAM   {OPTKEY}{VALUE}
PARAMS  \({PARAM}?(,{PARAM})*\)|
END;

function parse($string, &$c) {
    preg_match("/[a-z0-9-]+/", $string, $matches);
    $c['name'] = $matches[0];

    preg_match_all("/[.]([a-z0-9-]+)(\((.*?)\))?/", $string, $matches, PREG_SET_ORDER);
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
}

$c = array();
$c['name'] = "";
$c['ops'] = array();
parse($_REQUEST['name'], $c);
$c['plugins'] = array();
$c['code'] = $c['name']; // Start the code off with the name
$c['sql'] = array("where" => array('numbr = :name'), "orderby" => "timestamp DESC", "params" => array("name" => $c['name'], "limit" => array(1, PDO::PARAM_INT)));
/* Reserved
$c['numbr'] ;
$c['singleValue'];
*/

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

// Order of cannonical operations
$pluginTypes = array("selection", "operation", "format");
foreach ($pluginTypes as $type) {
    $plugins[$type] = scandir("numbrPlugins/$type");
}
foreach ($pluginTypes as $type) {
    $c['plugins'][$type] = array();
}

// The single value plugins (format, selection)
foreach ($c['ops'] as $key => $row) {
    list($op, $params) = $row;
    foreach ($plugins as $type => $p) {
        if (in_array($op, $p)) {
            $c['plugins'][$type][] = $row;
        }
    }
}

foreach ($pluginTypes as $type) {
    if (!isset($c['plugins'][$type])) continue;
    foreach ($c['plugins'][$type] as $key => $row) {
        list($op, $params) = $row;
        if (in_array($op, $plugins[$type])) {
            $c['code'] .= makeOrig($row);
        }
    }
}

if (count($c['plugins']['selection']) == 0)
    $c['plugins']['selection'] = array(array("default", ""));
if (count($c['plugins']['format']) == 0)
    $c['plugins']['format'] = array(array("default", ""));

foreach ($c['plugins']['selection'] as $key => $row) {
    list($op, $params) = $row;
    if (in_array($op, $plugins['selection'])) {
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
        $c['numbr'] = array();
    } else {
        $c['numbr'] = $result[0];
    }
}

// operator plugins
foreach ($c['plugins']['operation'] as $key => $row) {
    list($op, $params) = $row;
    if (in_array($op, $plugins['operation'])) {
        require("numbrPlugins/operation/$op/code.php");
    }
}

foreach ($c['plugins']['format'] as $key => $row) {
    list($op, $params) = $row;
    if (in_array($op, $plugins['format'])) {
        ob_start();
        require("numbrPlugins/format/$op/code.php");
        $data = ob_get_contents();
        ob_end_clean();
    }
}

print $data;
?>

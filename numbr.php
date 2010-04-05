<?php

require "db.inc";

class Numbr {

public $c = array();

public function parse($string) {
    preg_match("/^[a-z0-9-_]+/", $string, $matches);
    $this->c['name'] = $matches[0];

    preg_match_all("/[.]([a-z0-9-_]+)(\((.*?)\))?/", $string, $matches, PREG_SET_ORDER);
    foreach ($matches as $match) {
        $op = $match[1];
        $params = $match[3];
        if ($params) {
            $p = array();
            foreach( explode(",", $params) as $row ) {
                $boom = explode("=", $row, 2);
                if (!isset($boom[1])) {
                    $p[] = trim($boom[0]);
                } else {
                    $p[trim($boom[0])] = trim($boom[1]);
                }
            }
            if (count($p) > 0) {
                $params = $p;
            }
        }
        $op = strtolower($op);
        $this->c['ops'][] = array($op, $params);
    }
}

public function makeOrig($row) {
    list($op, $params) = $row;
    if ($op == "default") return "";

    $r = ".$op";
    if (!$params) return $r;
    $p = array();   
    $count = 0;
    foreach ($params as $key => $value) {
        if ($key === $count++)
            $p[] = $value;
        else 
            $p[] = "$key=$value";
    }
    $r .= "(" . implode(",", $p) . ")";
    return $r;
}


public function __construct( $string ) {
    $this->c['name'] = "";
    $this->c['ops'] = array();
    $this->parse($string);
}

public function run() {
    global $PDO;
    $this->c['plugins'] = array();
    $this->c['headers'] = array();
    $this->c['code'] = $this->c['name']; // Start the code off with the name
    $this->c['sql'] = array("where" => array('numbr = :name'), "orderby" => "timestamp DESC", "params" => array("name" => $this->c['name'], "limit" => array(PHP_INT_MAX, PDO::PARAM_INT)));
    /* Reserved
    $this->c['numbr'] ;
    $this->c['singleValue'];
    */

    // Order of cannonical operations
    $pluginTypes = array("selection", "operation", "format");
    foreach ($pluginTypes as $type) {
        $plugins[$type] = scandir("numbrPlugins/$type");
    }
    foreach ($pluginTypes as $type) {
        $this->c['plugins'][$type] = array();
    }

    // The single value plugins (format, selection)
    foreach ($this->c['ops'] as $key => $row) {
        list($op, $params) = $row;
        foreach ($plugins as $type => $p) {
            if (in_array($op, $p)) {
                $this->c['plugins'][$type][] = $row;
            }
        }
    }

    foreach ($pluginTypes as $type) {
        if (!isset($this->c['plugins'][$type])) continue;
        foreach ($this->c['plugins'][$type] as $key => $row) {
            list($op, $params) = $row;
            if (in_array($op, $plugins[$type])) {
                $this->c['code'] .= $this->makeOrig($row);
            }
        }
    }

    if (count($this->c['plugins']['selection']) == 0)
        $this->c['plugins']['selection'] = array(array("default", ""));
    if (count($this->c['plugins']['format']) == 0)
        $this->c['plugins']['format'] = array(array("default", ""));

    foreach ($this->c['plugins']['selection'] as $key => $row) {
        list($op, $params) = $row;
        if (in_array($op, $plugins['selection'])) {
            $c = $this->c;
            require("numbrPlugins/selection/$op/code.php");
            $this->c = $c;
        }
    }

    $where = implode(" AND ", $this->c['sql']['where']);
    $orderby = $this->c['sql']['orderby'];
    $s = $PDO->prepare("SELECT UNIX_TIMESTAMP(timestamp) as timestamp, data FROM numbr_data WHERE $where ORDER BY $orderby LIMIT :limit");
    foreach ($this->c['sql']['params'] as $key => $value) {
        if (is_string($value))
            $s->bindValue($key, $value);
        else if (is_int($value))
            $s->bindValue($key, $value, PDO::PARAM_INT);
        else if (is_array($value) && count($value) == 2) {
            $s->bindValue($key, $value[0], $value[1]);
        } else {
            $s->bindValue($key, $value);
        }
    }
    // Default value for name
    if (in_array("numbr = :name" , $this->c['sql']['where']) && !isset($this->c['sql']['params']['name'])) {
        $s->bindValue("name", $this->c['name']);
    }

    $r = $s->execute();
    if (!$r) 
        $data = array("error" => array("PDO" => $s->errorInfo()));
    else  {
        $result = $s->fetchAll(PDO::FETCH_ASSOC);

        if (count($result) == 0) {
            $data = NULL;
        } else if ($this->c['singleValue']) {
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
    $r = $s->execute(array("name" => $this->c['name']));
    if (!$r) 
        $this->c['numbr'] = array("error" => array("PDO" => $s->errorInfo()));
    else  {
        $result = $s->fetchAll(PDO::FETCH_ASSOC);

        if (count($result) == 0) {
            $this->c['numbr'] = array();
        } else {
            $this->c['numbr'] = $result[0];
        }
    }

    // operator plugins
    foreach ($this->c['plugins']['operation'] as $key => $row) {
        list($op, $params) = $row;
        if (in_array($op, $plugins['operation'])) {
            $c = $this->c;
            require("numbrPlugins/operation/$op/code.php");
            $this->c = $c;
        }
    }

    foreach ($this->c['plugins']['format'] as $key => $row) {
        list($op, $params) = $row;
        if (in_array($op, $plugins['format'])) {
            ob_start();
            $c = $this->c;
            require("numbrPlugins/format/$op/code.php");
            $this->c = $c;
            $data = ob_get_contents();
            ob_end_clean();
        }
    }

    return $data;
}
} // End class

$name = $_REQUEST['name'];
if (substr($name, 0, 13) == "dev/webnumbr/")
    $name = substr($name, 13);

$numbr = new Numbr($name);

$data = $numbr->run();
foreach ($numbr->c['headers'] as $header)
    header($header);

print $data;
?>

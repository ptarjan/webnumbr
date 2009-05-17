<?php

$ops = explode(".", $_REQUEST["name"]);
$name = array_shift($ops);

require "db.inc";

// PREPARSING OF OPERATORS
$c = array();
$c['limit'] = 1;
$c['unused'] = array();
$c['single'] = TRUE;
while (count($ops) > 0) {
    $param = array_shift($ops);
    preg_match("/([a-z0-9-_]*)(\((.*)\))?/", $param, $matches); // a-z0-9_- then optionally (.*)
    $op = $matches[1];
    $params = $matches[3];
    if ($params) {
        $p = array();
        foreach( explode(",", $params) as $row ) {
            $boom = explode("=", $row);
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

    switch (strtolower($op)) {
        case "all" :
            $c['limit'] = PHP_INT_MAX;
            $c['single'] = FALSE;
            break;
        case "latest" :
            $c['limit'] = 1;
            $c['single'] = TRUE;
            break;
        case "last" :
            if (!isset($params['count'])) {
                if (count($params) == 1 && isset($params[0]))
                    $count = (int) $params[0];
                else 
                    $count =1;
            } else
                $count = (int) $params['count'];
            $c['limit'] = $count;
            $c['single'] = FALSE;
            break;

// Formats
        case "text" :
        case "print" :
        case "print_r" :
        case "xml" :
        case "html" :
            $c['format'] = $op;
            $c['format-params'] = $params;
            break;
        case "json" :
            $c['format'] = $op;
            $c['format-params'] = $params;
            if (count($params) == 1 && isset($params[0]))
                $c['format-params'] = array('callback' => $params[0]);
            break;
        default :
            $c['unused'][] = array($op, $params);
    }
}
if (!isset($c['format'])) {
    if (isset($_REQUEST["format"])) 
        $c['format'] = $_REQUEST["format"];
    else 
        $_REQUEST["format"] = "html";
}

$s = $PDO->prepare("SELECT data, UNIX_TIMESTAMP(timestamp) as timestamp FROM numbr_data WHERE numbr = :name ORDER BY timestamp DESC LIMIT :limit");
$s->bindValue("limit", $c['limit'], PDO::PARAM_INT);
$s->bindValue("name", $name);
$r = $s->execute();
if (!$r) 
    $data = array("error" => $s->errorInfo());
else  {
    $result = $s->fetchAll(PDO::FETCH_ASSOC);

    if (count($result) == 0) {
        $data = NULL;
    } else if ($c['single']) {
        $data = (float) $result[0]['data'];
    } else {
        // Its an array
        $data = array();
        foreach ($result as $ind => $row) {
            $data[] = array((int) $row['timestamp'], (float) $row['data']);
        }
    }
}

// POST PARSING OF OPERATORS
foreach ($c['unused'] as $row) {
    list($op, $param) = $row;
    switch (strtolower($op)) {
        case "derivative" :
            if (!is_array($data)) continue;
            $derivative = NULL;
            if (isset($param['numtimes']))
                $derivative = (int) $param['numtimes'];
            else if (isset($param[0]))
                $derivative = (int) $param[0]; 
            if (!is_numeric($derivative))
                $derivative = 1;
            $derivative = (int) $derivative;
            for ($d = 0; $d < $derivative; $d ++) {
                $newData = array();
                $last = NULL;
                foreach ($data as $row) {
                    $time = (float) $row[0];
                    $val = (float) $row[1];
                    $old = array($time, $val);
                    if ($last === NULL) {
                        $last = $old;
                        continue;
                    }
                    // Discrete derivative (by the hours)
                    $val = ($val - $last[1]) / ($time - $last[0]) * 60 * 60;
                    // Put the point directly in between the two values
                    // $time -= ($time - $last[0]) / 2;
                    $last = $old;
                    $newData[] = (array($time, $val));
                }
                $data = $newData;
            }
            break;

        case "sum" :
        case "integral" :
            if (!is_array($data)) continue;
            $sum = NULL;
            if (isset($param['numtimes']))
                $sum = (int) $param['numtimes'];
            else if (isset($param[0]))
                $sum = (int) $param[0]; 
            if (!is_numeric($sum))
                $sum = 1;
            $sum = (int) $sum;

            for ($d = 0; $d < $sum; $d ++) {
                $newData = array();
                $last = 0;
                foreach ($data as $row) {
                    $time = (float) $row[0];
                    $val = (float) $row[1];
                    $val = $val + $last;
                    $last = $val;
                    $newData[] = (array($time, $val));
                }
                $data = $newData;
            }


    }
}

switch ($c['format']) {
    case "json" :
        header("Content-Type: application/json");
        $cb = $c['format-params']['callback'];
        if ($cb) {
            print $cb . "(" . json_encode($data) . ")";
        } else {
            print json_encode($data);
        }
        die(); break;
    case "text" :
    case "print" :
        print $data;
        die(); break;
    case "print_r" :
        print_r($data);
        die(); break;
    case "xml" :
        require "XMLHelper.inc";
        header("Content-Type: application/xml");
        print XMLHelper::xml_encode($data);
        die(); break;
    default :
    case "html" :
?>
<?php print '<?xml version="1.0" encoding="UTF-8"?>' ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>webNumr : <?php print htmlspecialchars($_REQUEST['name']) ?></title>
    <link rel="stylesheet" href="style.css" type='text/css' />  
    <style>
#webNumbr {
    margin : 0px 20px;
    padding : 5px;
    background-color : white;
    border : 1px dotted;    
    font-size : 300%;
    width: 710px;
}
form {
    margin : 20px;
}
table {
    width : 100%;
}
caption {
    font-size : 150%;
    font-weight : bold;
}
td, th {
    padding : 5px;
}
    </style>

  </head>
  <body>

    <div id='container'>
      <div id='header'>
        <a href='.'><img id='logo' src="../images/webNumbr-banner-100.png" title="webNumr" alt="webNumbr logo" /></a>
      </div>

      <div class='content'>
<!-- Start Content -->

<form action="numbr">
<input id="name" name="name" value="<?php print htmlspecialchars($_REQUEST['name']) ?>" style="width:649px"/>
<input type="submit" value="reload" />
</form>

<textarea class="center" id="webNumbr">
<?php print json_encode($data); ?>
&nbsp;
</textarea>

<div class="center"><a id="link">&nbsp;</a></div>

<div>
</div>

<h1>Commands</h1>
<div>
<b>Basics</b> : All commands are seperated by <i>.</i> All parameters are wrapped by <i>()</i>
</div>

<table>
<caption>Formats : These can appear anywhere and the last one to appear wins.</caption>
<tr>
 <th>name</th>
 <th>params</th>
 <th>doc</th>
</tr>
<tr>
 <td>json</td>
 <td>callback=cb</td>
 <td><a href="http://json.org">JSON</a>. Good for AJAX requests.</td>
</tr>
<tr>
 <td>text</td>
 <td></td>
 <td>Raw text printout. Good for easy use of a single number</td>
</tr>
<tr>
 <td>xml</td>
 <td></td>
 <td><a href="http://www.w3.org/XML/">XML</a>. Not recommended. Use only if required.</td>
</tr>
</table>

<table>
<caption>Data selection : These choose which piece of data you want. Last one wins.</caption>
<tr>
 <th>name</th>
 <th>params</th>
 <th>doc</th>
</tr>
<tr>
 <td>latest</td>
 <td></td>
 <td>Selects the latest number. Equivilent to last(count=1)</td>
</tr>
<tr>
 <td>last</td>
 <td>count=1</td>
 <td>Selects the last <i>count</i> entries. <i>count</i> defaults to 1</td>
</tr>
<tr>
 <td>all</td>
 <td></td>
 <td>Selects the whole data history</td>
</tr>
</table>

<table>
<caption>Data operators : These are evaluated in order and are chained together.</caption>
<tr>
 <th>name</th>
 <th>params</th>
 <th>doc</th>
</tr>
<tr>
 <td>derivative</td>
 <td>numtimes=1</td>
 <td>Performaces the discrete derivative on the data. Good for seeing change of graph</td>
</tr>
<tr>
 <td>sum</td>
 <td>numtimes=1</td>
 <td>Sums up the numbers. Basically the inverse of the derivative</td>
</tr>
<tr>
 <td>integral</td>
 <td></td>
 <td>Alias for sum</td>
</tr>
</table>
</div>

<script src="http://www.google.com/jsapi"></script>
<script>
google.load("jquery", "1");
google.setOnLoadCallback(function() {

$("form").submit(function() {
    $("#webNumbr").html('<img src="images/twirl.gif" alt="thinking" />');
    var val = $("#name").val();
    $.get("numbr?format=json&name=" + encodeURIComponent(val), "", function(data) {
        $("#webNumbr").height(0);
        $("#webNumbr").text(data);
        $("#webNumbr").height($("#webNumbr").get(0).scrollHeight);

        $("#link").text(val);
        $("#link").attr("href", "numbr?name=" + val);
    }, "html");
    return false;
});
$("form").submit();
$("#name").focus();

});
</script>

<!-- End Content -->
      </div>
    </div>
  </body>
</html>

<?php
        break;
};
?>

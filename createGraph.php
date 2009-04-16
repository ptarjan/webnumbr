<?php
if (isset($_REQUEST['go'])) {
    function required($p) {
        if (is_array($p)) 
            foreach ($p as $v) required($v);
        if (!isset($_REQUEST[$p])) die ("Required parameter $p");
        if (trim(isset($_REQUEST[$p])) == "") die ("Empty parameter $p");
    }
    required("name", "url", "xpath", "frequency");
    require ("db.inc");
    $stmt = $PDO->prepare("INSERT INTO graphs (name, url, xpath, frequency, createdTime) VALUES (:name, :url, :xpath, :frequency, NOW())");
    $r = $stmt->execute(array("name" => $_REQUEST['name'], "url" => $_REQUEST['url'], "xpath" => $_REQUEST['xpath'], "frequency" => $_REQUEST['frequency']));
    if (!$r) {
        die("Something is wrong with the database right now. Please retry again later, and send me an email webGrapher@paulisageek.com<br/>" . print_r($stmt->errorInfo(), TRUE));
    } else {
        $stmt = $PDO->prepare("SELECT id FROM graphs WHERE name = :name AND url = :url AND xpath = :xpath AND frequency = :frequency ORDER BY createdTime ASC");
        $r = $stmt->execute(array("name" => $_REQUEST['name'], "url" => $_REQUEST['url'], "xpath" => $_REQUEST['xpath'], "frequency" => $_REQUEST['frequency']));
        if (! $r) { 
            die ("ummm.. can't select from the database<br/>" . print_r($stmt->errorInfo(), TRUE));
        };

        $result = $stmt->fetchAll();    

        if (count($result) == 0) die ("ummm.. couldn't insert into database");

        header("Location: graph.php?id=" . $result[0]['id']);
    }
    die();
}

// print $_REQUEST['referer'] . "<br>" . $_SERVER['HTTP_REFERER'];
if (isset($_REQUEST['referer']))
    $referer = $_REQUEST['referer'];
else if (isset($_SERVER['HTTP_REFERER']))
    $referer = $_SERVER['HTTP_REFERER'];
else
    $referer = "";

$parsed = parse_url($referer);
$query = $parsed['query'];
$url = "";
foreach (explode("&", $query) as $arg) {
    $boom =  explode("=", $arg);
    if (count($boom) !== 2) continue;
    list($k, $v) = $boom;
    if ($k == "url") 
        $url = urldecode($v);
}
$xpath = $_REQUEST['xpath'];

?>

<?php
print '<?xml version="1.0" encoding="UTF-8"?>';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>Web Grapher - Create Graph</title>
    <link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/2.7.0/build/reset/reset-min.css" />
    <link rel="stylesheet" href="/style.css" type='text/css' />  

    <link rel="shortcut icon" href="graph.ico" type="image/x-icon">
    <link rel="icon" href="graph.ico" type="image/x-icon">

    <link type="text/css" href="http://jqueryui.com/latest/themes/base/ui.all.css" rel="stylesheet" />
    <script src='http://ajax.googleapis.com/ajax/libs/jquery/1.3/jquery.min.js'></script>
    <script type="text/javascript" src="http://jqueryui.com/latest/ui/ui.core.js"></script>
    <script type="text/javascript" src="http://jqueryui.com/latest/ui/ui.draggable.js"></script>
    <script type="text/javascript" src="http://jqueryui.com/latest/ui/ui.resizable.js"></script>
    <script type="text/javascript" src="http://jqueryui.com/latest/ui/ui.dialog.js"></script>
    <script>

$(document).ready(function() {
    function reload() {
        $("#data").html("<img src='http://l.yimg.com/a/i/eu/sch/smd/busy_twirl2_1.gif' />");
        $.get("selectNode.php?" + $.param({url : $(":input[name='url']").attr("value"), xpath : $(":input[name='xpath']").attr("value")}), function (data) {
            $("#data").html(data);
        });
    }
    $("#data").ready(reload);
    $("#reload").click(reload);

    var confirmed = false;
    $("form").submit(function(ev) {
        if ($.trim($(":input[name='name']").attr("value")) == "") {
            $(":input[name='name']").wrap("<span class='error' style='border:10px solid red'></span>");
            return false;
        }
        var data = parseInt($("#data").text());
        if (isNaN(data)) {
            $("#data").wrapInner("<span class='error' style='color:red'></span>");
            return false;
        }
        $("#dialog").dialog("open");
        if (!confirmed) {
            return false;
        } else {
            return true;
        }
    });
    $("#dialog").dialog({
        modal : true,
        autoOpen : false,
        buttons : {
            "Yes!" : function() { 
                confirmed = true;
                $("form").submit() 
            },
            "No! (Go back and edit)" : function () { $("#dialog").dialog("close"); },
        },
        title : "Last step before graphyness",
        hide : "slide",
    });
});

</script>

  </head>
  <body>
    <div id='main'>

    <h1>Last Step</h1>

    <form>
      <div id='content'>

<input type="hidden" name="go" value="go" />
<table>
<tr><td>Name (<b>required</b>):</td><td><input name="name" size="100"/></td></tr>
<tr><td>URL: </td><td><input name="url" value="<?php print htmlspecialchars($url) ?>" size="100" /></td></tr>
<tr><td>Xpath: </td><td><input name='xpath' value="<?php print htmlspecialchars($xpath); ?>" size="100" /></td></tr>
<tr><td>Example of the data (<b>must be a number</b>): </td><td><b id='data'></b> <input type="button" id='reload' value="Reload" /></tr>
<tr><td>Graph Frequency: </td><td><select name='frequency'>
<option value="1">1 hour</option>
<option value="6">6 hours</option>
<option value="12">12 hours</option>
<option value="24">24 hours</option>
</select></td></tr>
<tr><td></td><td><input type="submit" value="Create Graph" /></td></tr>
</table>
      </div>
</form>

<div id='dialog'>
Everything look good? These values can't be changed once you click yes.
<br/>
</div>

</div>
</body>
</html>

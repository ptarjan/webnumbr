<?php print '<?xml version="1.0" encoding="UTF-8"?>' ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>webNumbr : <?php print htmlspecialchars($_REQUEST['name']) ?></title>
    <link rel="stylesheet" href="style.css" type='text/css' />  
    <style type="text/css">
#webNumbr {
    margin : 0px 20px;
    padding : 5px;
    background-color : white;
    border : 1px dotted;    
    font-size : 300%;
    width: 710px;
    overflow : auto;
}
form#numbrForm {
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

#menu {
    float : left;
    margin : 0px;
   	padding: 5px;
	border-left : 1px solid;
	border-bottom : 1px solid;
	background-color : white;
	vertical-align : middle;
}
#menu form {
    display : inline;
}
#menu a {
    color : blue;
}
</style>
  </head>
  <body>
<!--
      <div id="menu">
        <form action='search'> 
        <label for="query" title="Search within the metadata of any numbr">Search:</label> 
        <input id="query" name='query' value='' size="20" />
        </form>

        <form action='selectNode'>
        <label for="url" title="Create a new numbr from any URL">New Numbr:</label> 
        <input id="url" name='url' value='http://' size="20" />
        </form>
    
        <a href="random">Random</a>
      </div>
-->

    <div id='container'>
<?php include ("tweet.inc") ?>
      <div id='header'>
        <a href='.'><img id='logo' src="images/webNumbr-banner-50.png" title="webNumbr" alt="webNumbr logo" /></a>
      </div>

      <div class='content'>
<!-- Start Content -->

<form id="numbrForm" action="numbr">
<input id="name" name="name" value="<?php print htmlspecialchars($_REQUEST['name']) ?>" style="width:640px"/>
<input type="submit" value="reload" />
</form>

<pre class="center" id="webNumbr" rows="1" cols="40">
<?php print json_encode($data); ?>
</pre>

<div class="center">
    <a id="link">&nbsp;</a>
    Embed: <input type="text" />
</div>

<div>
</div>

<h1>Commands</h1>
<div>
<b>Basics</b> : All commands are seperated by <i>.</i> All parameters are wrapped by <i>()</i>
</div>

<?php
function printDoc($dir) {
?>
<tr>
 <th>name</th>
 <th>params</th>
 <th>doc</th>
</tr>
<?php
    $p = scandir("numbrPlugins/$dir");
    sort($p);
    foreach ($p as $name) {
        if (substr($name, 0, 1) == ".") continue;
        $params = @file_get_contents("numbrPlugins/$dir/$name/params.txt");
        $doc = @file_get_contents("numbrPlugins/$dir/$name/doc.txt");
        if (!$doc) continue;
?>
<tr>
 <td><?php print $name ?></td>
 <td><?php print trim($params) ?></td>
 <td><?php print $doc ?></td>
</tr>
<?php
    }
}
?>

<table>
<caption>Formats : These can appear anywhere and the last one to appear wins.</caption>
<?php printDoc("format"); ?>
</table>

<table>
<caption>Selectors : These choose which piece of data you want. Last one wins.</caption>
<?php printDoc("selection"); ?>
</table>

<table>
<caption>Operators : These are evaluated in order and are chained together.</caption>
<?php printDoc("operator"); ?>
</table>

<script src="http://www.google.com/jsapi" type="text/javascript"></script>
<script type="text/javascript">
<!--
google.load("jquery", "1");
google.setOnLoadCallback(function() {jQuery.noConflict()(document).ready(function($) {
var addOp = function(op) {
    $("#name").val($("#name").val() + "." + op);
    reload();
}

$("tr td:first-child")
.filter(function() { return $(this).text() != "default" })
.wrapInner("<a>")
.children("a")
.attr("href", "#")
.attr("title", "Add this operator")
.css("color", "blue")
.click(function() {
    addOp($(this).text());
    return false;
});
$("tr td:nth-child(2)")
.wrapInner("<a>")
.children("a")
.attr("href", "#")
.attr("title", "Add this operator")
.css("color", "blue")
.click(function() {
    addOp(
        $(this).parent().prev().text() + "(" + $(this).text() + ")"
    );
    return false;
});

var reload = function() {
    $("#webNumbr").addClass("center").html('<img src="images/twirl.gif" alt="thinking" />');
    var val = $("#name").val();
    val = val.toLowerCase();
    val = val.replace(/[^a-z0-9-.()=]/g, '-'); 
    $("#name").val(val);
    $.get(val + "?format=json", "", function(data, status) {
        if (status != "success") {
            w.text("Error with the request. Try again or email me webNumbr@paulisageek.com");
            return;
        }
        var w = $("#webNumbr");
        if (data.length > 10) {
            w.removeClass("center");
        }
        if (data.search("http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd") != -1) {
            // Oops, we wasted an ajax call, oh well.
            w.html(
                $('<iframe/>')
                .attr("src", val)
                .attr("allowtransparnecy", true)
                .attr("frameborder", 0)
                .css("width", "100%")
                .css("height", "400px")
            );
        } else {
            /* w.height(0); */
            w.text(data);
            /* for the textarea
            var height = w.get(0).scrollHeight;
            if (w.get(0).scrollWidth != w.get(0).clientWidth) height += 24;
            w.height(height);
            */
        }
        $("#link").text(val);
        $("#link").attr("href", val);
    }, "html");
    return false;
}
$("form#numbrForm").submit(reload);
reload();
$("#name").focus();
});});
-->
</script>

<!-- End Content -->
<?php include("ga.inc") ?>

      </div>
    </div>
  </body>
</html>

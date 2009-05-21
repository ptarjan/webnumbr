<?php print '<?xml version="1.0" encoding="UTF-8"?>' ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>webNumbr : <?php print htmlspecialchars($_REQUEST['name']) ?></title>
    <link rel="stylesheet" href="/style.css" type='text/css' />  
    <style type="text/css">
#webNumbr {
    margin : 0px 20px;
    padding : 5px;
    background-color : white;
    border : 1px dotted;    
    font-size : 300%;
    width: 710px;
    overflow-x : auto;
}
#header a {
    margin-left : 48px; /* width of random thing */
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

table.docs {
    padding: 0px 10px;
}
table.docs caption {
    padding-top : 20px;
}
table.numbr_info a:visited {
    color : blue;
}
#random {
    float : right;
    background : white;
    margin : 0px;
    padding : 5px;
    vertical-align : middle;
    border : 1px solid;
    border-top : none;
    height : 21px;
    width : 48px;
}
#random a {
    text-decoration : none
}
#random a:visited {
    color : blue
}
#random a:hover {
    text-decoration : underline
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
    <div id="random">
        <a href="/random">Random</a>
    </div>

      <div id='header'>
        <a href='/'><img id='logo' src="/images/webNumbr-banner-50.png" title="webNumbr" alt="webNumbr logo" /></a>
      </div>

      <div class='content'>
<!-- Start Content -->

<form id="numbrForm" action="numbr">
<div id="myAutoComplete" class="yui-skin-sam">
    <input id="name" name="name" value="<?php print htmlspecialchars(str_replace(".html", "", $_REQUEST['name'])) ?>" />
    <input type="submit" value="reload" />
    <div id="autocomplete"></div>
</div>
</form>

<pre class="center" id="webNumbr" rows="1" cols="40">
<?php 
if (strpos($data, "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd") === FALSE) {
    print json_encode($data); 
}
?>
</pre>

<div class="center">
    <a id="link">&nbsp;</a>
    <div title="Put this on your website to embed the current version of this number. Your users without javascript will only see the version as of right now."><label for="embed">Embed:</label><input type="text" id="embed" style="width : 90%"/></div>
</div>

<div>
</div>

<h1>Commands</h1>
<div>
<b>Basics</b> : All commands are seperated by <i>.</i> All parameters are wrapped by <i>()</i>. <a href="/numbrExamples">Examples</a>. <a href="/numbrPlugins">Plugin sources</a>. 
</div><div>
Order of operations (all selectors, "SQL", all operations, all formats, "print")
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

    // Put default at the top
    $key = array_search("default", $p);
    unset($p[$key]);
    array_unshift($p, 'default');
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

<table class="docs">
<caption>Selectors : These choose which piece of data you want.</caption>
<?php printDoc("selection"); ?>
</table>

<table class="docs">
<caption>Operations : These are evaluated in order and are chained together.</caption>
<?php printDoc("operation"); ?>
</table>

<table class="docs">
<caption>Formats : Output encoding. Can be chained.</caption>
<?php printDoc("format"); ?>
</table>

<h1 class="numbr_info">Numbr Info</h1>
<table class="numbr_info">
<?php
foreach ($c['numbr'] as $key => $value) {
if ($key == "id") continue;
?>
<tr>
    <th><?php print htmlspecialchars($key); ?></th>
    <td><?php 
$hvalue = htmlspecialchars($value);
$link = "";
switch ($key) {
    case "name" :
        $link = "/$hvalue";
        break;
    case "title" :
    case "description" :
        $parts = explode(" ", $value);
        foreach ($parts as $part) {
            print '<a href="/search?query=' . urlencode($part) . '">' . htmlspecialchars($part) . '</a> ';
        }
        $hvalue = "";
        break;
    case "url" :
        $link = $hvalue;
        break;
    case "xpath" :
        $link = '/selectNode?' . http_build_query(array("url" => $c['numbr']['url'], "xpath" => $c['numbr']['xpath'], "action" => "show"));
        break;
    case "frequency" :
        $hvalue = "Every $hvalue hour" . ($value == 1 ? "" : "s");
        break;
    case "openid" :
        $link = $hvalue;
        break;
    case "is_fetching" :
        if ($value == 1)
            $hvalue = '<span style="color:green">Good : this numbr is fetching</span>';
        else 
            $hvalue = '<span style="color:red">Bad : this numbr is not fetching due to too many fetch errors</span>';
}
if (trim($hvalue) != "") {
    if (trim($link) != "") {
        print '<a href="' . $link . '">' . $hvalue . '</a>' ;
    } else {
        print $hvalue;
    }
}
?>
</td>
</tr>
<?php } ?>
</table>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js" type="text/javascript"></script>
<!--
<script type='text/javascript' src='/numbrPlugins/format/html/jquery-autocomplete/lib/jquery.bgiframe.min.js'></script>
<script type='text/javascript' src='/numbrPlugins/format/html/jquery-autocomplete/lib/jquery.ajaxQueue.js'></script>
<script type='text/javascript' src='/numbrPlugins/format/html/jquery-autocomplete/lib/thickbox-compressed.js'></script>
<script type='text/javascript' src='/numbrPlugins/format/html/jquery-autocomplete/jquery.autocomplete.min.js'></script>
<link rel="stylesheet" type="text/css" href="/numbrPlugins/format/html/jquery-autocomplete/jquery.autocomplete.css" />
<link rel="stylesheet" type="text/css" href="/numbrPlugins/format/html/jquery-autocomplete/lib/thickbox.css" />
-->
<!-- Combo-handled YUI CSS files: -->
<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/combo?2.7.0/build/autocomplete/assets/skins/sam/autocomplete.css">
<!-- Combo-handled YUI JS files: -->
<script type="text/javascript" src="http://yui.yahooapis.com/combo?2.7.0/build/yahoo-dom-event/yahoo-dom-event.js&2.7.0/build/animation/animation-min.js&2.7.0/build/connection/connection-min.js&2.7.0/build/datasource/datasource-min.js&2.7.0/build/autocomplete/autocomplete-min.js"></script>
<style type="text/css">
div.yui-skin-sam, .yui-skin-sam div {
    margin  : 0px;
}
#name, #autocomplete {
    width : 643px;
}
#name {
    position : static;
}
</style>

<script type="text/javascript">
<!--
$(document).ready(function($) {

/*
$("#name").attr("autocomplete", "off").autocomplete("/autocomplete", {
    matchCase : true,
    max : 50,
});
*/
// YAHOO autocomplete
(function() {
var oDS = new YAHOO.util.XHRDataSource("/autocomplete");
oDS.responseType = YAHOO.util.XHRDataSource.TYPE_TEXT;
oDS.responseSchema = {
    recordDelim: "\n",
    fieldDelim: "\t"
};
// Enable caching
oDS.maxCacheEntries = 5;

var oAC = new YAHOO.widget.AutoComplete("name", "autocomplete", oDS);
oAC.useShadow = true;

oAC.generateRequest = function (sQuery) {
    return "?q=" + sQuery;
};

// Keeps container centered
oAC.doBeforeExpandContainer = function(oTextbox, oContainer, sQuery, aResults) {
    var pos = YAHOO.util.Dom.getXY(oTextbox);
    pos[1] += YAHOO.util.Dom.get(oTextbox).offsetHeight + 2;
    YAHOO.util.Dom.setXY(oContainer,pos);
    return true;
};
/*
oAC.containerCollapseEvent.subscribe(function () {
    $("#name").submit();
});
*/

return {
    oDS: oDS,
    oAC: oAC
};
})();


$("#name").focus();

var addOp = function(op) {
    $("#name").val($("#name").val() + "." + op);
    reload();
}

$("table.docs tr td:first-child")
.filter(function() { return $(this).text() != "default" })
.wrapInner("<a>")
.children("a")
.attr("href", "#")
.attr("title", "Add this")
.css("color", "blue")
.click(function() {
    addOp($(this).text());
    return false;
});
$("table.docs tr td:nth-child(2)")
.wrapInner("<a>")
.children("a")
.attr("href", "#")
.attr("title", "Add this with params")
.each(function(i) { 
    var text = $(this).text(); 
    $(this).click(function() {
        addOp(
            $(this).parent().prev().text() + "(" + text + ")"
        );
        return false;
    });
    if (text.length > 20) {
        $(this).text(text.substring(0, 17) + "...");
    }
})
.css("color", "blue")
;
var reload = function() {
    // $("#webNumbr").addClass("center").html('<img src="images/twirl.gif" alt="thinking" />');
    var val = $("#name").val();
    // val = val.toLowerCase();
    // val = val.replace(/[^a-z0-9-.,()=]/g, '-'); 
    $("#name").val(val);
    // $.get("/numbr?format=json&name=" + encodeURIComponent(val), "", function(data, status) {
    $.get(val + "?format=json", "", function(data, status) {
        if (status != "success") {
            w.text("Error with the request. Try again or email me webNumbr@paulisageek.com");
            return;
        }
        var w = $("#webNumbr");
        if (data.search("http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd") != -1 || data.search('<?php print '<?xml version="1.0" encoding="UTF-8"?' ?>') != -1) {
            // Oops, we wasted an ajax call, oh well.
            var base = document.location.href;
            base = base.replace(/\/[^\/]*$/, '/');
            w.html(
                $('<iframe/>')
                .attr("src", base + val)
                .attr("allowtransparnecy", true)
                .attr("frameborder", 0)
                .css("width", "100%")
                .css("height", "400px")
            );
            $("#embed").val(w.html());
        } else {
            /* w.height(0); */
            w.slideUp("normal", function(){ 
                if (data.length > 20) {
                    w.removeClass("center");
                } else {
                    w.addClass("center");
                }
                $(this).text(data).slideDown("normal")
            });
            /* for the textarea
            var height = w.get(0).scrollHeight;
            if (w.get(0).scrollWidth != w.get(0).clientWidth) height += 24;
            w.height(height);
            */
            function randString(length, charset) {
                var ret = "";
                if (! charset)
                    charset = "abcdefghijklmnopqrstuvwxyz";
                for (var i=0; i < length; i++) {
                    var r = Math.floor(Math.random() * charset.length);
                    ret += charset.substring(r, r+1);
                }
                return ret;
            }
            // var rand = Math.floor(Math.random() * Math.pow(2, 32));
            var rand = randString(6);
            // var wnval = "webnumbr_" + rand;
            var wnval = "webnumbr";
            var embed = '<span id="webnumbr">' + wnval + '</span>'
            + '<script>var ' + wnval + ' = function(data) { document.getElementById("' + wnval + '").innerHTML = data; }</' + 'script>'
            + '<script src="http://webnumbr.com/' + val + '.json(callback=' + wnval + ')"></' + 'script>'
            $("#embed").val(embed);
        }
        $("#link").text(val);
        $("#link").attr("href", "/" + val);
        var name = $("#name").val().replace(/\..*/, '');
        if (name != $("#name").attr("defaultValue").replace(/\..*/, '')) {
            $("table.numbr_info").html("")
            $("h1.numbr_info").html('<a href="/' + name +'">Click to Load This Numbr\'s Info</a>');
        }
    }, "html");
    return false;
}
$("form#numbrForm").submit(reload);

reload();
$("#embed").focus(function() {
    $("#embed").select();
});

});
-->
</script>

<!-- End Content -->
<?php include("ga.inc") ?>

      </div>
    </div>
  </body>
</html>

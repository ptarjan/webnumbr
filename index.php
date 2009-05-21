<?php
print '<?xml version="1.0" encoding="UTF-8"?>';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>webNumbr: Can I get your Numbr?</title>
    <link rel="stylesheet" href="style.css" type='text/css' />  
    
  </head>
  <body>
    <div id='container'>
    <?php include ("menu.inc"); ?>
      <div id='header'>
        <a href='.'><img id='logo' src="images/webNumbr-banner-100.png" alt="logo" /></a>
      </div>

      <div class="content">

        <p class="center">
        Numbers are all over the web, but they are hard to access. webNumbr does 3 things : Extracts numbers from any webpage, gives you a short name for them, and keeps a history of them.
        </p>
        <p class="center">
<?php
require ("db.inc");
$stmt = $PDO->prepare("SELECT COUNT(name) as count FROM numbrs");
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
$count = (int) $data[0]['count'];
?>
        There are <b id="numbrCount"><?php print $count ?></b> numbrs and counting.
        </p>

        <h1 id="start">
          Start a Numbr
        </h1>

        <form action='selectNode'>
          <div id="startForm"> 
            <label id="urlLabel" for="url">URL:</label>
            <input id="url" type="text" name='url' value="http://" />
            <input id="submitURL" type='submit' value='Pick the Numbr on the Page' />
          </div>
        </form>

        <h1>Search All Numbrs</h1>

        <form action="search">
          <div id="searchForm">
            <input id="query" type="text" name='query' />
            <input id="submitQuery" type="submit" value='Search' />
          </div>
        </form>

        <h1>See a Numbr</h1>

        <form action="numbr">
          <div id="numbrForm" class="yui-skin-sam" style="margin-left : 10px">
            <input id="name" type="text" name='name' />
            <input id="submitNumbr" type="submit" value='Get Numbr' />
            <div id="autocomplete"></div>
          </div>
        </form>

        <h1>Last 10 Numbrs</h1>

        <ul>
<?php
$stmt = $PDO->prepare("SELECT name, short(title, 50) as shorttitle, title, description, url, short(url, 30) as shorturl, is_fetching FROM numbrs ORDER BY createdTime DESC LIMIT 10");
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($data as $row) {
?>
            <li>
              <a href="<?php print htmlspecialchars($row['name']) ?>" title="<?php print htmlspecialchars($row['description']) ?>">
                <?php print htmlspecialchars($row['name']) ?></a>
                : <a title="<?php print htmlspecialchars($row['title']) ?>"><?php print htmlspecialchars($row['shorttitle']) ?> </a>
              <a title="<?php print htmlspecialchars($row['url']) ?>">(<?php print htmlspecialchars($row['shorturl']) ?>)</a>
<?php if (!$row['is_fetching']) { ?>
              <span class="error">Not fetching due to errors.</span>
<?php } ?>
            </li>
<?php
}
?>
        </ul>

        <h1>Most Popular 10 <a href="allhosts">Hosts</a></h1>

        <ul>
<?php
$stmt = $PDO->prepare('SELECT COUNT(*) AS count, domain FROM numbrs GROUP BY domain ORDER BY count DESC LIMIT 10');
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($data as $row) {
    print "            <li><a href='search?query=" . htmlspecialchars($row['domain']) . "'>" . htmlspecialchars($row['domain']) . "</a> (" . htmlspecialchars($row['count']) . ")</li>\n";
}
?>
        </ul>
          
        <h1>News</h1>
        <ul><li>
        <span class="date">May 16, 2009</span> : With an idea from my friend <a href="http://yury.name">Yury</a>, <a href="http://paulisageek.com/webGraphr/">webGraphr</a> has now been split into two pieces. And so <a href="http://webnumbr.com">webNumbr</a> is born.
        </li></ul>

      </div>
    </div>

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
    width : 649px;
}
#name {
    position : static;
}
</style>

<script type="text/javascript">
<!--
$(document).ready(function($) {
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
}
}());
});
-->
</script>
<script type="text/javascript">
$(function($) {
var resizeURL = function() {
    $("#url").width($("#startForm").width() - $("#urlLabel").outerWidth(true) - $("#submitURL").outerWidth(true) - 25);
};
$("#startForm").ready(function() {
    $(window).resize(resizeURL);
    resizeURL();
});

var resizeSearch = function() {
    $("#query").width($("#searchForm").width() - $("#submitQuery").outerWidth() - 25);
};
$("#searchForm").ready(resizeSearch);
$(window).resize(resizeSearch);

var resizeNumbr = function() {
    $("#name").width($("#numbrForm").width() - $("#submitNumbr").outerWidth() - 25);
};
$("#numbrForm").ready(resizeNumbr);
$(window).resize(resizeNumbr);

/*
$("#name").attr("autocomplete", "off").autocomplete("/autocomplete", {
    matchCase : true,
    max : 50,
});
*/
});
</script>

<?php include("ga.inc") ?>

  </body>
</html>

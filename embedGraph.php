<?php
print '<?xml version="1.0" encoding="UTF-8"?>';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>webGraphr</title>
    <link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/2.7.0/build/reset/reset-min.css" />
    <link rel="stylesheet" href="/style.css" type='text/css' />  
    <link rel="stylesheet" href="style.css" type='text/css' />  

    <link rel="icon" href="images/webGraphr-favicon.png" type="image/x-icon" />

    <style type="text/css">
html {
    margin : 0px;
    height : 100%;
    overflow : hidden;
    background : transparent;
}
body {
    height : 100%;
}

div.content {
    margin : auto;
    width : auto;
    vertical-align : middle;
    -moz-border-radius : 0px;
    -webkit-border-radius : 0px;
    height : 100%;
    border : 0px;
    background-color : transparent;
}

h5 {
    margin : 0px;
    text-decoration : underline;
}
    </style>
  </head>
  <body>
    <h5 id='title'></h5>
    <div class="content">
        <div id="plot" class='center'>_______/\___|\___/\</div>
    </div>

<script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jquery/1.3/jquery.min.js'></script>
<script type='text/javascript' src='http://plugins.jquery.com/files/jquery.query-2.1.2.js.txt'></script>
<!--[if IE]><script type="text/javascript" src="js/flot/excanvas.pack.js"></script><![endif]-->
<script type="text/javascript" src="js/flot/jquery.flot.js"></script>
<script type="text/javascript" src='graph.js'></script>
<script>
if (typeof paulisageek == "undefined") { paulisageek = {}; }
if (typeof paulisageek.wg == "undefined") { paulisageek.wg = {}; }

paulisageek.wg.preGraphCallback = function(json) {
    $("#title").ready(function() {
        var keys = $.query.keys;
        delete keys.type;
        if ($("#title").text().length > 50) {
            $("#title").text($("#title").text().substring(0,47) + "...");
        }
        $("#title").wrap('<a target="webGrapher" href="http://paulisageek.com/webGraphr/graph?' + $.param(keys) + '"></a>"');
    });
    $("#plot").height(($(".content").innerHeight() - $("#title").height() - 20));
    $("#plot").width(($(".content").width() - 20));
}
paulisageek.wg.postGraphCallback = function(json) {
    $(".legend a").attr("target", "webGrapher");
    var legend = $(".legend");
    var html = legend.html();
    legend.data("oldLegend", html);
    var div = $(".legend div:first");
    legend.html($("<a/>")
        .attr("href", "#")
        .append("Show Legend")
        .css({
            "top":div.css("top"),
            "left":div.css("left"),
            "position":div.css("position"),
            "margin-left":div.css("margin-left"),
            "margin-top":div.css("margin-top"),
            "color":"black"
        })
        .click(function() {
            legend.html(legend.data("oldLegend"));
        })
    );
}
</script>

<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("UA-149816-4");
pageTracker._trackPageview();
} catch(err) {}
</script>

  </body>
</html>

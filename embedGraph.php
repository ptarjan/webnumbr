<?php
print '<?xml version="1.0" encoding="UTF-8"?>';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>Web Grapher</title>
    <link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/2.7.0/build/reset/reset-min.css" />
    <link rel="stylesheet" href="/style.css" type='text/css' />  
    <link rel="stylesheet" href="style.css" type='text/css' />  

    <link rel="icon" href="graph.ico" type="image/x-icon" />

    <script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jquery/1.3/jquery.min.js'></script>
    <script type='text/javascript' src='http://plugins.jquery.com/files/jquery.query-2.1.2.js.txt'></script>
    <!--[if IE]><script type="text/javascript" src="js/flot/excanvas.pack.js"></script><![endif]-->
    <script type="text/javascript" src="js/flot/jquery.flot.js"></script>
    <script type="text/javascript" src='graph.js'></script>
    <style type="text/css">
html {
    margin : 0px;
    height : 100%;
    overflow : hidden;
}
body {
    height : 100%;
}

div#main {
    margin : auto;
    min-height : 95%;
    width : auto;
    vertical-align : middle;
    -moz-border-radius : 0px;
    -webkit-border-radius : 0px;
}

div#plot {
    width : 90%;
    min-height : 95%;
}
h5 {
    margin : 0px;
    text-decoration : underline;
}
    </style>
  </head>
  <body>
    <div id="main">
        <h5 id='title'></h5>
        <div id="plot" class='center'>/\___|\___/\</div>
    </div>
    <script>
$(document).ready(function() {
    $("#plot").height(($("#main").innerHeight() - $("#title").outerHeight(true) - 17));
});
if (typeof paulisageek == "undefined") { paulisageek = {}; }
if (typeof paulisageek.wg == "undefined") { paulisageek.wg = {}; }

paulisageek.wg.graphCallback = function(json) {
    $("#title").ready(function() {
        var keys = $.query.keys;
        delete keys.type;
        $("#title").wrap('<a target="_new" href="http://paulisageek.com/webGrapher/graph.php?' + $.param(keys) + '"></a>"');
    });
}
    </script>
  </body>
</html>

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

    <link rel="icon" href="graph.ico" type="image/x-icon" />

    <script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jquery/1.3/jquery.js'></script>
    <script type='text/javascript' src='http://plugins.jquery.com/files/jquery.query-2.1.2.js.txt'></script>
    <!--[if IE]><script type="text/javascript" src="js/flot/excanvas.pack.js"></script><![endif]-->
    <script type="text/javascript" src="js/flot/jquery.flot.js"></script>

    <script type="text/javascript" src='graph.js'></script>
  </head>
  <body>
    <div id='main'>

      <h1 id='title'></h1>

      <div class='content'>

        <div id="plot" style="width:95%;height:600px;" class='center'></div>

      </div>

       <h1><a id='graphinfo' href='#'>See Graph Info</a></h1>

      <div class='content' id="data">
      </div>
    </div>
  </body>
</html>

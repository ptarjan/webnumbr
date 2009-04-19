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

    <!-- Calendar -->
    <link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.0/themes/redmond/jquery-ui.css" />
    <!-- END Calendar -->

    <style type="text/css">
.ui-datepicker-trigger {
    margin-left : 2px;
}
    </style>

  </head>
  <body>
    <div id='container'>
      <div id='header'>
        <a href='.'><img id='smalllogo' src="images/webGraphr-banner-100.png" /></a>
      </div>

      <div id='content'>

          <h1 id='title'></h1>

          <div>

            <div id="plot" style="width:95%;height:600px;" class='center'></div>

            <form id='dateRange' action="">
              <div class="center">
                <input type="hidden" name="id" />
                Last
                <input name="days" value="" size="3" />
                days. From
                <input name="from" value="" size="30" />
                to
                <input name="to" value="" size="30" />
                <input type="submit" value="Redraw" />
              </div>
            </form>

          </div>

          <h1><a id='graphinfo' href='#'>See Graph Info</a></h1>

          <div id="data"></div>

    </div>

<script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jquery/1.3/jquery.js'></script>
<script type='text/javascript' src='http://plugins.jquery.com/files/jquery.query-2.1.2.js.txt'></script>
<!--[if IE]><script type="text/javascript" src="js/flot/excanvas.pack.js"></script><![endif]-->
<script type="text/javascript" src="js/flot/jquery.flot.js"></script>

<script type="text/javascript" src="http://jquery-ui.googlecode.com/svn/tags/1.7.1/ui/ui.core.js"></script>
<script type="text/javascript" src="http://jquery-ui.googlecode.com/svn/tags/1.7.1/ui/ui.datepicker.js"></script>

<script type="text/javascript" src='fullgraph.js'></script>
<script type="text/javascript" src='graph.js'></script>

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

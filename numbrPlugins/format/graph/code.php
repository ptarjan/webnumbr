<?php
print '<?xml version="1.0" encoding="UTF-8"?>';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>webNumbr</title>

    <style type="text/css">
html {
    margin : 0px;
    height : 100%;
    overflow : hidden;
    background : transparent;
}
body {
    height : 100%;
    margin : 0px;
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
    padding : 0px;
    font-size : 0.75em;
}
    </style>
  </head>
  <body>
    <div class="content">
        <div id="plot" class='center'>_______/\___|\___/\</div>
    </div>

<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
<!--[if IE]><script type="text/javascript" src="/numbrPlugins/format/graph/flot/excanvas.pack.js"></script><![endif]-->
<script type="text/javascript" src="/numbrPlugins/format/graph/flot/jquery.flot.js"></script>
<script type="text/javascript" src="/numbrPlugins/format/graph/graph.js"></script>
<script type="text/javascript">
$("plot").ready(function($) {
    makeGraph(<?php print json_encode($c) ?>, <?php print json_encode($data) ?>);
});
</script>

<?php include("ga.inc") ?>
  </body>
</html>

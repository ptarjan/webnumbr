<?php
if (! isset($_REQUEST['url'])) {
print '<?xml version="1.0" encoding="UTF-8"?>';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>webGraphr - Graph anything on the web</title>
    <link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/2.7.0/build/reset/reset-min.css" />
    <link rel="stylesheet" href="/style.css" type='text/css' />  
    <link rel="stylesheet" href="style.css" type='text/css' />  
    
    <link rel="icon" href="images/webGraphr-favicon.png" type="image/x-icon" />

  </head>
  <body>
    <div id='container'>
      <div id='header'>
        <a href='.'><img id='logo' src="images/webGraphr-banner-100.png" alt="logo" /></a>
      </div>

      <div class="content">

        <h1 id='start'>
          Start a Graph
        </h1>

        <form action='selectNode'>
          <div id="startForm"> 
            <label id="urlLabel" for="url">URL:</label>
            <input id="url" name='url' value="http://" />
            <input id="submitURL" type='submit' value='Pick the Number on the Page' />
          </div>
        </form>
      </div>
    </div>
<script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jquery/1.3/jquery.js'></script>
<script type='text/javascript'>
var resizeURL = function() {
    $("#url").width($("#startForm").outerWidth() - $("#urlLabel").outerWidth(true) - $("#submitURL").outerWidth(true) - 15);
};
$("#startForm").ready(resizeURL);
$(window).resize(resizeURL);
</script>
  </body>
</html>

<?php
    die();
}
if (! isset($_REQUEST['xpath'])) $_REQUEST['xpath'] = NULL;

require ("fetch.inc");

try {
    $data = fetch($_REQUEST['url'], $_REQUEST['xpath'], $type);
} catch (FetchException $e) {
    print "Fetch Exception: " . $e->getMessage(); 
    die();
}

$next = 'http://' . $_SERVER['SERVER_NAME'] . dirname($_SERVER['PHP_SELF']) . '/createGraph';

if ($type === "num") {
    if ($data) 
        die($data);
    else 
        die ("Something is wrong with the fetch. Check URL and XPATH");
}
else if ($type === "html") {

    $data = $data->saveXML();
    // Eliminate shorttags
    $data = preg_replace('/<\s*([a-z]+)\s([^>]*)\/>/', "<$1 $2></$1>", $data);

    $rep = '

    <!-- paulisageek.com/nodeSelector Added Code -->
    <base href="' . htmlspecialchars($_REQUEST['url']) . '" />
    <!-- paulisageek.com/nodeSelector End Added Code -->

    ';

    $data = preg_replace('/(.*<\s*[hH][eE][aA][dD]\s?[^>]*>)(.*)/', "$1" . $rep . "$2", $data, -1, $count);
    if ($count == 0) {
        $data = preg_replace('/(.*<\s*[hH][tT][mM][lL]\s?[^>]*>)(.*)/', "$1" . "<head>" . $rep . "</head>" . "$2", $data, -1, $count);
    }

    $rep = '

    <!-- paulisageek.com/nodeSelector Added Code -->
    <script>
    __nsDoneURL = "' . $next . '";
    </script>
    <script src="http://paulisageek.com' . dirname(dirname($_SERVER['PHP_SELF'])) . '/nodeSelector/ns.js" ></script>
    <!-- paulisageek.com/nodeSelector End Added Code -->

    ';

    $data = preg_replace('/(.*<\/\s*[bB][oO][dD][yY]\s?[^>]*>)(.*)/', "$1" . $rep . "$2", $data, -1, $count);
    if ($count == 0) {
        $data = preg_replace('/(.*<\/\s*[hH][tT][mM][lL]\s?[^>]*>)(.*)/', "$1" . "<head>" . $rep . "</head>" . "$2", $data, -1, $count);
    }

    print $data;
} else if ($type === "json") {
    require "/var/www/paul.slowgeek.com/header.php";
?>
<h1>JSON</h1>

<div>This data is in JSON format, and sadly I haven't yet figured out a good way to let you select the node. I've converted it to XML, and you can run an XPath on it, but sadly you can't just point your cursor at it.</div>
<div><a href="<?php print $next; ?>?url=<?php print htmlspecialchars(urlencode($_REQUEST["url"])) ?>">Continue and write yur own XPath.</a></div>

<div>
<pre>
<?php print htmlspecialchars($data->saveXML()); ?>
</pre>
</div>

<?php
    require "/var/www/paul.slowgeek.com/footer.php";
}
?>

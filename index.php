<?php
require ("db.inc");
print '<?xml version="1.0" encoding="UTF-8"?>';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>WebGrapher - Graph anything on the web</title>
    <link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/2.7.0/build/reset/reset-min.css" />
    <link rel="stylesheet" href="/style.css" type='text/css' />  
    <link rel="stylesheet" href="style.css" type='text/css' />  
    
    <link rel="icon" href="images/webGrapher-favicon.png" type="image/x-icon" />

  </head>
  <body>
    <div id='container'>
      <div id='header'>
        <a href='.'><img id='logo' src="images/webGrapher-banner-100.png" /></a>
      </div>

      <div id='content'>

        <h1 id='start'>
          Start a Graph
        </h1>

        <form action='selectNode.php'>
          <div> 
            URL: <input name='url' value="http://" style='width: 70%' />
            <input type='submit' value='Pick the Number on the Page' />
          </div>
        </form>

        <h1>Search All Graphs</h1>

        <form action="search.php">
          <div>
            <input name='query' style='width : 90%' />
            <input type="submit" value='Search' />
          </div>
        </form>

        <h1>Last 10 Graphs</h1>

        <div>
          <ul>
<?php
$stmt = $PDO->prepare("SELECT name, id, url FROM graphs ORDER BY createdTime DESC LIMIT 10");
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($data as $row) {
    $url = substr($row['url'], 0, 30);
    if (strlen($row['url']) > 30) $url .= "...";

    $name = substr($row['name'], 0, 50);
    if (strlen($row['name']) > 50) $name .= "...";

    print "            <li><a href='graph.php?id=" . htmlspecialchars($row['id']) . "'>" . htmlspecialchars($name) . "</a> (<span title='" . htmlspecialchars($row['url']) . "'>" . htmlspecialchars($url) . "</span>)</li>\n";
}
?>
          </ul>
        </div>

        <h1>Most Popular 10 Hosts</h1>

        <div>
          <ul>
<?php
$stmt = $PDO->prepare('SELECT COUNT(*) AS count, @START:=LOCATE("/", url)+1, @END:=LOCATE("/", url, @START+1), SUBSTRING(url, @START+1, IF(@END = 0, LENGTH(url)+1, @END) - @START-1) as domain FROM graphs GROUP BY domain ORDER BY count DESC LIMIT 10');
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($data as $row) {
    print "          <li><a href='search.php?query=" . htmlspecialchars($row['domain']) . "'>" . htmlspecialchars($row['domain']) . "</a> (" . htmlspecialchars($row['count']) . ")</li>\n";
}
?>
          </ul>
        </div>  
          
        <h1>What is This Site?</h1>

        <p>This site graphs things on the internet. If there is a web page that you want to know how it changes over time, just put the URL in the <a href='#start'>text box</a>, click on the number that you want graphed, name your graph, and then sit back and enjoy the pretty data.</p>
        <p>Once you like it, you can embed it in your site, or use the API to do something fancy with it, or just keep coming back to oooo and aaaahhh at how pretty my design is.</p>

        <h1>Suggestions? Bugs? Requests? Fan Mail?</h1>
      
        <div class='center'>
          <a href='mailto:webGrapher@paulisageek.com'>webGrapher@paulisageek.com</a>
        </div>

      </div>
    </div>
  </body>
</html>

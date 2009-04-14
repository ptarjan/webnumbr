<?php
print '<?xml version="1.0" encoding="UTF-8"?>';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>Web Grapher - Search Results for <?php print htmlentities($_REQUEST['query']) ?></title>
    <link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/2.7.0/build/reset/reset-min.css" />
    <link rel="stylesheet" href="/style.css" type='text/css' />  

    <link rel="shortcut icon" href="graph.ico" type="image/x-icon">
    <link rel="icon" href="graph.ico" type="image/x-icon">

  </head>
  <body>
    <div id='main'>

      <h1>Search Results</h1>

      <div class='content'>
        <form action=''>
            <input name='query' value='<?php print htmlentities($_REQUEST['query']) ?>' style='width:90%' />
            <input type='submit' value='Search' />
        </form>
        <div id='searchResults'>
          <ul class='searchresults'>

<?php
require("db.inc");
$stmt = $PDO->prepare("SELECT name, id, url FROM graphs WHERE name LIKE CONCAT('%', :query, '%') OR url LIKE CONCAT('%', :query, '%')");
$stmt->execute(array("query" => $_REQUEST['query']));

$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($data as $row) {
    $url = substr($row['url'], 0, 30);
    if (strlen($row['url']) > 30) $url .= "...";

    print "            <li><a href='graph.php?id=" . htmlentities($row['id']) . "'>" . htmlentities($row['name']) . "</a> (" . htmlentities($url) . ")</li>\n";
}
?>
          </ul>
        </div>

        Number of Results : <span id='numResults'><?php print $stmt->rowCount() ?></span>
      </div>
    </div>
  </body>
</html>

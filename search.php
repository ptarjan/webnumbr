<?php
print '<?xml version="1.0" encoding="UTF-8"?>';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>webNumbr - search results for <?php print htmlspecialchars($_REQUEST['query']) ?></title>
    <link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/2.7.0/build/reset/reset-min.css" />
    <link rel="stylesheet" href="/style.css" type='text/css' />  
    <link rel="stylesheet" href="style.css" type='text/css' />  

  </head>
  <body>
    <div id='container'>
      <div id='header'>
        <a href='.'><img id='smalllogo' src="images/webNumbr-banner-32.png" alt="logo" /></a>
      </div>

      <div class="content">

        <h1>Search Results</h1>

        <form action=''>
          <div>
              <input name='query' value='<?php print htmlspecialchars($_REQUEST['query']) ?>' style='width:90%' />
              <input type='submit' value='Search' />
          </div>
        </form>
        <div id='searchResults'>
          <ul class='searchresults'>
<?php
require("db.inc");
$stmt = $PDO->prepare("
SELECT 
    name, short(title, 50) as shorttitle, title, description, url, short(url, 30) as shorturl, is_fetching

FROM numbrs WHERE 

name LIKE CONCAT('%', :query, '%') OR 
url LIKE CONCAT('%', :query, '%') OR 
title LIKE CONCAT('%', :query, '%') OR 
description LIKE CONCAT('%', :query, '%')
");
$stmt->execute(array("query" => $_REQUEST['query']));
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($data as $row) {
?>
            <li>
              <a href="n/<?php print htmlspecialchars($row['name']) ?>">
                <?php print htmlspecialchars($row['name']) ?>

              </a>
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
        </div>

        <div>
          Number of Results : <span id='numResults'><?php print $stmt->rowCount() ?></span>. 
        </div>
        
      </div>
    </div>

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

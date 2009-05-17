<?php
$s = (int) $_REQUEST['s'];
$n = 10;
$s2 = $s + $n;
?>
<?php print '<?xml version="1.0" encoding="UTF-8"?>' ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>webNumr - Host List [0, 10)</title>
    <link rel="stylesheet" href="style.css" type='text/css' />  
  </head>
  <body>

    <div id='container'>
      <div id='header'>
        <a href='.'><img id='logo' src="images/webNumbr-banner-100.png" title="logo" alt="logo" /></a>
      </div>

      <div class='content'>

<!-- Start Content -->

        <h1 class="first">Known Hosts - [<?php print $s ?>, <?php print $s2 ?>)</h1>

        <ul>
<?php
require_once("db.inc");
$stmt = $PDO->prepare('SELECT COUNT(*) AS count, @START:=LOCATE("/", url)+1, @END:=LOCATE("/", url, @START+1), SUBSTRING(url, @START+1, IF(@END = 0, LENGTH(url)+1, @END) - @START-1) as domain FROM numbrs GROUP BY domain ORDER BY count DESC LIMIT :n OFFSET :s');
$stmt->bindValue(':s', $s, PDO::PARAM_INT);
$stmt->bindValue(':n', $n, PDO::PARAM_INT);
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($data as $row) {
    print "            <li><a href='search?query=" . htmlspecialchars($row['domain']) . "'>" . htmlspecialchars($row['domain']) . "</a> (" . htmlspecialchars($row['count']) . ")</li>\n";
}
?>
        </ul>
   
<?php if (count($data) == $n) { ?> 
        <p> <a href="?s=<?php print $s + $n ?>">Next page</a> </p>
<?php } ?>


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

<!-- End Content -->
      </div>
    </div>
  </body>
</html>

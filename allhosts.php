<?php 
$s = (int) $_REQUEST['s'];
$n = 10;
$s2 = $s + $n;
$title = "webNumr - Host List [$s, $s2)";
$logo = "images/webNumbr-banner-100.png";
?>
<?php require "/var/www/paul.slowgeek.com/header.php" ?>


        <h1>Known Hosts - [<?php print $s ?>, <?php print $s2 ?>)</h1>

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

<?php require "/var/www/paul.slowgeek.com/footer.php" ?>

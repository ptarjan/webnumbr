<?php 
$title = "webGraphr - help"; 
$header = array('    <link rel="icon" href="images/webGraphr-favicon.png" type="image/x-icon" />');
$logo = "images/webGraphr-banner-100.png";
?>
<?php require "/var/www/paul.slowgeek.com/header.php" ?>
<table>
<tr><td>
<p><a href='http://paulisageek.com/webGraphr'>webGraphr</a> lets anyone build a graph for any site on the internet. It started because I wanted to know how much flight prices were for my trip to <a href="http://en.wikipedia.org/wiki/St._Martin">Saint Martin</a>. Did you know they vary by about $100!</p>
<div class='center'>
<iframe style="width: 450px; height: 300px;" src="http://paulisageek.com/webGraphr/embedGraph?type=js&amp;id=3&amp;to=April+16" frameborder="0"></iframe>
</div>

</td><td>

<p>With a few clicks, you can keep track of anything. Here is number of people following cnn on <a href='http://twitter.com'>twitter</a>:</p>
<div class="center">
<iframe style="width: 450px; height: 300px;" src="http://paulisageek.com/webGraphr/embedGraph?type=js&amp;id=9" frameborder="0"></iframe>
</div>

</td></tr>
<tr><td>

<p>You can even combine a few graphs together. Here are 3 interesting numbers from a torrent on <a href='http://thepiratebay.org'>the pirate bay</a>. You can see the seeders lag behind the leechers since when a leecher finishes it becomes a seeder, neat. And the undulation is not inline with North America usage habits, and more close to Europe's day. Also, the seeds for the torrent are steadily declining, but the leechers are staying high. Data is fun!</p>
<div class="center">
<iframe style="width: 450px; height: 300px;" src="http://paulisageek.com/webGraphr/embedGraph?type=js&amp;id=10%2C21%2C22" frameborder="0"></iframe>
</div>

</td><td>

<p>And you can pick date ranges. You can see data from the last <b>n</b> days, a graph from a given time onwards, up to a given time or any combination (that makes sense).</p>

</td></tr>
</table>

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

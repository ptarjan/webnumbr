<?php print '<?xml version="1.0"?>' ?>
<rss version="2.0">
  <channel>
    <title><?php print htmlspecialchars($c['numbr']['title']) ?> - <?php print $c['code'] ?></title>
    <link>http://webnumbr.com/<?php print $c['code'] ?></link>
    <description><?php print htmlspecialchars($c['numbr']['description']) ?></description>

<?php
if (!is_array($data)) {
    $data = array(strtotime($c['numbr']['modifiedTime']), $data);
}

if (isset($params['count']))
    $count = (int) $params['count'];
else if (isset($params[0]))
    $count = (int) $params[0];
if (!is_numeric($count))
    $count = 10;

$data = array_slice($data, -1 * $count);
arsort($data);
foreach ($data as $row) {
    $time = $row[0];
    $value = $row[1];

    if ($value == null) continue;

    $link = "http://webnumbr.com/" . htmlspecialchars(preg_replace("/.rss\([^)]*\)/", "", $c['code']));
    $permlink = $link . ".at($time)";
?>
 
    <item>
    <title><?php print htmlspecialchars($c['numbr']['title']) ?> @ <?php print date("ga, M j Y", $time); ?></title>
      <link><?php print $link ?></link>
      <description><?php print $value ?></description>
      <pubDate><?php print date(DATE_RFC822, $time) ?></pubDate>
      <guid><?php print $permlink ?></guid>
    </item>

<?php } ?>
  </channel>
</rss>

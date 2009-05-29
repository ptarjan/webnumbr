<?php
$link = "http://webnumbr.com/" . htmlspecialchars(str_replace(".rss", "", $c['code']));
$time = strtotime($c['numbr']['modifiedTime']);
$permlink = $link . ".latest.to($time).from($time)";
?>
<?php print '<?xml version="1.0"?>' ?>
<rss version="2.0">
  <channel>
    <title><?php print htmlspecialchars($c['numbr']['title']) ?></title>
    <link>http://webnumbr.com</link>
    <description>Follow interesting numbers from anywhere on the web</description>
    <language>en-us</language>
    <generator>Weblog Editor 2.0</generator>
    <managingEditor>webnumbr.editor@paulisageek.com</managingEditor>
    <webMaster>webnumbr.webmaster@paulisageek.com</webMaster>
    <ttl>60</ttl>
 
    <item>
      <title><?php print htmlspecialchars($c['numbr']['title']) ?></title>
      <link><?php print $link ?></link>
      <description><?php print $data ?></description>
      <pubDate><?php print date(DATE_RFC822, $time) ?></pubDate>
      <guid><?php print $permlink ?></guid>
    </item>
  </channel>
</rss>

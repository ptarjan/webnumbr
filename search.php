<?php ob_start() ?>
<?php
if (!isset($_REQUEST['query']) || empty($_REQUEST['query']) || strlen($_REQUEST['query']) < 3) {
    $subtitle = "search";
    if (strlen($_REQUEST['query']) < 3) {
?>
      Search must be greater than 3 chars in length
<?php
    }
?>
<form id="search_form" action="/search"> 
    <div>
        <input type="text" name="query" value="" /> 
        <input type="submit" value="Search " />
    </div>
</form>
<?php
} else {
    $current_search = htmlspecialchars($_REQUEST['query']);
    $subtitle = "search : " . htmlspecialchars($_REQUEST['query']);

function cutzero($value) {
   return preg_replace("/(\.?)0+$/", "", $value);
}

?>
        <h3 class="first">Search Results for <tt><?php print $current_search ?></tt></h3>

        <div id='searchResults'>
          <ul class='searchresults'>
<?php
require("db.inc");
$stmt = $PDO->prepare("
SELECT 
    name, short(title, 100) as shorttitle, title, description, url, short(url, 100) as shorturl, is_fetching

FROM numbrs WHERE

name LIKE CONCAT('%', :query, '%') OR 
url LIKE CONCAT('%', :query, '%') OR 
title LIKE CONCAT('%', :query, '%') OR 
description LIKE CONCAT('%', :query, '%')

ORDER BY createdTime DESC
");
$stmt->execute(array("query" => $_REQUEST['query'])) || die(json_encode($stmt->errorInfo()));
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($data) == 0) {
    print '0 results. Try <a class="external" href="http://google.com/search?q=' . urlencode("site:webnumbr.com " . $_REQUEST['query']) . '">google</a>?';
}
foreach ($data as $row) {
    $sd = $PDO->prepare("
SELECT 
    data

FROM numbr_data WHERE

numbr = :name

ORDER BY timestamp DESC
");
    $sd->execute(array("name" => $row['name'])) || die(json_encode($stmt->errorInfo()));
    $data = $sd->fetchAll(PDO::FETCH_ASSOC);
    $data = $data[0]['data'];
    if (trim($data) == "") continue;
    $data = cutzero(number_format($data, 4, ".", ","));
?>
        <li>
            <div class="search_data">
                <a href="/<?php print htmlspecialchars($row['name']) ?>" class="<?php print $row['is_fetching'] ? 'is_fetching' : 'is_not_fetching' ?>">
                  <?php print htmlspecialchars($data) ?>
                </a>
            </div>
            <div class="search_title">
                <a href="/<?php print htmlspecialchars($row['name']) ?>" title="<?php print htmlspecialchars($row['title'])?>">
                    <?php print ($row['shorttitle'] == "" ? "&nbsp;" : htmlspecialchars($row['shorttitle'])) ?>
                </a>
            </div>
            <div class="search_url">
                <a href="<?php print htmlspecialchars($row['url']) ?>" title="<?php print htmlspecialchars($row['url'])?>">
                    <?php print htmlspecialchars($row['shorturl']) ?>
                </a>
            </div>
       </li>
<?php
}
?>
          </ul>
        </div>

      </div>
<?php 
} // End of empty check
?>
<?php $content = ob_get_clean(); require("template.php"); ?>

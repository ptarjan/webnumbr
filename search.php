<?php
    $current_search = htmlspecialchars($_REQUEST['query']);
    $subtitle = "search : " . htmlspecialchars($_REQUEST['query']);
?>
<?php ob_start() ?>
        <h3 class="first">Search Results for <tt><?php print $current_search ?></tt></h3>

        <div id='searchResults'>
          <ul class='searchresults'>
<?php
require("db.inc");
$stmt = $PDO->prepare("
SELECT 
    name, short(title, 100) as shorttitle, title, description, url, short(url, 100) as shorturl

FROM numbrs WHERE

name LIKE CONCAT('%', :query, '%') OR 
url LIKE CONCAT('%', :query, '%') OR 
title LIKE CONCAT('%', :query, '%') OR 
description LIKE CONCAT('%', :query, '%')
");
$stmt->execute(array("query" => $_REQUEST['query'])) || die(json_encode($stmt->errorInfo()));
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($data as $row) {
    $sd = $PDO->prepare("
SELECT 
    data

FROM numbr_data WHERE

numbr = :name

ORDER BY timestamp DESC
LIMIT 1
");
    $sd->execute(array("name" => $row['name'])) || die(json_encode($stmt->errorInfo()));
    $data = $sd->fetchAll(PDO::FETCH_ASSOC);
    $data = $data[0]['data'];
    if (trim($data) == "") continue;
?>
        <li>
            <div class="search_data">
                <a href="<?php print htmlspecialchars($row['name']) ?>">
                    <?php print htmlspecialchars($data) ?>
                </a>
            </div>
            <div class="search_title" title="<?php print htmlspecialchars($row['title'])?>">
                <a href="<?php print htmlspecialchars($row['name']) ?>">
                    <?php print htmlspecialchars($row['shorttitle']) ?>
                </a>
            </div>
            <div class="search_url" title="<?php print htmlspecialchars($row['url'])?>">
                <a href="<?php print htmlspecialchars($row['url']) ?>">
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
<?php $content = ob_get_clean(); require("template.php"); ?>

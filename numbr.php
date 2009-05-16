<?php

$ops = explode(".", $_REQUEST["name"]);
$name = array_shift($ops);

require "db.inc";

if (!isset($_REQUEST["format"])) $_REQUEST["format"] = "html";

$limit = 1;
while (count($ops) > 0) {
    $op = array_unshift($ops);
    switch ($op) {
        case "all" :
            $limit = PHP_INT_MAX;
    }
}
$s = $PDO->prepare("SELECT data, timestamp FROM numbr_data WHERE numbr = :name ORDER BY timestamp DESC LIMIT :limit");
$s->bindValue("limit", $limit);
$s->bindValue("name", $name);
$s->execute();
$result = $s->fetchAll(PDO::FETCH_ASSOC);
$data = NULL;
if (count($result) == 1) {
    $data = (float) $result[0]['data'];
} else {
    // Its an array
    foreach ($result as $ind => $row) {
        $data[] = array((int) $row['timestamp'], (float) $row['data']);
    }
}

switch ($_REQUEST["format"]) {
    case "json" :
        if (isset($_REQUEST["callback"])) {
            print $_REQUEST["callback"] . "(" . json_encode($data) . ")";
        } else {
            print json_encode($data);
        }
    case "raw" :
        print $data;
    case "xml" :
        require "XMLHelper.inc";
        print XMLHelper::xml_encode($data);
    default :
    case "html" :
?>
<?php print '<?xml version="1.0" encoding="UTF-8"?>' ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>webNumr : webnumbrs</title>
    <link rel="stylesheet" href="style.css" type='text/css' />  
    <style>
    .webNumbr {
        width: 600px;
        margin : 20px;
        padding : 10px;
        background-color : white;
        border : 1px dotted;    
    }
    </style>

  </head>
  <body>

    <div id='container'>
      <div id='header'>
        <a href='.'><img id='logo' src="../images/webNumbr-banner-100.png" title="webNumr" alt="webNumbr logo" /></a>
      </div>

      <div class='content'>
<!-- Start Content -->
<div class="center" style="width: 100%">
<div class="webNumbr center">
<?php print json_encode($data); ?>

</div>
</div>

<!-- End Content -->
      </div>
    </div>
  </body>
</html>

<?php
        break;
};
?>

<?php
if (isset($_REQUEST["format"])) {
    $format = preg_replace("/[^a-z]/", "", $_REQUEST["format"]);
} else {
    $format = "html";
}

include("numbrPlugins/format/{$format}/code.php");
?>

<?php
$name = str_replace(".embed", "", $c['code']);

if (is_string($data)) {
    // It is probably already formatted so we should iframe it
    print "<iframe src=\"http://webnumbr.com/{$name}\" style=\"width: 100%; height: 400px;\" allowtransparency=\"true\" frameborder=\"0\"></iframe>";
} else {
    $id = "webNumbr_" . htmlentities($c['name']);
    $function = "webNumbr_" . htmlentities(str_replace("-", "_", $c['name']));

    print "<span id=\"$id\">" . json_encode($data) . "</span><script>var $function = function(data) { document.getElementById(\"$id\").innerHTML = data; }</script><script src=\"http://webnumbr.com/{$name}.json(callback=$function)\"></script>";
}
?>

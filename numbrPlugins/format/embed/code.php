<?php
$id = "webNumbr_" . htmlentities($c['name']);
$function = "webNumbr_" . htmlentities(str_replace("-", "_", $c['name']));
$name = str_replace(".embed", "", $c['code']);

print "<span id=\"$id\">" . json_encode($data) . "</span><script>var $function = function(data) { document.getElementById(\"$id\").innerHTML = data; }</script><script src=\"http://webnumbr.com/{$name}.json(callback=$function)\"></script>";

?>

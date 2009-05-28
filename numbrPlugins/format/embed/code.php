<?php
$id = "webNumbr_" . htmlentities($c['name']);
$function = "webNumbr_" . htmlentities($c['name']);

print "<span id=\"$id\">" . json_encode($data) . "</span><script>var $function = function(data) { document.getElementById(\"$id\").innerHTML = data; }</script><script src=\"http://webnumbr.com/{$c['code']}.json(callback=$function)\"></script>";

?>

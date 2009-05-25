<?php
$id = "webNumbr-" . htmlentities($c['name']);
?>
<span id="<?php print $id; ?>"><?php print (is_string($data) ? htmlspecialchars($data) : json_encode($data)) ?></span><script>var webnumbr = function(data) { document.getElementById("<?php print $id ?>").innerHTML = data; }</script><script src="http://webnumbr.com/<?php print $c['code'] ?>.json(callback=webnumbr)"></script>

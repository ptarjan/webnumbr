<?php
if (! isset($_REQUEST['url'])) die("Need URL parameter in query string");
if (! isset($_REQUEST['xpath'])) $_REQUEST['xpath'] = NULL;

require ("fetch.inc");

try {
    $data = fetch($_REQUEST['url'], $_REQUEST['xpath']);
} catch (FetchException $e) {
    print "Fetch Exception: " . $e->getMessage(); 
    die();
}

if (is_string($data)) {
    if ($data) 
        die($data);
    else 
        die ("Something is wrong with the fetch. Check URL and XPATH");
}

$data = $data->saveXML();

$rep = '

<!-- Added Code -->
<base href="' . htmlspecialchars($_REQUEST['url']) . '" />
<script>
__nsDoneURL = "http://' . $_SERVER['SERVER_NAME'] . dirname($_SERVER['PHP_SELF']) . '/createGraph";
</script>
<script src="http://paulisageek.com/nodeSelector/ns.js" ></script>
<!-- End Added Code -->

';

$data = preg_replace('/(.*<\s*[hH][eE][aA][dD]\s?[^>]*>)(.*)/', "$1" . $rep . "$2", $data, -1, $count);
if ($count == 0) {
    $data = preg_replace('/(.*<\s*[hH][tT][mM][lL]\s?[^>]*>)(.*)/', "$1" . "<head>" . $rep . "</head>" . "$2", $data, -1, $count);
}

print $data;

?>

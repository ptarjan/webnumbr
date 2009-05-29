<?php
// Hard parsing
if (count($params) == 1 && isset($params[0])) {
    $params['url'] = $params[0];
}
if (isset($params['url_encoded'])) 
    $params['url'] = urldecode($params['url_encoded']);

$url = $params['url'];

// HACK FOR RASMUS'S SERVER
$url = str_replace("http:/", "http://", $url);
// $url .= (strpos("?", $url) === FALSE ? '?' : '&');
// $url .= http_build_query(array("c" => $c, "data" => $data, "params" => $params));

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_POST, TRUE);
curl_setopt($ch, CURLOPT_POSTFIELDS, array("c" => $c, "data" => $data, "params" => $params));
$data = curl_exec($ch);

$json = json_decode($data, TRUE);
$bad = FALSE;
if ($json === FALSE) {
    // Leave $data as the curled data
} else if (!isset($json['data']) || !isset($json['c'])) { 
    $bad = TRUE;
}
if ($bad) {
    $data = "$url returned '$data'. Expected json {\"data\":3.14159,\"c\":{\"key\":\"value\"}}";
} else {
    $data = $json['data'];
    foreach ($json['c'] as $k => $v)
        $c[$k] = $v;
}
?>

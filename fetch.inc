<?php

class FetchException extends Exception {}

function fetch($url, $xpath = NULL) {
    if (is_string($url)) {
        $ch = curl_init($url);
        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_USERAGENT => "webGraphr 1.0 http://paulisageek.com/webGraphr/",
        );
        curl_setopt_array($ch, $options);
        $data = curl_exec($ch);

        $config = array(
                        'output-xml' => TRUE,
                        'wrap' => 0,
                       );
        $data = tidy_repair_string ($data, $config);

        $data = preg_replace("/xmlns='[^']*'/", "", $data);
        $data = preg_replace('/xmlns="[^"]*"/', "", $data);

        if (!function_exists("fetch_preg_lowercase")) {
            function fetch_preg_lowercase($matches) {
                return "<" . $matches[1] . strtolower($matches[2]);
            }
        }
        $data = preg_replace_callback("/< *(\/ *)?(\w+)/", "fetch_preg_lowercase", $data);

        $dom = new DomDocument;
        $dom->recover = true;

        libxml_use_internal_errors(true);
        $ret = @$dom->loadXML($data);
        libxml_use_internal_errors(false);

        if (!$ret) {
            throw new FetchException("Can't load xml");
        }
        if ($xpath == NULL) return $dom;

    } else if ($url instanceof DomDocument) {
        $dom = $url;
    } else {
        throw new FetchException("Expected a string or a DomDocument as first parameter");
    }

    $domxpath = new DomXPath($dom);
    $nl = $domxpath->query($xpath);
    if ($nl->length == 0) {
        throw new FetchException("No nodes selected");
    } else if ($nl->length > 1) {
        // throw new FetchException(" entries for xpath", "warning");
    }
    $num = $nl->item(0)->nodeValue;

    // Number matches
    preg_match("/([0-9,]+[.]?[0-9]*)/", $num, $matches);
    if (isset($matches[1])) $num = $matches[1];
    $num = str_replace(",", "", $num);

    if (!is_numeric($num)) {
        throw new FetchException("Not a number: $num");
    }
    return $num;
}


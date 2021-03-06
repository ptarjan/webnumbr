<?php

class FetchException extends Exception {}

function noLineErrorHandler( $errno, $errstr, $errfile, $errline, $errcontext ) {
    throw new FetchException($errstr);
}
require("XMLHelper.inc");

function cache($url,$ttl,$prefix='', &$headers) {
    $headers = array();
    $tmp = '/var/tmp/webnumbr/'.$prefix.md5($url);
    if(file_exists($tmp)) $st = stat($tmp);
    else $st = false;
    if(!$st || $st && ($st['mtime']<($_SERVER['REQUEST_TIME']-$ttl))) {
        if($st) touch($tmp);

        $opts = array(
          'http'=>array(
            'timeout' => 10,
            'max_redirects' => 10,
            'user_agent' => "webnumbrFetcher/1.0 (http://webnumbr.com/)",
          )
        );

        $context = stream_context_create($opts);

        $stream = fopen($url,'r', false, $context);
        if(!$stream) {
          if($st) return $tmp;
          return false;
        }
        $tmpf = tempnam('/var/tmp/webnumbr','tmp');
        $tmpstream = fopen($tmpf, 'w');
        $bytes = stream_copy_to_stream($stream, $tmpstream, 1000001);
        if ($bytes == 1000001)
            throw new FetchException("Documents larger than 1000000 bytes are unsupported");

        $meta = (stream_get_meta_data($stream));
        $headers = $meta['wrapper_data'];
        fclose($stream);

        fclose($tmpstream);
        rename($tmpf, $tmp);
    } else $bytes = $st['size'];
    return file_get_contents($tmp);
}

function buildDom($data) {

    $data = preg_replace("/xmlns='[^']*'/", "", $data);
    $data = preg_replace('/xmlns="[^"]*"/', "", $data);
    $data = trim($data);

    // To tell libxml that this doc is in utf8 and not latin1
    if (substr($data, 0, 5) !==  '<?xml') {
        $data = '<?xml version="1.0" encoding="utf8"?' . '>' . $data;
    }
    $dom = new DomDocument();
    $dom->recover = true;
    $dom->preserveWhitespace = FALSE;
    $dom->formatOutput = true;
    libxml_use_internal_errors(true);
    $ret = @$dom->loadXML($data);
    libxml_use_internal_errors(false);
    if (!$ret) {
        throw new FetchException("Can't load xml");
    }
    return $dom;
}

/** The same as curl_exec except tries its best to convert the output to utf8 **/
function curl_exec_utf8($ch) {
    $data = curl_exec($ch);
    if (!is_string($data)) return $data;

    unset($charset);
    $content_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);

    /* 1: HTTP Content-Type: header */
    preg_match( '@([\w/+]+)(;\s*charset=(\S+))?@i', $content_type, $matches );
    if ( isset( $matches[3] ) )
        $charset = $matches[3];

    /* 2: <meta> element in the page */
    if (!isset($charset)) {
        preg_match( '@<meta\s+http-equiv="Content-Type"\s+content="([\w/]+)(;\s*charset=([^\s"]+))?@i', $data, $matches );
        if ( isset( $matches[3] ) )
            $charset = $matches[3];
    }

    /* 3: <xml> element in the page */
    if (!isset($charset)) {
        preg_match( '@<\?xml.+encoding="([^\s"]+)@si', $data, $matches );
        if ( isset( $matches[1] ) )
            $charset = $matches[1];
    }

    /* 4: PHP's heuristic detection */
    if (!isset($charset)) {
        $encoding = mb_detect_encoding($data);
        if ($encoding)
            $charset = $encoding;
    }

    /* 5: Default for HTML */
    if (!isset($charset)) {
        if (strstr($content_type, "text/html") === 0)
            $charset = "ISO 8859-1";
    }

    /* Convert it if it is anything but UTF-8 */
    /* You can change "UTF-8"  to "UTF-8//IGNORE" to 
       ignore conversion errors and still output something reasonable */
    if (isset($charset) && strtoupper($charset) != "UTF-8")
        $data = iconv($charset, 'UTF-8', $data);

    return $data;
}

function fetch($url, $xpath = NULL, &$type = "", &$finalURL = NULL) {
    if (is_string($url)) {
        $ch = curl_init($url);
        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_USERAGENT => "webnumbrFetcher/1.0 (http://webnumbr.com/)",
            CURLOPT_ENCODING => "gzip",
        );
        curl_setopt_array($ch, $options);
        $data = curl_exec_utf8($ch);
        if (strlen($data) > 1000000)
            throw new FetchException("Document larger than 1000000 bytes unsupported");
        
        $finalURL = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    
        $ct = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        $boom = explode(";", $ct);
        $ct = $boom[0];

        /*
        $data = cache($url, 30, '', $headers);
        $ct = "";
        foreach ($headers as $header) {
            $boom = explode(":", $header, 2);
            if (strtolower($boom[0]) == "content-type")
                $ct = $boom[1];
        }
        $finalURL = $url;
        */

        switch ($ct) {
            case "application/xml":
            case "text/xml":
            case "application/rss+xml":
            case "application/atom+xml":
            case "application/rdf+xml":
            case "application/xhtml+xml":
                $dom = buildDom($data);
                $type = "xml";
                break;

            default :
                $json = json_decode($data, TRUE);
                if ($json !== NULL) {
                    $data = XMLHelper::xml_encode($json, 0, "json");

                    $dom = buildDom($data);
                    $type = "json";
                } else {
                    $config = array(
                                    'output-xhtml' => TRUE,
                                    'wrap' => 0,
                                   );

                    if (!function_exists("fetch_preg_lowercase")) {
                        function fetch_preg_lowercase($matches) {
                            return "<" . $matches[1] . strtolower($matches[2]);
                        }
                    }

                    $data = preg_replace_callback("/<\s*(\/\s*)?(\w+)/", "fetch_preg_lowercase", $data);
                    $data = tidy_repair_string ($data, $config, 'utf8');

                    $dom = buildDom($data);

                    $type = "html";
                }

        } // end switch

        if ($xpath === NULL) 
            return $dom;

    } else if ($url instanceof DomDocument) {
        $dom = $url;
    } else {
        throw new FetchException("Expected a string or a DomDocument as first parameter");
    }

    $domxpath = new DomXPath($dom);

    // Namespace finder
    $sxe = simplexml_import_dom($dom);
    $namespaces = $sxe->getNamespaces(true);
    foreach ($namespaces as $prefix => $ns) {
        $domxpath->registerNamespace($prefix, $ns);
    }

    set_error_handler("noLineErrorHandler");
    $nl = $domxpath->evaluate($xpath);
    restore_error_handler();

    if ($nl instanceof DOMNodeList) {
        if ($nl->length == 0) {
            throw new FetchException("No data at that location. Bad XPath?");
        } else if ($nl->length > 1) {
            // throw new FetchException(" entries for xpath", "warning");
        }
        $num = $nl->item(0)->nodeValue;
    } else {
        $num = $nl;
    }
    $oldnum = $num;

    // Number matches
    preg_match("/(-?\s*[0-9]+[0-9, ]*\.?[0-9]*)/", $num, $matches);
    if (isset($matches[1])) $num = $matches[1];
    $num = str_replace(",", "", $num);
    $num = str_replace(" ", "", $num);

    if (!is_numeric($num)) {
        throw new FetchException("Not a number: $oldnum");
    }
    $type = "num";
    return $num;
}


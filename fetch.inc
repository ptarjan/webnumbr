<?php

class FetchException extends Exception {}

function noLineErrorHandler( $errno, $errstr, $errfile, $errline, $errcontext ) {
    throw new FetchException($errstr);
}

class XMLHelper {
    public static function is_vector( &$array ) {
        if ( !is_array($array) || empty($array) ) {
            return false;
        }
        $next = 0;
        foreach ( $array as $k => $v ) {
            if ( $k !== $next ) return false;
            $next++;
        }
        return true;
    }
    public static function xml_encode_node ($string) {
        $string = (string) $string;
        if (strlen($string) === 0) return "_";
        if (preg_match("/[0-9]/", $string[0])) 
            $string = "_" . $string;
        return preg_replace("/[^0-9a-zA-Z-.]/", "_", $string);
    }

    public static function xmlspecialchars($text) {
        return str_replace('&#039;', '&apos;', htmlspecialchars($text, ENT_QUOTES));
    }

    public static function xml_encode($array, $i=0, $root = "_") {
        if(!$i) {
            $data = '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . '<' . self::xml_encode_node($root) . ">";
        } else {
            $data = '';
        }

        if (!is_array($array))  {
            $data .= self::xmlspecialchars($array);
        } else {
            $vector = false;
            if (self::is_vector($array))
                $vector = true;

            foreach($array as $k=>$v) {
                if (self::is_vector($v)) {
                    $data .= self::xml_encode($v, $i, $k);
                    continue;
                }

                if ($vector) $k = $root;

                $data .= "\n";
                $data .= str_repeat("\t", $i);
                $data .= "<" . self::xml_encode_node($k) . ">";
                if(is_object($v) || is_array($v)) {
                    $data .= self::xml_encode($v, ($i+1), $k);
                } else {
                    $dom = @DomDocument::loadXML($v);
                    if ($dom){
                        $xml = $dom->saveXML();
                        $xml = preg_replace("/<\?xml[^>]*>/", "", $xml);
                        $data .= self::xml_encode($xml, ($i+1), $k);
                    } else {
                        $data .= self::xml_encode($v, ($i+1), $k);
                    }
                }
                $data .= '</' . self::xml_encode_node($k) . ">";
            }
        }
        if(!$i) {
            $data .= '</' . self::xml_encode_node($root) . ">\n";
        }

        return $data;
    }
}

function fetch($url, $xpath = NULL, &$type = "") {
    if (is_string($url)) {
        $ch = curl_init($url);
        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_USERAGENT => "webGraphr 1.0 http://paulisageek.com/webGraphr/",
            CURLOPT_ENCODING => "gzip",
        );
        curl_setopt_array($ch, $options);
        $data = curl_exec($ch);

        $ct = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        $boom = explode(";", $ct);
        $ct = $boom[0];
        switch ($ct) {
            case "application/xml":
            case "text/xml":
            case "application/rss+xml":
            case "application/atom+xml":
            case "application/rdf+xml":
            case "application/xhtml+xml":
                $data = preg_replace("/xmlns='[^']*'/", "", $data);
                $data = preg_replace('/xmlns="[^"]*"/', "", $data);
                $dom = new DomDocument;
                $dom->preserveWhiteSpace = FALSE;
                $dom->formatOutput = TRUE;
                $dom->loadXML($data);
                $type = "xml";
                break;
            default :
    
        $json = json_decode($data, TRUE);
        if ($json) {
            $data = XMLHelper::xml_encode($json, 0, "json");
            $dom = new DomDocument;
            $dom->preserveWhiteSpace = FALSE;
            $dom->formatOutput = TRUE;
            $dom->loadXML($data);
            $type = "json";
            /*
            $data = $dom->saveXML();
            header("Content-type: application/xml");
            die($data);
            $data = htmlspecialchars($data);
            $data = str_replace("\n", "</div><div>", $data);
            $data = str_replace(" ", "&nbsp;", $data);
            $data = "<html><body><div>" . $data . "</div></body></html>";
            */
        } else {
            $config = array(
                            'output-xhtml' => TRUE,
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

            $data = preg_replace_callback("/<\s*(\/\s*)?(\w+)/", "fetch_preg_lowercase", $data);

            $dom = new DomDocument;
            $dom->recover = true;
            libxml_use_internal_errors(true);
            $ret = @$dom->loadXML($data);
            libxml_use_internal_errors(false);
            if (!$ret) {
                throw new FetchException("Can't load xml");
            }
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
            throw new FetchException("No nodes selected");
        } else if ($nl->length > 1) {
            // throw new FetchException(" entries for xpath", "warning");
        }
        $num = $nl->item(0)->nodeValue;
    } else {
        $num = $nl;
    }

    // Number matches
    preg_match("/([0-9,]+[.]?[0-9]*)/", $num, $matches);
    if (isset($matches[1])) $num = $matches[1];
    $num = str_replace(",", "", $num);

    if (!is_numeric($num)) {
        throw new FetchException("Not a number: $num");
    }
    $type = "num";
    return $num;
}


<?php
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
?>

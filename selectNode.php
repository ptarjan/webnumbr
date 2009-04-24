<?php
if (! isset($_REQUEST['url'])) {
print '<?xml version="1.0" encoding="UTF-8"?>';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>webGraphr - Graph anything on the web</title>
    <link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/2.7.0/build/reset/reset-min.css" />
    <link rel="stylesheet" href="/style.css" type='text/css' />  
    <link rel="stylesheet" href="style.css" type='text/css' />  
    
    <link rel="icon" href="images/webGraphr-favicon.png" type="image/x-icon" />

  </head>
  <body>
    <div id='container'>
      <div id='header'>
        <a href='.'><img id='logo' src="images/webGraphr-banner-100.png" alt="logo" /></a>
      </div>

      <div class="content">

        <h1 id='start'>
          Start a Graph
        </h1>

        <form action='selectNode'>
          <div id="startForm"> 
            <label id="urlLabel" for="url">URL:</label>
            <input id="url" name='url' value="http://" />
            <input id="submitURL" type='submit' value='Pick the Number on the Page' />
          </div>
        </form>
      </div>
    </div>
<script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jquery/1.3/jquery.js'></script>
<script type='text/javascript'>
var resizeURL = function() {
    $("#url").width($("#startForm").outerWidth() - $("#urlLabel").outerWidth(true) - $("#submitURL").outerWidth(true) - 15);
};
$("#startForm").ready(resizeURL);
$(window).resize(resizeURL);
</script>
  </body>
</html>

<?php
    die();
}
if (! isset($_REQUEST['xpath'])) $_REQUEST['xpath'] = NULL;

require ("fetch.inc");

try {
    $data = fetch($_REQUEST['url'], $_REQUEST['xpath'], $type, $finalURL);
} catch (FetchException $e) {
    print "Fetch Exception: " . $e->getMessage(); 
    die();
}

$next = 'http://' . $_SERVER['SERVER_NAME'] . dirname($_SERVER['PHP_SELF']) . '/createGraph';

if ($type === "num") {
    die($data);
}
else if ($type === "html") {

    $data = $data->saveXML();
    // Eliminate shorttags
    $data = preg_replace('/<\s*([a-z]+)\s([^>]*)\/>/', "<$1 $2></$1>", $data);

    $rep = '

    <!-- paulisageek.com/nodeSelector Added Code -->
    <base href="' . htmlspecialchars($_REQUEST['url']) . '" />
    <!-- paulisageek.com/nodeSelector End Added Code -->

    ';

    $data = preg_replace('/(.*<\s*[hH][eE][aA][dD]\s?[^>]*>)(.*)/', "$1" . $rep . "$2", $data, -1, $count);
    if ($count == 0) {
        $data = preg_replace('/(.*<\s*[hH][tT][mM][lL]\s?[^>]*>)(.*)/', "$1" . "<head>" . $rep . "</head>" . "$2", $data, -1, $count);
    }

    $rep = '

    <!-- paulisageek.com/nodeSelector Added Code -->
    <script>
    if (typeof paulisageek == "undefined") { paulisageek = {}; }
    if (typeof paulisageek.ns == "undefined") { paulisageek.ns = {}; }
    paulisageek.ns.doneURL = "' . $next . '";
    paulisageek.ns.params = "' . preg_replace('/"/', '\"', (json_encode(array("url" => $finalURL)))) . '";
    </script>
    <script src="http://paulisageek.com' . dirname(dirname($_SERVER['PHP_SELF'])) . '/nodeSelector/ns.js" ></script>
    <!-- paulisageek.com/nodeSelector End Added Code -->

    ';

    $data = preg_replace('/(.*<\/\s*[bB][oO][dD][yY]\s?[^>]*>)(.*)/', "$1" . $rep . "$2", $data, -1, $count);
    if ($count == 0) {
        $data = preg_replace('/(.*<\/\s*[hH][tT][mM][lL]\s?[^>]*>)(.*)/', "$1" . "<head>" . $rep . "</head>" . "$2", $data, -1, $count);
    }

    print $data;
} else {

/** 
    Old code that I can't really part with. This does DOM operations instead of the regular expressions, but 
    sadly our xml library couldn't indent it properly
function doNamespace($node, &$knownNamespaces) {
    if ($node->namespaceURI) {
        $boom = explode(":", $node->nodeName, 2);
        $prefix = $boom[0]; // stupid PHP not putting this on the previous line *grumble*
        if (!isset($knownNamespaces[$prefix])) {
            $knownNamespaces[$prefix] = $node->namespaceURI;
            return " xmlns:{$prefix}=\"{$node->namespaceURI}\"";
        }
    }
    return false;
}

function saveXML($node, $level = 0, $namespaces = array()){
    $r = "";
    switch ($node->nodeType) {
        case XML_ELEMENT_NODE :
            $r .= "<" . $node->nodeName;
            $r .= doNamespace($node, $namespaces);
            if ($node->hasAttributes()) {
                foreach ($node->attributes as $attrName => $attrNode) {
                    $r .= doNamespace($attrNode, $namespaces);
                    $r .= saveXML($attrNode);
                }
            }
            $r .= ">";
            if ($node->hasChildNodes()) {
                foreach ($node->childNodes as $child) {
                    $r .= saveXML($child, $level+1, $namespaces);
                }
            }
            $r .= "</" . $node->nodeName . ">";
            break;
        case XML_ATTRIBUTE_NODE :
            $r .= " {$node->nodeName}=\"" . htmlspecialchars($node->nodeValue) . "\"";
            break;
        case XML_TEXT_NODE :
            $r .= htmlspecialchars($node->nodeValue);
            break;
        case XML_CDATA_SECTION_NODE :
            $r .= "<![CDATA[{$node->nodeValue}]]>";
            break;
        case XML_COMMENT_NODE :
            $r .= "<!--{$node->nodeValue}-->";
            break;
        case XML_DOCUMENT_NODE :
            if ($node->hasChildNodes()) {
                foreach ($node->childNodes as $child) {
                    $r .= saveXML($child, $level+1, $namespaces);
                }
            }
            break;
        case XML_PI_NODE :
            $r .= "<?{$node->nodeName} {$node->nodeValue} ?".">";
            break;
        case XML_DOCUMENT_TYPE_NODE :
            $r .= $node->internalSubset;
            break;
        default :
            $r .= "?Node type not implemented: {$node->nodeType}?";
            break;
    }
    return $r;
}

function encodeXML($node, $doc = NULL, $level=0) {
    switch ($node->nodeType) {
        case XML_ELEMENT_NODE :
            $xmlNode = $doc->createElement("xml_{$node->nodeName}");
            $xmlNode->appendChild(
                $doc->createTextNode("<{$node->nodeName}")
            );
            if ($node->hasAttributes()) {
                foreach ($node->attributes as $attrName => $attrNode) {
                    $xmlAttrNode = $xmlNode->ownerDocument->createElement("xmlat_" . str_replace(":", "__colon__", $attrNode->nodeName));
                    $style = $doc->createAttribute("style");
                    $style->appendChild(
                        $doc->createTextNode("color:blue")
                    );
                    $xmlAttrNode->appendChild($style);
                    $xmlAttrNode->appendChild(
                        $doc->createTextNode(
                            " {$attrNode->nodeName}=\"{$attrNode->nodeValue}\""
                        )
                    );
                    $xmlNode->appendChild($xmlAttrNode);
                }
            }
            $xmlNode->appendChild(
                $doc->createTextNode(">")
            );
            if ($node->hasChildNodes()) {
                $first = TRUE;
                foreach ($node->childNodes as $child) {
                    $childNode = encodeXML($child, $doc, $level+1);
                    if ($childNode) {
                        if ($first) {
                            $first = FALSE;
                        }
                        $xmlNode->appendChild($childNode);
                    }
                }
            }
            $xmlNode->appendChild(
                $doc->createTextNode("</{$node->nodeName}")
            );
            $xmlNode->appendChild(
                $doc->createTextNode(">")
            );
            return $xmlNode;
            break;
        case XML_ATTRIBUTE_NODE :
            break;
        case XML_TEXT_NODE :
            return $doc->importNode($node, TRUE);
            break;
        case XML_CDATA_SECTION_NODE :
            return $doc->createTextNode(
                $node->nodeValue
            );
            return $doc->importNode($node, TRUE);
            break;
        case XML_COMMENT_NODE :
            return $doc->importNode($node, TRUE);
            break;
        case XML_DOCUMENT_NODE :
            $newDom = new DomDocument();
            $newDom->formatOutput = TRUE;
            $newDom->preserveWhitespace = FALSE;
            if ($node->hasChildNodes()) {
                foreach ($node->childNodes as $child) {
                    $newChild = encodeXML($child, $newDom);
                    if ($newChild) {
                        $newDom->appendChild(
                            $newChild
                        );
                    }
                }
            }
            return $newDom;
            break;
        case XML_PI_NODE :
            break;
        case XML_DOCUMENT_TYPE_NODE :
            break;
        default :
            print "!?Node type not implemented: {$node->nodeType}?!";
            break;
    }
    return $r;
}

$data = encodeXML($data);
**/

$xml = $data->saveXML($data);

if (isset($_REQUEST['format']) && $_REQUEST['format'] == "xml")  {
    header("Content-type: text/xml");
    die($xml);
}

require "/var/www/paul.slowgeek.com/header.php";
?>
<h1>Data Document</h1>

<div>
<pre id="xml">
<?php 

// Start nodes
$xml = preg_replace(",<([^>!?/\s][^>\s]*)(\s[^>]+)?([^>/])\s*>,", "<xml_$1$2$3>&lt;$1$2$3&gt;", $xml);
// 1 char start tags
$xml = preg_replace(",<([^>/])\s*>,", "<xml_$1>&lt;$1&gt;", $xml);
// End nodes
$xml = preg_replace(",</([^/>]+)\s*>,", "&lt;$1&gt;</xml_$1>", $xml);
// Short tags
$xml = preg_replace(",<([^>!?/\s]+)(\s[^>]+)?\s*/>,", "<xml_$1$2>&lt;$1$2 /&gt;</xml_$1>", $xml);

// Attributes
function markupAttr($matches) {
    return preg_replace(",([^\s=]+)\s*=\s*(\'[^<\']*\'|\"[^<\"]*\"),", "<xmlattr_$1>$1=$2</xmlattr_$1>", $matches[0]);
}
// Encoded nodes
$xml = preg_replace_callback(",&lt;.*?&gt;,", "markupAttr", $xml);
function htmlspecialchars_callback($matches) {
    return htmlspecialchars($matches[0]);
}

// Comments
$xml = preg_replace_callback(",<!--.*?-->,", 'htmlspecialchars_callback', $xml);
// Declaration
$xml = preg_replace_callback(",<\?.*?\?>,", 'htmlspecialchars_callback', $xml);
// CDATA
$xml = preg_replace(",<!\[CDATA\[(.*?)\]\]>,", '$1', $xml);

print $xml;
?>
</pre>
</div>

    <!-- paulisageek.com/nodeSelector Added Code -->
    <script>
    if (typeof paulisageek == "undefined") { paulisageek = {}; }
    if (typeof paulisageek.ns == "undefined") { paulisageek.ns = {}; }
    paulisageek.ns.clickCallback = function(xpath) {
        return xpath.replace("//pre[@id='xml']", "").replace(/\/xml_/g, "/").replace(/\/xmlattr_/g, "/@").replace(/__colon__/g, ":");
    }
    paulisageek.ns.doneURL = "<?php print $next ?>";
    paulisageek.ns.params = {"url" : "<?php print $finalURL ?>"};
    </script>
    <script src="http://paulisageek.com<?php print dirname(dirname($_SERVER['PHP_SELF'])) ?>/nodeSelector/ns.js" ></script>
    <!-- paulisageek.com/nodeSelector End Added Code -->
<?php
    require "/var/www/paul.slowgeek.com/footer.php";
}
?>

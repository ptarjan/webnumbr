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
if (! isset($_REQUEST['action'])) 
    if ($_REQUEST['xpath'] == NULL)
        $_REQUEST['action'] = 'pick';
    else 
        $_REQUEST['action'] = 'show';
if ($_REQUEST['action'] == 'show') {
    $showxpath = $_REQUEST['xpath'];
    $_REQUEST['xpath'] = NULL;
}

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
} else {
    if (isset($_REQUEST['format']) && $_REQUEST['format'] == "xml")  {
        header("Content-Type: text/xml");
        header("X-Content-Type-Options: nosniff");
        // stupid browser sniffing the content type
        $xml = $data->saveXML();
        $xml = str_replace('xmlns="http://www.w3.org/1999/xhtml"', "", $xml);
        die($xml);
    }
}

if ($type === "html") {

    if ($_REQUEST['action'] == "show") {
        $dx = new DomXpath ($data);
        $nl = $dx->query($showxpath);
        if ($nl->length == 0) {
            $div = $data->createElement("div");
            $div->setAttribute("style", "margin: 30px");
            $div->appendChild($data->createTextNode(
                "No nodes found from the Xpath. It is probably a string match. Search in the document for this :"
            ));
            $blink = $data->createElement("blink");
            $blink->setAttribute("id", "paulisaageek_webGraphr_blink");
            $blink->setAttribute("style", "border: 5px solid red; background-color: #0cf; color: black; margin: 10px; padding: 10px");
            $node = $dx->evaluate($showxpath);
            if (!is_string($node) && !is_int($node))
                $node = "Xpath doesn't match anything in the document";
            $blink->appendChild(
                $data->createTextNode(
                    $node
                )
            );
            $div->appendchild($blink);
                
            $data->documentElement->insertBefore($div, $data->documentElement->firstChild);
        } else {
            $node = $nl->item(0);
            $blink = $data->createElement("blink");
            $blink->setAttribute("id", "paulisaageek_webGraphr_blink");
            $blink->setAttribute("style", "border: 5px solid red; background-color: #0cf; color: black; z-index: 999");
            while ($node->childNodes->length > 0) {
                $child = $node->firstChild;
                $node->removeChild($child);
                $blink->appendChild($child);
            }
            $node->appendChild($blink);
        }
    }

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

    if ($_REQUEST['action'] == "pick") {
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

    } else if ($_REQUEST['action'] == "show") {
        $rep = '
        
        <!-- paulisageek.com/webGraphr START Added Code -->
        <script src="http://code.jquery.com/jquery-latest.js"></script>
        <script>
$(document).ready(function($) {
    var node = $("#paulisaageek_webGraphr_blink");
    node.ready(function($) {
        node
        .clone()
        .attr("id", node.attr("id") + "_clone")
        .css({
            "position" : "absolute",
            "top" : 0,
            "left" : 0
        })
        .appendTo(document.body)
        .animate(node.offset(), 2000, "linear", function() { 
            $("#" + node.attr("id") + "_clone").remove() 
        });
    });
});
        </script>    
        <!-- paulisageek.com/webGraphr END Added Code -->
    
        ';
    } else { 
        $rep = "";
    }
    $data = preg_replace('/(.*<\/\s*[bB][oO][dD][yY]\s?[^>]*>)(.*)/', "$1" . $rep . "$2", $data, -1, $count);
    if ($count == 0) {
        $data = preg_replace('/(.*<\/\s*[hH][tT][mM][lL]\s?[^>]*>)(.*)/', "$1" . $rep . "$2", $data, -1, $count);
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

require "/var/www/paul.slowgeek.com/header.php";
?>
<h1>Data Document</h1>

<div>
<pre id="xml">
<?php 

function htmlspecialchars_callback($matches) {
    return htmlspecialchars($matches[0]);
}

// CDATA
$xml = preg_replace_callback(",<!\[CDATA\[(.|\n)*?\]\]>,", "htmlspecialchars_callback", $xml);
// Remove the word "CDATA"
$xml = preg_replace(",&lt;!\[CDATA\[((.|\n)*?)\]\]&gt;,", "$1", $xml);

// Start nodes
$xml = preg_replace(",<([^>!?/\s][^>/\s]*)((\s+([^\s=]+)\s*=\s*(\'[^<\']*\'|\"[^<\"]*\"))+)?\s*>,", "<span name=\"$1\" $2>&lt;$1$2&gt;", $xml);
// End nodes
$xml = preg_replace(",<(/[^/>]+)\s*>,", "&lt;$1&gt;</span>", $xml);
// Short tags
$xml = preg_replace(",<([^>!?/\s]+)(\s[^>]+)?\s*/>,", "<span name=\"$1\" $2>&lt;$1$2 /&gt;</span>", $xml);

// Attributes
function markupAttr($matches) {
    return preg_replace(",([^\s=]+)\s*=\s*(\'[^<\']*\'|\"[^<\"]*\"),", "<span name=\"@$1\">$1=$2</span>", $matches[0]);
}
// Encoded nodes
$xml = preg_replace_callback(",&lt;.*?&gt;,", "markupAttr", $xml);

// Comments
$xml = preg_replace_callback(",<!--.*?-->,", 'htmlspecialchars_callback', $xml);
// Declaration
$xml = preg_replace_callback(",<\?.*?\?>,", 'htmlspecialchars_callback', $xml);

print $xml;
?>
</pre>
</div>

      </div>
    </div>
  </body>

    <!-- paulisageek.com/nodeSelector Added Code -->
    <script>
    if (typeof paulisageek == "undefined") { paulisageek = {}; }
    if (typeof paulisageek.ns == "undefined") { paulisageek.ns = {}; }
    paulisageek.ns.doneURL = "<?php print $next ?>";
    paulisageek.ns.params = {"url" : "<?php print $finalURL ?>"};
    paulisageek.ns.getXpath = function(e, oldXpath) {
        var xpath = "";
        while (e.nodeName.toLowerCase() != "pre") {
            var node = e.attributes["name"].nodeValue;
            var parent = e.parentNode;
            var children = $(parent).children("[name=" + node + "]");
            if (children.size() > 1) {
                var good = false;
                children.each(function(i) {
                    if (this == e) {
                        node = node + "[" + (i+1) + "]";
                        good = true;
                        return false;
                    }
                });
                if (! good) {
                    return false;
                }
            }
            xpath = "/" + node + xpath;
            e = parent;
        }
        return xpath;
    };
    </script>
    <script src="http://paulisageek.com<?php print dirname(dirname($_SERVER['PHP_SELF'])) ?>/nodeSelector/ns.js" ></script>
    <!-- paulisageek.com/nodeSelector End Added Code -->

</html>
<?php } ?>

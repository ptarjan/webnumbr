<?php
if (! isset($_REQUEST['url']) || $_REQUEST['url'] == "http://" || trim($_REQUEST['url']) == "") {
$subtitle = 'Create new numbr';
ob_start(); 
?>


        <h3 id="start" class="first">
          Start a Numbr
        </h3>
        
        <br/><ol>
        <li>Think of an interesting number:
          <br/>E.g. finance, sports, transportation, prices, or web statistics.</li>
        <li>Find a website that has this number on some page.</li>
        <li>Enter the URL into the box below. (Or use the <a href="
javascript:(
    function() {
        document.location = 'http://webnumbr.com/create?url=' + encodeURIComponent(document.location)
    }
)();
">webnumbr : create</a> bookmarklet)</li>
        <li>You then <b>point and click</b> on the number, enter a title and you're DONE!</li>
        </ol>

        <br/><form action='create'>
          <div id="startForm"> 
            <label id="urlLabel" for="url">URL:</label>
            <input id="url" name='url' style="width:400px;" value="http://" />
            <input id="submitURL" type='submit' value='Pick the Numbr on the Page' />
          </div>
        </form>
        
        <div id="other_numbrs"></div>

        <script src="http://code.jquery.com/jquery-latest.js"></script>
        <script>
$('#url').keyup(function() {
  $.getJSON('/checkURL', {url: $(this).val()}, function(urls) {
    if (urls.length == 0) $('#other_numbrs').text('');
    else $('#other_numbrs').html('Does your numbr already exist? <ul>');
    $.each(urls, function(i, url) {
      $('#other_numbrs').append($('<li>').append(
        $('<a>').attr('href', '/'+url.name).append(
          $('<span>').text(
            url.title ? url.title : url.name
          )
        )).append(' ').append(
          $('<span>').text(
            url.url.length > 50 ? url.url.substring(0,50) + '...' : url.url
          )
        )
      );
    });
  });
});
        </script>
<?php
    $content = ob_get_clean(); require("template.php");
    die();
}
if (strpos($_REQUEST["url"], "http") !== 0) {
    $_REQUEST['url'] = "http://" . $_REQUEST['url'];
}

if (! isset($_REQUEST['xpath'])) $_REQUEST['xpath'] = NULL;
if (! isset($_REQUEST['action'])) 
    if ($_REQUEST['xpath'] == NULL)
        $_REQUEST['action'] = 'pick';
    else 
        $_REQUEST['action'] = 'show';
switch ($_REQUEST['action']) {
case 'show' :
    $showxpath = $_REQUEST['xpath'];
    $_REQUEST['xpath'] = NULL;
    break;
case 'pick' :
    $_REQUEST['xpath'] = NULL;
    break;
case 'run' :
default :
    if (!$_REQUEST['xpath'])
        die("Can't run without an xpath");
    break;
}

require ("fetch.inc");
try {
    $data = fetch($_REQUEST['url'], $_REQUEST['xpath'], $type, $finalURL);
} catch (FetchException $e) {
    print "Fetch Exception: " . $e->getMessage(); 
    die();
}

$next = 'http://' . $_SERVER['SERVER_NAME'] . '/edit';

if ($type === "num") {
    die($data);
} else {
    if (isset($_REQUEST['format']) && $_REQUEST['format'] == "xml")  {
        header("Content-Type: text/xml");
        header("X-Content-Type-Options: nosniff");
        // stupid browser sniffing the content type
        $xml = @$data->saveXML();
        $xml = str_replace('xmlns="http://www.w3.org/1999/xhtml"', "", $xml);
        die($xml);
    }
}

$msg = '
        <div id="webnumbr-message"> 
            <img src="http://webnumbr.com/images/webnumbr-banner-50.png"> Click on the number that you are interested in...
        </div>
        <script>
            $(function() {
                $("#webnumbr-message").fadeIn(2000);
            });
        </script>
        <style>
        html {
            margin-top: 2.5em;
        }
        #webnumbr-message {
            position: fixed; 
            display: none;
            margin: auto;
            color: white; 
            background: black;
            border-bottom: 1px solid white;
            top: 0; 
            left: 0; 
            width: 100%; 
            height: 20pt; 
            text-align:center; 
            font-weight: bold;
            font-size: 12pt;
            padding-top: 7px;
            z-index: 999999;
        }
        #webnumbr-message img {
            height: 16pt;
            margin-right: 20px;
        }
        </style>
';

if ($type === "html") {

    if ($_REQUEST['action'] == "show") {
        $dx = new DomXpath ($data);
        $nl = @$dx->query($showxpath);
        if ($nl->length == 0) {
            $div = $data->createElement("div");
            $div->setAttribute("style", "margin: 30px");
            $div->appendChild($data->createTextNode(
                "No nodes found from the Xpath. It is probably a string match. Search in the document for this :"
            ));
            $blink = $data->createElement("blink");
            $blink->setAttribute("id", "webnumbr_blink");
            $blink->setAttribute("style", "border: 5px solid red; background-color: #0cf; color: black; margin: 10px; padding: 10px");
            $node = @$dx->evaluate($showxpath);
            if (!is_string($node) && !is_int($node))
                $node = $showxpath . " doesn't match anything in the document";
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
            $blink->setAttribute("id", "webnumbr_blink");
            $blink->setAttribute("style", "border: 5px solid red; background-color: #0cf; color: black; z-index: 999");
            while ($node->childNodes->length > 0) {
                $child = $node->firstChild;
                $node->removeChild($child);
                $blink->appendChild($child);
            }
            $node->appendChild($blink);
        }
    }

    $data = @$data->saveXML();

    // Eliminate shorttags
    $data = preg_replace('/<\s*([a-z]+)\s([^>]*)\/>/', "<$1 $2></$1>", $data);

    $rep = '

    <!-- webnumbr.com Added Code -->
    <base href="' . htmlspecialchars($_REQUEST['url']) . '"></base>
    <!-- webnumbr.com End Added Code -->

    ';

    $data = preg_replace('/(<\s*[hH][eE][aA][dD]\s?[^>]*>)/', "$1" . $rep, $data, -1, $count);
    if ($count == 0) {
        $data = preg_replace('/(<\s*[hH][tT][mM][lL]\s?[^>]*>)/', "$1" . "<head>" . $rep . "</head>", $data, -1, $count);
    }

    if ($_REQUEST['action'] == "pick") {
        $rep = '

        <!-- webnumbr.com Added Code -->
        <script src="http://code.jquery.com/jquery-latest.js"></script>
        ' . $msg . '
        <script>
        if (typeof paulisageek == "undefined") { paulisageek = {}; }
        if (typeof paulisageek.ns == "undefined") { paulisageek.ns = {}; }
        paulisageek.ns.doneURL = "' . $next . '";
        paulisageek.ns.params = "' . preg_replace('/"/', '\"', (json_encode(array("url" => $finalURL)))) . '";
        </script>
        <script src="http://paulisageek.com' . dirname(dirname($_SERVER['PHP_SELF'])) . '/nodeSelector/ns.js" ></script>
        <!-- webnumbr.com End Added Code -->

        ';

    } else if ($_REQUEST['action'] == "show") {
        $rep = '
        
        <!-- webnumbr.com START Added Code -->
        <script src="http://code.jquery.com/jquery-latest.js"></script>
        <script>
$(document).ready(function($) {
    var node = $("#webnumbr_blink");
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
        <!-- webnumbr.com END Added Code -->
    
        ';
    } else { 
        $rep = "";
    }
    
    $data = preg_replace('/(<\/\s*[bB][oO][dD][yY]\s?[^>]*>)/', "$1" . $rep, $data, -1, $count);
    if ($count == 0) {
        $data = preg_replace('/(<\/\s*[hH][tT][mM][lL]\s?[^>]*>)/', "$1" . $rep, $data, -1, $count);
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

?>
<?php print '<?xml version="1.0" encoding="UTF-8"?>' ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>webnumbr - XML selection</title>
    <link rel="stylesheet" href="style.css" type='text/css' />  
  </head>
  <body>

    <div id='container'>
      <div class='content'>

<!-- Start Content -->
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

    <!-- webnumbr.com Added Code -->
    <script src="http://code.jquery.com/jquery-latest.js"></script>
    <?php print $msg ?>
    <script>
    if (typeof paulisageek == "undefined") { paulisageek = {}; }
    if (typeof paulisageek.ns == "undefined") { paulisageek.ns = {}; }
    paulisageek.ns.doneURL = "<?php print $next ?>";
    paulisageek.ns.params = <?php print json_encode(array("url" => $finalURL)) ?>;
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
    <!-- webnumbr.com End Added Code -->

</html>
<?php } ?>



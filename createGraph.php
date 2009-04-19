<?php
if (isset($_REQUEST['go'])) {
    function required($p) {
        if (is_array($p)) 
            foreach ($p as $v) required($v);
        if (!isset($_REQUEST[$p])) die ("Required parameter $p");
        if (trim(isset($_REQUEST[$p])) == "") die ("Empty parameter $p");
    }

    required("name", "url", "xpath", "frequency");
    require ("db.inc");

    chdir("openid");
    require ("common.php");
    $dirbase = sprintf("http://%s%s/", $_SERVER['SERVER_NAME'], dirname($_SERVER['PHP_SELF']));
    $base = $dirbase . "createGraph?";

    $_REQUEST["_done"] = $base . http_build_query(array(
        "name" => $_REQUEST["name"],
        "url" => $_REQUEST["url"],
        "xpath" => $_REQUEST["xpath"],
        "frequency" => $_REQUEST["frequency"],
        "go" => $_REQUEST["go"],
    ));
    $_REQUEST["_root"] = $dirbase;

    // OpenID
    $consumer = getConsumer();

    // Complete the authentication process using the server's
    // response.
    $response = $consumer->complete($_REQUEST['_done']);

    $openid = "";
    // Check the response status.
    if ($response->status == Auth_OpenID_CANCEL) {
        // This means the authentication was cancelled.
        print ('Verification cancelled.');
    } else if ($response->status == Auth_OpenID_FAILURE) {
        // Authentication failed; display the error message.
        if (strpos($response->message, "<No mode set>") === FALSE)  {
            print ("OpenID authentication failed: " . $response->message);
            error_log ("OpenID authentication failed: " . $response->message);
            die();
        }
    } else if ($response->status == Auth_OpenID_SUCCESS) {
        // This means the authentication succeeded; extract the
        // identity URL and Simple Registration data (if it was
        // returned).
        $openid = $response->getDisplayIdentifier();
    }

    if (isset($_REQUEST['openid_identifier']) && $_REQUEST['openid_identifier'] !== "http://" && $_REQUEST['openid_identifier'] !== "") {
        require ("try_auth.php");
        die();
    };

    if (strpos($_SERVER["REQUEST_URI"], "/dev/") === 0) {
        print "<pre>";
        print_r($_REQUEST);
        print "</pre>";
        die("Not inserting in dev mode");
    }
    $stmt = $PDO->prepare("INSERT INTO graphs (name, url, xpath, frequency, openid, createdTime) VALUES (:name, :url, :xpath, :frequency, :openid, NOW())");
    $r = $stmt->execute(array("name" => $_REQUEST['name'], "url" => $_REQUEST['url'], "xpath" => $_REQUEST['xpath'], "frequency" => $_REQUEST['frequency'], "openid" => $openid));
    if (!$r) {
        die("Something is wrong with the database right now. Please retry again later, and send me an email webGraphr@paulisageek.com<br/>" . print_r($stmt->errorInfo(), TRUE));
    } else {
        $stmt = $PDO->prepare("SELECT id FROM graphs WHERE name = :name AND url = :url AND xpath = :xpath AND frequency = :frequency AND openid = :openid ORDER BY createdTime ASC");
        $r = $stmt->execute(array("name" => $_REQUEST['name'], "url" => $_REQUEST['url'], "xpath" => $_REQUEST['xpath'], "frequency" => $_REQUEST['frequency'], "openid" => $openid));
        if (! $r) { 
            die ("ummm.. can't select from the database<br/>" . print_r($stmt->errorInfo(), TRUE));
        };

        $result = $stmt->fetchAll();    

        if (count($result) == 0) die ("ummm.. couldn't insert into database");

        header("Location: graph?id=" . $result[0]['id']);
    }
    die();
}

// print $_REQUEST['referer'] . "<br>" . $_SERVER['HTTP_REFERER'];
if (isset($_REQUEST['url']))
    $url = $_REQUEST['url'];
else {
    if (isset($_REQUEST['referer']))
        $referer = $_REQUEST['referer'];
    else if (isset($_SERVER['HTTP_REFERER']))
        $referer = $_SERVER['HTTP_REFERER'];
    else
        $referer = "";

    $parsed = parse_url($referer);
    $query = $parsed['query'];

    $url = "";
    foreach (explode("&", $query) as $arg) {
        $boom =  explode("=", $arg, 2);
        if (count($boom) !== 2) continue;
        list($k, $v) = $boom;
        if ($k == "url") 
            $url = urldecode($v);
    }
}

?>
<?php
print '<?xml version="1.0" encoding="UTF-8"?>';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>webGraphr - Create Graph</title>
    <link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/2.7.0/build/reset/reset-min.css" />
    <link rel="stylesheet" href="/style.css" type='text/css' />  
    <link rel="stylesheet" href="style.css" type='text/css' />  

    <link rel="shortcut icon" href="images/webGraphr-favicon.png" type="image/x-icon">
    <link rel="icon" href="images/webGraphr-favicon.png" type="image/x-icon">

    <link type="text/css" href="http://jquery-ui.googlecode.com/svn/tags/1.7.1/themes/base/ui.all.css" rel="stylesheet" />
  </head>
  <body>
    <div id='container'>
      <div id='header'>
        <a href='.'><img id='smalllogo' src="images/webGraphr-banner-100.png" /></a>
      </div>

      <div class="content">

        <h1>Last Step</h1>

        <form>
          <input name="go" value="go" type="hidden" />
          <table>
            <tr><td>OpenID (to edit later)</td><td>
                <input type="text" style="padding-left: 20px; background: #FFFFFF url(https://s.fsdn.com/sf/images//openid/openid_small_logo.png) no-repeat scroll 0 50%;" size="50" value="http://" name="openid_identifier" id="openid_identifier"/>
            </td></tr>
            <tr><td>Name (<b>required</b>):</td><td><input name="name" size="100" value="<?php print htmlspecialchars($_REQUEST["name"]) ?>" /></td></tr>
            <tr><td>URL: </td><td><input name="url" value="<?php print htmlspecialchars($url) ?>" size="100" /></td></tr>
            <tr><td>Xpath: </td><td><input name='xpath' value="<?php print htmlspecialchars($_REQUEST["xpath"]); ?>" size="100" /></td></tr>
            <tr><td>Example of the data (<b>must be a number</b>): </td><td><b id='data'></b> <input type="button" id='reload' value="Reload" /></tr>
            <tr><td>Graph Frequency: </td><td><select name='frequency'>
            <option value="1">1 hour</option>
            <option value="6">6 hours</option>
            <option value="12">12 hours</option>
            <option value="24">24 hours</option>
            </select></td></tr>
            <tr><td></td><td><input type="submit" value="Create Graph" /></td></tr>
          </table>
        </form>
      </div>
    </div>

    <div id='dialog' style='display:none'>
        <p>Everything look good?</p>
        <p>If you don't register with OpenID you can't change these.</p>
    <br/>

<script src='http://ajax.googleapis.com/ajax/libs/jquery/1.3/jquery.min.js'></script>
<script type="text/javascript" src="http://jquery-ui.googlecode.com/svn/tags/1.7.1/ui/ui.core.js"></script>
<script type="text/javascript" src="http://jquery-ui.googlecode.com/svn/tags/1.7.1/ui/ui.draggable.js"></script>
<script type="text/javascript" src="http://jquery-ui.googlecode.com/svn/tags/1.7.1/ui/ui.resizable.js"></script>
<script type="text/javascript" src="http://jquery-ui.googlecode.com/svn/tags/1.7.1/ui/ui.dialog.js"></script>
<script>
$(document).ready(function() {
    function reload() {
        $("#data").html("<img src='http://l.yimg.com/a/i/eu/sch/smd/busy_twirl2_1.gif' />");
        $.get("selectNode?" + $.param({url : $(":input[name='url']").attr("value"), xpath : $(":input[name='xpath']").attr("value")}), function (data) {
            $("#data").html(data);
        });
    }
    $("#data").ready(reload);
    $("#reload").click(reload);

    var confirmed = false;
    $("form").submit(function(ev) {
        if ($.trim($(":input[name='name']").attr("value")) == "") {
            $(":input[name='name']").wrap("<span class='error' style='border:10px solid red'></span>");
            return false;
        }
        var data = parseInt($("#data").text());
        if (isNaN(data)) {
            $("#data").wrapInner("<span class='error' style='color:red'></span>");
            return false;
        }
        $("#dialog").dialog("open");
        if (!confirmed) {
            return false;
        } else {
            return true;
        }
    });
    $("#dialog").dialog({
        modal : true,
        autoOpen : false,
        buttons : {
            "Yes!" : function() { 
                confirmed = true;
                $("form").submit() 
            },
            "No! (Go back and edit)" : function () { $("#dialog").dialog("close"); }
        },
        title : "Last step before graphyness",
        "hide" : "slide"
    });
});
</script>

<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
// document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<!-- <script src='http://google-analytics.com/ga.js' type='text/javascript'></script> -->
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("UA-149816-4");
pageTracker._trackPageview();
} catch(err) {}
</script>

  </body>
</html>

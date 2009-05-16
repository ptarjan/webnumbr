<?php
if (!isset($_REQUEST['mode'])) $_REQUEST['mode'] = "create";
if (isset($_REQUEST['go'])) {
    function required($p) {
        if (is_array($p)) 
            foreach ($p as $v) required($v);
        if (!isset($_REQUEST[$p])) die ("Required parameter $p");
        if (trim(isset($_REQUEST[$p])) == "") die ("Empty parameter $p");
    }

    required("name", "url", "xpath", "frequency");
    ob_start();
    require "checkName.php";
    $errors = ob_get_contents();
    if ($errors) die($errors);

    if (strpos($_REQUEST["url"], "http") !== 0) {
        die ("Only urls starting with http are supported");
    }

    require ("db.inc");

    // 
    // OpenID
    //
    chdir("openid");
    require ("common.php");
    $dirbase = sprintf("http://%s%s/", $_SERVER['SERVER_NAME'], dirname($_SERVER['PHP_SELF']));
    $base = $dirbase . "createNumbr?";

    $_REQUEST["_done"] = $base . http_build_query(
        $_REQUEST
    );
    $_REQUEST["_root"] = $dirbase;

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
            error_log ("OpenID authentication failed: " . $response->message);
            die ("OpenID authentication failed: " . $response->message);
        } else {
            // This part redirects them to their openid provider
            if ($_REQUEST["mode"] == "edit") {
                $stmt = $PDO->prepare("SELECT openid FROM numbrs WHERE name=:name");
                $stmt->execute(array("name" => $_REQUEST['name']));
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (count($result) != 1 || trim($result[0]['openid']) == "") die ("Invalid openid. How did you get here in the first place?");
                $openid = $result[0]['openid'];
            }
            if (isset($_REQUEST['openid']) && $_REQUEST['openid'] !== "http://" && $_REQUEST['openid'] !== "") {
                $openid = $_REQUEST['openid'];
            };
            require("try_auth.php");
            doOpenID($openid);
            die();
        }
    } else if ($response->status == Auth_OpenID_SUCCESS) {
        // This means the authentication succeeded; extract the
        // identity URL and Simple Registration data (if it was
        // returned).
        $openid = $response->getDisplayIdentifier();
    }

    chdir("..");
    //
    // End OpendID
    //

    if (strpos($_SERVER["REQUEST_URI"], "/dev/") === 0) {
        print "<pre>";
        print_r($_REQUEST);
        print "</pre>";
        // die("Not inserting in dev mode");
    }

    if ($_REQUEST["mode"] == "edit") {
        $stmt = $PDO->prepare("UPDATE numbrs SET title=:title, description=:description, url=:url, xpath=:xpath, frequency=:frequency WHERE name=:name");
        $r = $stmt->execute(array(
            "title" => $_REQUEST['title'], 
            "description" => $_REQUEST['description'], 
            "url" => $_REQUEST['url'], 
            "xpath" => $_REQUEST['xpath'], 
            "frequency" => $_REQUEST['frequency'], 
            "name" => $_REQUEST['name'], 
        ));
    } else {
        $stmt = $PDO->prepare("INSERT INTO numbr_table (name, title, description, url, xpath, frequency, openid, createdTime) VALUES (:name, :title, :description, :url, :xpath, :frequency, :openid, NOW())");
        $r = $stmt->execute(array(
            "name" => $_REQUEST['name'], 
            "title" => $_REQUEST['title'], 
            "description" => $_REQUEST['description'], 
            "url" => $_REQUEST['url'], 
            "xpath" => $_REQUEST['xpath'], 
            "frequency" => $_REQUEST['frequency'], 
            "openid" => $openid, 
        ));
    }
    if (!$r) 
        die("Something is wrong with the database right now. It could be that someone just took your name, or something else is weird. Please retry again later, and send me an email webNumbr@paulisageek.com<br/>" . print_r($stmt->errorInfo(), TRUE));

    if (isset($_REQUEST['parent'])) {
        $stmt = $PDO->prepare("INSERT INTO numbr_data (data, timestamp, numbr) SELECT data, timestamp, :numbr FROM numbr_data WHERE numbr = :parent");
        $stmt->execute(array(
            "parent" => $_REQUEST["parent"],
            "numbr" => $_REQUEST["name"],
        ));
    }

    header("Location: $" . $_REQUEST["name"]);
    die();
}

if ($_REQUEST['mode'] == "edit") {
    require_once ("db.inc");
    $stmt = $PDO->prepare("SELECT * FROM numbrs WHERE name=:name");
    $stmt->execute(array("name" => $_REQUEST['name']));
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (count($result) != 1) die ("bad name");
    foreach ($result[0] as $name => $value) 
        $_REQUEST[$name] = $value;
}


if (isset($_REQUEST['parent'])) {
    require_once ("db.inc");
    $stmt = $PDO->prepare("SELECT * FROM numbrs WHERE name = :name");
    $stmt->execute(array("name" => $_REQUEST["parent"]));
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (count($result) === 1) {
        foreach ($result as $row) {
            foreach ($row as $key => $value) {
                if ($key !== "parent")
                    $_REQUEST[$key] = $value;
            }
        }
    }
}

$url = "";
// print $_REQUEST['referer'] . "<br>" . $_SERVER['HTTP_REFERER'];
if (isset($_REQUEST['url']))
    $url = $_REQUEST['url'];
else {
    if (isset($_REQUEST['params'])) {
        $params = json_decode($_REQUEST['params'], TRUE);
        if (isset($params['url']))
            $url = $params['url'];
    }
}

// after all the direct aproaches use the referer
if (empty($url)) {
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
    <title>webNumbr - Create Numbr</title>
    <link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/2.7.0/build/reset/reset-min.css" />
    <link rel="stylesheet" href="/style.css" type='text/css' />  
    <link rel="stylesheet" href="style.css" type='text/css' />  

    <link type="text/css" href="http://jquery-ui.googlecode.com/svn/tags/1.7.1/themes/base/ui.all.css" rel="stylesheet" />
    <style type="text/css">
input[type=text], textarea {
    width : 400px;
}
th {
    text-align: right;
    padding: 0px 10px;
}
    </style>
  </head>
  <body>
    <div id='container'>
      <div id='header'>
        <a href='.'><img id='smalllogo' src="images/webNumbr-banner-32.png" alt="logo" /></a>
      </div>

      <div class="content">

        <h1>Last Step</h1>

        <form action="">
        <p> 
          <input name="mode" value="<?php print htmlspecialchars($_REQUEST['mode']) ?>" type="hidden" /> 
          <input name="go" value="1" type="hidden" /> 
        </p>
          <table>
<?php if (isset($_REQUEST["parent"])) { ?>
            <tr><th>Extends (inherits all data from)</th><td><input name="parent" value="<?php print htmlspecialchars($_REQUEST["parent"]); ?>" /></td></tr>
<?php } ?>
            <tr><th><a href="http://openid.net">OpenID</a></th><td>
                <input type="text" style="padding-left: 20px; background: #FFFFFF url(https://s.fsdn.com/sf/images//openid/openid_small_logo.png) no-repeat scroll 0 50%; width : 380px" maxlength="255" value="<?php $_REQUEST["openid"] ? print htmlspecialchars($_REQUEST["openid"]) : "http://" ?>" name="openid" id="openid" <?php print $_REQUEST["mode"] == "edit" ? 'disabled="disabled" ' : "" ?> />
            </td></tr>
            <tr><th><a title="unique name to fetch this numbr">Name (?)</a></th><td><input type="text" name="name" maxlength="63" value="<?php print htmlspecialchars($_REQUEST["name"]) ?>" /></td><td id="name_msg"></td></tr>
            <tr><th><a title="human readable title">Title (?)</a></th><td><input type="text" name="title" maxlength="255" value="<?php print htmlspecialchars($_REQUEST["title"]) ?>" /></td></tr>
            <tr><th><a title="longer description, used in searches">Description (?)</a></th><td><textarea name="description" rows="3" cols="98" maxlength="1000"><?php print htmlspecialchars($_REQUEST["description"]) ?></textarea></td></tr>
            <tr><th>URL</th><td><input type="text" name="url" value="<?php print htmlspecialchars($url) ?>" maxlength="2000" /></td></tr>
            <tr><th>Xpath</th><td><input type="text" name='xpath' value="<?php print htmlspecialchars($_REQUEST["xpath"]); ?>" maxlength="1000" /></td></tr>
            <tr><th>Example of the data (<b>must be a number</b>)</th><td><b id='data' style="margin : 0px 10px"></b> <input type="button" id='reload' value="Reload" /> <span id="messages"></span></td></tr>
            <tr><th>Fetch Frequency</th><td><select name='frequency'>
            <option value="1"<?php if ($_REQUEST['frequency'] == 1) print ' selected="selected"'; ?>>1 hour</option>
            <option value="6"<?php if ($_REQUEST['frequency'] == 6) print ' selected="selected"'; ?>>6 hours</option>
            <option value="12"<?php if ($_REQUEST['frequency'] == 12) print ' selected="selected"'; ?>>12 hours</option>
            <option value="24"<?php if ($_REQUEST['frequency'] == 24) print ' selected="selected"'; ?>>24 hours</option>
            </select></td></tr>
            <tr><td></td><td><input type="submit" value="<?php print ($_REQUEST["mode"] == "edit" ? "Edit" : "Create" ) . " Numbr"?>" /></td></tr>
          </table>
        </form>
      </div>
    </div>

    <div id='dialog' style='display:none'>
        <p>Everything look good?</p>
        <p>You can only edit things if you put in a valid openid.</p>
    </div>


<script src="http://www.google.com/jsapi"></script>
<script type="text/javascript" src="createNumbr.js"></script>

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

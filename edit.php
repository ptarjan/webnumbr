<?php
$subtitle = 'Edit numbr details';
ob_start(); 
?>

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
    require ("db.inc");

    if ($_REQUEST["mode"] == "edit") {
        // Edit mode doesn't check name
    } else {
        ob_start();
        require "checkName.php";
        $errors = ob_get_contents();
        ob_end_clean();
        if ($errors)
            die($errors);
    }

    if (strpos($_REQUEST["url"], "http") !== 0) {
        die ("Only urls starting with http are supported");
    }
    
    if ($_REQUEST["mode"] == "edit" || (isset($_REQUEST['openid']) && trim($_REQUEST['openid']) != "")) {
        // 
        // OpenID
        //
        chdir("openid");
        require ("common.php");
        $dirbase = sprintf("http://%s/", $_SERVER['SERVER_NAME']);
        $base = $dirbase . "edit?";

        $_REQUEST["_done"] = $base . http_build_query(
            $_REQUEST
        );
        $_REQUEST["_root"] = $dirbase;

        $consumer = getConsumer();

        // Complete the authentication process using the server's
        // response.
        $response = $consumer->complete($_REQUEST['_done']);

        $openid = "";
        if (isset($_REQUEST['openid']) && $_REQUEST['openid'] !== "http://" && $_REQUEST['openid'] !== "") {
            $openid = $_REQUEST['openid'];
        };

        if ($_REQUEST["mode"] == "edit") {
            $stmt = $PDO->prepare("SELECT openid FROM numbrs WHERE name=:name");
            $stmt->execute(array("name" => $_REQUEST['name']));
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (count($result) != 1 || trim($result[0]['openid']) == "") die ("Invalid openid. How did you get here in the first place?");
            $openid = $result[0]['openid'];
        }

        // Check the response status.
        if ($response->status == Auth_OpenID_CANCEL) {
            // This means the authentication was cancelled.
            die ('OpenID cancelled.');
        } else if ($response->status == Auth_OpenID_FAILURE) {
            // Authentication failed; display the error message.
            if (strpos($response->message, "<No mode set>") === FALSE)  {
                error_log ("OpenID authentication failed: " . $response->message);
                die ("OpenID authentication failed: " . $response->message);
            } else {
                // This part redirects them to their openid provider
                require("try_auth.php");
                doOpenID($openid);
                die();
            }
        } else if ($response->status == Auth_OpenID_SUCCESS) {
            // This means the authentication succeeded; extract the
            // identity URL and Simple Registration data (if it was
            // returned).
            $newopenid = $response->getDisplayIdentifier();
            if ($_REQUEST["mode"] == "edit") {
                if ($openid != $newopenid)
                    die ("That isn't the same openid as in the database. Did you really make this numbr?");
            } else {
                $openid = $newopenid;
            }
        }
    } else {
        $openid = "";
    }

    chdir("..");
    //
    // End OpendID
    //

    if (strpos($_SERVER["SERVER_NAME"], "dev.") === 0) {
        print "<pre>";
        print_r($_REQUEST);
        print "</pre>";
        // die("Not inserting in dev mode");
    }

    if ($_REQUEST["mode"] == "edit") {
        $stmt = $PDO->prepare("UPDATE numbrs SET title=:title, description=:description, url=:url, xpath=:xpath, frequency=:frequency WHERE name=:name");
        $r = $stmt->execute(array(
            "name" => $_REQUEST['name'], 
            "title" => $_REQUEST['title'], 
            "description" => $_REQUEST['description'], 
            "url" => $_REQUEST['url'], 
            "xpath" => $_REQUEST['xpath'], 
            "frequency" => $_REQUEST['frequency'], 
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

    // Fetch it for the first time
    require ("fetch.inc");
    $num = fetch($_REQUEST['url'], $_REQUEST['xpath']);
    $s = $PDO->prepare("INSERT INTO numbr_data (numbr, data) VALUES (:name, :data)");
    $s->execute(array("name" => $_REQUEST['name'], "data" => $num));
    $s = $PDO->prepare("UPDATE numbr_table SET goodFetches = goodFetches + 1 WHERE name = :name");
    $s->execute(array("name" => $_REQUEST["name"]));

    header("Location: " . $_REQUEST["name"]);
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
// print $_REQUEST['referer'] . "<br/>" . $_SERVER['HTTP_REFERER'];
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

// FIREFOX HACK
$_REQUEST['xpath'] = preg_replace(",/tbody,", "", $_REQUEST['xpath']);

?>

        <link type="text/css" href="http://jquery-ui.googlecode.com/svn/tags/1.7.1/themes/base/ui.all.css" rel="stylesheet" />

        <form action="" class="edit-form">
        <p> 
          <input name="mode" value="<?php print htmlspecialchars($_REQUEST['mode']) ?>" type="hidden" /> 
          <input name="go" value="1" type="hidden" /> 
<?php if ($_REQUEST["mode"] == "edit") { ?>
          <input name="name" value="<?php print htmlspecialchars($_REQUEST["name"]) ?>" type="hidden" />
<?php } ?>
        </p>
          <table>
<?php if (isset($_REQUEST["parent"])) { ?>
            <tr><th>Extends (inherits all data from)</th><td><input type="text" name="parent" value="<?php print htmlspecialchars($_REQUEST["parent"]); ?>" /></td></tr>
<?php } ?>
            <tr><th>OpenID</th><td>
                <input type="text" style="padding-left: 20px; background: #FFFFFF url(https://s.fsdn.com/sf/images//openid/openid_small_logo.png) no-repeat scroll 0 50%; width : 340px" maxlength="255" value="<?php $_REQUEST["openid"] ? print htmlspecialchars($_REQUEST["openid"]) : "http://" ?>" name="openid" id="openid" <?php print $_REQUEST["mode"] == "edit" ? 'disabled="disabled" ' : "" ?> />
                <?php if ($_REQUEST["mode"] == "edit") print '<span style="color: red"><--- This must be you</span>'; ?>
            </td></tr>
            <tr><th><span title="human readable title">Title (?)</span></th><td><input type="text" name="title" maxlength="255" value="<?php print htmlspecialchars($_REQUEST["title"]) ?>" /></td></tr>
            <tr><th><span title="unique name to fetch this numbr">Name (?)</span></th><td><input type="text" name="name" maxlength="63" value="<?php print htmlspecialchars($_REQUEST["name"]) ?>" <?php print $_REQUEST["mode"] == "edit" ? 'disabled="disabled" ' : "" ?> /></td><td id="name_msg"></td></tr>
            <tr><th><span title="longer description, used in searches">Description (?)</span></th><td><textarea name="description" rows="3" maxlength="1000"><?php print htmlspecialchars($_REQUEST["description"]) ?></textarea></td></tr>
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


    <div id='dialog' style='display:none'>
        <p>Everything look good?</p>
        <p>You can only edit things if you put in a valid openid.</p>
    </div>


<script src="http://www.google.com/jsapi"></script>
<script type="text/javascript" src="edit.js"></script>
<?php
    $content = ob_get_clean(); require("template.php");
?>

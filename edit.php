<?php
session_start();
$subtitle = 'Edit numbr details';
ob_start(); 
?>

<?php
if (!isset($_REQUEST['mode'])) $_REQUEST['mode'] = "create";

if ($_REQUEST["mode"] == "edit") {
    require ("db.inc");
    $stmt = $PDO->prepare("SELECT openid FROM numbrs WHERE name=:name");
    $stmt->execute(array("name" => $_REQUEST['name']));
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (count($result) != 1 || trim($result[0]['openid']) == "") die ("Invalid openid. How did you get here in the first place?");
    $openid = $result[0]['openid'];
}

if (isset($_REQUEST['go'])) {
    function required($p) {
        if (is_array($p)) 
            foreach ($p as $v) required($v);
        if (!isset($_REQUEST[$p])) die ("Required parameter $p");
        if (trim(isset($_REQUEST[$p])) == "") die ("Empty parameter $p");
    }

    required("name", "url", "xpath", "frequency");
    require_once("db.inc");

    // edit checks openid, otherwise check name
    if ($_REQUEST["mode"] == "edit") {
        if ($openid != $_SESSION['openid'])
            die ("You aren't the same openid as in the database. Did you really make this numbr?");
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
        $newopenid = (isset($_SESSION['openid']) ? $_SESSION['openid'] : "");
        $r = $stmt->execute(array(
            "name" => $_REQUEST['name'], 
            "title" => $_REQUEST['title'], 
            "description" => $_REQUEST['description'], 
            "url" => $_REQUEST['url'], 
            "xpath" => $_REQUEST['xpath'], 
            "frequency" => $_REQUEST['frequency'], 
            "openid" => $newopenid,
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
$_REQUEST['xpath'] = preg_replace(",/tbody,", "/", $_REQUEST['xpath']);

?>

        <link type="text/css" href="http://jquery-ui.googlecode.com/svn/tags/1.7.1/themes/base/ui.all.css" rel="stylesheet" />

        <form action="" class="edit-form">
        <p>
          <input name="mode" value="<?php print htmlspecialchars($_REQUEST['mode']) ?>" type="hidden" /> 
          <input name="go" value="1" type="hidden" /> 
<?php
    $next = 'http://' . $_SERVER['SERVER_NAME'] . '/rpx?_next=' . urlencode($_SERVER['REQUEST_URI']);
?>
<?php if ($_REQUEST["mode"] == "edit") { ?>
          <input name="name" value="<?php print htmlspecialchars($_REQUEST["name"]) ?>" type="hidden" />
<?php } ?>
        </p>
        <p>
            <span title="human readable title">Title</span> 
            <input type="text" name="title" maxlength="255" value="<?php print htmlspecialchars($_REQUEST["title"]) ?>" />
            <input type="submit" value="<?php print ($_REQUEST["mode"] == "edit" ? "Edit" : "Create" ) . " Numbr"?>" />
        </p>
<?php if (!isset($_SESSION['openid'])) { ?>
        <p style="color:red">
            You should
            <a id="login" class="rpxnow" onclick="return false;" href="https://webnumbr.rpxnow.com/openid/v2/signin?token_url=<?php print urlencode($next) ?>">Log In</a>
            if you ever want to edit this.
        </p>
<?php } ?>
        <p>
            <span id="name_msg"></span>
        </p>
        <p>
            Current value <b id='data' style="margin : 0px 10px"></b> <span id="messages"></span>
        </p>
<?php if ($_REQUEST["mode"] == "edit" && (! isset($_SESSION['openid']) || $_SESSION['openid'] != $openid)) { ?>
        <p class="error">
            You must
            <a id="login" class="rpxnow" onclick="return false;" href="https://webnumbr.rpxnow.com/openid/v2/signin?token_url=<?php print urlencode($next) ?>">Log In</a>
            as <b><?php print $openid ?></b> to edit this.
        </p>
<?php } ?>
        <p>
            <a href="#" id="advanced_toggle">Advanced settings</a>
        <p>
          </table>
          <table id="advanced">
<?php if (isset($_REQUEST["parent"])) { ?>
            <tr><th>Extends (inherits all data from)</th><td><input type="text" name="parent" value="<?php print htmlspecialchars($_REQUEST["parent"]); ?>" /></td></tr>
<?php } ?>
            <tr><th><span title="unique name to fetch this numbr">Slug</span></th><td><input type="text" name="name" maxlength="63" value="<?php print htmlspecialchars($_REQUEST["name"]) ?>" <?php print $_REQUEST["mode"] == "edit" ? 'disabled="disabled" ' : "" ?> /></td></tr>
            <tr><th><span title="longer description, used in searches">Description</span></th><td><textarea name="description" rows="3" maxlength="1000"><?php print htmlspecialchars($_REQUEST["description"]) ?></textarea></td></tr>
            <tr><th>URL</th><td><input type="text" name="url" value="<?php print htmlspecialchars($url) ?>" maxlength="2000" /></td></tr>
            <tr><th>Xpath</th><td><input type="text" name='xpath' value="<?php print htmlspecialchars($_REQUEST["xpath"]); ?>" maxlength="1000" /><input type="button" id='reload' value="Reload Data" /> </td></tr>
            <tr><th>Fetch Frequency</th><td><select name='frequency'>
            <option value="1"<?php if ($_REQUEST['frequency'] == 1) print ' selected="selected"'; ?>>1 hour</option>
            <option value="6"<?php if ($_REQUEST['frequency'] == 6) print ' selected="selected"'; ?>>6 hours</option>
            <option value="12"<?php if ($_REQUEST['frequency'] == 12) print ' selected="selected"'; ?>>12 hours</option>
            <option value="24"<?php if ($_REQUEST['frequency'] == 24) print ' selected="selected"'; ?>>24 hours</option>
            </select></td></tr>
          </table>
        </form>


<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.1/jquery.min.js"></script>
<script type="text/javascript" src="edit.js"></script>
<?php ?>
<?php
    $content = ob_get_clean(); require("template.php");
?>

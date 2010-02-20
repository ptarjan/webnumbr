<?php
session_start();
if ($data === NULL) {
    if (isset($c['numbr']['error']) || count($c['numbr']) == 0) {
        header("Location: /search?query={$c['name']}");
    }
}
$numbr = array();
foreach ($c['numbr'] as $key => $val) 
    $numbr[$key] = htmlspecialchars($val);

$embedCode = $c['code'];
$graphCode = $c['code'];
if (count($c['plugins']['selection']) == 1 && $c['plugins']['selection'][0][0] == 'default') {
    $graphCode = str_replace($c['name'], "{$c['name']}.all", $graphCode);
    $embedCode = str_replace($c['name'], "{$c['name']}.latest", $embedCode);
}

$ch = curl_init("http://" . $_SERVER['HTTP_HOST'] . str_replace(" ", "%20", "/$embedCode.embed"));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
$embed = htmlentities(curl_exec($ch));

$graphCode .= ".graph";

$ch = curl_init("http://" . $_SERVER['HTTP_HOST'] . str_replace(" ", "%20", "/$graphCode.embed"));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
$graphembed = htmlentities(curl_exec($ch));

function cutzero($value) {
   return preg_replace("/(\.?)0+$/", "", $value);
}

// ================ templates parts ===================

$subtitle = $numbr['title'];
$htmlHead = <<<END
<link rel="alternate" type="application/rss+xml" title="{$c['name']}" href="{$c['code']}.rss" />
END;
ob_start();
?>
            <div id="numbr_title">
                    <?php print $numbr['title'] ?>
            </div>
<?php 
if (is_array($data)) { 
    $data = end($data); 
    if (is_array($data)) 
        $data = $data[1]; 
}

if (is_numeric($data)) {
?>
            <div id="numbr_box">
                <?php print cutzero(number_format($data, 4, ".", ",")); ?>
            </div>

            <div id="numbr_embed_code">
                Embed code: 
                <input type="text" value="<?php print $embed ?>"/>
                <a href="/<?php print $c['code'] ?>.rss"><img title="RSS feed" alt="RSS feed" src="/images/feed-icon-28x28.png" /></a>
            </div>
<?php } ?>

            <div class="clear"></div>

            <div id="numbr_graph">
            <iframe src="/<?php print $graphCode ?>" style="width: 100%; height: 400px;" allowtransparency="true" frameborder="0"></iframe>

            <br/><div id="numbr_graph_embed_code">Embed code for graph: <input type="text" value="<?php print $graphembed ?>" style="width: 750px;" /></div>
            </div>

<?php if (count($c['numbr'])) { ?>

<h3 class="numbr_info">Numbr Info
<?php
if (isset($c['numbr']['openid']) && !empty($c['numbr']['openid']))
    $openid = $c['numbr']['openid'];
else
    $openid = "";

if ($openid) {
    if (!isset($_SESSION['openid']) || $openid != $_SESSION['openid']) {
        $next = urlencode('http://' . $_SERVER['SERVER_NAME'] . '/rpx?_next=' . urlencode($_SERVER['REQUEST_URI']));
        print <<<END
(<a id="login" class="rpxnow" onclick="return false;" href="https://webnumbr.rpxnow.com/openid/v2/signin?token_url=$next">login</a> as the owner of this graph to edit)
END;
    } else {
        print " <a href=\"/edit?mode=edit&name=" . urlencode($c['numbr']['name']) . "\">(edit)</a>";
    }
}
?></h3>
<table class="numbr_info">
<?php

foreach ($c['numbr'] as $key => $value) {
if ($key == "id") continue;
?>
<tr>
    <th><?php print htmlspecialchars($key); ?></th>
    <td><?php 
$hvalue = htmlspecialchars($value);
$link = "";
switch ($key) {
    case "name" :
        $link = "/$hvalue";
        break;
    case "title" :
    case "description" :
        $parts = explode(" ", $value);
        foreach ($parts as $part) {
            print '<a href="/search?query=' . urlencode($part) . '">' . htmlspecialchars($part) . '</a> ';
        }
        $hvalue = "";
        break;
    case "url" :
        $link = $hvalue;
        break;
    case "xpath" :
        $link = '/create?' . http_build_query(array("url" => $c['numbr']['url'], "xpath" => $c['numbr']['xpath'], "action" => "show"));
        break;
    case "frequency" :
        $hvalue = "Every $hvalue hour" . ($value == 1 ? "" : "s");
        break;
    case "openid" :
        $hvalue = '<a href="/profile?name=' . urlencode($hvalue) . '">' . $hvalue . '</a>';
        break;
    case "is_fetching" :
        if ($value == 1)
            $hvalue = '<span style="color:green">Good : this numbr is fetching</span>';
        else 
            $hvalue = '<span style="color:red">Bad : this numbr is not fetching due to too many fetch errors</span>';
}
if (trim($hvalue) != "") {
    if (trim($link) != "") {
        print '<a href="' . htmlspecialchars($link) . '">' . $hvalue . '</a>' ;
    } else {
        print $hvalue;
    }
}
?>
</td>
</tr>
<?php } ?>
</table>

<?php } else { ?>
Made from:
<ul>
<?php 
foreach ($c['plugins']['operation'] as $op) {
    if ($op[0] == 'join') {
        foreach ($op[1] as $numbr) {
            print '<li><a href="/' . htmlspecialchars($numbr) . '">' . htmlspecialchars($numbr) . '</a>';
        }
    }
}
?>
</ul>
<?php } ?>
            <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
            <script type="text/javascript">
            $(function() {
                $("input").focus(function() {
                    $(this).select();
                });
            });
            </script>
<?php
    $content = ob_get_clean();



//========== template =========================

include ("template.php");
?>

<?php
if ($data === NULL) die("No numbr found");
$numbr = array();
foreach ($c['numbr'] as $key => $val) 
    $numbr[$key] = htmlspecialchars($val);

$ch = curl_init("http://" . $_SERVER['HTTP_HOST'] . "/{$c['code']}.embed");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
$embed = htmlentities(curl_exec($ch));

$ch = curl_init("http://" . $_SERVER['HTTP_HOST'] . "/{$c['code']}.all.graph.embed");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
$graphembed = htmlentities(curl_exec($ch));

// ================ templates parts ===================

$subtitle = $numbr['title'];

ob_start();
?>
            <div class="numbr_title">
                    <?php print $numbr['title'] ?>
            </div>
            <div class="numbr_box">
                    <?php print $data ?>
            </div>

            <div class="numbr_embed_code">
                Embed code: 
                <input type="text" value="<?php print $embed ?>"/>
                <a href="/<?php print $c['code'] ?>.rss"><img title="RSS feed" alt="RSS feed" src="/images/feed-icon-28x28.png" /></a>
            </div>

            <div class="clear"></div>

            <div class="numbr_graph">
            <iframe src="/<?php print $c['code'] ?>.all.graph" style="width: 100%; height: 400px;" allowtransparnecy="true" frameborder="0"></iframe>

            <br/><div class="numbr_graph_embed_code">Embed code for graph: <input type="text" value="<?php print $graphembed ?>"/></div>
            </div>

            <h3>Description</h3> 
            <div class="numbr_description">
                    <?php print $numbr['description'] ?>
            </div>

            <h3>Source</h3>
            <div class="numbr_description">
                <a href="<?php print $numbr['url'] ?>" class="numbr_url">
                    <?php print $numbr['url'] ?>
                </a>
            </div>

<?php if (isset($_REQUEST['info'])) { ?>
<h3 class="numbr_info">Numbr Info</h3>
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
        $link = $hvalue;
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

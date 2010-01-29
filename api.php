<?php
// ================ templates parts ===================
$subtitle = "API";
ob_start();
?>

<div class="docs">
<h2>WebNumbr API</h2>

Simply change the url you use to access your numbr and you can do neat things.

<ul> 
<li>All commands are seperated by <i>.</i> 
</li><li>All parameters are wrapped by <i>()</i>
</li><li>You can read the <a href="/numbrPlugins">plugin sources</a>
</li><li>Order of operations (all selectors, "do SQL", all operations, all formats, "do output")
</li></ul>

<?php
function printDoc($dir) {
?>
<tr>
 <th>name</th>
 <th>params</th>
 <th>doc</th>
 <th>example</th>
</tr>
<?php
    $p = scandir("numbrPlugins/$dir");
    sort($p);

    // Put default at the top
    $key = array_search("default", $p);
    unset($p[$key]);
    array_unshift($p, 'default');
    $i = 0;
    foreach ($p as $name) {
        if (substr($name, 0, 1) == ".") continue;
        $params = @file_get_contents("numbrPlugins/$dir/$name/params.txt");
        $doc = @file_get_contents("numbrPlugins/$dir/$name/doc.txt");
        $example = @file_get_contents("numbrPlugins/$dir/$name/example.txt");
        $examplebreak = str_replace("/", "/&#x200b;", $example);
        $params = str_replace("/", "/&#x200b;", $params);
        $example = "<a href=\"/$example\">$examplebreak</a>";
        $name = "<a href=\"numbrPlugins/$dir/$name/code.php\">$name</a>";
        
        if (!$doc) continue;
        $i++;
?>
 <tr class="<?php print ($i % 2 == 0 ? "evenRow" : "oddRow") ?>">
 <td><?php print $name ?></td>
 <td><?php print trim($params) ?></td>
 <td><?php print $doc ?></td>
 <td><?php print $example ?></td>
</tr>
<?php
    }
}
?>

<table>
<h3>Selectors: These choose which piece of data you want.</h3>
<tbody>
<?php printDoc("selection"); ?>
</tbody>
</table>

<table>
<h3>Operations: These are evaluated in order and are chained together.</h3>
<tbody>
<?php printDoc("operation"); ?>
</tbody>
</table>

<table>
<h3>Formats: Output encoding. Can be chained.</h3>
<tbody>
<?php printDoc("format"); ?>
</tbody>
</table>
</div>

<?php
$content = ob_get_clean();

//========== template =========================

require ("template.php");
?>

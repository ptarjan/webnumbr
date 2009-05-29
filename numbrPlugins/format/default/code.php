<?php
if ($data == NULL) die("No numbr found");
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

$content = <<<END

<div class="numbr_card">
            <div class="numbr_title">
                    {$numbr['title']}
            </div>
            <div class="numbr_box">
                    $data              
            </div>
<div class="numbr_embed_code">Embed code: <input type="text" value="$embed"/></div>

            <div class="clear"></div>

<div class="numbr_graph">
<iframe src="/{$c['code']}.all.graph" style="width: 100%; height: 400px;" allowtransparnecy="true" frameborder="0"></iframe>

<br><div class="numbr_graph_embed_code">Embed code for graph: <input type="text" value="$graphembed"/></div>
</div>


            <h3>Description</h3> 
            <div class="numbr_description">
                    {$numbr['description']}
            </div>

             <h3>Source</h3>

             <a href="{$numbr['url']}" class="numbr_url">
                {$numbr['url']}
             </a>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js" ></script>
<script>
$("input").focus(function() {
    $(this).select();
});
</script>

END;




//========== template =========================

include ("template.php");
?>

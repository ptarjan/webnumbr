<?php
$ch = curl_init("http://" . $_SERVER['HTTP_HOST'] . "/wiki-en-pages.embed");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
$embed = htmlspecialchars(curl_exec($ch));

$ch = curl_init("http://" . $_SERVER['HTTP_HOST'] . "/piratebay-peers.all.graph.embed");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
$embedgraph = htmlentities(curl_exec($ch));

// ================ templates parts ===================

$subtitle = "Follow interesting numbers from anywhere on the web";
ob_start();
?>
                <div class="slogan">
                    Follow interesting numbers from anywhere on the web
                </div>


                <div id="examples">
                        <table cellpadding="5" width="90%" >
                            <tr>
                                <td>
                                    <a href="/webnumbrs">Number of Web Numbrs</a>
                                </td>
                                <td class="leftpadding">
                                    <span id="webNumbr_webnumbrs">Loading ...</span><script>var webNumbr_webnumbrs = function(data) { document.getElementById("webNumbr_webnumbrs").innerHTML = data; }</script><script src="http://webnumbr.com/webnumbrs.latest.json(callback=webNumbr_webnumbrs)"></script>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <a href="/yhoo">Yahoo stock price</a>
                                </td>
                                <td class="leftpadding">
                                    <span id="webNumbr_yhoo">Loading ...</span><script>var webNumbr_yhoo = function(data) { document.getElementById("webNumbr_yhoo").innerHTML = data; }</script><script src="http://webnumbr.com/yhoo.latest.json(callback=webNumbr_yhoo)"></script>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <a href="/temperature-sanjose">Temperature in San Jose</a>
                                </td>
                                <td class="leftpadding">
                                    <span id="webNumbr_temperature-sanjose">Loading ...</span><script>var webNumbr_temperature_sanjose = function(data) { document.getElementById("webNumbr_temperature-sanjose").innerHTML = data; }</script><script src="http://webnumbr.com/temperature-sanjose.latest.json(callback=webNumbr_temperature_sanjose)"></script>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <a href="/crude-oil-brent">Crude Brent Oil</a>
                                </td>
                                <td class="leftpadding">
                                    <span id="webNumbr_crude-oil-brent">Loading ...</span><script>var webNumbr_crude_oil_brent = function(data) { document.getElementById("webNumbr_crude-oil-brent").innerHTML = data; }</script><script src="http://webnumbr.com/crude-oil-brent.latest.json(callback=webNumbr_crude_oil_brent)"></script>
                                </td>
                            </tr>
<!--
                            <tr>
                                <td>
                                    <a href="/gas-sanjose">Cheapest gas in San Jose</a>
                                </td>
                                <td class="leftpadding">
                                    <span id="webNumbr_gas-sanjose">2.666</span><script>var webNumbr_gas_sanjose = function(data) { document.getElementById("webNumbr_gas-sanjose").innerHTML = data; }</script><script src="http://webnumbr.com/gas-sanjose.latest.json(callback=webNumbr_gas_sanjose)"></script>
                                </td>
                            </tr>
-->
                            <tr>
                                <td>
                                    <a href="/semantic-sm-video">Number of semantic video files</a>
                                </td>
                                <td class="leftpadding">
                                    <span id="webNumbr_semantic-sm-video">Loading ...</span><script>var webNumbr_semantic_sm_video = function(data) { document.getElementById("webNumbr_semantic-sm-video").innerHTML = data; }</script><script src="http://webnumbr.com/semantic-sm-video.latest.json(callback=webNumbr_semantic_sm_video)"></script>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <a href="/wiki-en-pages">Pages in English Wikipedia</a>
                                </td>
                                <td class="leftpadding">
                                    <span id="webNumbr_wiki-en-pages">Loading ...</span><script>var webNumbr_wiki_en_pages = function(data) { document.getElementById("webNumbr_wiki-en-pages").innerHTML = data; }</script><script src="http://webnumbr.com/wiki-en-pages.latest.json(callback=webNumbr_wiki_en_pages)"></script>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <a href="/earthquake-ca">Last earthquake in CA</a>
                                </td>
                                <td class="leftpadding">
                                    <span id="webNumbr_earthquake-ca">Loading ...</span><script>var webNumbr_earthquake_ca = function(data) { document.getElementById("webNumbr_earthquake-ca").innerHTML = data; }</script><script src="http://webnumbr.com/earthquake-ca.latest.json(callback=webNumbr_earthquake_ca)"></script>
                                </td>
                            </tr>
                        </table>
                
                <br/>
                <table id="embedexamples">
                <caption>Embed code examples</caption>
                <tr><td>
                    <span title="To embed the numbr 'wiki-en-pages' you can simply paste this onto your page">Pages in English Wikipedia (number)</span>
                </td><td>
                    <input value="<?php print $embed ?>" />
                </td></tr>
                <tr><td>
                    <span title="To embed the graph 'piratebay-peers' you can simply paste this onto your page">Users of Piratebay (graph)</span>
                </td><td>
                    <input value="<?php print $embedgraph ?>" />
                </td></tr>
                </table>

                </div>
                <div id="onsite">We find interesting numbers, create <b>numbr pages</b>, update their values <b>every hour</b> and keep the history. 
                <b><a href="/create">Create</a></b> a numbr from any webpage. 
                                  
                <br/><br/>Search numbrs, see a <b><a href="/random">random</a></b> one, browse <b><a href="/all">all</a></b> and bookmark your favorite.
                </div>
                <div id="embedcodes">With webNumbr.com you can <b>embed near real-time values</b> of any number on your page. 
                Just <b><a href="/create">create</a></b> the one you need, grab an embed code and place it on your website.
                You can embed graphs too.
                
                </div>
                <div class="clear">
                </div>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js" ></script>
<script>
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

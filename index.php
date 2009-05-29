<?php
$ch = curl_init("http://" . $_SERVER['HTTP_HOST'] . "/piratebay-peers.embed");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
$embed = htmlspecialchars(curl_exec($ch));

$ch = curl_init("http://" . $_SERVER['HTTP_HOST'] . "/wiki-en-pages.all.graph.embed");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
$embedgraph = htmlentities(curl_exec($ch));

// ================ templates parts ===================

$subtitle = "Can I get your number?";
ob_start();
?>
                <div class="slogan">
                    Follow interesting numbers from anywhere on the web
                </div>


                <div id="examples">
                        <table cellpadding="5" width="90%" >
                            <tr>
                                <td>
                                    <a href="/webnumbrs">Number of webNumbrs</a>
                                </td>
                                <td class="leftpadding">
                                    <span id="webNumbr_webnumbrs">76</span><script>var webNumbr_webnumbrs = function(data) { document.getElementById("webNumbr_webnumbrs").innerHTML = data; }</script><script src="http://webnumbr.com/webnumbrs.json(callback=webNumbr_webnumbrs)"></script>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <a href="/yhoo">Yahoo stock price</a>
                                </td>
                                <td class="leftpadding">
                                    <span id="webNumbr_yhoo">14.94</span><script>var webNumbr_yhoo = function(data) { document.getElementById("webNumbr_yhoo").innerHTML = data; }</script><script src="http://webnumbr.com/yhoo.json(callback=webNumbr_yhoo)"></script>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <a href="/temperature-sanjose">Temperature in San Jose</a>
                                </td>
                                <td class="leftpadding">
                                    <span id="webNumbr_temperature-sanjose">62</span><script>var webNumbr_temperature_sanjose = function(data) { document.getElementById("webNumbr_temperature-sanjose").innerHTML = data; }</script><script src="http://webnumbr.com/temperature-sanjose.json(callback=webNumbr_temperature_sanjose)"></script>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <a href="/crude-oil-brent">Crude Brent Oil</a>
                                </td>
                                <td class="leftpadding">
                                    <span id="webNumbr_crude-oil-brent">60.75</span><script>var webNumbr_crude_oil_brent = function(data) { document.getElementById("webNumbr_crude-oil-brent").innerHTML = data; }</script><script src="http://webnumbr.com/crude-oil-brent.json(callback=webNumbr_crude_oil_brent)"></script>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <a href="/gas-sanjose">Cheapest gas in San Jose</a>
                                </td>
                                <td class="leftpadding">
                                    <span id="webNumbr_gas-sanjose">2.666</span><script>var webNumbr_gas_sanjose = function(data) { document.getElementById("webNumbr_gas-sanjose").innerHTML = data; }</script><script src="http://webnumbr.com/gas-sanjose.json(callback=webNumbr_gas_sanjose)"></script>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <a href="/semantic-sm-video">Number of semantic video files</a>
                                </td>
                                <td class="leftpadding">
                                    <span id="webNumbr_semantic-sm-video">452000000</span><script>var webNumbr_semantic_sm_video = function(data) { document.getElementById("webNumbr_semantic-sm-video").innerHTML = data; }</script><script src="http://webnumbr.com/semantic-sm-video.json(callback=webNumbr_semantic_sm_video)"></script>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <a href="/wiki-en-pages">Pages in English Wikipedia</a>
                                </td>
                                <td class="leftpadding">
                                    <span id="webNumbr_wiki-en-pages">2896040</span><script>var webNumbr_wiki_en_pages = function(data) { document.getElementById("webNumbr_wiki-en-pages").innerHTML = data; }</script><script src="http://webnumbr.com/wiki-en-pages.json(callback=webNumbr_wiki_en_pages)"></script>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <a href="/slashdot-poll-votes">Votes on current /. poll </a>
                                </td>
                                <td class="leftpadding">
                                    <span id="webNumbr_slashdot-poll-votes">4772</span><script>var webNumbr_slashdot_poll_votes = function(data) { document.getElementById("webNumbr_slashdot-poll-votes").innerHTML = data; }</script><script src="http://webnumbr.com/slashdot-poll-votes.json(callback=webNumbr_slashdot_poll_votes)"></script>
                                </td>
                            </tr>
                        </table>
                
                <br>
                <table id="embedexamples">
                <caption>Embed code examples</caption>
                <tr><td>
                    <span href="#" title="To embed the numbr <b>wiki-en-pages</b> you can simply paste this onto your page">Pages in English Wikipedia (number)</span>
                </td><td>
                    <input value="<?php print $embed ?>" />
                </td></tr>
                <tr><td>
                    <span href="#" title="To embed the graph <b>piratebay-peers</b> you can simply paste this onto your page">Users of Piratebay (graph)</span>
                </td><td>
                    <input value="<?php print $embedgraph ?>" />
                </td></tr>
                </table>

                </div>
                <div id="onsite">We find interesting numbers, create <b>numbr pages</b>, update their values <b>every hour</b> and keep the history. 
                                  <b><a href="/create">Create</a></b> a numbr from any webpage. 
                                  
                                  <br><br>Search numbrs, see a <b><a href="/random">random</a></b> one, browse <b><a href="/all">all</a></b> and bookmark your favorite.
                </div>
                <div id="embedcodes">With webNumbr.com you can <b>embed near real-time values</b> of any number on your page. 
                Just <b><a href="/create">create</a></b> the one you need, grab an embed code and place it on your website.
                You can embed graphs too.
                
                </div>
                <div class="clear">
                </div>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js" ></script>
<script>
$("input").focus(function() {
    $(this).select();
    });
</script>


<?php 
$content = ob_get_clean();

//========== template =========================

include ("template.php");
?>

<?php

// webnumbr is ...
$thoughts = array(
"like OMG the GREATEST thing in like EVER!!!!",
/*
"superfly",
"adequate for my honored needs",
"like shooting a Winnebago over a crocodile pond",
"greater than e^(i\pi) - 1",
"<insert comment here>",
"horrible and show nev[CARRIER LOST]",
"in need of an urgent makeover",
*/
);
$thought = $thoughts[rand(0, count($thoughts)-1)];

$status = urlencode("@webnumbr  http://webnumbr.com  is $thought");

// ================ templates parts ===================

$subtitle = "Can I get your number?";

$content = <<<END

                <div id="top">
                    <div id="idea">
                        <h1>What we do:</h1>
                        <ul>
                            <li>
                                Extract numbers values from any webpage
                            </li>
                            <li>
                                Give them short names
                            </li>
                            <li>
                                Keep a history as they change
                            </li>
                           <li>
                                Make them reusable in all possible ways
                            </li>
                        </ul>
                    </div>
                    <div id="picture">
                        <img height="200" src="/images/webNumbr-explanation.png"/>
                    </div>
                </div>
                <div id="examples">
                    <div id="supply">
                 
                        <h1>Broadcasted numbrs</h1>

                            <div class="minimenu">
                            <a href="http://webnumbr.yury.name/search">Search numbrs</a> 
                            &nbsp;&nbsp; <a href="/random">Random numbr</a>
                            &nbsp;&nbsp; <a href="http://webnumbr.yury.name/selectNode">Start a numbr</a>
                            </div>


                        <table cellpadding="5" >
                            <tr>
                                <td>
                                    Number of webnumbers
                                </td>
                                <td class="leftpadding">
                                    <span id="webNumbr-webnumbrs">76</span><script>var webnumbr_webnumbrs = function(data) { document.getElementById("webNumbr-webnumbrs").innerHTML = data; }</script><script src="http://webnumbr.com/webnumbrs.embed.json(callback=webnumbr_webnumbrs)"></script>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Yahoo stock price
                                </td>
                                <td class="leftpadding">
                                    <span id="webNumbr-yhoo">14.98</span><script>var webnumbr_yhoo = function(data) { document.getElementById("webNumbr-yhoo").innerHTML = data; }</script><script src="http://webnumbr.com/yhoo.embed.json(callback=webnumbr_yhoo)"></script>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Temperature in San Jose
                                </td>
                                <td class="leftpadding">
                                    <span id="webNumbr-temperature-sanjose">52</span><script>var webnumbr_temperature-sanjose = function(data) { document.getElementById("webNumbr-temperature-sanjose").innerHTML = data; }</script><script src="http://webnumbr.com/temperature-sanjose.json(callback=webnumbr_temperature-sanjose)"></script>
                                </td>
                            </tr>
                        </table>

                    </div>
                    <div id="demand">
                        <h1>Use numbrs</h1>

                            <div class="minimenu">
                            <a href="http://webnumbr.yury.name/faq">Instructions</a> 
                            </div>

                        <ul>
                            <li>
                                Embed code: include real-time values of webNumbrs in your website
                            </li>
                            <li>
                                JSON, RSS, Email notifications
                            </li>
                            <li>Graph numbrs
                            </li>
                        </ul>
                    </div>
                <div class="clear"></div>
                </div>
                <div id="feedback">
                    <form action="/emailus">
                        <textarea id="feedbacktext" rows="3" name="useridea" type="text">Which numbers (and other data) should we broadcast?
How should we provide access to them?
                        </textarea>
                        <input type="submit" value="Send idea">
                    </form>
                </div>

END;

$header = <<<END
                    <table>
                        <tr>
                            <td valigin="center">
                                <a href='.'><img id='logo' src="images/webNumbr-banner-100.png" alt="logo" /></a>
                            </td>
                            <td valign="center" style="padding-left:100px; font-size:48px;">
                                <a style="text-decoration:none;" href="http://twitter.com/home?status=$status">Comments?<img height="36" src="/images/twitter.jpg"/></a>
                            </td>
                        </tr>
                    </table>

END;



//========== template =========================

include ("template.php");
?>

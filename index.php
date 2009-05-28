<?php


// ================ templates parts ===================

$subtitle = "Can I get your number?";

$content = <<<END

                <h2 style="margin:0px auto;"></h2>

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
                </div>
                <div id="examples">
                    <div id="supply">
                 
                        <h1>Broadcasted numbrs</h1>

 
                        <table cellpadding="5" >
                            <tr>
                                <td>
                                    Number of webNumbrs
                                </td>
                                <td class="leftpadding">
                                    <span id="webNumbr_webnumbrs">76</span><script>var webNumbr_webnumbrs = function(data) { document.getElementById("webNumbr_webnumbrs").innerHTML = data; }</script><script src="http://webnumbr.com/webnumbrs.embed.json(callback=webNumbr_webnumbrs)"></script>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Yahoo stock price
                                </td>
                                <td class="leftpadding">
                                    <span id="webNumbr_yhoo">14.94</span><script>var webNumbr_yhoo = function(data) { document.getElementById("webNumbr_yhoo").innerHTML = data; }</script><script src="http://webnumbr.com/yhoo.embed.json(callback=webNumbr_yhoo)"></script>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Temperature in San Jose
                                </td>
                                <td class="leftpadding">
                                    <span id="webNumbr_temperature-sanjose">64</span><script>var webNumbr_temperature-sanjose = function(data) { document.getElementById("webNumbr_temperature-sanjose").innerHTML = data; }</script><script src="http://webnumbr.com/temperature-sanjose.embed.json(callback=webNumbr_temperature-sanjose)"></script>
                                </td>
                            </tr>
                        </table>

                    </div>
                    <div id="demand">
                        <h1>Use numbrs</h1>

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




//========== template =========================

include ("template.php");
?>

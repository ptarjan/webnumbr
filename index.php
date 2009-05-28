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
                                    Number of webnumbers
                                </td>
                                <td class="leftpadding">
                                    <span id="webnumbr">webnumbr</span><script>var webnumbr = function(data) { document.getElementById("webnumbr").innerHTML = data; }</script><script src="http://webnumbr.com/webnumbrs.json(callback=webnumbr)"></script>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Yahoo stock price
                                </td>
                                <td class="leftpadding">
                                    <span id="webnumbr">webnumbr</span><script>var webnumbr = function(data) { document.getElementById("webnumbr").innerHTML = data; }</script><script src="http://webnumbr.com/yhoo.json(callback=webnumbr)"></script>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Crude Brent Oil 
                                </td>
                                <td class="leftpadding">
                                    <span id="webnumbr">webnumbr</span><script>var webnumbr = function(data) { document.getElementById("webnumbr").innerHTML = data; }</script><script src="http://webnumbr.com/crude-oil-brent.json(callback=webnumbr)"></script>
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

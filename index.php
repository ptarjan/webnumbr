<?php


// ================ templates parts ===================

$subtitle = "Can I get your number?";

$content = <<<END

                <div class="slogan">Follow interesting numbers on our website or yours</div>


                <div id="examples">
                        <table cellpadding="5" width="90%" >
                            <tr>
                                <td>
                                    Number of webnumbrs
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
                            <tr>
                                <td>
                                    Crude Brent Oil 
                                </td>
                                <td class="leftpadding">
                                    <span id="webnumbr">webnumbr</span><script>var webnumbr = function(data) { document.getElementById("webnumbr").innerHTML = data; }</script><script src="http://webnumbr.com/crude-oil-brent.json(callback=webnumbr)"></script>
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
                            <tr>
                                <td>
                                    Crude Brent Oil 
                                </td>
                                <td class="leftpadding">
                                    <span id="webnumbr">webnumbr</span><script>var webnumbr = function(data) { document.getElementById("webnumbr").innerHTML = data; }</script><script src="http://webnumbr.com/crude-oil-brent.json(callback=webnumbr)"></script>
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
                <div id="onsite">We find interesting numbers, create <b>numbr pages</b>, updating their values <b>every hour</b> and keep the history. 
                                  You can <b><a href="/create">create</a></b> them too! 
                                  
                                  <br><br>Search numbrs, check a <b><a href="/random">random</a></b> one, browse <b><a href="/all">all</a></b> and bookmark your favorite.
                </div>
                <div id="embedcodes">With webNumbr.com you can <b>embed near real-time values</b> of any number on your page. 
                Just <b><a href="/create">create</a></b> the one you need, grab an embed code and place it to your website.
                You can embed graphs too.
                
                <br><br>Embed numbr example
                <br>Embed graph example
                </div>
                <div class="clear">
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

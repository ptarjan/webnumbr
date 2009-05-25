<?php
print '<?xml version="1.0" encoding="UTF-8"?>
';
?> <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <title>webNumbr: Can I get your Numbr?</title>
        <link rel="stylesheet" href="style.css" type='text/css' />
    </head>
    <body>
        <center>
            <div id="wrap">
                <div id="header">
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
                    ?>
                    <table>
                        <tr>
                            <td valigin="center">
                                <a href='.'><img id='logo' src="images/webNumbr-banner-100.png" alt="logo" /></a>
                            </td>
                            <td valign="center" style="padding-left:100px; font-size:48px;">
                                <a style="text-decoration:none;" href="http://twitter.com/home?status=<?php print urlencode("@webnumbr  http://webnumbr.com  is $thought") ?>">Comments?<img height="36" src="/images/twitter.jpg"/></a>
                            </td>
                        </tr>
                    </table>
                </div>
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
                                    <span id="webNumbr-webnumbrs">76</span><script>var webnumbr = function(data) { document.getElementById("webNumbr-webnumbrs").innerHTML = data; }</script><script src="http://webnumbr.com/webnumbrs.embed.json(callback=webnumbr)"></script>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Yahoo stock price
                                </td>
                                <td class="leftpadding">
                                    <span id="webNumbr-yhoo">14.98</span><script>var webnumbr = function(data) { document.getElementById("webNumbr-yhoo").innerHTML = data; }</script><script src="http://webnumbr.com/yhoo.embed.json(callback=webnumbr)"></script>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Crude Brent Oil 
                                </td>
                                <td class="leftpadding">
                                    <span id="webNumbr-temperature-sanjose">52</span><script>var webnumbr = function(data) { document.getElementById("webNumbr-temperature-sanjose").innerHTML = data; }</script><script src="http://webnumbr.com/temperature-sanjose.json(callback=webnumbr)"></script>
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

                <center>
                    <div id="footer">
                        <a href="/">webNumbr</a> by <a  href="http://paulisageek.com">Paul</a> and <a href="yury.name">Yury</a>
                    </div>
                </center>
            </div>
        </center>

        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
        <script>
$(function() {
    $("#feedbacktext").focus(function() {
        $(this).css("color", "black").val("");
    });
});
        </script>
        <?php include("ga.inc") ?>
    </body>
</html>

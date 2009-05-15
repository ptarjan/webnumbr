<?php
print '<?xml version="1.0" encoding="UTF-8"?>';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>webGraphr - Graph anything on the web</title>
    <link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/2.7.0/build/reset/reset-min.css" />
    <link rel="stylesheet" href="/style.css" type='text/css' />  
    <link rel="stylesheet" href="style.css" type='text/css' />  
    
    <link rel="icon" href="images/webGraphr-favicon.png" type="image/x-icon" />

  </head>
  <body>
    <div id='container'>
      <div id='header'>
        <a href='.'><img id='logo' src="images/webGraphr-banner-100.png" alt="logo" /></a>
      </div>

      <div class="content">

        <iframe style="float: left; padding: 10px 20px; width: 450px; height: 300px;" src="http://paulisageek.com/webGraphr/embedGraph?type=js&amp;id=4" frameborder="0" allowtransparency="true"></iframe>
        <iframe style="float: right; padding: 10px 20px; width: 450px; height: 300px;" src="http://paulisageek.com/webGraphr/embedGraph?type=js&amp;id=16" frameborder="0" allowtransparency="true"></iframe>

        <p>This site builds graphs from the web -&gt; Web Graphs -&gt; webGrapher -&gt; webGraphr (web 2.0 names are awesome, and <a href="http://search.yahoo.com/search?q=webGraphr">quite</a> <a href="http://www.google.com/search?q=webGraphr">unique</a>).</p>
        <p>If you want to know how data on a webpage changes over time, just put the URL in the <a href='selectNode'>text box</a>, click on the number that you want graphed, name your graph, and then sit back and enjoy the pretty, mesmerizing data.</p>
        <p>Once you like it, you can embed it in your site, or use the API to do something fancy, or just keep coming back to oooo and aaaahhh at how pretty your graph looks. <a href="about">Read More..</a></p>
        <p class="clear" />
        <p class="center">See a <a href="random">random graph</a> to give you an idea of what this is all about.</p>

        <h1 id='start'>
          Start a Graph
        </h1>

        <form action='selectNode'>
          <div id="startForm"> 
            <label id="urlLabel" for="url">URL:</label>
            <input id="url" name='url' value="http://" />
            <input id="submitURL" type='submit' value='Pick the Number on the Page' />
          </div>
        </form>

        <h1>Search All Graphs</h1>

        <form action="search">
          <div id="searchForm">
            <input id="query" name='query' />
            <input id="submitQuery" type="submit" value='Search' />
          </div>
        </form>

        <h1>Last 10 Graphs</h1>

        <ul>
<?php
require ("db.inc");
$stmt = $PDO->prepare("SELECT name, id, url FROM graphs ORDER BY createdTime DESC LIMIT 10");
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($data as $row) {
    $url = substr($row['url'], 0, 30);
    if (strlen($row['url']) > 30) $url .= "...";

    $name = substr($row['name'], 0, 50);
    if (strlen($row['name']) > 50) $name .= "...";

    print "          <li><a href='graph?id=" . htmlspecialchars($row['id']) . "'>" . htmlspecialchars($name) . "</a> (<span title='" . htmlspecialchars($row['url']) . "'>" . htmlspecialchars($url) . "</span>)</li>\n";
}
?>
        </ul>

        <h1>Most Popular 10 <a href="allhosts">Hosts</a></h1>

        <ul>
<?php
$stmt = $PDO->prepare('SELECT COUNT(*) AS count, @START:=LOCATE("/", url)+1, @END:=LOCATE("/", url, @START+1), SUBSTRING(url, @START+1, IF(@END = 0, LENGTH(url)+1, @END) - @START-1) as domain FROM graphs GROUP BY domain ORDER BY count DESC LIMIT 10');
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($data as $row) {
    print "            <li><a href='search?query=" . htmlspecialchars($row['domain']) . "'>" . htmlspecialchars($row['domain']) . "</a> (" . htmlspecialchars($row['count']) . ")</li>\n";
}
?>
        </ul>
          
        <h1>News</h1>
        <ul><li>
        <span class="date">May 14, 2009</span> :  <span>I'm back from <a href="http://paulisageek.blogspot.com/2009/05/st-maartenmartin.html">vacation</a> (hense the lack of news). As alluded to before, you can now <a href="http://paulisageek.com/webGraphr/createGraph?mode=edit&id=27">edit</a> a graph that has your openid as the owner.
        </li><li>
        <span class="date">April 27, 2009</span> :  <span>Since my friend <a href="http://dawning.ca">Jamie</a> made a <a href="graph?id=50">graph</a> that I had NO IDEA what it was doing, I wrote a <a href="selectNode?url=http%3A%2F%2Fpaulisageek.com%2FwebGraphr%2F&xpath=%2F%2Fdiv%5B%40id%3D%27container%27%5D%2Fdiv%5B2%5D%2Ful%5B2%5D%2Fli%5B1%5D&action=show">little visualizer</a> to see what graphs do. Just go to the graph info, and click on the xpath.</span>
        </li><li>
        <span class="date">April 26, 2009</span> :  <span>You can now spend ALL DAY looking at <a href="random">random graphs</a></span>
        </li><li>
        <span class="date">April 24, 2009</span> :  <span><a href="selectNode?url=http%3A%2F%2Fquery.yahooapis.com%2Fv1%2Fpublic%2Fyql%3Fq%3Dselect%2520*%2520from%2520geo.places%2520where%2520text%253D%2522san%2520jose%2522%26format%3Dxml">Attributes</a> are now selectable with your mouse. And you can get the xml output directly by putting <a href="selectNode?url=http%3A%2F%2Fquery.yahooapis.com%2Fv1%2Fpublic%2Fyql%3Fq%3Dselect%2520*%2520from%2520geo.places%2520where%2520text%253D%2522san%2520jose%2522%26format%3Djson&format=xml">&amp;format=xml</a> in the query string.</span>
        </li><li>
        <span class="date">April 22, 2009</span> :  <span><a href="selectNode?url=http%3A%2F%2Fquery.yahooapis.com%2Fv1%2Fpublic%2Fyql%3Fq%3Dselect%2520*%2520from%2520geo.places%2520where%2520text%253D%2522san%2520jose%2522%26format%3Dxml">XML</a> is also now supported. I strip the default namespace on docs, but other <a href="selectNode?url=http%3A%2F%2Fpaulisageek.blogspot.com%2Ffeeds%2Fposts%2Fdefault%3Falt%3Drss">namespaces should work</a>. <a href="selectNode?url=http%3A%2F%2Fquery.yahooapis.com%2Fv1%2Fpublic%2Fyql%3Fq%3Dselect%2B*%2Bfrom%2Bflickr.photos.search%2Bwhere%2Bhas_geo%253D%2522true%2522%2Band%2Btext%253D%2522san%2Bfrancisco%2522">Attribute selecting</a> is a pain, but you can do it yourself in XPath (with /some/path/to/node/@id). One more "gotcha" is that the automatic xpath only can do lowercase nodes (since most browsers don't preserve case), you might have to fix up the case yourself.</span>
        </li><li>
        <span class="date">April 22, 2009</span> :  <span>Sweet! I figured out a <span style="text-decoration:line-through;">hackey</span> great way to select <a href="selectNode?url=http%3A%2F%2Fquery.yahooapis.com%2Fv1%2Fpublic%2Fyql%3Fq%3Dselect%2520*%2520from%2520flickr.photos.search%2520where%2520has_geo%253D%2522true%2522%2520and%2520text%253D%2522paul%2520tarjan%2522%26format%3Djson%26callback%3D">JSON nodes</a>.</span>
        </li><li>
        <span class="date">April 21, 2009</span> :  <span>You can now convert <a href="selectNode?url=http%3A%2F%2Fquery.yahooapis.com%2Fv1%2Fpublic%2Fyql%3Fq%3Dselect%2520*%2520from%2520flickr.photos.search%2520where%2520has_geo%253D%2522true%2522%2520and%2520text%253D%2522paul%2520tarjan%2522%26format%3Djson%26callback%3D">JSON to XML</a>. Let me know if it is working for you, or if you have any better ideas for the xpath generation.</span>
        </li><li>
        <span class="date">April 19, 2009</span> :  <span>I wish I was your <a href="graph?id=4&derivative=1">derivative</a> so I could lie <a href="graph?id=4&derivative=2">tangent</a> to your <a href="graph?id=4&derivative=3">curves</a>.</span>
        </li><li>
        <span class="date">April 19, 2009</span> :  <span>Now you can <a href="createGraph?parent=4">extend</a> graphs if the xpath breaks or you mis-typed something.</span>
        </li><li>
        <span class="date">April 19, 2009</span> :  <span>Added <a href="http://openid.net">OpenID</a> when creating graphs. Hmmm, what could come next ...</span>
        </li><li>
        <span class="date">April 18, 2009</span> :  <span>Launched. The web gets graphier!</span>
        </li></ul>

        <h1>Suggestions? Bugs? Requests? Fan Mail?</h1>
      
        <div class='center'>
          <a href='mailto:webGraphr@paulisageek.com'>webGraphr@paulisageek.com</a>
        </div>


      </div>
    </div>

<script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jquery/1.3/jquery.js'></script>

<script type='text/javascript'>
var resizeURL = function() {
    $("#url").width($("#startForm").width() - $("#urlLabel").outerWidth(true) - $("#submitURL").outerWidth(true) - 20);
};
$("#startForm").ready(function() {
    $(window).resize(resizeURL);
    resizeURL();
});

var resizeSearch = function() {
    $("#query").width($("#searchForm").width() - $("#submitQuery").outerWidth() - 20);
};
$("#startForm").ready(resizeSearch);
$(window).resize(resizeSearch);
</script>

<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("UA-149816-4");
pageTracker._trackPageview();
} catch(err) {}
</script>

  </body>
</html>

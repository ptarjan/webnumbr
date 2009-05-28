<?php
print '<?xml version="1.0" encoding="UTF-8"?>
';

// webnumbr is ...
$thoughts = array(
"like OMG the GREATEST thing in like EVER!!!!",
/*
"superfly",
"adequate for my honored needs",
"like shooting a Winnebago over a crocodile pond",
"greater than e^(i\pi) - 1",
"<insert comment here>",
"horrible and should nev[CARRIER LOST]",
"in need of an urgent makeover",
*/
);
$thought = $thoughts[rand(0, count($thoughts)-1)];

$status = urlencode("@webnumbr http://webnumbr.com is $thought");

?> 

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <title>webNumbr | <?php print $subtitle ?></title>
        <link rel="stylesheet" href="style.css" type='text/css' />
    </head>
    <body>
            <div id="wrap">
                <div id="header">
                        <span class="mainmenu">
                        <a href="/">Home</a> <a href="/create">Create</a> <a href="/all">All</a> <a href="/random">Random</a> <a href="/about">About</a> 
                        <form id="search_form" action="/search"> Search : <input name="query" value="<?php print $current_search ?>" /> </form>
                        </span>
                        <span class="logo">
                            <a href='/'><img id='logopic' src="images/webNumbr-banner-50.png" alt="logo" /></a>
                        </span>
                        <div class="clear"></div>
                </div>
				<div id="content">
				<?php print $content ?>
				</div>
                    <div id="footer">
                                <a href="http://twitter.com/home?status=<?php print $status ?>">Comments? <img height="20" src="/images/twitter.jpg"/></a>
                        
                    </div>
            </div>

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

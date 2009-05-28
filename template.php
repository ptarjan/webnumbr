

<?php
print '<?xml version="1.0" encoding="UTF-8"?>
';
?> <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <title>webNumbr | <?php print $subtitle ?></title>
        <link rel="stylesheet" href="style.css" type='text/css' />
    </head>
    <body>
        <center>
            <div id="wrap">
                <div id="header">
                <?php print $header ?>
                </div>
				<div id="content">
				<?php print $content ?>
				</div>
                <center>
                    <div id="footer">
                        <a href="/about">About webNumbr</a>
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

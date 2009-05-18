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
<style type="text/css">
#tweet {
    float : left; 
    position : absolute; /* don't take up any space */
    background : white; 
    margin : 0px; 
    padding : 5px;
    vertical-align : middle; 
    border : 1px solid;
    border-top : none;
}
#tweet a {
    text-decoration : none
}
#tweet a:visited {
    color : blue
}
#tweet a:hover {
    text-decoration : underline
}
</style>
    <div id="tweet">
        <a href="http://twitter.com/home?status=<?php print urlencode("@ptarjan http://webnumbr.com is $thought") ?>"><img src="http://twitter.com/favicon.ico"/ >Comments?</a>
    </div>

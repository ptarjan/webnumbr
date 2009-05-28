<?php


// ================ templates parts ===================

$subtitle = "Numbr title";

$content = <<<END

<div class="numbr_card">
            <div class="numbr_title">
                    Numbr title
            </div>
            <div class="numbr_box">
                    $data              
            </div>
<div class="numbr_embed_code">Embed code: <input type="text" value="embed code"/></div>

            <div class="clear"></div>

<div class="numbr_graph">
graph

<br><div class="graph_embed_code">Embed code for graph: <input type="text" value="embed code"/></div>
</div>


            <div class="numbr_description">
                    <h3>Description</h3> 
                    description text

                 <br>&nbsp;<h3>Source</h3>

                <a href="http://www.expedia.com/pub/agent.dll?qscr=fexp&amp;flag=q&amp;city1=SJC&amp;citd1=SXM&amp;date1=5/1/2009&amp;time1=362&amp;date2=5/9/2009&amp;time2=362&amp;cAdu=1&amp;cSen=&amp;cChi=&amp;cInf=&amp;infs=2&amp;tktt=&amp;trpt=2&amp;ecrc=&amp;eccn=&amp;qryt=8&amp;load=1&amp;airp1=&amp;dair1=&amp;rdct=1&amp;rfrr=-429">
                    http://www.expedia.com/pub/agent.dll?qscr=fexp&amp;flag=q&amp;city1=SJC&amp;citd1=SXM&amp;date1=5/1/2009&amp;time1=36...                </a>
            </div>



END;




//========== template =========================

include ("template.php");
?>

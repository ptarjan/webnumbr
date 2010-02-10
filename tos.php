<?php
// ================ templates parts ===================

$subtitle = "Terms of Service";
ob_start();
?>

<br/>
<h1 class="first">Terms of Service</h1>
<p>
Access is granted to the Service "AS IS", WITHOUT WARRANTY OF ANY KIND. OWNER EXPRESSLY DISCLAIMS ALL WARRANTIES OR CONDITIONS, EXPRESS OR IMPLIED, INCLUDING, BUT NOT LIMITED TO THE IMPLIED WARRANTIES OR CONDITIONS OF TITLE, OWNERSHIP, MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE, NON-INFRINGEMENT AND FREEDOM FROM INTERFERENCE WITH YOUR ENJOYMENT. 

<p>
You are solely responsible for determining the appropriateness of this Service for your purpose, and assume all risks associated with its use, including risks connected with your submission of information. The risks you assume include, but are not limited to, the risks of program errors; damage to or loss of information, programs or equipment; and unavailability or interruption of operations. Owner is not responsible for the accuracy, completeness, timeliness, reliability, content or availability of the Service or results from it. 

<p>
Owner will not be liable for any direct damages; special, incidental, or indirect damages; or economic consequential damages (including lost profits or savings), even if Owner has been advised of the possibility of such damages. Owner will not be liable for the loss of or damage to your information or results, or any damages claimed by you based on a third party claim, including a licensor of information you submit.

<?php 
$content = ob_get_clean();

//========== template =========================

include ("template.php");
?>

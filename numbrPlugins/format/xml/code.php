<?php

require "XMLHelper.inc";
$c['header'][] = "Content-Type: application/xml";
print XMLHelper::xml_encode($data);

?>

<?php

require "XMLHelper.inc";
$c['headers'][] = "Content-Type: application/xml";
print XMLHelper::xml_encode($data);

?>

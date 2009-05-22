<?php

require "XMLHelper.inc";
// header("Content-Type: application/xml");
print XMLHelper::xml_encode($data);

?>

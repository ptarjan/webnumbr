<?php
ini_set('include_path', realpath(dirname(__FILE__)));
header('Content-Type: text/plain');
require_once 'MC/Google/Visualization.php';

require '../../../db.inc';
$vis = new MC_Google_Visualization($PDO);

$vis->addEntity('numbr_data', array(
    'fields' => array(
        'timestamp' => array('field' => 'timestamp', 'type' => 'timestamp'),
        'data' => array('field' => 'data', 'type' => 'number'),
    ), 
    'where' => 'numbr = \''.$c['name'].'\''
));
$vis->setDefaultEntity('numbr_data');

$vis->handleRequest();
?>

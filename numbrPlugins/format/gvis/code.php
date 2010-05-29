<?php
ini_set('include_path', realpath(dirname(__FILE__)));
header('Content-Type: text/plain');
require_once 'MC/Google/Visualization.php';

require '../../../db.inc';
$vis = new MC_Google_Visualization($PDO);

$fields = array();
if (!$params['timestamp'])
  $fields['timestamp'] = array('field' => 'timestamp', 'type' => 'timestamp');

$fields[$c['name']] = array('field' => 'data', 'type' => 'number');

$vis->addEntity('numbr_data', array(
    'fields' => $fields,
    'where' => 'numbr = \''.$c['name'].'\''
));
$vis->setDefaultEntity('numbr_data');

$vis->handleRequest();
?>

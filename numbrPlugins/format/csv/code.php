<?php
$c['headers'][] = "Content-Type: text/csv";

if (is_array($data))
    foreach ($data as $row) {
        print "$row[0],$row[1]\n";
    }
else
    print $data;

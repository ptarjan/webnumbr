<?php
$options = getopt("nfdp");
if (isset($options['n'])) {
    $insert = FALSE;
    print "NOT INSERTING -- DRY RUN\n\n";
} else {
    $insert = TRUE;
}

if (isset($options['f'])) {
    $force = TRUE;
    print "FORCING FETCH -- IGNORING FREQUENCY\n\n";
} else {
    $force = FALSE;
}

if (isset($options['d'])) {
    $dev = TRUE;
    print "DEV MODE -- USING DEV INCLUDE FILES\n\n";
} else {
    $dev = FALSE;
}

if (isset($options['p'])) {
    $print = TRUE;
    print "PRINT MODE -- PRINTING NUMBER\n\n";
} else {
    $print = FALSE;
}

function p($row, $str, $type = "error") {
    if (isset($row['name'])) $name = $row['name'];
    else $name = $row;
    print date("r") . ": $name: $type: $str\n" ;
}

if ($dev) {
    require "/var/www/paul.slowgeek.com/dev/webnumbr/db.inc";
    require "/var/www/paul.slowgeek.com/dev/webnumbr/fetch.inc";
} else {
    require "/var/www/webnumbr/db.inc";
    require "/var/www/webnumbr/fetch.inc";
}

$stmt = $PDO->prepare("
SELECT * FROM numbrs WHERE is_fetching = 1 ORDER BY goodFetches / (goodFetches + badFetches) DESC
");
$stmt->execute();
$graphs = $stmt->fetchAll();

$stmtTimestamp = $PDO->prepare("SELECT UNIX_TIMESTAMP(MAX(timestamp)) AS maxts FROM numbr_data WHERE numbr = :name");
$stmtInsert = $PDO->prepare("INSERT INTO numbr_data (numbr, data) VALUES (:name, :data)");
$stmtBad = $PDO->prepare("UPDATE numbr_table SET badFetches = badFetches + 1, badFetchesSequential = badFetchesSequential + 1 WHERE name = :name");
$stmtGood = $PDO->prepare("UPDATE numbr_table SET goodFetches = goodFetches + 1, badFetchesSequential = 0 WHERE name = :name");
libxml_use_internal_errors(true);
$cache = array();

p("---", "--- Starting ---", "info");

foreach ($graphs as $row) {
    // find the next time it should run
    $stmtTimestamp->execute(array("name" => $row['name']));
    $result = $stmtTimestamp->fetchAll();
    // Time minus 5 minutes
    if ($result[0]['maxts'] + $row['frequency'] * 60 * 60 - (5 * 60) > time()) 
        if (!$force)
            continue;

    p($row, "running", "info");

    try {

        if (! isset($cache[$row['url']])) {
            p($row, "fetching: " . $row['url'], "info");
            $cache[$row['url']] = fetch($row['url'], null);
        }
        $dom = $cache[$row['url']];
        $num = fetch($dom, $row['xpath']);

    } catch (FetchException $e) {
        p($row, $e->getMessage());
        if ($insert) {
            $r = $stmtBad->execute(array("name" => $row["name"]));
            if (! $r) { p($row, "SQL error\n" . print_r($stmtInsert->errorInfo(), TRUE)); }
        }
        continue;
    }
    if ($print) {
        p($row, $num, "num");
    }
    
    if ($insert) {
        $r = $stmtInsert->execute(array("name" => $row['name'], "data" => $num));
        if (! $r) { p($row, "SQL error\n" . print_r($stmtInsert->errorInfo(), TRUE)); }
        $r = $stmtGood->execute(array("name" => $row["name"]));
        if (! $r) { p($row, "SQL error\n" . print_r($stmtInsert->errorInfo(), TRUE)); }
    }
}

p("---", "--- Ending ---", "info");

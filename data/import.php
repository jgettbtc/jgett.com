<?php

require_once('../app/includes/class.importer.php');

set_time_limit(0);

header('Content-Type: application/json');

function handleBitboImport($importer){
    $interval = $_GET['interval'] ?? "30_min";
    $limit = $_GET['limit'] ?? "336";
    $insert = ($_GET["insert"] ?? "true") === "true";
    echo json_encode($importer->import_bitbo($interval, $limit, $insert));
}

function handleYahooImport($importer){
    $p1 = $_GET['p1'] ?? date('Y-m-d');
    $p2 = $_GET['p2'] ?? date('Y-m-d', strtotime($p1) + 86400);
    $interval = $_GET['interval'] ?? "1d";
    echo json_encode($importer->import_yahoo($p1, $p2, $interval));
}

function handleFileImport($importer){
    $exchange = $_GET['exchange'] ?? "";
    if ($source)
        if ($exchange)
            echo json_encode($importer->import_file($command, $exchange, $source));
        else
            throw new Exception('missing required parameter: exchange');
    else
        throw new Exception('missing required parameter: source');
}

$importer = new Importer();

$command = $_GET['command'] ?? "";
$source = $_GET['source'] ?? "";

try{
    if ($command === "import" && $source === "bitbo")
        handleBitboImport($importer);
    else if ($command === "import" && $source === "yahoo")
        handleYahooImport($importer);
    else if ($command === "import" || $command === "count")
        handleFileImport($importer);
    else
        throw new Exception('invalid parameters');
}catch (Exception $e){
    http_response_code(400);
    echo json_encode(array('error_status' => 'bad_request', 'error_message' => $e->getMessage()));
}

$importer->close();

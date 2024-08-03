<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");

    require('../../config.php');
    include('../database.php');
    include('../checker.php');
    include('../logger.php');

    $key = isset($_GET['key']) ? $_GET['key'] : "";
    $loggerName = "API-ListKey";

    // Check if the key is valid and the action is defined
    $perms = verifyAPIKey($key);
    if($perms === null) {
        exit; // The verifyAPIKey function has already returned an appropriate response and terminated the execution
    }

    if(!verifyKeyPerms($key, $perms, PERMISSION_LIST_KEY)) {
        exit; // The verifyKeyPerms function has already returned an appropriate response and terminated the execution
    }
    
    if($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        $data = json_decode(file_get_contents("php://input"), true);
    } else {
        makeLog($loggerName, $key, "Wrong method request", 1);
        echo json_encode(["code" => INVALID_API_METHOD, "message" => "Create project API can only take DELETE method"]);
        exit;
    }
?>
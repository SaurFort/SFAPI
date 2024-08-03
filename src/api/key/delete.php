<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");

    require('../../config.php');
    include('../database.php');
    include('../checker.php');
    include('../logger.php');

    $key = isset($_GET['key']) ? $_GET['key'] : "";
    $loggerName = "API-DeleteKey";

    // Check if the key is valid and the action is defined
    $perms = verifyAPIKey($key);
    if($perms === null) {
        exit; // The verifyAPIKey function has already returned an appropriate response and terminated the execution
    }

    if(!verifyKeyPerms($key, $perms, PERMISSION_DELETE_KEY)) {
        exit; // The verifyKeyPerms function has already returned an appropriate response and terminated the execution
    }
    
    if($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        $data = json_decode(file_get_contents("php://input"), true);

        if(isset($data['key']) && !verifyKeyPerms($key, $perms, PERMISSION_ACCESS_OTHER_USER_KEY, true)) {
            makeLog($loggerName, $key, "An user just tried to delete a key of an other user without a super key, " . $data['key'], 3);
            echo json_encode(["code" => API_KEY_DELETE_ARGUMENT_ERROR, "message" => "You can't delete a key of an other user without a super key"]);
            exit;
        } else {
            
        }


    } else {
        makeLog($loggerName, $key, "Wrong method request", 1);
        echo json_encode(["code" => INVALID_API_METHOD, "message" => "Create project API can only take DELETE method"]);
        exit;
    }
?>
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
        } elseif(isset($data['key']) && verifyKeyPerms($key, $perms, PERMISSION_ACCESS_OTHER_USER_KEY, true)) {
            makeLog($loggerName, $key, "An user gonna delete the key " . $data['key'], 3);
            $targetKey = $data['key'];
        } else {
            $targetKey = $key;
        }

        $query = "DELETE FROM api_keys WHERE api_key = ?";
        $stmt = $conn->prepare($query);

        if($stmt === false) {
            makeLog($loggerName, $key, "SQL Prepare Error: " . $conn->error, 2);
            echo json_encode(["code" => SQL_PREPARE_ERROR, "message" => "SQL Prepare Error: " . $conn->error]);
            exit;
        }

        $stmt->bind_param("s", $targetKey);
        
        if($stmt->execute()) {
            makeLog($loggerName, $key, "The key $targetKey was successfully deleted", 4);
            echo json_encode(["code" => QUERY_WORKED_SUCCESSFULLY, "message" => "The key was successfully deleted"]);
        } else {
            makeLog($loggerName, $key, "SQL Error: " . $conn->error, 3);
            echo json_encode(["code" => SQL_QUERY_ERROR, "message" => "SQL Error: " . $conn->error]);
            exit;
        }
    } else {
        makeLog($loggerName, $key, "Wrong method request", 1);
        echo json_encode(["code" => INVALID_API_METHOD, "message" => "Delete key API can only take DELETE method"]);
        exit;
    }
?>
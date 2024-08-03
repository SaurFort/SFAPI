<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");

    require('../../config.php');
    include('../database.php');
    include('../checker.php');
    include('../logger.php');

    $key = isset($_GET['key']) ? $_GET['key'] : "";
    $loggerName = "API-DisableKey";

    // Check if the key is valid and the action is defined
    $perms = verifyAPIKey($key);
    if($perms === null) {
        exit; // The verifyAPIKey function has already returned an appropriate response and terminated the execution
    }

    if(!verifyKeyPerms($key, $perms, PERMISSION_DISABLE_KEY)) {
        exit; // The verifyKeyPerms function has already returned an appropriate response and terminated the execution
    }
    
    if($_SERVER['REQUEST_METHOD'] === 'PUT') {
        $data = json_decode(file_get_contents("php://input"), true);

        if(isset($data['key']) && verifyKeyPerms($perms, $key, PERMISSION_ACCESS_OTHER_USER_KEY, true)) {
            $targetKey = $data['key'];
        } elseif(isset($data['key']) && !verifyKeyPerms($perms, $key, PERMISSION_ACCESS_OTHER_USER_KEY, true)) {
            makeLog($loggerName, $key, "An user tried to disable a key of an other user, targeted key: " . $data['key'], 2);
            echo json_encode(["code" => API_KEY_ENABLE_OR_DISABLE_ARGUMENT_ERROR . "B", "message" => "You can't disable the key of an other user"]);
            exit;
        } else {
            $targetKey = $key;
        }

        $status = 1;

        $query = "UPDATE api_keys SET is_active = ? WHERE api_key = ?";
        $stmt = $conn->prepare($query);

        if($stmt === false) {
            makeLog($loggerName, $key, "SQL Prepare Error: " . $conn->error, 2);
            echo json_encode(["code" => SQL_PREPARE_ERROR, "message" => "SQL Prepare Error: " . $conn->error]);
            exit;
        }

        $stmt->bind_param("is", $status, $targetKey);

        if($stmt->execute()) {
            makeLog($loggerName, $key, "$targetKey was successfully disabled", 3);
            echo json_encode(["code" => QUERY_WORKED_SUCCESSFULLY, "message" => "The key was successfully disabled"]);
        } else {
            makeLog($loggerName, $key, "SQL Error: " . $conn->error, 2);
            echo json_encode(["code" => SQL_QUERY_ERROR, "message" => "SQL Error: " . $conn->error]);
            exit;
        }
    } else {
        makeLog($loggerName, $key, "Wrong method request", 1);
        echo json_encode(["code" => INVALID_API_METHOD, "message" => "Disable key API can only take PUT method"]);
        exit;
    }
?>
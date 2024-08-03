<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");

    require('../../config.php');
    include('../database.php');
    include('../checker.php');
    include('../logger.php');

    $key = isset($_GET['key']) ? $_GET['key'] : "";
    $loggerName = "API-GenerateKey";

    // Check if the key is valid
    $perms = verifyAPIKey($key);
    if($perms === null) {
        exit; // The verifyAPIKey function has already returned an appropriate response and terminated the execution
    }

    if(!verifyKeyPerms($key, $perms, PERMISSION_GENERATE_KEY, true)) {
        exit; // The verifyKeyPerms function has already returned an appropriate response and terminated the execution
    }
    
    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents("php://input"), true);
        $perms = "";

        if(empty($data['keyOwner'])) {
            makeLog($loggerName, $key, "Argument keyOwner not provided", 2);
            echo json_encode(["code" => API_KEY_GENERATE_ARGUMENT_ERROR . "A", "message" => "Argument keyOwner not provided"]);
            exit;
        }
        if(empty($data['active'])) {
            makeLog($loggerName, $key, "Argument active not provided, so key is desactivate", 1);
            $data['active'] = 0; // 0 = key is not active | 1 = key is active
            $activeString = "inactive";
        } else {
            $activeString = "active";
        }

        for($i = 0; $i < TOTAL_PERMISSIONS; $i++) {
            if($i === PERMISSION_DISABLE_KEY || $i === PERMISSION_ENABLE_KEY) {
                $perms .= "true;";
            } elseif(isset($data['perm' . $i]) === true) {
                $perms .= "true;";
            } else {
                $perms .= "false;";
            }
        }
        //makeLog($loggerName, $key, "Key permissions sets to " . $perms, 4);

        $newKey = generateAPIKey();
        $keyVersion = "v" . substr(VERSION, 0, 1);
        $creation = date("Y-m-d");

        $query = "INSERT INTO api_keys (owner, api_key, api_version, perms, created, is_active) VALUES (?,?,?,?,?,?)";
        $stmt = $conn->prepare($query);

        if($stmt === false) {
            makeLog($loggerName, $key, "SQL Prepare Error: " . $conn->error, 2);
            echo json_encode(["code" => SQL_PREPARE_ERROR, "message" => "SQL Prepare Error: " . $conn->error]);
            exit;
        }

        $stmt->bind_param("ssssss", $data['keyOwner'], $newKey, $keyVersion, $perms, $creation, $data['active']);
        
        if($stmt->execute()) {
            makeLog($loggerName, $key, "A new key was generated: $newKey with the permissions: $perms for the user: " . $data['keyOwner'] . " with the status: $activeString", 4);
            echo json_encode(["code" => QUERY_WORKED_SUCCESSFULLY, "message" => "Key generated successfully.", "key" => $newKey, "keyStatus" => $activeString]);
        } else {
            makeLog($loggerName, $key, "SQL Error: " . $conn->error, 4);
            echo json_encode(["code" => SQL_QUERY_ERROR, "message" => "SQL Error: " . $conn->error]);
            exit;
        }
    } else {
        makeLog($loggerName, $key, "Wrong method request", 1);
        echo json_encode(["code" => INVALID_API_METHOD, "message" => "Create project API can only take POST method"]);
        exit;
    }



    function generateAPIKey() {
        $bytes = random_bytes(16);
    
        $bytes[6] = chr((ord($bytes[6]) & 0x0f) | 0x40);
        $bytes[8] = chr((ord($bytes[8]) & 0x3f) | 0x80);
    
        $uuid = sprintf(
            '%s-%s-%s-%s-%s',
            bin2hex(substr($bytes, 0, 4)),
            bin2hex(substr($bytes, 4, 2)),
            bin2hex(substr($bytes, 6, 2)),
            bin2hex(substr($bytes, 8, 2)),
            bin2hex(substr($bytes, 10))
        );
    
        return KEY_PREFIX . $uuid;
    }
?>
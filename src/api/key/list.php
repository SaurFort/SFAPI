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
    
    if($_SERVER['REQUEST_METHOD'] === 'GET') {
        if(isset($_GET['owner']) && $_GET['owner'] !== verifyKeyOwner($key) && !verifyKeyPerms($key, $perms, PERMISSION_ACCESS_OTHER_USER_KEY, true)) {
            exit;
        } elseif(isset($_GET['owner']) && $_GET['owner'] !== verifyKeyOwner($key) && verifyKeyPerms($key, $perms, PERMISSION_ACCESS_OTHER_USER_KEY, true)) {
            $owner = $_GET['owner'];
        } elseif(isset($_GET['owner']) && $_GET['owner'] == verifyKeyOwner($key)) {
            $owner = $_GET['owner'];
        } elseif(empty($_GET['owner']) && verifyKeyPerms($key, $perms, PERMISSION_ACCESS_OTHER_USER_KEY, true)) {
            $owner;
        } else {
            $owner = verifyKeyOwner($key);
        }

        $from = isset($_GET['from']) ? $_GET['from'] : 0;
        $limit = isset($_GET['limit']) ? $_GET['limit'] : 10;

        if(!is_int($from)) {
            makeLog($loggerName, $key, "The argument from is not a valid integer", 2);
            echo json_encode(["code" => API_KEY_LIST_ARGUMENT_ERROR . "A", "message" => "The argument from is not a valid integer"]);
            exit;
        } elseif($from > 0) {
            $from -= 1;
        }

        if(!is_int($limit)) {
            makeLog($loggerName, $key, "The argument limit is not a valid integer", 2);
            echo json_encode(["code" => API_KEY_LIST_ARGUMENT_ERROR . "B", "message" => "The argument limit is not a valid integer"]);
            exit;
        }

        $query = "SELECT * FROM api_keys";

        if(isset($owner)) {
            $query .= " WHERE owner = ?";
        }

        if($limit !== -1) {
            $query .= " LIMIT ?";
        }

        if($from > 0) {
            $query .= " OFFSET ?";
        }

        $stmt = $conn->prepare($query);

        if($stmt === false) {
            makeLog($loggerName, $key, "SQL Prepare Error: " . $conn->error, 2);
            echo json_encode(["code" => SQL_PREPARE_ERROR, "message" => "SQL Prepare Error: " . $conn->error]);
            exit;
        }

        if(isset($owner) && $limit !== -1 && $from > 0) {
            $stmt->bind_param("sii", $owner, $limit, $from);
        } elseif(isset($owner) && $limit !== -1) {
            $stmt->bind_param("si", $owner, $limit);
        } elseif($limit !== -1 && $from > 0) {
            $stmt->bind_param("ii", $limit, $from);
        } elseif(isset($owner) && $from > 0) {
            $stmt->bind_param("si", $owner, $from);
        } elseif(isset($owner)) {
            $stmt->bind_param("s", $owner);
        } elseif($limit !== -1) {
            $stmt->bind_param("i", $limit);
        } elseif($from > 0) {
            $stmt->bind_param("i", $from);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        if($result === false) {
            makeLog($loggerName, $key, "SQL Error: " . $conn->error, 2);
            echo json_encode(["code" => SQL_QUERY_ERROR, "message" => "SQL Error: " . $conn->error]);
            exit;
        }

        $keys = [];

        while($row = $result->fetch_assoc()) {
            $keys[] = $row;
        }

        if(empty($keys)) {
            makeLog($loggerName, $key, "SQL Empty Row, no keys found for user $owner", 2);
            echo json_encode(["code" => SQL_QUERY_EMPTY_ROW_ERROR, "message" => "No keys found."]);
        } else {
            $keysString = "";
            foreach ($keys as $keyString) {
                $keysString .= " Owner: " . $keyString['owner'] . " Key: " . $keyString['api_key'] . " ;";
            }
            makeLog($loggerName, $key, "Getted keys: $keysString", 2);
            echo json_encode($keys);
        }
    } else {
        makeLog($loggerName, $key, "Wrong method request", 1);
        echo json_encode(["code" => INVALID_API_METHOD, "message" => "List key API can only take GET method"]);
        exit;
    }
?>
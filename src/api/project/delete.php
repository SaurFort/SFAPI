<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");

    require('../../config.php');
    include('../database.php');
    include('../checker.php');
    include('../logger.php');

    $key = isset($_GET['key']) ? $_GET['key'] : "";
    $loggerName = "API-DeleteProject";

    // Check if the key is valid and the action is defined
    $perms = verifyAPIKey($key);
    if($perms === null) {
        exit; // The verifyAPIKey function has already returned an appropriate response and terminated the execution
    }

    if(!verifyKeyPerms($key, $perms, PERMISSION_DELETE_PROJECTS)) {
        exit; // The verifyKeyPerms function has already returned an appropriate response and terminated the execution
    }

    if($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        $data = json_decode(file_get_contents("php://input"), true);

        if (empty($data['id'])) {
            makeLog($loggerName, $key, "Argument id is not provided", 1);
            echo json_encode(["code" => PROJECT_DELETE_ARGUMENT_ERROR, "message" => "Missing project ID for deletion."]);
            exit;
        }

        $query = "SELECT owner FROM projects WHERE id = ?";
        $stmt = $conn->prepare($query);

        if($stmt === false) {
            makeLog($loggerName, $key, "SQL Prepare Error: " . $conn->error, 2);
            echo json_encode(["code" => SQL_PREPARE_ERROR, "message" => "SQL Prepare Error: " . $conn->error]);
            exit;
        }
        
        $stmt->bind_param("i", $data['id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        if($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            $owner = $row['owner'];

            if(verifyKeyOwner($key) !== $owner && verifyKeyPerms($key, $perms, PERMISSION_OTHER_USERS_PROJECTS)) {
                makeLog($loggerName, $key, "Missing permission to update project of other users", 1);
                echo json_encode(["code" => API_KEY_PERMISSION_ERROR, "message" => "You don't have permissions to update project of other users"]);
                exit;
            }
        } else {
            makeLog($loggerName, $key, "SQL Empty Row Error: " . $conn->error, 2);
            echo json_encode(["code" => SQL_QUERY_EMPTY_ROW_ERROR, "message" => "No projects found for this id"]);
            exit;
        }

        $query = "DELETE FROM projects WHERE id = ?";
        $stmt = $conn->prepare($query);

        if($stmt === false) {
            makeLog($loggerName, $key, "SQL Prepare Error: " . $conn->error, 2);
            echo json_encode(["code" => SQL_PREPARE_ERROR, "message" => "SQL Prepare Error: " . $conn->error]);
            exit;
        }

        $stmt->bind_param("i", $data['id']);
        
        if($stmt->execute()) {
            makeLog($loggerName, $key, "Project successfully deleted", 3);
            $stmt->close();

            $query = "DELETE FROM project_translations WHERE project_id = ?";
            $stmt = $conn->prepare($query);

            if($stmt === false) {
                makeLog($loggerName, $key, "SQL Prepare Error: " . $conn->error, 2);
                echo json_encode(["code" => SQL_PREPARE_ERROR, "message" => "SQL Prepare Error: " . $conn->error]);
                exit;
            }

            $stmt->bind_param("i", $data['id']);

            if($stmt->execute()) {
                makeLog($loggerName, $key, "Project translations successfully deleted", 3);
                echo json_encode(["message" => "Successfully delete project and translations."]);
            } else {
                makeLog($loggerName, $key, "SQL Error: " . $conn->error, 2);
                echo json_encode(["code" => SQL_QUERY_ERROR, "message" => "SQL Error: " . $conn->error]);
                exit;
            }
        } else {
            makeLog($loggerName, $key, "SQL Error: " . $conn->error, 2);
            echo json_encode(["code" => SQL_QUERY_ERROR, "message" => "SQL Error: " . $conn->error]);
            exit;
        }
    } else {
        echo json_encode(["code" => INVALID_API_METHOD, "message" => "Update project API can only take DELETE method"]);
        exit;
    }
?>
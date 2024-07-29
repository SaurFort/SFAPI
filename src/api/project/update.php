<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");

    require('../../config.php');
    include('../database.php');
    include('../checker.php');
    include('../logger.php');

    $key = isset($_GET['key']) ? $_GET['key'] : "";
    $loggerName = "API-UpdateProject";

    // Check if the key is valid and the action is defined
    $perms = verifyAPIKey($key);
    if($perms === null) {
        exit; // The verifyAPIKey function has already returned an appropriate response and terminated the execution
    }

    if(!verifyKeyPerms($key, $perms, PERMISSION_UPDATE_PROJECTS)) {
        exit; // The verifyKeyPerms function has already returned an appropriate response and terminated the execution
    }

    if($_SERVER['REQUEST_METHOD'] === 'PUT') {
        $data = json_decode(file_get_contents("php://input"), true);

        if(empty($data['id'])) {
            makeLog($loggerName, $key, "Argument id not provided", 1);
            echo json_encode(["code" => PROJECT_UPDATE_ARGUMENT_ERROR, "message" => "Missing parameters for project update."]);
            exit;
        }

        $query = "SELECT owner FROM projects WHERE id = ?";
        $stmt = $conn->prepare($query);

        if($stmt === false) {
            makeLog($loggerName, $key, "SQL Prepare Error: " . $conn->error, 2);
            echo json_encode(["code" => SQL_PREPARE_ERROR, "message" => "SQL Prepare Error: " . $conn->error]);
            exit;
        }

        $stmt->bind_param('i', $data['id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        
        if($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            $owner = $row['owner'];

            if(verifyKeyOwner($key) !== $owner && !verifyKeyPerms($key, $perms, PERMISSION_OTHER_USERS_PROJECTS)) {
                makeLog($loggerName, $key, "Missing permission to update project of other users", 1);
                echo json_encode(["code" => API_KEY_PERMISSION_ERROR, "message" => "You don't have permissions to update project of other users"]);
                exit;
            }
        } else {
            makeLog($loggerName, $key, "SQL Empty Row Error: " . $conn->error, 2);
            echo json_encode(["code" => SQL_QUERY_EMPTY_ROW_ERROR, "message" => "No projects found for this id"]);
            exit;
        }


        $fields = [];
        $types = '';
        $values = [];

        if (isset($data['name'])) {
            $fields[] = "name = ?";
            $types .= 's';
            $values[] = $data['name'];
        }
        if (isset($data['technologies'])) {
            $fields[] = "technologies = ?";
            $types .= 's';
            $values[] = $data['technologies'];
        }
        if (isset($data['creation'])) {
            $fields[] = "creation = ?";
            $types .= 's';
            $values[] = $data['creation'];
        }

        $values[] = $data['id'];
        $types .= 'i';

        if (count($fields) > 0) {
            $query = "UPDATE projects SET " . implode(", ", $fields) . " WHERE id = ?";
            $stmt = $conn->prepare($query);

            if ($stmt === false) {
                echo json_encode(["code" => SQL_PREPARE_ERROR, "message" => "SQL Error: " . $conn->error]);
                exit;
            }

            $stmt->bind_param($types, ...$values);

            if ($stmt->execute()) {
                $stmt->close();

                if (isset($data['description-en'])) {
                    $query = "UPDATE project_translations SET description = ? WHERE project_id = ? AND language = 'en'";
                    $stmt = $conn->prepare($query);

                    if($stmt === false) {
                        makeLog($loggerName, $key, "SQL Prepare Error: " . $conn->error, 2);
                        echo json_encode(["code" => SQL_PREPARE_ERROR, "message" => "SQL Prepare Error: " . $conn->error]);
                        exit;
                    }

                    $stmt->bind_param('si', $data['description-en'], $data['id']);

                    if (!$stmt->execute()) {
                        makeLog($loggerName, $key, "SQL Error: " . $conn->error, 2);
                        echo json_encode(["code" => SQL_QUERY_ERROR, "message" => "SQL Error: " . $conn->error]);
                        $stmt->close();
                        exit;
                    }

                    $stmt->close();
                    makeLog($loggerName, $key, "English project translation with project id " . $data['id'] . " was successfully updated", 1);
                }

                if (isset($data['description-fr'])) {
                    $query = "UPDATE project_translations SET description = ? WHERE project_id = ? AND language = 'fr'";
                    $stmt = $conn->prepare($query);

                    if($stmt === false) {
                        makeLog($loggerName, $key, "SQL Prepare Error: " . $conn->error, 2);
                        echo json_encode(["code" => SQL_PREPARE_ERROR, "message" => "SQL Prepare Error: " . $conn->error]);
                        exit;
                    }

                    $stmt->bind_param('si', $data['description-fr'], $data['id']);

                    if (!$stmt->execute()) {
                        makeLog($loggerName, $key, "SQL Error: " . $conn->error, 2);
                        echo json_encode(["code" => SQL_QUERY_ERROR, "message" => "SQL Error: " . $conn->error]);
                        $stmt->close();
                        exit;
                    }

                    $stmt->close();
                    makeLog($loggerName, $key, "French project translation with project id " . $data['id'] . " was successfully updated", 1);
                }

                makeLog($loggerName, $key, "Project with the id " . $data['id'] . " was successfully updated", 3);
                echo json_encode(["message" => "The project and its translations were successfully updated."]);
            } else {
                makeLog($loggerName, $key, "SQL Error: " . $conn->error, 2);
                echo json_encode(["code" => SQL_QUERY_ERROR, "message" => "SQL Error: " . $conn->error]);
                exit;
            }
        } else {
            makeLog($loggerName, $key, "Project with the id " . $data['id'] . " has no fields to update", 1);
            echo json_encode(["code" => PROJECT_UPDATE_ARGUMENT_ERROR, "message" => "No fields to update."]);
            exit;
        }
    } else {
        echo json_encode(["code" => INVALID_API_METHOD, "message" => "Update project API can only take PUT method"]);
        exit;
    }
?>
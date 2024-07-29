<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");

    require('../../config.php');
    include('../database.php');
    include('../checker.php');
    include('../logger.php');

    $key = isset($_GET['key']) ? $_GET['key'] : "";
    $action = isset($_GET['action']) ? $_GET['action'] : "";

    // Check if the key is valid and the action is defined
    $perms = verifyAPIKey($key);
    if($perms === null) {
        exit; // The verifyAPIKey function has already returned an appropriate response and terminated the execution
    }

    if(!verifyKeyPerms($key, $perms, PERMISSION_CREATE_PROJECTS)) {
        exit; // The verifyKeyPerms function has already returned an appropriate response and terminated the execution
    }

    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents("php://input"), true);

        if(empty($data['name'])) {
            makeLog("API-CreateProject", $key, "Project's name is not provided", 1);
            echo json_encode(["code" => PROJECT_CREATE_ARGUMENT_ERROR . "A", "message" => "Project's name is not provided"]);
            exit;
        }
        if(empty($data['technologies'])) {
            makeLog("API-CreateProject", $key, "Project's technologies is not provided", 1);
            echo json_encode(["code" => PROJECT_CREATE_ARGUMENT_ERROR . "B", "message" => "Project's technologies is not provided"]);
            exit;
        }
        if(empty($data['description-en'])) {
            makeLog("API-CreateProject", $key, "Project's english description is not provided", 1);
            echo json_encode(["code" => PROJECT_CREATE_ARGUMENT_ERROR . "C", "message" => "Project's english description is not provided"]);
            exit;
        }
        if(empty($data['owner'])) {
            makeLog("API-CreateProject", $key, "Project's owner is not provided", 1);
            echo json_encode(["code" => PROJECT_CREATE_ARGUMENT_ERROR . "D", "message" => "Project's owner is not provided"]);
            exit;
        } elseif(!verifyKeyPerms($key, $perms, PERMISSION_OTHER_USERS_PROJECTS) && !verifyKeyOwner($key)) {
            makeLog("API-CreateProject", $key, "Missing permission to create project for other users", 1);
            echo json_encode(["code" => PROJECT_CREATE_ARGUMENT_ERROR . "E", "message" => "You don't have permissions to create project for other users"]);
            exit;
        }

        $query = "SELECT name, owner FROM projects WHERE name = ? AND owner = ?";
        $stmt = $conn->prepare($query);

        if($stmt === false) {
            makeLog("API-CreateProject", $key, "SQL Prepare Error: " . $conn->error, 2);
            echo json_encode(["code" => SQL_PREPARE_ERROR, "message" => "SQL Prepare Error: " . $conn->error]);
            exit;
        }

        $stmt->bind_param("ss", $data['name'], $data['owner']);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows > 0) {
            makeLog("API-CreateProject", $key, "Project " . $data['name'] . " by " . $data['owner'] . " already exist", 1);
            echo json_encode(["code" => PROJECT_CREATE_ARGUMENT_ERROR . "F", "message" => "Project name already exist for this owner"]);
            exit;
        }
        $stmt->close();

        $query = "INSERT INTO projects (name, technologies, owner, creation) VALUES (?,?,?,?)";
        $stmt = $conn->prepare($query);

        if($stmt === false) {
            makeLog("API-CreateProject", $key, "SQL Prepare Error: " . $conn->error, 2);
            echo json_encode(["code" => SQL_PREPARE_ERROR, "message" => "SQL Prepare Error: " . $conn->error]);
            exit;
        }

        if(isset($data['creation'])) {
            $stmt->bind_param("ssss", $data['name'], $data['technologies'], $data['owner'], $data['creation']);
        } else {
            $date = date("Y-m-d");
            $stmt->bind_param("ssss", $data['name'], $data['technologies'], $data['owner'], $date);
        }

        if($stmt->execute()) {
            $stmt->close();

            $query = "SELECT id FROM projects WHERE name = ? AND owner = ?";
            $stmt = $conn->prepare($query);

            if($stmt === false) {
                makeLog("API-CreateProject", $key, "SQL Prepare Error: " . $conn->error, 2);
                echo json_encode(["code" => SQL_PREPARE_ERROR, "message" => "SQL Prepare Error: " . $conn->error]);
                exit;
            }

            $stmt->bind_param("ss", $data['name'], $data['owner']);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();

            if($result->num_rows) {
                $row = $result->fetch_assoc();
                $projectID = $row['id'];

                $query = "INSERT INTO project_translations (project_id, language, description) VALUES (?,?,?)";
                $stmt = $conn->prepare($query);

                if($stmt === false) {
                    makeLog("API-CreateProject", $key, "SQL Prepare Error: " . $conn->error, 2);
                    echo json_encode(["code" => SQL_PREPARE_ERROR, "message" => "SQL Prepare Error: " . $conn->error]);
                    exit;
                }

                $language = "en";
                $stmt->bind_param("sss", $projectID, $language, $data['description-en']);
                $stmt->execute();
                $stmt->close();

                if (isset($data['description-fr'])) {
                    $query = "INSERT INTO project_translations (project_id, language, description) VALUES (?, ?, ?)";
                    $stmt = $conn->prepare($query);

                    if ($stmt === false) {
                        echo json_encode(["code" => SQL_PREPARE_ERROR, "message" => "SQL Error: " . $conn->error]);
                        exit;
                    }

                    $language = "fr";
                    $stmt->bind_param("iss", $projectID, $language, $data['description-fr']);
                    $stmt->execute();
                    $stmt->close();
                } else {
                    $data['description-fr'] = "none";
                }

                makeLog("API-CreateProject", $key, "Project " . $data['name'] . " by " . $data['owner'] . " was added with his description, en: " . $data['description-en'] . " and fr: " . $data['description-fr'], 3);
                echo json_encode(["code" => QUERY_WORKED_SUCCESSFULLY, "message" => "The project was successfully added to the db."]);
                exit;
            } else {
                makeLog("API-CreateProject", $key, "SQL Empty Row Error: " . $conn->error, 2);
                echo json_encode(["code" => SQL_QUERY_EMPTY_ROW_ERROR, "message" => "No projects found."]);
                exit;
            }
        } else {
            makeLog("API-CreateProject", $key, "SQL Error: " . $conn->error, 2);
            echo json_encode(["code" => SQL_QUERY_ERROR, "message" => "SQL Error: " . $conn->error]);
            exit;
        }
    } else {
        echo json_encode(["code" => INVALID_API_METHOD, "message" => "Create project API can only take POST method"]);
        exit;
    }
?>
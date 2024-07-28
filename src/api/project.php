<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");

    require('../config.php');
    include('database.php');
    include('checker.php');

    $key = isset($_GET['key']) ? $_GET['key'] : "";
    $action = isset($_GET['action']) ? $_GET['action'] : "";

    // Check if the key is valid and the action is defined
    $perms = verifyAPIKey($key);
    if ($perms === null) {
        exit; // The verifyAPIKey function has already returned an appropriate response and terminated the execution
    }

    if (!in_array($action, ['create', 'update', 'delete', 'read'])) {
        echo json_encode(["code" => PROJECT_ACTION_ERROR, "message" => "The action requested is not a valid action."]);
        exit;
    }

    // Check permissions
    switch ($action) {
        case 'create':
            $requiredPerm = PERMISSION_CREATE_PROJECTS;
            break;
        case 'update':
            $requiredPerm = PERMISSION_UPDATE_PROJECTS;
            break;
        case 'delete':
            $requiredPerm = PERMISSION_DELETE_PROJECTS;
            break;
        case 'read':
            $requiredPerm = PERMISSION_READ_PROJECTS;
            break;
        default:
            echo json_encode(["code" => PROJECT_ACTION_ERROR, "message" => "The action requested is not a valid action."]);
            exit;
    }

    if (!verifyKeyPerms($perms, $requiredPerm)) {
        exit; // The verifyKeyPerms function has already returned an appropriate response and terminated the execution
    }

    // If the action is "create", execute the corresponding query
    if ($action === "create" && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['name']) || !isset($data['technologies']) || !isset($data['description-en'])) {
            echo json_encode(["code" => PROJECT_CREATE_ARGUMENT_ERROR, "message" => "Missing parameters for project creation."]);
            exit;
        }

        $query = "INSERT INTO projects (name, technologies, creation) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);

        if ($stmt === false) {
            echo json_encode(["code" => SQL_PREPARE_ERROR, "message" => "SQL Error: " . $conn->error]);
            exit;
        }

        if (isset($data['creation'])) {
            $stmt->bind_param("sss", $data['name'], $data['technologies'], $data['creation']);
        } else {
            $date = date("Y-m-d");
            $stmt->bind_param("sss", $data['name'], $data['technologies'], $date);
        }

        if ($stmt->execute()) {
            $stmt->close();

            $query = "SELECT id FROM projects WHERE name = ?";
            $stmt = $conn->prepare($query);

            if ($stmt === false) {
                echo json_encode(["code" => SQL_PREPARE_ERROR, "message" => "SQL Error: " . $conn->error]);
                exit;
            }

            $stmt->bind_param("s", $data['name']);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $projectID = $row['id'];

                // Insert English description
                $query = "INSERT INTO project_translations (project_id, language, description) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($query);

                if ($stmt === false) {
                    echo json_encode(["code" => SQL_PREPARE_ERROR, "message" => "SQL Error: " . $conn->error]);
                    exit;
                }
                
                $language = "en";
                $stmt->bind_param("iss", $projectID, $language, $data['description-en']);
                $stmt->execute();
                $stmt->close();

                // Insert French description if it exists
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
                }

                echo json_encode(["message" => "The project was successfully added to the db."]);
                exit;
            } else {
                echo json_encode(["code" => SQL_QUERY_EMPTY_ROW_ERROR, "message" => "No projects found."]);
                exit;
            }
        } else {
            echo json_encode(["code" => SQL_QUERY_ERROR, "message" => "SQL Error: " . $conn->error]);
            exit;
        }
    }

    // If the action is "update", execute the corresponding query
    if ($action === "update" && $_SERVER['REQUEST_METHOD'] === 'PUT') {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['id'])) {
            echo json_encode(["code" => PROJECT_UPDATE_ARGUMENT_ERROR, "message" => "Missing parameters for project update."]);
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

        // Add the ID to the values array
        $values[] = $data['id'];
        $types .= 'i';

        if (count($fields) > 0) {
            $query = "UPDATE projects SET " . implode(", ", $fields) . " WHERE id = ?";
            $stmt = $conn->prepare($query);

            if ($stmt === false) {
                echo json_encode(["code" => SQL_PREPARE_ERROR, "message" => "SQL Error: " . $conn->error]);
                exit;
            }

            // Using the spread operator to pass parameters
            $stmt->bind_param($types, ...$values);

            if ($stmt->execute()) {
                $stmt->close();

                // Update the translations if provided
                if (isset($data['description-en'])) {
                    $query = "UPDATE project_translations SET description = ? WHERE project_id = ? AND language = 'en'";
                    $stmt = $conn->prepare($query);

                    if ($stmt === false) {
                        echo json_encode(["code" => SQL_PREPARE_ERROR, "message" => "SQL Error: " . $conn->error]);
                        exit;
                    }

                    $stmt->bind_param('si', $data['description-en'], $data['id']);

                    if (!$stmt->execute()) {
                        echo json_encode(["code" => SQL_QUERY_ERROR, "message" => "SQL Error: " . $conn->error]);
                        $stmt->close();
                        exit;
                    }

                    $stmt->close();
                }

                if (isset($data['description-fr'])) {
                    $query = "UPDATE project_translations SET description = ? WHERE project_id = ? AND language = 'fr'";
                    $stmt = $conn->prepare($query);

                    if ($stmt === false) {
                        echo json_encode(["code" => SQL_PREPARE_ERROR, "message" => "SQL Error: " . $conn->error]);
                        exit;
                    }

                    $stmt->bind_param('si', $data['description-fr'], $data['id']);

                    if (!$stmt->execute()) {
                        echo json_encode(["code" => SQL_QUERY_ERROR, "message" => "SQL Error: " . $conn->error]);
                        $stmt->close();
                        exit;
                    }

                    $stmt->close();
                }

                echo json_encode(["message" => "The project and its translations were successfully updated."]);
            } else {
                echo json_encode(["code" => SQL_QUERY_ERROR, "message" => "SQL Error: " . $conn->error]);
                exit;
            }

        } else {
            echo json_encode(["code" => PROJECT_UPDATE_ARGUMENT_ERROR, "message" => "No fields to update."]);
        }
    }


    // If the action is "delete", execute the corresponding query
    if ($action === "delete" && $_SERVER['REQUEST_METHOD'] === 'DELETE') {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['id'])) {
            echo json_encode(["code" => PROJECT_DELETE_ARGUMENT_ERROR, "message" => "Missing project ID for deletion."]);
            exit;
        }

        $query = "DELETE FROM projects WHERE id = ?";
        $stmt = $conn->prepare($query);

        if ($stmt === false) {
            echo json_encode(["code" => SQL_PREPARE_ERROR, "message" => "SQL Error: " . $conn->error]);
            exit;
        }

        $stmt->bind_param("i", $data['id']);

        if($stmt->execute()) {
            $stmt->close();

            $query = "DELETE FROM project_translations WHERE project_id = ?";
            $stmt = $conn->prepare($query);

            if ($stmt === false) {
                echo json_encode(["code" => SQL_PREPARE_ERROR, "message" => "SQL Error: " . $conn->error]);
                exit;
            }

            $stmt->bind_param("i", $data['id']);

            if($stmt->execute()) {
                echo json_encode(["message" => "Successfully delete project and translations."]);
            } else {
                echo json_encode(["code" => SQL_QUERY_ERROR, "message" => "SQL Error: " . $conn->error]);
                exit;
            }
        } else {
            echo json_encode(["code" => SQL_QUERY_ERROR, "message" => "SQL Error: " . $conn->error]);
            exit;
        }
    }

    // If the action is "read", execute the corresponding query
    if ($action === "read") {
        $lang = isset($_GET['lang']) ? $_GET['lang'] : 'en';
        $sort = isset($_GET['sort']) ? $_GET['sort'] : 'latest';
        $filterType = isset($_GET['filtertype']) ? $_GET['filtertype'] : '';
        $filter = isset($_GET['filter']) ? $_GET['filter'] : '';
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : -1;

        if (!in_array($lang, ['en', 'fr'])) {
            echo json_encode(["code" => PROJECT_READ_ARGUMENT_ERROR . "A", "message" => "Invalid language parameter."]);
            exit;
        }

        if (!in_array($sort, ['latest', 'oldest'])) {
            echo json_encode(["code" => PROJECT_READ_ARGUMENT_ERROR . "B", "message" => "Invalid sort parameter."]);
            exit;
        }

        if (!in_array($filterType, ['id', 'name']) && !empty($filterType)) {
            echo json_encode(["code" => PROJECT_READ_ARGUMENT_ERROR . "C", "message" => "Invalid filter type parameter."]);
            exit;
        } else {
            if (empty($filter) && !empty($filterType)) {
                echo json_encode(["code" => PROJECT_READ_ARGUMENT_ERROR . "D", "message" => "Filter is empty but filtertype is defined."]);
                exit;
            }

            if ($filterType === "id" && !empty($filterType)) {
                if (!filter_var($filter, FILTER_VALIDATE_INT)) {
                    echo json_encode(["code" => PROJECT_READ_ARGUMENT_ERROR . "E", "message" => "Filtertype is defined on id but filter is not a valid integer id."]);
                    exit;
                }
            }
        }

        $query = "
            SELECT p.id, p.name, pt.description, p.technologies, p.creation
            FROM projects p
            JOIN project_translations pt ON p.id = pt.project_id
            WHERE pt.language = ?
        ";

        if ($filterType === "id") {
            $query .= " AND p.id = ?";
        } elseif ($filterType === "name") {
            $query .= " AND p.name LIKE ?";
        }

        if ($sort === 'latest') {
            $query .= " ORDER BY p.creation DESC";
        } elseif ($sort === 'oldest') {
            $query .= " ORDER BY p.creation ASC";
        }

        if ($limit > 0) {
            $query .= " LIMIT ?";
        }

        $stmt = $conn->prepare($query);

        if ($stmt === false) {
            echo json_encode(["code" => SQL_PREPARE_ERROR, "message" => "SQL Error: " . $conn->error]);
            exit;
        }

        if ($filterType === "id") {
            $filter_param = $filter;
            if ($limit > 0) {
                $stmt->bind_param('sii', $lang, $filter_param, $limit);
            } else {
                $stmt->bind_param('ss', $lang, $filter_param);
            }
        } elseif ($filterType === "name") {
            $filter_param = "%$filter%";
            if ($limit > 0) {
                $stmt->bind_param('ssi', $lang, $filter_param, $limit);
            } else {
                $stmt->bind_param('ss', $lang, $filter_param);
            }
        } elseif ($limit > 0) {
            $stmt->bind_param('si', $lang, $limit);
        } else {
            $stmt->bind_param('s', $lang);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        if ($result === false) {
            echo json_encode(["code" => SQL_QUERY_ERROR, "message" => "SQL Error: " . $conn->error]);
            exit;
        }

        $projects = [];

        while ($row = $result->fetch_assoc()) {
            $row['creation'] = date('d/m/Y', strtotime($row['creation']));
            $projects[] = $row;
        }

        if (empty($projects)) {
            echo json_encode(["code" => SQL_QUERY_EMPTY_ROW_ERROR, "message" => "No projects found."]);
        } else {
            echo json_encode($projects);
        }
    }
?>

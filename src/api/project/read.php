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

    if(!verifyKeyPerms($key, $perms, PERMISSION_READ_PROJECTS)) {
        exit; // The verifyKeyPerms function has already returned an appropriate response and terminated the execution
    }

    if($_SERVER['REQUEST_METHOD'] === 'GET') {
        $lang = isset($_GET['lang']) ? $_GET['lang'] : 'en';
        $sort = isset($_GET['sort']) ? $_GET['sort'] : 'latest';
        $filterType = isset($_GET['filtertype']) ? $_GET['filtertype'] : '';
        $filter = isset($_GET['filter']) ? $_GET['filter'] : '';
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : -1;

        if(isset($_GET['owner']) && verifyKeyPerms($key, $perms, PERMISSION_READ_OTHER_PROJECTS)) {
            $owner = $_GET['owner'];
        } else {
            $owner = verifyKeyOwner($key);
        }

        if(!in_array($lang, ['en', 'fr'])) {
            echo json_encode(["code" => PROJECT_READ_ARGUMENT_ERROR . "A", "message" => "Invalid language parameter."]);
            exit;
        }

        if(!in_array($sort, ['latest', 'oldest'])) {
            echo json_encode(["code" => PROJECT_READ_ARGUMENT_ERROR . "B", "message" => "Invalid sort parameter."]);
            exit;
        }

        if(!in_array($filterType, ['id', 'name']) && !empty($filterType)) {
            echo json_encode(["code" => PROJECT_READ_ARGUMENT_ERROR . "C", "message" => "Invalid filter type parameter."]);
            exit;
        } else {
            if(empty($filter) && !empty($filterType)) {
                echo json_encode(["code" => PROJECT_READ_ARGUMENT_ERROR . "D", "message" => "Filter is empty but filtertype is defined."]);
                exit;
            }

            if($filterType === "id" && !empty($filterType)) {
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
            WHERE pt.language = ? AND p.owner = ?
        ";
        
        if($filterType === "id") {
            $query .= " AND p.id = ?";
        } elseif ($filterType === "name") {
            $query .= " AND p.name LIKE ?";
        }

        if($sort === 'latest') {
            $query .= " ORDER BY p.creation DESC";
        } elseif ($sort === 'oldest') {
            $query .= " ORDER BY p.creation ASC";
        }

        if($limit > 0) {
            $query .= " LIMIT ?";
        }

        $stmt = $conn->prepare($query);

        if($stmt === false) {
            echo json_encode(["code" => SQL_PREPARE_ERROR, "message" => "SQL Error: " . $conn->error]);
            exit;
        }

        if($filterType === "id") {
            $filter_param = $filter;
            if($limit > 0) {
                $stmt->bind_param('ssii', $lang, $owner, $filter_param, $limit);
            } else {
                $stmt->bind_param('sss', $lang, $owner, $filter_param);
            }
        } elseif($filterType === "name") {
            $filter_param = "%$filter%";
            if($limit > 0) {
                $stmt->bind_param('sssi', $lang, $owner, $filter_param, $limit);
            } else {
                $stmt->bind_param('sss', $lang, $owner, $filter_param);
            }
        } elseif($limit > 0) {
            $stmt->bind_param('ssi', $lang, $owner, $limit);
        } else {
            $stmt->bind_param('ss', $lang, $owner);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        if($result === false) {
            echo json_encode(["code" => SQL_QUERY_ERROR, "message" => "SQL Error: " . $conn->error]);
            exit;
        }

        $projects = [];

        while($row = $result->fetch_assoc()) {
            $row['creation'] = date('d/m/Y', strtotime($row['creation']));
            $projects[] = $row;
        }

        if(empty($projects)) {
            echo json_encode(["code" => SQL_QUERY_EMPTY_ROW_ERROR, "message" => "No projects found."]);
        } else {
            echo json_encode($projects);
        }
    } else {
        echo json_encode(["code" => INVALID_API_METHOD, "message" => "Read project API can only take GET method"]);
        exit;
    }
?>
<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");

    require('../../config.php');
    include('../database.php');
    include('../checker.php');
    include('../logger.php');

    $key = isset($_GET['key']) ? $_GET['key'] : "";
    $loggerName = "API-LoginAccount";

    // Check if the key is valid and the action is defined
    $perms = verifyAPIKey($key);
    if ($perms === null) {
        exit; // The verifyAPIKey function has already returned an appropriate response and terminated the execution
    }

    // Check if the API key has permission to login user
    if (!verifyKeyPerms($key, $perms, PERMISSION_LOGIN_USER)) {
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents("php://input"), true);

        if (empty($data['username']) && empty($data['email'])) {
            makeLog($loggerName, $key, "No username or email provided", 1);
            echo json_encode(["code" => ACCOUNT_LOGIN_ARGUMENT_ERROR . "A", "message" => "You need at least one argument to login the user."]);
            exit;
        }
        if (empty($data['password'])) {
            makeLog($loggerName, $key, "No password provided for username: " . $data['username'] . " and/or email: " . $data['email'], 1);
            echo json_encode(["code" => ACCOUNT_LOGIN_ARGUMENT_ERROR . "B", "message" => "The password cannot be empty."]);
            exit;
        }

        $username = isset($data['username']) ? $data['username'] : null;
        $email = isset($data['email']) ? $data['email'] : null;

        // Debugging output for username and email
        // echo "Username: " . ($username ? $username : 'NULL') . "\n";
        // echo "Email: " . ($email ? $email : 'NULL') . "\n";

        $query = "SELECT * FROM login WHERE";

        // Build the query based on the available parameters
        if ($username && $email) {
            $query .= " username = ? AND email = ?";
        } elseif ($username) {
            $query .= " username = ?";
        } elseif ($email) {
            $query .= " email = ?";
        }

        // Debugging output for the query
        // echo "Query: " . $query . "\n";

        $stmt = $conn->prepare($query);
        if ($stmt === false) {
            makeLog($loggerName, $key, "SQL Prepare Error for the $username | $email, error: " . $conn->error, 2);
            echo json_encode(["code" => SQL_PREPARE_ERROR, "message" => "SQL Error: " . $conn->error]);
            exit;
        }

        // Bind the parameters based on the available data
        if ($username && $email) {
            $stmt->bind_param("ss", $username, $email);
        } elseif ($username) {
            $stmt->bind_param("s", $username);
        } elseif ($email) {
            $stmt->bind_param("s", $email);
        }

        if (!$stmt->execute()) {
            makeLog($loggerName, $key, "SQL Execute Error for $username | $email, error: " . $stmt->error, 2);
            echo json_encode(["code" => SQL_QUERY_ERROR, "message" => "SQL Execute Error: " . $stmt->error]);
            exit;
        }

        $result = $stmt->get_result();
        if ($result === false) {
            makeLog($loggerName, $key, "SQL Get Result Error for $username | $email, error: " . $stmt->error, 2);
            echo json_encode(["code" => SQL_QUERY_ERROR, "message" => "SQL Get Result Error: " . $stmt->error]);
            exit;
        }

        if ($result->num_rows > 0) {
            
            $row = $result->fetch_assoc();

            // Verify the password
            if (password_verify($data['password'], $row['password'])) {
                makeLog($loggerName, $key, "Correct credentials for $username | $email", 3);
                echo json_encode(["code" => QUERY_WORKED_SUCCESSFULLY, "message" => "Correct credentials."]);
            } else {
                makeLog($loggerName, $key, "Unvalid password for $username | $email", 2);
                // Debugging output
                // echo "B";
                echo json_encode(["code" => ACCOUNT_LOGIN_FAILED, "message" => "Invalid credentials."]);
                exit;
            }
        } else {
            makeLog($loggerName, $key, "Username: $username, or Email: $email are unvalid", 2);

            // Debugging output
            // echo "A";
            echo json_encode(["code" => ACCOUNT_LOGIN_FAILED, "message" => "Invalid credentials."]);
            exit;
        }
    } else {
        makeLog($loggerName, $key, "Wrong method request", 1);
        echo json_encode(["code" => INVALID_API_METHOD, "message" => "Login can only take POST method"]);
        exit;
    }
?>

<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");

    require('../../config.php');
    include('../database.php');
    include('../checker.php');
    include('../logger.php');

    $key = isset($_GET['key']) ? $_GET['key'] : "";

    // Check if the key is valid and the action is defined
    $perms = verifyAPIKey($key);
    if ($perms === null) {
        exit; // The verifyAPIKey function has already returned an appropriate response and terminated the execution
    }

    if (!verifyKeyPerms($key, $perms, PERMISSION_REGISTER_USER)) {
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents("php://input"), true);

        // Validate required fields
        if (empty($data['username'])) {
            makeLog("API-Register", $key, "No username provided", 2);
            echo json_encode(["code" => ACCOUNT_REGISTER_ARGUMENT_ERROR . "A", "message" => "Missing or undefined value for username."]);
            exit;
        }
        if (empty($data['email'])) {
            makeLog("API-Register", $key, "No email address provided", 2);
            echo json_encode(["code" => ACCOUNT_REGISTER_ARGUMENT_ERROR . "B", "message" => "Missing or undefined value for email."]);
            exit;
        }
        if (empty($data['password'])) {
            makeLog("API-Register", $key, "No password provided", 2);
            echo json_encode(["code" => ACCOUNT_REGISTER_ARGUMENT_ERROR . "C", "message" => "Missing or undefined value for password."]);
            exit;
        }
        if (empty($data['confirmationPassword'])) {
            makeLog("API-Register", $key, "No password confirmation provided", 2);
            echo json_encode(["code" => ACCOUNT_REGISTER_ARGUMENT_ERROR . "D", "message" => "Missing or undefined value for confirmationPassword."]);
            exit;
        }
        
        // Hash algorithm prefixes not supported by the API
        $hashPrefixes = ["$2y$", "$2b$", "$2a$", "\$argon2i$", "\$argon2d$", "\$s0$", "$1$", "$5$", "$6$"];
        
        // Function to check if the password begins with one of the prefixes
        function isAlreadyHashed($password, $hashPrefixes) {
            foreach ($hashPrefixes as $prefix) {
                if (str_starts_with($password, $prefix)) {
                    return true;
                }
            }
            return false;
        }
        
        // Checks whether the password has already been hashed
        if (isAlreadyHashed($data['password'], $hashPrefixes)) {
            makeLog("API-Register", $key, "Password alreay hashed but hash is not supported", 2);
            echo json_encode(["code" => ACCOUNT_REGISTER_PASSWORD_ERROR . "A", "message" => "Password is already hashed and the algorithm used is not supported by the API."]);
            exit;
        }

        $username = $data['username'];
        $email = $data['email'];
        $password = $data['password'];
        $passwordConfirmation = $data['confirmationPassword'];
        $rank = isset($data['rank']) ? $data['rank'] : 'user';

        // Verify if password is hashed with correct algorithm and correct parameter, else hash it
        if (str_starts_with($password, "\$argon2id\$v=19\$m=65536,t=4,p=1")) {
            $hashedPassword = $password;
        } elseif (str_starts_with($password, "\$argon2id")) {
            makeLog("API-Register", $key, "Password hashed but not with the valid parameters.", 2);
            echo json_encode(["code" => ACCOUNT_REGISTER_PASSWORD_ERROR . "B", "message" => "Password hash not used the correct parameter."]);
            exit;
        } else {
            if ($password !== $passwordConfirmation) {
                makeLog("API-Register", $key, "Password and password confirmation are not the same", 2);
                echo json_encode(["code" => ACCOUNT_REGISTER_PASSWORD_ERROR . "C", "message" => "Password and password confirmation are not the same."]);
                exit;
            }

            $hashedPassword = password_hash($password, PASSWORD_ARGON2ID);
        }

        // Verify if password is correctly hashed
        if (!password_verify($passwordConfirmation, $hashedPassword)) {
            makeLog("API-Register", $key, "The password was not hashed correctly", 3);
            echo json_encode(["code" => ACCOUNT_REGISTER_PASSWORD_ERROR . "D", "message" => "The password was not hashed correctly."]);
            exit;
        }

        // Check if username or email already exists
        $checkQuery = "SELECT id FROM login WHERE username = ? OR email = ?";
        $stmt = $conn->prepare($checkQuery);

        if ($stmt === false) {
            makeLog("API-Register", $key, "SQL Error: " . $conn->error, 2);
            echo json_encode(["code" => SQL_PREPARE_ERROR, "message" => "SQL Error: " . $conn->error]);
            exit;
        }

        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            makeLog("API-Register", $key, "Username: $username or Email: $email are already registered", 3);
            echo json_encode(["code" => ACCOUNT_REGISTER_DUPLICATE_ERROR, "message" => "Username or email already exists."]);
            $stmt->close();
            exit;
        }
        $stmt->close();

        // Insert the new user
        $query = "INSERT INTO login (username, email, password, `rank`) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        if ($stmt === false) {
            makeLog("API-Register", $key, "SQL Prepare Error: " . $conn->error, 2);
            echo json_encode(["code" => SQL_PREPARE_ERROR, "message" => "SQL Error: " . $conn->error]);
            exit;
        }
        $stmt->bind_param("ssss", $username, $email, $hashedPassword, $rank);
        if ($stmt->execute()) {
            makeLog("API-Register", $key, "Account of $username was registered successfully.", 3);
            echo json_encode(["code" => QUERY_WORKED_SUCCESSFULLY, "message" => "User registered successfully."]);
        } else {
            makeLog("API-Register", $key, "SQL Error: " . $conn->error, 2);
            echo json_encode(["code" => SQL_QUERY_ERROR, "message" => "SQL Error: " . $stmt->error]);
        }
        $stmt->close();
        exit;
    } else {
        echo json_encode(["code" => INVALID_API_METHOD, "message" => "Register can only take POST method"]);
        exit;
    }
?>

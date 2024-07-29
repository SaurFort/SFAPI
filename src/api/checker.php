<?php
    function verifyAPIKey(string $key) {
        include("../logger.php");

        if (empty($key)) {
            echo json_encode(["code" => EMPTY_API_KEY, "message" => "You haven't set your API key."]);
            exit; // Add an exit here to ensure execution stops.
        } elseif (str_starts_with($key, KEY_PREFIX)) { // Check version
            include('../database.php');

            $query = "SELECT api.api_key, api.perms FROM api_keys api WHERE api.api_key = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $key);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();

                // Debug: Display retrieved permissions
                // var_dump($row['perms']);

                makeLog("API-KeyVerification", $key, "Valid key", 1);
                return $row['perms'];
            } else {
                makeLog("API-KeyVerification", $key, "The key is not valid.", 2);
                echo json_encode(["code" => API_KEY_WRONG, "message" => "Your API key is not valid"]);
                exit;
            }
        } else {
            makeLog("API-KeyVerification", $key, "The key is not made for the version " . VERSION, 2);
            echo json_encode(["code" => API_KEY_VERSION_ERROR, "message" => "Your API key is not made for the version " . VERSION]);
            exit;
        }
    }

    function verifyKeyPerms(string $key, string $keyPerms, int $requiredPermsCode) {
        // Debug: Display permissions before processing them
        // var_dump($keyPerms);

        if ($keyPerms === "*") {
            makeLog("API-KeyVerification", $key, "A super key was used", 4);
            return true;
        } else {
            // Remove any trailing spaces and semicolons
            $keyPerms = trim($keyPerms, " ;");
            
            // Perms structure is in the file config.php
            $perms = explode(";", $keyPerms);

            // Debug: Display permissions after splitting into array
            // var_dump($perms);

            // Ensure the index exists before comparing
            if (isset($perms[$requiredPermsCode]) && ($perms[$requiredPermsCode] === "true" || $perms[$requiredPermsCode] === "1")) {
                makeLog("API-KeyVerification", $key, "Correct permission number " . $requiredPermsCode, 2);
                return true;
            } else {
                makeLog("API-KeyVerification", $key, "Missing permission number " . $requiredPermsCode, 2);
                echo json_encode(["code" => API_KEY_PERMISSION_ERROR, "message" => "You don't have the permission number " . $requiredPermsCode]);
                exit;
            }
        }
    }
?>

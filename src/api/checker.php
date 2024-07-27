<?php
    function verifyAPIKey(string $key) {
        if (empty($key)) {
            echo json_encode(["code" => EMPTY_API_KEY, "message" => "You haven't set your API key."]);
            exit; // Add an exit here to ensure execution stops.
        } elseif (str_starts_with($key, KEY_PREFIX)) { // Version verification
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

                return $row['perms'];
            } else {
                echo json_encode(["code" => API_KEY_WRONG, "message" => "Your API key is not valid."]);
                exit;
            }
        } else {
            echo json_encode(["code" => API_KEY_VERSION_ERROR, "message" => "Your API key is not made for the version " . VERSION]);
            exit;
        }
    }

    function verifyKeyPerms(string $keyPerms, int $requiredPermsCode) {
        // Debug: Display permissions before processing them
        // var_dump($keyPerms);

        if ($keyPerms === "*") {
            return true;
        } else {
            // Remove any trailing spaces and semicolons
            $keyPerms = trim($keyPerms, " ;");
            
            // Perms structure is "CREATE_PROJECTS;UPDATE_PROJECTS;DELETE_PROJECTS;READ_PROJECTS"
            $perms = explode(";", $keyPerms);

            // Debug: Display permissions after splitting into array
            // var_dump($perms);

            // Ensure the index exists before comparing
            if (isset($perms[$requiredPermsCode]) && ($perms[$requiredPermsCode] === "true" || $perms[$requiredPermsCode] === "1")) {
                return true;
            } else {
                echo json_encode(["code" => API_KEY_PERMISSION_ERROR, "message" => "You don't have the permission number " . $requiredPermsCode]);
                exit;
            }
        }
    }
?>

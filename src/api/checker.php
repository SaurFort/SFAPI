<?php
    function verifyAPIKey(string $key) {
        if (empty($key)) {
            echo json_encode(["code" => EMPTY_API_KEY, "message" => "You haven't set your API key."]);
        } elseif(str_starts_with($key, KEY_PREFIX)) { // Version verification
            include('../database.php');

            $query = "SELECT api.api_key, api.api_version, api.perms FROM api_keys api WHERE api.api_key = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $key);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();

                return $row['perms'];
            } else {
                echo json_encode(["code" => API_KEY_WRONG, "message" => "You API key is not valid."]);
                exit;
            }
        } else {
            echo json_encode(["code" => API_KEY_VERSION_ERROR, "message" => "Your API key is not made for the version " . VERSION]);
            exit;
        }
    }

    function verifyKeyPerms(string $keyPerms, int $requiredPermsCode) {
        if($keyPerms === "*") {
            return true;
        } else {
            // Perms structure is "CREATE_PROJECTS;UPDATE_PROJECTS;DELETE_PROJECTS;READ_PROJECTS"
            $perms = explode(";", $keyPerms);

            if($perms[$requiredPermsCode] === true) {
                return true;
            } else {
                echo json_encode(["code" => API_KEY_PERMISSION_ERROR, "message" => "You don't have the permission number " . $requiredPermsCode]);
                exit;
            }
        }
    }
?>
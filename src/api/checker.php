<?php
    function verifyAPIKey(string $key, bool $is_active = true) {
        // Intentional delay to make request slower.
        sleep(API_DELAY);

        if (empty($key)) {
            makeLog("API-KeyVerification", "", "An user tried to access the API without key", 2);
            echo json_encode(["code" => EMPTY_API_KEY, "message" => "You haven't set your API key."]);
            exit; // Add an exit here to ensure execution stops.
        } elseif (str_starts_with($key, KEY_PREFIX)) { // Check version
            include('../database.php');

            $query = "SELECT api.api_key, api.perms, api.owner, api.is_active FROM api_keys api WHERE api.api_key = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $key);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();

                $perms = $row['perms'];
                $owner = $row['owner'];
                $isActive = $row['is_active'];

                if($isActive !== 1 && $is_active) {
                    makeLog("API-KeyVerification", $key, "$owner tried to use a disabled key", 2);
                    echo json_encode(["code" => API_KEY_DISABLED, "message" => "Your api key is disabled"]);
                    exit;
                }

                // Debug: Display retrieved permissions
                // var_dump($row['perms']);

                makeLog("API-KeyVerification", $key, "$owner just used a key, is active: $is_active", 1);
                return $perms;
            } else {
                makeLog("API-KeyVerification", $key, "API key not valid", 1);
                echo json_encode(["code" => API_KEY_WRONG, "message" => "Your API key is not valid"]);
                exit;
            }
        } else {
            makeLog("API-KeyVerification", $key, "The API key is not made for the version " . VERSION, 2);
            echo json_encode(["code" => API_KEY_VERSION_ERROR, "message" => "Your API key is not made for the version " . VERSION]);
            exit;
        }
    }

    function verifyKeyPerms(string $key, string $keyPerms, int $requiredPermsCode, bool $superKey = false) {
        // Debug: Display permissions before processing them
        // var_dump($keyPerms);

        if ($keyPerms === "*" && $superKey) {
            makeLog("API-KeyVerification", $key, "A super key was used in a part of API who need it", 4);
            return true;
        } elseif($keyPerms === "*" && !$superKey) {
            makeLog("API-KeyVerification", $key, "A super key was used", 3);
            return true;
        } elseif(!$superKey) {
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
        } else {
            makeLog("API-KeyVerification", $key, "Super key needed but no valid super key provided", 4);
            echo json_encode(["code" => API_KEY_SUPERKEY_NEEDED, "message" => "A super key is needed but you don't have a super key"]);
            exit;
        }
    }

    function verifyKeyOwner(string $key) {
        include('../database.php');

        $query = "SELECT api.owner FROM api_keys api WHERE api.api_key = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $key);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            $owner = $row['owner'];

            makeLog("API-KeyVerification", $key, "$owner just been verified", 1);
            return $owner;
        } else {
            makeLog("API-KeyVerification", $key, "SQL Error: " . $conn->error, 1);
            return false;
        }
    }
?>

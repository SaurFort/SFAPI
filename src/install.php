<?php
    include("../api/database.php");
    include("../api/logger.php");

    $loggerName = "API-Installer";

    // api_keys table creation

    $query = "CREATE TABLE IF NOT EXISTS `api_keys` (
                `id` int NOT NULL AUTO_INCREMENT,
                `owner` varchar(35) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
                `api_key` varchar(42) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
                `api_version` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
                `perms` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
                `created` date NOT NULL,
                `is_active` int NOT NULL DEFAULT '1',
                PRIMARY KEY (`id`),
                UNIQUE KEY `api_key_2` (`api_key`,`api_version`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;";
    $stmt = $conn->prepare($query);

    if($stmt === false) {
        makeLog($loggerName, "", "SQL Prepare Error: " . $conn->error, 2);
        echo("<div align='center'><h2 style='color: red;'>An error occurred</h2><p>" . $conn->error . "</p></div>");
        exit;
    }

    if($stmt->execute()) {
        $stmt->close();
        makeLog($loggerName, "", "Table api_keys successfully created", 4);
    } else {
        makeLog($loggerName, "", "SQL Error: " . $conn->error, 3);
        echo("<div align='center'><h2 style='color: red;'>An error occurred</h2><p>" . $conn->error . "</p></div>");
        exit;
    }

    // login table creation

    $query = "CREATE TABLE IF NOT EXISTS `login` (
                `id` int NOT NULL AUTO_INCREMENT,
                `username` varchar(35) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
                `email` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
                `password` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
                `rank` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `username` (`username`),
                UNIQUE KEY `email` (`email`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;";
    $stmt = $conn->prepare($query);

    if($stmt === false) {
        makeLog($loggerName, "", "SQL Prepare Error: " . $conn->error, 2);
        echo("<div align='center'><h2 style='color: red;'>An error occurred</h2><p>" . $conn->error . "</p></div>");
        exit;
    }

    if($stmt->execute()) {
        $stmt->close();
        makeLog($loggerName, "", "Table login successfully created", 4);
    } else {
        makeLog($loggerName, "", "SQL Error: " . $conn->error, 3);
        echo("<div align='center'><h2 style='color: red;'>An error occurred</h2><p>" . $conn->error . "</p></div>");
        exit;
    }

    // mailing table creation

    $query = "CREATE TABLE IF NOT EXISTS `mailing` (
                `id` int NOT NULL AUTO_INCREMENT,
                `email` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
                `subscribe` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT 'true',
                PRIMARY KEY (`id`),
                UNIQUE KEY `email` (`email`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;";
    $stmt = $conn->prepare($query);

    if($stmt === false) {
        makeLog($loggerName, "", "SQL Prepare Error: " . $conn->error, 2);
        echo("<div align='center'><h2 style='color: red;'>An error occurred</h2><p>" . $conn->error . "</p></div>");
        exit;
    }

    if($stmt->execute()) {
        $stmt->close();
        makeLog($loggerName, "", "Table mailing successfully created", 4);
    } else {
        makeLog($loggerName, "", "SQL Error: " . $conn->error, 3);
        echo("<div align='center'><h2 style='color: red;'>An error occurred</h2><p>" . $conn->error . "</p></div>");
        exit;
    }

    // projects table creation

    $query = "CREATE TABLE IF NOT EXISTS `projects` (
                `id` int NOT NULL AUTO_INCREMENT,
                `name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
                `technologies` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
                `owner` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
                `creation` date NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `name` (`name`,`owner`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;";
    $stmt = $conn->prepare($query);

    if($stmt === false) {
        makeLog($loggerName, "", "SQL Prepare Error: " . $conn->error, 2);
        echo("<div align='center'><h2 style='color: red;'>An error occurred</h2><p>" . $conn->error . "</p></div>");
        exit;
    }

    if($stmt->execute()) {
        $stmt->close();
        makeLog($loggerName, "", "Table projects successfully created", 4);
    } else {
        makeLog($loggerName, "", "SQL Error: " . $conn->error, 3);
        echo("<div align='center'><h2 style='color: red;'>An error occurred</h2><p>" . $conn->error . "</p></div>");
        exit;
    }

    // project_translations table creation

    $query = "CREATE TABLE IF NOT EXISTS `project_translations` (
                `id` int NOT NULL AUTO_INCREMENT,
                `project_id` int NOT NULL,
                `language` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
                `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `project_id` (`project_id`,`language`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;";
    $stmt = $conn->prepare($query);

    if($stmt === false) {
        makeLog($loggerName, "", "SQL Prepare Error: " . $conn->error, 2);
        echo("<div align='center'><h2 style='color: red;'>An error occurred</h2><p>" . $conn->error . "</p></div>");
        exit;
    }

    if($stmt->execute()) {
        $stmt->close();
        makeLog($loggerName, "", "Table project_translations successfully created", 4);
    } else {
        makeLog($loggerName, "", "SQL Error: " . $conn->error, 3);
        echo("<div align='center'><h2 style='color: red;'>An error occurred</h2><p>" . $conn->error . "</p></div>");
        exit;
    }

    // Super key generation

    $owner = "root";
    $key = generateUuidV4WithPrefix(KEY_PREFIX);
    $apiVersion = "v" . substr(VERSION, 0, 1);
    $perms = "*";
    $created = date("Y-m-d");
    $isActive = 1;

    $query = "INSERT INTO api_keys (owner, api_key, api_version, perms, created, is_active) VALUES (?,?,?,?,?,?)";

    $stmt = $conn->prepare($query);

    if($stmt === false) {
        makeLog($loggerName, "", "SQL Prepare Error: " . $conn->error, 2);
        echo("<div align='center'><h2 style='color: red;'>An error occurred</h2><p>" . $conn->error . "</p></div>");
        exit;
    }

    $stmt->bind_param("sssssi", $owner, $key, $apiVersion, $perms, $created, $isActive);

    if($stmt->execute()) {
        $stmt->close();

        makeLog($loggerName, $key, "Super key created successfully for the API", 4);
        echo("<div align='center'><h2>The super key of the API is:</h2><p>$key</p></div>");

        
        $file = 'config.php';
        $content = file_get_contents($file);
        $newConfig = "define(\"API_INSTALLED\", true);";

        if (strpos($content, 'define("API_INSTALLED"') !== false) {
            $content = preg_replace('/define\("API_INSTALLED",\s*(true|false)\);/', $newConfig, $content);
        } else {
            $content = preg_replace('/<\?php/', "<?php\n$newConfig\n", $content, 1);
        }

        file_put_contents($file, $content);

        exit;
    } else {
        makeLog($loggerName, "", "SQL Error: " . $conn->error, 3);
        echo("<div align='center'><h2 style='color: red;'>An error occurred</h2><p>" . $conn->error . "</p></div>");
        exit;
    }

    function generateUuidV4WithPrefix($prefix) {
        $bytes = random_bytes(16);
    
        $bytes[6] = chr((ord($bytes[6]) & 0x0f) | 0x40);
        $bytes[8] = chr((ord($bytes[8]) & 0x3f) | 0x80);
    
        $uuid = sprintf(
            '%s-%s-%s-%s-%s',
            bin2hex(substr($bytes, 0, 4)),
            bin2hex(substr($bytes, 4, 2)),
            bin2hex(substr($bytes, 6, 2)),
            bin2hex(substr($bytes, 8, 2)),
            bin2hex(substr($bytes, 10))
        );
    
        return $prefix . $uuid;
    }
?>
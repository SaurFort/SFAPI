<?php
    function makeLog(string $apiRequested, string $apiKey, string $message, int $severity) {
        date_default_timezone_set('UTC'); // Set timezone to UTC (adjust as needed)

        $date = date("Y-m-d"); // Format the date for the log file name
        $timestamp = date("H:i:s"); // Get the current time for the log entry
        //$userIP = getUserIP(); // Get the IP address of the user
        $fileName = LOGS_FOLDER . $date . ".log"; // Create the log file name using the current date

        if (!file_exists(LOGS_FOLDER)) {
            mkdir(LOGS_FOLDER, 0777, true);
        }

        $logMessage = "$severity: [$timestamp] [$apiRequested]: " . $message . " - Key: " . $apiKey . "\n"; // Create the log message with a newline

        // Debugging output
        // echo($fileName);

        // Write the log message to the log file, create the file if it doesn't exist
        file_put_contents($fileName, $logMessage, FILE_APPEND);
    }

    /*function getUserIP() {
        // Check for shared internet/ISP IPs
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } 
        // Check for proxies
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } 
        // Default remote IP address
        else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }*/
?>
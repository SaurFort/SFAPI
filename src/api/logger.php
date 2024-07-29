<?php
    function makeLog(string $apiRequested, string $apiKey, string $message, int $severity) {
        date_default_timezone_set('UTC'); // Set timezone to UTC (adjust as needed)

        $date = date("Y-m-d"); // Format the date for the log file name
        $timestamp = date("H:i:s"); // Get the current time for the log entry
        $userIP = getUserIP(); // Get the IP address of the user
        $fileName = $date . ".log"; // Create the log file name using the current date
        $logMessage = "$severity: [$timestamp] [$apiRequested] | [$userIP]: " . $message . " with the key " . $apiKey . "\n"; // Create the log message with a newline

        // Write the log message to the log file, create the file if it doesn't exist
        file_put_contents(LOGS_FOLDER . $fileName, $logMessage, FILE_APPEND);
    }

    function getUserIP() {
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
    }
?>
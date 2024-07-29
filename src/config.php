<?php
    // API Info
    define("NAME", "SFAPI");
    define("VERSION", "0.6.0");
    define("CREATORS", "SaurFort");
    define("AVAILABLE LANGUAGE", "en;fr");

    // API settings
    define("LOGS_FOLDER", "../../logs/"); // Path to logs folder

    // API Key
    define("KEY_PREFIX", "apiv0_");

    // API Permissions
    define("PERMISSION_REGISTER_USER", 0);
    define("PERMISSION_LOGIN_USER", 1);
    define("PERMISSION_CREATE_PROJECTS", 2);
    define("PERMISSION_READ_PROJECTS", 3);
    define("PERMISSION_UPDATE_PROJECTS", 4);
    define("PERMISSION_DELETE_PROJECTS", 5);

    // Error code
    define("QUERY_WORKED_SUCCESSFULLY", "01");
    define("INVALID_API_METHOD", "02");
    define("EMPTY_API_KEY", "10");
    define("API_KEY_VERSION_ERROR", "11");
    define("API_KEY_PERMISSION_ERROR", "12");
    define("API_KEY_WRONG", "13");
    define("ACCOUNT_REGISTER_ARGUMENT_ERROR", "14");
    define("ACCOUNT_REGISTER_PASSWORD_ERROR", "15");
    define("ACCOUNT_REGISTER_DUPLICATE_ERROR", "16");
    define("ACCOUNT_LOGIN_ARGUMENT_ERROR", "17");
    define("ACCOUNT_LOGIN_FAILED", "18");
    define("PROJECT_ACTION_ERROR", "30");
    define("PROJECT_CREATE_ARGUMENT_ERROR", "31");
    define("PROJECT_UPDATE_ARGUMENT_ERROR", "32");
    define("PROJECT_DELETE_ARGUMENT_ERROR", "33");
    define("PROJECT_READ_ARGUMENT_ERROR", "34");
    define("SQL_QUERY_ERROR", "90");
    define("SQL_QUERY_EMPTY_ROW_ERROR", "91");
    define("SQL_PREPARE_ERROR", "92");
?>
<?php
    // API Info
    define("NAME", "SFAPI");
    define("VERSION", "0.5.0");
    define("CREATOR", "SaurFort");
    define("AVAILABLE LANGUAGE", "en;fr");

    // API Key
    define("KEY_PREFIX", "apiv0_");

    // API Permissions
    define("PERMISSION_CREATE_PROJECTS", 0);
    define("PERMISSION_UPDATE_PROJECTS", 1);
    define("PERMISSION_DELETE_PROJECTS", 2);
    define("PERMISSION_READ_PROJECTS", 3);

    // Error code
    define("EMPTY_API_KEY", "10");
    define("API_KEY_VERSION_ERROR", "11");
    define("API_KEY_PERMISSION_ERROR", "12");
    define("API_KEY_WRONG", "13");
    define("PROJECT_ACTION_ERROR", "30");
    define("PROJECT_CREATE_ARGUMENT_ERROR", "31");
    define("PROJECT_UPDATE_ARGUMENT_ERROR", "32");
    define("PROJECT_DELETE_ARGUMENT_ERROR", "33");
    define("PROJECT_READ_ARGUMENT_ERROR", "34");
    define("SQL_QUERY_ERROR", "90");
    define("SQL_QUERY_EMPTY_ROW_ERROR", "91");
    define("SQL_PREPARE_ERROR", "92");
?>
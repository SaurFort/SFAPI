<?php
    // API Info
    define("NAME", "SFAPI");
    define("VERSION", "0.9.1");
    define("CREATORS", "SaurFort");
    define("AVAILABLE_LANGUAGES", "en;fr");

    // API settings
    define("LOGS_FOLDER", "../../logs/");               // Path to logs folder
    define("KEY_PREFIX", "apiv0_");                     // API Key prefix
    define("API_DELAY", 1);                             // API delay in seconds to improve security, recommended: 1 second

    // Database 
    define("DB_SERVER", "localhost:3308");
    define("DB_NAME", "sfapi");
    define("DB_USERNAME", "root");
    define("DB_PASSWORD", "");

    // Mailing API (SMTP)
    define("MAILING_ENABLED", false);                   // If you can access to a mailing services so you can enabled it
    define("SMTP_SERVER" , "");                         // Put your out server using SMTP
    define("SMTP_PORT" , "");                           // default: 465
    define("SMTP_AUTH" , true);                         // If your SMTP server request a password set it on true, else set it on false
    define("EMAIL_ADDRESS" , "");                       // Recommended: use a noreply email address.
    define("EMAIL_NAME", "");                           // The name you want to be showed when user received an email of the API
    define("EMAIL_PASSWORD" , "");                      // If auth is set to true you need to fill this

    // API Permissions
    define("TOTAL_PERMISSIONS", 13);                    // Update this value if you add more permissions
    define("PERMISSION_REGISTER_USER", 0);
    define("PERMISSION_LOGIN_USER", 1);
    define("PERMISSION_SEND_MAIL", 2);
    define("PERMISSION_CREATE_PROJECTS", 3);
    define("PERMISSION_READ_PROJECTS", 4);
    define("PERMISSION_UPDATE_PROJECTS", 5);
    define("PERMISSION_DELETE_PROJECTS", 6);
    define("PERMISSION_OTHER_USERS_PROJECTS", 7);
    define("PERMISSION_DELETE_KEY", 8);
    define("PERMISSION_DISABLE_KEY", 9);
    define("PERMISSION_ENABLE_KEY", 10);
    define("PERMISSION_GENERATE_KEY", 11);
    define("PERMISSION_LIST_KEY", 12);
    define("PERMISSION_ACCESS_OTHER_USER_KEY", 13);

    // API Code
    define("QUERY_WORKED_SUCCESSFULLY", "01");
    define("INVALID_API_METHOD", "02");
    define("EMPTY_API_KEY", "10");
    define("API_KEY_VERSION_ERROR", "11");
    define("API_KEY_PERMISSION_ERROR", "12");
    define("API_KEY_WRONG", "13");
    define("API_KEY_DISABLED", "14");
    define("API_KEY_SUPERKEY_NEEDED", "15");
    define("API_KEY_GENERATE_ARGUMENT_ERROR", "16");
    define("API_KEY_DELETE_ARGUMENT_ERROR", "17");
    define("API_KEY_ENABLE_OR_DISABLE_ARGUMENT_ERROR", "18");
    define("API_KEY_LIST_ARGUMENT_ERROR", "19");
    define("MAILER_DISABLED", "20");
    define("MAILER_SEND_EMAIL_ARGUMENT_ERROR", "21");
    define("MAILER_SEND_EMAIL_FAILED", "22");
    define("PROJECT_CREATE_ARGUMENT_ERROR", "30");
    define("PROJECT_UPDATE_ARGUMENT_ERROR", "31");
    define("PROJECT_DELETE_ARGUMENT_ERROR", "32");
    define("PROJECT_READ_ARGUMENT_ERROR", "33");
    define("ACCOUNT_REGISTER_ARGUMENT_ERROR", "40");
    define("ACCOUNT_REGISTER_PASSWORD_ERROR", "41");
    define("ACCOUNT_REGISTER_DUPLICATE_ERROR", "42");
    define("ACCOUNT_LOGIN_ARGUMENT_ERROR", "43");
    define("ACCOUNT_LOGIN_FAILED", "44");
    define("SQL_QUERY_ERROR", "90");
    define("SQL_QUERY_EMPTY_ROW_ERROR", "91");
    define("SQL_PREPARE_ERROR", "92");

    // When the API is installed, this change to be true
    define("API_INSTALLED", false);
?>
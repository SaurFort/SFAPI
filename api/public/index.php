<?php
    require("../config.php");

    if(isset($_GET['install']) && $_GET['install'] === "true" && !API_INSTALLED) {
        include("../install.php");
    }
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>SaurFort's API</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                margin: 0;
                background-color: #f4f4f4;
                color: #333;
            }
            main {
                text-align: center;
            }
            h1 {
                color: #e74c3c;
            }
        </style>
    </head>
    <body>
        <main>
            <h1>Access forbidden</h1>
            <p>You are not authorized to access this API.</p>
            <p>To access the API, your login must be whitelisted.</p>
        </main>
    </body>
</html>

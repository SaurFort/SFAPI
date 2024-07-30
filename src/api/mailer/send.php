<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");

    // Debugging infinite sending data
    //ini_set('max_execution_time', 300); // 300 seconds = 5 minutes

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require('../../../vendor/autoload.php');
    require('../../config.php');
    include('../database.php');
    include('../checker.php');
    include('../logger.php');

    $key = isset($_GET['key']) ? $_GET['key'] : "";
    $loggerName = "API-SendMail";

    // Check if the key is valid and the action is defined
    $perms = verifyAPIKey($key);
    if ($perms === null) {
        exit; // The verifyAPIKey function has already returned an appropriate response and terminated the execution
    }

    // Check if the API key has permission to send mail
    if (!verifyKeyPerms($key, $perms, PERMISSION_SEND_MAIL)) {
        exit;
    }

    // Check if the Mailer API is enabled
    if (!MAILING_ENABLED) {
        makeLog($loggerName, $key, "Someone tried to use the mailer.", 3);
        echo json_encode(["code" => MAILER_DISABLED, "message" => "You can't send email because this functionality is disabled."]);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
        $data = json_decode(file_get_contents("php://input"), true);

        if (empty($data['email'])) {
            makeLog($loggerName, $key, "No email provided", 2);
            echo json_encode(["code" => MAILER_SEND_EMAIL_ARGUMENT_ERROR . "A", "message" => "Argument email is not provided"]);
            exit;
        }
        if (empty($data['subject'])) {
            makeLog($loggerName, $key, "No subject provided", 2);
            echo json_encode(["code" => MAILER_SEND_EMAIL_ARGUMENT_ERROR . "B", "message" => "Argument subject is not provided"]);
            exit;
        }
        if (empty($data['body'])) {
            makeLog($loggerName, $key, "No body provided", 2);
            echo json_encode(["code" => MAILER_SEND_EMAIL_ARGUMENT_ERROR . "C", "message" => "Argument body is not provided"]);
            exit;
        }

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = SMTP_SERVER;
            $mail->SMTPAuth   = SMTP_AUTH;
            $mail->Username   = EMAIL_ADDRESS;
            $mail->Password   = EMAIL_PASSWORD;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = SMTP_PORT;
            $mail->CharSet    = "UTF-8";

            $mail->setFrom(EMAIL_ADDRESS, EMAIL_NAME);
            $mail->addAddress($data['email']);

            $mail->isHTML(true);
            $mail->Subject = $data['subject'];
            $mail->Body    = $data['body'];

            $mail->send();
            makeLog($loggerName, $key, "Email sent to " . $data['email'] . " with the subject " . $data['subject'] . " and the body " . $data['body'], 3);
            echo json_encode(["code" => QUERY_WORKED_SUCCESSFULLY, "message" => "Email sent successfully"]);
            exit;
        } catch (Exception $e) {
            makeLog($loggerName, $key, "Email could not be sent. Mailer Error: " . $mail->ErrorInfo, 3);
            echo json_encode(["code" => MAILER_SEND_EMAIL_FAILED, "message" => "Email could not be sent. Mailer Error: " . $mail->ErrorInfo]);
            exit;
        }
    } else {
        makeLog($loggerName, $key, "Wrong method request", 1);
        echo json_encode(["code" => INVALID_API_METHOD, "message" => "Send mail can only take PUT method"]);
        exit;
    }
?>

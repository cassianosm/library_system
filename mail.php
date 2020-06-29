<?php

require_once './vendor/autoload.php';

// Message array -> default is false
$msg = array(
    "success" => false,
    "message" => array(),
);

// http://respect.github.io/Validation/
use Respect\Validation\Validator as v;

if (isset($_POST["name"]) && isset($_POST["email"]) && isset($_POST["message"])) {

    $nameValidator = v::stringType()->length(5, 99);
    $validName = $nameValidator->validate($_POST["name"]);

    $messageValidator = v::stringType()->length(5, 999);
    $validMessage = $messageValidator->validate($_POST["message"]);

    $emailValidator = v::email();
    $validEmail = $emailValidator->validate($_POST["email"]);

    if (!$validName) {
        array_push($msg["message"], "Name must be larger than 5 digits.");
    }

    if (!$validEmail) {
        array_push($msg["message"], "Invalid E-mail.");
    }

    if (!$validMessage) {
        array_push($msg["message"], "Message must be larger than 5 digits.");
    }

    if ($validEmail && $validName && $validMessage) {

        $from = $_POST["email"];
        $name = $_POST["name"];
        $message = $_POST["message"];

        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= 'From: ' . $from . "\r\n" .
        'Reply-To: ' . $from . "\r\n" .
        'X-Mailer: PHP/' . phpversion();

        $to = 'contact@cassianosm.com';
        $subject = 'Site Contact: ' . $name;

        if (!mail($to, $subject, $message, $headers)) {
            array_push($msg["message"], "Error sending e-mail.");
        } else {
            $msg["success"] = true;
            array_push($msg["message"], "Thank you, your message was sent.");
        }

    }

} else {
    array_push($msg["message"], "Error sending e-mail.");
}

echo (json_encode($msg));

?>
<?php

require_once 'sessionHandler.php';
require_once './class/DBConnection.class.php';
require_once './vendor/autoload.php';
require_once 'functions.php';

// http://respect.github.io/Validation/
use Respect\Validation\Validator as v;

// Message array -> default is false
$msg = array(
    "success" => false,
    "message" => array(),
);

// User that is logged in should not be able to call this page.
if (isset($_SESSION["user"])) {
    array_push($msg["message"], "Already logged in");
    echo (json_encode($msg));
    die;
}

// Error if no desired action
if (!isset($_POST["action"])) {
    array_push($msg["message"], "No action");
    echo (json_encode($msg));
    die;
} else {
    $action = $_POST["action"];
}

// Login action
if (isset($_POST["login"]) && isset($_POST["password"]) && $action == "login") {

    $loginValidator = v::stringType()->noWhitespace()->length(4, 99);
    $validLogin = $loginValidator->validate($_POST["login"]);

    $passValidator = v::stringType()->noWhitespace()->length(5, 99);
    $validPass = $passValidator->validate($_POST["password"]);

    if (!$validLogin) {
        array_push($msg["message"], "Invalid Username: must contain at least 4 digits and no white spaces.");
    }

    if (!$validPass) {
        array_push($msg["message"], "Invalid Password: must contain at least 5 digits and no white spaces.");
    }

    if (!$validLogin || !$validPass) {
        echo (json_encode($msg));
        die;
    }

    $conn = new DBConnection();

    $conn = $conn->getConnection();

    $login = $conn->real_escape_string($_POST["login"]);

    // Still have to hash in JavaScript since not https
    $pass = hash("sha256", $_POST["password"]);

    $string = "call sp_user_login('" . $login . "', '" . $pass . "');";

    $result = $conn->query($string);

    clearConnection($conn);

    if ($result && $result->num_rows > 0) {

        $resultset = $result->fetch_assoc();

        $_SESSION["user"] = $resultset;

        $_SESSION["user"]["reserved_items"] = explode(',', $_SESSION["user"]["reserved_items"]);

        $msg["success"] = true;
        array_push($msg["message"], "Success");

    } else {
        $msg["success"] = false;
        array_push($msg["message"], "Login / Pass combination not found");

    }
    echo (json_encode($msg));
    mysqli_close($conn);
    die();
}

// Register action
if ($action == "register") {

    // Check if all data is sent
    if (isset($_POST["login"]) && isset($_POST["password"]) && isset($_POST["name"]) && isset($_POST["confirm"]) && isset($_POST["email"])) {

        // Validation
        $loginValidator = v::stringType()->noWhitespace()->length(4, 99);
        $validLogin = $loginValidator->validate($_POST["login"]);

        $passValidator = v::stringType()->noWhitespace()->length(5, 99);
        $validPass = $passValidator->validate($_POST["password"]);

        $nameValidator = v::stringType()->length(5, 99);
        $validName = $nameValidator->validate($_POST["name"]);

        $emailValidator = v::email();
        $validEmail = $emailValidator->validate($_POST["email"]);

        if (!$validName) {
            array_push($msg["message"], "Name must be larger than 5 digits.");
        }

        if (!$validLogin) {
            array_push($msg["message"], "Invalid Username: must contain at least 4 digits and no white spaces.");
        }

        if (!$validEmail) {
            array_push($msg["message"], "Invalid E-mail.");
        }

        if (!$validPass) {
            array_push($msg["message"], "Invalid Password: must contain at least 5 digits and no white spaces.");
        }

        if ($_POST["password"] !== $_POST["confirm"]) {
            array_push($msg["message"], "Passwords don\'t match.");
        }

        if (!$validLogin || !$validPass || !$validName || !$validEmail || $_POST["password"] !== $_POST["confirm"]) {

            echo (json_encode($msg));
            die();

        } else {

            $conn = new DBConnection();

            $conn = $conn->getConnection();

            $login = $conn->real_escape_string($_POST["login"]);
            $name = $conn->real_escape_string($_POST["name"]);
            $email = $conn->real_escape_string($_POST["email"]);

            // Still have to hash in JavaScript since not https
            $pass = hash("sha256", $_POST["password"]);

            $string = "call sp_insert_user('" . $name . "','" . $login . "','" . $pass . "', 0, '" . $email . "');";

            if (!($result = $conn->query($string))) {

                array_push($msg["message"], $conn->error);

            } else {

                $resultset = $result->fetch_assoc();

                $_SESSION["user"] = $resultset;
                $_SESSION["user"]["reserved_items"] = explode(',', $_SESSION["user"]["reserved_items"]);

                $msg["success"] = true;
                array_push($msg["message"], "Success");

            }

            echo (json_encode($msg));
            clearConnection($conn);
            mysqli_close($conn);
            die();

        }

    } else {
        array_push($msg["message"], "Post Error.");
        echo (json_encode($msg));
        die();
    }

}

echo (json_encode($msg));

?>
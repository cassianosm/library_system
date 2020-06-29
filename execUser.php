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
if (!isset($_SESSION["user"])) {
    array_push($msg["message"], "Not logged in");
    echo (json_encode($msg));
    die;
}

// Error if no desired action
if (!isset($_POST["action"])) {
    array_push($msg["message"], "Error");
    echo (json_encode($msg));
    die;
} else {
    $action = $_POST["action"];
}

// Update Pass
if ($action == "updatePass") {

    if (isset($_POST["password"]) && isset($_POST["confirm"]) && isset($_POST["id"])) {

        $passValidator = v::stringType()->noWhitespace()->length(5, 99);
        $validPass = $passValidator->validate($_POST["password"]);

        if (!$validPass) {
            array_push($msg["message"], "Invalid Password: must contain at least 5 digits and no white spaces.");
        }

        if ($_POST["password"] !== $_POST["confirm"]) {
            array_push($msg["message"], "Passwords don't match.");
        }

        if ($_POST["id"] !== $_SESSION["user"]["id"]) {
            array_push($msg["message"], "Session Error.");
        }

        if (!$validPass || $_POST["password"] !== $_POST["confirm"] || $_POST["id"] !== $_SESSION["user"]["id"]) {
            echo (json_encode($msg));
            die;
        } else {

            $conn = new DBConnection();
            $conn = $conn->getConnection();

            $pass = hash("sha256", $_POST["password"]);

            $string = "call sp_update_user_pass('" . $pass . "', " . $_SESSION["user"]["id"] . ");";

            if (!($result = $conn->query($string))) {

                array_push($msg["message"], $conn->error);

            } else {

                $msg["success"] = true;
                array_push($msg["message"], "Password changed successfully");

            }
            echo (json_encode($msg));
            clearConnection($conn);
            mysqli_close($conn);
            die();
        }

    } else {
        array_push($msg["message"], "Error.");
        echo (json_encode($msg));
        die();
    }

}

// Update info
if ($action == "updateInfo") {

    if (isset($_POST["login"]) && isset($_POST["name"]) && isset($_POST["email"]) && isset($_POST["id"])) {

        $loginValidator = v::stringType()->noWhitespace()->length(4, 99);
        $validLogin = $loginValidator->validate($_POST["login"]);

        $nameValidator = v::stringType()->length(5, 99);
        $validName = $nameValidator->validate($_POST["name"]);

        $emailValidator = v::email();
        $validEmail = $emailValidator->validate($_POST["email"]);

        if (!$validLogin) {
            array_push($msg["message"], "Invalid Username: must contain at least 4 digits and no white spaces.");
        }

        if (!$validName) {
            array_push($msg["message"], "Name must be larger than 5 digits.");
        }

        if (!$validEmail) {
            array_push($msg["message"], "Invalid E-mail.");
        }

        if ($_POST["id"] !== $_SESSION["user"]["id"]) {
            array_push($msg["message"], "Session Error.");
        }

        if (!$validLogin || !$validName || !$validEmail || $_POST["id"] !== $_SESSION["user"]["id"]) {
            echo (json_encode($msg));
            die();
        } else {

            $conn = new DBConnection();
            $conn = $conn->getConnection();

            $login = $conn->real_escape_string($_POST["login"]);
            $name = $conn->real_escape_string($_POST["name"]);
            $email = $conn->real_escape_string($_POST["email"]);

            $string = "call sp_update_user_info('" . $name . "','" . $login . "', 0, '" . $email . "', " . $_SESSION["user"]["id"] . ");";

            //$result = $conn -> query($string);

            clearConnection($conn);

            if (!($result = $conn->query($string))) {

                array_push($msg["message"], $conn->error);

            } else {

                $resultset = $result->fetch_assoc();

                $_SESSION["user"] = $resultset;

                $msg["success"] = true;
                array_push($msg["message"], "Info updated.");

            }

            echo (json_encode($msg));
            clearConnection($conn);
            mysqli_close($conn);
            die();

        }

    } else {

        array_push($msg["message"], "Error.");
        echo (json_encode($msg));
        die();

    }
}

// Reserve Item or Cancel reservation
// The procedures return the the current reserved items
if ($action == "reserveItem" || $action == "cancelReserve") {

    if (isset($_POST["item_id"]) && is_numeric(intval($_POST["item_id"]))) {

        $conn = new DBConnection();
        $conn = $conn->getConnection();

        if ($action == "reserveItem") {

            $string = "call sp_reserve_item(" . $_POST["item_id"] . ", " . $_SESSION["user"]["id"] . ");";
            $feedback = "Item reserved successfully";

        } else {

            $string = "call sp_cancel_reserve(" . $_POST["item_id"] . ", " . $_SESSION["user"]["id"] . ");";
            $feedback = "Item reservation canceled successfully";
        }

        clearConnection($conn);

        if (!($result = $conn->query($string))) {

            array_push($msg["message"], $conn->error);

        } else {

            $resultset = $result->fetch_assoc();

            if (sizeof($resultset) > 0) {
                $temp = explode(",", $resultset["reserved_items"]);
                $_SESSION["user"]["reserved_items"] = $temp;

            } else {
                $_SESSION["user"]["reserved_items"] = array();
            }

            $msg["success"] = true;
            array_push($msg["message"], $feedback);

        }

        echo (json_encode($msg));
        clearConnection($conn);
        mysqli_close($conn);
        die();

    } else {

        array_push($msg["message"], "Error.");
        echo (json_encode($msg));
        die();

    }

}

echo (json_encode($msg));

?>
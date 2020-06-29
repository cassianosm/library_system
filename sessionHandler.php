<?php
session_start();

if (!isset($_SESSION["user"])) {
    $user = array();
} else {
    $user = $_SESSION["user"];
}

?>
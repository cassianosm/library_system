<?php

session_start();
// Unset all of the session variables.
$_SESSION = array();
$_SESSION["user"] = array();
session_destroy();

header('Location: index.php');
exit;

?>
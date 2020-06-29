<?php

require_once 'sessionHandler.php';

require_once './class/DBConnection.class.php';
require_once './vendor/autoload.php';
require_once 'functions.php';

// User that is logged in should be able to see this page.
if (sizeof($_SESSION["user"]) == 0) {
    header('Location: index.php');
    exit;
}

$template = getTwigTemplate('profile.twig');
$conn = new DBConnection();
$conn = $conn->getConnection();

// Retrieving categories
$cat = $conn->query("call sp_retrieveCategories();");
clearConnection($conn);

echo $template->render(array("title" => "Profile",
    "breadCrumb" => "Profile",
    "categories" => $cat,
    "user" => $user));

mysqli_close($conn);

?>
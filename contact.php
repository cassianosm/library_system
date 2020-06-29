<?php

require_once 'sessionHandler.php';

require_once './class/DBConnection.class.php';
require_once './vendor/autoload.php';
require_once 'functions.php';

$template = getTwigTemplate('contact.twig');
$conn = new DBConnection();
$conn = $conn->getConnection();

// Retrieving categories
$cat = $conn->query("call sp_retrieveCategories();");
clearConnection($conn);

echo $template->render(array("title" => "Contact",
    "breadCrumb" => "Contact",
    "categories" => $cat,
    "user" => $user));

mysqli_close($conn);

?>
<?php

require_once 'sessionHandler.php';

require_once './class/DBConnection.class.php';
require_once './vendor/autoload.php';
require_once 'functions.php';

$template = getTwigTemplate('home.twig');
$conn = new DBConnection();
$conn = $conn->getConnection();

// Retrieving categories
$cat = $conn->query("call sp_retrieveCategories();");
clearConnection($conn);

// Implement load more (jquery?)
$result = $conn->query("call sp_retrieveFirstPageItems(1, 20);");

//print_r($result);
clearConnection($conn);

echo $template->render(array("title" => "Home",
    "breadCrumb" => "Home",
    "items" => $result,
    "categories" => $cat,
    "user" => $user));

mysqli_close($conn);

?>
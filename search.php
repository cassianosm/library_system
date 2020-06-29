<?php

require_once 'sessionHandler.php';

require_once './class/DBConnection.class.php';
require_once './vendor/autoload.php';
require_once 'functions.php';

// http://respect.github.io/Validation/
use Respect\Validation\Validator as v;

// validate input
if (!isset($_GET["search"])) {
    header('Location: index.php');
    exit;
}

$validSearch = v::stringType()->length(3, 99)->validate($_GET["search"]);

if (!$validSearch) {
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}

$template = getTwigTemplate('category.twig');
$conn = new DBConnection();
$conn = $conn->getConnection();

// Retrieving categories
$cat = $conn->query("call sp_retrieveCategories();");
clearConnection($conn);

$search = $conn->real_escape_string($_GET["search"]);
$string = 'call sp_searchItems("' . $search . '");';

$result = $conn->query($string);

//print_r($result);
clearConnection($conn);

echo $template->render(array("title" => "Search Results",
    "breadCrumb" => "Search",
    "items" => $result,
    "categories" => $cat,
    "user" => $user));

mysqli_close($conn);

?>
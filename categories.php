<?php

require_once 'sessionHandler.php';

require_once './class/DBConnection.class.php';
require_once './vendor/autoload.php';
require_once 'functions.php';

// Checking for categoryId and Name
if (isset($_GET["categoryid"]) && is_numeric(intval($_GET["categoryid"])) && isset($_GET["categoryname"])) {
    $categoryId = $_GET["categoryid"];
    $categoryName = urldecode($_GET["categoryname"]);
} else {
    header('Location: index.php');
    exit;
}

// Array that contains info for displaying pagination
$pagination = array(
    "page" => 1,
    "number_per_page" => 20,
    "total" => 0,
    "start" => 0,
    "totalPages" => 1,
    "baseLink" => "categories.php?categoryid=" . $categoryId . "&categoryname=" . urlencode($categoryName),
);

if (isset($_GET["page"]) && is_numeric(intval($_GET["page"])) && $_GET["page"] > 0) {
    $pagination["page"] = $_GET["page"];
}

$template = getTwigTemplate('category.twig');
$conn = new DBConnection();
$conn = $conn->getConnection();

// Retrieving categories
$cat = $conn->query("call sp_retrieveCategories();");
clearConnection($conn);

// Retrieving total in category
$string = "call sp_retrieveTotalInCategory(" . $categoryId . ");";
$resultTotal = $conn->query($string);

if ($resultTotal) {
    $total = $resultTotal->fetch_assoc();
    $pagination["total"] = $total["total"];
}

clearConnection($conn);

// Start position for query using limit
$pagination["start"] = ($pagination["page"] - 1) * $pagination["number_per_page"];

// Total page number
if ($pagination["total"] > 0) {
    $pagination["totalPages"] = ceil($pagination["total"] / $pagination["number_per_page"]);
}

// Retrieve items by start, total and category
$string = 'call sp_retrieveItemsByCategory(' . $pagination["start"] . ', ' . $pagination["number_per_page"] . ', ' . $categoryId . ');';

$result = $conn->query($string);
clearConnection($conn);

echo $template->render(array("title" => $categoryName,
    "breadCrumb" => "Category > " . $categoryName,
    "items" => $result,
    "categories" => $cat,
    "user" => $user,
    "pagination" => $pagination));

mysqli_close($conn);

?>
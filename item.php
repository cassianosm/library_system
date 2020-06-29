<?php

require_once 'sessionHandler.php';
require_once './class/DBConnection.class.php';
require_once './vendor/autoload.php';
require_once 'functions.php';

if (isset($_GET["itemid"]) && is_numeric(intval($_GET["itemid"]))) {
    $itemId = $_GET["itemid"];
} else {
    header('Location: index.php');
    exit;
}

$template = getTwigTemplate('item.twig');
$conn = new DBConnection();
$conn = $conn->getConnection();

// Retrieving categories
$cat = $conn->query("call sp_retrieveCategories();");
clearConnection($conn);

// Implement load more (jquery?)
$result = $conn->query("call sp_retrieveItem(" . $itemId . ");");

$item = [];
$itemTitle = "Detail";

if ($result && $result->num_rows > 0) {

    $resultset = $result->fetch_assoc();

    $item = $resultset;

    $itemTitle = $item["title"];

}

clearConnection($conn);

echo $template->render(array("title" => $itemTitle,
    "breadCrumb" => "Detail > " . $itemTitle,
    "item" => $item,
    "categories" => $cat,
    "user" => $user));

mysqli_close($conn);

?>
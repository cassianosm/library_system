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

$template = getTwigTemplate('dashboard.twig');
$conn = new DBConnection();
$conn = $conn->getConnection();

// Retrieving categories
$cat = $conn->query("call sp_retrieveCategories();");
clearConnection($conn);

$borrowed = [];
$reserved = [];

// Retrieving items
$string = 'call sp_retrieve_user_borrowed_reserved(' . $_SESSION["user"]["id"] . ');';

$result = $conn->multi_query($string);

if ($result) {

    $rowset1 = $conn->store_result();
    //$borrowed = $rowset1->fetch_assoc();

    $borrowed = array();

    while ($row = $rowset1->fetch_assoc()) {
        array_push($borrowed, $row);
    }

    $conn->next_result();

    $reserved = array();

    $rowset2 = $conn->store_result();
    //$reserved = $rowset2->fetch_assoc();

    while ($row = $rowset2->fetch_assoc()) {
        array_push($reserved, $row);
    }

    //var_dump($reserved);
}

clearConnection($conn);

echo $template->render(array("title" => "Dashboard",
    "breadCrumb" => "Dashboard",
    "reserved" => $reserved,
    "borrowed" => $borrowed,
    "categories" => $cat,
    "user" => $user));

mysqli_close($conn);

?>
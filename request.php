<?php
session_start();
include('person_block.php');
include('person_login.php');
include('person_signup.php');
include('person_profile.php');
include('person_post.php');
include('image_resize.php');


$BlockObj = new Block();
if ($BlockObj->isBlocked() == "Blocked") {
    echo json_encode("Blocked");
    exit;
}

$PostObj = new Post();

if (isset($_GET["type"])) {// Fetch posts from the database
    echo json_encode($PostObj->loadPosts());
}

if (isset($_POST["function"])) {
    switch($_POST["function"]) {
        case "login":
            $UserObj = new LogIn();
            $ResultStr = $UserObj->checkLogin($_POST["user"], $_POST["password"]);
            if ($ResultStr == "Blocked") {
                $BlockObj->blockPerson();
            }
            echo json_encode($ResultStr);
        break;
        case "signup":
            $NewUserObj = new SignUp();
            $ResultStr = $NewUserObj->checkSignUp($_POST['firstName'], $_POST['lastName'], $_POST['email'], $_POST['username'], $_POST['password']);
            echo json_encode($ResultStr);
        break;
        case "submit":
            $CurrentDateStr = date('Y-m-d H:i:s');
            if (!isset($_FILES["image"])) {
                $ResultObj = $PostObj->submitAltPost($_SESSION['id'], $_POST["title"], $_POST["message"], $CurrentDateStr);
                echo json_encode($ResultObj);
            } else {
                $ResultObj = $PostObj->submitPost($_SESSION['id'], $_POST["title"], $_POST["message"], $CurrentDateStr, $_FILES["image"]);
                echo json_encode($ResultObj);
            }
        break;
        case "getProfilePicture":
            $SOURCECONSTANTSTR = "data:image/jpeg;base64,";
            $ProfilePictureStr = $SOURCECONSTANTSTR . $_SESSION['pfp'];
            echo json_encode($ProfilePictureStr);
        break;
        case "changeProfile":
            $ProfileObj = new Profile();
            if (!isset($_FILES['image'])) {
                $ResultObj = $ProfileObj->changeAltProfile($_SESSION['id'], $_POST["name"], $_POST["surname"], $_POST["email"], $_POST['username'], $_POST['password']);
                echo json_encode($ResultObj);
            } else {
                $ResultObj = $ProfileObj->changeProfile($_SESSION['id'], $_POST["name"], $_POST["surname"], $_POST["email"], $_POST["username"], $_POST["password"], $_FILES['image']);
                echo json_encode($ResultObj);
            }
        break;
        case "deletePerson":
            $ProfileObj = new Profile();
            $ProfileObj->deleteThisProfile($_SESSION['id']);
            echo json_encode('deleted');
        break;
        case "getProfilePicture":
            $MyProfilePictureStr = $_SESSION['pfp'];
            echo json_encode($MyProfilePictureStr);
        break;
    }
}

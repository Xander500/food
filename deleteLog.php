<?php
    session_cache_expire(30);
    session_start();

    //get login and access details
    $loggedIn = false;
    $accessLevel = 0;
    $userID = null;
    if (isset($_SESSION['_id'])) {
        $loggedIn = true;
        // 0 = not logged in, 1 = student, 3 instructor
        $accessLevel = $_SESSION['access_level'];
        $userID = $_SESSION['_id'];
    } 
    // must be a logged in user
    if ($accessLevel < 1) {
        header('Location: login.php');
        echo 'bad access level';
        die();
    }

    require_once('database/dbVolunteerActivity.php');
    require_once('include/input-validation.php');

    //get log id
    $id = Null;
    if (isset($_GET['id'])) {
        //$args = sanitize($_POST);
        $id = $_GET['id'];
    }
    if (!$id) {
        header('Location: index.php');
        die();
    }
    //get log volunteer id
    $volunteer = get_volunteerID_from_logID($id);
    if (!$volunteer) {
        header('Location: index.php');
        die();
    }

    //must be an instructor or the same user as the log
    if ($accessLevel !== 3 && $userID !== $volunteer) {
        header('Location: log.php?' . http_build_query(["id" => $id]));
        echo 'not allowed to edit this log';
        die();
    }    

    if (delete_log($id)) {
        header('Location: index.php?deleteSuccess');
        die();
    }

    //
    die();
    header('Location: index.php');
?>
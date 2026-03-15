<?php
    session_cache_expire(30);
    session_start();

    if ($_SESSION['access_level'] < 2) {
        header('Location: index.php');
        die();
    }

    require_once('database/dbVolunteerActivity.php');
    require_once('include/input-validation.php');
    //$args = sanitize($_POST);
    //$id = $args['id'];
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
    }
    if (!$id) {
        header('Location: index.php');
        die();
    }
    

    if (delete_log($id)) {
        header('Location: viewAllLogs.php?deleteSuccess');
        die();
    }

    //
    header('Location: index.php');
?>
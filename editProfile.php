<?php
    // Author: Lauren Knight
    // Description: Profile edit page
    session_cache_expire(30);
    session_start();
    ini_set("display_errors",1);
    error_reporting(E_ALL);

    require_once('include/input-validation.php');

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

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["profile-edit-form"])) {
        require_once('domain/User.php');
        require_once('database/dbUsers.php');
        // make every submitted field SQL-safe except for password
        $ignoreList = array('password');
        $args = sanitize($_POST, $ignoreList);

        $editingSelf = true;
        if ($_SESSION['access_level'] >= 2 && isset($_POST['id'])) {
            $id = $_POST['id'];
            $editingSelf = $id == $_SESSION['_id'];
            $id = $args['id'];
            // Check to see if user is a lower-level manager here
        } else {
            $id = $_SESSION['_id'];
        }

        // echo "<p>The form was submitted:</p>";
        // foreach ($args as $key => $value) {
        //     echo "<p>$key: $value</p>";
        // }

        $required = array(
            'first_name', 'last_name',
            'email', 'season', 'year'
        );
        $errors = false;
        if (!wereRequiredFieldsSubmitted($args, $required)) {
            $errors = true;
        }

        $first_name = $args['first_name'];
        $last_name = $args['last_name'];
        $email = validateEmail($args['email']);
        if (!$email) {
            $errors = true;
            // echo 'bad email';
        }
        $semester = $args['season'] . " " . $args['year'];

        $person = retrieve_user($id);
       
        // For the new fields, default to 0 if not set
        if ($errors) {
            $updateSuccess = false;
        }

        $result = update_user_required($id, $first_name, $last_name, $email, $semester);

        if ($result) {
            if ($editingSelf) {
                header('Location: viewProfile.php?editSuccess');
            } else {
                header('Location: viewProfile.php?editSuccess&id='. $id);
            }
            die();
        } else {
            if ($editingSelf) {
                header('Location: viewProfile.php?editFailed');
            } else {
                header('Location: viewProfile.php?editFailed&id='. $id);
            }
            die();
        }
    }
?>
<!DOCTYPE html>
<html>
<head>
    <?php require_once('universal.inc'); ?>
    <title>UMW Alleviating Food Waste | Edit Profile</title>
    <link src="css/base.css" rel="stylesheet">
</head>
<body>
    <?php
        require_once('header.php');
        $isAdmin = $_SESSION['access_level'] >= 2;
        require_once('profileEditForm.php');
    ?>
</body>
</html>
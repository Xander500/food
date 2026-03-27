<?php
    session_start();
    require_once('include/input-validation.php');
?>

<!DOCTYPE html>
<html>
<head>
    <?php require_once('database/dbMessages.php'); ?>
    <title>UMW Alleviating Food Waste Volunteer Tracking | Add Organization</title>
    <link href="css/base.css" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="images/alleviatingFoodWasteLogo.png">
<!-- BANDAID FIX FOR HEADER BEING WEIRD -->
<?php 
$tailwind_mode = true;
?>
<style>
    .date-box {
        background: #C9AB81;
        padding: 7px 30px;
        border-radius: 50px;
        box-shadow: -4px 4px 4px rgba(0, 0, 0, 0.25) inset;
        color: white;
        font-size: 24px;
        font-weight: 700;
        text-align: center;
    }
    .dropdown {
        padding-right: 50px;
    }
</style>
<!-- BANDAID END, REMOVE ONCE SOME GENIUS FIXES -->
</head>
<body class="relative">
<?php
    require_once('header.php');

    require_once('domain/User.php');
    require_once('database/dbOrganizations.php');

    $showPopup = false;

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $args = sanitize($_POST);

        $required = array('name');

        $errors = false;

        if (!wereRequiredFieldsSubmitted($args, $required)) {
            $errors = true;
        }

        $name = $args['name'];
        
        if (isset($_POST['description'])) {
            $description = $args['description'];
        } else {
            $description = null;
        }

        if (isset($_POST['location'])) {
            $location = $args['location'];
        } else {
            $location = null;
        }

        if (isset($_POST['email'])) {
            $email = strtolower($args['email']);
            if (!validateEmail($email)) {
                echo "<p>Invalid email.</p>";
                $errors = true;
            }
        } else {
            $email = null;
        }

        if ($errors) {
            echo '<p class="error">Your form submission contained unexpected or invalid input.</p>';
            die();
        }

        // echo "pre object: " . $name . ", " . $email . ", " . $location . ", " . 
        //     $description;

        $newOrg = new Organization(null, $name, $email, $location, $description);

        // echo "post object: " . $newOrg->get_id() . ", " . $newOrg->get_name() . ", " . $newOrg->get_email() . ", " . $newOrg->get_location() . ", " 
        //     . $newOrg->get_description();

        $result = add_organization($newOrg);
        if (!$result) {
            $showPopup = true;
        } else {
            echo '<head>
                    <meta HTTP-EQUIV="REFRESH" content="2; url=addOrganization.php">
                </head>
                <div id="popupMessage" class="pop-up">
                    ' . $name . ' has been added as an organization.
                </div>';
            // echo '<script>document.location = "login.php?registerSuccess";</script>';
            $title = $name . " has been added as an organization";
            $body = "A new organization has been added";
            system_message_all_admins($title, $body);
            // echo 'Organization added';
        }
    } else {
        require_once('addOrganizationForm.php');
    }
?>

<?php if ($showPopup): ?>
    <head>
        <meta HTTP-EQUIV="REFRESH" content="2; url=addOrganization.php">
    </head>
    <div id="popupMessage" class="pop-up">
        That organization is already added.
    </div>
<?php endif; ?>

</body>
</html>

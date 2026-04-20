<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    session_cache_expire(30);
    session_start();

    date_default_timezone_set("America/New_York");

    if (!isset($_SESSION['access_level']) || $_SESSION['access_level'] < 1) {
        if (isset($_SESSION['change-password'])) {
            header('Location: changePassword.php');
        } else {
            header('Location: login.php');
        }
        die();
    }

    include_once('database/dbUsers.php');
    include_once('domain/User.php');
    // Get date?
    if (isset($_SESSION['_id'])) {
        $user = retrieve_user($_SESSION['_id']);
    }
    $notRoot = $user->get_id() != 'vmsroot';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="./css/base.css" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="<?php include 'logo.php'; ?>">
    <title>UMW Alleviating Food Waste Volunteer Tracking | Dashboard</title>
    <?php require_once('universal.inc') ?>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Quicksand, sans-serif;
            background-color: #1F1F21;
        }

        h2 {
        	font-weight: normal;
            font-size: 30px;
        }

        .full-width-bar {
            width: 100%;
            background: #C9AB81;
            padding: 17px 5%;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }
        .full-width-bar-sub {
            width: 100%;
            background: var(--page-background-color);
            padding: 17px 5%;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }

        .content-box {
            flex: 1 1 280px; /* Adjusts width dynamically */
            max-width: 375px;
            padding: 10px 2px; /* Altered padding to make closer */
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            position: relative;
        }

        .content-box-sub {
            flex: 1 1 300px; /* Adjusts width dynamically */
            max-width: 470px;
            padding: 10px 10px; /* Altered padding to make closer */
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            position: relative;
        }

        .content-box img {
            width: 100%;
            height: auto;
            /* background: white; */
            border-radius: 5px;
            /* border-bottom-right-radius: 50px;
            border: 0.5px solid #828282; */
        }

        .content-box-sub img {
            width: 105%;
            height: auto;
            background: white;
            border-radius: 5px;
            border-bottom-right-radius: 50px;
            border: 1px solid #828282;
        }

        .small-text {
            position: absolute;
            top: 20px;
            left: 30px;
            font-size: 14px;
            font-weight: 700;
            color: #3A3A3A;
        }

        .large-text {
            position: absolute;
            top: 40px;
            left: 30px;
            font-size: 22px;
            font-weight: 700;
            color: black;
            max-width: 90%;
        }

        .large-text-sub {
            position: absolute;
            /*top: 120px;*/
            top: 60%;
            left: 10%;
            font-size: 22px;
            font-weight: 700;
            color: var(--page-font-color);
            max-width: 90%;
        }

        .graph-text {
            position: absolute;
            top: 75%;
            left: 10%;
            font-size: 14px;
            font-weight: 700;
            color: var(--page-font-color);
            max-width: 90%;
        }

        /* Navbar Container */
        .navbar {
            width: 100%;
            height: 95px;
            position: fixed;
            top: 0;
            left: 0;
            background-color: var(--page-background-color);
            box-shadow: 0px 2px 8px rgba(0, 0, 0, 0.25);
            display: flex;
            align-items: center;
            padding: 0 20px;
            z-index: 1000;
        }

        /* Left Section: Logo & Nav Links */
        .left-section {
            display: flex;
            align-items: center;
            gap: 30px; /* Space between logo and links */
        }

        /* Logo */
        .logo-container {
            background: #C9AB81;
            padding: 10px 20px;
            border-radius: 50px;
            box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.25) inset;
        }

        .logo-container img {
            width: 128px;
            height: 52px;
            display: block;
        }

        /* Navigation Links */
        .nav-links {
            display: flex;
            gap: 20px;
        }

        /* .nav-links div {
            font-size: 24px;
            font-weight: 700;
            color: black;
            cursor: pointer;
        } */

        /* Right Section: Date & Icon */
        .right-section {
            /* margin-left: auto; Pushes right section to the end */
            margin-right: 0px;
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .date-box {
            background: #C9AB81;
            padding: 10px 30px;
            border-radius: 50px;
            /* box-shadow: -4px 4px 4px rgba(0, 0, 0, 0.25) inset; */
            color: white;
            font-size: 24px;
            font-weight: 700;
            text-align: center;
        }

        .icon {
            width: 47px;
            height: 47px;
            /*background: #292D32;*/
            border-radius: 50%;
        }

        /* Button Control */
        .arrow-button {
            position: absolute;
            bottom: 30px;
            right: 30px;
            background: transparent;
            border: none;
            font-size: 20px;
            cursor: pointer;
            transition: transform 0.3s ease;

        }

        .arrow-button:hover {
            transform: translateX(5px); /* Moves the arrow slightly on hover */
        }
    .circle-arrow-button {
        position: absolute;
        bottom: 30px;
        right: 18px;
        display: flex;
        align-items: center;
        gap: 10px;
        background: transparent;
        border: none;
        font-size: 20px;
        font-family: Quicksand, sans-serif;
        font-weight: bold;
        color: var(--page-font-color);
        cursor: pointer;
        transition: transform 0.3s ease;
    }

    .circle {
        width: 30px;
        height: 30px;
        /*background-color:; /* Blue color */
        background-color: var(--page-font-color);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        transition: transform 0.3s ease;
    }

    .circle-arrow-button:hover {
        background-color:transparent !important;
    }

    .circle-arrow-button:hover .circle {
        transform: translateX(5px); /* Moves the circle slightly on hover */
    }
.colored-box {
    display: inline-block; /* Ensures it wraps tightly around the text */
    background-color: #C9AB81; /* Change to any color */
    color: white; /* Text color */
    padding: 1px 5px; /* Adds space inside the box */
    border-radius: 5px; /* Optional: Rounds the corners */
    font-weight: bold; /* Optional: Makes text bold */
}


        /* Footer */
        .footer {
            width: 100%;
            background: var(--accent-color);
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 30px 50px;
            flex-wrap: wrap;
        }

        /* Left Section */
        .footer-left {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .footer-logo {
            width: 150px; /* Adjust logo size */
            margin-bottom: 15px;
        }

        /* Social Media Icons */
        .social-icons {
            display: flex;
            gap: 15px;
        }

        .social-icons a {
            color: white;
            font-size: 20px;
            transition: color 0.3s ease;
        }

        .social-icons a:hover {
            color: #dcdcdc;
        }

        /* Right Section */
        .footer-right {
            display: flex;
            gap: 50px;
            flex-wrap: wrap;
            align-items: flex-start;
        }

        .footer-section {
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 10px;
            color: #C9AB81;
            font-family: Inter, sans-serif;
            font-size: 16px;
            font-weight: 500;
        }

        .footer-topic {
            font-size: 1.25rem;
            font-weight: bold;
        }

        .footer a {
            color: white;
            text-decoration: none;
            transition: background 0.2s ease, color 0.2s ease;
            padding: 5px 10px;
            border-radius: 5px;
        }

        /* Icon Overlay */
        .background-image {
            width: 100%;
            border-radius: 10px;
        }

        .icon-overlay {
            position: absolute;
            top: 40px; /* Adjust as needed */
            left: 50%;
            transform: translateX(-50%);
            background: var(--page-font-color); /* Optional background for better visibility */
            padding: 10px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .icon-overlay img {
            width: 40px; /* Adjust size as needed */
            height: 40px;
            opacity: 0.9;
        }

        .content-box-test:hover .icon-overlay img {
            transform: scale(1.1) rotate(5deg);
            transition: transform 0.5s ease, fill 0.5s ease;
        }





        .content-box-test {
            position: relative;
            background-color: var(--accent-color);   /* tan background */
            border-radius: 12px;
            padding: 20px;
            color: var(--page-font-color);                 /* default text color */
            flex: 1 1 280px;
            max-width: 375px;
            min-height: 250px;            /* keeps all boxes same height even without bg image */
            }


        /* .content-box-test .large-text-sub,
        .content-box-test .graph-text {
            color: black;
        } */


        .background-image {
        display: none;
        }


        /* .full-width-bar-sub{
            background-color: #1F1F21 !important;
        } */


        /* Responsive Design */
   </style>
<!--BEGIN TEST, UPLOAD AND NOTIFICATIONS CHANGED-->
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            document.querySelector(".extra-info").style.maxHeight = "0px"; // Ensure proper initialization
        });
        function toggleInfo(event) {
            event.stopPropagation(); // Prevents triggering the main button click
            let info = event.target.nextElementSibling;
            let isVisible = info.style.maxHeight !== "0px";
            info.style.maxHeight = isVisible ? "0px" : "100px";
            event.target.innerText = isVisible ? "↓" : "↑";
        }
    </script>
<!--END TEST-->
</head>

<!-- ONLY SUPER ADMIN WILL SEE THIS -->
<?php if ($_SESSION['access_level'] >= 2): ?>
<body>
<?php require_once('header.php');?>

    <!-- Dummy content to enable scrolling -->
    <div style="margin-top: 0px; padding: 30px 20px;">
        <h2 style="text-align: left;"><b>Welcome <?php echo $user->get_first_name() ?>!</b> Let's get started.</h2>
    </div>
    <div class="full-width-bar">
        <div style="width: 100%;">
            <a class="return-button" style="display:flex; width: fit-content; margin-left:auto; margin-right:auto; margin-bottom: 1rem;" href="addLog.php">Add Volunteer Log</a>
            <?php require_once("viewAllLogs.php") ?>
        </div>
    </div>
    <div style="width: 90%; /* Stops before page ends */
                border-bottom: 2px solid var(--main-color);
                margin: 2% auto; /* Adds vertical space and centers */">
    </div>
    
    <?php include_once("footer.php"); ?>

    <!-- Font Awesome for Icons -->
    <script src="https://kit.fontawesome.com/yourkit.js" crossorigin="anonymous"></script>


</body>
<?php endif ?>

<!-- ONLY VOLUNTEERS WILL SEE THIS -->
<?php if ($_SESSION['access_level'] < 2): ?>
<body>
<?php require 'header.php';?>
    <!-- Icon Container -->
    <div style="position: absolute; top: 110px; right: 30px; z-index: 999; display: flex; flex-direction: row; gap: 30px; align-items: center; text-align: center;"></div>

    <!-- Dummy content to enable scrolling -->
    <div style="margin-top: 0px; padding: 30px 20px;">
        <h2 style="text-align: left;"><b>Welcome <?php echo $user->get_first_name() ?>!</b> Let's get started.</h2>
    </div>

    <div class="full-width-bar">
        <div style="width: 100%;">
            <a class="return-button" style="display:flex; width: fit-content; margin-left:auto; margin-right:auto; margin-bottom: 1rem;" href="addlog.php">Add Volunteer Log</a>
            <?php require_once("viewAllLogs.php") ?>
        </div>
    </div>
    <div style="width: 90%; /* Stops before page ends */
                border-bottom: 2px solid var(--main-color);
                margin: 2% auto; /* Adds vertical space and centers */">
    </div>

    <?php include_once("footer.php"); ?>

    <!-- Font Awesome for Icons -->
    <script src="https://kit.fontawesome.com/yourkit.js" crossorigin="anonymous"></script>

</body>
<?php endif ?>
</html>

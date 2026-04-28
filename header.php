<!-- This looks really, really great!  -Thomas -->
<?php
date_default_timezone_set('America/New_York');
/*
 * Copyright 2013 by Allen Tucker.
 * This program is part of RMHP-Homebase, which is free software.  It comes with
 * absolutely no warranty. You can redistribute and/or modify it under the terms
 * of the GNU General Public License as published by the Free Software Foundation
 * (see <http://www.gnu.org/licenses/ for more information).
 *
if (date("H:i:s") > "18:19:59") {
	require_once 'database/dbShifts.php';
	auto_checkout_missing_shifts();
}
 */

// check if we are in locked mode, if so,
// user cannot access anything else without
// logging back in
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;700&family=Quicksand:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="./css/base.css" rel="stylesheet">
    <style>
<?php if (empty($tailwind_mode)): ?>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
<?php endif; ?>
        body {
            font-family: Nunito, Quicksand, sans-serif;
            padding-top: 96px;
            font-size: 14pt;
        }

/*BEGIN STYLE TEST*/
         .extra-info {
            max-height: 0px;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
            font-size: 14px;
            color: #444;
            margin-top: 5px;
        }
       .content-box-test{
            flex: 1 1 370px; /* Adjusts width dynamically */
            max-width: 470px;
            padding: 10px 10px; /* Altered padding to make closer */
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            position: relative;
            cursor: pointer;
            /* border: 0.1px solid var(--page-font-color); */
            transition: border 0.3s;
            border-radius: 10px;
            /* border-bottom-right-radius: 50px; */
        }
         .content-box-test:hover,
         .content-box-test:focus-visible {
            border: 4px solid var(--page-font-color);
        }
/*END STYLE TEST*/

        .full-width-bar {
            width: 100%;
            background: var(--page-background-color);
            padding: 17px 5%;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }
        .full-width-bar-sub {
            width: 100%;
            background: white;
            padding: 17px 5%;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }

        .content-box {
            background-color: var(--accent-color);
            flex: 1 1 280px; /* Adjusts width dynamically */
            max-width: 375px;
            padding: 10px 2px; /* Altered padding to make closer */
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            position: relative;
            height: 260px;
            border-radius: 12px;
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
            /* border-bottom-right-radius: 50px; */
            /* border: 0.5px solid #828282; */
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
            color: var(--page-font-color);
        }

        .large-text {
            position: absolute;
            top: 40px;
            left: 30px;
            font-size: 22px;
            font-weight: 700;
            color: black;
            max-width: 90%;
            color: var(--page-font-color);
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
            margin-bottom: 80px;
        }

        /* Navbar Container */
        .navbar {
	    gap: 10px;
            width: 100%;
            height: 100px;
            position: fixed;
            top: 0;
            left: 0;
            background: var(--page-background-color);
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
            gap: 20px; /* Space between logo and links */
        }

        /* Logo */
        .logo-container {
            background: var(--page-background-color);
            padding: 10px 20px;
            border-radius: 50px;
            box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.25) inset;
        }

        .logo-container img {
            width: 60px;
            height: 60px;
            display: block;
        }

        /* Navigation Links */
        .nav-links {
            display: flex;
            gap: 20px;
        }

        /* .nav-links div, .nav-item a {
            font-size: 24px;
            font-weight: 700;
            color: var(--page-font-color);
            cursor: pointer;
        } */

        /* Right Section: Date & Icon */
        .right-section {
            margin-left: auto; /* Pushes right section to the end */
            display: flex;
            align-items: center;
            gap: 20px;
	    }

        /* Dropdown Control */
        .nav-item, .nav-item a {
            position: relative;
            cursor: pointer;
            padding: 0px;
            transition: color 0.3s, outline 0.3s;
            font-size: 30px;
            color: var(--page-font-color);
            font-weight: 700;
        }

        .dropdown {
            display: none;
            position: absolute;
            top: 150%;
            left: -10%;
            background-color: var(--page-background-color);
            border: 1px solid var(--page-font-color);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            min-width: 150px;
            padding: 10px;
            color: var(--page-font-color);
        }
        .dropdown div {
            padding: 8px;
            white-space: nowrap;
            transition: background 0.3s;
        }
        .dropdown div:hover,
        .dropdown div:focus-visible {
            background: rgba(0, 0, 0, 0.1);
        }

        .nav-item:hover, .nav-item:focus-visible, .nav-item.active, .nav-item a.header-link:hover {
            color: var(--accent-color);
            /* outline: 1px solid var(--accent-color); */
            outline-offset: 7px;
        }

        .date-box {
            background: var(--accent-color);
            padding: 10px 30px;
            border-radius: 50px;
            /* box-shadow: -4px 4px 4px rgba(0, 0, 0, 0.25) inset; */
            color: var(--page-font-color);
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
.nav-buttons {
    position: absolute;
    bottom: 10%; /* Adjust as needed */
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: 15px;
    justify-content: center;
    width: 100%;
}

/* Button Styling */
.nav-button {
    background: var(--page-font-color);
    border: none;
    color: white;
    font-size: 20px;
    font-family: 'Quicksand', sans-serif;
    font-weight: 600;
    border-radius: 20px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 10px;
    transition: all 0.4s ease-in-out;
    backdrop-filter: blur(8px);
    padding: 6px 8px;
    padding-top: 10px;
    width: 55px; /* Initially a circle */
    overflow: hidden;
    white-space: nowrap;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

/* Expand button on hover */
.nav-button:hover,
.nav-button:focus-visible {
    width: 160px;
    padding: 6px 8px;
    padding-top: 10px
}

.nav-button .text {
    opacity: 0;
    transform: translateX(-10px);
    transition: opacity 0.3s ease-in-out, transform 0.3s ease-in-out;
}

.nav-button:hover .text,
.nav-button:focus .text {
    opacity: 1;
    transform: translateX(0);
}

.nav-button .arrow {
    display: inline-block;
    transition: transform 0.3s ease;
    filter:invert(1);
}

.nav-button:hover .arrow,
.nav-button:focus-visible .arrow {
    transform: translateX(5px);
}
       /* Button Control */
        .arrow-button {
            position: absolute;
            bottom: 24px;
            right: 16px;
            background: transparent;
            border: none;
            font-size: 23px;
            font-weight: bold;
            color: var(--page-font-color);
            cursor: pointer;
            transition: transform 0.3s ease;
            padding: 0;
        }

        .arrow-button:hover,
        .arrow-button:focus-visible {
            transform: translateX(5px); /* Moves the arrow slightly on hover */
            background: transparent;
        }

        /* Footer */
        .footer {
            width: 100%;
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

        .social-icons a:hover,
        .social-icons a:focus-visible {
            color: rgb(31,31,33);
        }

        /* Right Section */
        .footer-right {
            display: flex;
            gap: 50px;
            flex-wrap: wrap;
        }

        .footer-section {
            display: flex;
            flex-direction: column;
            gap: 10px;
            color: var(--main-color);
            font-family: Inter, sans-serif;
            font-size: 16px;
            font-weight: 500;
        }

        .footer-topic {
            font-weight: bold;
        }

        .footer a {
            color: var(--page-font-color);
            text-decoration: underline;
            font-weight: 550;
            transition: background 0.2s ease, color 0.2s ease;
            padding: 5px 10px;
            border-radius: 5px;
        }

        .footer a:hover,
        .footer a:focus-visible {
            color: var(--main-color-hover);
        }

        /* Icon Overlay */
        .background-image {
            width: 100%;
            border-radius: 17px;
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
            filter: invert(1);
        }

        .nav-item img {
            border-radius: 15px;
            transition: filter 0.05s, background-color 0.05s;
        }

        .nav-item:hover img, .nav-item:focus-visible img, .nav-item.active img {
            filter: invert(68%) sepia(66%) saturate(345%) hue-rotate(43deg) brightness(90%) contrast(95%);
        }

        .icon .dropdown{
            top: 130%;
            left: -485%;
        }

        .in-nav {
            display: flex;
            align-items: center;
            gap: 8px;
        }
	.in-nav span {
	    font-size:24px;
	}

	.in-nav img {
            width: 40px;
            height: 40px;
            border-radius: 0px;
            /* border-bottom-right-radius: 20px; */
            filter: brightness(0) saturate(100%) invert(14%) sepia(89%) saturate(465%) hue-rotate(167deg) brightness(103%) contrast(84%) !important;
        }

/* for calendar */
    .icon-butt svg {
        transition: transform 0.2s ease, fill 0.2s ease;
        cursor: pointer;
    }

    .icon-butt:hover svg,
    .icon-butt:focus-visible svg {
        transform: scale(1.1) rotate(5deg); /* Slight enlarge & tilt effect */
        fill: var(--accent-color); /* Changes to a blue shade */
    }

    .font-change {
	font-size: 30px;
	font-family: Quicksand;
    color: white;
    }

        /* Accessibility menu styles */
        .accessibility-btn {
            position: fixed;
            bottom: 18px;
            right: 18px;
            width: 70px;
            height: 70px;
            border-radius: 80px !important;
            background: var(--main-color);
            border: 3px solid var(--main-color);
            cursor: pointer !important;
            z-index: 2000;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 4px !important;
        }
        .accessibility-btn img {
            width: 70px;
            height: 70px;
            object-fit: contain;
            filter: invert(1);
        }

        /* Modal */
        .accessibility-modal-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 2100;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .accessibility-modal {
            background: #1f1f21;
            color: white;
            max-width: 520px;
            width: 100%;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.6);
        }
        .accessibility-modal h3 { margin-bottom: 8px; }
        .modal-header { display:flex; justify-content:space-between; align-items:center; }
        .nav-link { color: white; text-decoration: none; }
        .dropdown-link { color: inherit; text-decoration: none; display:block; }
        .icon-img {filter: brightness(0) saturate(100%) invert(14%) sepia(89%) saturate(465%) hue-rotate(167deg) brightness(103%) contrast(84%);}
        .modal-close { background:transparent;border:none;color:white;font-size:30px;cursor:pointer; }
        .modal-desc { color: rgba(255,255,255,0.7); }
        .accessibility-row { display:flex; gap:12px; align-items:center; margin:10px 0; }
        .accessibility-row label { min-width: 120px; font-weight:600; }
        .accessibility-modal select, .accessibility-modal input[type="radio"]{ font-size:16px; }
        .accessibility-actions { display:flex; justify-content:flex-end; gap:8px; margin-top:16px; }
        .accessibility-actions button { padding:8px 12px; border-radius:8px; cursor:pointer; border:none; }
        .accessibility-actions .save { background:var(--wv-accent-color); color:var(--wv-accent-foreground); }
        .accessibility-actions .reset { background:transparent; color:#fff; border:1px solid rgba(255,255,255,0.12); }

        /* Bigger base font sizes applied by class toggles via JS */



        /* Responsive Design */
	@media (max-width: 0px) {
	   .content-box-test {
		flex: 1 1 300px;
	    }
	}

        @media (max-width: 900px) {
           .footer {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }
            .footer-right {
                flex-direction: column;
                align-items: center;
                gap: 30px;
                margin-top: 20px;
            }

        }
    </style>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll(".nav-item").forEach(item => {
                item.addEventListener("click", function(event) {
                    event.stopPropagation();
                    document.querySelectorAll(".nav-item").forEach(nav => {
                        if (nav !== item) {
                            nav.classList.remove("active");
                            if(nav.querySelector(".dropdown") !== null) {
                                nav.querySelector(".dropdown").style.display = "none";
                            }
                        }
                    });
                    this.classList.toggle("active");
                    let dropdown = this.querySelector(".dropdown");
                    if (dropdown) {
                        dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
                    }
                });
            });
            document.addEventListener("click", function() {
                document.querySelectorAll(".nav-item").forEach(nav => {
                    nav.classList.remove("active");
                    if(nav.querySelector(".dropdown") !== null) {
                        nav.querySelector(".dropdown").style.display = "none";
                    }
                });
            });
        });
    </script>
</head>

<header>

    <?php
    //Log-in security
    //If they aren't logged in, display our log-in form.
    $showing_login = false;
    ob_start();
    include('logo.php');
    $logo = ob_get_clean();
    if (!isset($_SESSION['logged_in'])) {
		echo('<div class="navbar">
        <!-- Left Section: Logo & Nav Links -->
        <div class="left-section">
            <div class="logo-container">
                <a href="index.php"><img src="' . $logo . '" alt="Logo"></a>
            </div>
            <div class="nav-item">
                <a class="header-link" href="index.php">Home</a>
            </div>
        </div>

        <!-- Right Section: Date & Icon -->
        <div class="right-section">
            <div class="nav-links">
                <div class="nav-item">
                    <div class="icon">
                        <img src="images/usaicon.png" alt="User Icon" class="icon-img in-nav-img">
                        <div class="dropdown">
                            <a href="signup.php" class="dropdown-link"><div>Create Account</div></a>
                            <a href="login.php" class="dropdown-link"><div>Log in</div></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>');

    } else if ($_SESSION['logged_in']) {

        /*         * Set our permission array.
         * anything a guest can do, a volunteer and manager can also do
         * anything a volunteer can do, a manager can do.
         *
         * If a page is not specified in the permission array, anyone logged into the system
         * can view it. If someone logged into the system attempts to access a page above their
         * permission level, they will be sent back to the home page.
         */
        //pages guests are allowed to view
        // LOWERCASE
        /*
        *  For A guest can log in, go to WVF's home page,
        * -Evan
        */

        // FOODDB auth =============================
        // 0 = you dont need to be logged in
        // 1 = you must be logged in, instructor level minimum
        // 2 = must be an instructor

        // ACTUAL values for permissions are: 1 for students, 3 for instructors. not logged-in users don't have access level set / 0

        //YOU MUST PULL VALUES IN ALL LOWECASE
        $permission_array['index.php'] = 0;
        $permission_array['volunteerregister.php'] = 0;

        $permission_array['log.php'] = 1;
        $permission_array['viewalllogs.php'] = 1;
        $permission_array['viewallorgs.php'] = 1;
        $permission_array['editlog.php'] = 1;
        $permission_array['deletelog.php'] = 1;
        $permission_array['addlog.php'] = 1;
        $permission_array['volunteermanagement.php'] = 1;
        $permission_array['viewprofile.php'] = 1;
        $permission_array['addorganization.php'] = 1;
        $permission_array['editorganizationsearch.php'] = 1;
        $permission_array['editorganization.php'] = 1;
        $permission_array['editprofile.php'] = 1;
        $permission_array['changepassword.php'] = 1;
        $permission_array['eventsuccess.php'] = 1;
        $permission_array['eventfailure.php'] = 1;
        $permission_array['organizationmanagement.php'] = 1;
        $permission_array['organization.php'] = 1;
        $permission_array['viewimpactsummary.php'] = 1;
        $permission_array['howto.php'] = 1;

        $permission_array['personsearch.php'] = 3;
        $permission_array['deleteusersearch.php'] = 3;
        $permission_array['modifyuserrole.php'] = 3;
        $permission_array['deleteuser.php'] = 3;
        $permission_array['deleteorganizationsearch.php'] = 3;
        $permission_array['deleteorganization.php'] = 3;
        $permission_array['analyticsdashboard.php'] = 3;
        $permission_array['impactbystudent.php'] = 3;
        $permission_array['impactbyorg.php'] = 3;
        $permission_array['generatereport.php'] = 3;
        $permission_array['archivalsearch.php'] = 3;
        $permission_array['archivalmanagement.php'] = 3;
        $permission_array['monthlyimpact.php'] = 3;
        $permission_array['adminsettings.php'] = 3;
        
        $permission_array['viewallusers.php'] = 3;

        //Check if they're at a valid page for their access level.
        $current_page = strtolower(substr($_SERVER['PHP_SELF'], strrpos($_SERVER['PHP_SELF'], '/') + 1));
        $current_page = substr($current_page, strpos($current_page,"/"));

        if($permission_array[$current_page] > $_SESSION['access_level']){
            //in this case, the user doesn't have permission to view this page.
            //we redirect them to the index page.
            echo "<script type=\"text/javascript\">window.location = \"index.php\";</script>";
            //note: if javascript is disabled for a user's browser, it would still show the page.
            //so we die().
            die();
        }
        //This line gives us the path to the html pages in question, useful if the server isn't installed @ root.
        $path = strrev(substr(strrev($_SERVER['SCRIPT_NAME']), strpos(strrev($_SERVER['SCRIPT_NAME']), '/')));
		$venues = array("portland"=>"RMH Portland"); // Is this used anywhere? Do we need it? -Blue

        //they're logged in and session variables are set.
	//
	// SUPER ADMIN ONLY HEADER
        if ($_SESSION['access_level'] >= 2) {
        ob_start();
        include('logo.php');
        $logo = ob_get_clean();
		echo('<div class="navbar">
        <!-- Left Section: Logo & Nav Links -->
        <div class="left-section">
            <div class="logo-container">
                <a href="index.php"><img src="' . $logo . '" alt="Logo"></a>
            </div>
            <div class="nav-links">
                <div class="nav-item">
                    <a class="header-link" href="index.php">Home</a>
                </div>
                <div class="nav-item">Management
                    <div class="dropdown">
                        <a href="volunteerManagement.php" style="text-decoration: none;">
                        <div class="in-nav">
                            <span>Manage Volunteers</span>
                        </div>
                        </a>
                        <a href="organizationManagement.php" style="text-decoration: none;">
                        <div class="in-nav">
                            <span>Manage Organizations</span>
                        </div>
                        </a>
                        <a href="archivalManagement.php" style="text-decoration: none;">
                        <div class="in-nav">
                            <span>Manage Archives</span>
                        </div>
                        </a>
                    </div>
                </div>
                <div class="nav-item">Analytics
                    <div class="dropdown">
                        <a href="analyticsDashboard.php" style="text-decoration: none;">
                        <div class="in-nav">
                            <span>Analytics Dashboard</span>
                        </div>
                        </a>
                        <a href="generateReport.php" style="text-decoration: none;">
                        <div class="in-nav">
                            <span>Export to CSV/XLS</span>
                        </div>
                        </a>
                    </div>
                </div>
                <div class="nav-item">
                    <a class="header-link" href="howto.php">Instructions</a>
                </div>
            </div>
        </div>
        <!-- Right Section: Date & Icon -->
        <div class="right-section">
            <div class="nav-links">
                <div class="nav-item">
                    <div class="icon">
                        <img src="images/usaicon.png" alt="User Icon" class="icon-img in-nav-img">
                        <div class="dropdown">
                            <a href="changePassword.php" class="dropdown-link"><div>Change Password</div></a>
                            <a href="adminSettings.php" class="dropdown-link"><div>Admin Settings</div></a>
                            <a href="logout.php" class="dropdown-link"><div>Log Out</div></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>');
	}

        // VOLUNTEER ONLY HEADER
        if ($_SESSION['access_level'] <= 1) {
        ob_start();
        include('logo.php');
        $logo = ob_get_clean();
		echo('<div class="navbar">
        <!-- Left Section: Logo & Nav Links -->
        <div class="left-section">
            <div class="logo-container">
                <a href="index.php"><img src="' . $logo . '" alt="Logo"></a>
            </div>
            <div class="nav-links">
                <div class="nav-item">
                    <a class="header-link" href="index.php">Home</a>
                </div>
                <div class="nav-item">
                    <a class="header-link" href="viewImpactSummary.php">Impact Summary</a>
                </div>
<div class="nav-item">Organizations
    <div class="dropdown">
        <a href="addOrganization.php" style="text-decoration: none;">
        <div class="in-nav">
            <span>Add Organization</span>
        </div>
        </a>
        <a href="editOrganizationSearch.php" style="text-decoration: none;">
        <div class="in-nav">
            <span>Edit Organizations</span>
        </div>
        </a>
        <a href="viewAllOrgs.php" style="text-decoration: none;">
        <div class="in-nav">
            <span>View All Organizations</span>
        </div>
        </a>
    </div>
</div>
                <div class="nav-item">
                    <a class="header-link" href="howto.php">Instructions</a>
                </div>
            </div>
        </div>
        <!-- Right Section: Date & Icon -->
        <div class="right-section">
            <div class="nav-links">
                <div class="nav-item" style="outline:none;">
                    <div class="icon">
                        <img src="images/usaicon.png" alt="User Icon" class="icon-img in-nav-img">
                        <div class="dropdown">
                            <a href="viewProfile.php" style="text-decoration: none;"><div>View Profile</div></a>
                            <a href="editProfile.php" style="text-decoration: none;"><div>Edit Profile</div></a>
                            <a href="changePassword.php" style="text-decoration: none;"><div>Change Password</div></a>
                            <a href="logout.php" style="text-decoration: none;"><div>Log Out</div></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>');
        }
    }
    ?>
<script>
  function updateDateAndCheckBoxes() {
    const now = new Date();
    const width = window.innerWidth;

    // Format the date based on width
    let formatted = "";
    if (width > 1650) {
      formatted = "Today is " + now.toLocaleDateString("en-US", {
        weekday: "long",
        year: "numeric",
        month: "long",
        day: "numeric"
      });
    } else if (width >= 1450) {
      formatted = now.toLocaleDateString("en-US", {
        weekday: "long",
        year: "numeric",
        month: "long",
        day: "numeric"
      });
    } else {
      formatted = now.toLocaleDateString("en-US"); // e.g., 04/17/2025
    }

    // Update right-section date boxes
    document.querySelectorAll(".right-section .date-box").forEach(el => {
      if (width < 1130) {
        el.style.display = "none";
      } else {
        el.style.display = "";
        el.textContent = formatted;
      }
    });

    // Update left-section date boxes (Check In / Out or icon)
document.querySelectorAll(".left-section .date-box").forEach(el => {
  if (width < 750) {
    el.style.display = "none";
  } else {
    el.style.display = "";
    el.textContent = width < 1130 ? "🔁" : "Check In/Out";
  }
});

document.querySelectorAll(".icon-butt").forEach(el => {
  if (width < 800) {
    el.style.display = "none";
  } else {
    el.style.display = "";
  }
});




  }

  // Run on load and resize
  window.addEventListener("resize", updateDateAndCheckBoxes);
  window.addEventListener("load", updateDateAndCheckBoxes);
</script>
<!-- Accessibility Button + Modal -->
<button class="accessibility-btn" id="accessibilityBtn" aria-haspopup="dialog" aria-controls="accessibilityModal" title="Accessibility settings">
    <img src="images/accessibility-menu.png" alt="Accessibility Menu">
</button>

<div class="accessibility-modal-backdrop" id="accessibilityBackdrop" role="dialog" aria-modal="true" aria-hidden="true">
    <div class="accessibility-modal" id="accessibilityModal">
        <div class="modal-header">
            <h3>Accessibility Settings</h3>
            <button id="accessibilityClose" class="modal-close" style="max-width: 22%;">&times;</button>
        </div>
        <p class="modal-desc">Adjust font size, font style, and color scheme. Settings persist across pages and visits.</p>

        <div class="accessibility-row">
            <label for="acc-font-size">Font size</label>
            <div style="display:flex; align-items:center; gap:8px;">
                <input id="acc-font-size" type="range" min="12" max="24" step="1" value="14">
                <span id="acc-font-size-value">14pt</span>
            </div>
        </div>

        <div class="accessibility-row">
            <label for="acc-font-family">Font style</label>
            <select id="acc-font-family">
                <option value="nunito">Nunito (default)</option>
                <option value="quicksand">Quicksand</option>
                <option value="comic">Comic Sans</option>
                <option value="opendyslexic">OpenDyslexic</option>
                <option value="times">Times New Roman</option>
            </select>
        </div>

        <!-- Color scheme removed; keeping font controls only -->

        <div class="accessibility-actions">
            <button class="reset" id="accReset">Reset</button>
            <button class="save" id="accSave">Save</button>
        </div>
    </div>
</div>

<script>
    (function(){
        const KEY = 'wv_accessibility_settings';
        const defaults = { fontSize: 14, fontFamily: 'nunito' };

        function getSettings(){
            try{
                const raw = localStorage.getItem(KEY);
                return raw ? JSON.parse(raw) : Object.assign({}, defaults);
            }catch(e){ return Object.assign({}, defaults); }
        }

        function saveSettings(s){
            try{ localStorage.setItem(KEY, JSON.stringify(s)); }catch(e){}
        }

        function applySettings(s){
            // font size in points
            var size = Number(s.fontSize) || defaults.fontSize;
            if(size < 12) size = 12; if(size > 24) size = 24;
            document.documentElement.style.fontSize = size + 'pt';
            // update visible slider value if present
            var sizeDisplay = document.getElementById('acc-font-size-value'); if(sizeDisplay) sizeDisplay.textContent = size + 'pt';

            // font family mapping
            if(s.fontFamily === 'nunito'){
                document.body.style.fontFamily = 'Nunito, Quicksand, sans-serif';
            } else if (s.fontFamily === 'quicksand'){
                document.body.style.fontFamily = 'Quicksand, sans-serif';
            } else if (s.fontFamily === 'comic'){
                document.body.style.fontFamily = '"Comic Sans MS", "Comic Sans", cursive';
            } else if (s.fontFamily === 'opendyslexic'){
                document.body.style.fontFamily = 'OpenDyslexic, "Arial", sans-serif';
            } else if (s.fontFamily === 'times'){
                document.body.style.fontFamily = '"Times New Roman", Times, serif';
            }

            // color scheme support removed; icons keep their default CSS filters
        }

        // Initialize UI values from settings
        function populateUI(s){
            const size = document.getElementById('acc-font-size');
            const sizeVal = document.getElementById('acc-font-size-value');
            const ff = document.getElementById('acc-font-family');
            if(size) size.value = (s.fontSize !== undefined ? s.fontSize : defaults.fontSize);
            if(sizeVal) sizeVal.textContent = (s.fontSize !== undefined ? s.fontSize : defaults.fontSize) + 'pt';
            if(ff) ff.value = s.fontFamily || defaults.fontFamily;
        }

        // DOM elements
        const btn = document.getElementById('accessibilityBtn');
        const backdrop = document.getElementById('accessibilityBackdrop');
        const closeBtn = document.getElementById('accessibilityClose');
        const saveBtn = document.getElementById('accSave');
        const resetBtn = document.getElementById('accReset');

        // open/close helpers
        function openModal(){ backdrop.style.display = 'flex'; backdrop.setAttribute('aria-hidden','false'); document.getElementById('acc-font-size').focus(); }
        function closeModal(){ backdrop.style.display = 'none'; backdrop.setAttribute('aria-hidden','true'); btn.focus(); }

        btn.addEventListener('click', function(e){
            e.stopPropagation();
            const s = getSettings();
            populateUI(s);
            openModal();
        });
        closeBtn.addEventListener('click', closeModal);
        backdrop.addEventListener('click', function(e){ if(e.target === backdrop) closeModal(); });

        saveBtn.addEventListener('click', function(){
            const s = {
                fontSize: Number(document.getElementById('acc-font-size').value),
                fontFamily: document.getElementById('acc-font-family').value
            };
            applySettings(s);
            saveSettings(s);
            closeModal();
        });

        // live update when moving slider
        const slider = document.getElementById('acc-font-size');
        if(slider){ slider.addEventListener('input', function(){ document.getElementById('acc-font-size-value').textContent = this.value + 'pt'; }); }

        resetBtn.addEventListener('click', function(){
            localStorage.removeItem(KEY);
            const s = Object.assign({}, defaults);
            applySettings(s);
            populateUI(s);
        });

        // apply on load
        document.addEventListener('DOMContentLoaded', function(){
            const s = getSettings();
            applySettings(s);
        });
    })();
</script>
</header>

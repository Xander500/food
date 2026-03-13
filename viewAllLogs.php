<?php
    // Template for new VMS pages. Base your new page on this one

    // Make session information accessible, allowing us to associate
    // data with the logged-in user.
    session_cache_expire(30);
    session_start();

    $loggedIn = false;
    $accessLevel = 0;
    $userID = null;
    if (isset($_SESSION['_id'])) {
        $loggedIn = true;
        // 0 = not logged in, 1 = standard user, 2 = manager (Admin), 3 super admin (TBI)
        $accessLevel = $_SESSION['access_level'];
        $userID = $_SESSION['_id'];
    }  
        // Require admin privileges
    if ($accessLevel < 2) {
        header('Location: login.php');
        //echo 'bad access level';
        die();
    }
    include 'database/dbVolunteerActivity.php';
    include 'database/dbUsers.php';
    include 'database/dbOrganizations.php';


    //check for sorting 
    //make sure no sql injections
    $sortby = null; $order = null;
    //echo $_GET['sortby'] . " " . $_GET['order'] . "\n";
    if (isset($_GET['sortby'])) {
        switch ($_GET['sortby']) {
            case 'student': $sortby = 'last_name'; $order = 'asc'; break;
            case 'date': $sortby = 'date'; $order = 'desc'; break;
            case 'organization': $sortby = 'organization_name'; $order = 'asc'; break;
            case 'hours': $sortby = 'hours'; $order = 'asc'; break;
            case 'location': $sortby = 'location'; $order = 'asc'; break;
            case 'poundsoffood': $sortby = 'poundsOfFood'; $order = 'asc'; break;
            case 'description': $sortby = 'description'; $order = 'asc'; break;
        }
    }
        //check if order is desc
    if (isset($_GET['order'])) {
        switch ($_GET['order']) {
            case 'desc': $order = 'desc'; break;
            case 'asc': $order = 'asc'; break;
        }
    }

    $logs = get_all_volunteer_activities_custom_sort_pagination($sortby, $order, 10, 0);

    
    //include 'domain/Event.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <?php require_once('universal.inc') ?>
        <link rel="stylesheet" href="css/event.css">
        <script src="js/messages.js"></script>
        <title>View All Volunteer Activity</title>
    </head>
    <body>
        <?php require_once('header.php') ?>
        <?php require_once('database/dbVolunteerActivity.php');?>
        <?php require_once('database/dbPersons.php');?>
        <h1>Volunteer Activity</h1>
        <main class="general">
            <?php

                if(isset($_SESSION['user_id']) && $_SESSION['user_id'] != 'guest') {
                    $user = retrieve_person($userID);
                }

                if (sizeof($logs) > 0): ?>
                <div class="table-wrapper">
                    <h2>View All Volunteer Activity</h2>
                    <table class="general">
                        <thead>
                            <tr>
                                <th></th>
                                <th style="width:1px">Student</th>
                                <th style="width:1px">Date</th>
                                <th style="width:1px">Organization</th>
                                <th style="width:1px">Hours</th>
                                <th style="width:1px">Location</th>
                                <th style="width:1px">Food Rescued (lbs)</th>
                                <th style="width:1px">Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                #require_once('database/dbPersons.php');
                                #require_once('include/output.php');
                                #$id_to_name_hash = [];
                                foreach ($logs as $log) {
                                    $logID = $log->getID();
                                    $studentID = $log->getVolunteerID();
                                    $date = $log->getDate();
                                    $organizationID = $log->getOrganizationID();
                                    $hours = $log->getHours();
                                    $location = $log->getLocation();
                                    $pounds = $log->getPoundsOfFood();
                                    $description = $log->getDescription();
                                    
                                    $studentName = get_user_full_name_from_id($studentID);
                                    $organizationName = get_organization_name_from_id($organizationID);

                                    echo "
                                    <tr data-event-id='$logID'>
                                        <td><a href='log.php?id=$logID' class='event-link'>👁</a></td>
                                        <td>$studentName</td>
                                        <td>$date</td>
                                        <td>$organizationName</td>
                                        <td>$hours</td>
                                        <td>$location</td>
                                        <td>$pounds</td>
                                        <td>$description</td
                                    </tr>";
                                }
                            ?>
                        </tbody>
                    </table>
                </div>

                <ul class="pagination">

                    <li class="pagination_li">
                        <a href="#" class="pagination_link">&lt;</a>
                    </li>
                    <li class="pagination_li">
                        <a href="#" class="pagination_link">1</a>
                    </li>
                    <li class="pagination_li">
                        <a href="sdfsd" class="pagination_link pagination_link--active">2</a>
                    </li>
                    <li class="pagination_li">
                        <a href="#" class="pagination_link">&gt;</a>
                    </li>
                </ul>
                <?php else: ?>
                <p class="no-events standout">There are currently no logs available to view.<a class="button add" href="addEvent.php">Create a New Event</a> </p>
            <?php endif ?>
            <a class="button cancel" href="index.php">Return to Dashboard</a>
        </main>
    

    </body>
</html>
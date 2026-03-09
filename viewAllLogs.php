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
    include 'database/dbVolunteerActivity.php';
    include 'database/dbUsers.php';
    include 'database/dbOrganizations.php';
    
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
                $logs = get_all_volunteer_activities_sorted_by_date();

                if(isset($_SESSION['user_id']) && $_SESSION['user_id'] != 'guest') {
                    $user = retrieve_person($userID);
                }

                if (sizeof($logs) > 0): ?>
                <div class="table-wrapper">
                    <h2>View All Volunteer Activity</h2>
                    <table class="general">
                        <thead>
                            <tr>
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



                <?php else: ?>
                <p class="no-events standout">There are currently no logs available to view.<a class="button add" href="addEvent.php">Create a New Event</a> </p>
            <?php endif ?>
            <a class="button cancel" href="index.php">Return to Dashboard</a>
        </main>
    

    </body>
</html>
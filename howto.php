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
    //require being a logged in user
    if ($accessLevel < 1) {
        header('Location: login.php');
        //echo 'bad access level';
        die();
    }

    //control who sees which sections
    //0 means everone,1 means student only, 3 means instructor only.
    $sections = [
        "add_vol_log" => 0,
        "view_all_vol_logs" => 0,
        "search_vol_logs" => 0,
        "edit_vol_log" => 0,
        "delete_vol_log" => 0,
        "view_own_vol_logs" => 1,
        "viewimpact_summary" => 1,
        "add_org" => 0,
        "edit_org" => 0,
        "delete_orgs" => 3,
        "manage_user_roles" => 3,
        "export_data" => 3,
        "view_analytics_dashboard" => 3,
    ];
?>
<!DOCTYPE html>
<html>
    <head>
        <?php require_once('universal.inc') ?>
        <link rel="stylesheet" href="css/base.css">
        <title>UMW Alleviating Food Waste Volunteer Tracking | Instructions</title>




    </head>
    <body class="ht-bg">


        <?php require_once('header.php') ?>

        <!-- Hero Banner -->
        <div class="hero-bg"></div>


        <main class="general howto-page">
            
            <div class="sidebar-wrapper">
                <div class="sidebar">
                    <div class="sidebar-item">
                        <h1>Instructions</h1>
                        <ol>
                            <li><a href="#add-activity-log">Add a Volunteer Activity Log</a></li>
                            <li><a href="#view-logs">View All Volunteer Activity Logs</a></li>
                            <li><a href="#search-logs">Search Volunteer Activity Logs</a></li>
                            <li><a href="#edit-log">Edit a Volunteer Activity Log</a></li>
                            <li><a href="#delete-log">Delete a Volunteer Activity Log</a></li>
                            <li><a href="#view-own-logs">View My Volunteer Activity Logs</a></li>
                            <li><a href="#view-impact-summary">View Your Personal Impact Summary</a></li>
                            <li><a href="#add-org">Add Organization</a></li>
                            <li><a href="#edit-org">Edit Organization</a></li>
                            <li><a href="#delete-orgs">Delete Organizations</a></li>
                            <li><a href="#manage-user-roles">Manage User Roles</a></li>
                            <li><a href="#export-data">Export Data</a></li>
                            <li><a href="#view-analytics">View Analytics Dashboard</a></li>
                        </ol>
                    </div>
                </div>
            </div>

            <div class="main-content-box">
                <section id="add-activity-log">
                    <h3>Add a Volunteer Activity Log</h3>
                    <ul>
                        <li>To add records of your volunteer activities with a non-profit or volunteer organization on a particular date, navigate to the <a href="link goes here">homepage</a> and select the <a href="link goes here">"Add Log" button</a> at the top of the page.</li>
                        <li>Enter the details about your activity into the form and click "Create Activity."</li>
                        <li>You are required to provide information about the date, the duration (number of hours), and the organization.</li>
                        <li>You may additionally provide information about the location, the pounds of food rescued, and a description of the activity.</li>
                    </ul>
                </section>
                <section id="view-logs">
                    <h3>View All Volunteer Activity Logs</h3>
                    <ul>
                        <li>To view volunteer activity logs, navigate to the <a href="link goes here">log display table</a> on the <a href="link goes here">homepage</a>.</li>
                        <li>Scroll down until you see the section titled "View All Volunteer Activity."</li>
                        <li>If there are numerous logs, the table will display only one page at a time. Page navigation links are found at the lower right corner of the table. Click the numbered buttons to view that page or the arrows to navigate pages.</li>
                        <li>By default, the logs are sorted by date. Click any column header link to sort by that field. An arrow will appear next to the selected header, indicating ascending or descending order. Click the header again to reverse the order.</li>
                        <li>To view the details of a single log, click the "👁" icon to the left of that log's row.</li>
                    </ul>
                </section>
                <section id="search-logs">
                    <h3>Search Volunteer Activity Logs</h3>
                    <ul>
                        <li>To search active volunteer activity logs, navigate to the <a href="link goes here">log display table</a> on the <a href="link goes here">homepage</a>.</li>
                        <li>At the top of the page, there is a "Search Volunteer Activity" form. Note: Dropdown selections will only display options for students/organizations/semesters that currently appear in active logs.</li>
                        <ul>
                            <li>Search by Student: Select a student's name to only view logs featuring that student.</li>
                            <li>Search by Organization: Select an organization to only view logs featuring that non-profit or volunteer organization.</li>
                            <li>Search by Semester: Select a semester to view logs created by students registered in that semester.</li>
                            <li>Search After this Date: Select a date to only view activities on or after that date.</li>
                            <li>Search Before this Date: Select a date to only view activities on or before that date.</li>
                            <li>Search for at least this many Hours: Enter a number to view logs with a duration of at least that many hours.</li>
                            <li>Search for no more than this many Hours: Enter a number to view logs with a duration of at most that many hours.</li>
                            <li>Search for at least this many Pounds of Food: Enter a number to view logs with at least that many pounds of food rescued.</li>
                            <li>Search for no more than this many Pounds of Food: Enter a number to view logs with at most that many pounds of food rescued.</li>
                        </ul>
                    </ul>
                    <ul>
                        <li>Once you have selected your filters, click the "Apply Filters" button. You may continue to <a href="link goes here">view the logs, as described above</a>.</li>
                    </ul>
                </section>
                <section id="edit-log">

                    <h3>Edit a Volunteer Activity Log</h3>
                    <ul>
                        <li>Note: Students may only edit their own logs. Instructors may edit any log.</li>
                        <li>To edit a previously created volunteer activity log, navigate to that log's page by <a href="link goes here">searching for the log</a> on the homepage.</li>
                        <li>On the log's page, you will see the header "Volunteer Activity Details" with a pencil icon to the right. Click the pencil to open the edit form.</li>
                        <li>Make your changes and click the "Update Log" button.</li>
                    </ul>
                </section>
                <section id="delete-log">
                    <h3>Delete a Volunteer Activity Log</h3>
                    <ul>
                        <li>Note: Students may only delete their own logs. Instructors may delete any log.</li>
                        <li>To delete a previously created volunteer activity log, navigate to that log's page by <a href="link goes here">searching for the log</a> on the homepage.</li>
                        <li>On the log's page, you will see the header "Volunteer Activity Details" with a trashcan icon to the right. Click the trashcan to open the delete form.</li>
                    </ul>
                </section>

                <section id="view-own-logs">
                    <h3>View My Volunteer Activity Logs</h3>
                    <!-- Add instructions here if needed -->
                </section>

                <section id="view-impact-summary">
                    <h3>View Your Personal Impact Summary</h3>
                    <!-- Add instructions here if needed -->
                </section>

                <section id="add-organization">
                    <h3>Add Organization</h3>
                    <ul>
                        <li>To add a new non-profit or volunteer organization, navigate to the <a href="link goes here">"Add Organization" page</a> via the navigation bar dropdown at the top of any page.</li>
                        <li>Enter the details about the organization and click "Submit."</li>
                        <li>You are required to provide the organization's name.</li>
                        <li>You may additionally provide an e-mail, a location, and a description.</li>
                    </ul>
                </section>

                <section id="edit-org">
                    <h3>Edit Organization</h3>
                    <ul>
                        <li>Note: Students may only edit their own logs. Instructors may edit any log.</li>
                        <li>To edit the details of a previously created volunteer activity log, navigate to that log's page by <a href="link goes here">searching for the log</a> on the homepage.</li>
                        <li>On the log's page, you will see the header "Volunteer Activity Details" with a pencil icon to the right. Click the pencil to open the edit form.</li>
                        <li>Make your changes and click the "Update Log" button.</li>
                    </ul>
                </section>

                <section id="add-org">
                    <h3>Add Organization</h3>
                    <!-- Add instructions here -->
                </section>

                <section id="delete-orgs">
                    <h3>Delete Organizations</h3>
                    <!-- Add instructions here -->
                </section>

                <section id="manage-user-roles">
                    <h3>Manage User Roles</h3>
                    <!-- Add instructions here -->
                </section>

                <section id="export-data">
                    <h3>Export Data</h3>
                    <!-- Add instructions here -->
                </section>

                <section id="view-analytics">
                    <h3>View Analytics Dashboard</h3>
                    <!-- Add instructions here -->
                </section>
            </div>
        </main>
    </body>
</html>

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
    //ignore the ones with 0, not coded below for variable appeareance
    $sections = [
        "add_log" => 0,
        "view_logs" => 0,
        "search_logs" => 0,
        "edit_log" => 0,
        "delete_log" => 0,
        "view_own_logs" => 1,
        "view_impact_summary" => 1,
        "add_org" => 0,
        "edit_org" => 0,
        "delete_orgs" => 3,
        "search_users" => 3,
        "manage_user_roles" => 3,
        "export_data" => 3,
        "view_analytics" => 3,
        "student_search" => 3,
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
                            <li><a href="#add-log">Add a Volunteer Activity Log</a></li>
                            <li><a href="#view-logs">View All Volunteer Activity Logs</a></li>
                            <li><a href="#search-logs">Search Volunteer Activity Logs</a></li>
                            <li><a href="#edit-log">Edit a Volunteer Activity Log</a></li>
                            <li><a href="#delete-log">Delete a Volunteer Activity Log</a></li>
                            <?php if ($sections['view_own_logs'] == $accessLevel): ?><li><a href="#view-own-logs">View My Volunteer Activity Logs</a></li><?php endif; ?>
                            <?php if ($sections['view_impact_summary'] == $accessLevel): ?><li><a href="#view-impact-summary">View Your Personal Impact Summary</a></li><?php endif; ?>
                            <li><a href="#add-org">Add Organization</a></li>
                            <li><a href="#edit-org">Edit Organization</a></li>
                            <?php if ($sections['delete_orgs'] == $accessLevel): ?><li><a href="#delete-orgs">Delete Organizations</a></li><?php endif; ?>
                            <?php if ($sections['search_users'] == $accessLevel): ?><li><a href="#search-users">Search Users</a></li><?php endif; ?>
                            <?php if ($sections['manage_user_roles'] == $accessLevel): ?><li><a href="#manage-user-roles">Manage User Roles</a></li><?php endif; ?>
                            <?php if ($sections['export_data'] == $accessLevel): ?><li><a href="#export-data">Export Data</a></li><?php endif; ?>
                            <?php if ($sections['view_analytics'] == $accessLevel): ?><li><a href="#view-analytics">View Analytics Dashboard</a></li><?php endif; ?>
                        </ol>
                    </div>
                </div>
            </div>

            <div class="main-content-box">
                <section id="add-log">
                    <h3 <?php if ($sections['add_log'] == 3) { echo 'class="aside_instructor-only"'; } ?>>Add a Volunteer Activity Log</h3>
                    <ul>
                        <li>To add records of your volunteer activities with a non-profit or volunteer organization on a particular date, navigate to the <a href="index.php" target="_blank">homepage</a> and select the <a href="link goes here" target="_blank">"Add Log" button</a> at the top of the page.</li>
                        <li>Enter the details about your activity into the form and click "Create Activity."</li>
                        <li>You are required to provide information about the date, the duration (number of hours), and the organization.</li>
                        <li>You may additionally provide information about the location, the pounds of food rescued, and a description of the activity.</li>
                    </ul>
                </section>
                <section id="view-logs">
                    <h3 <?php if ($sections['view_logs'] == 3) { echo 'class="aside_instructor-only"'; } ?>>View All Volunteer Activity Logs</h3>
                    <ul>
                        <li>To view volunteer activity logs, navigate to the <a href="viewAllLogs.php" target="_blank">log display table</a> on the <a href="index.php" target="_blank">homepage</a>.</li>
                        <li>Scroll down until you see the section titled "View All Volunteer Activity."</li>
                        <li>If there are numerous logs, the table will display only one page at a time. Page navigation links are found at the lower right corner of the table. Click the numbered buttons to view that page or the arrows to navigate pages.</li>
                        <li>By default, the logs are sorted by date. Click any column header link to sort by that field. An arrow will appear next to the selected header, indicating ascending or descending order. Click the header again to reverse the order.</li>
                        <li>To view the details of a single log, click the "👁" icon to the left of that log's row.</li>
                    </ul>
                </section>
                <section id="search-logs">
                    <h3 <?php if ($sections['search_logs'] == 3) { echo 'class="aside_instructor-only"'; } ?>>Search Volunteer Activity Logs</h3>
                    <ul>
                        <li>To search active volunteer activity logs, navigate to the <a href="viewAllLogs.php" target="_blank">log display table</a> on the <a href="index.php" target="_blank">homepage</a>.</li>
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
                        <li>Once you have selected your filters, click the "Apply Filters" button. You may continue to <a href="#view-logs">view the logs, as described above</a>.</li>
                    </ul>
                </section>
                <section id="edit-log">
                    <h3 <?php if ($sections['edit_log'] == 3) { echo 'class="aside_instructor-only"'; } ?>>Edit a Volunteer Activity Log</h3>
                    <ul>
                        <li>Note: Students may only edit their own logs. Instructors may edit any log.</li>
                        <li>To edit a previously created volunteer activity log, navigate to that log's page by <a href="#search-logs">searching for the log</a> on the homepage.</li>
                        <li>On the log's page, you will see the header "Volunteer Activity Details" with a pencil icon to the right. Click the pencil to open the edit form.</li>
                        <li>Make your changes and click the "Update Log" button.</li>
                    </ul>
                </section>
                <section id="delete-log">
                    <h3 <?php if ($sections['delete_log'] == 3) { echo 'class="aside_instructor-only"'; } ?>>Delete a Volunteer Activity Log</h3>
                    <ul>
                        <li>Note: Students may only delete their own logs. Instructors may delete any log.</li>
                        <li>To delete a previously created volunteer activity log, navigate to that log's page by <a href="#search-logs">searching for the log</a> on the homepage.</li>
                        <li>On the log's page, you will see the header "Volunteer Activity Details" with a trashcan icon to the right. Click the trashcan to delete the log.</li>
                    </ul>
                </section>

                <?php if ($sections['view_own_logs'] == $accessLevel): ?>
                <section id="view-own-logs">
                    <h3 <?php if ($sections['view_own_logs'] == 3) { echo 'class="aside_instructor-only"'; } ?>>View My Volunteer Activity Logs</h3>
                    <ul>
                        <li>To view your own volunteer activity logs, navigate to the <a href="viewAllLogs.php" target="_blank">log display table</a> folowing the guide on how to <a href="#view-logs">View All Volunteer Activity Logs</a>.</li>
                        <li>Open the dropdown selection to search by student.  Select your own name.  Click the "Apply Filters" button."</li>
                    </ul>
                </section>
                <?php endif; ?>

                <?php if ($sections['view_impact_summary'] == $accessLevel): ?>
                <section id="view-impact-summary">
                    <h3 <?php if ($sections['view_impact_summary'] == 3) { echo 'class="aside_instructor-only"'; } ?>>View Your Personal Impact Summary</h3>
                    <ul>
                        <li>To view your personal impact summary, navigate to the <a href="viewImpactSummary.php" target="_blank">"Personal Impact Summary" page</a> via the navigation bar dropdown at the top of any page.</li>
                        <li>Your personal impact summary displays the total number of hours you have volunteered, the total pounds of food you have rescued, and the total number of your volunteer activity logs.  It also displays the number of hours and pounds of food rescued broken down by organizaiton.</li>
                        <li>Note: Volunteer activity logs do not require you to fill out the "Duration" and "Pounds of Food" fields.  The personal impact summary only reflects information recorded in volunteer activity logs.</li>

                    </ul>
                </section>
                <?php endif; ?>

                <section id="add-org">
                    <h3 <?php if ($sections['add_org'] == 3) { echo 'class="aside_instructor-only"'; } ?>>Add Organization</h3>
                    <ul>
                        <li>To add a new non-profit or volunteer organization, navigate to the <a href="addOrganization.php" target="_blank">"Add Organization" page</a> via the navigation bar dropdown at the top of any page.</li>
                        <li>Enter the details about the organization and click "Submit."</li>
                        <li>You are required to provide the organization's name.</li>
                        <li>You may additionally provide an e-mail, a location, and a description.</li>
                    </ul>
                </section>

                <section id="edit-org">
                    <h3 <?php if ($sections['edit_org'] == 3) { echo 'class="aside_instructor-only"'; } ?>>Edit Organization</h3>
                    <ul>
                        <li>To edit a previously created organization, navigate to that organization's page by <a href="#search-orgs">searching for the organization</a> on the homepage.</li>
                        <li>On the organization's page, you will see the header "Organization Details" with a pencil icon to the right. Click the pencil to open the edit form.</li>
                        <li>Make your changes and click the "Update Organization" button.</li>
                    </ul>
                </section>

                <?php if ($sections['delete_orgs'] == $accessLevel): ?>
                <section id="delete-orgs">
                    <h3 <?php if ($sections['delete_orgs'] == 3) { echo 'class="aside_instructor-only"'; } ?>>Delete Organizations</h3>
                    <ul>
                        <li>To delete previously archived organizations, navigate to the <a href="deleteOrganizationSearch.php" target="_blank">"Delete Organizations" page</a>.</li>
                        <li>Enter the name of the organization in the "Name" field and click the "Search" button.</li>
                        <li>Locate the organizations you would like to delete and click the tick box next to each.  When you are ready, click the "Delete Selected" button.</li>
                        <li>Note: The search will only display archived organizations.  If you do not see the organization you would like to delete, make sure it is archived first.</li>
                        <li>Note: This is a permanent action that will delete the organization and all of its associated data, including volunteer activity logs.  The action cannot be reversed.</li>
                    </ul>
                </section>
                <?php endif; ?>

                <?php if ($sections['search_users'] == $accessLevel): ?>
                <section id="search-users">
                    <h3 <?php if ($sections['search_users'] == 3) { echo 'class="aside_instructor-only"'; } ?>>Search Students</h3>
                    <ul>
                        <li>To search for a student, navigate to the <a href="personSearch.php" target="_blank">"Search Users" page</a> via the navigation bar dropdown at the top of any page.</li>
                        <li>Enter the student's name or other details to find their profile.</li>
                        <li>Note: By defualt search results show both archived and active users.  If you would like to exclude archived or active users, deselect the appropriate checkbox at the bottom of the form.</li>
                        <li>Click the "Search" button to view the results.</li>
                        <li>Click on a the "Profile" link to view the student's profile.</li>
                        <li>Click on a the "Update Status" link to change the user's status to archived or active and to change the user's role to Student or Instructor.</li>
                    </ul>
                </section>
                <?php endif; ?>

                <?php if ($sections['manage_user_roles'] == $accessLevel): ?>
                <section id="manage-user-roles">
                    <h3 <?php if ($sections['manage_user_roles'] == 3) { echo 'class="aside_instructor-only"'; } ?>>Manage User Roles</h3>
                    <ul>
                        <li>To set a user role to either Student or Instructor, navigate to that students's page by <a href="#student-search">searching for the student</a>.</li>
                        <li>Click on a the "Update Status" link in the appropriate user's row in the search results.</li>
                        <li>Make your changes and click the "Update" button.</li>
                    </ul>
                </section>
                <?php endif; ?>

                <?php if ($sections['export_data'] == $accessLevel): ?>
                <section id="export-data">
                    <h3 <?php if ($sections['export_data'] == 3) { echo 'class="aside_instructor-only"'; } ?>>Export Data</h3>
                    <ul>
                        <li>To generate data exports, navigate to the <a href="generateReport.php" target="_blank">"Generate Reports" page</a>.</li>
                        <li>Select the type of report and file format you would like to generate.  You may also select the tick box labelled "Archived" to include archived data in addition to active data.</li>
                        <li>Click the "Generate Report" button to generate the report.  Depending on the size of the data, it may take a moment for the file to download.</li>
                    </ul>
                </section>
                <?php endif; ?>

                <?php if ($sections['view_analytics'] == $accessLevel): ?>
                <section id="view-analytics">
                    <h3 <?php if ($sections['view_analytics'] == 3) { echo 'class="aside_instructor-only"'; } ?>>View Analytics Dashboard</h3>
                    <ul>
                        <li>To view the analytics dashboard, navigate to the <a href="analyticsDashboard.php" target="_blank">"Analytics Dashboard" page</a>.</li>
                        <li>The dashboard will display data from volunteer activity logs:</li>
                        <ul>
                            <li>The overall number of hours volunteered and pounds of food rescued.</li>
                            <li>Bar graphs showing the total hours volunteered and pounds of food rescued each month.</li>
                            <li>A geographical map showing the locations of volunteer activities.</li>
                        </ul>
                        <li>You may click the "Impact by Student" button in the left side bar to view a table showing the total hours volunteered, the pounds of food rescued, and the number of logs for each student.</li>
                        <li>You may click the "Impact by Organization" button in the left side bar to view a table showing the total hours volunteered, the pounds of food rescued, and the number of logs for each organization.</li>
                    </ul>
                </section>
                <?php endif; ?>
            </div>
        </main>
    </body>
</html>

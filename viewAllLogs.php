<?php
    // Template for new VMS pages. Base your new page on this one

    // Make session information accessible, allowing us to associate
    // data with the logged-in user.
    // session_cache_expire(30);
    // session_start();

    // $loggedIn = false;
    // $accessLevel = 0;
    // $userID = null;
    // if (isset($_SESSION['_id'])) {
    //     $loggedIn = true;
    //     // 0 = not logged in, 1 = standard user, 2 = manager (Admin), 3 super admin (TBI)
    //     $accessLevel = $_SESSION['access_level'];
    //     $userID = $_SESSION['_id'];
    // }
    // //require being a logged in user
    // if ($accessLevel < 1) {
    //     header('Location: login.php');
    //     //echo 'bad access level';
    //     die();
    // }

    include_once 'database/dbVolunteerActivity.php';
    include_once 'database/dbUsers.php';
    include_once 'database/dbOrganizations.php';
    include_once 'include/output.php';


    //check for sorting
    //make sure no sql injections
    $sortby = 'date'; $order = 'asc'; $sortby_display = 'date';
    //echo $_GET['sortby'] . " " . $_GET['order'] . "\n";
    if (isset($_GET['sortby']) && in_array($_GET['sortby'], ['student', 'date', 'organization',
        'hours', 'location', 'poundsoffood', 'description'])) {

        $sortby_display = $_GET['sortby'];
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
    } else if ($sortby === 'date') {
            $order = 'desc';
    }

    //get filters
    $filters = extract_permitted_filters_on_logs($_GET); //preserve for putting into urls in pagination and header links

    //get page
    $per_page = 8;
    $page_display_range = 2;
    $page_num = max(0, (int)($_GET['page'] ?? 1) - 1);
    //get max pagination
    $max_pages = max(0, ceil(get_num_logs_with_filters($filters) / $per_page) - 1); //total allowed pages
    $page_num = (int) min($max_pages, $page_num);

    $logs = get_all_volunteer_activities_custom_sort_pagination_with_filters($sortby, $order, $per_page, $page_num * $per_page, $filters, $wants_archived = false);

    //include 'domain/Event.php';
?>
<!-- <!DOCTYPE html>
<html> -->
    <head>
        <?php //require_once('universal.inc') ?>
        <script src="js/messages.js"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css"/>
        <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    </head>
    <body>
        <?php //require_once('header.php') ?>
        <!-- <main class="general"> -->
            <!-- <h1>Volunteer Activity</h1>

            <h2>Search Volunteer Activity</h2> -->
            <form class="log_filters" action="index.php?" method="GET">
                <input type="hidden" name="page" value="<?php echo hsc($page_num + 1); ?>" />
                <input type="hidden" name="sortby" value="<?php echo hsc($sortby_display); ?>" />
                <input type="hidden" name="order" value="<?php echo hsc($order); ?>" />
                <div class="log_filters--row">
                    <div class="log_filter">
                        <label for="studentSelect">Search by Student</label>
                        <select id="studentSelect" name="students">
                            <option value=""></option>
                            <?php foreach (get_students_in_logs() as $row): ?>
                                <option value="<?php echo hsc($row['id']); ?>" <?php echo isset($filters['students']) && $filters['students'] == $row['id'] ? 'selected' : ''; ?>>
                                    <?php echo hsc($row['last_name']) . ", " . hsc($row['first_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="log_filter">
                        <label for="organizationSelect">Search by Organization</label>
                        <select id="organizationSelect" name="organizations">
                            <option value=""></option>
                            <?php foreach (get_organizations_in_logs() as $row): ?>
                                <option value="<?php echo hsc($row['id']); ?>" <?php echo isset($filters['organizations']) && $filters['organizations'] == $row['id'] ? 'selected' : ''; ?>>
                                    <?php echo hsc($row['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="log_filter">
                        <label for="semesterSelect">Search by Semester</label>
                        <select id="semesterSelect" name="semesters">
                            <option value=""></option>
                            <?php foreach (get_semesters_in_users() as $row): ?>
                                <option value="<?php echo hsc($row['semester']); ?>" <?php echo isset($filters['semesters']) && $filters['semesters'] == $row['semester'] ? 'selected' : ''; ?>>
                                    <?php echo hsc($row['semester']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="log_filters--row">
                    <div class="log_filter">
                        <label for="startDate">Search after this Date</label>
                        <input type="date" id="startDate" name="startdate"
                        value="<?php echo hsc($filters['startdate'] ?? ''); ?>">

                        <label for="endDate">Search Before this Date</label>
                        <input type="date" id="endDate" name="enddate" value="<?php echo hsc($filters['enddate'] ?? ''); ?>">
                    </div>
                    <div class="log_filter">
                        <label for="minHours">Search for at least this many Hours</label>
                        <input type="number" id="minHours" name="minhours" min="0" placeholder="From" value="<?php echo hsc($filters['minhours'] ?? ''); ?>">
                        <label for="maxHours">Search for no more than this many Hours</label>
                        <input type="number" id="maxHours" name="maxhours" min="0" placeholder="To" value="<?php echo hsc($filters['maxhours'] ?? ''); ?>">
                    </div>
                    <div class="log_filter">
                        <label for="minFood">Search for at least this many Pounds of Food</label>
                        <input type="number" id="minFood" name="minfood" min="0" placeholder="From" value="<?php echo hsc($filters['minfood'] ?? ''); ?>">
                        <label for="maxFood">Search for no more than this many Pounds of Food</label>
                        <input type="number" id="maxFood" name="maxfood" min="0" placeholder="To" value="<?php echo hsc($filters['maxfood'] ?? ''); ?>">
                    </div>
                </div>
                <div style="margin: auto; width: 75%;">
                    <button type="submit">Apply Filters</button>
                    <a class="button cancel" href="index.php">Clear Filters</a>
                </div>
            </form>

            <?php

                if(isset($_SESSION['user_id']) && $_SESSION['user_id'] != 'guest') {
                    $user = retrieve_person($userID);
                }

                if (sizeof($logs) > 0): ?>
                <div class="table-wrapper">
                    <!-- <h2 id="log-table">View All Volunteer Activity</h2> -->
                    <table class="general" id="log-table">
                        <thead>
                            <tr>
                                <th style="width:2%;"></th>
                                <th style="width:14%"><a class='event-link <?php echo ($sortby_display === 'student') ? 'sorted-'. hsc($order) : '' ?>' href='index.php?<?php echo hsc(http_build_query(array_merge(['page' => $page_num + 1, 'sortby' => 'student', 'order' => (($sortby_display === 'student') ? (($order === 'asc') ? 'desc' : 'asc') : 'asc')], $filters))); ?>#log-table'>Student</a><div></div></th>
                                <th style="width:14%"><a class='event-link <?php echo ($sortby_display === 'date') ? 'sorted-'. hsc($order) : '' ?>' href='index.php?<?php echo hsc(http_build_query(array_merge(['page' => $page_num + 1, 'sortby' => 'date', 'order' => (($sortby_display === 'date') ? (($order === 'asc') ? 'desc' : 'asc') : 'desc')], $filters))); ?>#log-table'>Date</a></th>
                                <th style="width:14%"><a class='event-link <?php echo ($sortby_display === 'organization') ? 'sorted-'. hsc($order) : '' ?>' href='index.php?<?php echo hsc(http_build_query(array_merge(['page' => $page_num + 1, 'sortby' => 'organization', 'order' => (($sortby_display === 'organization') ? (($order === 'asc') ? 'desc' : 'asc') : 'asc')], $filters))); ?>#log-table'>Organization</a></th>
                                <th style="width:5%"><a class='event-link <?php echo ($sortby_display === 'hours') ? 'sorted-'. hsc($order) : '' ?>' href='index.php?<?php echo hsc(http_build_query(array_merge(['page' => $page_num + 1, 'sortby' => 'hours', 'order' => (($sortby_display === 'hours') ? (($order === 'asc') ? 'desc' : 'asc') : 'asc')], $filters))); ?>#log-table'>Hours</a></th>
                                <th style="width:23%"><a class='event-link <?php echo ($sortby_display === 'location') ? 'sorted-'. hsc($order) : '' ?>' href='index.php?<?php echo hsc(http_build_query(array_merge(['page' => $page_num + 1, 'sortby' => 'location', 'order' => (($sortby_display === 'location') ? (($order === 'asc') ? 'desc' : 'asc') : 'asc')], $filters))); ?>#log-table'>Location</a></th>
                                <th style="width:8%"><a class='event-link <?php echo ($sortby_display === 'poundsoffood') ? 'sorted-'. hsc($order) : '' ?>' href='index.php?<?php echo hsc(http_build_query(array_merge(['page' => $page_num + 1, 'sortby' => 'poundsoffood', 'order' => (($sortby_display === 'poundsoffood') ? (($order === 'asc') ? 'desc' : 'asc') : 'asc')], $filters))); ?>#log-table'>Food Rescued (lbs)</a></th>
                                <th style="width:20%"><a class='event-link <?php echo ($sortby_display === 'description') ? 'sorted-'. hsc($order) : '' ?>' href='index.php?<?php echo hsc(http_build_query(array_merge(['page' => $page_num + 1, 'sortby' => 'description', 'order' => (($sortby_display === 'description') ? (($order === 'asc') ? 'desc' : 'asc') : 'asc')], $filters))); ?>#log-table'>Description</a></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
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
                                    ?>
                                    <tr data-event-id="<?php echo hsc($logID); ?>">
                                        <td><a href="log.php?id=<?php echo hsc($logID); ?>" class="event-link">👁</a></td>
                                        <td><?php echo hsc($studentName); ?></td>
                                        <td><?php echo hsc($date); ?></td>
                                        <td><?php echo hsc($organizationName); ?></td>
                                        <td><?php echo hsc($hours); ?></td>
                                        <td><?php echo hsc($location); ?></td>
                                        <td><?php echo hsc($pounds); ?></td>
                                        <td><?php echo hsc($description); ?></td>
                                    </tr>
                                <?php
                                }
                            ?>
                        </tbody>
                    </table>
                </div>

                <ul class="pagination">
                    <?php if ($page_num - $page_display_range > 0): ?>
                        <li class="pagination_li">
                            <a href="index.php?<?php echo hsc(http_build_query(array_merge(['page' => 0, 'sortby' => $sortby_display, 'order' => $order], $filters))); ?>#log-table" class="pagination_link">&#x21e4;</a>
                        </li>
                    <?php endif; ?>
                    <?php for($x = max(0, $page_num - $page_display_range); $x <= min($max_pages, $page_num + $page_display_range); $x++): ?>
                        <li class="pagination_li">
                            <a href="index.php?<?php echo hsc(http_build_query(array_merge(['page' => $x + 1, 'sortby' => $sortby_display, 'order' => $order], $filters))); ?>#log-table" class="pagination_link<?php if ($page_num === $x): ?> pagination_link--active<?php endif; ?>"><?php echo hsc($x + 1); ?></a>
                        </li>
                    <?php endfor; ?>
                    <?php if ($page_num < $max_pages - $page_display_range): ?>
                    <li class="pagination_li">
                        <a href="index.php?<?php echo hsc(http_build_query(array_merge(['page' => $max_pages + 1, 'sortby' => $sortby_display, 'order' => $order], $filters))); ?>#log-table" class="pagination_link">&#8677;</a>
                    </li>
                    <?php endif; ?>
                </ul>
                <?php else: ?>
                <p class="no-events standout">There are currently no logs available to view.<a class="button add" href="addLog.php">Create a New Log</a> </p>
            <?php endif ?>
            <!-- <a class="button cancel" href="index.php" style="margin: auto;">Return to Dashboard</a> -->
        <!-- </main> -->

    <script>
        document.addEventListener('DOMContentLoaded', function() {
        new Choices('#studentSelect', {
            searchEnabled: true,
            removeItemButton: true,
            placeholder: true,
            placeholderValue: 'Select students...',
            shouldSort: false
        });
        new Choices('#organizationSelect', {
            searchEnabled: true,
            removeItemButton: true,
            placeholder: true,
            placeholderValue: 'Select organizations...',
            shouldSort: false
        });

        new Choices('#semesterSelect', {
            searchEnabled: true,
            removeItemButton: true,
            placeholder: true,
            placeholderValue: 'Select semesters...',
            shouldSort: false
        });
    });


    </script>
    </body>
</html>

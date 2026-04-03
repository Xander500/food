<?php
    // Template for new VMS pages. Base your new page on this one

    // Make session information accessible, allowing us to associate
    // data with the logged-in user.
    session_cache_expire(30);
    session_start();
    require_once('database/dbUsers.php');


    $loggedIn = false;
    $accessLevel = 0;
    $userID = null;
    if (isset($_SESSION['_id'])) {
        $loggedIn = true;
        // 0 = not logged in, 1 = student, 3 instructor
        $accessLevel = $_SESSION['access_level'];
        $userID = $_SESSION['_id'];
    }
    // admin-only access
    if ($accessLevel < 2) {
        header('Location: index.php');
        die();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>UMW Alleviating Food Waste Volunteer Tracking | Manage Semesters</title>
    <!-- <link href="css/management_tw.css" rel="stylesheet"> -->
    <link rel="icon" type="image/x-icon" href="images/alleviatingFoodWasteLogo.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css"/>
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
<!-- BANDAID FIX FOR HEADER BEING WEIRD -->
<?php
$tailwind_mode = true;
require_once('header.php');
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

        body, main {
        background-color: white;
        }   

        .info-section .info-text {
         color: #92c44c !important;
        }

        .blue-div {
        background-color: #92c44c !important;
        }
    
</style>
<!-- BANDAID END, REMOVE ONCE SOME GENIUS FIXES -->
</head>
<body>

<!-- <header class="hero-header">
    <div class="center-header">
        <h1>User Search</h1>
    </div>
</header> -->

<main>
    <div class="main-content-box">
        <div class="text-center mb-8">
            <h2>Manage Semesters</h2>
            <div class="info-box">
                <p class="sub-text">Fill out the form to archive or unarchive all students or volunteer activity logs associated with a semester.  Mass changes will only apply to student accounts, not instructor accounts.</p>
            </div>
        </div>

        <form id="person-search" class="section-box mb-4" method="get">

        <?php
            if (isset($_GET['semester'])) {
                require_once('include/input-validation.php');
                $args = sanitize($_GET);
                $required = ['semester'];

                if (!wereRequiredFieldsSubmitted($args, $required, true)) {
                    echo '<div class="error-block">Missing expected form elements.</div>';
                }

                $semester = $args['semester'];
                $status = $args['status'] ?? [];

                if (!($semester)) {
                    echo '<div class="error-block">At least one search criterion is required, including semester.</div>';
                }else {
                    echo "<h3>Results</h3>";

                    if (isset($_GET['action']) && isset($_GET['apply_to']) && $_GET['apply_to'] == 'Student') {
                        $action = $_GET['action'];
                        $apply_to = $_GET['apply_to'];
                        $action_code = ($action == 'Archive') ? 1 : 0; // 1 for archive, 0 for unarchive

                        $changes = archive_users_by_semester($semester, $action_code);
                        echo '<div class="success-block">'. $changes . ' student accounts associated with ' . $semester . ' have been'. ($action_code ? ' archived' : ' unarchived') . '.</div>';

                        $persons = search_users($name="", $id="", $semester, $role="Student", $status=[]); //only care about searching semester //only want to apply to students
                        require_once('include/output.php');

                        if (count($persons) > 0) {
                            echo '
                            <div class="search-results-table">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Status</th>
                                            <th>First</th>
                                            <th>Last</th>
                                            <th>Username</th>
                                            <th>Role</th>
                                        </tr>
                                    </thead>
                                    <tbody>';
                            foreach ($persons as $person) {
                                echo '
                                        <tr>
                                            <td>' . (($person->is_archived()==0)?"Active":"Archived") . '</td>
                                            <td>' . $person->get_first_name() . '</td>
                                            <td>' . $person->get_last_name() . '</td>
                                            <td>' . $person->get_id() . '</td>
                                            <td>' . ucfirst($person->get_role() ?? '') . '</td>
                                        </tr>';
                            }
                            echo '
                                    </tbody>
                                </table>
                            </div>';

                        } else {
                            echo '<div class="error-block">Your search returned no results.</div>';
                        }
                        echo '<h3>Search Again</h3>';
                    } //end student table display
                    else {
                        echo '<div class="error-block">Action and apply_to fields are required to perform archival actions.</div>';
                    }
                }
            }
        ?>         
            <div>
                <label for="semester">Semester</label>
                <select id="semester" name="semester" class="w-full">
                    <?php foreach (get_semesters_in_users() as ['semester' => $s]): ?>
                    <option value="<?php echo $s ?>" <?php if (isset($semester) && $semester == $s) echo 'selected'; ?>><?php echo $s ?></option>
                    <?php endforeach; ?>
                </select>
            </div> 

            <div>
                <label for="action">I want to</label>
                <select id="action" name="action" class="w-full">
                    <option value="Archive" <?php if (isset($action) && $action == 'Archive') echo 'selected'; ?>>Archive</option>
                    <option value="Unarchive" <?php if (isset($action) && $action == 'Unarchive') echo 'selected'; ?>>Unarchive</option>
                </select>
            </div>

            <div>
                <label for="apply_to">Apply action to</label>
                <select id="apply_to" name="apply_to" class="w-full">
                    <option value="Student" <?php if (isset($apply_to) && $apply_to == 'Student') echo 'selected'; ?>>Students</option>
                </select>
            </div>

            <div class="text-center pt-4">
                <input type="submit" value="Search" class="blue-button">
            </div>

        </form>
        <div class="text-center mt-6">
            <a href="index.php" class="return-button">Return to Dashboard</a>
        </div>
    </div>

</main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
        new Choices('#semester', {
            searchEnabled: true,
            removeItemButton: true,
            placeholder: true,
            placeholderValue: 'Select semester',
            shouldSort: true
        });
    });


    </script>

</body>
</html>


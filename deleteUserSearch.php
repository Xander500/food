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
        // 0 = not logged in, 1 = standard user, 2 = manager (Admin), 3 super admin (TBI)
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
    <title>UMW Alleviating Food Waste Volunteer Tracking | User Search</title>
    <!-- <link href="css/normal_tw.css" rel="stylesheet"> -->
     <link rel="icon" type="image/x-icon" href="images/alleviatingFoodWasteLogo.png">
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

    body {
        background-color: white !important;
    }

    /* .info-text {
        color: #92c44c !important;
    }

    .blue-div {
        background-color: #92c44c !important;
    }

    .text-blue-700,
    .text-blue-700:visited,
    .text-blue-700:hover {
        color: black !important;
        }

    .main-content-box label {
            color: #92c44c !important;
    } */

</style>
<!-- BANDAID END, REMOVE ONCE SOME GENIUS FIXES -->
</head>
<body>

<!-- <header class="hero-header">
    <div class="center-header">
        <h1>Delete User</h1>
    </div>
</header> -->

<main>
    <div class="main-content-box">
        <div class="text-center mb-8">
            <h2>Find a User</h2>
            <div class="info-box">
                <p class="sub-text">Use the fields below to filter and search for users.</p>        
            </div>
        </div>

                        <?php
            if (isset($_GET['name']) || isset($_GET['id']) || isset($_GET['role']) || isset($_GET['semester'])) {
                require_once('include/input-validation.php');
                require_once('database/dbUsers.php');
                $args = sanitize($_GET);
                $required = ['name', 'id', 'role', 'semester'];

                if (!wereRequiredFieldsSubmitted($args, $required, true)) {
                    echo '<div class="error-block">Missing expected form elements.</div>';
                }

                $name = $args['name'];
                $id = $args['id'];
                $semester = $args['semester'];
                $role = $args['role'];


                if (!($name || $id || $semester || $role)) {
                    echo '<div class="error-block">At least one search criterion is required.</div>';
                } else if (!valueConstrainedTo($role, ['Instructor', 'Student', ''])) {
                    echo '<div class="error-block">The system did not understand your request.</div>';
                }else {
                    echo '<div class="section-box mb-6" style="background-color:#92c44c; padding:25px; border-radius:10px; max-width:900px; margin:0 auto;"><h3 style="color:white;">Search Results</h3>';
                    $persons = search_users($name, $id, $semester, $role, ['1']);
                    require_once('include/output.php');

                    if (count($persons) > 0) {
                        echo '
                        <form method="post" action="deleteUsersBulk.php" onsubmit="return confirm(\'Are you sure you want to delete the selected users?  This action will permanently delete the users AND their volunteer activity.\');">
                        <div class="search-results-table" style="margin-top:10px; display:flex; justify-content:center;">
                            <table style="width:100%;">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" id="select-all"></th>
                                        <th>Status</th>
                                        <th>First</th>
                                        <th>Last</th>
                                        <th>Username</th>
                                        <th>Semester</th>
                                        <th>Role</th>
                                        <th>Profile</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>';
                        $mailingList = '';
                        $notFirst = false;
                        foreach ($persons as $person) {
                            if ($notFirst) {
                                $mailingList .= ', ';
                            } else {
                                $notFirst = true;
                            }
                            $mailingList .= $person->get_email();
                            echo '
                                    <tr>
                                        <td><input type="checkbox" name="selected_users[]" value="' . $person->get_id() . '"></td>
                                        <td>' . (($person->is_archived()==0)?"Active":"Archived") . '</td>
                                        <td>' . $person->get_first_name() . '</td>
                                        <td>' . $person->get_last_name() . '</td>
                                        <td style="word-break: break-word;">' . $person->get_id() . '</td>
                                        <td>' . $person->get_semester() . '</td>
                                        <td>' . ucfirst($person->get_role() ?? '') . '</td>
                                        <td><a href="viewProfile.php?id=' . $person->get_id() . '" class="text-blue-700 underline">Profile</a></td>
                                        <td><a href="deleteUser.php?id=' . $person->get_id() . '" onclick="return confirm(\'Are You Sure?  This action will permanently delete the user AND their volunteer activity.\');" class="text-blue-700 underline">Delete User</a></td>
                                    </tr>';
                        }
echo '
            </tbody>
        </table>
    </div>
<div class="text-center pt-4">
    <p id="selected-count" style="margin-bottom:10px; color:white; font-weight:bold;">0 users selected</p>
    <input type="hidden" name="bulk_delete" value="1">
    <input type="submit" id="bulk-delete-btn" value="Delete Selected Users" class="blue-button" style="background-color: #b91c1c; opacity:0.5; cursor:not-allowed;" disabled>
</div>
</form>';
echo "</div>";

                        /*echo '
                        <div class="mt-4">
                            <label>Result Mailing List:</label>
                            <p class="text-gray-700 break-words">' . $mailingList . '</p>
                        </div>';*/
                    } else {
                        echo '<div class="error-block">Your search returned no results.</div>';
                    }
                    echo '<h3 style="margin-top:20px;">Search Again</h3>';
                }
            }
        ?>

        <form id="person-search" class="section-box mb-4" method="get" style="max-width:900px; margin:20px auto 0 auto;">



            <div>
                <label for="name">Name</label>
                <input type="text" id="name" name="name" class="w-full" value="<?php if (isset($name)) echo htmlspecialchars($_GET['name']); ?>" placeholder="Enter the user's first and/or last name">
            </div>

            <div>
                <label for="id">Username</label>
                <input type="text" id="id" name="id" class="w-full" value="<?php if (isset($id)) echo htmlspecialchars($_GET['id']); ?>" placeholder="Enter the user's username (login ID)">
            </div>
            <div>
                <label for="role">Role</label>
                <select id="role" name="role" class="w-full">
                    <option value="">Any</option>
                    <option value="Student" <?php if (isset($role) && $role == 'Student') echo 'selected'; ?>>Student</option>
                    <option value="Instructor" <?php if (isset($role) && $role == 'Instructor') echo 'selected'; ?>>Instructor</option>
                </select>
            </div>

            <div>
                <label for="semester">Semester</label>
                <select id="semester" name="semester" class="w-full">
                    <option value="">Any</option>
                    <?php foreach (get_semesters_in_users() as ['semester' => $s]): ?>
                    <option value="<?php echo $s ?>" <?php if (isset($semester) && $semester == $s) echo 'selected'; ?>><?php echo $s ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="text-center pt-4">
                <input type="submit" value="Search" class="blue-button">
            </div>

        </form>



        <div class="text-center mt-6">
            <div style="margin-top:20px; text-align:center;">
    <a class="button cancel" href="index.php">Return to Dashboard</a>
</div>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('input[name="selected_users[]"]');
    const deleteBtn = document.getElementById('bulk-delete-btn');

    if (!selectAll || !deleteBtn || checkboxes.length === 0) {
        return;
    }

function updateButtonState() {
    const checkedBoxes = Array.from(checkboxes).filter(cb => cb.checked);
    const count = checkedBoxes.length;

    deleteBtn.disabled = count === 0;

    if (count > 0) {
        deleteBtn.style.opacity = "1";
        deleteBtn.style.cursor = "pointer";
    } else {
        deleteBtn.style.opacity = "0.5";
        deleteBtn.style.cursor = "not-allowed";
    }

    const counter = document.getElementById('selected-count');
    if (counter) {
        counter.textContent = count + (count === 1 ? " user selected" : " users selected");
    }
}

    selectAll.addEventListener('change', function() {
        checkboxes.forEach(cb => cb.checked = this.checked);
        updateButtonState();
    });

    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateButtonState);
    });

    updateButtonState();
});
</script>

</body>
</html>
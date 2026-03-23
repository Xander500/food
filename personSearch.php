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
    <!-- <link href="css/management_tw.css" rel="stylesheet"> -->
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
            <h2>Find a User</h2>
            <div class="info-box">
                <p class="sub-text">Use the fields below to filter and search for users.</p>
            </div>
        </div>

        <form id="person-search" class="section-box mb-4" method="get">

        <?php
            if (isset($_GET['name']) || isset($_GET['id']) || isset($_GET['role']) || isset($_GET['semester'])) {
                require_once('include/input-validation.php');
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
                    echo "<h3>Search Results</h3>";
                    $persons = search_users($name, $id, $semester, $role);
                    require_once('include/output.php');

                    if (count($persons) > 0) {
                        echo '
                        <div class="search-results-table">
                            <table>
                                <thead>
                                    <tr>
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
                                        <td>' . $person->get_first_name() . '</td>
                                        <td>' . $person->get_last_name() . '</td>
                                        <td>' . $person->get_id() . '</td>
                                        <td>' . $person->get_semester() . '</td>
                                        <td>' . ucfirst($person->get_role() ?? '') . '</td>
                                        <td><a href="viewProfile.php?id=' . $person->get_id() . '" class="text-blue-700 underline">Profile</a></td>
                                        <td><a href="modifyUserRole.php?id=' . $person->get_id() . '" class="text-blue-700 underline">Update Status</a></td>
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
                }
            }
        ?>

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
            <a href="index.php" class="return-button">Return to Dashboard</a>
        </div>
    </div>

    <!-- <div class="info-section">
        <div class="blue-div"></div>
        <p class="info-text">
            Use this tool to filter and search for volunteers or participants by their role, zip code, phone, archive status, and more. Mailing list support is built in.
        </p>
    </div> -->
</main>

</body>
</html>


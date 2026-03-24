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
    // admin-only access
    if ($accessLevel < 2) {
        header('Location: index.php');
        die();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>UMW Alleviating Food Waste Volunteer Tracking | Organization Search</title>
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
<main>
    <div class="main-content-box">
        <div class="text-center mb-8">
            <h2>Find an Organization</h2>
            <div class="info-box">
                <p class="sub-text">Use the fields below to filter and search for organizations.</p>
            </div>
        </div>

        <form id="org-search" class="section-box mb-4" method="get">
            <?php
                if (isset($_GET['name']) || isset($_GET['location'])) {
                    require_once('include/input-validation.php');
                    require_once('database/dbOrganizations.php');
                    
                    $args = sanitize($_GET);

                    if (isset($_GET['name'])) {
                        $name = $args['name'];
                    } else {
                        $name = null;
                    }

                    if (isset($_GET['location'])) {
                        $location = $args['location'];
                    } else {
                        $location = null;
                    }

                    if (!($name || $location)) {
                        echo '<div class="error-block">At least one search criterion is required.</div>';
                    } else {
                        echo "<h3>Search Results</h3>";
                        $orgs = find_organizations($name, $location);
                        require_once('include/output.php');

                        if (count($orgs) > 0) {
                            echo '
                            <div class="search-results-table">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Description</th>
                                            <th>Location</th>
                                            <th>Email</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>';
                            foreach ($orgs as $org) {
                                echo '
                                        <tr>
                                            <td>' . $org->get_name() . '</td>
                                            <td>' . $org->get_description() . '</td>
                                            <td>' . $org->get_location() . '</td>
                                            <td><a href="mailto:' . $org->get_email() . '" class="text-blue-700 underline">' . $org->get_email() . '</a></td>
                                            <td><a href="editOrganization.php?id=' . $org->get_id() . '" class="text-blue-700 underline">Edit</a></td>
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
                <input type="text" id="name" name="name" class="w-full" value="<?php if (isset($name)) echo htmlspecialchars($_GET['name']); ?>" placeholder="Enter the name of the organization">
            </div>
            <div>
                <label for="location">Location</label>
                <input type="text" id="location" name="location" class="w-full" value="<?php if (isset($location)) echo htmlspecialchars($_GET['location']); ?>" placeholder="Enter the location of the organization">
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
</body>
</html>


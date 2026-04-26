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
<?php
$tailwind_mode = true;
require_once('header.php');
require_once('include/output.php');
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
</head>
<body>
<main>
    <div class="main-content-box">
        <div class="text-center mb-8">
            <h2>Find an Organization</h2>
            <div class="info-box">
                <p class="sub-text">Use the fields below to filter and search for organizations. You may only search and delete archived organizations. Deleting organizations is permanent and will remove all data associated with them, including volunteer activity logs.</p>        

            </div>
        </div>

        <?php
            $name = null;
            $location = null;
            $orgs = [];

            if (isset($_GET['name']) || isset($_GET['location'])) {
                require_once('include/input-validation.php');
                require_once('database/dbOrganizations.php');

                $args = sanitize($_GET);

                $name = isset($_GET['name']) ? $args['name'] : null;
                $location = isset($_GET['location']) ? $args['location'] : null;

                if (!($name || $location)) {
                    echo '<div class="error-block">At least one search criterion is required.</div>';
                } else {
                    $orgs = find_organizations($name, $location);

                    if (count($orgs) > 0) {
                        echo '
                        <div class="section-box mb-6" style="background-color:#92c44c; padding:25px; border-radius:10px; max-width:900px; margin:0 auto 24px auto;">
                            <h3 style="color:white;">Search Results</h3>
                            <form method="post" action="deleteOrganizationBulk.php" onsubmit="return confirm(\'Are you sure you want to delete the selected organizations?\');">
                                <div class="search-results-table" style="margin-top:10px; display:flex; justify-content:center;">
                                    <table style="width:100%;">
                                        <thead>
                                            <tr>
                                                <th><input type="checkbox" id="select-all-orgs"></th>
                                                <th>Name</th>
                                                <th>Description</th>
                                                <th>Location</th>
                                                <th>Email</th>
                                            </tr>
                                        </thead>
                                        <tbody>';

                        foreach ($orgs as $org) {
                            echo '
                                            <tr>
                                                <td><input type="checkbox" name="selected_orgs[]" value="' . hsc($org->get_id()) . '"></td>
                                                <td>' . hsc($org->get_name()) . '</td>
                                                <td>' . hsc($org->get_description()) . '</td>
                                                <td>' . hsc($org->get_location()) . '</td>
                                                <td><a href="mailto:' . hsc($org->get_email()) . '" class="text-blue-700 underline">' . hsc($org->get_email()) . '</a></td>
                                            </tr>';
                        }

                        echo '
                                        </tbody>
                                    </table>
                                </div>
                                <div class="text-center pt-4">
                                    <p id="selected-org-count" style="margin-bottom:10px; color:white; font-weight:bold;">0 organizations selected</p>
                                    <input type="hidden" name="bulk_delete" value="1">
                                    <input type="submit" id="bulk-delete-org-btn" value="Delete Selected Organizations" class="blue-button" style="background-color: #b91c1c; opacity:0.5; cursor:not-allowed;" disabled>
                                </div>
                            </form>
                        </div>';
                    } else {
                        echo '<div class="error-block">Your search returned no results.</div>';
                    }
                }
                echo '<h3 style="margin-top:20px;">Search Again</h3>';
            }
        ?>

        <form id="org-search" class="section-box mb-4" method="get" style="max-width:900px; margin:20px auto 0 auto;">
            <div>
                <label for="name">Name</label>
                <input type="text" id="name" name="name" class="w-full" value="<?php if (isset($name)) echo hsc($_GET['name']); ?>" placeholder="Enter the name of the organization">
            </div>
            <div>
                <label for="location">Location</label>
                <input type="text" id="location" name="location" class="w-full" value="<?php if (isset($location)) echo hsc($_GET['location']); ?>" placeholder="Enter the location of the organization">
            </div>
            <div class="text-center pt-4">
                <input type="submit" value="Search" class="blue-button">
            </div>
        </form>

        <div class="text-center" style="margin-top: 60px;">
            <a href="index.php" class="return-button">Return to Dashboard</a>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.getElementById('select-all-orgs');
    const checkboxes = document.querySelectorAll('input[name="selected_orgs[]"]');
    const deleteBtn = document.getElementById('bulk-delete-org-btn');
    const counter = document.getElementById('selected-org-count');

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

        if (counter) {
            counter.textContent = count + (count === 1 ? " organization selected" : " organizations selected");
        }

        selectAll.checked = count === checkboxes.length && checkboxes.length > 0;
    }

    selectAll.addEventListener('change', function () {
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
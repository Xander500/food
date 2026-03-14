<?php
    // Make session information accessible, allowing us to associate
    // data with the logged-in user.
    session_cache_expire(30);
    session_start();

    ini_set("display_errors",1);
    error_reporting(E_ALL);

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
        echo 'bad access level';
        die();
    }

    require_once('include/input-validation.php');
    require_once('database/dbVolunteerActivity.php');
    require_once('database/dbOrganizations.php');

    $errors = '';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $args = sanitize($_POST, null);
        $required = array(
            "id", "volunteer_id", "date", "organization_id", "hours"
        );

        if (!wereRequiredFieldsSubmitted($args, $required)) {
            echo 'bad form data';
            die();
        } else {
            $id = $args['id'];
            $date = $args['date'] = validateDate($args["date"]);

            $args['description'] = $args['description'] ?? '';
            $args['poundsOfFood'] = $args['poundsOfFood'] ?? 0;

            if (!$date) {
                $errors .= 'Invalid date format. ';
            }

            if (!is_numeric($args['hours']) || $args['hours'] < 0) {
                $errors .= 'Hours must be a non-negative number. ';
            }

            if (!empty($errors)) {
                // If there are validation errors, we can display them to the user
                // and stop further processing.
                echo $errors;
                die();
            }

            if (!$errors) {
                $success = update_volunteerlog($id, $args);
                if (!$success) {
                    echo 'Failed to update event.';
                    die();
                }
                header('Location: log.php?id=' . $id);
                die();
            }
        }
    }

    if (!isset($_GET['id'])) {
        die();
    }

    $args  = sanitize($_GET);
    $id    = $args['id'];
    $event = fetch_volunteer_activity_by_id($id);
    if (!$event) {
        echo "Log does not exist";
        die();
    }

    $organizations = get_organizations_id_name();

    require_once('include/output.php');

    $con = connect();
?>
<!DOCTYPE html>
<html>
    <head>
        <?php require_once('universal.inc') ?>
        <title>Edit Volunteer Log</title>
    </head>
    <body>
        <?php require_once('header.php') ?>
        <h1>Edit Volunteer Log</h1>
        <main class="date">
        <?php if ($errors): ?>
            <div class="error-toast"><?php echo $errors ?></div>
        <?php endif ?>
            <h2>Log Details</h2>
            <form id="edit-log-form" method="post">
                
                <input type="hidden" name="id" value="<?php echo $id ?>"/> 
            
                <label for ="volunter_id">Volunteer ID</label>
                <input type="number" id="volunteer_id" name="volunteer_id"
                    value="<?php echo htmlspecialchars($log['volunteer_id']) ?>" required>

                <label for="date">Date</label>
                <input type="date" id="date" name="date"
                    value="<?php echo htmlspecialchars($log['date']) ?>" required>

                <label for="organization_id">Organization</label>
                <select id="organization_id" name="organization_id" required>
                    <option value="">Select an organization</option>
                    <?php foreach ($organizations as $org): ?>
                        <option value="<?php echo $org['id'] ?>"
                            <?php if ($org['id'] == $log['organization_id']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($org['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="hours">Hours</label>
                <input type="number" id="hours" name="hours" min="0"
                    value="<?php echo htmlspecialchars($log['hours']) ?>" required>

                <label for="location">Location</label>
                <input type="text" id="location" name="location"
                    value="<?php echo htmlspecialchars($log['location']) ?>">

                <label for="poundsOfFood">Pounds of Food</label>
                <input type="number" id="poundsOfFood" name="poundsOfFood" min="0" step="0.1"
                    value="<?php echo htmlspecialchars($log['poundsOfFood']) ?>">

                <label for="description">Description</label>
                <input type="text" id="description" name="description"
                    value="<?php echo htmlspecialchars($log['description']) ?>">
  

                <input type="submit" value="Update Log">
                <a class="button cancel" href="log.php?id=<?php echo htmlspecialchars($_GET['id']) ?>" style="margin-top: .5rem">Cancel</a>
            </form>
        </main>
    </body>
</html>

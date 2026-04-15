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
    // Require logged in
    if ($accessLevel < 1) {
        header('Location: login.php');
        echo 'bad access level';
        die();
    }

    require_once('include/input-validation.php');
    require_once('database/dbOrganizations.php');

    $errors = '';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $args = sanitize($_POST, null);
        $required = array("name", "archived");

        if (!wereRequiredFieldsSubmitted($args, $required)) {
            echo 'bad form data';
            var_dump($args);
            die();
        } else {
            $id = $args['id'];
            $name = $args['name'];
            $email = $args['email'];
            $description = $args['description'];
            $location = $args['location'];
            $archived = $args['archived'];

            if (!empty($errors)) {
                // If there are validation errors, we can display them to the user
                // and stop further processing.
                echo $errors;
                die();
            }

            if (!$errors) {
                $success = update_organization($id, $args);
                if (!$success) {
                    echo 'Failed to update organization.';
                    die();
                }
                header('Location: organization.php?id=' . $id);
                die();
            }
        }
    }

    if (!isset($_GET['id'])) {
        die();
    }

    $args  = sanitize($_GET);
    $id    = $args['id'];
    $org = fetch_organization_by_id($id);
    if (!$org) {
        echo "Organization does not exist";
        die();
    }

    require_once('include/output.php');

?>
<!DOCTYPE html>
<html>
    <head>
        <?php require_once('universal.inc') ?>
        <title>Edit Organization</title>
    </head>
    <body>
        <?php require_once('header.php') ?>
        <h1>Edit Organization</h1>
        <main class="date">
            <?php if ($errors): ?>
                <div class="error-toast"><?php echo $errors ?></div>
            <?php endif ?>
            <h2>Organization Details</h2>
            <form id="edit-org-form" method="post">
                
                <input type="hidden" name="id" value="<?php echo $id ?>"/> 

                <?php if ($accessLevel == 3): ?>
                <label for ="archived">Archival Status</label>
                <select id="archived" name="archived" required <?php if ($accessLevel < 3) { echo "disabled";} ?>>
                    <option value="0" <?php if (!$org['archived']) echo 'selected'; ?>>Active</option>
                    <option value="1" <?php if ($org['archived']) echo 'selected'; ?>>Archived</option>
                </select>
                <?php else: ?>
                    <input type="hidden" name="archived" value="<?php echo hsc($org['archived']); ?>" />
                <?php endif; ?>
            
                <label for ="name">Name</label>
                <input type="text" id="name" name="name"
                    value="<?php echo htmlspecialchars($org['name']) ?>" required>

                <label for="description">Description</label>
                <textarea id="description" name="description"><?php echo htmlspecialchars($org['description']) ?></textarea>

                <label for="location">Location</label>
                <input type="text" id="location" name="location"
                    value="<?php echo htmlspecialchars($org['location']) ?>">

                <label for="email">Email</label>
                <input type="email" id="email" name="email"
                    value="<?php echo htmlspecialchars($org['email']) ?>">

                <input type="submit" value="Update Organization">
                <a class="button cancel" href="organization.php?id=<?php echo htmlspecialchars($_GET['id']) ?>" style="margin-top: .5rem">Cancel</a>
            </form>
        </main>
    </body>
</html>
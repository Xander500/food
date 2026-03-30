<?php 

    session_cache_expire(30);
    session_start();

    // Ensure user is logged in
    if (!isset($_SESSION['access_level']) || $_SESSION['access_level'] < 1) {
        //header('Location: login.php');
        //die();
    }

    //get login and permissions
    $loggedIn = false;
    $accessLevel = 0;
    $userID = null;
    if (isset($_SESSION['_id'])) {
        $loggedIn = true;
        // 0 = not logged in, 1 = student, 3 instructor
        $accessLevel = $_SESSION['access_level'];
        $userID = $_SESSION['_id'];
    } 
    // must be a logged in user
    if ($accessLevel < 1) {
        header('Location: login.php');
        echo 'bad access level';
        die();
    }

    require_once('include/input-validation.php');
    $args = sanitize($_GET);
    $displayUpdateMessage = false;
    if (isset($args["id"])) {
        $id = $args["id"];
    } else {
        header('Location: viewAllLogs.php');
        die();
  	}

    if (isset($args["update"])) {
        $displayUpdateMessage = true;
    }
  	
  	include_once('database/dbOrganizations.php');
  	
    // We need to check for a bad ID here before we query the db
    // otherwise we may be vulnerable to SQL injection(!)
  	$org_info = fetch_organization_by_id($id);
    if ($org_info == NULL) {
        //! TODO: Need to create error page for no event found
        // header('Location: ___.php');

        // Lauren: changing this to a more specific error message for testing
        echo 'bad event ID';
        die();
    }

    if(isset($_SESSION['access_level'])) {
        $access_level = $_SESSION['access_level'];
    }    
?>

<!DOCTYPE html>
<html>

<head>
    <?php 
        require_once('universal.inc');
    ?>
    <title>UMW Alleviating Food Waste Volunteer Tracking | <?php echo $org_info['name'] ?></title>
    <link rel="stylesheet" href="event.css" type="text/css" />
    <?php if (isset($_SESSION['access_level']) && $access_level >= 2) : ?>
        <script src="js/event.js"></script>
    <?php endif ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <?php require_once('header.php') ?>
    <main class="org-info">
        <!-- Success notifications -->
        <?php if (isset($_GET['createSuccess'])): ?>
            <div class="happy-toast">Organization created successfully!</div>
        <?php endif ?>
        <?php if (isset($_GET['editSuccess'])): ?>
            <div class="happy-toast">Organization details updated successfully!</div>
        <?php endif ?>
        <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (isset($_POST['createSuccess'])) {
                    echo "<div class='happy-toast'>Organization created successfully!</div>";
                }
                else if (isset($_POST['editSuccess'])) {
                    echo "<div class='happy-toast'>Organization details updated successfully!</div>";
                }
            }
        ?>
        <!---->
        
        <?php
            require_once('include/output.php');
            $id = $org_info['id'];
            $name = $org_info['name'];
            $description = $org_info['description'];
            $location = $org_info['location'];
            $email = $org_info['email'];
        ?>

        <!-- Organization Information Table -->
        <h2 class="org-head">
            Organization Details
        <a href="editOrganization.php?id=<?= $id ?>" title="Edit Organization" class="edit-icon">
            <i class="fas fa-pencil-alt" style="color: var(--main-color);"></i>
        </a>
        <?php 
            $confirmText = "Are you sure you want to delete this data?  This action is permanant and irrecoverable.";
            //! change for edit buttons for instructor
            if ( $accessLevel >= 2): ?>
                <a href="deleteOrganization.php?id=<?= $id ?>" title="Delete Organization" class="delete-icon"
                    onclick="return confirm('<?= htmlspecialchars($confirmText, ENT_QUOTES) ?>');">
                    <i class="fas fa-trash" style="color: var(--main-color);"></i>
                </a>
        <?php endif; ?>
        </h2>

        <div id="table-wrapper">
            <table>
                <tr>  
                    <td class="label">Name</td>
                    <td><?php echo $name; ?></td>
                </tr>
                <tr>
                    <td class="label">Description</td>
                    <td><?php echo $description; ?></td>
                </tr>
                <tr>
                    <td class="label">Location</td>
                    <td><?php echo $location; ?></td>
                </tr>
                <tr>
                    <td class="label">Email</td>
                    <td><?php echo $email; ?></td>
                </tr>
            </table>
        </div>

        <a class="button cancel" href="organizationManagement.php" style="margin-left: auto; margin-right: auto;">Manage Organizations</a>
        <a class="button cancel" href="index.php" style="margin-left: auto; margin-right: auto;">Return to Dashboard</a>

    </main>
</body>
</html>
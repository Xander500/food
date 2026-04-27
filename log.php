<?php 

    session_cache_expire(30);
    session_start();


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
    require_once('include/output.php');
    $args = sanitize($_GET);
    $displayUpdateMessage = false;
    if (!isset($args["id"])) {
        header('Location: viewAllLogs.php');
        die();
  	}
    $id = $args["id"]; //log id


    if (isset($args["update"])) {
        $displayUpdateMessage = true;
    }
  	
  	include_once('database/dbVolunteerActivity.php');
    include_once('database/dbUsers.php');
    include_once('database/dbOrganizations.php');
  	
    // We need to check for a bad ID here before we query the db
    // otherwise we may be vulnerable to SQL injection(!)
  	$log_info = fetch_volunteer_activity_by_id($id);
    if ($log_info == NULL) {
        //! TODO: Need to create error page for no event found
        // header('Location: ___.php');

        echo 'bad log ID';
        die();
    }


    ini_set("display_errors",1);
    error_reporting(E_ALL);
?>

<!DOCTYPE html>
<html>

<head>
    <?php 
        require_once('universal.inc');
    ?>
    <title>UMW Alleviating Food Waste Volunteer Tracking | Log <?php echo hsc($log_info['id']) ?></title>
    <link rel="stylesheet" href="event.css" type="text/css" />
    <?php if ($accessLevel >= 2) : ?>
        <script src="js/event.js"></script>
    <?php endif ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <?php require_once('header.php') ?>
    <!-- <h1>View Volunteer Activity</h1> -->
    <main class="event-info">
        <!-- Success notifications -->
        <?php if (isset($_GET['createSuccess'])): ?>
            <div class="happy-toast">Volunteer Activity Log created successfully!</div>
        <?php endif ?>
        <?php if (isset($_GET['editSuccess'])): ?>
            <div class="happy-toast">Volunteer Activity Log details updated successfully!</div>
        <?php endif ?>
        <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (isset($_POST['createSuccess'])) {
                    echo "<div class='happy-toast'>Volunteer Activity Log created successfully!</div>";
                }
                else if (isset($_POST['editSuccess'])) {
                    echo "<div class='happy-toast'>Volunteer Activity Log details updated successfully!</div>";
                }
            }
        ?>
        <!---->
        
        <?php
            require_once('include/output.php');
            $log_date = date('l F j, Y', strtotime($log_info['date']));
            $log_hours = $log_info['hours'];
            $log_id = $log_info['id'];
            $log_poundsOfFood = $log_info['poundsOfFood'];
            $log_description = $log_info['description'];
            $log_location = $log_info['location'];
            $log_volunteer_id = $log_info['volunteerID'];
            $log_organization_id = $log_info['organizationID'];
            $log_archival = ($log_info['archived'] == '1');

            $log_studentName = get_user_full_name_from_id($log_volunteer_id);
            $log_organizationName = get_organization_name_from_id($log_organization_id);
            require_once('include/time.php');
        ?>

        <!-- Event Information Table -->
        <h2 class="event-head">
            Volunteer Activity Details
            <?php  
            $confirmText = "Are you sure you want to delete this data?  This action is permanant and irrecoverable.";
            //! show edit buttons for instructor or student who owns the log
            if ($accessLevel ===3 || $userID === $log_info['volunteerID']): ?>
                <a href="editLog.php?id=<?= hsc($id) ?>" title="Edit Log" class="edit-icon">
                    <i class="fas fa-pencil-alt" style="color: var(--main-color);"></i>
                </a>
                <a href="deleteLog.php?id=<?= hsc($id) ?>" title="Delete Log" class="delete-icon"
                    onclick="return confirm('<?= htmlspecialchars($confirmText, ENT_QUOTES) ?>');"> 
                    <i class="fas fa-trash" style="color: var(--main-color);"></i>
                </a>
        <?php endif; ?>
        </h2>
        <p class="log-status-msg">
            <?php if (!$log_archival): ?>
            <img class="button-icon" src="images/check-circle.svg" alt="Active Icon" style="margin-right: 5px;">
            This log is active.
            <?php else: ?>
            <img class="button-icon" src="images/archive.svg" alt="Archive Icon" style="margin-right: 5px;">
            This log is archived.
            <?php endif; ?>
        </p>

        <div id="table-wrapper">
            <table>
                <tr>  
                    <td class="label">Volunteer</td>
                    <td><?php echo hsc($log_studentName); ?></td>
                </tr>
                <tr>
                    <td class="label">Date</td>
                    <td><?php echo hsc($log_date); ?></td>
                </tr>
                <tr>
                    <td class="label">Organization</td>
                    <td><?php echo hsc($log_organizationName); ?></td>
                </tr>
                <tr>
                    <td class="label">Hours</td>
                    <td><?php echo hsc($log_hours); ?></td>
                </tr>
                <tr>
                    <td class="label">Location</td>
                    <td>
                        <?php echo wordwrap(hsc($log_location), 50, "<br />\n"); ?>
                    </td>
                </tr>
                <tr>
                    <td class="label">Food Rescued (lbs)</td>
                    <td><?php echo hsc($log_poundsOfFood); ?></td>
                </tr>
                <tr>
                    <td class="label">Description</td>
                    <td>
                        <?php echo wordwrap(hsc($log_description), 50, "<br />\n"); ?>
                    </td>
                </tr>

            </table>
        </div>

        <a class="button cancel" href="addLog.php" style="margin-left: auto; margin-right: auto;">Add Volunteer Actitvity</a>
        <a class="button cancel" href="index.php" style="margin-left: auto; margin-right: auto;">Return to Dashboard</a>

    </main>
</body>
</html>

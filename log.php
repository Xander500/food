<?php 

    session_cache_expire(30);
    session_start();
    $_SESSION['access_level'] = 2;

    // Ensure user is logged in
    if (!isset($_SESSION['access_level']) || $_SESSION['access_level'] < 1) {
        //header('Location: login.php');
        //die();
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
  	
  	include_once('database/dbVolunteerActivity.php');
    include 'database/dbUsers.php';
    include 'database/dbOrganizations.php';
  	
    // We need to check for a bad ID here before we query the db
    // otherwise we may be vulnerable to SQL injection(!)
  	$log_info = fetch_volunteer_activity_by_id($id);
    if ($log_info == NULL) {
        //! TODO: Need to create error page for no event found
        // header('Location: ___.php');

        // Lauren: changing this to a more specific error message for testing
        echo 'bad event ID';
        die();
    }

    include_once('database/dbUsers.php');
    if(isset($_SESSION['access_level'])) {
        $access_level = $_SESSION['access_level'];
    }

    ini_set("display_errors",1);
    error_reporting(E_ALL);
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $args = sanitize($_POST);
        $get = sanitize($_GET);
        if (isset($_POST['attach-post-media-submit'])) {
            if ($access_level < 2) {
                echo 'forbidden';
                die();
            }
            $required = [
                'url', 'description', 'format', 'id'
            ];
            if (!wereRequiredFieldsSubmitted($args, $required)) {
                echo "dude, args missing";
                die();
            }
            $type = 'post';
            $format = $args['format'];
            $url = $args['url'];
            if ($format == 'video') {
                $url = convertYouTubeURLToEmbedLink($url);
                if (!$url) {
                    echo "bad video link";
                    die();
                }
            } else if (!validateURL($url)) {
                echo "bad url";
                die();
            }
            $eid = $args['id'];
            $description = $args['description'];
            if (!valueConstrainedTo($format, ['link', 'video', 'picture'])) {
                echo "dude, bad format";
                die();
            }
            attach_post_event_media($eid, $url, $format, $description);
            header('Location: event.php?id=' . $id . '&attachSuccess');
            die();
        }
    } else { //method = get
        if (isset($args["request_type"])) {
            $eventID = $args["id"];
    
            // Check if Get request from user is from an organization member
            // (volunteer, admin/super admin)
            if ($request_type == 'add self' && $access_level >= 1) {
                if (!$active) {
                    echo 'forbidden';
                    die();
                }
                $volunteerID = $args['selected_id'];
                $person = retrieve_person($volunteerID);
                $name = $person->get_first_name() . ' ' . $person->get_last_name();
                $name = htmlspecialchars_decode($name);
                require_once('database/dbMessages.php');
                require_once('include/output.php');
                $event = fetch_event_by_id($eventID);
                
                $eventName = htmlspecialchars_decode($event['name']);
                $eventDate = date('l, F j, Y', strtotime($event['date']));
                $eventStart = time24hto12h($event['start-time']);
                $eventEnd = time24hto12h($event['end-time']);
                system_message_all_admins("$name signed up for an event!", "Exciting news!\r\n\r\n$name signed up for the [$eventName](event: $eventID) event from $eventStart to $eventEnd on $eventDate.");
                // Check if GET request from user is from an admin/super admin
            // (Only admins and super admins can add another user)
            } else if ($request_type == 'add another' && $access_level > 1) {
                $volunteerID = strtolower($args['selected_id']);
                if ($volunteerID == 'vmsroot') {
                    echo 'invalid user id';
                    die();
                }
                require_once('database/dbMessages.php');
                require_once('include/output.php');
                $event = fetch_event_by_id($eventID);
                $eventName = htmlspecialchars_decode($event['name']);
                $eventDate = date('l, F j, Y', strtotime($event['date']));
                $eventStart = time24hto12h($event['startTime']);
                $eventEnd = time24hto12h($event['endTime']);
                send_system_message($volunteerID, 'You were assigned to an event!', "Hello,\r\n\r\nYou were assigned to the [$eventName](event: $eventID) event from $eventStart to $eventEnd on $eventDate.");
            } else {
                header('Location: event.php?id='.$eventID);
                die();
            }
        }
    }
?>

<!DOCTYPE html>
<html>

<head>
    <?php 
        require_once('universal.inc');
    ?>
    <title>Volunteer Impact Tracking System | Log <?php echo $log_info['id'] ?></title>
    <link rel="stylesheet" href="event.css" type="text/css" />
    <?php if (isset($_SESSION['access_level']) && $access_level >= 2) : ?>
        <script src="js/event.js"></script>
    <?php endif ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <?php require_once('header.php') ?>
    <h1>View Volunteer Activity</h1>
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

            $log_studentName = get_user_full_name_from_id($log_volunteer_id);
            $log_organizationName = get_organization_name_from_id($log_organization_id);
            require_once('include/time.php');
        ?>

        <!-- Event Information Table -->
        <h2 class="event-head">
            Volunteer Activity Details
            <?php 
            //! change for edit buttons for instructor
            if (isset($_SESSION['access_level']) && $access_level >= 2): ?>
                <a href="editEvent.php?id=<?= $id ?>" title="Edit Event" class="edit-icon">
                    <i class="fas fa-pencil-alt"></i>
                </a>
                <a href="deleteEvent.php?id=<?= $id ?>" title="Delete Event" class="delete-icon"
                    onclick="return confirm('<?= htmlspecialchars($confirmText, ENT_QUOTES) ?>');">
                    <i class="fas fa-trash"></i>
                </a>
        <?php endif; ?>
        </h2>

        <div id="table-wrapper">
            <table>
                <tr>  
                    <td class="label">Volunteer</td>
                    <td><?php echo $log_studentName; ?></td>
                </tr>
                <tr>
                    <td class="label">Date</td>
                    <td><?php echo $log_date; ?></td>
                </tr>
                <tr>
                    <td class="label">Organization</td>
                    <td><?php echo $log_organizationName; ?></td>
                </tr>
                <tr>
                    <td class="label">Hours</td>
                    <td><?php echo $log_hours; ?></td>
                </tr>
                <tr>
                    <td class="label">Location</td>
                    <td>
                        <?php echo wordwrap($log_location, 50, "<br />\n"); ?>
                    </td>
                </tr>
                <tr>
                    <td class="label">Food Rescued (lbs)</td>
                    <td><?php echo $log_poundsOfFood; ?></td>
                </tr>
                <tr>
                    <td class="label">Description</td>
                    <td>
                        <?php echo wordwrap($log_description, 50, "<br />\n"); ?>
                    </td>
                </tr>

            </table>
        </div>

        <?php
        
        //! check
        if (isset($_SESSION['access_level']) && $access_level >= 2) : ?>
            <div id="delete-confirmation-wrapper" class="modal hidden">
                <div class="modal-content">
                    <p>Are you sure you want to delete this event?</p>
                    <p>This action cannot be undone.</p>
                    <form method="post" action="deleteEvent.php">
                        <input type="submit" value="Delete Event" class="button danger">
                        <input type="hidden" name="id" value="<?= $id ?>">
                    </form>
                    <button id="delete-cancel" class="button cancel">Cancel</button>
                </div>
            </div>

            <div id="complete-confirmation-wrapper" class="modal hidden">
                <div class="modal-content">
                    <p>Are you sure you want to complete this event?</p>
                    <p>This action cannot be undone.</p>
                    <form method="post" action="completeEvent.php">
                        <input type="submit" value="Archive Event" class="button">
                        <input type="hidden" name="id" value="<?= $id ?>">
                    </form>
                    <button id="complete-cancel" class="button cancel">Cancel</button>

                </div>
            </div>
            <?php endif ?>


            <?php if (isset($_SESSION['access_level']) && $access_level < 2) : ?>
                <div id="cancel-confirmation-wrapper" class="modal hidden">
                <div class="modal-content">
                    <p>Are you sure you want to cancel your sign-up for this event?</p>
                    <p>This action cannot be undone.</p>
                   <form method="post" action="cancelEvent.php">
                        <input type="submit" value="Cancel Sign-Up" class="button danger">
                        <input type="hidden" name="id" value="<?= $_REQUEST['id'] ?>">
                        <input type="hidden" name="user_id" value="<?= $_REQUEST['user_id'] ?>">
                    </form>
                    <button onclick="document.getElementById('cancel-confirmation-wrapper').classList.add('hidden')" id="cancel-cancel" class="button cancel">Cancel</button>
                </div>
            </div>
            <?php
        ?>
        <?php endif ?>

            

        <!-- Scripts for Modal Controls -->
        <script>
            function showDeleteConfirmation() {
                document.getElementById('delete-confirmation-wrapper').classList.remove('hidden');
            }
            function showCancelConfirmation() {
                document.getElementById('cancel-confirmation-wrapper').classList.remove('hidden');
            }
            function showCompleteConfirmation() {
                document.getElementById('complete-confirmation-wrapper').classList.remove('hidden');
            }
            document.getElementById('delete-cancel').onclick = function() {
                document.getElementById('delete-confirmation-wrapper').classList.add('hidden');
            };
            document.getElementById('cancel-cancel').onclick = function() {
                document.getElementById('cancel-confirmation-wrapper').classList.add('hidden');
            }
            document.getElementById('complete-cancel').onclick = function() {
                document.getElementById('complete-confirmation-wrapper').classList.add('hidden');
            };
        </script>
        <a class="button cancel" href="viewAllLogs.php">View All Volunteer Activity</a>
        <a class="button cancel" href="addEvent.php">Add Volunteer Actitvity</a>
        <a class="button cancel" href="index.php">Return to Dashboard</a>


    </main>
</body>
</html>
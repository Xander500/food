<?php session_cache_expire(30);
    session_start();
    // Make session information accessible, allowing us to associate
    // data with the logged-in user.

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
    if ($accessLevel < 1) {
        header('Location: login.php');
        //echo 'bad access level';
        die();
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        require_once('include/input-validation.php');
        require_once('database/dbVolunteerActivity.php');
        $args = sanitize($_POST, null);
        $required = array(
            "date", "hours", "description", "poundsOfFood", "organizationID", "location"
        );
        if (!wereRequiredFieldsSubmitted($args, $required)) {
            echo 'bad form data';
            die();
        } else {
            // Accept either HTML5 24h time (HH:MM) or 12h times with am/pm

            $date = $args['date'] = validateDate($args["date"]);


            // FIXED: Replaced the broken check "if (!$date > 11)"


            $args['series_id'] = bin2hex(random_bytes(16)); // new new

            //FOODDB alter so it passes input from from if its a teacher
            if ($accessLevel < 2)
                $id = create_activitylog($args);
            else
                $id = create_activitylog($args, false);

            if (!$id) {
                header('Location: eventFailure.php');
                die();
            } else {
                header('Location: eventSuccess.php');
                exit();
            }
        }
    }

    $date = null;
    if (isset($_GET['date'])) {
        $date = $_GET['date'];
        $datePattern = '/[0-9]{4}-[0-9]{2}-[0-9]{2}/';
        $timeStamp = strtotime($date);
        if (!preg_match($datePattern, $date) || !$timeStamp) {
            header('Location: calendar.php');
            die();
        }
    }

    include_once('database/dbinfo.php');
    $con=connect();

?><!DOCTYPE html>
<html>
    <head>
        <?php require_once('universal.inc') ?>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css"/>
        <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
        <title>UMW Alleviating Food Waste | Create Activity</title>

    </head>
    <body>
        <?php require_once('header.php') ?>
        <main class="date">
            <h2>New Activity Form</h2>
            <form id="new-event-form" method="POST">
                <!--
                <div class="event-sect">
                <label for="name">* Activity Name </label>
                <input type="text" id="name" name="name" required placeholder="Enter name">
                </div>
-->

<!--            //FOODDB only shows if it is an admin, otherwise we just take from the session what the Volunteer ID is-->
                <?php if ($_SESSION['access_level'] >= 2): ?>
                <div class="event-sect">
                    <label for="volunteerID">* Volunteer ID </label>
                    <select id="volunteerID" name="volunteerID" required placeholder="Enter Volunteer ID">
                        <?php
                        require_once('database/dbUsers.php'); // maybe put at top
                        $volunteers = retrieve_all();
                        while ($row = $volunteers->fetch_assoc()) {
                            echo "<option value='{$row['id']}'>" . htmlspecialchars($row['first_name']) . " " . htmlspecialchars($row['last_name']) . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <?php endif ?>

                <div class="event-sect">
                    <label for="date">* Date </label>
                    <input type="date" id="date" name="date"
                        <?php if ($date) echo 'value="' . $date . '"'; ?> required>
                    <label for="hours">* Duration (hours) </label>
                    <input type="number" id="hours" name="hours" in="1" max="99"
                        required placeholder="e.g. 2">
                </div>

                <div class="event-sect">
                <label for="description"> Description </label>
                <input type="text" id="description" name="description" placeholder="Enter description">

                <label for="poundsOfFood"> Pounds of Food </label>
                <input type="number" id="poundsOfFood" name="poundsOfFood" min="0" step="0.1" placeholder="Enter pounds of food">
                </div>

                <!--   Event visibility checkbox, not sure if we need it at all
                <div class="event-sect">
                <label for="name">* Event Visibility</label>
                <p class="sub-text" style="margin-bottom: 1rem;">Visibility controls who can see the event listing on the calendar.</p>
                <div class="radio-group">
                    <div class="radio-element">
                    <label>
                        <input type="radio" name="visibility" value="public" checked>Public
                    </label>
                    </div>
                    <div class="radio-element">
                    <label>
                        <input type="radio" name="visibility" value="private">Private
                    </label>
                    </div>
                </div>
                </div>
-->

                <div class="event-sect">
                    <label for="location">* Location </label>
                    <input type="text" id="location" name="location" placeholder="Enter location">

                    <label for="organizationID">* Organization </label>
                    <select id="organizationID" name="organizationID" required placeholder="Enter Organization ID">
<!--                        <option value="">Select organization</option>-->
                        <?php
                            require_once('database/dbOrganizations.php'); // maybe put at top
                            $organizations = get_organizations_id_name();
                            while ($row = $organizations->fetch_assoc()) {
                                echo "<option value='{$row['id']}'>" . htmlspecialchars($row['name']) . "</option>";
                            }
                        ?>
                    </select>
                </div>

                <input type="submit" value="Create Activity" style="width:100%;">

            </form>
                <script>
                    // Debug: log submit attempts and list invalid fields
                    (function(){
                        const form = document.getElementById('new-event-form');
                        if(!form) return;
                        form.addEventListener('submit', function(e){
                            try{
                                console.log('addEvent form submit event', e);
                                const ok = form.checkValidity();
                                console.log('form.checkValidity()', ok);
                                if(!ok){
                                    e.preventDefault();
                                    const invalids = [];
                                    form.querySelectorAll(':invalid').forEach(function(el){ invalids.push({name: el.name, type: el.type, value: el.value}); });
                                    console.error('Form invalid fields:', invalids);
                                    alert('Form validation failed for: ' + invalids.map(i=>i.name).join(', '));
                                } else {
                                    console.log('Form appears valid; letting submit proceed');
                                }
                            }catch(err){
                                console.error('Error in submit debug handler', err);
                            }
                        }, false);
                    })();
                </script>

                <?php if ($date): ?>
                    <a class="button cancel" href="calendar.php?month=<?php echo substr($date, 0, 7) ?>" style="margin-top: -.5rem">Return to Calendar</a>
                <?php else: ?>
                    <a class="button cancel" href="index.php" style="margin-top: -.5rem">Return to Dashboard</a>
                <?php endif ?>


                <script>
                    const organizationID = new Choices('#organizationID', {
                        searchEnabled: true,
                        removeItemButton: true,
                        placeholder: true,
                        placeholderValue: 'Select organizations',
                    });
                </script>
                <script>
                    const volunteerID = new Choices('#volunteerID', {
                        searchEnabled: true,
                        removeItemButton: true,
                        placeholder: true,
                        placeholderValue: 'Select volunteer',
                    });
                </script>


                <script type="text/javascript">
                    /* for checkboxes if we need them
                    $(document).ready(function(){
                        var checkboxes = $('.checkboxes');
                        checkboxes.change(function(){
                            if($('.checkboxes:checked').length>0) {
                                checkboxes.removeAttr('required');
                            } else {
                                checkboxes.attr('required', 'required');
                            }
                        });
                    });
                    */

                    /* Recurring event/activity options
                    (function(){
                        const recurring = document.getElementById('recurring');
                        const options = document.getElementById('recurring-options');
                        const recurrenceType = document.getElementById('recurrence_type');
                        const customBlock = document.getElementById('custom-interval');
                        const customDays = document.getElementById('custom_days');

                        function toggleOptions(){
                            const on = recurring && recurring.checked;
                            if (options) options.style.display = on ? 'block' : 'none';
                            if (!on) {
                                if (recurrenceType) recurrenceType.value = '';
                                if (customBlock) customBlock.style.display = 'none';
                                if (customDays) customDays.value = '';
                            }
                        }
                        function toggleCustom(){
                            if (!recurrenceType || !customBlock) return;
                            customBlock.style.display = (recurrenceType.value === 'custom') ? 'block' : 'none';
                            customBlock.style.display = (recurrenceType.value === 'custom') ? 'block' : 'none';
                        }

                        if (recurring) {
                            recurring.addEventListener('change', toggleOptions);
                            toggleOptions();
                        }
                        if (recurrenceType) {
                            recurrenceType.addEventListener('change', toggleCustom);
                            toggleCustom();
                        }
                    })();
                     */
                </script>
        </main>
    </body>
</html>

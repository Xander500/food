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
        // 0 = not logged in, 1 = student, 3 instructor
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
    include_once('include/output.php');
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
            <!-- FOODDB only shows if it is an admin, otherwise we just take from the session what the Volunteer ID is -->
                <?php if ($_SESSION['access_level'] >= 2): ?>
                <div class="event-sect">
                    <label for="volunteerID">* Volunteer ID </label>
                    <select id="volunteerID" name="volunteerID" required placeholder="Enter Volunteer ID">
                        <?php
                        require_once('database/dbUsers.php'); // maybe put at top
                        $volunteers = retrieve_all($want_archived=false);
                        while ($row = $volunteers->fetch_assoc()) {
                            if ($row['role'] == "Student") // only show volunteers
                            echo "<option value='" . hsc($row['id']) . "'>". hsc($row['first_name']) . " " . hsc($row['last_name']) . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <?php endif ?>

                <div class="event-sect">
                    <label for="date">* Date </label>
                    <input type="date" id="date" name="date"
                        <?php if ($date) echo 'value="' . hsc($date) . '"'; ?> required>
                    <label for="hours">* Duration (hours) </label>
                    <input type="number" id="hours" name="hours" min="0" step=".01" max="99"
                        required placeholder="e.g. 2">
                </div>

                <div class="event-sect">
                <label for="description"> Description </label>
                <input type="text" id="description" name="description" placeholder="Enter description">

                <label for="poundsOfFood"> Pounds of Food </label>
                <input type="number" id="poundsOfFood" name="poundsOfFood" min="0" step="0.01" max="9999" placeholder="Enter pounds of food">
                </div>

                <div class="event-sect">
                    <label for="location">Location </label>
                    <input type="text" id="location" name="location" placeholder="Enter location">
                    <input type="hidden" id="longitude" name="longitude">
                    <input type="hidden" id="latitude" name="latitude">
                    <div id="location-suggestions" style="position:relative;"></div>

                    <label for="organizationID">* Organization </label>
                    <select id="organizationID" name="organizationID" required placeholder="Enter Organization ID">
                        <?php
                            require_once('database/dbOrganizations.php'); // maybe put at top
                            $organizations = get_organizations_id_name($want_archived=false);
                            while ($row = $organizations->fetch_assoc()) {
                                echo "<option value='" . hsc($row['id']) . "'>". hsc($row['name']) . "</option>";
                            }
                        ?>
                    </select>
                </div>
                <div style="text-align: center;">
                    <input type="submit" value="Create Activity" style="width: 25%;">
                </div>
            </form>
        </main>

<script>
const organizationID = new Choices('#organizationID', {
    searchEnabled: true,
    removeItemButton: true,
    placeholder: true,
    placeholderValue: 'Select organizations',
    shouldSort: false,
});

const volunteerID = new Choices('#volunteerID', {
    searchEnabled: true,
    removeItemButton: true,
    placeholder: true,
    placeholderValue: 'Select volunteer',
    shouldSort: false,
});
 
const MAPTILER_KEY = 'EGKCnnMrNWBQqtJG1Izh';
const input = document.getElementById('location');
const box = document.getElementById('location-suggestions');

let timeout = null;

input.addEventListener('input', function () {
    clearTimeout(timeout);

    const query = this.value;

    if (query.length < 3) {
        box.innerHTML = '';
        return;
    }

    timeout = setTimeout(async () => {
        const url = `https://api.maptiler.com/geocoding/${encodeURIComponent(query)}.json?key=${MAPTILER_KEY}`;

        const res = await fetch(url);
        const data = await res.json();

        box.innerHTML = '';

        data.features.forEach(feature => {
            const div = document.createElement('div');

            div.textContent = feature.place_name;
            div.style.padding = '8px';
            div.style.cursor = 'pointer';
            div.style.background = '#fff';
            div.style.border = '1px solid #ddd';

            div.addEventListener('click', () => {
                input.value = feature.place_name;

                document.getElementById('latitude').value = feature.center[1];
                document.getElementById('longitude').value = feature.center[0];
                
                box.innerHTML = '';
            });

            box.appendChild(div);
        });

    }, 300);
});

document.addEventListener('click', function(e){
    if (e.target !== input) {
        box.innerHTML = '';
    }
});
</script>
    </body>
</html>

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
    if ($accessLevel < 2) {
        header('Location: login.php');
        //echo 'bad access level';
        die();
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        require_once('include/input-validation.php');
        require_once('database/dbEvents.php');
        $args = sanitize($_POST, null);
        $required = array(
            "name", "date", "hours", "description", "food", "organization"
        );
        if (!wereRequiredFieldsSubmitted($args, $required)) {
            echo 'bad form data';
            die();
        } else {
            // Accept either HTML5 24h time (HH:MM) or 12h times with am/pm
            
            $date = $args['date'] = validateDate($args["date"]);
            $args["training_level_required"] = $_POST['training_level_required'] ?? 'None';
    
            $args['startDate'] = $date;
            $args['endDate']   = $date;   
            $args['startTime'] = $startTime;
            $args['endTime']   = $endTime;


            //1. Start of use case #8 recurring, etc
            $isRecurring = isset($_POST['recurring']) ? 1 : 0;
            $recurrenceType = $isRecurring ? ($_POST['recurrence_type'] ?? '') : '';
            $customDays = ($isRecurring && $recurrenceType === 'custom') ? (int)($_POST['custom_days'] ?? 0) : null;

            
            if ($isRecurring) {
                if (!in_array($recurrenceType, ['daily','weekly','monthly','custom'], true)) {
                    echo 'invalid recurrence type';
                    die();
                }
                if ($recurrenceType === 'custom' && (!$customDays || $customDays < 1)) {
                    echo 'invalid custom interval';
                    die();
                }
                $args['is_recurring'] = 1;
                $args['recurrence_type'] = $recurrenceType;                  // daily|weekly|monthly|custom
                $args['recurrence_interval_days'] = ($recurrenceType === 'custom') ? $customDays : null;
            } else {
                $args['is_recurring'] = 0;
                $args['recurrence_type'] = null;
                $args['recurrence_interval_days'] = null;
            }
            //1. Start of use case #8 recurring, etc

            // FIXED: Replaced the broken check "if (!$date > 11)"
            if (!$startTime || !$endTime || !$date){
                echo 'bad args';
                die();
            }

            $args['series_id'] = bin2hex(random_bytes(16)); // new new

            $id = create_event($args);
            if (!$id) {
                die();
            } else {
    
                $counts = [
                    'daily'   => 30,  // next 30 days
                    'weekly'  => 12,  // next 12 weeks
                    'monthly' => 6,   // next 6 months
                    'custom'  => 12,  // 12 custom intervals
                ];
                
                $intervalMap = [
                    'daily'   => 'P1D',
                    'weekly'  => 'P1W',
                    'monthly' => 'P1M',
                ];
                if ($recurrenceType === 'custom') {
                    $customDays = max(1, $customDays);
                    $intervalSpec = 'P' . $customDays . 'D';
                } else {
                    $intervalSpec = $intervalMap[$recurrenceType] ?? null;
                }

                if ($isRecurring && $intervalSpec && isset($counts[$recurrenceType])) {
                    $current = new DateTime($args['startDate']);  
                    $step    = new DateInterval($intervalSpec);
                    $times   = $counts[$recurrenceType];

                    for ($i = 0; $i < $times; $i++) {
                        $current->add($step);
                        $ymd = $current->format('Y-m-d');

                        $dup = $args;                 
                        $dup['startDate'] = $ymd;
                        $dup['endDate']   = $ymd;
                        $dup['date']      = $ymd;    

                        create_event($dup);
                    }
                }
                
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
        <title>Whiskey Valor | Create Event</title>
    </head>
    <body>
        <?php require_once('header.php') ?>
        <h1 style="color: white;">Create Activity</h1>
        <main class="date">
            <h2>New Activity Form</h2>
            <form id="new-event-form" method="POST">
                <div class="event-sect">
                <label for="name">* Activity Name </label>
                <input type="text" id="name" name="name" required placeholder="Enter name"> 
                </div>

                <div class="event-sect">
                    <div class="event-datetime">
                        <div class="event-date">
                            <label for="date">* Date </label>
                            <input type="date" id="date" name="date" 
                                <?php if ($date) echo 'value="' . $date . '"'; ?> 
                                required>
                        </div>
                        <div class="event-date">
                            <label for="hours">* Duration (hours) </label>
                            <input type="number" id="hours" name="hours" 
                                in="1" max="99" required placeholder="e.g. 2">
                        </div>
                    </div>
                </div>

                <div class="event-sect">
                <label for="name">* Description </label>
                <input type="text" id="description" name="description" required placeholder="Enter description">

                <label for="food">* Pounds of Food </label>
                <input type="number" id="food" name="food" min="0" step="0.1" required placeholder="Enter pounds of food">
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
                <label for="name">Location </label>
                <input type="text" id="location" name="location" placeholder="Enter location">

                <label for="name">* Organization </label>
                <input type="text" id="organization" name="organization" required placeholder="Enter Organization Name">
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
                    })(); */
                </script>
        </main>
    </body>
</html>
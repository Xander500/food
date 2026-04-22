<?php
    // Template for new VMS pages. Base your new page on this one

    // Make session information accessible, allowing us to associate
    // data with the logged-in user.

use function PHPSTORM_META\type;

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
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>UMW Alleviating Food Waste Volunteer Tracking | Analytics Dashboard</title>
  <link rel="icon" type="image/x-icon" href="images/alleviatingFoodWasteLogo.png">

<!-- BANDAID FIX FOR HEADER BEING WEIRD -->
<?php
    $tailwind_mode = true;
    require_once('header.php');
    require_once('database/dbVolunteerActivity.php');
    include_once 'include/output.php';
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

  .top-bar {
      background-color: #C9AB81;   /* gold color */
      height: 200px;             /* height of the bar */
      width: 100%;              /* full width */
      position: fixed;
  }

  /* body {
    background-color: #1F1F21; 
  } */

  .button-left-gray {
    background-color: var(--accent-color) !important;
  }


 .button-section button {
    background-color: var(--accent-color) !important;
    color: var(--page-font-color) !important;
    border: none !important;
  }

.div-blue {
    background-color: var(--page-font-color);
  }

.button-icon {
    filter: brightness(0) saturate(100%) invert(14%) sepia(89%) saturate(465%) hue-rotate(167deg) brightness(103%) contrast(84%) !important;
  } 

.text-section h1 {
    color: var(--page-font-color) !important;
  }

.text-section p {
    color: var(--page-font-color) !important;
  }

.button-section button > div {
    background-color: transparent !important;
    font-weight: 550 !important;
  }

</style>
<!-- BANDAID END, REMOVE ONCE SOME GENIUS FIXES -->
</head>

<?php
    $hours = getTotalHours(isset($_GET['semester']) ? $_GET['semester'] : "All");
    $pounds = getTotalPounds(isset($_GET['semester']) ? $_GET['semester'] : "All");

    // function sem_sort($a, $b) {
    //     if ($a['semester'] == $b['semester']) {
    //         return 0;
    //     }
    //     if ((int)substr($a['semester'], -4) < (int)substr($b['semester'], -4)) {
    //         return 1;
    //     } else if ((int)substr($a['semester'], -4) > (int)substr($b['semester'], -4)){
    //         return -1;
    //     } else {
    //         if (str_contains($a['semester'], "Spring")) {
    //             return 1;
    //         } else {
    //             return -1;
    //         }
    //     }
    // }

    // $sems = get_semesters_in_users();
    // usort($sems, "sem_sort");

    $sems = get_years_logs();
    var_dump($sems);
?>

<body>
  <!-- Main Content -->
    <div style="display: inline;">
        <h1 class="impact-header">Analytics Dashboard</h1>
        <form class="analytics-semester-select" action="analyticsDashboard.php?" method="GET">
            <select id="semesterSelect" name="semester" onchange="this.form.submit()">
                <option <?php echo !isset($_GET['semester']) ? 'selected' : ''; ?>>All</option>
                <?php foreach ($sems as $row): ?>
                    <!-- <option value="<?php echo hsc($row['semester']); ?>" <?php echo isset($_GET['semester']) && $_GET['semester'] == $row['semester'] ? 'selected' : ''; ?>>
                        <?php echo hsc($row['semester']); ?>
                    </option> -->
                    <option value="<?php echo "Spring " . hsc($row[0]); ?>" <?php echo isset($_GET['semester']) && $_GET['semester'] == "Spring " . hsc($row[0]) ? 'selected' : ''; ?>>
                        <?php echo "Spring " . hsc($row[0]); ?>
                    </option>
                    <option value="<?php echo "Fall " . hsc($row[0]); ?>" <?php echo isset($_GET['semester']) && $_GET['semester'] == "Fall " . hsc($row[0]) ? 'selected' : ''; ?>>
                        <?php echo "Fall " . hsc($row[0]); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>
    <main class="analytics-body">
        <div class="display">
            <div>
                <div class="nums-display">
                    <div class="num">Total Hours Volunteered : <?php echo $hours != null ? round($hours, 2) : 0;?></div>
                    <div class="num">Total Pounds of Food Rescued: <?php echo $pounds != null ? round($pounds, 2) : 0; ?></div>
                    <div class="num"><a href="impactByStudent.php">Impact by Student</a></div>
                    <div class="num"><a href="impactByOrg.php">Impact by Organization</a></div>
                </div>
                <div class="map-container">
                    <h2>Volunteer Activity Map</h2>
                    <div class="map">
                        <?php include_once 'map.php'; ?>
                    </div>
                </div>
            </div>
            <div>
                <?php include_once 'monthlyImpact.php'; ?>
                <div class="text-center mt-6">
                    <a href="index.php" class="return-button" style="font-size: 1.5rem; box-shadow: 0px 0px 20px 5px var(--main-color);">Return to Dashboard</a>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
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
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>UMW Alleviating Food Waste Volunteer Tracking | Analytics Dashboard</title>
  <link rel="icon" type="image/x-icon" href="images/alleviatingFoodWasteLogo.png">

<!-- BANDAID FIX FOR HEADER BEING WEIRD -->
<?php
$tailwind_mode = true;
require_once('header.php');

require_once('database/dbVolunteerActivity.php');
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
    $hours = getTotalHours();
    $pounds = getTotalPounds();
?>

<body>
  <!-- Main Content -->
    <h1 class="impact-header">Analytics Dashboard</h1>
    <main style="margin: 25px;">
        <div class="display">
            <div class="num-displays">
                <div class="num">Total Hours Volunteered : <?php echo $hours;?></div>
                <div class="num">Total Pounds of Food Rescued: <?php echo $pounds;?></div>
            </div>

            <div class="num-displays">
                <div class="num"><a href="impactByStudent.php">Impact by Student</a></div>
                <div class="num"><a href="impactByOrg.php">Impact by Organization</a></div>
            </div>

            <div class="text-center mt-6">
                <a href="index.php" class="return-button">Return to Dashboard</a>
            </div>
        <div>
    </main>
</body>
</html>
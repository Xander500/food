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
  <title>UMW Alleviating Food Waste Volunteer Tracking | Impact By Student</title>
  <link href="css/management_tw.css" rel="stylesheet">
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
    $rows = getImpactByOrg();
?>

<body>
  <!-- Main Content -->
    <h1 class="impact-header">Impact By Organization</h1>
    <main style="margin: 25px;">
        <div class="impact">
            <table>
                <thead>
                    <tr>
                        <th>Organization</th>
                        <th>Hours</th>
                        <th>Pounds</th>
                        <th># of Logs</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        foreach ($rows as $row) {
                            echo "
                                <tr>
                                    <td>" . $row[0] . "</td>
                                    <td>" . round($row[1], 2) . "</td>
                                    <td>" . round($row[2], 2) . "</td>
                                    <td>" . $row[3] . "</td>
                                </tr>";
                        }
                    ?>
                </tbody>
            </table>
            <div class="text-center mt-6">
                    <a href="analyticsDashboard.php" class="return-button">Return to Analytics Dashboard</a>
            </div>
        </div>
    </main>
</body>
</html>
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
    if ($accessLevel < 1) {
        header('Location: index.php');
        die();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>UMW Alleviating Food Waste Volunteer Tracking | Organization Management Page</title>
  <link href="css/management_tw.css" rel="stylesheet">
  <link rel="icon" type="image/x-icon" href="images/alleviatingFoodWasteLogo.png">

<!-- BANDAID FIX FOR HEADER BEING WEIRD -->
<?php
$tailwind_mode = true;
require_once('header.php');
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
    background-color: #C9AB81 !important;
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

<body>
  <!-- Main Content -->
<main>
    <div class="sections">

        <!-- Buttons Section -->
        <div class="button-section">
            <?php
            if (isset($_GET['deleted']) || isset($_GET['failed'])) {
                $deleted = intval($_GET['deleted'] ?? 0);
                $failed = intval($_GET['failed'] ?? 0);

                if ($deleted > 0 || $failed > 0) {
                    echo '<div style="width:100%; margin-bottom:20px;">';
                    echo '<div style="width:100%; text-align:center; background:#d4edda; padding:12px 20px; border-radius:8px;">';

                    if ($deleted > 0) {
                        echo '<div>' . $deleted . ' organization(s) deleted successfully.</div>';
                    }

                    if ($failed > 0) {
                        echo '<div style="color:#b00020; margin-top:6px;">'
                            . $failed . ' organization(s) could not be deleted because they are linked to volunteer activity.'
                            . '</div>';
                    }

                    echo '</div>';
                    echo '</div>';
                }
            }
            ?>

          

            <!-- Buttons Section -->
                <button onclick="window.location.href='addOrganization.php';">
	                <div class="button-left-gray"></div>
	                <div>Create New Organization</div>
	                <img class="button-icon" src="images/add-person.svg" alt="Person Icon">
                </button>

                <button onclick="window.location.href='editOrganizationSearch.php';">
	                <div class="button-left-gray"></div>
                    <div>Edit Organizations</div>
                    <img class="button-icon" src="images/person-search.svg" alt="Person Icon">
                </button>

                <?php if ($accessLevel === 3): ?>
                <button onclick="window.location.href='deleteOrganizationSearch.php';">
                    <div class="button-left-gray"></div>
                    <div>Delete Organizations</div>
                    <img class="button-icon h-10 w-10 left-5" src="images/trash.svg" alt="Person Icon">
                </button>
                <?php endif; ?>
	
                <div class="text-center mt-6">
                        <a href="index.php" class="return-button">Return to Dashboard</a>
                </div>
		
            </div>

            <!-- Text Section -->
            <div class="text-section">
                <h1>Organization Management</h1>
                <div class="div-blue"></div>
                <p>
                Welcome to the organization management hub. From this menu, you will have access to operations such as creating, deleting, and searching organizations.
                </p>
            </div>

        
    </main>
</body>
</html>
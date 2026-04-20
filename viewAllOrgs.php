<?php
    session_cache_expire(30);
    session_start();

    $loggedIn = false;
    $accessLevel = 0;
    $userID = null;
    if (isset($_SESSION['_id'])) {
        $loggedIn = true;
        $accessLevel = $_SESSION['access_level'];
        $userID = $_SESSION['_id'];
    }

    if ($accessLevel < 1) {
        header('Location: login.php');
        die();
    }

    include_once 'database/dbOrganizations.php';
    include_once 'include/output.php';

    $orgs = fetch_organizations();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>UMW Alleviating Food Waste Volunteer Tracking | View All Organizations</title>
    <link rel="icon" type="image/x-icon" href="images/alleviatingFoodWasteLogo.png">
    <?php
    $tailwind_mode = true;
    require_once('header.php');
    ?>
</head>
<body>
<main>
    <div class="main-content-box">
        <div class="text-center mb-8">
            <h2>View All Organizations</h2>
            <div class="info-box">
                <p class="sub-text">Below is the full list of organizations.</p>
            </div>
        </div>

<div class="section-box mb-6" style="background-color:#92c44c; padding:25px; border-radius:10px; max-width:1000px; margin:0 auto;">
    <div class="table-wrapper">
        <table class="general" id="org-table" style="width:100%;">
            <thead>
                <tr>
                    <th style="width:20%;">Name</th>
                    <th style="width:20%;">Email</th>
                    <th style="width:20%;">Location</th>
                    <th style="width:40%;">Description</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($orgs): ?>
                    <?php while ($row = mysqli_fetch_assoc($orgs)): ?>
                        <tr>
                            <td><?php echo hsc($row['name']); ?></td>
                            <td><?php echo hsc($row['email']); ?></td>
                            <td><?php echo hsc($row['location']); ?></td>
                            <td><?php echo hsc($row['description']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">There are currently no organizations to view.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

        <div class="text-center" style="margin-top: 40px;">
            <a href="index.php" class="return-button">Return to Dashboard</a>
        </div>
    </div>
</main>
</body>
</html>
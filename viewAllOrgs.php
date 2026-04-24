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

        body, main {
            background-color: white;
        }

        .orgs-page-wrap {
            width: 100%;
            padding: 30px 5%;
        }

        .table-card {
            max-width: 1200px;
            margin: 0 auto;
        }

        .event-link {
            text-decoration: none;
            font-weight: 600;
        }
    </style>
</head>
<body>
<main>
    <div class="orgs-page-wrap">
        <div class="main-content-box">
            <div class="text-center mb-8">
                <h2>View All Organizations</h2>
                <div class="info-box">
                    <p class="sub-text">Below is the full list of organizations.</p>
                </div>
            </div>

            <div class="table-wrapper table-card">
                <table class="general" id="org-table">
                    <thead>
                        <tr>
                            <th style="width:4%;"></th>
                            <th style="width:20%;">Name</th>
                            <th style="width:22%;">Email</th>
                            <th style="width:20%;">Location</th>
                            <th style="width:34%;">Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($orgs): ?>
                            <?php while ($row = mysqli_fetch_assoc($orgs)): ?>
                                <tr>
                                    <td>
                                        <a href="organization.php?id=<?php echo hsc($row['id']); ?>" class="event-link">👁</a>
                                    </td>
                                    <td>
                                        <a href="organization.php?id=<?php echo hsc($row['id']); ?>" class="event-link">
                                            <?php echo hsc($row['name']); ?>
                                        </a>
                                    </td>
                                    <td><?php echo hsc($row['email']); ?></td>
                                    <td><?php echo hsc($row['location']); ?></td>
                                    <td><?php echo hsc($row['description']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5">There are currently no organizations to view.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="text-center" style="margin-top: 35px;">
                <a href="organizationManagement.php" class="return-button">Return to Organization Management</a>
            </div>
        </div>
    </div>
</main>
</body>
</html>
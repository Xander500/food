<?php
    session_cache_expire(30);
    session_start();

    require_once('database/dbUsers.php');
    require_once('include/output.php');

    $loggedIn = false;
    $accessLevel = 0;
    $userID = null;

    if (isset($_SESSION['_id'])) {
        $loggedIn = true;
        $accessLevel = $_SESSION['access_level'];
        $userID = $_SESSION['_id'];
    }

    // admin-only
    if ($accessLevel < 2) {
        header('Location: index.php');
        die();
    }

    $users = retrieve_all();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>UMW Alleviating Food Waste Volunteer Tracking | View All Users</title>
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

        .users-page-wrap {
            width: 100%;
            padding: 30px 5%;
        }

        .users-page-wrap h2 {
            text-align: center;
            margin-bottom: 10px;
        }

        .users-page-wrap .sub-text {
            text-align: center;
            margin-bottom: 25px;
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
    <div class="users-page-wrap">
        <div class="main-content-box">
            <div class="text-center mb-8">
                <h2>View All Users</h2>
                <div class="info-box">
                    <p class="sub-text">Below is the full list of active registered users.</p>
                </div>
            </div>

            <div class="table-wrapper table-card">
                <table class="general" id="user-table">
                    <thead>
                        <tr>
                            <th style="width:4%;"></th>
                            <th style="width:18%;">Name</th>
                            <th style="width:14%;">Username</th>
                            <th style="width:24%;">Email</th>
                            <th style="width:16%;">Semester</th>
                            <th style="width:12%;">Role</th>
                            <th style="width:12%;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($users && mysqli_num_rows($users) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($users)): ?>
                                <?php if ($row['id'] === 'vmsroot') continue; ?>
                                <tr>
                                    <td>
                                        <a href="viewProfile.php?id=<?php echo hsc($row['id']); ?>" class="event-link">👁</a>
                                    </td>
                                    <td>
                                        <a href="viewProfile.php?id=<?php echo hsc($row['id']); ?>" class="event-link">
                                            <?php echo hsc($row['first_name']) . ' ' . hsc($row['last_name']); ?>
                                        </a>
                                    </td>
                                    <td><?php echo hsc($row['id']); ?></td>
                                    <td><?php echo hsc($row['email']); ?></td>
                                    <td><?php echo hsc($row['semester']); ?></td>
                                    <td><?php echo hsc($row['role']); ?></td>
                                    <td><?php echo ($row['archived'] == 1) ? 'Archived' : 'Active'; ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7">There are currently no users to view.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="text-center" style="margin-top: 35px;">
                <a href="volunteerManagement.php" class="return-button">Return to User Management</a>
            </div>
        </div>
    </div>
</main>
</body>
</html>
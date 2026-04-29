<?php
    session_cache_expire(30);
    session_start();

    $loggedIn = false;
    $accessLevel = 0;
    $userID = null;

    if (!isset($_SESSION['access_level']) || $_SESSION['access_level'] < 1) {
        header('Location: login.php');
        die();
    }
    if (isset($_SESSION['_id'])) {
        $loggedIn = true;
        $accessLevel = $_SESSION['access_level'];
        $userID = $_SESSION['_id'];
    } else {
        header('Location: login.php');
        die();
    }

    $id = $userID;

    require_once('database/dbUsers.php');
    require_once('database/dbVolunteerActivity.php');
    require_once('include/output.php');

    $user = retrieve_user($id);

    $impact = get_impact_summary_by_volunteer($id);
    $totalHours   = $impact['total_hours']   ?? 0;
    $totalPounds  = $impact['total_pounds']  ?? 0;
    $totalLogs    = $impact['total_logs']    ?? 0;

    $orgBreakdown  = get_impact_summary_by_organization($id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>UMW Alleviating Food Waste Volunteer Tracking | Personal Impact Summary</title>
  <link rel="icon" type="image/x-icon" href="images/alleviatingFoodWasteLogo.png">
  <script src="https://cdn.tailwindcss.com"></script>
  <?php require_once('header.php'); ?>
</head>

<body>

  <?php if ($id === 'vmsroot'): ?>
    <div class="absolute left-[40%] top-[20%] bg-red-800 p-4 text-white rounded-xl text-xl">The root user does not have a profile.</div>
    </body></html>
    <?php die(); endif; ?>

  <?php if (!$user): ?>
    <div class="absolute left-[40%] top-[20%] bg-red-800 p-4 text-white rounded-xl text-xl">User does not exist.</div>
    </body></html>
  <?php die(); endif; ?>

  <!-- Profile Content -->
  <div class="impact-body">
    <!-- Left Box -->
    <div class="impact-body-left">
      <div>
        <h2 class="profile-name">My Impact</h2>
        <div class="profile-info">
          <div class="profile-info-item">
            <span>Joined</span>
            <span><?php echo hsc(date('m/d/Y', strtotime($user->get_start_date()))) ?></span>
          </div>
          <div class="profile-info-item">
            <span>Semester</span>
            <span><?php echo hsc($user->get_semester()) ?></span>
          </div>
        </div>

        <div class="profile-impact-nums">
          <div class="profile-impact-num">
            <p class="profile-impact-num-top"><?php echo number_format((float)$totalHours, 1) ?></p>
            <p>Total Hours Volunteered</p>
          </div>
          <div class="profile-impact-num">
            <p class="profile-impact-num-top"><?php echo number_format((float)$totalPounds, 1) ?></p>
            <p>Pounds of Food Rescued</p>
          </div>
          <div class="profile-impact-num">
            <p class="profile-impact-num-top"><?php echo (int)$totalLogs ?></p>
            <p>Activity Logs Submitted</p>
          </div>
        </div>
      </div>

      <div class="profile-buttons">
        <button style="width: 65%;" onclick="window.location.href='viewProfile.php';">View Profile</button>
        <button style="width: 65%;" onclick="window.location.href='index.php';">Return to Homepage</button>
      </div>
    </div>

    <div class="impact-body-right">
      <div class="profile-tab-header">
        <h3 class="tab-button profile-tab">Breakdown by Organization</h3>
      </div>

      <?php if (empty($orgBreakdown)): ?>
        <p class="text-gray-500 italic">No activity logged yet.</p>
      <?php else: ?>
        <div>
          <table>
            <thead>
              <tr>
                <th>Organization</th>
                <th>Hours</th>
                <th>Lbs. of Food</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($orgBreakdown as $row): ?>
                <tr>
                  <td><?php echo hsc($row['organization_name']) ?></td>
                  <td><?php echo number_format((float)$row['hours'], 1) ?></td>
                  <td><?php echo number_format((float)$row['pounds'], 1) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
            <tfoot>
              <tr>
                <td>Total:</td>
                <td><?php echo number_format((float)$totalHours, 1) ?></td>
                <td><?php echo number_format((float)$totalPounds, 1) ?></td>
              </tr>
            </tfoot>
          </table>
        </div>
      <?php endif; ?>
      </div>
    </div>
  </div>
</body>
</html>

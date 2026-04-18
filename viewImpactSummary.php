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
  <script src="https://cdn.tailwindcss.com"></script>
  <?php require_once('header.php'); ?>
</head>

<body class="bg-gray-100">

  <?php if ($id === 'vmsroot'): ?>
    <div class="absolute left-[40%] top-[20%] bg-red-800 p-4 text-white rounded-xl text-xl">The root user does not have a profile.</div>
    </body></html>
    <?php die(); endif; ?>

  <?php if (!$user): ?>
    <div class="absolute left-[40%] top-[20%] bg-red-800 p-4 text-white rounded-xl text-xl">User does not exist.</div>
    </body></html>
    <?php die(); endif; ?>

  <!-- Hero Banner -->
  <div class="h-48 relative" style="background-color: var(--page-background-color);"></div>

  <!-- Profile Content -->
  <div class="max-w-6xl mx-auto px-4 -mt-20 relative z-10 flex flex-col md:flex-row gap-6">

    <!-- Left Box -->
    <div class="w-full md:w-1/3 bg-white border border-gray-300 rounded-2xl shadow-lg p-6 flex flex-col justify-between">
      <div>
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-xl font-semibold">My Impact</h2>
        </div>

        <div class="space-y-2 divide-y divide-gray-300">
          <div class="flex justify-between py-2">
            <span class="font-medium">Joined</span>
            <span><?php echo hsc(date('m/d/Y', strtotime($user->get_start_date()))) ?></span>
          </div>
          <div class="flex justify-between py-2">
            <span class="font-medium">Semester</span>
            <span><?php echo hsc($user->get_semester()) ?></span>
          </div>
        </div>

        <div class="mt-6 space-y-3">
          <div class="rounded-xl p-4 text-center" style="background-color: #1F1F21;">
            <p class="text-3xl font-bold" style="color: #C9AB81;"><?php echo number_format((float)$totalHours, 1) ?></p>
            <p class="text-sm font-medium text-gray-300 mt-1">Total Hours Volunteered</p>
          </div>
          <div class="rounded-xl p-4 text-center" style="background-color: #1F1F21;">
            <p class="text-3xl font-bold" style="color: #92c44c;"><?php echo number_format((float)$totalPounds, 1) ?></p>
            <p class="text-sm font-medium text-gray-300 mt-1">Pounds of Food Rescued</p>
          </div>
          <div class="rounded-xl p-4 text-center" style="background-color: #1F1F21;">
            <p class="text-3xl font-bold text-white"><?php echo (int)$totalLogs ?></p>
            <p class="text-sm font-medium text-gray-300 mt-1">Activity Logs Submitted</p>
          </div>
        </div>
      </div>

      <div class="mt-6 space-y-2">
        <button onclick="window.location.href='viewProfile.php';"
          class="text-lg font-medium w-full px-4 py-2 bg-[#92c44c] text-[#1F1F21] rounded-md hover:bg-[#1F1F21] hover:text-[#C9AB81] cursor-pointer">
          View Profile
        </button>
        <button onclick="window.location.href='index.php';"
          class="text-lg font-medium w-full px-4 py-2 border-2 border-gray-300 text-black rounded-md hover:border-[#1F1F21] cursor-pointer">
          Return to Dashboard
        </button>
      </div>
    </div>

    <div class="w-full md:w-2/3 flex flex-col gap-6">

      <div class="bg-white rounded-2xl shadow-lg border border-gray-300 p-6">
        <div class="flex border-b border-gray-300 mb-4">
          <h3 class="text-lg font-medium text-[#2B2B2E] border-b-4 border-[#C9AB81] px-1 pb-2">
            Breakdown by Organization
          </h3>
        </div>

        <?php if (empty($orgBreakdown)): ?>
          <p class="text-gray-500 italic">No activity logged yet.</p>
        <?php else: ?>
          <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
              <thead>
                <tr class="text-xs font-semibold uppercase text-gray-500 border-b border-gray-200">
                  <th class="py-2 pr-4">Organization</th>
                  <th class="py-2 pr-4 text-right">Hours</th>
                  <th class="py-2 text-right">Lbs. of Food</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100">
                <?php foreach ($orgBreakdown as $row): ?>
                  <tr class="hover:bg-gray-50">
                    <td class="py-2 pr-4 font-medium text-gray-800"><?php echo hsc($row['organization_name']) ?></td>
                    <td class="py-2 pr-4 text-right text-gray-700"><?php echo number_format((float)$row['hours'], 1) ?></td>
                    <td class="py-2 text-right text-gray-700"><?php echo number_format((float)$row['pounds'], 1) ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
              <tfoot>
                <tr class="border-t-2 border-gray-300 font-semibold text-[#1F1F21]">
                  <td class="py-2 pr-4">Total</td>
                  <td class="py-2 pr-4 text-right"><?php echo number_format((float)$totalHours, 1) ?></td>
                  <td class="py-2 text-right"><?php echo number_format((float)$totalPounds, 1) ?></td>
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

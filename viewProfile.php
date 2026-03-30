<?php
    // Make session information accessible, allowing us to associate
    // data with the logged-in user.
    session_cache_expire(30);
    session_start();

    $loggedIn = false;
    $accessLevel = 0;
    $userID = null;
    $isAdmin = false;
    if (!isset($_SESSION['access_level']) || $_SESSION['access_level'] < 1) {
        header('Location: login.php');
        die();
    }
    if (isset($_SESSION['_id'])) {
        $loggedIn = true;
        // 0 = not logged in, 1 = standard user, 2 = manager (Admin), 3 super admin (TBI)
        $accessLevel = $_SESSION['access_level'];
        $isAdmin = $accessLevel >= 2;
        $userID = $_SESSION['_id'];
    } else {
        header('Location: login.php');
        die();
    }
    if ($isAdmin && isset($_GET['id'])) {
        require_once('include/input-validation.php');
        $args = sanitize($_GET);
        $id = strtolower($args['id']);
    } else {
        $id = $userID;
    }
    require_once('database/dbUsers.php');
    //if (isset($_GET['removePic'])) {
     // if ($_GET['removePic'] === 'true') {
       // remove_profile_picture($id);
      //}
    //}

   $user = retrieve_user($id);

   if ($isAdmin && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_hours'])) {
    require_once('database/dbUsers.php'); // already required, so you can just remove the duplicate
    $con = connect();

    $newHours = floatval($_POST['new_hours']);
    $safeID = mysqli_real_escape_string($con, $id);

    $update = mysqli_query($con, "
        UPDATE dbpersons 
        SET total_hours_volunteered = $newHours 
        WHERE id = '$safeID'
    ");

    if ($update) {
        $user = retrieve_user($id); // refresh with updated hours
        echo '
        <div id="success-message" class="absolute left-[40%] top-[15%] z-50 bg-green-800 p-4 text-white rounded-xl text-xl">
          Hours updated successfully!
        </div>
        <script>
          setTimeout(() => {
            const msg = document.getElementById("success-message");
            if (msg) msg.remove();
          }, 3000);
        </script>
        ';
    } else {
        echo '<div class="absolute left-[40%] top-[15%] z-50 bg-red-800 p-4 text-white rounded-xl text-xl">Failed to update hours.</div>';
    }
  
}

    $viewingOwnProfile = $id == $userID;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if (isset($_POST['url'])) {
        if (!update_profile_pic($id, $_POST['url'])) {
          header('Location: viewProfile.php?id='.$id.'&picsuccess=False');
        } else {
          header('Location: viewProfile.php?id='.$id.'&picsuccess=True');
        }
      }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Whiskey Valor | Profile Page</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    function showSection(sectionId) {
      const sections = document.querySelectorAll('.profile-section');
      sections.forEach(section => section.classList.add('hidden'));
      document.getElementById(sectionId).classList.remove('hidden');

      const tabs = document.querySelectorAll('.tab-button');
      tabs.forEach(tab => {
        tab.classList.remove('border-b-4', 'border-[#C9AB81]');
        tab.classList.add('hover:border-b-2', 'hover:border-[#C9AB81]');
      });

      const activeTab = document.querySelector(`[data-tab="${sectionId}"]`);
      activeTab.classList.add('border-b-4', 'border-[#C9AB81]');
      activeTab.classList.remove('hover:border-b-2', 'hover:border-[#C9AB81]');
    }

    window.onload = () => showSection('personal');
  </script>
  <?php 
    require_once('header.php'); 
    require_once('include/output.php');
  ?>

    <script>

      function openModal(modalID) {
          document.getElementById(modalID).classList.remove('hidden');
      }

      function closeModal(modalID) {
          document.getElementById(modalID).classList.add('hidden');
      }

      window.onload = () => showSection('personal');
  </script>

</head>
            <?php if ($id == 'vmsroot'): ?>
		<div class="absolute left-[40%] top-[20%] bg-red-800 p-4 text-white rounded-xl text-xl">The root user does not have a profile.</div>
                </main></body></html>
                <?php die() ?>
            <?php elseif (!$user): ?>
		<div class="absolute left-[40%] top-[20%] bg-red-800 p-4 text-white rounded-xl text-xl">User does not exist.</div>
                </main></body></html>
                <?php die() ?>
            <?php endif ?>
            <?php if (isset($_GET['editSuccess'])): ?>
		<div class="absolute left-[40%] top-[15%] z-50 bg-green-800 p-4 text-white rounded-xl text-xl">Profile updated successfully!</div>
            <?php endif ?>
            <?php if (isset($_GET['editFailed'])): ?>
		<div class="absolute left-[40%] top-[15%] z-50 bg-red-800 p-4 text-white rounded-xl text-xl">Profile failed to update.</div>
            <?php endif ?>
            <?php if (isset($_GET['rscSuccess'])): ?>
		<div class="absolute left-[40%] top-[15%] z-50 bg-green-800 p-4 text-white rounded-xl text-xl">User role updated successfully!</div>
            <?php endif ?>

<body class="bg-gray-100">
  <!-- Hero Section -->
  <div class="h-48 relative" style="background-color: var(--page-background-color);">
  </div>

  <!-- Profile Content -->
  <div class="max-w-6xl mx-auto px-4 -mt-20 relative z-10 flex flex-col md:flex-row gap-6">
    <!-- Left Box -->
    <div class="w-full md:w-1/3 bg-white border border-gray-300 rounded-2xl shadow-lg p-6 flex flex-col justify-between">
      <div>
	<div class="flex justify-between items-center">
	<?php if ($viewingOwnProfile): ?>
          <h2 class="text-xl font-semibold mb-4">My Profile</h2>

	<?php else: ?>
	  <h2 class="text-xl font-semibold mb-4">Viewing <?php echo $user->get_first_name() . ' ' . $user->get_last_name() ?></h2>
	<?php endif ?>
	</div>
        <div class="space-y-2 divide-y divide-gray-300">
          <div class="flex justify-between py-2">
            <span class="font-medium">Joined</span><span><?php echo hsc(date('m/d/Y', strtotime($user->get_start_date()))) ?></span>
          </div>
          <div class="flex justify-between py-2">
            <span class="font-medium">Semester</span><span><?php echo hsc($user->get_semester()) ?></span>
          </div>
        </div>
      </div>
      <div class="mt-6 space-y-2">
        <button onclick="window.location.href='editProfile.php<?php if ($id != $userID) echo '?id=' . $id ?>';" class="text-lg font-medium w-full px-4 py-2 bg-[#92c44c] text-[#1F1F21] rounded-md hover:bg-[#1F1F21] hover:text-[#C9AB81] cursor-pointer">Edit Profile</button>
        <button onclick="window.location.href='index.php';" class="text-lg font-medium w-full px-4 py-2 border-2 border-gray-300 text-black rounded-md hover:border-[#1F1F21] cursor-pointer">Return to Dashboard</button>
      </div>
    </div>

    <!-- Right Box -->
    <div class="w-full md:w-2/3 bg-white rounded-2xl shadow-lg border border-gray-300 p-6">
      <!-- Tabs -->
      <div class="flex border-b border-gray-300 mb-4">
        <h3 class="tab-button px-4 py-2 text-lg font-medium text-[#2B2B2E] border-b-4 border-[#1F1F21]" data-tab="personal" onclick="showSection('personal')">Personal Information</h3>
      </div>

      <!-- Personal Section -->
      <div id="personal" class="profile-section space-y-4">
        <div>
          <span class="block text-sm font-medium text-[#1F1F21]">Name</span>
          <p class="text-gray-900 font-medium text-xl"><?php echo hsc($user->get_first_name()) ?> <?php echo hsc($user->get_last_name()) ?></p>
        </div>
        <div>
          <span class="block text-sm font-medium text-[#1F1F21]">Username</span>
          <p class="text-gray-900 font-medium text-xl"><?php echo hsc($user->get_id()) ?></p>
        </div>
        <div>
          <span class="block text-sm font-medium text-[#1F1F21]">Email</span>
          <p class="text-gray-900 font-medium text-xl"><a href="mailto:<?php echo hsc($user->get_email()) ?>"><?php echo hsc($user->get_email()) ?></a></p>
        </div>
        <div>
          <span class="block text-sm font-medium text-[#1F1F21]">Role</span>
          <p class="text-gray-900 font-medium text-xl"><?php echo hsc($user->get_role()) ?></p>
        </div>
      </div>	      
    </div>
  </div>
</body>
</html>
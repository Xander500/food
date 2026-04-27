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

    /*
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if (isset($_POST['url'])) {
        if (!update_profile_pic($id, $_POST['url'])) {
          header('Location: viewProfile.php?id='.$id.'&picsuccess=False');
        } else {
          header('Location: viewProfile.php?id='.$id.'&picsuccess=True');
        }
      }
    }
      */
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>UMW Alleviating Food Waste | Profile Page</title>
  <link rel="icon" type="image/x-icon" href="images/alleviatingFoodWasteLogo.png">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    function showSection(sectionId) {
      const sections = document.querySelectorAll('.profile-section');
      sections.forEach(section => section.classList.add('hidden'));
      document.getElementById(sectionId).classList.remove('hidden');

      const tabs = document.querySelectorAll('.tab-button');
      tabs.forEach(tab => {
        tab.classList.remove('border-b-4', 'border-[#759d3d]');
        tab.classList.add('hover:border-b-2', 'hover:border-[#759d3d]');
      });

      const activeTab = document.querySelector(`[data-tab="${sectionId}"]`);
      activeTab.classList.add('border-b-4', 'border-[#759d3d]');
      activeTab.classList.remove('hover:border-b-2', 'hover:border-[#759d3d]');
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
            <?php if (!$user): ?>
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

<body>
  <!-- Profile Content -->
  <div class="profile-container">
    <!-- Left Box -->
    <div class="profile-box-left">
      <div>
	      <?php if ($viewingOwnProfile): ?>
          <h2 class="profile-name">My Profile</h2>
	      <?php else: ?>
	        <h2 class="profile-name">Viewing <?php echo $user->get_first_name() . ' ' . $user->get_last_name() ?></h2>
	      <?php endif ?>
	      <div class="profile-info">
          <div class="profile-info-item">
            <span>Joined</span><span><?php echo hsc(date('m/d/Y', strtotime($user->get_start_date()))) ?></span>
          </div>
          <div class="profile-info-item">
            <span>Semester</span><span><?php echo hsc($user->get_semester()) ?></span>
          </div>
          <div class="profile-info-item">
            <span>Status</span><span><?php echo ($user->is_archived() ? 'Archived' : 'Active') ?></span>
          </div>
        </div>
      </div>
      <div class="profile-buttons">
        <button onclick="window.location.href='editProfile.php<?php if ($id != $userID) echo '?id=' . $id ?>';">Edit Profile</button>
        <button onclick="window.location.href='volunteerManagement.php';">Return to Dashboard</button>
      </div>
    </div>

    <!-- Right Box -->
    <div class="profile-box-right">
      <!-- Tabs -->
      <div class="profile-tab-header">
        <h3 class="tab-button profile-tab" data-tab="personal" onclick="showSection('personal')">Personal Information</h3>
      </div>

      <!-- Personal Section -->
      <div id="personal" class="profile-section profile-tab-section">
        <div class="profile-tab-section-item">
          <span class="profile-tab-section-item-heading">Name</span>
          <p class="profile-tab-section-item-data"><?php echo hsc($user->get_first_name()) ?> <?php echo hsc($user->get_last_name()) ?></p>
        </div>
        <div class="profile-tab-section-item">
          <span class="profile-tab-section-item-heading">Username</span>
          <p class="profile-tab-section-item-data"><?php echo hsc($user->get_id()) ?></p>
        </div>
        <div class="profile-tab-section-item">
          <span class="profile-tab-section-item-heading">Email</span>
          <p class="profile-tab-section-item-data"><a href="mailto:<?php echo hsc($user->get_email()) ?>"><?php echo hsc($user->get_email()) ?></a></p>
        </div>
        <div class="profile-tab-section-item">
          <span class="profile-tab-section-item-heading">Role</span>
          <p class="profile-tab-section-item-data"><?php echo hsc($user->get_role()) ?></p>
        </div>
      </div>	      
    </div>
  </div>
</body>
</html>
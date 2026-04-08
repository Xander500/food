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

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>UMW Alleviating Food Waste Volunteer Tracking | Settings </title>
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
	  <h2 class="text-xl font-semibold mb-4">Settings</h2>
	</div>
        <div class="space-y-2 divide-y divide-gray-300">
          <div class="flex justify-between py-2">
            <span class="font-medium">Joined</span><span>ddgdg</span>
          </div>
        </div>
      </div>
      <div class="mt-6 space-y-2">
        <button onclick="window.location.href='';" class="text-lg font-medium w-full px-4 py-2 bg-[#92c44c] text-[#1F1F21] rounded-md hover:bg-[#1F1F21] hover:text-[#C9AB81] cursor-pointer">Edit Profile</button>
        <button onclick="window.location.href='index.php';" class="text-lg font-medium w-full px-4 py-2 border-2 border-gray-300 text-black rounded-md hover:border-[#1F1F21] cursor-pointer">Return to Dashboard</button>
      </div>
    </div>

    <!-- Right Box -->
    <div class="w-full md:w-2/3 bg-white rounded-2xl shadow-lg border border-gray-300 p-6">
      <!-- Tabs -->
      <div class="flex border-b border-gray-300 mb-4">
        <h3 class="tab-button px-4 py-2 text-lg font-medium text-[#2B2B2E] border-b-4 border-[#1F1F21]" data-tab="personal" onclick="showSection('personal')">Display</h3>
      </div>

      <!-- Personal Section -->
      <div id="personal" class="profile-section space-y-4">
        <div>
          <span class="block text-sm font-medium text-[#1F1F21]">Name</span>
          <p class="text-gray-900 font-medium text-xl"></p>
        </div>
        <div>
          <span class="block text-sm font-medium text-[#1F1F21]">Username</span>
          <p class="text-gray-900 font-medium text-xl"></p>
        </div>
      </div>	      
    </div>
  </div>
</body>
</html>
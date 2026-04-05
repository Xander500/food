<?php
session_cache_expire(30);
session_start();

$loggedIn = false;
$accessLevel = 0;
$userID = null;
$isAdmin = false;

require_once('database/dbUsers.php');

if (!isset($_POST['bulk_delete'])) {
    header('Location: deleteUserSearch.php');
    exit();
}

if (!isset($_SESSION['access_level']) || $_SESSION['access_level'] < 1) {
    header('Location: login.php');
    die();
}

if (isset($_SESSION['_id'])) {
    $loggedIn = true;
    $accessLevel = $_SESSION['access_level'];
    $isAdmin = $accessLevel >= 2;
    $userID = $_SESSION['_id'];
} else {
    header('Location: login.php');
    die();
}

if (!$isAdmin) {
    header('Location: index.php');
    die();
}

$deletedUsers = [];
$failedUsers = [];

if (isset($_POST['selected_users']) && is_array($_POST['selected_users'])) {
    foreach ($_POST['selected_users'] as $id) {
        $id = trim($id);

        if ($id === '' || $id === 'vmsroot') {
            $failedUsers[] = $id;
            continue;
        }

        $user = retrieve_user($id);

        if ($user && remove_person($id)) {
            $deletedUsers[] = $user->get_first_name() . ' ' . $user->get_last_name() . ' (' . $id . ')';
        } else {
            $failedUsers[] = $id;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bulk Delete Users</title>
    <?php require('universal.inc'); ?>
</head>
<body>
    <main>
        <div style="max-width: 800px; margin: 40px auto; text-align: center;">
            <h2>Batch Delete Results</h2>

            <?php if (count($deletedUsers) > 0): ?>
                <p class="happy-toast centered"><?php echo count($deletedUsers); ?> user(s) deleted successfully.</p>
                <div style="margin-top: 20px; text-align: left;">
                    <h3>Deleted Users:</h3>
                    <ul>
                        <?php foreach ($deletedUsers as $deleted): ?>
                            <li><?php echo htmlspecialchars($deleted); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if (count($failedUsers) > 0): ?>
                <p class="error-toast centered"><?php echo count($failedUsers); ?> user(s) could not be deleted.</p>
                <div style="margin-top: 20px; text-align: left;">
                    <h3>Failed Users:</h3>
                    <ul>
                        <?php foreach ($failedUsers as $failed): ?>
                            <li><?php echo htmlspecialchars($failed); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if (count($deletedUsers) === 0 && count($failedUsers) === 0): ?>
                <p>No users were selected.</p>
            <?php endif; ?>

            <div style="margin-top: 30px;">
                <a class="button cancel" href="deleteUserSearch.php" style="margin-right: 10px;">Back to User Search</a>
                <a class="button cancel" href="index.php">Return to Dashboard</a>
            </div>
        </div>
    </main>
</body>
</html>
<?php
session_cache_expire(30);
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['access_level'] < 2) {
    header('Location: login.php');
    die();
}

include_once('header.php');

$photos = 'photos.php';
if(file_exists($photos)) {
    $config = include($photos);
} else {
    $config = [];
}

$successMsg = '';
$errorMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $extensions = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

    if (!empty($_FILES['background']['name'])) {
        $file = $_FILES['background'];
        if (in_array($file['type'], $extensions)) {
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $dest = 'images/uploads/background.' . $ext;

            foreach (glob('images/uploads/background.*') as $old) {
                unlink($old);
            }

            if (move_uploaded_file($file['tmp_name'], $dest)) {
                $config['background'] = $dest;
            } else {
                $errorMsg = 'Failed to upload background image.';
            }
        } else {
            $errorMsg = 'Invalid file type for background.';
        }
    }

    if (!empty($_FILES['logo']['name'])) {
        $file = $_FILES['logo'];
        if (in_array($file['type'], $extensions)) {
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $dest = 'images/uploads/logo.' . $ext;

            foreach (glob('images/uploads/logo.*') as $old) {
                unlink($old);
            }

            if (move_uploaded_file($file['tmp_name'], $dest)) {
                $config['logo'] = $dest;
            } else {
                $errorMsg = 'Failed to upload logo.';
            }
        } else {
            $errorMsg = 'Invalid file type for logo.';
        }
    }

    if (!$errorMsg) {
        $phpContent = "<?php\nreturn [\n";
        foreach ($config as $key => $value) {
            $phpContent .= "    '{$key}' => '{$value}',\n";
        }
        $phpContent .= "];\n";
        file_put_contents($photos, $phpContent);
        $successMsg = 'Settings saved successfully!';
    }
}

$currentBg   = $config['background'] ?? 'images/UMW_campus.jpg';
$currentLogo = $config['logo']       ?? 'images/default_logo.png';
?>
<!DOCTYPE html>
<html>
<head>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>* { font-family: Quicksand, sans-serif; }</style>
    <title>Admin Settings</title>
</head>
<body class="bg-gray-100 min-h-screen p-8">

    <div class="max-w-2xl mx-auto bg-white rounded-2xl shadow-lg p-8 mt-20">
        <h1 class="text-3xl font-bold text-[#213e57] mb-2">Admin Settings</h1>
        <p class="text-gray-500 mb-6">Update the login page images below.</p>

        <?php if ($successMsg): ?>
            <div class="bg-green-700 text-white p-3 rounded-lg mb-4"><?= $successMsg ?></div>
        <?php endif; ?>
        <?php if ($errorMsg): ?>
            <div class="bg-red-700 text-white p-3 rounded-lg mb-4"><?= $errorMsg ?></div>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data" class="space-y-8">

            <div>
                <h2 class="text-xl font-semibold text-[#213e57] mb-3">Login Page Background</h2>
                <img src="<?= htmlspecialchars($currentBg) ?>"
                     alt="Current background"
                     class="w-full h-48 object-cover rounded-lg mb-3 border border-gray-200">
                <input type="file" name="background" accept="image/*"
                       class="w-full p-3 border border-gray-300 rounded-lg bg-gray-100">
            </div>

            <div>
                <h2 class="text-xl font-semibold text-[#213e57] mb-3">Logo</h2>
                <img src="<?= htmlspecialchars($currentLogo) ?>"
                     alt="Current logo"
                     class="h-24 object-contain mb-3 border border-gray-200 rounded-lg p-2">
                <input type="file" name="logo" accept="image/*"
                       class="w-full p-3 border border-gray-300 rounded-lg bg-gray-100">
            </div>

            <button type="submit"
                    class="w-full bg-[#213e57] hover:bg-[#92c44c] text-white font-semibold py-3 rounded-lg transition duration-300">
                Save Changes
            </button>
        </form>

        <a href="index.php" class="block text-center text-[#759d3d] mt-6 hover:underline">← Back to Dashboard</a>
    </div>

</body>
</html>
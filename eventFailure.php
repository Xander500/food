<?php
session_cache_expire(30);
session_start();
header("refresh:3;url=addEvent.php");
?>

<!DOCTYPE html>
<html>
<head>
    <?php require_once('universal.inc') ?>
    <title>UMW Alleviating Food Waste | Create Activity</title>
</head>
<body>
<?php require_once('header.php') ?>
<h1>Failed to Create Activity!</h1>
</body>
</html>

<?php
$photo = include 'photos.php';
echo $photo['logo'] ?? 'images/alleviatingFoodWasteLogo.png';
?>
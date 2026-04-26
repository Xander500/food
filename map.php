<?php
require_once('database/dbVolunteerActivity.php');
$locations = get_all_activity_locations_for_map(isset($_GET['semester']) ? $_GET['semester'] : "All");
?>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <div id="map" style="height: 500px; width: 100%; border-radius: 10px; z-index: 0;"></div>

    <script>
        var map = L.map('map').setView([38.30086584212594, -77.4606028485915], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        <?php foreach ($locations as $loc): ?>
            L.marker([<?= $loc['latitude'] ?>, <?= $loc['longitude'] ?>])
                .addTo(map)
                .bindPopup("<?= htmlspecialchars($loc['location'], ENT_QUOTES) ?>");
        <?php endforeach; ?>
    </script>
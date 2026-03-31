<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Map_Test</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <style>
        body{
            background-color: rgb(33, 62, 87);
        }
        .my-box{
            width: 700px;
            padding: 5px;
            border: 5px black;
            margin: 50px auto;
            background-color: black;
        }
        #map {
            height: 700px;
            width: 100%;
        }
    </style>
</head>
<body>
    <div class ="my-box">
        <div id = "map"></div>
    </div>
    <script>
        var map = L.map('map').setView([38.30086584212594, -77.4606028485915], 15);

        L.tileLayer('https://api.maptiler.com/maps/base-v4/{z}/{x}/{y}.png?key=EGKCnnMrNWBQqtJG1Izh',{
            attribution: '<a href="https://www.maptiler.com/copyright/" target="_blank">&copy; MapTiler</a> <a href="https://www.openstreetmap.org/copyright" target="_blank">&copy; OpenStreetMap contributors</a>'
        }).addTo(map);
        var marker = L.marker([38.3018173037993, -77.47393140066606]).addTo(map);
        var marker = L.marker([38.30255787781403, -77.45870786616035]).addTo(map);
        var marker = L.marker([38.30180148226378, -77.4625196350227]).addTo(map);
        var marker = L.marker([38.30629228973498, -77.46543254169393]).addTo(map);
        var marker = L.marker([38.31264708729075, -77.46864411489169]).addTo(map);

    </script>
</body>
</html>
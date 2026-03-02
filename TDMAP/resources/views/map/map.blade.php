<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Map Popup Layer</title>

<link rel="stylesheet"
href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>

<style>
html, body {
    margin:0;
    padding:0;
    height:100%;
}

#map {
    height:100vh;
}

/* ===== NÚT MỞ ===== */
.map-layer-toggle {
    position:absolute;
    bottom:100px;
    left:15px;
    background:white;
    width:50px;
    height:50px;
    border-radius:14px;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:22px;
    cursor:pointer;
    z-index:1000;
    box-shadow:0 4px 12px rgba(0,0,0,0.2);
}

/* ===== POPUP KHUNG NHỎ ===== */
.map-layer-popup {
    position:absolute;
    bottom:85px;
    left:15px;
    width:320px;
    background:white;
    border-radius:18px;
    padding:15px;
    z-index:2000;
    box-shadow:0 8px 25px rgba(0,0,0,0.25);
    display:none;
}

.map-layer-popup.active {
    display:block;
}

/* Header */
.map-layer-header {
    font-weight:600;
    margin-bottom:12px;
}

/* Grid */
.map-layer-grid {
    display:grid;
    grid-template-columns:repeat(2,1fr);
    gap:12px;
}

/* Item */
.map-layer-item {
    text-align:center;
    cursor:pointer;
}

.map-layer-item img {
    width:100%;
    border-radius:10px;
}

.map-layer-item span {
    display:block;
    font-size:13px;
    margin-top:5px;
}

/* ===== MOBILE RESPONSIVE ===== */
@media (max-width:480px){
    .map-layer-popup{
        width:90%;
        left:5%;
        bottom:90px;
    }
}
</style>
</head>

<body>

<div id="map"></div>

<!-- NÚT -->
<div class="map-layer-toggle" onclick="togglePopup()">🗺</div>

<!-- POPUP -->
<div id="mapLayerPopup" class="map-layer-popup">
    <div class="map-layer-header">Chọn bản đồ</div>

    <div class="map-layer-grid">

        <div class="map-layer-item" onclick="changeBaseLayer(street)">
            <img src="https://i.imgur.com/7v9Yp9S.png">
            <span>Đường phố</span>
        </div>

        <div class="map-layer-item" onclick="changeBaseLayer(esriSat)">
            <img src="https://i.imgur.com/vN6zR0R.png">
            <span>Vệ tinh</span>
        </div>

        <div class="map-layer-item" onclick="changeBaseLayer(esriTopo)">
            <img src="https://i.imgur.com/9O3KQbT.png">
            <span>Topo</span>
        </div>

        <div class="map-layer-item" onclick="changeBaseLayer(hybrid)">
            <img src="https://i.imgur.com/vN6zR0R.png">
            <span>Hybrid</span>
        </div>

    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
var map = L.map('map').setView([10.762622,106.660172],13);

/* ===== BASE LAYERS ===== */

var street = L.tileLayer(
'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png'
);

var esriSat = L.tileLayer(
'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}'
);

var esriTopo = L.tileLayer(
'https://server.arcgisonline.com/ArcGIS/rest/services/World_Topo_Map/MapServer/tile/{z}/{y}/{x}'
);

var hybrid = L.layerGroup([
    L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}'),
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png')
]);

/* ===== DEFAULT ===== */
var currentBaseLayer = street;
currentBaseLayer.addTo(map);

/* ===== ĐỔI MAP ===== */
function changeBaseLayer(newLayer){

    if(currentBaseLayer){
        map.removeLayer(currentBaseLayer);
    }

    currentBaseLayer = newLayer;
    currentBaseLayer.addTo(map);

    closePopup();
}

/* ===== POPUP CONTROL ===== */
function togglePopup(){
    document.getElementById("mapLayerPopup").classList.toggle("active");
}

function closePopup(){
    document.getElementById("mapLayerPopup").classList.remove("active");
}
</script>

</body>
</html>
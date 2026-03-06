<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>TDMAP-PRO</title>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/map.css') }}">

</head>

<body>

    <div id="map"></div>

    <!-- BUTTON MAP -->

    <div class="map-layer-toggle" onclick="togglePopup()">🗺</div>

    <!-- POPUP -->

    <div id="mapLayerPopup" class="map-layer-popup">

        <div class="map-layer-header">Chọn bản đồ</div>

        <div class="map-layer-grid">

            <div class="map-layer-item" onclick="changeBaseLayer(street)">
                <i class="fa-solid fa-road"></i>
                <span>Đường phố</span>
            </div>

            <div class="map-layer-item" onclick="changeBaseLayer(esriSat)">
                <i class="fa-solid fa-satellite"></i>
                <span>Vệ tinh</span>
            </div>

            <div class="map-layer-item" onclick="changeBaseLayer(esriTopo)">
                <i class="fa-solid fa-mountain"></i>
                <span>Topo</span>
            </div>

            <div class="map-layer-item" onclick="changeBaseLayer(hybrid)">
                <i class="fa-solid fa-layer-group"></i>
                <span>Hybrid</span>
            </div>

        </div>

    </div>

    <!-- PANEL -->

    <div id="locationPanel" class="location-panel">

        <div class="panel-header">
            <span>📍 Thông tin vị trí</span>
            <button onclick="closePanel()">✖</button>
        </div>

        <div id="panelContent" class="panel-content"></div>

    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/proj4js/2.8.0/proj4.js"></script>

    <script src="{{ asset('js/map.js') }}"></script>
</body>

</html>
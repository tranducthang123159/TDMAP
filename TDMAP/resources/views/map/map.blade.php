<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>TDMAP-PRO</title>




    <link href="https://unpkg.com/maplibre-gl@3.6.2/dist/maplibre-gl.css" rel="stylesheet">
    <script src="https://unpkg.com/maplibre-gl@3.6.2/dist/maplibre-gl.js"></script>

    <!-- FONT AWESOME -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- MAP CSS -->
    <link rel="stylesheet" href="{{ asset('css/map.css') }}">

</head>

<body>

    <div id="map"></div>


    <!-- BUTTON MAP -->

    <div class="map-layer-toggle" onclick="togglePopup()">🗺</div>

    <div id="mapLayerPopup" class="map-layer-popup">

        <div class="map-layer-header">
            Chọn bản đồ
            <span class="map-close-btn" onclick="closePopup()">✖</span>
        </div>

        <div class="map-layer-grid">

            <div class="map-layer-item" onclick="setBaseMap('street')">
                <i class="fa-solid fa-road"></i>
                <span>Đường phố</span>
            </div>

            <div class="map-layer-item" onclick="setBaseMap('sat')">
                <i class="fa-solid fa-satellite"></i>
                <span>Vệ tinh</span>
            </div>

            <div class="map-layer-item" onclick="setBaseMap('topo')">
                <i class="fa-solid fa-mountain"></i>
                <span>Topo</span>
            </div>

        </div>

    </div>

    <!-- PANEL -->

    <div id="locationPanel" class="location-panel">

        <div class="panel-header">
            <span>📍 Thông tin</span>
            <button onclick="closePanel()">✖</button>
        </div>

        <div class="panel-body">
            <div class="parcel-panel">

                <div class="parcel-header" onclick="toggleParcelInfo()">
                    <span>📍 Vị trí ghim</span>
                    <span id="parcelArrow">▼</span>
                </div>

                <div id="parcelInfoBody" class="parcel-body">

                    <div id="pinContent"></div>

                </div>

            </div>

            <div class="parcel-panel">

                <div class="parcel-header" onclick="toggleParcelInfo()">
                    <span>🧾 Thông tin thửa</span>
                    <span id="parcelArrow">▼</span>
                </div>

                <div id="parcelInfoBody" class="parcel-body">

                    <div id="parcelContent"></div>

                </div>

            </div>
        </div>

    </div>

    <!-- SCRIPTS -->
    <script src="https://cdn.jsdelivr.net/npm/@turf/turf@6/turf.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/proj4js/2.8.0/proj4.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/proj4@2.9.0/dist/proj4.js"></script>
    <script src="{{ asset('js/map.js') }}"></script>
    <script src="{{ asset('js/common.js') }}"></script>

    <!-- LOAD LAYER -->
    <script src="{{ asset('js/dccu.js') }}"></script>
    <script src="{{ asset('js/dcmoi.js') }}"></script>
    <script src="{{ asset('js/quyhoach.js') }}"></script>

    <!-- UPLOAD -->
    <script src="{{ asset('js/upload.js') }}"></script>

    <script src="{{ asset('js/parcel-info.js') }}"></script>
    <script src="{{ asset('js/parcel-search.js') }}"></script>
    <script src="{{ asset('js/search.js') }}"></script>
       <script src="{{ asset('js/do_kc.js') }}"></script>
     <script src="{{ asset('js/do_dt.js') }}"></script>
   
</body>

</html>

/* =========================
INIT MAP
========================= */

var map = L.map('map').setView([10.762622, 106.660172], 13);

/* =========================
BASE LAYER
========================= */

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
labelLayer = L.layerGroup().addTo(map);
var currentBaseLayer = street;

currentBaseLayer.addTo(map);

/* =========================
CHANGE LAYER
========================= */

function changeBaseLayer(newLayer) {

    if (currentBaseLayer) {

        map.removeLayer(currentBaseLayer);

    }

    currentBaseLayer = newLayer;

    currentBaseLayer.addTo(map);

    closePopup();

}

/* =========================
POPUP CONTROL
========================= */

function togglePopup() {

    document.getElementById("mapLayerPopup").classList.toggle("active");

}

function closePopup() {

    document.getElementById("mapLayerPopup").classList.remove("active");

}

/* =========================
CLICK MAP
========================= */

var marker;

map.on("click", function (e) {

    var lat = e.latlng.lat;
    var lng = e.latlng.lng;

    addMarker(lat, lng);

});

/* =========================
ADD MARKER
========================= */

function addMarker(lat, lng) {

    if (marker) {

        map.removeLayer(marker);

    }

    marker = L.marker([lat, lng]).addTo(map);

    getAddress(lat, lng);

}

/* =========================
GET ADDRESS
========================= */

function getAddress(lat, lng) {

    fetch(
        "https://nominatim.openstreetmap.org/reverse?format=json&lat=" + lat + "&lon=" + lng
    )

        .then(res => res.json())

        .then(data => {

            var address = data.display_name;

            /* WGS84 -> VN2000 */

            proj4.defs("VN2000",
                "+proj=tmerc +lat_0=0 +lon_0=108.5 +k=0.9999 +x_0=500000 +y_0=0 +ellps=WGS84 +units=m +no_defs");

            var result = proj4("EPSG:4326", "VN2000", [lng, lat]);

            var vnX = result[1].toFixed(3);
            var vnY = result[0].toFixed(3);

            var html = `

<div>🏠 ${address}</div>

<div>🌍 WGS84: ${lat.toFixed(6)} , ${lng.toFixed(6)}</div>

<div>📐 VN2000 (KTT 108.5)</div>

<div>X: ${vnX}</div>

<div>Y: ${vnY}</div>

`;

            document.getElementById("panelContent").innerHTML = html;

            openPanel();

        });

}

/* =========================
PANEL
========================= */

function openPanel() {
    document.getElementById("locationPanel").classList.add("active");
}

function closePanel() {
    document.getElementById("locationPanel").classList.remove("active");
}


/* =========================
GPS LOCATION
========================= */

function locateMe() {

    if (!navigator.geolocation) {
        alert("Trình duyệt không hỗ trợ GPS");
        return;
    }

    navigator.geolocation.getCurrentPosition(function (pos) {

        var lat = pos.coords.latitude;
        var lng = pos.coords.longitude;

        map.setView([lat, lng], 18);

        addMarker(lat, lng);

    }, function () {

        alert("Không lấy được vị trí");

    });

}

/* =========================
RELOAD MAP
========================= */

function reloadMap() {

    location.reload();

}

/* =========================
CLEAR MARKER
========================= */

function clearMarker() {

    if (marker) {
        map.removeLayer(marker);
        marker = null;
    }

    closePanel();

}


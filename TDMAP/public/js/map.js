/* =========================
INIT MAP
========================= */

var map = L.map('map', {
    zoomControl: false
}).setView([10.762622, 106.660172], 13);


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

var currentBaseLayer = street;
currentBaseLayer.addTo(map);


/* =========================
CHANGE MAP LAYER
========================= */

function changeBaseLayer(layer){

    if(currentBaseLayer){
        map.removeLayer(currentBaseLayer);
    }

    currentBaseLayer = layer;

    currentBaseLayer.addTo(map);

    closePopup();
}


/* =========================
POPUP CONTROL
========================= */

function togglePopup(){
    document.getElementById("mapLayerPopup").classList.toggle("active");
}

function closePopup(){
    document.getElementById("mapLayerPopup").classList.remove("active");
}


/* =========================
MARKER
========================= */

var marker;


/* =========================
CLICK MAP
========================= */

map.on("click",function(e){

    var lat = e.latlng.lat;
    var lng = e.latlng.lng;

    addMarker(lat,lng);

});


/* =========================
ADD MARKER
========================= */

function addMarker(lat,lng){

    if(marker){
        map.removeLayer(marker);
    }

    marker = L.marker([lat,lng]).addTo(map);

    getAddress(lat,lng);

}


/* =========================
GET ADDRESS + VN2000
========================= */

function getAddress(lat,lng){

fetch("https://nominatim.openstreetmap.org/reverse?format=json&lat="+lat+"&lon="+lng)

.then(res=>res.json())

.then(data=>{

var address = data.display_name;


/* WGS84 -> VN2000 */

proj4.defs("VN2000",
"+proj=tmerc +lat_0=0 +lon_0=108.5 +k=0.9999 +x_0=500000 +y_0=0 +ellps=WGS84 +units=m +no_defs");

var result = proj4("EPSG:4326","VN2000",[lng,lat]);

var vnX = result[1].toFixed(3);
var vnY = result[0].toFixed(3);


var html = `

<div class="info-row">📍 <b>Địa chỉ</b><br>${address}</div>

<div class="info-row">
🌍 <b>WGS84</b><br>
${lat.toFixed(6)} , ${lng.toFixed(6)}
</div>

<div class="info-row">
📐 <b>VN2000 (KTT 108.5)</b><br>
X: ${vnX} <br>
Y: ${vnY}
</div>

`;

document.getElementById("panelContent").innerHTML = html;

openPanel();

});

}


/* =========================
PANEL CONTROL
========================= */

function openPanel(){
document.getElementById("locationPanel").classList.add("active");
}

function closePanel(){
document.getElementById("locationPanel").classList.remove("active");
}


/* =========================
GPS LOCATION
========================= */

function locateMe(){

if(!navigator.geolocation){

alert("Trình duyệt không hỗ trợ GPS");

return;

}

navigator.geolocation.getCurrentPosition(function(pos){

var lat = pos.coords.latitude;
var lng = pos.coords.longitude;

map.setView([lat,lng],18);

addMarker(lat,lng);

});

}


/* =========================
RELOAD MAP
========================= */

function reloadMap(){

location.reload();

}


/* =========================
CLEAR MARKER
========================= */

function clearMarker(){

if(marker){
map.removeLayer(marker);
marker=null;
}

closePanel();

}
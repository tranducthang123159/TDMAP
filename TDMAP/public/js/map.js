/* =========================
INIT MAP
========================= */

window.map = new maplibregl.Map({

container: 'map',

style: {
version: 8,
glyphs: "https://demotiles.maplibre.org/font/{fontstack}/{range}.pbf",

sources: {

basemap: {
type: "raster",
tiles: [
"https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}"
],
tileSize: 256,
maxzoom: 22
}

},

layers: [
{
id: "basemap",
type: "raster",
source: "basemap"
}
]

},

center: [106.7,10.8],
zoom: 13,
maxZoom: 22,
minZoom: 3,
antialias:true

});


/* =========================
MAP MODE
========================= */

let mapMode="pin";
window.measureMarkers=[];


/* =========================
BASEMAP SWITCH
========================= */

function setBaseMap(type){

let tiles;

if(type==="street"){

tiles=[
"https://a.tile.openstreetmap.org/{z}/{x}/{y}.png",
"https://b.tile.openstreetmap.org/{z}/{x}/{y}.png",
"https://c.tile.openstreetmap.org/{z}/{x}/{y}.png"
];

}

if(type==="sat"){

tiles=[
"https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}"
];

}

map.getSource("basemap").setTiles(tiles);

}


/* =========================
UPLOAD MAP
========================= */

function loadMap(){

let dc_cu=document.getElementById("dc_cu").files[0];
let dc_moi=document.getElementById("dc_moi").files[0];
let quy_hoach=document.getElementById("quy_hoach").files[0];

if(!dc_cu && !dc_moi && !quy_hoach){
alert("Chọn ít nhất 1 file");
return;
}

if(dc_cu) uploadAndLoad(dc_cu,"dc_cu");
if(dc_moi) uploadAndLoad(dc_moi,"dc_moi");
if(quy_hoach) uploadAndLoad(quy_hoach,"quy_hoach");

}


/* =========================
CLICK MAP
========================= */

var marker=null;


/* ĐO DT */
map.off("click");
map.on("click", function(e){

let features = map.queryRenderedFeatures(e.point,{
layers:["dc_moi_fill"]
});

if(features.length>0){
return;
}

let lng = e.lngLat.lng;
let lat = e.lngLat.lat;

/* PIN */
if(mapMode==="pin"){
addMarker(lat,lng);
}

/* ĐO DIỆN TÍCH */
if(mapMode==="dt"){
addDTPoint(lng,lat);
}

/* ĐO KHOẢNG CÁCH */
if(mapMode==="kc"){
addKCPoint(lng,lat);
}

});
/* =========================
ADD MARKER
========================= */
map.on("dblclick",function(){

if(mapMode==="dt" && dtPoints.length>2){

dtPoints.push(dtPoints[0]);
drawDT();

}

});
function addMarker(lat,lng){

if(marker){
marker.remove();
}

let el=document.createElement("div");
el.className="marker";

marker=new maplibregl.Marker(el)
.setLngLat([lng,lat])
.addTo(map);

getAddress(lat,lng);

}


/* =========================
GET ADDRESS + VN2000
========================= */

function getAddress(lat,lng){

fetch("https://nominatim.openstreetmap.org/reverse?format=json&lat="+lat+"&lon="+lng)

.then(res=>res.json())

.then(data=>{

let address=data.display_name;

/* WGS84 -> VN2000 */

let result=proj4("EPSG:4326","VN2000",[lng,lat]);

let vnX=result[1].toFixed(3);
let vnY=result[0].toFixed(3);

let html=`

<div>🏠 ${address}</div>
<div>🌍 WGS84: ${lat.toFixed(6)} , ${lng.toFixed(6)}</div>
<div>📐 VN2000 (KTT 108.5)</div>
<div>X: ${vnX}</div>
<div>Y: ${vnY}</div>

`;

document.getElementById("pinContent").innerHTML=html;

openPanel();

});

}


/* =========================
PANEL
========================= */

function openPanel(){
document.getElementById("locationPanel").classList.add("active");
}

function closePanel(){

document.getElementById("locationPanel").classList.remove("active");

document.getElementById("pinContent").innerHTML="";
document.getElementById("parcelContent").innerHTML="";

}


/* =========================
GPS
========================= */

function locateMe(){

if(!navigator.geolocation){
alert("Trình duyệt không hỗ trợ GPS");
return;
}

navigator.geolocation.getCurrentPosition(function(pos){

let lat=pos.coords.latitude;
let lng=pos.coords.longitude;

map.flyTo({
center:[lng,lat],
zoom:18
});

addMarker(lat,lng);

},function(){

alert("Không lấy được vị trí");

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
marker.remove();
marker=null;
}

document.getElementById("pinContent").innerHTML="";

}


/* =========================
POPUP LAYER
========================= */

function togglePopup(){

let popup=document.getElementById("mapLayerPopup");

if(popup.style.display==="block"){
popup.style.display="none";
}else{
popup.style.display="block";
}

}

function closePopup(){
document.getElementById("mapLayerPopup").style.display="none";
}


/* =========================
PARCEL INFO TOGGLE
========================= */

function toggleParcelInfo(){

let body=document.getElementById("parcelInfoBody");
let arrow=document.getElementById("parcelArrow");

body.classList.toggle("hide");

if(body.classList.contains("hide")){
arrow.innerHTML="▲";
}else{
arrow.innerHTML="▼";
}

}


/* =========================
MODE SWITCH
========================= */

function startPin(){
mapMode="pin";
}

function startKC(){
mapMode="kc";
}

function startDT(){
mapMode="dt";
}
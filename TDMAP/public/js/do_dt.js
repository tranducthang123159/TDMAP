/* =========================
MEASURE AREA
========================= */

let dtPoints=[];
let dtMarkers=[];
let dtGPSWatch=null;
let dtInfoMarker=null;

let dtActive=false;

/* =========================
START
========================= */

function startDT(){

if(!dtActive){

/* tắt đo khoảng cách */

kcActive=false;
clearKC();

dtActive=true;
mapMode="dt";

showExportPDF();
showMeasureToast("Đang bật đo diện tích...",true);

setTimeout(()=>{
showMeasureToast("✔ Đo diện tích đã bật<br>Chạm lại để tắt",false);
},1200);

}else{

showMeasureToast("Đang tắt đo diện tích...",true);

setTimeout(()=>{

dtActive=false;
hideExportPDF();

mapMode="pin";

showMeasureToast("✔ Đã tắt đo diện tích",false);

},1200);

}

}


/* =========================
ADD POINT
========================= */

function addDTPoint(lng,lat){

dtPoints.push([lng,lat]);

let el=document.createElement("div");
el.className="measure-marker";

let marker=new maplibregl.Marker({
element:el,
anchor:"center"
})
.setLngLat([lng,lat])
.addTo(map);

dtMarkers.push(marker);

drawDT();

}


/* =========================
DRAW
========================= */

function drawDT(){

/* nếu <2 điểm thì xoá line */

if(dtPoints.length<2){

if(map.getLayer("dt_line")) map.removeLayer("dt_line");
if(map.getSource("dt_line")) map.removeSource("dt_line");

return;
}


/* =========================
LINE
========================= */

let line={
type:"Feature",
geometry:{
type:"LineString",
coordinates:dtPoints
}
};

if(map.getLayer("dt_line")) map.removeLayer("dt_line");
if(map.getSource("dt_line")) map.removeSource("dt_line");

map.addSource("dt_line",{type:"geojson",data:line});

map.addLayer({
id:"dt_line",
type:"line",
source:"dt_line",
paint:{
"line-color":"#ff0000",
"line-width":3
}
});


/* =========================
POLYGON
========================= */

if(dtPoints.length>=3){

let polyCoords=[...dtPoints,dtPoints[0]];

let polygon={
type:"Feature",
geometry:{
type:"Polygon",
coordinates:[polyCoords]
}
};

if(map.getLayer("dt_poly")) map.removeLayer("dt_poly");
if(map.getSource("dt_poly")) map.removeSource("dt_poly");

map.addSource("dt_poly",{type:"geojson",data:polygon});

map.addLayer({
id:"dt_poly",
type:"fill",
source:"dt_poly",
paint:{
"fill-color":"#ff0000",
"fill-opacity":0.15
}
});

}


/* =========================
EDGE LENGTH
========================= */

let labels=[];

/* xoá label cũ */

if(map.getLayer("dt_label")) map.removeLayer("dt_label");
if(map.getSource("dt_label")) map.removeSource("dt_label");

for(let i=1;i<dtPoints.length;i++){

let p1=dtPoints[i-1];
let p2=dtPoints[i];

let mid=[
(p1[0]+p2[0])/2,
(p1[1]+p2[1])/2
];

let d=turf.distance(
turf.point(p1),
turf.point(p2),
{units:"meters"}
);

let angle=Math.atan2(
p2[1]-p1[1],
p2[0]-p1[0]
)*180/Math.PI;

labels.push({
type:"Feature",
geometry:{type:"Point",coordinates:mid},
properties:{
text:d.toFixed(2)+" m",
angle:angle
}
});

}

map.addSource("dt_label",{
type:"geojson",
data:{
type:"FeatureCollection",
features:labels
}
});

map.addLayer({
id:"dt_label",
type:"symbol",
source:"dt_label",
layout:{
"text-field":["get","text"],
"text-size":14,
"text-rotate":["get","angle"],
"text-allow-overlap":true
},
paint:{
"text-color":"#ff0000",
"text-halo-color":"#ffffff",
"text-halo-width":2
}
});


/* =========================
AREA
========================= */

if(dtPoints.length>=3){

let poly=turf.polygon([[...dtPoints,dtPoints[0]]]);

let area=turf.area(poly);

let center=turf.center(poly).geometry.coordinates;

if(dtInfoMarker){
dtInfoMarker.remove();
}

let el=document.createElement("div");
el.className="measure-info";
el.innerHTML="📐 "+area.toFixed(2)+" m²";

dtInfoMarker=new maplibregl.Marker({
element:el
})
.setLngLat(center)
.addTo(map);

}

}


/* =========================
UNDO
========================= */



/* =========================
CLEAR
========================= */

function clearDT(){

dtPoints=[];

dtMarkers.forEach(m=>m.remove());
dtMarkers=[];

if(map.getLayer("dt_line")) map.removeLayer("dt_line");
if(map.getSource("dt_line")) map.removeSource("dt_line");

if(map.getLayer("dt_poly")) map.removeLayer("dt_poly");
if(map.getSource("dt_poly")) map.removeSource("dt_poly");

if(map.getLayer("dt_label")) map.removeLayer("dt_label");
if(map.getSource("dt_label")) map.removeSource("dt_label");

if(dtInfoMarker){
dtInfoMarker.remove();
}

if(dtGPSWatch){
navigator.geolocation.clearWatch(dtGPSWatch);
dtGPSWatch=null;
}

}



/* =========================
GPS AREA
========================= */

function startDTGPS(){

if(!navigator.geolocation){
alert("Trình duyệt không hỗ trợ GPS");
return;
}

dtGPSWatch=navigator.geolocation.watchPosition(function(pos){

let lat=pos.coords.latitude;
let lng=pos.coords.longitude;

/* bay tới vị trí */

map.flyTo({
center:[lng,lat],
zoom:18
});

/* thêm điểm */

addDTPoint(lng,lat);

},
function(){
alert("Không lấy được GPS");
},
{
enableHighAccuracy:true,
maximumAge:0
});

}


/* =========================
UNDO DT
========================= */

function undoDT(){

if(dtPoints.length===0) return;

/* xoá điểm cuối */

dtPoints.pop();

/* xoá marker */

let marker=dtMarkers.pop();

if(marker) marker.remove();

/* nếu <2 điểm thì xoá hết */

if(dtPoints.length<2){

if(map.getLayer("dt_line")) map.removeLayer("dt_line");
if(map.getSource("dt_line")) map.removeSource("dt_line");

if(map.getLayer("dt_poly")) map.removeLayer("dt_poly");
if(map.getSource("dt_poly")) map.removeSource("dt_poly");

if(map.getLayer("dt_label")) map.removeLayer("dt_label");
if(map.getSource("dt_label")) map.removeSource("dt_label");

if(dtInfoMarker){
dtInfoMarker.remove();
}

return;

}

/* vẽ lại */

drawDT();

}


/* =========================
UNDO DT
========================= */


/* =========================
UNDO BUTTON
========================= */

function undoMeasures(){

if(mapMode==="kc"){
undoKC();
}

if(mapMode==="dt"){
undoDT();
}

}


/* =========================
CLEAR BUTTON
========================= */

function clearMeasures(){

clearKC();
clearDT();

kcActive=false;
dtActive=false;

mapMode="pin";

showMeasureToast("✔ Đã xóa toàn bộ đo",false);

}
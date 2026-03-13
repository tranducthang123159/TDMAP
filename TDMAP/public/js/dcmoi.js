/* =========================
LOAD ĐỊA CHÍNH MỚI (MapLibre)
========================= */

function loadDcMoi(data){

clearMeasure();

/* tránh nhân event */

map.off("click","dc_moi_fill");
map.off("dblclick","dc_moi_fill");

/* nếu layer đã tồn tại thì xoá */

if(map.getSource("dc_moi")){
map.removeLayer("dc_moi_fill");
map.removeLayer("dc_moi_line");
map.removeSource("dc_moi");
}

/* add source */

map.addSource("dc_moi",{
type:"geojson",
data:data
});

/* polygon */

map.addLayer({
id:"dc_moi_fill",
type:"fill",
source:"dc_moi",
paint:{
"fill-color":"#00ffff",
"fill-opacity":0.15
}
});

/* border */

map.addLayer({
id:"dc_moi_line",
type:"line",
source:"dc_moi",
paint:{
"line-color":"#00ffff",
"line-width":1
}
});

/* =========================
CLICK THỬA
========================= */

map.on("click","dc_moi_fill",function(e){

let feature = e.features[0];

let p = feature.properties;

/* highlight */

highlightParcel(feature);

/* info */

showParcelInfo(p);

/* đo */

drawParcelMeasure(feature);

});

/* =========================
DOUBLE CLICK
========================= */

map.on("dblclick","dc_moi_fill",function(e){

let lng = e.lngLat.lng;
let lat = e.lngLat.lat;

addMarker(lat,lng);

});

/* =========================
ZOOM TỚI LAYER
========================= */

let bbox = turf.bbox(data);

map.fitBounds([
[bbox[0],bbox[1]],
[bbox[2],bbox[3]]
],{
padding:20
});

/* search */

initParcelSearch(data);

}
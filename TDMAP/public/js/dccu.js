/* =========================
LOAD ĐỊA CHÍNH CŨ (MapLibre)
========================= */

function loadDcCu(data){

/* tránh click nhân đôi */

map.off("click","dc_cu_fill");
map.off("dblclick","dc_cu_fill");


/* xóa layer cũ */

if(map.getLayer("dc_cu_fill")){
map.removeLayer("dc_cu_fill");
}

if(map.getLayer("dc_cu_line")){
map.removeLayer("dc_cu_line");
}

if(map.getSource("dc_cu")){
map.removeSource("dc_cu");
}


/* add source */

map.addSource("dc_cu",{
type:"geojson",
data:data
});


/* polygon fill */

map.addLayer({
id:"dc_cu_fill",
type:"fill",
source:"dc_cu",
paint:{
"fill-color":"yellow",
"fill-opacity":0.05
}
});


/* border */

map.addLayer({
id:"dc_cu_line",
type:"line",
source:"dc_cu",
paint:{
"line-color":"yellow",
"line-width":1
}
});


/* CLICK THỬA */

map.on("click","dc_cu_fill",function(e){

if(!e.features || e.features.length===0) return;

let feature=e.features[0];
let p=feature.properties;

/* highlight */

highlightParcel(feature);

/* info */

showParcelInfo(p);

/* đo cạnh */

drawParcelMeasure(feature);

});


/* DOUBLE CLICK */

map.on("dblclick","dc_cu_fill",function(e){

let lng=e.lngLat.lng;
let lat=e.lngLat.lat;

addMarker(lat,lng);

});


/* zoom tới layer */

let bbox=turf.bbox(data);

map.fitBounds([
[bbox[0],bbox[1]],
[bbox[2],bbox[3]]
],{
padding:20
});

initParcelSearch(data);
}
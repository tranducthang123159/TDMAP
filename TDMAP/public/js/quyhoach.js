/* =========================
LOAD QUY HOẠCH (MapLibre)
========================= */

function loadQuyHoach(data){

/* tránh click nhân đôi */

map.off("click","quyhoach_fill");


/* xóa layer cũ */

if(map.getLayer("quyhoach_fill")){
map.removeLayer("quyhoach_fill");
}

if(map.getLayer("quyhoach_line")){
map.removeLayer("quyhoach_line");
}

if(map.getSource("quy_hoach")){
map.removeSource("quy_hoach");
}


/* add source */

map.addSource("quy_hoach",{
type:"geojson",
data:data
});


/* polygon fill */

map.addLayer({
id:"quyhoach_fill",
type:"fill",
source:"quy_hoach",
paint:{
"fill-color":"#ff0000",
"fill-opacity":0.25
}
});


/* border */

map.addLayer({
id:"quyhoach_line",
type:"line",
source:"quy_hoach",
paint:{
"line-color":"#ff0000",
"line-width":1
}
});


/* CLICK */

map.on("click","quyhoach_fill",function(e){

if(!e.features || e.features.length===0) return;

let feature=e.features[0];
let p=feature.properties;

/* highlight */

highlightParcel(feature);

/* đo cạnh */

drawParcelMeasure(feature);

/* info */

showInfo(p);

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
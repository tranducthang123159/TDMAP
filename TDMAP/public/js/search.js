/* =========================
SEARCH VN2000
========================= */

function searchVN(){

let x=parseFloat(document.getElementById("vn_x").value);
let y=parseFloat(document.getElementById("vn_y").value);

if(isNaN(x)||isNaN(y)){
alert("Nhập đúng tọa độ X Y");
return;
}

/* VN2000 -> WGS84 */

proj4.defs(
"VN2000",
"+proj=tmerc +lat_0=0 +lon_0=108.5 +k=0.9999 +x_0=500000 +y_0=0 +ellps=WGS84 +units=m +no_defs"
);

let result=proj4("VN2000","EPSG:4326",[y,x]);

let lng=result[0];
let lat=result[1];

/* bay tới */

map.flyTo({
center:[lng,lat],
zoom:18
});

/* marker */

addMarker(lat,lng);

}


/* =========================
SEARCH WGS84
========================= */

function searchWGS(){

let lat=parseFloat(document.getElementById("wgs_lat").value);
let lng=parseFloat(document.getElementById("wgs_lng").value);

if(isNaN(lat)||isNaN(lng)){
alert("Nhập đúng Lat Lng");
return;
}

map.flyTo({
center:[lng,lat],
zoom:18
});

addMarker(lat,lng);

}

function toggleSearchPanel(){

let body=document.getElementById("searchBody");
let arrow=document.getElementById("searchArrow");

body.classList.toggle("collapsed");

if(body.classList.contains("collapsed")){
arrow.innerHTML="▼";
}else{
arrow.innerHTML="▲";
}

}
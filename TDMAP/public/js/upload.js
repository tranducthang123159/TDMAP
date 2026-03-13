/* =========================
UPLOAD FILE
========================= */
/* =========================
STORE GEOJSON
========================= */

let geo_dc_cu = null;
let geo_dc_moi = null;
let geo_quy_hoach = null;
function uploadAndLoad(file,type){

const loading = document.getElementById("loading");
loading.style.display="flex";

let formData = new FormData();
formData.append("file",file);
formData.append("type",type);

fetch("/upload-map",{
method:"POST",
headers:{
"X-CSRF-TOKEN":document.querySelector('meta[name="csrf-token"]').content
},
body:formData
})

.then(res=>res.json())

.then(data=>{

console.log("Upload thành công:",type);

readGeoJSON(file,type);

})

.catch(err=>{

console.error("Upload lỗi:",err);

loading.style.display="none";

alert("Upload thất bại");

});

}


/* =========================
READ GEOJSON
========================= */

function readGeoJSON(file,type){

const loading = document.getElementById("loading");

let reader = new FileReader();

reader.onload = function(e){

try{

let geoData = JSON.parse(e.target.result);

/* LƯU DATA */

if(type==="dc_cu"){
geo_dc_cu = geoData;
loadDcCu(geoData);
}

if(type==="dc_moi"){
geo_dc_moi = geoData;
loadDcMoi(geoData);
}

if(type==="quy_hoach"){
geo_quy_hoach = geoData;
loadQuyHoach(geoData);
}

loading.style.display="none";

}catch(err){

console.error(err);
loading.style.display="none";
alert("File GeoJSON lỗi");

}

};

reader.readAsText(file);

}
/* =========================
CLEAR MAP
========================= */

function clearMap(){

if(map.getSource("dc_cu")){
map.removeLayer("dc_cu_fill");
map.removeLayer("dc_cu_line");
map.removeSource("dc_cu");
}

if(map.getSource("dc_moi")){
map.removeLayer("dc_moi_fill");
map.removeLayer("dc_moi_line");
map.removeSource("dc_moi");
}

if(map.getSource("quy_hoach")){
map.removeLayer("quyhoach_fill");
map.removeLayer("quyhoach_line");
map.removeSource("quy_hoach");
}

}


/* =========================
CLEAR BUTTON
========================= */

function clearAll(){

clearMap();

document.getElementById("dc_cu").value="";
document.getElementById("dc_moi").value="";
document.getElementById("quy_hoach").value="";

}
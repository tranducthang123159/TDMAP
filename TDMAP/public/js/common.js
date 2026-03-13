/* =========================
VN2000 PROJECTION
========================= */

proj4.defs(
"VN2000",
"+proj=tmerc +lat_0=0 +lon_0=105 +k=0.9999 +x_0=500000 +y_0=0 +ellps=WGS84 +units=m +no_defs"
);


/* =========================
WGS84 -> VN2000
========================= */

function toVN2000(coord){

let r = proj4("EPSG:4326","VN2000",[coord[0],coord[1]]);

return {x:r[0],y:r[1]};

}


/* =========================
DISTANCE
========================= */

function distVN2000(a,b){

let dx=a.x-b.x;
let dy=a.y-b.y;

return Math.sqrt(dx*dx+dy*dy);

}


/* =========================
AREA
========================= */

function areaVN2000(points){

let area=0;

for(let i=0;i<points.length;i++){

let j=(i+1)%points.length;

area+=points[i].x*points[j].y;
area-=points[j].x*points[i].y;

}

return Math.abs(area)/2;

}


/* =========================
CLEAR LABELS
========================= */

let measureMarkers=[];

function clearMeasure(){

measureMarkers.forEach(m=>m.remove());

measureMarkers=[];

}


/* =========================
HIGHLIGHT PARCEL
========================= */

function highlightParcel(feature){

if(map.getSource("parcelHighlight")){

map.removeLayer("parcelHighlightFill");
map.removeLayer("parcelHighlightLine");
map.removeSource("parcelHighlight");

}

map.addSource("parcelHighlight",{
type:"geojson",
data:feature
});

map.addLayer({
id:"parcelHighlightFill",
type:"fill",
source:"parcelHighlight",
paint:{
"fill-color":"yellow",
"fill-opacity":0.25
}
});

map.addLayer({
id:"parcelHighlightLine",
type:"line",
source:"parcelHighlight",
paint:{
"line-color":"red",
"line-width":3
}
});

}


/* =========================
DRAW MEASURE
========================= */

function drawParcelMeasure(feature){

clearMeasure();


/* GET POLYGON */

let coords;

if(feature.geometry.type==="MultiPolygon"){

coords=feature.geometry.coordinates[0][0];

}else{

coords=feature.geometry.coordinates[0];

}


/* REMOVE LAST DUPLICATE POINT */

coords=coords.slice(0,coords.length-1);


/* REDUCE LABELS IF MANY POINTS */

let step=1;

if(coords.length>300) step=10;
else if(coords.length>200) step=8;
else if(coords.length>120) step=6;
else if(coords.length>60) step=3;
else if(coords.length>30) step=2;


/* CONVERT VN2000 */

let ptsVN=[];

coords.forEach(c=>{
ptsVN.push(toVN2000(c));
});


/* =========================
VERTEX LABELS
========================= */

coords.forEach((p,i)=>{

if(i%step!==0) return;

let el=document.createElement("div");

el.className="pointLabel";

el.innerHTML=i+1;

let m=new maplibregl.Marker({
element:el,
anchor:"center"
})
.setLngLat(p)
.addTo(map);

measureMarkers.push(m);

});


/* =========================
EDGE LENGTH
========================= */

let perimeter=0;

for(let i=0;i<ptsVN.length;i++){

let p1=ptsVN[i];
let p2=ptsVN[(i+1)%ptsVN.length];

let dist=distVN2000(p1,p2);

perimeter+=dist;

if(i%step!==0) continue;

let mid=[
(coords[i][0]+coords[(i+1)%coords.length][0])/2,
(coords[i][1]+coords[(i+1)%coords.length][1])/2
];

let el=document.createElement("div");

el.className="edgeLabel";

el.innerHTML=dist.toFixed(2)+" m";

let m=new maplibregl.Marker({
element:el,
anchor:"center"
})
.setLngLat(mid)
.addTo(map);

measureMarkers.push(m);

}


/* =========================
AREA
========================= */

let area=areaVN2000(ptsVN);


/* =========================
CENTER LABEL
========================= */

let center=turf.centroid(feature).geometry.coordinates;

let el=document.createElement("div");

el.className="areaLabel";

el.innerHTML=`
Diện tích: ${area.toFixed(2)} m²<br>
Chu vi: ${perimeter.toFixed(2)} m
`;

let m=new maplibregl.Marker({
element:el,
anchor:"center"
})
.setLngLat(center)
.addTo(map);

measureMarkers.push(m);

}


/* =========================
MEASURE DISTANCE
========================= */

let kcPoints = [];
let kcMarkers = [];
let kcGPSWatch = null;

let kcTotalDistance = 0;
let kcInfoMarker = null;


/* =========================
START MEASURE
========================= */
let kcActive = false;

function startKC(){

if(!kcActive){

/* tắt đo diện tích */

dtActive=false;
clearDT();

kcActive=true;
mapMode="kc";

showExportPDF();
showMeasureToast("Đang bật đo khoảng cách...",true);

setTimeout(()=>{
showMeasureToast("✔ Đo khoảng cách đã bật<br>Chạm lại để tắt",false);
},1200);

}else{

showMeasureToast("Đang tắt đo...",true);

setTimeout(()=>{

kcActive=false;
hideExportPDF();

mapMode="pin";

showMeasureToast("✔ Đã tắt đo",false);

},1200);

}

}

function showMeasureToast(text, loading) {

    let toast = document.getElementById("measureToast");
    let icon = document.getElementById("toastIcon");
    let txt = document.getElementById("toastText");

    txt.innerHTML = text;

    if (loading) {

        icon.innerHTML = "";
        icon.className = "toast-loading";

    } else {

        icon.className = "toast-success";
        icon.innerHTML = "✔";

    }

    toast.classList.add("show");

    setTimeout(() => {
        toast.classList.remove("show");
    }, 2500);

}

/* =========================
ADD POINT
========================= */

function addKCPoint(lng, lat) {

    kcPoints.push([lng, lat]);

    let el = document.createElement("div");
    el.className = "measure-marker";

    let marker = new maplibregl.Marker({
        element: el,
        anchor: "center"
    })
        .setLngLat([lng, lat])
        .addTo(map);

    kcMarkers.push(marker);

    drawKC();

}


/* =========================
DRAW LINE
========================= */

function drawKC() {

    if (kcPoints.length < 2) return;


    /* LINE */

    let geo = {
        type: "Feature",
        geometry: {
            type: "LineString",
            coordinates: kcPoints
        }
    };

    if (map.getLayer("kc_line")) map.removeLayer("kc_line");
    if (map.getSource("kc_line")) map.removeSource("kc_line");

    map.addSource("kc_line", { type: "geojson", data: geo });

    map.addLayer({
        id: "kc_line",
        type: "line",
        source: "kc_line",
        paint: {
            "line-color": "#ff0000",
            "line-width": 3
        }
    });


    /* =========================
    EDGE LABEL
    ========================= */

    let labels = [];

    for (let i = 1; i < kcPoints.length; i++) {

        let p1 = kcPoints[i - 1];
        let p2 = kcPoints[i];

        /* midpoint */

        let mid = [
            (p1[0] + p2[0]) / 2,
            (p1[1] + p2[1]) / 2
        ];

        /* distance */

        let d = turf.distance(
            turf.point(p1),
            turf.point(p2),
            { units: "meters" }
        );

        let text = d.toFixed(2) + " m";

        /* angle */

        let angle = Math.atan2(
            p2[1] - p1[1],
            p2[0] - p1[0]
        ) * 180 / Math.PI;

        labels.push({
            type: "Feature",
            geometry: { type: "Point", coordinates: mid },
            properties: { text: text, angle: angle }
        });

    }

    if (map.getLayer("kc_label")) map.removeLayer("kc_label");
    if (map.getSource("kc_label")) map.removeSource("kc_label");

    map.addSource("kc_label", {
        type: "geojson",
        data: {
            type: "FeatureCollection",
            features: labels
        }
    });

    map.addLayer({
        id: "kc_label",
        type: "symbol",
        source: "kc_label",
        layout: {
            "text-field": ["get", "text"],
            "text-size": 14,
            "text-rotate": ["get", "angle"],
            "text-allow-overlap": true,
            "text-ignore-placement": true
        },
        paint: {
            "text-color": "#ff0000",
            "text-halo-color": "#ffffff",
            "text-halo-width": 2
        }
    });


    /* =========================
    TOTAL DISTANCE
    ========================= */

    let total = 0;

    for (let i = 1; i < kcPoints.length; i++) {

        let d = turf.distance(
            turf.point(kcPoints[i - 1]),
            turf.point(kcPoints[i]),
            { units: "meters" }
        );

        total += d;

    }

    kcTotalDistance = total;


    /* =========================
    AREA (IF POLYGON)
    ========================= */

    let area = 0;

    if (kcPoints.length >= 3) {

        let poly = turf.polygon([[...kcPoints, kcPoints[0]]]);

        area = turf.area(poly);

    }


    /* =========================
    INFO BOX
    ========================= */

    let last = kcPoints[kcPoints.length - 1];

    if (kcInfoMarker) {
        kcInfoMarker.remove();
    }

    let el = document.createElement("div");

    el.className = "measure-info";

    el.innerHTML =
        "📏 " + total.toFixed(2) + " m" +
        (area > 0 ? "<br>📐 " + area.toFixed(2) + " m²" : "");

    kcInfoMarker = new maplibregl.Marker({
        element: el,
        anchor: "top"
    })
        .setLngLat(last)
        .addTo(map);

}


/* =========================
GPS AUTO ADD POINT
========================= */

function startKCGPS() {

    if (!navigator.geolocation) {
        alert("Trình duyệt không hỗ trợ GPS");
        return;
    }

    kcGPSWatch = navigator.geolocation.watchPosition(function (pos) {

        let lat = pos.coords.latitude;
        let lng = pos.coords.longitude;

        /* bay map tới vị trí */

        map.flyTo({
            center: [lng, lat],
            zoom: 18
        });

        /* thêm điểm */

        addKCPoint(lng, lat);

    },
        function () {
            alert("Không lấy được GPS");
        },
        {
            enableHighAccuracy: true,
            maximumAge: 0
        });

}


/* =========================
UNDO
========================= */


/* =========================
CLEAR
========================= */

function clearKC() {

    kcPoints = [];

    kcMarkers.forEach(m => m.remove());

    kcMarkers = [];

    if (map.getLayer("kc_line")) map.removeLayer("kc_line");
    if (map.getSource("kc_line")) map.removeSource("kc_line");

    if (map.getLayer("kc_label")) map.removeLayer("kc_label");
    if (map.getSource("kc_label")) map.removeSource("kc_label");

    if (kcInfoMarker) {
        kcInfoMarker.remove();
    }

    if (kcGPSWatch) {
        navigator.geolocation.clearWatch(kcGPSWatch);
        kcGPSWatch = null;
    }

}

function exportPDF() {

    if (dtPoints.length === 0 && kcPoints.length === 0) {
        alert("Không có dữ liệu đo");
        return;
    }

    /* lấy điểm */

    let points = mapMode === "dt" ? dtPoints : kcPoints;

    /* khởi tạo PDF */

    const { jsPDF } = window.jspdf;
    let pdf = new jsPDF();

    pdf.setFontSize(14);
    pdf.text("BẢNG TỌA ĐỘ ĐO ĐẠC", 20, 20);

    let y = 40;

    /* header */

    pdf.text("STT", 20, y);
    pdf.text("X (m)", 40, y);
    pdf.text("Y (m)", 90, y);
    pdf.text("Z (m)", 140, y);

    y += 10;

    /* chuyển WGS84 -> VN2000 */

    proj4.defs(
        "VN2000",
        "+proj=tmerc +lat_0=0 +lon_0=108.5 +k=0.9999 +x_0=500000 +y_0=0 +ellps=WGS84 +units=m +no_defs"
    );

    /* loop điểm */

    points.forEach((p, i) => {

        let result = proj4("EPSG:4326", "VN2000", [p[0], p[1]]);

        let x = result[1].toFixed(3);
        let yv = result[0].toFixed(3);

        pdf.text((i + 1).toString(), 20, y);
        pdf.text(x, 40, y);
        pdf.text(yv, 90, y);
        pdf.text("0.000", 140, y);

        y += 8;

    });

    /* lưu */

    pdf.save("toa_do_do_dac.pdf");

}

/* =========================
START GPS
========================= */

function startGPS(){

if(mapMode==="kc"){
startKCGPS();
}

if(mapMode==="dt"){
startDTGPS();
}

}



/* =========================
UNDO KC
========================= */

function undoKC(){

if(kcPoints.length===0) return;

/* xoá điểm cuối */

kcPoints.pop();

/* xoá marker */

let marker=kcMarkers.pop();

if(marker){
marker.remove();
}

/* nếu còn >=2 điểm thì vẽ lại */

if(kcPoints.length>=2){

drawKC();

}else{

/* xoá line */

if(map.getLayer("kc_line")) map.removeLayer("kc_line");
if(map.getSource("kc_line")) map.removeSource("kc_line");

/* xoá label */

if(map.getLayer("kc_label")) map.removeLayer("kc_label");
if(map.getSource("kc_label")) map.removeSource("kc_label");

/* xoá info */

if(kcInfoMarker){
kcInfoMarker.remove();
}

}

}
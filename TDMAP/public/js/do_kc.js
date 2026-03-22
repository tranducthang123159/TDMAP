/* =========================
MEASURE DISTANCE
========================= */

let kcPoints = [];
let kcMarkers = [];
let kcGPSWatch = null;

let kcTotalDistance = 0;
let kcInfoMarker = null;

let kcActive = false;

/* =========================
MEASURE SNAP HELPERS
========================= */

const MEASURE_SNAP_PIXEL = 18;
const MEASURE_CLOSE_PIXEL = 22;

function measurePixelDistance(lngLat1, lngLat2) {
    const p1 = map.project(lngLat1);
    const p2 = map.project(lngLat2);

    const dx = p1.x - p2.x;
    const dy = p1.y - p2.y;

    return Math.sqrt(dx * dx + dy * dy);
}

function measureFindNearestPoint(targetLngLat, points, snapPx = MEASURE_SNAP_PIXEL) {
    if (!points || !points.length) return null;

    let nearest = null;
    let min = Infinity;

    points.forEach(p => {
        const d = measurePixelDistance(
            { lng: targetLngLat.lng, lat: targetLngLat.lat },
            { lng: p[0], lat: p[1] }
        );

        if (d < min) {
            min = d;
            nearest = p;
        }
    });

    if (min <= snapPx) {
        return {
            point: nearest,
            distancePx: min
        };
    }

    return null;
}

function measureIsSamePoint(p1, p2, toleranceMeters = 0.05) {
    if (!p1 || !p2) return false;

    const d = turf.distance(
        turf.point(p1),
        turf.point(p2),
        { units: "meters" }
    );

    return d <= toleranceMeters;
}

/* =========================
START MEASURE
========================= */

function startKC() {

    if (!kcActive) {

        /* tắt đo diện tích */
        dtActive = false;
        clearDT();

        kcActive = true;
        mapMode = "kc";

        showExportPDF();
        showMeasureToast("Đang bật đo khoảng cách...", true);

        setTimeout(() => {
            showMeasureToast("✔ Đo khoảng cách đã bật<br>Chạm lại để tắt", false);
        }, 1200);

    } else {

        showMeasureToast("Đang tắt đo...", true);

        setTimeout(() => {
            kcActive = false;
            hideExportPDF();

            mapMode = "pin";

            showMeasureToast("✔ Đã tắt đo", false);
        }, 1200);
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

    let point = [lng, lat];

    /* snap vào điểm cũ */
    const snapped = measureFindNearestPoint({ lng, lat }, kcPoints);
    if (snapped) {
        point = snapped.point;
    }

    /* tránh trùng điểm liên tiếp */
    const last = kcPoints[kcPoints.length - 1];
    if (last && measureIsSamePoint(last, point)) {
        return;
    }

    kcPoints.push(point);

    let el = document.createElement("div");
    el.className = "measure-marker";

    let marker = new maplibregl.Marker({
        element: el,
        anchor: "center"
    })
        .setLngLat(point)
        .addTo(map);

    kcMarkers.push(marker);

    drawKC();
}

/* =========================
DRAW LINE
========================= */

function drawKC() {

    if (kcPoints.length < 2) {

        if (map.getLayer("kc_line")) map.removeLayer("kc_line");
        if (map.getSource("kc_line")) map.removeSource("kc_line");

        if (map.getLayer("kc_label")) map.removeLayer("kc_label");
        if (map.getSource("kc_label")) map.removeSource("kc_label");

        if (kcInfoMarker) {
            kcInfoMarker.remove();
            kcInfoMarker = null;
        }

        return;
    }

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

    /* EDGE LABEL */
    let labels = [];

    for (let i = 1; i < kcPoints.length; i++) {
        let p1 = kcPoints[i - 1];
        let p2 = kcPoints[i];

        let mid = [
            (p1[0] + p2[0]) / 2,
            (p1[1] + p2[1]) / 2
        ];

        let d = turf.distance(
            turf.point(p1),
            turf.point(p2),
            { units: "meters" }
        );

        let text = d.toFixed(2) + " m";

        let angle = Math.atan2(
            p2[1] - p1[1],
            p2[0] - p1[0]
        ) * 180 / Math.PI;

        if (angle > 90 || angle < -90) angle += 180;

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

    /* TOTAL DISTANCE */
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

    /* AREA (IF POLYGON) */
    let area = 0;

    if (kcPoints.length >= 3) {
        let poly = turf.polygon([[...kcPoints, kcPoints[0]]]);
        area = turf.area(poly);
    }

    /* INFO BOX */
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

    navigator.geolocation.getCurrentPosition(function (pos) {

        let lat = pos.coords.latitude;
        let lng = pos.coords.longitude;

        map.flyTo({
            center: [lng, lat],
            zoom: 18
        });

        addKCPoint(lng, lat);

    }, function (err) {
        alert("Không lấy được GPS");
        console.error(err);
    }, {
        enableHighAccuracy: true,
        maximumAge: 0,
        timeout: 10000
    });
}



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
        kcInfoMarker = null;
    }

    if (kcGPSWatch) {
        navigator.geolocation.clearWatch(kcGPSWatch);
        kcGPSWatch = null;
    }
}

/* =========================
EXPORT PDF
========================= */

function exportTXT() {

    let points = mapMode === "dt" ? dtPoints : kcPoints;

    if (!points || points.length === 0) {
        alert("Chưa có dữ liệu đo!");
        return;
    }

    let content = "\uFEFFBẢNG TỌA ĐỘ ĐO ĐẠC\n";
    content += "STT\tX(m)\tY(m)\tZ(m)\n";

    proj4.defs(
        "VN2000",
        "+proj=tmerc +lat_0=0 +lon_0=108.5 +k=0.9999 +x_0=500000 +y_0=0 +ellps=WGS84 +units=m +no_defs"
    );

    points.forEach((p, i) => {

        let result = proj4("EPSG:4326", "VN2000", [p[0], p[1]]);

        let x = result[1].toFixed(3);
        let yv = result[0].toFixed(3);

        content += `${i + 1}\t${x}\t${yv}\t0.000\n`;
    });

    let blob = new Blob([content], { type: "text/plain;charset=utf-8;" });

    let link = document.createElement("a");
    link.href = URL.createObjectURL(blob);
    link.download = "toa_do_do_dac.txt";

    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
/* =========================
START GPS
========================= */

function startGPS() {
    if (mapMode === "kc") {
        startKCGPS();
    }

    if (mapMode === "dt") {
        startDTGPS();
    }
}

/* =========================
UNDO KC
========================= */

function undoKC() {

    if (kcPoints.length === 0) return;

    kcPoints.pop();

    let marker = kcMarkers.pop();
    if (marker) marker.remove();

    if (kcPoints.length >= 2) {
        drawKC();
    } else {

        if (map.getLayer("kc_line")) map.removeLayer("kc_line");
        if (map.getSource("kc_line")) map.removeSource("kc_line");

        if (map.getLayer("kc_label")) map.removeLayer("kc_label");
        if (map.getSource("kc_label")) map.removeSource("kc_label");

        if (kcInfoMarker) {
            kcInfoMarker.remove();
            kcInfoMarker = null;
        }
    }
}
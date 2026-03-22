/* =========================
SPLIT PARCEL MODULE
========================= */

window.splitMode = false;
window.splitSelectedParcel = null;

let splitPoints = [];
let splitMarkers = [];
let splitPreviewMarkers = [];
let splitResultFeatures = [];
let splitClickBound = false;

const SPLIT_LINE_SOURCE = "split_line_source";
const SPLIT_PREVIEW_SOURCE = "split_preview_source";

/* =========================
TOGGLE SPLIT
========================= */

function toggleSplitParcel() {
    if (window.splitMode) {
        resetSplitParcel(true);
        return;
    }
    startSplitParcel();
}

/* =========================
START SPLIT
========================= */

function startSplitParcel() {
    if (!window.currentFeature) {
        showToast("Vui lòng chọn thửa trước.", "warning");
        return;
    }

    resetSplitParcel(false);

    window.splitMode = true;
    window.splitSelectedParcel = window.currentFeature;
    splitPoints = [];
    splitResultFeatures = [];
    window.mapMode = "split";

    map.getCanvas().style.cursor = "crosshair";

    highlightParcel(window.splitSelectedParcel);
    drawParcelMeasure(window.splitSelectedParcel);

    updateSplitStatus("✏️ Đang tách thửa: chọn 2 điểm trên ranh", true);
    updateSplitButtons();

    showToast("Đã bật tách thửa. Chọn 2 điểm trên ranh thửa.", "success");
}

/* =========================
RESET SPLIT
========================= */

function resetSplitParcel(showMsg = true) {
    window.splitMode = false;
    window.splitSelectedParcel = null;
    window.mapMode = "pin";

    splitPoints = [];
    splitResultFeatures = [];

    splitMarkers.forEach(m => m.remove());
    splitMarkers = [];

    splitPreviewMarkers.forEach(m => m.remove());
    splitPreviewMarkers = [];

    removeSplitLayers();

    map.getCanvas().style.cursor = "";

    updateSplitStatus("", false);
    updateSplitButtons();

    if (showMsg) {
        showToast("Đã thoát chế độ tách thửa.", "info");
    }
}

/* =========================
UNDO POINT
========================= */

function undoSplitPoint() {
    if (!window.splitMode) {
        showToast("Bạn chưa bật chế độ tách thửa.", "warning");
        return;
    }

    if (!splitPoints.length) {
        showToast("Chưa có điểm nào để hoàn tác.", "warning");
        return;
    }

    splitPoints.pop();

    let mk = splitMarkers.pop();
    if (mk) mk.remove();

    drawSplitLine();
    clearSplitPreview();
    updateSplitButtons();

    if (splitPoints.length === 0) {
        updateSplitStatus("✏️ Đang tách thửa: chọn 2 điểm trên ranh", true);
    } else if (splitPoints.length === 1) {
        updateSplitStatus("✏️ Đã chọn điểm 1: chọn điểm 2", true);
    }

    showToast("Đã hoàn tác 1 điểm.", "info");
}

/* =========================
BIND MAP CLICK
========================= */

function bindSplitMapClick() {
    if (splitClickBound) return;
    splitClickBound = true;

    map.on("click", function (e) {
        if (!window.splitMode) return;
        if (!window.splitSelectedParcel) return;

        if (splitPoints.length >= 2) {
            showToast("Đã đủ 2 điểm cắt. Bấm Cắt, Hoàn tác hoặc Thoát.", "warning");
            return;
        }

        let coord = [e.lngLat.lng, e.lngLat.lat];
        coord = snapPointToParcelBoundary(window.splitSelectedParcel, coord);

        if (splitPoints.length === 1 && sameCoord(splitPoints[0], coord)) {
            showToast("Điểm thứ 2 trùng điểm thứ 1. Hãy chọn vị trí khác.", "warning");
            return;
        }

        splitPoints.push(coord);

        let marker = createSplitPointMarker(splitPoints.length, coord);
        splitMarkers.push(marker);

        drawSplitLine();
        updateSplitButtons();

        if (splitPoints.length === 1) {
            updateSplitStatus("✏️ Đã chọn điểm 1: chọn điểm 2", true);
            showToast("Đã chọn điểm 1.", "info");
        }

        if (splitPoints.length === 2) {
            updateSplitStatus("✏️ Đã đủ 2 điểm: bấm Cắt để hoàn tất", true);
            previewSplitParcel();
        }
    });
}

bindSplitMapClick();

/* =========================
CREATE SPLIT POINT MARKER
========================= */

function createSplitPointMarker(index, lngLat) {
    const el = document.createElement("div");
    el.style.width = "30px";
    el.style.height = "30px";
    el.style.borderRadius = "50%";
    el.style.background = "#ef4444";
    el.style.color = "#fff";
    el.style.display = "flex";
    el.style.alignItems = "center";
    el.style.justifyContent = "center";
    el.style.fontWeight = "700";
    el.style.fontSize = "14px";
    el.style.border = "2px solid #fff";
    el.style.boxShadow = "0 4px 12px rgba(0,0,0,0.25)";
    el.innerText = index;

    return new maplibregl.Marker({
        element: el,
        anchor: "center"
    })
        .setLngLat(lngLat)
        .addTo(map);
}

/* =========================
UPDATE BUTTONS
========================= */

function updateSplitButtons() {
    const btnStart = document.getElementById("btnSplitStart");
    const btnFinish = document.getElementById("btnSplitFinish");
    const btnUndo = document.getElementById("btnSplitUndo");
    const btnExit = document.getElementById("btnSplitExit");

    if (btnStart) {
        if (window.splitMode) {
            btnStart.innerText = "🟢 Đang tách thửa";
            btnStart.style.background = "#16a34a";
            btnStart.style.color = "#fff";
        } else {
            btnStart.innerText = "✏️ Bật tách thửa";
            btnStart.style.background = "#facc15";
            btnStart.style.color = "#000";
        }
    }

    if (btnFinish) {
        btnFinish.disabled = !(window.splitMode && splitPoints.length === 2);
        btnFinish.style.opacity = btnFinish.disabled ? "0.6" : "1";
        btnFinish.style.cursor = btnFinish.disabled ? "not-allowed" : "pointer";
    }

    if (btnUndo) {
        btnUndo.disabled = !(window.splitMode && splitPoints.length > 0);
        btnUndo.style.opacity = btnUndo.disabled ? "0.6" : "1";
        btnUndo.style.cursor = btnUndo.disabled ? "not-allowed" : "pointer";
    }

    if (btnExit) {
        btnExit.disabled = !window.splitMode;
        btnExit.style.opacity = btnExit.disabled ? "0.6" : "1";
        btnExit.style.cursor = btnExit.disabled ? "not-allowed" : "pointer";
    }
}

/* =========================
SNAP POINT TO BOUNDARY
========================= */

function snapPointToParcelBoundary(feature, coord) {
    try {
        const ring = getParcelCoords(feature);
        if (!ring.length) return coord;

        const line = turf.lineString([...ring, ring[0]]);
        const snapped = turf.nearestPointOnLine(line, turf.point(coord));

        return snapped.geometry.coordinates;
    } catch (err) {
        console.error("Snap lỗi:", err);
        return coord;
    }
}

/* =========================
DRAW SPLIT LINE
========================= */

function drawSplitLine() {
    if (!splitPoints.length) {
        if (map.getLayer("split_line")) map.removeLayer("split_line");
        if (map.getSource(SPLIT_LINE_SOURCE)) map.removeSource(SPLIT_LINE_SOURCE);
        return;
    }

    const line = {
        type: "Feature",
        geometry: {
            type: "LineString",
            coordinates: splitPoints
        }
    };

    if (map.getSource(SPLIT_LINE_SOURCE)) {
        map.getSource(SPLIT_LINE_SOURCE).setData(line);
    } else {
        map.addSource(SPLIT_LINE_SOURCE, {
            type: "geojson",
            data: line
        });

        map.addLayer({
            id: "split_line",
            type: "line",
            source: SPLIT_LINE_SOURCE,
            paint: {
                "line-color": "#ef4444",
                "line-width": 3,
                "line-dasharray": [2, 2]
            }
        });
    }
}

/* =========================
PREVIEW SPLIT
========================= */

function previewSplitParcel() {
    clearSplitPreview();

    if (!window.splitSelectedParcel || splitPoints.length !== 2) return;

    try {
        const result = splitPolygonByTwoBoundaryPoints(
            window.splitSelectedParcel,
            splitPoints[0],
            splitPoints[1]
        );

        if (!result || result.length < 2) {
            showToast("Không tách được thửa. Hãy chọn lại 2 điểm trên ranh.", "error");
            return;
        }

        splitResultFeatures = result;
        showSplitPreview(result);
        showToast("Đã xem trước kết quả tách thửa.", "success");
    } catch (err) {
        console.error("Preview split lỗi:", err);
        showToast("Lỗi khi tách thử thửa.", "error");
    }
}

/* =========================
FINISH SPLIT
========================= */

function finishSplitParcel() {
    if (!window.splitSelectedParcel) {
        showToast("Chưa chọn thửa để tách.", "warning");
        return;
    }

    if (splitPoints.length !== 2) {
        showToast("Bạn cần chọn đúng 2 điểm cắt.", "warning");
        return;
    }

    if (!splitResultFeatures.length) {
        previewSplitParcel();
        if (!splitResultFeatures.length) return;
    }

    const oldProps = window.splitSelectedParcel.properties || {};
    const f1 = splitResultFeatures[0];
    const f2 = splitResultFeatures[1];

    f1.properties = {
        ...oldProps,
        SHTHUA: (oldProps.SHTHUA || "") + "_1",
        DIENTICH: Number(turf.area(f1)).toFixed(2)
    };

    f2.properties = {
        ...oldProps,
        SHTHUA: (oldProps.SHTHUA || "") + "_2",
        DIENTICH: Number(turf.area(f2)).toFixed(2)
    };

    updateSplitStatus(
        `✅ Đã tách: ${f1.properties.DIENTICH} m² | ${f2.properties.DIENTICH} m²`,
        true
    );

    showToast("Tách thửa thành công.", "success", 2600);

    console.log("Kết quả tách:", {
        oldParcel: window.splitSelectedParcel,
        newParcels: [f1, f2]
    });

    window.splitMode = false;
    window.mapMode = "pin";
    map.getCanvas().style.cursor = "";

    updateSplitButtons();

    // Nếu muốn tải GeoJSON ngay thì mở dòng dưới
    // downloadSplitResultGeoJSON([f1, f2]);
}

/* =========================
SHOW PREVIEW
========================= */

function showSplitPreview(features) {
    const fc = {
        type: "FeatureCollection",
        features: features.map((f, i) => ({
            ...f,
            id: i
        }))
    };

    if (map.getSource(SPLIT_PREVIEW_SOURCE)) {
        map.getSource(SPLIT_PREVIEW_SOURCE).setData(fc);
    } else {
        map.addSource(SPLIT_PREVIEW_SOURCE, {
            type: "geojson",
            data: fc
        });

        map.addLayer({
            id: "split_preview_fill",
            type: "fill",
            source: SPLIT_PREVIEW_SOURCE,
            paint: {
                "fill-color": [
                    "match",
                    ["get", "id"],
                    0, "#22c55e",
                    1, "#3b82f6",
                    "#9ca3af"
                ],
                "fill-opacity": 0.35
            }
        });

        map.addLayer({
            id: "split_preview_line",
            type: "line",
            source: SPLIT_PREVIEW_SOURCE,
            paint: {
                "line-color": "#f97316",
                "line-width": 2
            }
        });
    }

    features.forEach((f, i) => {
        const area = turf.area(f);
        const center = turf.centroid(f).geometry.coordinates;

        const mkEl = document.createElement("div");
        mkEl.style.padding = "6px 10px";
        mkEl.style.borderRadius = "999px";
        mkEl.style.background = i === 0 ? "#16a34a" : "#2563eb";
        mkEl.style.color = "#fff";
        mkEl.style.fontSize = "12px";
        mkEl.style.fontWeight = "700";
        mkEl.style.boxShadow = "0 4px 12px rgba(0,0,0,0.2)";
        mkEl.innerText = `Thửa ${i + 1}: ${area.toFixed(2)} m²`;

        const mk = new maplibregl.Marker({
            element: mkEl,
            anchor: "center"
        })
            .setLngLat(center)
            .addTo(map);

        splitPreviewMarkers.push(mk);
    });
}

/* =========================
CLEAR PREVIEW
========================= */

function clearSplitPreview() {
    splitResultFeatures = [];

    splitPreviewMarkers.forEach(m => m.remove());
    splitPreviewMarkers = [];

    if (map.getLayer("split_preview_fill")) map.removeLayer("split_preview_fill");
    if (map.getLayer("split_preview_line")) map.removeLayer("split_preview_line");
    if (map.getSource(SPLIT_PREVIEW_SOURCE)) map.removeSource(SPLIT_PREVIEW_SOURCE);
}

/* =========================
REMOVE ALL SPLIT LAYERS
========================= */

function removeSplitLayers() {
    clearSplitPreview();

    if (map.getLayer("split_line")) map.removeLayer("split_line");
    if (map.getSource(SPLIT_LINE_SOURCE)) map.removeSource(SPLIT_LINE_SOURCE);
}

/* =========================
GET OUTER RING
========================= */

function getOuterRing(feature) {
    const coords = getParcelCoords(feature);
    if (!coords || coords.length < 3) return [];

    let ring = [...coords];

    const first = ring[0];
    const last = ring[ring.length - 1];

    if (first[0] !== last[0] || first[1] !== last[1]) {
        ring.push(first);
    }

    return ring;
}

/* =========================
HELPER
========================= */

function sameCoord(a, b, eps = 1e-10) {
    return Math.abs(a[0] - b[0]) < eps && Math.abs(a[1] - b[1]) < eps;
}

function pointOnSegment(p, a, b, eps = 1e-10) {
    const cross = (p[1] - a[1]) * (b[0] - a[0]) - (p[0] - a[0]) * (b[1] - a[1]);
    if (Math.abs(cross) > eps) return false;

    const dot = (p[0] - a[0]) * (b[0] - a[0]) + (p[1] - a[1]) * (b[1] - a[1]);
    if (dot < -eps) return false;

    const lenSq = (b[0] - a[0]) ** 2 + (b[1] - a[1]) ** 2;
    if (dot - lenSq > eps) return false;

    return true;
}

/* =========================
INSERT POINT INTO RING
========================= */

function insertPointIntoRing(ring, point) {
    let newRing = [];

    for (let i = 0; i < ring.length - 1; i++) {
        const a = ring[i];
        const b = ring[i + 1];

        newRing.push(a);

        if (sameCoord(point, a) || sameCoord(point, b)) continue;

        if (pointOnSegment(point, a, b)) {
            newRing.push(point);
        }
    }

    newRing.push(newRing[0]);
    return newRing;
}

/* =========================
FIND POINT INDEX
========================= */

function findPointIndex(ring, point) {
    for (let i = 0; i < ring.length - 1; i++) {
        if (sameCoord(ring[i], point)) return i;
    }
    return -1;
}

/* =========================
SLICE RING PATH
========================= */

function sliceRingPath(ring, startIdx, endIdx) {
    const n = ring.length - 1;
    const path = [];
    let i = startIdx;

    while (true) {
        path.push(ring[i]);
        if (i === endIdx) break;
        i = (i + 1) % n;
    }

    return path;
}

/* =========================
BUILD POLYGON
========================= */

function buildPolygonFromCoords(coords) {
    let ring = [...coords];

    if (!sameCoord(ring[0], ring[ring.length - 1])) {
        ring.push(ring[0]);
    }

    if (ring.length < 4) return null;

    const poly = turf.polygon([ring]);

    if (turf.area(poly) <= 0) return null;

    return poly;
}

/* =========================
MAIN SPLIT LOGIC
========================= */

function splitPolygonByTwoBoundaryPoints(feature, p1, p2) {
    let ring = getOuterRing(feature);
    if (!ring.length) return null;

    ring = insertPointIntoRing(ring, p1);
    ring = insertPointIntoRing(ring, p2);

    const i1 = findPointIndex(ring, p1);
    const i2 = findPointIndex(ring, p2);

    if (i1 === -1 || i2 === -1 || i1 === i2) {
        throw new Error("Không xác định được vị trí điểm cắt trên ranh.");
    }

    const pathA = sliceRingPath(ring, i1, i2);
    const pathB = sliceRingPath(ring, i2, i1);

    const poly1Coords = [...pathA, p1];
    const poly2Coords = [...pathB, p2];

    const poly1 = buildPolygonFromCoords(poly1Coords);
    const poly2 = buildPolygonFromCoords(poly2Coords);

    if (!poly1 || !poly2) {
        throw new Error("Không tạo được polygon mới.");
    }

    return [poly1, poly2];
}

/* =========================
DOWNLOAD GEOJSON
========================= */

function downloadSplitResultGeoJSON(features) {
    const fc = {
        type: "FeatureCollection",
        features: features
    };

    const blob = new Blob([JSON.stringify(fc, null, 2)], {
        type: "application/geo+json;charset=utf-8"
    });

    const url = URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = url;
    a.download = "split_result.geojson";
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}
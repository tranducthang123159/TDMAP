/* =========================
INIT MAP
========================= */

window.map = new maplibregl.Map({
    container: "map",
    style: {
        version: 8,
        glyphs: "https://demotiles.maplibre.org/font/{fontstack}/{range}.pbf",
        sources: {
            basemap: {
                type: "raster",
                tiles: [
                    "https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}"
                ],
                tileSize: 256,
                maxzoom: 22
            }
        },
        layers: [
            {
                id: "basemap",
                type: "raster",
                source: "basemap"
            }
        ]
    },
    center: [106.7, 10.8],
    zoom: 13,
    maxZoom: 22,
    minZoom: 3,
    antialias: false,
    preserveDrawingBuffer: false,
    fadeDuration: 0,
    attributionControl: false
});

/* =========================
CHẶN LOAD TRÙNG FILE
========================= */
if (window.__MAP_JS_ALREADY_LOADED__) {
    console.warn("map.js đã load trước đó, bỏ qua lần load trùng.");
} else {
    window.__MAP_JS_ALREADY_LOADED__ = true;
}

/* =========================
MAP MODE
========================= */

window.currentMapMeta = null;
window.fullLoaded = false;
window.liteLoaded = false;
window.ultraLoaded = false;
window.currentRenderedMapType = null;

window.mapCache = {
    ultra: {},
    lite: {},
    full: {}
};

window.mapMode = "pin";

map.dragRotate.disable();
map.touchZoomRotate.disableRotation();
map.doubleClickZoom.enable();

/* =========================
BASEMAP SWITCH
========================= */

function setBaseMap(type) {
    let tiles = [];

    if (type === "street") {
        tiles = [
            "https://a.tile.openstreetmap.org/{z}/{x}/{y}.png",
            "https://b.tile.openstreetmap.org/{z}/{x}/{y}.png",
            "https://c.tile.openstreetmap.org/{z}/{x}/{y}.png"
        ];
    } else if (type === "sat") {
        tiles = [
            "https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}"
        ];
    } else if (type === "topo") {
        tiles = [
            "https://tile.opentopomap.org/{z}/{x}/{y}.png"
        ];
    }

    const source = map.getSource("basemap");
    if (source && tiles.length) {
        source.setTiles(tiles);
    }
}

/* =========================
UPLOAD MAP
========================= */

function loadMap() {
    const dc_cu = document.getElementById("dc_cu")?.files?.[0];
    const dc_moi = document.getElementById("dc_moi")?.files?.[0];
    const quy_hoach = document.getElementById("quy_hoach")?.files?.[0];

    if (!dc_cu && !dc_moi && !quy_hoach) {
        alert("Chọn ít nhất 1 file");
        return;
    }

    if (dc_cu) uploadAndLoad(dc_cu, "dc_cu");
    if (dc_moi) uploadAndLoad(dc_moi, "dc_moi");
    if (quy_hoach) uploadAndLoad(quy_hoach, "quy_hoach");
}

/* =========================
CLICK MAP
========================= */

window.marker = window.marker || null;
window.markerPopup = window.markerPopup || null;

function getInteractiveParcelLayers() {
    const layers = [];

    if (map.getLayer("dc_moi_fill")) layers.push("dc_moi_fill");
    if (map.getLayer("dc_cu_fill")) layers.push("dc_cu_fill");
    if (map.getLayer("quyhoach_fill")) layers.push("quyhoach_fill");
    if (map.getLayer("canh_fill")) layers.push("canh_fill");

    return layers;
}

map.on("click", function (e) {
    const interactiveLayers = getInteractiveParcelLayers();
    let features = [];

    if (interactiveLayers.length) {
        features = map.queryRenderedFeatures(e.point, {
            layers: interactiveLayers
        });
    }

    if (features.length > 0) {
        return;
    }

    const lng = e.lngLat.lng;
    const lat = e.lngLat.lat;

    if (window.mapMode === "pin") {
        addMarker(lat, lng);
    }

    if (window.mapMode === "dt" && typeof addDTPoint === "function") {
        addDTPoint(lng, lat);
    }

    if (window.mapMode === "kc" && typeof addKCPoint === "function") {
        addKCPoint(lng, lat);
    }
});

/* =========================
DOUBLE CLICK MAP
========================= */

map.on("dblclick", function () {
    if (
        window.mapMode === "dt" &&
        typeof dtPoints !== "undefined" &&
        Array.isArray(dtPoints) &&
        dtPoints.length > 2 &&
        typeof drawDT === "function"
    ) {
        dtPoints.push(dtPoints[0]);
        drawDT();
    }
});

/* =========================
OPEN GOOGLE DIRECTIONS
========================= */

function openGoogleDirections(lat, lng) {
    const url = `https://www.google.com/maps/dir/?api=1&destination=${lat},${lng}&travelmode=driving`;
    window.open(url, "_blank");
}

/* =========================
COPY COORD
========================= */

function copyCoordinates(lat, lng) {
    const text = `${lat}, ${lng}`;
    navigator.clipboard.writeText(text)
        .then(() => {
            alert("Đã copy tọa độ: " + text);
        })
        .catch(() => {
            alert("Không copy được tọa độ");
        });
}

/* =========================
ADD MARKER
========================= */

function addMarker(lat, lng) {
    clearMarker();

    const el = document.createElement("div");
    el.className = "marker";

    window.marker = new maplibregl.Marker(el)
        .setLngLat([lng, lat])
        .addTo(map);

    window.markerPopup = new maplibregl.Popup({
        offset: 28,
        closeOnClick: false
    })
        .setLngLat([lng, lat])
        .setHTML(`
            <div class="gm-popup">
                <div class="gm-popup-head">📍 Vị trí đã ghim</div>
                <div class="gm-popup-loading">Đang lấy thông tin vị trí...</div>
            </div>
        `)
        .addTo(map);

    getAddress(lat, lng);
}

/* =========================
GET ADDRESS + VN2000
========================= */

function getAddress(lat, lng) {
    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
        .then(res => res.json())
        .then(data => {
            const address = data.display_name || "Không rõ địa chỉ";

            if (typeof updateVN2000 === "function") {
                updateVN2000(108.5);
            }

            let vnX = "";
            let vnY = "";

            if (typeof proj4 !== "undefined" && typeof currentKTT !== "undefined") {
                const result = proj4(
                    proj4.defs("EPSG:4326"),
                    proj4.defs("VN2000_Current"),
                    [lng, lat]
                );

                vnX = result[1].toFixed(3);
                vnY = result[0].toFixed(3);
            }

            const html = `
                <div class="gm-popup">
                    <div class="gm-popup-head">📍 Vị trí đã ghim</div>
                    <div class="gm-popup-body">
                        <div class="gm-popup-row">
                            <span class="gm-popup-label">Địa chỉ:</span><br>
                            ${address}
                        </div>

                        <div class="gm-popup-row">
                            <span class="gm-popup-label">WGS84:</span><br>
                            <span class="gm-popup-coord">${lat.toFixed(6)}, ${lng.toFixed(6)}</span>
                        </div>

                        <div class="gm-popup-row">
                            <span class="gm-popup-label">VN2000:</span><br>
                            <span class="gm-popup-coord">${vnX}, ${vnY}</span>
                        </div>

                        <div class="gm-popup-actions">
                            <button class="gm-popup-btn primary" onclick="openGoogleDirections(${lat}, ${lng})">🧭 Chỉ đường</button>
                            <button class="gm-popup-btn gray" onclick="copyCoordinates(${lat}, ${lng})">📋 Copy tọa độ</button>
                        </div>
                    </div>
                </div>
            `;

            if (window.markerPopup) {
                window.markerPopup.setHTML(html);
            }

            const pin = document.getElementById("pinContent");
            if (pin) {
                pin.innerHTML = `
                    <div>🏠 ${address}</div>
                    <div>🌍 WGS84: ${lat.toFixed(6)}, ${lng.toFixed(6)}</div>
                    <div>📐 VN2000: ${vnX}, ${vnY}</div>
                `;
            }
        })
        .catch(err => {
            console.error("Lỗi lấy địa chỉ:", err);
        });
}

/* =========================
PANEL
========================= */

function openPanel() {
    document.getElementById("locationPanel")?.classList.add("active");
}

function closePanel() {
    document.getElementById("locationPanel")?.classList.remove("active");

    const pin = document.getElementById("pinContent");
    if (pin) pin.innerHTML = "";
}

/* =========================
GPS
========================= */

function locateMe() {
    if (!navigator.geolocation) {
        alert("Trình duyệt không hỗ trợ GPS");
        return;
    }

    navigator.geolocation.getCurrentPosition(function (pos) {
        const lat = pos.coords.latitude;
        const lng = pos.coords.longitude;

        map.easeTo({
            center: [lng, lat],
            zoom: 18,
            duration: 0
        });

        addMarker(lat, lng);
    }, function () {
        alert("Không lấy được vị trí");
    });
}

/* =========================
RELOAD MAP
========================= */

function reloadMap() {
    location.reload();
}

/* =========================
CLEAR MARKER
========================= */

function clearMarker() {
    if (window.marker) {
        window.marker.remove();
        window.marker = null;
    }

    if (window.markerPopup) {
        window.markerPopup.remove();
        window.markerPopup = null;
    }

    const pin = document.getElementById("pinContent");
    if (pin) pin.innerHTML = "";
}

function removeMarker() {
    clearMarker();
}

/* =========================
POPUP LAYER
========================= */

function togglePopup() {
    const popup = document.getElementById("mapLayerPopup");
    if (!popup) return;

    popup.style.display = popup.style.display === "block" ? "none" : "block";
}

function closePopup() {
    const popup = document.getElementById("mapLayerPopup");
    if (popup) popup.style.display = "none";
}

/* =========================
MODE SWITCH
========================= */

function startPin() {
    window.mapMode = "pin";
}

function startKC() {
    window.mapMode = "kc";
}

function startDT() {
    window.mapMode = "dt";
}

/* =========================
BẢN ĐỒ ĐÃ LƯU
========================= */

function toggleSavedMaps() {
    const panel = document.getElementById("savedMapPanel");
    if (!panel) return;

    panel.classList.toggle("active");

    if (panel.classList.contains("active")) {
        loadSavedMaps();
    }
}

function normalizeMapType(type) {
    const mapTypes = {
        dcmoi: "dc_moi",
        dccu: "dc_cu",
        quyhoach: "quy_hoach",
        dc_moi: "dc_moi",
        dc_cu: "dc_cu",
        quy_hoach: "quy_hoach",
        canh: "canh"
    };

    return mapTypes[String(type || "").trim().toLowerCase()] || String(type || "").trim().toLowerCase();
}

function normalizeIncomingGeoJSON(geojson) {
    if (typeof normalizeGeoJSON === "function") {
        try {
            return normalizeGeoJSON(geojson);
        } catch (e) {
            console.warn("normalizeGeoJSON lỗi, dùng dữ liệu gốc:", e);
        }
    }

    return geojson;
}

function renderMapByType(type, geojson) {
    if (!geojson) return;

    const normalized = normalizeMapType(type);
    const cleanGeojson = normalizeIncomingGeoJSON(geojson);

    window.currentRenderedMapType = normalized;

    if (normalized === "dc_cu") {
        window.geo_dc_cu = cleanGeojson;
        if (typeof loadDcCu === "function") loadDcCu(cleanGeojson);
        return;
    }

    if (normalized === "dc_moi") {
        window.geo_dc_moi = cleanGeojson;
        if (typeof loadDcMoi === "function") loadDcMoi(cleanGeojson);
        return;
    }

    if (normalized === "quy_hoach") {
        window.geo_quy_hoach = cleanGeojson;
        if (typeof loadQuyHoach === "function") loadQuyHoach(cleanGeojson);
        return;
    }

    if (normalized === "canh") {
        window.geo_canh = cleanGeojson;
        if (typeof loadCanh === "function") loadCanh(cleanGeojson);
        return;
    }

    throw new Error("Không tìm thấy hàm xử lý loại bản đồ: " + type);
}

function getMapTypeLabel(type) {
    const normalized = normalizeMapType(type);

    const labels = {
        dc_moi: "ĐC mới",
        dc_cu: "ĐC cũ",
        quy_hoach: "Quy hoạch",
        canh: "Canh"
    };

    return labels[normalized] || type || "Không rõ";
}

function loadSavedMaps() {
    const list = document.getElementById("savedMapList");
    if (!list) return;

    list.innerHTML = `<div class="saved-map-empty">Đang tải dữ liệu...</div>`;

    fetch("/my-files-json", {
        credentials: "same-origin",
        headers: {
            "Accept": "application/json",
            "X-Requested-With": "XMLHttpRequest"
        }
    })
        .then(async res => {
            const text = await res.text();
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            return JSON.parse(text);
        })
        .then(data => {
            if (!data.success || !data.files || data.files.length === 0) {
                list.innerHTML = `<div class="saved-map-empty">Chưa có file nào đã lưu</div>`;
                return;
            }

            let html = "";

            data.files.forEach(file => {
                html += `
                    <div class="saved-map-item" onclick="viewSavedMap(${file.id})">
                        <div class="saved-map-name">${file.file_name}</div>
                        <div class="saved-map-meta">
                            <span>Loại: ${getMapTypeLabel(file.type)}</span>
                            <span>Size: ${formatFileSize(file.file_size)}</span>
                        </div>
                    </div>
                `;
            });

            list.innerHTML = html;
        })
        .catch(err => {
            console.error("Lỗi loadSavedMaps:", err);
            list.innerHTML = `<div class="saved-map-empty">Không tải được danh sách file</div>`;
        });
}

async function fetchGeoJSONCached(url, level = "full") {
    if (!url) return null;

    if (!window.mapCache[level]) {
        window.mapCache[level] = {};
    }

    if (window.mapCache[level][url]) {
        return window.mapCache[level][url];
    }

    const res = await fetch(url, {
        credentials: "same-origin",
        headers: {
            "Accept": "application/json",
            "X-Requested-With": "XMLHttpRequest"
        }
    });

    const text = await res.text();

    if (!res.ok) {
        throw new Error(`Không tải được dữ liệu bản đồ (${res.status})`);
    }

    let data = null;
    try {
        data = JSON.parse(text);
    } catch (e) {
        console.error("GeoJSON trả về không phải JSON:", text);
        throw new Error("URL GeoJSON trả HTML thay vì JSON");
    }

    window.mapCache[level][url] = data;
    return data;
}

async function viewSavedMap(id) {
    const loading = document.getElementById("loading");
    if (loading) loading.style.display = "flex";

    try {
        const res = await fetch(`/map-files/${id}/json`, {
            credentials: "same-origin",
            headers: {
                "Accept": "application/json",
                "X-Requested-With": "XMLHttpRequest"
            }
        });

        const text = await res.text();
        console.log("VIEW MAP STATUS:", res.status);
        console.log("VIEW MAP RESPONSE:", text);

        if (!res.ok) {
            throw new Error(`Không đọc được metadata file (${res.status})`);
        }

        let meta = null;
        try {
            meta = JSON.parse(text);
        } catch (e) {
            throw new Error("map-files/{id}/json đang trả HTML, không phải JSON");
        }

        if (!meta.success) {
            throw new Error(meta.message || "Không đọc được file");
        }

        window.currentMapMeta = meta;
        window.fullLoaded = false;
        window.liteLoaded = false;
        window.ultraLoaded = false;

        const type = normalizeMapType(meta.type);

        let firstUrl = meta.full_url;
        let firstLevel = "full";

        if (type === "quy_hoach") {
            firstUrl = meta.ultra_lite_url || meta.lite_url || meta.full_url;
            firstLevel = meta.ultra_lite_url ? "ultra" : (meta.lite_url ? "lite" : "full");
        }

        const firstGeojson = await fetchGeoJSONCached(firstUrl, firstLevel);
        renderMapByType(type, firstGeojson);

        window.fullLoaded = firstLevel === "full";
        window.liteLoaded = firstLevel === "lite" || firstLevel === "full";
        window.ultraLoaded = firstLevel === "ultra" || firstLevel === "lite" || firstLevel === "full";

        syncMapToggles();

        if (typeof closePopup === "function") {
            closePopup();
        }
    } catch (err) {
        console.error("Lỗi viewSavedMap:", err);
        alert(err.message || "Lỗi khi load file từ database");
    } finally {
        if (loading) loading.style.display = "none";
    }
}

async function updateMapResolutionByZoom() {
    if (!window.currentMapMeta) return;

    const type = normalizeMapType(window.currentMapMeta.type);
    const z = map.getZoom();

    try {
        if (type === "quy_hoach") {
            if (z >= 17 && !window.fullLoaded && window.currentMapMeta.full_url) {
                const fullGeojson = await fetchGeoJSONCached(window.currentMapMeta.full_url, "full");
                renderMapByType(type, fullGeojson);
                window.fullLoaded = true;
                window.liteLoaded = true;
                window.ultraLoaded = true;
                syncMapToggles();
                return;
            }

            if (z >= 14 && !window.liteLoaded && window.currentMapMeta.lite_url) {
                const liteGeojson = await fetchGeoJSONCached(window.currentMapMeta.lite_url, "lite");
                renderMapByType(type, liteGeojson);
                window.liteLoaded = true;
                window.ultraLoaded = true;
                syncMapToggles();
                return;
            }
        } else {
            if (!window.fullLoaded && window.currentMapMeta.full_url) {
                const fullGeojson = await fetchGeoJSONCached(window.currentMapMeta.full_url, "full");
                renderMapByType(type, fullGeojson);
                window.fullLoaded = true;
                window.liteLoaded = true;
                window.ultraLoaded = true;
                syncMapToggles();
            }
        }
    } catch (e) {
        console.error("Lỗi updateMapResolutionByZoom:", e);
    }
}

async function ensureFullMapLoaded() {
    if (!window.currentMapMeta || window.fullLoaded || !window.currentMapMeta.full_url) return;

    try {
        const type = normalizeMapType(window.currentMapMeta.type);
        const fullGeojson = await fetchGeoJSONCached(window.currentMapMeta.full_url, "full");
        renderMapByType(type, fullGeojson);
        window.fullLoaded = true;
        window.liteLoaded = true;
        window.ultraLoaded = true;
        syncMapToggles();
    } catch (e) {
        console.error("Lỗi ensureFullMapLoaded:", e);
    }
}

map.on("zoomend", updateMapResolutionByZoom);

function isLayerVisible(layerId) {
    if (!map.getLayer(layerId)) return false;

    const visibility = map.getLayoutProperty(layerId, "visibility");
    return visibility !== "none";
}

function syncMapToggles() {
    const dcMoi = document.getElementById("toggle_dc_moi");
    const dcCu = document.getElementById("toggle_dc_cu");
    const qh = document.getElementById("toggle_qh");
    const canh = document.getElementById("toggle_canh");

    if (dcMoi) dcMoi.checked = map.getLayer("dc_moi_fill") ? isLayerVisible("dc_moi_fill") : false;
    if (dcCu) dcCu.checked = map.getLayer("dc_cu_fill") ? isLayerVisible("dc_cu_fill") : false;
    if (qh) qh.checked = map.getLayer("quyhoach_fill") ? isLayerVisible("quyhoach_fill") : false;
    if (canh) canh.checked = typeof window.canhVisible !== "undefined" ? window.canhVisible : !!map.getLayer("canh_fill");
}

function formatFileSize(bytes) {
    if (!bytes) return "0 B";
    if (bytes < 1024) return bytes + " B";
    if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + " KB";
    return (bytes / (1024 * 1024)).toFixed(1) + " MB";
}

function toggleMapGroup(type) {
    if (type === "dc_moi") {
        setLayerVisibility(["dc_moi_fill", "dc_moi_line"], document.getElementById("toggle_dc_moi")?.checked ?? true);
    }

    if (type === "dc_cu") {
        setLayerVisibility(["dc_cu_fill", "dc_cu_line"], document.getElementById("toggle_dc_cu")?.checked ?? true);
    }

    if (type === "quy_hoach") {
        setLayerVisibility(["quyhoach_fill", "quyhoach_line"], document.getElementById("toggle_qh")?.checked ?? true);
    }

    if (type === "canh") {
        if (typeof toggleCanhVisibility === "function") {
            toggleCanhVisibility(document.getElementById("toggle_canh")?.checked ?? true);
        }
    }

    syncMapToggles();
}

function setLayerVisibility(layerIds, isVisible) {
    layerIds.forEach(function (id) {
        if (map.getLayer(id)) {
            map.setLayoutProperty(id, "visibility", isVisible ? "visible" : "none");
        }
    });
}
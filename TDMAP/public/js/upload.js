/* =========================
UPLOAD FILE + LOAD GEOJSON
========================= */

let geo_dc_cu = null;
let geo_dc_moi = null;
let geo_quy_hoach = null;

/* =========================
TYPE MAPPING
========================= */
function normalizeUploadType(type) {
    const mapType = {
        dc_cu: "dccu",
        dc_moi: "dcmoi",
        quy_hoach: "quyhoach",
        dccu: "dccu",
        dcmoi: "dcmoi",
        quyhoach: "quyhoach"
    };

    return mapType[type] || type;
}

function normalizeFrontendType(type) {
    const mapType = {
        dccu: "dc_cu",
        dcmoi: "dc_moi",
        quyhoach: "quy_hoach",
        dc_cu: "dc_cu",
        dc_moi: "dc_moi",
        quy_hoach: "quy_hoach"
    };

    return mapType[type] || type;
}

function getTypeLabel(type) {
    const labels = {
        dc_cu: "Địa chính cũ",
        dc_moi: "Địa chính mới",
        quy_hoach: "Quy hoạch",
        dccu: "Địa chính cũ",
        dcmoi: "Địa chính mới",
        quyhoach: "Quy hoạch"
    };

    return labels[type] || type;
}

function getInputIdByType(type) {
    const ids = {
        dccu: "dc_cu",
        dcmoi: "dc_moi",
        quyhoach: "quy_hoach",
        dc_cu: "dc_cu",
        dc_moi: "dc_moi",
        quy_hoach: "quy_hoach"
    };

    return ids[type] || type;
}

function getBadgeByLevel(level) {
    switch (Number(level)) {
        case 1: return "VIP 1";
        case 2: return "VIP 2";
        case 3: return "VIP 3";
        case 0: return "FREE";
        default: return "KHÁCH";
    }
}

/* =========================
STATUS BOX
========================= */
function setVipStatusBox(html, type = "normal") {
    const box = document.getElementById("vipUploadStatus");
    if (!box) return;

    box.classList.remove("vip-status-error", "vip-status-warning", "vip-status-success");

    if (type === "error") box.classList.add("vip-status-error");
    if (type === "warning") box.classList.add("vip-status-warning");
    if (type === "success") box.classList.add("vip-status-success");

    box.innerHTML = html;
}

function showUploadMessage(message, type = "normal") {
    setVipStatusBox(message, type);
}

/* =========================
LOCK / UNLOCK INPUT
========================= */
function lockUploadInput(type, message = "") {
    const inputId = getInputIdByType(type);
    const input = document.getElementById(inputId);
    if (!input) return;

    input.disabled = true;
    input.dataset.locked = "1";
    input.title = message || "Bạn đã hết lượt tải cho mục này";
    input.style.opacity = "0.7";
    input.style.cursor = "not-allowed";
}

function unlockUploadInput(type) {
    const inputId = getInputIdByType(type);
    const input = document.getElementById(inputId);
    if (!input) return;

    input.disabled = false;
    input.dataset.locked = "0";
    input.title = "";
    input.style.opacity = "";
    input.style.cursor = "";
}

function applyUploadLocks(data) {
    if (!data || !data.remaining) return;

    const remainDcmoi = data.remaining.dcmoi;
    const remainDccu = data.remaining.dccu;
    const remainQh = data.remaining.quyhoach;

    if (remainDcmoi === 0) lockUploadInput("dcmoi", "Đã hết lượt tải Địa chính mới");
    else unlockUploadInput("dcmoi");

    if (remainDccu === 0) lockUploadInput("dccu", "Đã hết lượt tải Địa chính cũ");
    else unlockUploadInput("dccu");

    if (remainQh === 0) lockUploadInput("quyhoach", "Đã hết lượt tải Quy hoạch");
    else unlockUploadInput("quyhoach");
}

/* =========================
RENDER VIP STATUS
========================= */
function renderVipUploadStatus(data) {
    const vipName = data.vip_name || getBadgeByLevel(data.vip_level);
    const limits = data.limits || {};
    const used = data.used || {};
    const remaining = data.remaining || {};

    applyUploadLocks(data);

    let tips = [];

    if ((remaining.dccu ?? 0) <= 0) {
        tips.push(`Địa chính cũ: ${used.dccu ?? 0}/${limits.dccu} file (còn 0)`);
    }

    if ((remaining.dcmoi ?? 0) <= 0) {
        tips.push(`Địa chính mới: ${used.dcmoi ?? 0}/${limits.dcmoi} file (còn 0)`);
    }

    if ((remaining.quyhoach ?? 0) <= 0) {
        tips.push(`Quy hoạch: ${used.quyhoach ?? 0}/${limits.quyhoach} file (còn 0)`);
    }

    let html = `
        <strong>Gói hiện tại: ${vipName}</strong><br><br>
        Địa chính mới: ${used.dcmoi ?? 0}/${limits.dcmoi === -1 ? "∞" : limits.dcmoi} file ${remaining.dcmoi === null ? "(Không giới hạn)" : `(còn ${remaining.dcmoi})`}<br>
        Địa chính cũ: ${used.dccu ?? 0}/${limits.dccu === -1 ? "∞" : limits.dccu} file ${remaining.dccu === null ? "(Không giới hạn)" : `(còn ${remaining.dccu})`}<br>
        Quy hoạch: ${used.quyhoach ?? 0}/${limits.quyhoach === -1 ? "∞" : limits.quyhoach} file ${remaining.quyhoach === null ? "(Không giới hạn)" : `(còn ${remaining.quyhoach})`}
    `;

    if (tips.length > 0 && Number(data.vip_level) < 3) {
        html += `
            <div class="vip-upgrade-tip">
                <div class="vip-upgrade-title">Bạn đã hết lượt tải ở một số mục:</div>
                <ul>
                    ${tips.map(t => `<li>${t}</li>`).join("")}
                </ul>
                <a href="/vip/payment" class="vip-upgrade-btn">💰 Nâng cấp VIP</a>
            </div>
        `;
    }

    setVipStatusBox(html);
}

function loadVipUploadStatus() {
    if (!window.isLogin) {
        setVipStatusBox(`
            <strong>Chưa đăng nhập</strong><br>
            Bạn cần đăng nhập để tải dữ liệu bản đồ.
        `, "warning");
        return;
    }

    fetch("/my-files-json", {
        method: "GET",
        headers: {
            "Accept": "application/json"
        }
    })
        .then(async res => {
            const data = await res.json();

            if (!res.ok || !data.success) {
                throw data;
            }

            return data;
        })
        .then(data => {
            renderVipUploadStatus(data);
        })
        .catch(err => {
            console.error("Không lấy được thông tin VIP:", err);

            setVipStatusBox(`
                <strong>Không tải được thông tin VIP</strong><br>
                Vui lòng thử lại sau.
            `, "error");
        });
}

/* =========================
UI LOADING
========================= */
function showLoading() {
    const loading = document.getElementById("loading");
    if (loading) loading.style.display = "flex";
}

function hideLoading() {
    const loading = document.getElementById("loading");
    if (loading) loading.style.display = "none";
}

/* =========================
GEOJSON NORMALIZE
========================= */
function isValidCoord(coord) {
    return (
        Array.isArray(coord) &&
        coord.length >= 2 &&
        Number.isFinite(Number(coord[0])) &&
        Number.isFinite(Number(coord[1]))
    );
}

function sameCoord(a, b) {
    if (!isValidCoord(a) || !isValidCoord(b)) return false;
    return Number(a[0]) === Number(b[0]) && Number(a[1]) === Number(b[1]);
}

function closeRing(ring) {
    if (!Array.isArray(ring) || ring.length < 3) return null;

    const cleanRing = ring
        .filter(isValidCoord)
        .map(coord => [Number(coord[0]), Number(coord[1])]);

    if (cleanRing.length < 3) return null;

    if (!sameCoord(cleanRing[0], cleanRing[cleanRing.length - 1])) {
        cleanRing.push([...cleanRing[0]]);
    }

    if (cleanRing.length < 4) return null;

    return cleanRing;
}

function normalizePolygonCoords(coords) {
    if (!Array.isArray(coords) || !coords.length) return null;

    const rings = coords
        .map(closeRing)
        .filter(Boolean);

    return rings.length ? rings : null;
}

function normalizeMultiPolygonCoords(coords) {
    if (!Array.isArray(coords) || !coords.length) return null;

    const polygons = coords
        .map(normalizePolygonCoords)
        .filter(Boolean);

    return polygons.length ? polygons : null;
}

function normalizeFeature(feature) {
    if (!feature || feature.type !== "Feature" || !feature.geometry) return null;

    const geometry = feature.geometry;
    const type = geometry.type;

    if (type === "Polygon") {
        const polygon = normalizePolygonCoords(geometry.coordinates);
        if (!polygon) return null;

        return {
            ...feature,
            properties: feature.properties || {},
            geometry: {
                type: "Polygon",
                coordinates: polygon
            }
        };
    }

    if (type === "MultiPolygon") {
        const multiPolygon = normalizeMultiPolygonCoords(geometry.coordinates);
        if (!multiPolygon) return null;

        return {
            ...feature,
            properties: feature.properties || {},
            geometry: {
                type: "MultiPolygon",
                coordinates: multiPolygon
            }
        };
    }

    return null;
}

function collectCoordsFromGeometry(geometry, out = []) {
    if (!geometry || !geometry.coordinates) return out;

    const walk = (value) => {
        if (!Array.isArray(value)) return;

        if (value.length >= 2 && Number.isFinite(Number(value[0])) && Number.isFinite(Number(value[1]))) {
            out.push([Number(value[0]), Number(value[1])]);
            return;
        }

        value.forEach(walk);
    };

    walk(geometry.coordinates);
    return out;
}

function computeBBoxFromFeatures(features) {
    let minX = Infinity;
    let minY = Infinity;
    let maxX = -Infinity;
    let maxY = -Infinity;

    features.forEach(feature => {
        const coords = collectCoordsFromGeometry(feature.geometry, []);
        coords.forEach(([x, y]) => {
            if (x < minX) minX = x;
            if (y < minY) minY = y;
            if (x > maxX) maxX = x;
            if (y > maxY) maxY = y;
        });
    });

    if (![minX, minY, maxX, maxY].every(Number.isFinite)) {
        return null;
    }

    return [minX, minY, maxX, maxY];
}

function normalizeGeoJSON(geoData) {
    if (!geoData || typeof geoData !== "object") {
        throw new Error("GeoJSON rỗng hoặc không hợp lệ");
    }

    const rawFeatures = Array.isArray(geoData.features) ? geoData.features : [];
    const features = rawFeatures
        .map(normalizeFeature)
        .filter(Boolean);

    if (!features.length) {
        throw new Error("Không có polygon hợp lệ để hiển thị");
    }

    const bbox = computeBBoxFromFeatures(features);

    return {
        type: "FeatureCollection",
        features,
        bbox: bbox || geoData.bbox || null
    };
}

/* =========================
GLOBAL GEO STORAGE
========================= */
function assignGeoToGlobal(type, geojson) {
    const normalizedType = normalizeFrontendType(type);

    if (normalizedType === "dc_cu") {
        geo_dc_cu = geojson;
        return;
    }

    if (normalizedType === "dc_moi") {
        geo_dc_moi = geojson;
        return;
    }

    if (normalizedType === "quy_hoach") {
        geo_quy_hoach = geojson;
    }
}

/* =========================
CALL MAP LOADER
========================= */
function dispatchLoadGeoJSON(type, geojson) {
    const normalizedType = normalizeFrontendType(type);

    if (normalizedType === "dc_cu") {
        if (typeof loadDcCu !== "function") {
            throw new Error("Thiếu hàm loadDcCu()");
        }
        loadDcCu(geojson);
        return;
    }

    if (normalizedType === "dc_moi") {
        if (typeof loadDcMoi !== "function") {
            throw new Error("Thiếu hàm loadDcMoi()");
        }
        loadDcMoi(geojson);
        return;
    }

    if (normalizedType === "quy_hoach") {
        if (typeof loadQuyHoach !== "function") {
            throw new Error("Thiếu hàm loadQuyHoach()");
        }
        loadQuyHoach(geojson);
        return;
    }

    throw new Error("Không xác định được loại bản đồ");
}

/* =========================
UPLOAD + LOAD TỪ SERVER
========================= */
function uploadAndLoad(file, type) {
    showLoading();

    const backendType = normalizeUploadType(type);

    const formData = new FormData();
    formData.append("file", file);
    formData.append("type", backendType);

    fetch("/upload-map", {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
            "Accept": "application/json",
            "X-Requested-With": "XMLHttpRequest"
        },
        credentials: "same-origin",
        body: formData
    })
        .then(async res => {
            const text = await res.text();

            console.log("UPLOAD STATUS:", res.status);
            console.log("UPLOAD HEADERS:", [...res.headers.entries()]);
            console.log("UPLOAD RESPONSE:", text);

            let data = {};
            try {
                data = JSON.parse(text);
            } catch (e) {
                throw new Error("Server không trả JSON. Có thể bị chặn bởi hosting, SSL, CSRF hoặc lỗi 500.");
            }

            if (!res.ok || !data.success) {
                throw {
                    status: res.status,
                    data: data
                };
            }

            return data;
        })
        .then(async data => {
            let msg = `<strong>Tải lên thành công</strong><br>${data.name || file.name}`;

            if (data.remaining === null) {
                msg += `<br>${data.vip_name || "VIP 3"}: không giới hạn`;
            } else if (typeof data.remaining !== "undefined") {
                msg += `<br>Còn lại ${data.remaining} file cho mục ${data.type_label || getTypeLabel(type)}`;
            }

            showUploadMessage(msg, "success");

            if (typeof viewSavedMap === "function") {
                await viewSavedMap(data.id);
            }

            loadVipUploadStatus();
        })
        .catch(err => {
            console.error("Upload lỗi:", err);
            showUploadMessage(`
                <strong>Upload thất bại</strong><br>
                ${err.message || err?.data?.message || "Failed to fetch"}
            `, "error");
        })
        .finally(() => {
            hideLoading();
        });
}

/* =========================
LOAD GEOJSON TỪ URL SERVER
========================= */
async function loadGeoJSONFromUrl(url, type) {
    if (!url) {
        throw new Error("Không có URL GeoJSON");
    }

    const res = await fetch(url, {
        headers: {
            "Accept": "application/json"
        }
    });

    if (!res.ok) {
        throw new Error("Không tải được dữ liệu GeoJSON từ server");
    }

    const geoData = await res.json();
    const normalizedGeoData = normalizeGeoJSON(geoData);

    assignGeoToGlobal(type, normalizedGeoData);
    dispatchLoadGeoJSON(type, normalizedGeoData);
}

/* =========================
CLEAR MAP
========================= */
function removeLayerSafe(layerId) {
    try {
        if (map.getLayer(layerId)) {
            map.removeLayer(layerId);
        }
    } catch (e) {
        console.warn("Không remove được layer:", layerId, e);
    }
}

function removeSourceSafe(sourceId) {
    try {
        if (map.getSource(sourceId)) {
            map.removeSource(sourceId);
        }
    } catch (e) {
        console.warn("Không remove được source:", sourceId, e);
    }
}

function clearHighlightSafe() {
    removeLayerSafe("parcelHighlightFill");
    removeLayerSafe("parcelHighlightLine");
    removeSourceSafe("parcelHighlight");
}

function clearMeasureSafe() {
    if (typeof clearMeasure === "function") {
        clearMeasure();
    }
}

function clearMap() {
    clearMeasureSafe();
    clearHighlightSafe();

    removeLayerSafe("dc_cu_fill");
    removeLayerSafe("dc_cu_line");
    removeSourceSafe("dc_cu");

    removeLayerSafe("dc_moi_fill");
    removeLayerSafe("dc_moi_line");
    removeSourceSafe("dc_moi");

    removeLayerSafe("quyhoach_fill");
    removeLayerSafe("quyhoach_line");
    removeSourceSafe("quy_hoach");
}

/* =========================
CLEAR ALL
========================= */
function clearAll() {
    clearMap();

    geo_dc_cu = null;
    geo_dc_moi = null;
    geo_quy_hoach = null;

    if (window.currentMapMeta) window.currentMapMeta = null;
    if (typeof window.fullLoaded !== "undefined") window.fullLoaded = false;
    if (typeof window.liteLoaded !== "undefined") window.liteLoaded = false;
    if (typeof window.ultraLoaded !== "undefined") window.ultraLoaded = false;

    const dcCuInput = document.getElementById("dc_cu");
    const dcMoiInput = document.getElementById("dc_moi");
    const quyHoachInput = document.getElementById("quy_hoach");

    if (dcCuInput) dcCuInput.value = "";
    if (dcMoiInput) dcMoiInput.value = "";
    if (quyHoachInput) quyHoachInput.value = "";

    if (typeof removeMarker === "function") {
        removeMarker();
    }

    window.currentFeature = null;
}
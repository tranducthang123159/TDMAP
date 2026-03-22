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
    preserveDrawingBuffer: true
});

/* =========================
MAP MODE
========================= */

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
    let dc_cu = document.getElementById("dc_cu")?.files?.[0];
    let dc_moi = document.getElementById("dc_moi")?.files?.[0];
    let quy_hoach = document.getElementById("quy_hoach")?.files?.[0];

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

let marker = null;
let markerPopup = null;

map.on("click", function (e) {
    let features = [];

    if (map.getLayer("dc_moi_fill")) {
        features = map.queryRenderedFeatures(e.point, {
            layers: ["dc_moi_fill"]
        });
    }

    if (features.length > 0) {
        return;
    }

    let lng = e.lngLat.lng;
    let lat = e.lngLat.lat;

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
    if (marker) {
        marker.remove();
        marker = null;
    }

    if (markerPopup) {
        markerPopup.remove();
        markerPopup = null;
    }

    let el = document.createElement("div");
    el.className = "marker";

    marker = new maplibregl.Marker(el)
        .setLngLat([lng, lat])
        .addTo(map);

    markerPopup = new maplibregl.Popup({
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
    fetch("https://nominatim.openstreetmap.org/reverse?format=json&lat=" + lat + "&lon=" + lng)
        .then(res => res.json())
        .then(data => {
            let address = data.display_name || "Không rõ địa chỉ";

            if (typeof updateVN2000 === "function") {
                updateVN2000(108.5);
            }

            let vnX = "";
            let vnY = "";

            if (typeof proj4 !== "undefined" && typeof currentKTT !== "undefined") {
                let result = proj4(
                    proj4.defs("EPSG:4326"),
                    proj4.defs("VN2000_Current"),
                    [lng, lat]
                );

                vnX = result[1].toFixed(3);
                vnY = result[0].toFixed(3);
            }

            let html = `
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
                            <span class="gm-popup-coord">X: ${vnX}</span><br>
                            <span class="gm-popup-coord" style="margin-top:6px;">Y: ${vnY}</span>
                        </div>

                        <div class="gm-popup-actions">
                            <button class="gm-popup-btn primary" onclick="openGoogleDirections(${lat}, ${lng})">
                                🧭 Chỉ đường
                            </button>
                            <button class="gm-popup-btn gray" onclick="copyCoordinates(${lat}, ${lng})">
                                📋 Copy tọa độ
                            </button>
                        </div>
                    </div>
                </div>
            `;

            if (markerPopup) {
                markerPopup.setHTML(html);
            }

            const pin = document.getElementById("pinContent");
            if (pin) {
                pin.innerHTML = `
                    <div>🏠 ${address}</div>
                    <div>🌍 WGS84: ${lat.toFixed(6)}, ${lng.toFixed(6)}</div>
                    <div>📐 VN2000</div>
                    <div>X: ${vnX}</div>
                    <div>Y: ${vnY}</div>
                `;
            }
        })
        .catch(err => {
            console.error("Lỗi lấy địa chỉ:", err);

            if (markerPopup) {
                markerPopup.setHTML(`
                    <div class="gm-popup">
                        <div class="gm-popup-head">📍 Vị trí đã ghim</div>
                        <div class="gm-popup-body">
                            <div class="gm-popup-row">
                                <span class="gm-popup-label">WGS84:</span><br>
                                <span class="gm-popup-coord">${lat.toFixed(6)}, ${lng.toFixed(6)}</span>
                            </div>

                            <div class="gm-popup-actions">
                                <button class="gm-popup-btn primary" onclick="openGoogleDirections(${lat}, ${lng})">
                                    🧭 Chỉ đường
                                </button>
                                <button class="gm-popup-btn gray" onclick="copyCoordinates(${lat}, ${lng})">
                                    📋 Copy tọa độ
                                </button>
                            </div>

                            <div class="gm-popup-row" style="margin-top:10px;">
                                Không lấy được địa chỉ.
                            </div>
                        </div>
                    </div>
                `);
            }
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
        let lat = pos.coords.latitude;
        let lng = pos.coords.longitude;

        map.flyTo({
            center: [lng, lat],
            zoom: 18
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
    if (marker) {
        marker.remove();
        marker = null;
    }

    if (markerPopup) {
        markerPopup.remove();
        markerPopup = null;
    }

    const pin = document.getElementById("pinContent");
    if (pin) pin.innerHTML = "";
}

/* =========================
POPUP LAYER
========================= */

function togglePopup() {
    let popup = document.getElementById("mapLayerPopup");
    if (!popup) return;

    popup.style.display = popup.style.display === "block" ? "none" : "block";
}

function closePopup() {
    let popup = document.getElementById("mapLayerPopup");
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

function loadSavedMaps() {
    const list = document.getElementById("savedMapList");
    if (!list) return;

    list.innerHTML = `<div class="saved-map-empty">Đang tải dữ liệu...</div>`;

    fetch("/my-map-files/json")
        .then(res => res.json())
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
                            <span>Loại: ${file.type}</span>
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

function viewSavedMap(id) {
    const loading = document.getElementById("loading");
    if (loading) loading.style.display = "flex";

    fetch(`/map-file/${id}/geojson`)
        .then(res => res.json())
        .then(data => {
            console.log("DATA FILE DB:", data);

            if (!data.success) {
                alert(data.message || "Không đọc được file");
                return;
            }

            const geojson = data.geojson;
            const type = data.type;

            if (type === "dc_cu" && typeof loadDcCu === "function") {
                loadDcCu(geojson);
            } else if (type === "dc_moi" && typeof loadDcMoi === "function") {
                loadDcMoi(geojson);
            } else if (type === "quy_hoach" && typeof loadQuyHoach === "function") {
                loadQuyHoach(geojson);
            } else {
                console.error("Không tìm thấy hàm load cho type:", type);
                alert("Không tìm thấy hàm xử lý loại bản đồ: " + type);
                return;
            }

            syncMapToggles();
        })
        .catch(err => {
            console.error("Lỗi viewSavedMap:", err);
            alert("Lỗi khi load file từ database");
        })
        .finally(() => {
            if (loading) loading.style.display = "none";
        });
}

function formatFileSize(bytes) {
    if (!bytes) return "0 B";
    if (bytes < 1024) return bytes + " B";
    if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + " KB";
    return (bytes / (1024 * 1024)).toFixed(1) + " MB";
}

/* =========================
TOGGLE HIỆN / ẨN
========================= */

function toggleMapGroup(type) {
    if (type === "dc_moi") {
        setLayerVisibility(
            ["dc_moi_fill", "dc_moi_line"],
            document.getElementById("toggle_dc_moi")?.checked ?? true
        );
    }

    if (type === "dc_cu") {
        setLayerVisibility(
            ["dc_cu_fill", "dc_cu_line"],
            document.getElementById("toggle_dc_cu")?.checked ?? true
        );
    }

    if (type === "quy_hoach") {
        setLayerVisibility(
            ["quyhoach_fill", "quyhoach_line"],
            document.getElementById("toggle_qh")?.checked ?? true
        );
    }

    if (type === "canh") {
        if (typeof toggleCanhVisibility === "function") {
            toggleCanhVisibility(
                document.getElementById("toggle_canh")?.checked ?? true
            );
        }
    }
}

function setLayerVisibility(layerIds, isVisible) {
    layerIds.forEach(function(id) {
        if (map.getLayer(id)) {
            map.setLayoutProperty(id, "visibility", isVisible ? "visible" : "none");
        }
    });
}

function syncMapToggles() {
    const dcMoi = document.getElementById("toggle_dc_moi");
    const dcCu = document.getElementById("toggle_dc_cu");
    const qh = document.getElementById("toggle_qh");
    const canh = document.getElementById("toggle_canh");

    if (dcMoi) dcMoi.checked = true;
    if (dcCu) dcCu.checked = true;
    if (qh) qh.checked = true;

    if (canh) {
        canh.checked = typeof window.canhVisible !== "undefined" ? window.canhVisible : true;
    }
}
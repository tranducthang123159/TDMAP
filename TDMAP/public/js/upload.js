/* =========================
UPLOAD FILE + LOAD GEOJSON
========================= */

/* =========================
STORE GEOJSON
========================= */
let geo_dc_cu = null;
let geo_dc_moi = null;
let geo_quy_hoach = null;

/* =========================
UPLOAD + LƯU DB
========================= */
function uploadAndLoad(file, type) {
    const loading = document.getElementById("loading");
    if (loading) loading.style.display = "flex";

    let formData = new FormData();
    formData.append("file", file);
    formData.append("type", type);

    fetch("/upload-map", {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (!data.success) {
            throw new Error(data.message || "Upload thất bại");
        }

        console.log("Upload thành công:", type);
        readGeoJSON(file, type);
    })
    .catch(err => {
        console.error("Upload lỗi:", err);

        if (loading) loading.style.display = "none";
        alert("Upload thất bại");
    });
}

/* =========================
READ GEOJSON BẰNG WORKER
========================= */
function readGeoJSON(file, type) {
    const loading = document.getElementById("loading");
    const worker = new Worker("/js/geoWorker.js");

    worker.postMessage(file);

    worker.onmessage = function (e) {
        let geoData = e.data;

        if (!geoData) {
            if (loading) loading.style.display = "none";
            alert("Không đọc được dữ liệu GeoJSON");
            worker.terminate();
            return;
        }

        try {
            if (type === "dc_cu") {
                geo_dc_cu = geoData;
                loadDcCu(geoData);
            }

            if (type === "dc_moi") {
                geo_dc_moi = geoData;
                loadDcMoi(geoData);
            }

            if (type === "quy_hoach") {
                geo_quy_hoach = geoData;

                // Nếu file quy hoạch là VN2000 thì convert trước khi load
                // geo_quy_hoach = convertGeoJSON_VN2000_to_WGS84(geoData);

                loadQuyHoach(geo_quy_hoach);
            }
        } catch (error) {
            console.error("Lỗi khi load layer:", error);
            alert("Có lỗi khi hiển thị bản đồ");
        }

        if (loading) loading.style.display = "none";
        worker.terminate();
    };

    worker.onerror = function (err) {
        console.error("Worker lỗi:", err);
        if (loading) loading.style.display = "none";
        alert("Không thể đọc file GeoJSON");
        worker.terminate();
    };
}

/* =========================
CLEAR MAP
========================= */
function clearMap() {
    /* địa chính cũ */
    if (map.getLayer("dc_cu_fill")) map.removeLayer("dc_cu_fill");
    if (map.getLayer("dc_cu_line")) map.removeLayer("dc_cu_line");
    if (map.getSource("dc_cu")) map.removeSource("dc_cu");

    /* địa chính mới */
    if (map.getLayer("dc_moi_fill")) map.removeLayer("dc_moi_fill");
    if (map.getLayer("dc_moi_line")) map.removeLayer("dc_moi_line");
    if (map.getSource("dc_moi")) map.removeSource("dc_moi");

    /* quy hoạch */
    if (map.getLayer("quyhoach_fill")) {
        map.off("click", "quyhoach_fill");
        map.removeLayer("quyhoach_fill");
    }

    if (map.getLayer("quyhoach_line")) {
        map.removeLayer("quyhoach_line");
    }

    if (map.getSource("quy_hoach")) {
        map.removeSource("quy_hoach");
    }
}

/* =========================
CLEAR ALL
========================= */
function clearAll() {
    clearMap();

    geo_dc_cu = null;
    geo_dc_moi = null;
    geo_quy_hoach = null;

    let dcCuInput = document.getElementById("dc_cu");
    let dcMoiInput = document.getElementById("dc_moi");
    let quyHoachInput = document.getElementById("quy_hoach");

    if (dcCuInput) dcCuInput.value = "";
    if (dcMoiInput) dcMoiInput.value = "";
    if (quyHoachInput) quyHoachInput.value = "";

    if (typeof clearMeasure === "function") {
        clearMeasure();
    }

    if (typeof removeMarker === "function") {
        removeMarker();
    }

    if (window.currentFeature) {
        window.currentFeature = null;
    }
}
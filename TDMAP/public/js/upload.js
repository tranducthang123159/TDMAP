/* =========================
UPLOAD FILE + LOAD GEOJSON
========================= */

let geo_dc_cu = null;
let geo_dc_moi = null;
let geo_quy_hoach = null;

/* =========================
MAP TYPE FRONTEND -> BACKEND
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

    if (remainDcmoi === 0) {
        lockUploadInput("dcmoi", "Đã hết lượt tải Địa chính mới");
    } else {
        unlockUploadInput("dcmoi");
    }

    if (remainDccu === 0) {
        lockUploadInput("dccu", "Đã hết lượt tải Địa chính cũ");
    } else {
        unlockUploadInput("dccu");
    }

    if (remainQh === 0) {
        lockUploadInput("quyhoach", "Đã hết lượt tải Quy hoạch");
    } else {
        unlockUploadInput("quyhoach");
    }
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
/* =========================
LOAD VIP STATUS
========================= */
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
SHOW MESSAGE IN BOX
========================= */
function showUploadMessage(message, type = "normal") {
    setVipStatusBox(message, type);
}

/* =========================
UPLOAD + LƯU DB
========================= */
function uploadAndLoad(file, type) {
    const loading = document.getElementById("loading");
    if (loading) loading.style.display = "flex";

    const backendType = normalizeUploadType(type);

    let formData = new FormData();
    formData.append("file", file);
    formData.append("type", backendType);

    fetch("/upload-map", {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
            "Accept": "application/json"
        },
        body: formData
    })
    .then(async res => {
        let data = {};

        try {
            data = await res.json();
        } catch (e) {
            throw new Error("Server trả về dữ liệu không hợp lệ");
        }

        if (!res.ok || !data.success) {
            throw {
                status: res.status,
                data: data
            };
        }

        return data;
    })
    .then(data => {
        let msg = `<strong>Tải lên thành công</strong><br>${data.name || file.name}`;

        if (data.remaining === null) {
            msg += `<br>${data.vip_name || "VIP 3"}: không giới hạn`;
        } else if (typeof data.remaining !== "undefined") {
            msg += `<br>Còn lại ${data.remaining} file cho mục ${data.type_label || getTypeLabel(type)}`;
        }

        showUploadMessage(msg, "success");

        readGeoJSON(file, type);
        loadVipUploadStatus();
    })
    .catch(err => {
        console.error("Upload lỗi:", err);

        if (loading) loading.style.display = "none";

        if (err.status === 401 && err.data) {
            showUploadMessage(`
                <strong>Chưa đăng nhập</strong><br>
                ${err.data.message || "Bạn cần đăng nhập để tải file"}
            `, "warning");
            return;
        }

        if (err.status === 403 && err.data) {
            const data = err.data;

            let message = `<strong>Đã hết lượt tải file</strong><br>`;
            message += `${data.message || "Bạn đã đạt giới hạn upload của gói hiện tại."}`;

            if (typeof data.limit !== "undefined" && typeof data.used !== "undefined") {
                message += `<br>Đã dùng: ${data.used}/${data.limit} file cho mục ${data.type_label || getTypeLabel(type)}.`;
            }

           message += `<br><a href="/vip/payment" style="display:inline-block;margin-top:8px;padding:8px 12px;background:#f59e0b;color:#fff;border-radius:10px;text-decoration:none;font-weight:700;">💰 Nâng cấp VIP</a>`;

            showUploadMessage(message, "warning");

            if (data.type) {
                lockUploadInput(data.type, "Đã hết lượt tải");
            }

            loadVipUploadStatus();
            return;
        }

        if (err.status === 422 && err.data) {
            if (err.data.errors) {
                const firstKey = Object.keys(err.data.errors)[0];
                const firstError = err.data.errors[firstKey]?.[0];

                showUploadMessage(`
                    <strong>Dữ liệu không hợp lệ</strong><br>
                    ${firstError || "File tải lên không hợp lệ"}
                `, "error");
                return;
            }

            showUploadMessage(`
                <strong>Dữ liệu không hợp lệ</strong><br>
                ${err.data.message || "Upload không hợp lệ"}
            `, "error");
            return;
        }

        if (err.data && err.data.message) {
            showUploadMessage(`
                <strong>Upload thất bại</strong><br>
                ${err.data.message}
            `, "error");
            return;
        }

        showUploadMessage(`
            <strong>Upload thất bại</strong><br>
            Vui lòng thử lại sau.
        `, "error");
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
            showUploadMessage(`
                <strong>Không đọc được dữ liệu GeoJSON</strong><br>
                Vui lòng kiểm tra lại file.
            `, "error");
            worker.terminate();
            return;
        }

        try {
            if (type === "dc_cu" || type === "dccu") {
                geo_dc_cu = geoData;
                loadDcCu(geoData);
            }

            if (type === "dc_moi" || type === "dcmoi") {
                geo_dc_moi = geoData;
                loadDcMoi(geoData);
            }

            if (type === "quy_hoach" || type === "quyhoach") {
                geo_quy_hoach = geoData;
                loadQuyHoach(geo_quy_hoach);
            }
        } catch (error) {
            console.error("Lỗi khi load layer:", type, error);

            let errorText = "Vui lòng thử lại.";

            if (error?.message) {
                errorText = error.message;
            } else if (typeof error === "string") {
                errorText = error;
            } else {
                try {
                    errorText = JSON.stringify(error);
                } catch (e) {}
            }

            showUploadMessage(`
                <strong>Có lỗi khi hiển thị bản đồ ${getTypeLabel(type)}</strong><br>
                ${errorText}
            `, "error");
        }

        if (loading) loading.style.display = "none";
        worker.terminate();
    };

    worker.onerror = function (err) {
        console.error("Worker lỗi:", err);
        if (loading) loading.style.display = "none";
        showUploadMessage(`
            <strong>Không thể đọc file GeoJSON</strong><br>
            Vui lòng thử lại.
        `, "error");
        worker.terminate();
    };
}

/* =========================
CLEAR MAP
========================= */
function clearMap() {
    if (map.getLayer("dc_cu_fill")) map.removeLayer("dc_cu_fill");
    if (map.getLayer("dc_cu_line")) map.removeLayer("dc_cu_line");
    if (map.getSource("dc_cu")) map.removeSource("dc_cu");

    if (map.getLayer("dc_moi_fill")) map.removeLayer("dc_moi_fill");
    if (map.getLayer("dc_moi_line")) map.removeLayer("dc_moi_line");
    if (map.getSource("dc_moi")) map.removeSource("dc_moi");

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
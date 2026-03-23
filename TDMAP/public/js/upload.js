/* =========================
UPLOAD FILE + LOAD GEOJSON
========================= */

window.geo_dc_cu = window.geo_dc_cu || null;
window.geo_dc_moi = window.geo_dc_moi || null;
window.geo_quy_hoach = window.geo_quy_hoach || null;

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

function showLoading() {
    const loading = document.getElementById("loading");
    if (loading) loading.style.display = "flex";
}

function hideLoading() {
    const loading = document.getElementById("loading");
    if (loading) loading.style.display = "none";
}

function normalizeGeoJSON(geoData) {
    return geoData;
}

function uploadAndLoad(file, type) {
    showLoading();

    const backendType = normalizeUploadType(type);
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || "";

    const formData = new FormData();
    formData.append("file", file);
    formData.append("type", backendType);

    fetch("/upload-map", {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": csrf,
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

            let data = null;

            try {
                data = JSON.parse(text);
            } catch (e) {
                throw new Error("upload-map đang trả HTML hoặc text, không phải JSON");
            }

            if (!res.ok || !data.success) {
                throw new Error(data.message || `Upload lỗi HTTP ${res.status}`);
            }

            return data;
        })
        .then(async data => {
            let msg = `<strong>Tải lên thành công</strong><br>${data.name || file.name}`;

            if (typeof data.remaining !== "undefined") {
                msg += `<br>${data.message || ""}`;
            }

            showUploadMessage(msg, "success");

            if (typeof viewSavedMap === "function" && data.id) {
                await viewSavedMap(data.id);
            }
        })
        .catch(err => {
            console.error("Upload lỗi:", err);
            showUploadMessage(`
                <strong>Upload thất bại</strong><br>
                ${err.message || "Failed to fetch"}
            `, "error");
        })
        .finally(() => {
            hideLoading();
        });
}
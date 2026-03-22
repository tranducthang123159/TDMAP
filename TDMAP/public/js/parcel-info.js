window.currentFeature = null;

function showParcelInfo(feature) {
    let p = feature?.properties ? feature.properties : feature;

    if (!p) {
        console.error("Feature lỗi:", feature);
        return;
    }

    window.currentFeature = feature;

    let html = `
    <div class="parcel-bar-head" onclick="toggleParcelBar()">
        <div class="parcel-bar-title">Thông tin thửa đất</div>
        <div class="parcel-bar-arrow" id="parcelBarArrow">▼</div>
    </div>

    <div class="parcel-bar-body" id="parcelBarBody">
        <div class="bar-content">
            <div class="bar-item">
                <span>Tờ</span>
                <b>${p.SHBANDO ?? ""}</b>
            </div>

            <div class="bar-item">
                <span>Thửa</span>
                <b>${p.SHTHUA ?? ""}</b>
            </div>

            <div class="bar-item">
                <span>Tờ cũ</span>
                <b>${p.SOTOCU ?? ""}</b>
            </div>

            <div class="bar-item">
                <span>Diện tích</span>
                <b>${Number(p.DIENTICH || 0).toFixed(2)} m²</b>
            </div>

            <div class="bar-item">
                <span>Loại đất</span>
                <b>${p.KHLOAIDAT ?? ""}</b>
            </div>

            <div class="bar-item owner">
                <span>Chủ</span>
                <b>${p.TENCHU ?? ""}</b>
            </div>

            <div class="bar-actions">
                <button class="btn green" onclick="openParcelGoogleMaps()">🚗 Chỉ đường</button>
                <button class="btn purple" onclick="exportCoordinates()">📍 Tọa độ</button>
                <button class="btn indigo" onclick="exportParcelPDF()">📄 PDF</button>

                <button class="btn yellow" id="btnSplitStart" onclick="toggleSplitParcel()">✏️ Bật tách thửa</button>
                <button class="btn" id="btnSplitFinish" style="background:#2563eb;color:#fff" onclick="finishSplitParcel()">✅ Cắt</button>
                <button class="btn" id="btnSplitUndo" style="background:#f59e0b;color:#fff" onclick="undoSplitPoint()">↩ Hoàn tác</button>
                <button class="btn" id="btnSplitExit" style="background:#6b7280;color:#fff" onclick="resetSplitParcel()">✖ Thoát</button>
            </div>
        </div>
    </div>
    `;

    const bar = document.getElementById("parcelBar");
    if (bar) {
        bar.innerHTML = html;
        bar.classList.add("active");
        bar.classList.remove("collapsed");
    }
}

function toggleParcelBar() {
    const bar = document.getElementById("parcelBar");
    const arrow = document.getElementById("parcelBarArrow");

    if (!bar) return;

    bar.classList.toggle("collapsed");

    if (arrow) {
        arrow.innerHTML = bar.classList.contains("collapsed") ? "▲" : "▼";
    }
}

function hideParcelBar() {
    const bar = document.getElementById("parcelBar");
    if (bar) {
        bar.classList.remove("active");
        bar.classList.remove("collapsed");
    }
}
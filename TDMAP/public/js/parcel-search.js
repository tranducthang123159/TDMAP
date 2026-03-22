/* =========================
PARCEL SEARCH SYSTEM - OPTIMIZED
========================= */

let parcelList = [];
let filteredParcelList = [];
let parcelEventsInitialized = false;

const PARCEL_RENDER_LIMIT = 100;

/* =========================
UTILS
========================= */

function safeLower(value) {
    return (value ?? "").toString().toLowerCase().trim();
}

function debounce(fn, delay = 250) {
    let timer = null;
    return function (...args) {
        clearTimeout(timer);
        timer = setTimeout(() => fn.apply(this, args), delay);
    };
}

/* =========================
LOAD DATA
========================= */

function initParcelSearch(data) {
    parcelList = Array.isArray(data?.features) ? data.features : [];
    filteredParcelList = [];

    initParcelEvents();
    renderInitialParcelList();
}

/* =========================
INITIAL RENDER
========================= */

function renderInitialParcelList() {
    const container = document.getElementById("parcelList");
    if (!container) return;

    if (!parcelList.length) {
        container.innerHTML = `<div class="parcel-empty">Không có dữ liệu thửa đất</div>`;
        return;
    }

    if (parcelList.length > PARCEL_RENDER_LIMIT) {
        container.innerHTML = `
            <div class="parcel-empty">
                Có <b>${parcelList.length.toLocaleString()}</b> thửa đất.<br>
                Hãy nhập điều kiện tìm kiếm để hiển thị kết quả nhanh hơn.
            </div>
        `;
        return;
    }

    filteredParcelList = parcelList.slice(0, PARCEL_RENDER_LIMIT);
    renderParcelList(filteredParcelList, parcelList.length);
}

/* =========================
RENDER LIST
========================= */

function renderParcelList(list = [], totalMatched = 0) {
    const container = document.getElementById("parcelList");
    if (!container) return;

    if (!list.length) {
        container.innerHTML = `<div class="parcel-empty">Không tìm thấy thửa phù hợp</div>`;
        return;
    }

    let html = "";

    if (totalMatched > list.length) {
        html += `
            <div class="parcel-result-info">
                Tìm thấy <b>${totalMatched.toLocaleString()}</b> kết quả,
                đang hiển thị <b>${list.length}</b> kết quả đầu tiên.
            </div>
        `;
    } else {
        html += `
            <div class="parcel-result-info">
                Tìm thấy <b>${list.length.toLocaleString()}</b> kết quả.
            </div>
        `;
    }

    for (let i = 0; i < list.length; i++) {
        const f = list[i];
        const p = f?.properties || {};

        html += `
        <div class="parcel-row" onclick="selectParcel(${i}, true)">
            <div><b>Tờ mới:</b> ${p.SHBANDO ?? ""}</div>
            <div><b>Tờ cũ:</b> ${p.SOTOCU ?? ""}</div>
            <div><b>Thửa:</b> ${p.SHTHUA ?? ""}</div>
            <div><b>Chủ:</b> ${p.TENCHU ?? ""}</div>
        </div>
        `;
    }

    container.innerHTML = html;
}

/* =========================
FILTER
========================= */

function filterParcel() {
    const searchTo = safeLower(document.getElementById("searchTo")?.value);
    const searchToCu = safeLower(document.getElementById("searchToCu")?.value);
    const searchThua = safeLower(document.getElementById("searchThua")?.value);
    const searchChu = safeLower(document.getElementById("searchChu")?.value);

    const hasKeyword = searchTo || searchToCu || searchThua || searchChu;

    if (!hasKeyword) {
        renderInitialParcelList();
        return;
    }

    const result = [];
    let totalMatched = 0;

    for (let i = 0; i < parcelList.length; i++) {
        const f = parcelList[i];
        const p = f?.properties || {};

        const shbando = safeLower(p.SHBANDO);
        const sotocu = safeLower(p.SOTOCU);
        const shthua = safeLower(p.SHTHUA);
        const tenchu = safeLower(p.TENCHU);

        const match =
            (!searchTo || shbando.includes(searchTo)) &&
            (!searchToCu || sotocu.includes(searchToCu)) &&
            (!searchThua || shthua.includes(searchThua)) &&
            (!searchChu || tenchu.includes(searchChu));

        if (match) {
            totalMatched++;

            if (result.length < PARCEL_RENDER_LIMIT) {
                result.push(f);
            }
        }
    }

    filteredParcelList = result;
    renderParcelList(filteredParcelList, totalMatched);
}

/* =========================
EVENTS
========================= */

function initParcelEvents() {
    if (parcelEventsInitialized) return;
    parcelEventsInitialized = true;

    const debouncedFilter = debounce(filterParcel, 300);

    document.getElementById("searchTo")?.addEventListener("input", debouncedFilter);
    document.getElementById("searchToCu")?.addEventListener("input", debouncedFilter);
    document.getElementById("searchThua")?.addEventListener("input", debouncedFilter);
    document.getElementById("searchChu")?.addEventListener("input", debouncedFilter);
}

/* =========================
SELECT PARCEL
========================= */

function selectParcel(i, isFiltered = false) {
    const sourceList = isFiltered ? filteredParcelList : parcelList;
    const feature = sourceList[i];

    if (!feature) return;

    highlightParcel(feature);
    showParcelInfo(feature);
    drawParcelMeasure(feature);

    const bbox = turf.bbox(feature);

    map.fitBounds(
        [
            [bbox[0], bbox[1]],
            [bbox[2], bbox[3]]
        ],
        { padding: 40 }
    );
}

/* =========================
TOGGLE UPLOAD
========================= */

function toggleUpload() {
    let body = document.getElementById("uploadBody");
    let arrow = document.getElementById("uploadArrow");

    if (!body || !arrow) return;

    body.classList.toggle("hide");
    arrow.innerHTML = body.classList.contains("hide") ? "▲" : "▼";
}

/* =========================
TOGGLE PARCEL PANEL
========================= */

function toggleParcelPanel() {
    let body = document.getElementById("parcelBody");
    let arrow = document.getElementById("parcelArrow");

    if (!body || !arrow) return;

    body.classList.toggle("collapsed");
    arrow.innerHTML = body.classList.contains("collapsed") ? "▲" : "▼";
}
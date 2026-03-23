let currentKTT = 108.5;
let measureMarkers = [];
window.canhVisible = true;

/* =========================
VN2000
========================= */

function updateVN2000(ktt) {
    currentKTT = ktt;

    const def = `+proj=tmerc +lat_0=0 +lon_0=${ktt} +k=0.9999 +x_0=500000 +y_0=0 +ellps=WGS84 +units=m +no_defs`;
    proj4.defs("VN2000_Current", def);
}

updateVN2000(currentKTT);

function toVN2000(coord) {
    const r = proj4(
        proj4.defs("EPSG:4326"),
        proj4.defs("VN2000_Current"),
        [coord[0], coord[1]]
    );

    return {
        x: r[1],
        y: r[0]
    };
}

function distVN2000(a, b) {
    const dx = a.x - b.x;
    const dy = a.y - b.y;
    return Math.sqrt(dx * dx + dy * dy);
}

function areaVN2000(points) {
    let area = 0;

    for (let i = 0; i < points.length; i++) {
        const j = (i + 1) % points.length;
        area += points[i].x * points[j].y;
        area -= points[j].x * points[i].y;
    }

    return Math.abs(area) / 2;
}

/* =========================
TOAST MESSAGE
========================= */

function showToast(message, type = "info", timeout = 2200) {
    let box = document.getElementById("appToast");

    if (!box) {
        box = document.createElement("div");
        box.id = "appToast";
        box.style.position = "fixed";
        box.style.top = "20px";
        box.style.right = "20px";
        box.style.zIndex = "99999";
        box.style.minWidth = "240px";
        box.style.maxWidth = "360px";
        box.style.padding = "12px 14px";
        box.style.borderRadius = "10px";
        box.style.color = "#fff";
        box.style.fontSize = "14px";
        box.style.fontWeight = "600";
        box.style.boxShadow = "0 8px 24px rgba(0,0,0,0.18)";
        box.style.transition = "all 0.25s ease";
        box.style.opacity = "0";
        box.style.transform = "translateY(-10px)";
        document.body.appendChild(box);
    }

    if (type === "success") {
        box.style.background = "#16a34a";
    } else if (type === "warning") {
        box.style.background = "#f59e0b";
    } else if (type === "error") {
        box.style.background = "#dc2626";
    } else {
        box.style.background = "#2563eb";
    }

    box.innerText = message;
    box.style.opacity = "1";
    box.style.transform = "translateY(0)";

    clearTimeout(box._hideTimer);
    box._hideTimer = setTimeout(() => {
        box.style.opacity = "0";
        box.style.transform = "translateY(-10px)";
    }, timeout);
}

/* =========================
HELPER HÌNH HỌC
========================= */

function isValidLngLat(coord) {
    return Array.isArray(coord) &&
        coord.length >= 2 &&
        Number.isFinite(Number(coord[0])) &&
        Number.isFinite(Number(coord[1]));
}

function getRingAreaScore(coords) {
    if (!Array.isArray(coords) || coords.length < 3) return 0;

    let area = 0;
    for (let i = 0; i < coords.length; i++) {
        const [x1, y1] = coords[i];
        const [x2, y2] = coords[(i + 1) % coords.length];
        area += x1 * y2 - x2 * y1;
    }

    return Math.abs(area / 2);
}

function cleanRingCoords(coords) {
    if (!Array.isArray(coords)) return [];

    let clean = coords
        .filter(isValidLngLat)
        .map(c => [Number(c[0]), Number(c[1])]);

    if (clean.length > 1) {
        const first = clean[0];
        const last = clean[clean.length - 1];

        if (first[0] === last[0] && first[1] === last[1]) {
            clean = clean.slice(0, clean.length - 1);
        }
    }

    return clean;
}

function getFeatureCenter(feature) {
    try {
        if (typeof turf !== "undefined") {
            const center = turf.centroid(feature)?.geometry?.coordinates;
            if (isValidLngLat(center)) return center;
        }
    } catch (e) {
        console.warn("turf.centroid lỗi:", e);
    }

    const coords = getParcelCoords(feature);
    if (!coords.length) return null;

    let sumX = 0;
    let sumY = 0;

    coords.forEach(([x, y]) => {
        sumX += x;
        sumY += y;
    });

    return [sumX / coords.length, sumY / coords.length];
}

/* =========================
LẤY TỌA ĐỘ THỬA
========================= */

function getParcelCoords(feature) {
    if (!feature || !feature.geometry) return [];

    const geometry = feature.geometry;

    if (geometry.type === "Polygon") {
        const outerRing = cleanRingCoords(geometry.coordinates?.[0] || []);
        return outerRing;
    }

    if (geometry.type === "MultiPolygon") {
        const polygons = Array.isArray(geometry.coordinates) ? geometry.coordinates : [];
        let bestRing = [];
        let bestArea = 0;

        polygons.forEach(polygon => {
            const ring = cleanRingCoords(polygon?.[0] || []);
            const area = getRingAreaScore(ring);

            if (area > bestArea) {
                bestArea = area;
                bestRing = ring;
            }
        });

        return bestRing;
    }

    return [];
}

/* =========================
MIDPOINT OFFSET NHẸ RA NGOÀI CẠNH
========================= */

function getOffsetMidpoint(coords, i, offset = 0.000006) {
    const a = coords[i];
    const b = coords[(i + 1) % coords.length];

    const midX = (a[0] + b[0]) / 2;
    const midY = (a[1] + b[1]) / 2;

    const dx = b[0] - a[0];
    const dy = b[1] - a[1];
    const len = Math.sqrt(dx * dx + dy * dy);

    if (!len) return [midX, midY];

    const nx = -dy / len;
    const ny = dx / len;

    return [
        midX + nx * offset,
        midY + ny * offset
    ];
}

/* =========================
TẠO LABEL CẠNH XOAY THEO CẠNH
========================= */

function createEdgeLabel(text, angle) {
    const wrap = document.createElement("div");
    wrap.className = "edgeMarker";

    const inner = document.createElement("div");
    inner.className = "edgeLabel";
    inner.innerText = text;

    if (angle > 90) angle -= 180;
    if (angle < -90) angle += 180;

    inner.style.transform = `rotate(${angle}deg) translateY(-10px)`;

    wrap.appendChild(inner);
    return wrap;
}

/* =========================
QUẢN LÝ HIỆN / ẨN CẠNH
========================= */

function setMeasureMarkerVisible(marker, visible) {
    if (!marker) return;

    let el = null;

    if (typeof marker.getElement === "function") {
        el = marker.getElement();
    } else if (marker._element) {
        el = marker._element;
    }

    if (el) {
        el.style.display = visible ? "" : "none";
    }
}

function syncCanhToggleState() {
    const checkbox = document.getElementById("toggle_canh");
    if (checkbox) {
        window.canhVisible = checkbox.checked;
    }
}

function toggleCanhVisibility(forceValue = null) {
    if (typeof forceValue === "boolean") {
        window.canhVisible = forceValue;
    } else {
        syncCanhToggleState();
    }

    measureMarkers.forEach(marker => {
        setMeasureMarkerVisible(marker, window.canhVisible);
    });
}

/* =========================
CLEAR MEASURE
========================= */

function clearMeasure() {
    measureMarkers.forEach(m => {
        if (m && typeof m.remove === "function") {
            m.remove();
        }
    });

    measureMarkers = [];
}

/* =========================
HIGHLIGHT THỬA
========================= */

function highlightParcel(feature) {
    if (!feature || !feature.geometry) return;

    try {
        if (map.getSource("parcelHighlight")) {
            map.getSource("parcelHighlight").setData(feature);
        } else {
            map.addSource("parcelHighlight", {
                type: "geojson",
                data: feature
            });

            map.addLayer({
                id: "parcelHighlightFill",
                type: "fill",
                source: "parcelHighlight",
                paint: {
                    "fill-color": "#fde047",
                    "fill-opacity": 0.22
                }
            });

            map.addLayer({
                id: "parcelHighlightLine",
                type: "line",
                source: "parcelHighlight",
                paint: {
                    "line-color": "#ef4444",
                    "line-width": [
                        "interpolate",
                        ["linear"],
                        ["zoom"],
                        10, 1.5,
                        14, 3,
                        18, 4
                    ]
                }
            });
        }
    } catch (e) {
        console.error("Lỗi highlightParcel:", e);
    }
}

/* =========================
VẼ ĐO THỬA
========================= */

function drawParcelMeasure(feature) {
    if (!feature) return;

    syncCanhToggleState();
    clearMeasure();

    const coords = getParcelCoords(feature);
    if (!coords.length || coords.length < 3) {
        console.warn("Không có tọa độ thửa hợp lệ");
        return;
    }

    const isMobile = /Android|iPhone|iPad|iPod|Opera Mini|IEMobile|WPDesktop/i.test(navigator.userAgent);

    const maxVertexLabels = isMobile ? 8 : 24;
    const maxEdgeLabels = isMobile ? 8 : 18;

    const ptsVN = coords.map(c => toVN2000(c));

    /* LABEL ĐỈNH */
    coords.forEach((p, i) => {
        if (i >= maxVertexLabels) return;

        const el = document.createElement("div");
        el.className = "vertexLabel";
        el.innerHTML = i + 1;

        const m = new maplibregl.Marker({
            element: el,
            anchor: "center"
        }).setLngLat(p).addTo(map);

        measureMarkers.push(m);
        setMeasureMarkerVisible(m, window.canhVisible);
    });

    let perimeter = 0;
    let drawnEdgeLabels = 0;
    const step = Math.max(1, Math.ceil(ptsVN.length / maxEdgeLabels));

    /* LABEL CẠNH */
    for (let i = 0; i < ptsVN.length; i++) {
        const p1 = ptsVN[i];
        const p2 = ptsVN[(i + 1) % ptsVN.length];

        const dist = distVN2000(p1, p2);
        perimeter += dist;

        if (i % step !== 0) continue;
        if (drawnEdgeLabels >= maxEdgeLabels) continue;
        if (dist < 4) continue;

        const a = coords[i];
        const b = coords[(i + 1) % coords.length];
        const mid = getOffsetMidpoint(coords, i, isMobile ? 0.000008 : 0.00001);

        const dx = b[0] - a[0];
        const dy = b[1] - a[1];
        const angle = Math.atan2(dy, dx) * 180 / Math.PI;

        const el = createEdgeLabel(dist.toFixed(2), angle);

        const m = new maplibregl.Marker({
            element: el,
            anchor: "center"
        }).setLngLat(mid).addTo(map);

        measureMarkers.push(m);
        setMeasureMarkerVisible(m, window.canhVisible);
        drawnEdgeLabels++;
    }

    /* DIỆN TÍCH */
    const p = feature.properties || {};
    let area = Number(p.DIENTICH || 0);

    if (!area || area <= 0) {
        area = areaVN2000(ptsVN);
    }

    const center = getFeatureCenter(feature);
    if (!center) return;

    const el = document.createElement("div");
    el.className = "areaLabel";
    el.innerHTML = `
        <div class="areaLine">Diện tích: ${area.toFixed(2)} m²</div>
        <div class="areaLine">Chu vi: ${perimeter.toFixed(2)} m</div>
    `;

    const m = new maplibregl.Marker({
        element: el,
        anchor: "center"
    }).setLngLat(center).addTo(map);

    measureMarkers.push(m);
    setMeasureMarkerVisible(m, window.canhVisible);
}

/* =========================
XUẤT TXT TỌA ĐỘ
========================= */

function exportCoordinates() {
    if (!window.currentFeature) {
        showToast("Chưa chọn thửa!", "warning");
        return;
    }

    const feature = window.currentFeature;
    const coords = getParcelCoords(feature);

    if (!coords.length) {
        showToast("Không có tọa độ thửa", "warning");
        return;
    }

    let text = "X(m)\tY(m)\tZ(m)\n";

    coords.forEach((c) => {
        const vn = toVN2000(c);
        const x = vn.x.toFixed(3);
        const y = vn.y.toFixed(3);
        const z = 0;

        text += `${x}\t${y}\t${z}\n`;
    });

    const blob = new Blob([text], { type: "text/plain;charset=utf-8;" });
    const url = URL.createObjectURL(blob);

    const a = document.createElement("a");
    a.href = url;

    const p = feature.properties || {};
    a.download = `To_${p.SHBANDO || ""}_Thua_${p.SHTHUA || ""}.txt`;

    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);

    URL.revokeObjectURL(url);
    showToast("Đã xuất file tọa độ.", "success");
}

/* =========================
XUẤT PDF
========================= */

async function exportParcelPDF() {
    if (!window.currentFeature) {
        showToast("Chưa chọn thửa!", "warning");
        return;
    }

    if (!window.jspdf || !window.jspdf.jsPDF) {
        showToast("Chưa nạp thư viện jsPDF!", "error");
        console.error("window.jspdf không tồn tại");
        return;
    }

    const feature = window.currentFeature;
    const coords = getParcelCoords(feature);

    if (!coords.length) {
        showToast("Không có tọa độ thửa để xuất PDF", "warning");
        return;
    }

    const p = feature.properties || {};
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF("p", "mm", "a4");

    const pageWidth = 210;
    const pageHeight = 297;
    const margin = 10;
    const contentWidth = pageWidth - margin * 2;

    let y = 12;

    function line(yPos) {
        doc.line(margin, yPos, pageWidth - margin, yPos);
    }

    function rect(x, y, w, h) {
        doc.rect(x, y, w, h);
    }

    function cell(x, y, w, h, text, align = "left") {
        doc.rect(x, y, w, h);

        let tx = x + 2;
        if (align === "center") tx = x + w / 2;
        if (align === "right") tx = x + w - 2;

        doc.text(String(text ?? ""), tx, y + 5, { align });
    }

    function addNewPageWithHeader() {
        doc.addPage();
        y = 12;

        doc.setFont("helvetica", "bold");
        doc.setFontSize(14);
        doc.text("BANG TOA DO THUA DAT", pageWidth / 2, y, { align: "center" });
        y += 8;

        doc.setFont("helvetica", "normal");
        doc.setFontSize(10);
    }

    doc.setFont("helvetica", "bold");
    doc.setFontSize(16);
    doc.text("THONG TIN THUA DAT", pageWidth / 2, y, { align: "center" });
    y += 8;

    doc.setFont("helvetica", "normal");
    doc.setFontSize(10);
    doc.text("Trich xuat thong tin va toa do VN2000", pageWidth / 2, y, { align: "center" });
    y += 8;

    line(y);
    y += 6;

    doc.setFont("helvetica", "bold");
    doc.setFontSize(12);
    doc.text("1. THONG TIN CHUNG", margin, y);
    y += 4;

    const infoTop = y;
    const infoHeight = 34;
    rect(margin, infoTop, contentWidth, infoHeight);

    doc.setFont("helvetica", "normal");
    doc.setFontSize(11);

    doc.text(`To ban do: ${p.SHBANDO || ""}`, margin + 4, infoTop + 7);
    doc.text(`Thua dat: ${p.SHTHUA || ""}`, 110, infoTop + 7);

    doc.text(`To cu: ${p.SOTOCU || ""}`, margin + 4, infoTop + 14);
    doc.text(`Dien tich: ${p.DIENTICH || ""} m2`, 110, infoTop + 14);

    doc.text(`Loai dat: ${p.KHLOAIDAT || ""}`, margin + 4, infoTop + 21);
    doc.text(`Chu su dung: ${p.TENCHU || ""}`, margin + 4, infoTop + 28);

    y = infoTop + infoHeight + 8;

    doc.setFont("helvetica", "bold");
    doc.setFontSize(12);
    doc.text("2. BANG TOA DO VN2000", margin, y);
    y += 6;

    const col1 = 18;
    const col2 = 80;
    const col3 = 80;
    const rowH = 8;

    doc.setFontSize(10);
    cell(margin, y, col1, rowH, "STT", "center");
    cell(margin + col1, y, col2, rowH, "X (m)", "center");
    cell(margin + col1 + col2, y, col3, rowH, "Y (m)", "center");
    y += rowH;

    doc.setFont("helvetica", "normal");

    coords.forEach((c, i) => {
        const vn = toVN2000(c);

        if (y + rowH > pageHeight - 15) {
            addNewPageWithHeader();

            doc.setFont("helvetica", "bold");
            cell(margin, y, col1, rowH, "STT", "center");
            cell(margin + col1, y, col2, rowH, "X (m)", "center");
            cell(margin + col1 + col2, y, col3, rowH, "Y (m)", "center");
            y += rowH;
            doc.setFont("helvetica", "normal");
        }

        cell(margin, y, col1, rowH, i + 1, "center");
        cell(margin + col1, y, col2, rowH, vn.x.toFixed(3), "right");
        cell(margin + col1 + col2, y, col3, rowH, vn.y.toFixed(3), "right");
        y += rowH;
    });

    if (window.html2canvas) {
        try {
            const mapEl = document.getElementById("map");

            if (mapEl) {
                doc.addPage();
                y = 12;

                doc.setFont("helvetica", "bold");
                doc.setFontSize(14);
                doc.text("3. HINH BAN DO THUA DAT", pageWidth / 2, y, { align: "center" });
                y += 10;

                const canvas = await html2canvas(mapEl, {
                    useCORS: true,
                    backgroundColor: "#ffffff",
                    scale: 1
                });

                const imgData = canvas.toDataURL("image/png");

                let imgW = 190;
                let imgH = canvas.height * imgW / canvas.width;

                if (imgH > 250) {
                    imgH = 250;
                    imgW = canvas.width * imgH / canvas.height;
                }

                const imgX = (pageWidth - imgW) / 2;
                doc.addImage(imgData, "PNG", imgX, y, imgW, imgH);
            }
        } catch (err) {
            console.error("Loi chup ban do:", err);
        }
    }

    const filename = `To_${p.SHBANDO || ""}_Thua_${p.SHTHUA || ""}.pdf`;
    doc.save(filename);
    showToast("Đã xuất PDF.", "success");
}

/* =========================
GOOGLE MAPS
========================= */

function openParcelGoogleMaps() {
    if (!window.currentFeature) {
        showToast("Chưa chọn thửa!", "warning");
        return;
    }

    const feature = window.currentFeature;

    if (!feature.geometry) {
        showToast("Thửa không có dữ liệu hình học!", "warning");
        return;
    }

    const center = getFeatureCenter(feature);

    if (!center || center.length < 2) {
        showToast("Không lấy được tọa độ thửa!", "error");
        return;
    }

    const lng = center[0];
    const lat = center[1];

    const googleMapsUrl = `https://www.google.com/maps/search/?api=1&query=${lat},${lng}`;
    window.open(googleMapsUrl, "_blank");
}